<?php

/**
 * AdminAuthorController - Quản lý tác giả
 * 
 * Các chức năng CRUD cơ bản:
 * - Danh sách tác giả
 * - Thêm tác giả mới
 * - Sửa tác giả
 * - Xóa tác giả
 */

require_once __DIR__ . '/../../Controller/BaseController.php';
require_once __DIR__ . '/../../Model/Authors.php';
require_once __DIR__ . '/../../Controller/helpers/SessionHelper.php';
require_once __DIR__ . '/../../Controller/helpers/Validator.php';

class AdminAuthorController extends BaseController
{
    private $authorModel;

    public function __construct($db_connection)
    {
        parent::__construct($db_connection);
        $this->authorModel = new Authors($db_connection);

        SessionHelper::start();
        $this->checkAdminAuth();
    }

    public function index()
    {
        try {
            $authors = $this->authorModel->getAllAuthors();

            // Get book count for each author (optional, can be added to model later)
            foreach ($authors as &$author) {
                // Assuming simple query here or add method to Author model
                $author['book_count'] = $this->getBookCountByAuthor($author['ma_tac_gia']);
            }

            return [
                'authors' => $authors,
                'csrf_token' => SessionHelper::generateCSRFToken()
            ];
        } catch (Exception $e) {
            error_log("Error in AdminAuthorController::index: " . $e->getMessage());
            SessionHelper::setFlash('error', 'Lỗi: ' . $e->getMessage());
            return [
                'authors' => [],
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
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method');
            }

            $token = $_POST['csrf_token'] ?? '';
            if (!SessionHelper::verifyCSRFToken($token)) {
                throw new Exception('Invalid CSRF token');
            }

            $validator = new Validator();
            $validator->required('ten_tacgia', $_POST['ten_tacgia'] ?? '', 'Vui lòng nhập tên tác giả');

            if ($validator->hasErrors()) {
                SessionHelper::setFlash('error', $validator->getFirstError());
                header('Location: index.php?page=author_create');
                exit;
            }

            $authorData = [
                'ten_tacgia' => Validator::sanitizeString($_POST['ten_tacgia']),
                'but_danh' => Validator::sanitizeString($_POST['but_danh'] ?? ''),
                'tieu_su' => Validator::sanitizeString($_POST['tieu_su'] ?? ''),
                'ngay_sinh' => !empty($_POST['ngay_sinh']) ? $_POST['ngay_sinh'] : null,
                'quoc_tich' => Validator::sanitizeString($_POST['quoc_tich'] ?? '')
            ];

            if ($this->authorModel->addAuthor($authorData)) {
                SessionHelper::setFlash('success', 'Thêm tác giả thành công');
                header('Location: index.php?page=authors');
            } else {
                throw new Exception('Không thể thêm tác giả');
            }
        } catch (Exception $e) {
            error_log("Error in store: " . $e->getMessage());
            SessionHelper::setFlash('error', $e->getMessage());
            header('Location: index.php?page=author_create');
            exit;
        }
    }

    public function edit()
    {
        try {
            $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            if ($id <= 0) throw new Exception('ID không hợp lệ');

            $author = $this->authorModel->getAuthorById($id);
            if (!$author) throw new Exception('Không tìm thấy tác giả');

            return [
                'author' => $author,
                'csrf_token' => SessionHelper::generateCSRFToken()
            ];
        } catch (Exception $e) {
            SessionHelper::setFlash('error', $e->getMessage());
            header('Location: index.php?page=authors');
            exit;
        }
    }

    public function update()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new Exception('Invalid request method');

            $token = $_POST['csrf_token'] ?? '';
            if (!SessionHelper::verifyCSRFToken($token)) throw new Exception('Invalid CSRF token');

            $id = isset($_POST['id_tacgia']) ? (int)$_POST['id_tacgia'] : 0;
            if ($id <= 0) throw new Exception('ID không hợp lệ');

            $validator = new Validator();
            $validator->required('ten_tacgia', $_POST['ten_tacgia'] ?? '', 'Vui lòng nhập tên tác giả');

            if ($validator->hasErrors()) {
                SessionHelper::setFlash('error', $validator->getFirstError());
                header('Location: index.php?page=author_edit&id=' . $id);
                exit;
            }

            $authorData = [
                'ten_tacgia' => Validator::sanitizeString($_POST['ten_tacgia']),
                'but_danh' => Validator::sanitizeString($_POST['but_danh'] ?? ''),
                'tieu_su' => Validator::sanitizeString($_POST['tieu_su'] ?? ''),
                'ngay_sinh' => !empty($_POST['ngay_sinh']) ? $_POST['ngay_sinh'] : null,
                'quoc_tich' => Validator::sanitizeString($_POST['quoc_tich'] ?? '')
            ];

            if ($this->authorModel->updateAuthor($id, $authorData)) {
                SessionHelper::setFlash('success', 'Cập nhật tác giả thành công');
                header('Location: index.php?page=authors');
            } else {
                throw new Exception('Không thể cập nhật tác giả');
            }
        } catch (Exception $e) {
            SessionHelper::setFlash('error', $e->getMessage());
            $id = $_POST['id_tacgia'] ?? 0;
            header('Location: index.php?page=author_edit&id=' . $id);
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

            // Check bindings (books)
            $bookCount = $this->getBookCountByAuthor($id);
            if ($bookCount > 0) {
                throw new Exception("Không thể xóa tác giả này vì đang có $bookCount sách liên kết.");
            }

            if ($this->authorModel->deleteAuthor($id)) {
                SessionHelper::setFlash('success', 'Xóa tác giả thành công');
            } else {
                throw new Exception('Không thể xóa tác giả');
            }
        } catch (Exception $e) {
            SessionHelper::setFlash('error', $e->getMessage());
        }
        header('Location: index.php?page=authors');
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
                if ($this->getBookCountByAuthor($id) > 0) {
                    $skipped++;
                    continue;
                }
                if ($this->authorModel->deleteAuthor($id)) {
                    $deleted++;
                }
            }

            $msg = "Đã xóa $deleted tác giả.";
            if ($skipped > 0) $msg .= " Bỏ qua $skipped tác giả do có sách liên kết.";

            SessionHelper::setFlash($skipped > 0 ? 'warning' : 'success', $msg);
        } catch (Exception $e) {
            SessionHelper::setFlash('error', $e->getMessage());
        }
        header('Location: index.php?page=authors');
        exit;
    }

    private function getBookCountByAuthor($authorId)
    {
        $query = "SELECT COUNT(*) as count FROM sach WHERE id_tacgia = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $authorId);
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
