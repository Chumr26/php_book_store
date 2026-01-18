<?php

/**
 * AdminPublisherController - Quản lý nhà xuất bản
 * 
 * Các chức năng CRUD cơ bản:
 * - Danh sách NXB
 * - Thêm NXB mới
 * - Sửa NXB
 * - Xóa NXB
 */

require_once __DIR__ . '/../../Controller/BaseController.php';
require_once __DIR__ . '/../../Model/Publishers.php';
require_once __DIR__ . '/../../Controller/helpers/SessionHelper.php';
require_once __DIR__ . '/../../Controller/helpers/Validator.php';

class AdminPublisherController extends BaseController
{
    private $publisherModel;

    public function __construct($db_connection)
    {
        parent::__construct($db_connection);
        $this->publisherModel = new Publishers($db_connection);

        SessionHelper::start();
        $this->checkAdminAuth();
    }

    public function index()
    {
        try {
            $publishers = $this->publisherModel->getAllPublishers();

            foreach ($publishers as &$pub) {
                $pub['book_count'] = $this->getBookCountByPublisher($pub['ma_nxb']);
            }

            return [
                'publishers' => $publishers,
                'csrf_token' => SessionHelper::generateCSRFToken()
            ];
        } catch (Exception $e) {
            error_log("Error: " . $e->getMessage());
            SessionHelper::setFlash('error', 'Lỗi: ' . $e->getMessage());
            return [
                'publishers' => [],
                'csrf_token' => ''
            ];
        }
    }

    public function create()
    {
        return [
            'csrf_token' => SessionHelper::generateCSRFToken()
        ];
    }

    public function store()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new Exception('Invalid request method');

            $token = $_POST['csrf_token'] ?? '';
            if (!SessionHelper::verifyCSRFToken($token)) throw new Exception('Invalid CSRF token');

            $validator = new Validator();
            $validator->required('ten_nxb', $_POST['ten_nxb'] ?? '', 'Vui lòng nhập tên nhà xuất bản');

            if ($validator->hasErrors()) {
                SessionHelper::setFlash('error', $validator->getFirstError());
                header('Location: index.php?page=publisher_create');
                exit;
            }

            $pubData = [
                'ten_nxb' => Validator::sanitizeString($_POST['ten_nxb']),
                'dia_chi' => Validator::sanitizeString($_POST['dia_chi'] ?? ''),
                'dien_thoai' => Validator::sanitizeString($_POST['dien_thoai'] ?? ''),
                'email' => Validator::sanitizeString($_POST['email'] ?? ''),
                'website' => Validator::sanitizeString($_POST['website'] ?? '')
            ];

            if ($this->publisherModel->addPublisher($pubData)) {
                SessionHelper::setFlash('success', 'Thêm NXB thành công');
                header('Location: index.php?page=publishers');
            } else {
                throw new Exception('Không thể thêm NXB');
            }
        } catch (Exception $e) {
            SessionHelper::setFlash('error', $e->getMessage());
            header('Location: index.php?page=publisher_create');
            exit;
        }
    }

    public function edit()
    {
        try {
            $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            if ($id <= 0) throw new Exception('ID không hợp lệ');

            $publisher = $this->publisherModel->getPublisherById($id);
            if (!$publisher) throw new Exception('Không tìm thấy NXB');

            return [
                'publisher' => $publisher,
                'csrf_token' => SessionHelper::generateCSRFToken()
            ];
        } catch (Exception $e) {
            SessionHelper::setFlash('error', $e->getMessage());
            header('Location: index.php?page=publishers');
            exit;
        }
    }

    public function update()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new Exception('Invalid request method');

            $token = $_POST['csrf_token'] ?? '';
            if (!SessionHelper::verifyCSRFToken($token)) throw new Exception('Invalid CSRF token');

            $id = isset($_POST['id_nxb']) ? (int)$_POST['id_nxb'] : 0;
            if ($id <= 0) throw new Exception('ID không hợp lệ');

            $validator = new Validator();
            $validator->required('ten_nxb', $_POST['ten_nxb'] ?? '', 'Vui lòng nhập tên nhà xuất bản');

            if ($validator->hasErrors()) {
                SessionHelper::setFlash('error', $validator->getFirstError());
                header('Location: index.php?page=publisher_edit&id=' . $id);
                exit;
            }

            $pubData = [
                'ten_nxb' => Validator::sanitizeString($_POST['ten_nxb']),
                'dia_chi' => Validator::sanitizeString($_POST['dia_chi'] ?? ''),
                'dien_thoai' => Validator::sanitizeString($_POST['dien_thoai'] ?? ''),
                'email' => Validator::sanitizeString($_POST['email'] ?? ''),
                'website' => Validator::sanitizeString($_POST['website'] ?? '')
            ];

            if ($this->publisherModel->updatePublisher($id, $pubData)) {
                SessionHelper::setFlash('success', 'Cập nhật NXB thành công');
                header('Location: index.php?page=publishers');
            } else {
                throw new Exception('Không thể cập nhật NXB');
            }
        } catch (Exception $e) {
            SessionHelper::setFlash('error', $e->getMessage());
            $id = $_POST['id_nxb'] ?? 0;
            header('Location: index.php?page=publisher_edit&id=' . $id);
            exit;
        }
    }

    public function delete()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new Exception('Invalid request method');

            $token = $_POST['csrf_token'] ?? '';
            if (!SessionHelper::verifyCSRFToken($token)) throw new Exception('Invalid CSRF token');

            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            if ($id <= 0) throw new Exception('ID không hợp lệ');

            // Check bindings
            $bookCount = $this->getBookCountByPublisher($id);
            if ($bookCount > 0) {
                throw new Exception("Không thể xóa NXB này vì đang có $bookCount sách liên kết.");
            }

            if ($this->publisherModel->deletePublisher($id)) {
                SessionHelper::setFlash('success', 'Xóa NXB thành công');
            } else {
                throw new Exception('Không thể xóa NXB');
            }
        } catch (Exception $e) {
            SessionHelper::setFlash('error', $e->getMessage());
        }
        header('Location: index.php?page=publishers');
        exit;
    }

    public function bulkDelete()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new Exception('Invalid request method');

            $token = $_POST['csrf_token'] ?? '';
            if (!SessionHelper::verifyCSRFToken($token)) throw new Exception('Invalid CSRF token');

            $ids = $_POST['ids'] ?? [];
            if (empty($ids)) throw new Exception('Vui lòng chọn ít nhất một mục');

            $deleted = 0;
            $skipped = 0;

            foreach ($ids as $id) {
                $id = (int)$id;
                if ($this->getBookCountByPublisher($id) > 0) {
                    $skipped++;
                    continue;
                }
                if ($this->publisherModel->deletePublisher($id)) {
                    $deleted++;
                }
            }

            $msg = "Đã xóa $deleted NXB.";
            if ($skipped > 0) $msg .= " Bỏ qua $skipped NXB do có sách liên kết.";

            SessionHelper::setFlash($skipped > 0 ? 'warning' : 'success', $msg);
        } catch (Exception $e) {
            SessionHelper::setFlash('error', $e->getMessage());
        }
        header('Location: index.php?page=publishers');
        exit;
    }

    private function getBookCountByPublisher($id)
    {
        $query = "SELECT COUNT(*) as count FROM sach WHERE id_nxb = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return (int)$result['count'];
    }

    private function checkAdminAuth()
    {
        if (!SessionHelper::isAdminLoggedIn()) {
            SessionHelper::setFlash('error', 'Vui lòng đăng nhập với quyền admin');
            header('Location: /Admin/login.php');
            exit;
        }
    }
}
