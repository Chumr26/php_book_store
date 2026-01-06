<?php

/**
 * AdminCategoryController - Quản lý danh mục sách
 * 
 * Các chức năng CRUD cơ bản:
 * - Danh sách danh mục
 * - Thêm danh mục mới
 * - Sửa danh mục
 * - Xóa danh mục
 * - Quản lý thứ tự hiển thị
 * - Thay đổi trạng thái
 */

require_once __DIR__ . '/../../Controller/BaseController.php';
require_once __DIR__ . '/../../Model/Categories.php';
require_once __DIR__ . '/../../Controller/helpers/SessionHelper.php';
require_once __DIR__ . '/../../Controller/helpers/Validator.php';

class AdminCategoryController extends BaseController
{
    private $categoryModel;

    /**
     * Constructor
     * 
     * @param mysqli $db_connection Kết nối database
     */
    public function __construct($db_connection)
    {
        parent::__construct($db_connection);
        $this->categoryModel = new Categories($db_connection);

        SessionHelper::start();
        $this->checkAdminAuth();
    }

    /**
     * Danh sách danh mục
     * 
     * @return array
     */
    public function index()
    {
        try {
            $categories = $this->categoryModel->getAllCategories();

            // Get book count for each category
            foreach ($categories as &$category) {
                $category['book_count'] = $this->getBookCountByCategory($category['ma_danh_muc']);
            }

            return [
                'categories' => $categories,
                'csrf_token' => SessionHelper::generateCSRFToken()
            ];
        } catch (Exception $e) {
            error_log("Error in AdminCategoryController::index: " . $e->getMessage());
            SessionHelper::setFlash('error', 'Lỗi: ' . $e->getMessage());
            return [
                'categories' => [],
                'csrf_token' => ''
            ];
        }
    }

    /**
     * Hiển thị form thêm danh mục
     * 
     * @return array
     */
    public function create()
    {
        return [
            'csrf_token' => SessionHelper::generateCSRFToken()
        ];
    }

    /**
     * Xử lý thêm danh mục
     * 
     * @return void
     */
    public function store()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method');
            }

            // CSRF validation
            $token = $_POST['csrf_token'] ?? '';
            if (!SessionHelper::verifyCSRFToken($token)) {
                throw new Exception('Invalid CSRF token');
            }

            // Validate input
            $validator = new Validator();

            $validator->required('ten_danh_muc', $_POST['ten_danh_muc'] ?? '', 'Vui lòng nhập tên danh mục');
            $validator->minLength('ten_danh_muc', $_POST['ten_danh_muc'] ?? '', 2, 'Tên danh mục phải có ít nhất 2 ký tự');

            if ($validator->hasErrors()) {
                SessionHelper::setFlash('error', $validator->getFirstError());
                header('Location: index.php?page=admin_category_create');
                exit;
            }

            // Prepare data
            $categoryData = [
                'ten_danh_muc' => Validator::sanitizeString($_POST['ten_danh_muc']),
                'mo_ta' => Validator::sanitizeString($_POST['mo_ta'] ?? ''),
                'thu_tu' => isset($_POST['thu_tu']) ? (int)$_POST['thu_tu'] : 0
            ];

            // Create category
            $categoryId = $this->categoryModel->addCategory($categoryData);

            if ($categoryId) {
                SessionHelper::setFlash('success', 'Thêm danh mục mới thành công');
                header('Location: index.php?page=admin_categories');
            } else {
                throw new Exception('Không thể thêm danh mục');
            }
        } catch (Exception $e) {
            error_log("Error in store: " . $e->getMessage());
            SessionHelper::setFlash('error', $e->getMessage());
            header('Location: index.php?page=admin_category_create');
            exit;
        }
    }

    /**
     * Hiển thị form sửa danh mục
     * 
     * @return array
     */
    public function edit()
    {
        try {
            $categoryId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

            if ($categoryId <= 0) {
                throw new Exception('ID danh mục không hợp lệ');
            }

            $category = $this->categoryModel->getCategoryById($categoryId);

            if (!$category) {
                throw new Exception('Không tìm thấy danh mục');
            }

            return [
                'category' => $category,
                'csrf_token' => SessionHelper::generateCSRFToken()
            ];
        } catch (Exception $e) {
            error_log("Error in edit: " . $e->getMessage());
            SessionHelper::setFlash('error', $e->getMessage());
            header('Location: index.php?page=admin_categories');
            exit;
        }
    }

    /**
     * Xử lý cập nhật danh mục
     * 
     * @return void
     */
    public function update()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method');
            }

            // CSRF validation
            $token = $_POST['csrf_token'] ?? '';
            if (!SessionHelper::verifyCSRFToken($token)) {
                throw new Exception('Invalid CSRF token');
            }

            $categoryId = isset($_POST['ma_danh_muc']) ? (int)$_POST['ma_danh_muc'] : 0;

            if ($categoryId <= 0) {
                throw new Exception('ID danh mục không hợp lệ');
            }

            // Validate input
            $validator = new Validator();

            $validator->required('ten_danh_muc', $_POST['ten_danh_muc'] ?? '', 'Vui lòng nhập tên danh mục');
            $validator->minLength('ten_danh_muc', $_POST['ten_danh_muc'] ?? '', 2, 'Tên danh mục phải có ít nhất 2 ký tự');

            if ($validator->hasErrors()) {
                SessionHelper::setFlash('error', $validator->getFirstError());
                header('Location: index.php?page=admin_category_edit&id=' . $categoryId);
                exit;
            }

            // Prepare data
            $categoryData = [
                'ten_danh_muc' => Validator::sanitizeString($_POST['ten_danh_muc']),
                'mo_ta' => Validator::sanitizeString($_POST['mo_ta'] ?? ''),
                'thu_tu' => isset($_POST['thu_tu']) ? (int)$_POST['thu_tu'] : 0
            ];

            // Update category
            $success = $this->categoryModel->updateCategory($categoryId, $categoryData);

            if ($success) {
                SessionHelper::setFlash('success', 'Cập nhật danh mục thành công');
                header('Location: index.php?page=admin_categories');
            } else {
                throw new Exception('Không thể cập nhật danh mục');
            }
        } catch (Exception $e) {
            error_log("Error in update: " . $e->getMessage());
            SessionHelper::setFlash('error', $e->getMessage());
            $categoryId = $_POST['ma_danh_muc'] ?? 0;
            header('Location: index.php?page=admin_category_edit&id=' . $categoryId);
            exit;
        }
    }

    /**
     * Xóa danh mục
     * 
     * @return void
     */
    public function delete()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method');
            }

            // CSRF validation
            $token = $_POST['csrf_token'] ?? '';
            if (!SessionHelper::verifyCSRFToken($token)) {
                throw new Exception('Invalid CSRF token');
            }

            $categoryId = isset($_POST['category_id']) ? (int)$_POST['category_id'] : 0;

            if ($categoryId <= 0) {
                throw new Exception('ID danh mục không hợp lệ');
            }

            // Check if category has books
            $bookCount = $this->getBookCountByCategory($categoryId);

            if ($bookCount > 0) {
                throw new Exception("Không thể xóa danh mục có $bookCount sách. Vui lòng di chuyển sách sang danh mục khác trước.");
            }

            // Delete category
            $success = $this->categoryModel->deleteCategory($categoryId);

            if ($success) {
                SessionHelper::setFlash('success', 'Xóa danh mục thành công');
            } else {
                throw new Exception('Không thể xóa danh mục');
            }
        } catch (Exception $e) {
            error_log("Error in delete: " . $e->getMessage());
            SessionHelper::setFlash('error', $e->getMessage());
        }

        header('Location: index.php?page=admin_categories');
        exit;
    }

    /**
     * Xóa nhiều danh mục
     * 
     * @return void
     */
    public function bulkDelete()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method');
            }

            // CSRF validation
            $token = $_POST['csrf_token'] ?? '';
            if (!SessionHelper::verifyCSRFToken($token)) {
                throw new Exception('Invalid CSRF token');
            }

            $ids = $_POST['ids'] ?? [];

            if (empty($ids) || !is_array($ids)) {
                throw new Exception('Vui lòng chọn ít nhất một danh mục để xóa');
            }

            $deletedCount = 0;
            $skippedCount = 0;
            $errors = 0;

            foreach ($ids as $id) {
                $id = (int)$id;
                if ($id <= 0) continue;

                // Check if category has books
                $bookCount = $this->getBookCountByCategory($id);

                if ($bookCount > 0) {
                    $skippedCount++;
                    continue;
                }

                if ($this->categoryModel->deleteCategory($id)) {
                    $deletedCount++;
                } else {
                    $errors++;
                }
            }

            // Build result message
            $message = "Đã xóa thành công $deletedCount danh mục.";
            $messageType = 'success';

            if ($skippedCount > 0) {
                $message .= " Bỏ qua $skippedCount danh mục vì đang chứa sách.";
                $messageType = 'warning';
            }

            if ($errors > 0) {
                $message .= " Có $errors lỗi xảy ra.";
                $messageType = 'warning';
            }

            SessionHelper::setFlash($messageType, $message);
        } catch (Exception $e) {
            error_log("Error in bulkDelete: " . $e->getMessage());
            SessionHelper::setFlash('error', $e->getMessage());
        }

        header('Location: index.php?page=admin_categories');
        exit;
    }

    /**
     * Cập nhật thứ tự danh mục (AJAX)
     * 
     * @return void
     */
    public function updateOrder()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method');
            }

            // CSRF validation
            $token = $_POST['csrf_token'] ?? '';
            if (!SessionHelper::verifyCSRFToken($token)) {
                throw new Exception('Invalid CSRF token');
            }

            $orders = $_POST['orders'] ?? [];

            if (empty($orders) || !is_array($orders)) {
                throw new Exception('Dữ liệu không hợp lệ');
            }

            // Update each category's order
            foreach ($orders as $categoryId => $order) {
                $categoryId = (int)$categoryId;
                $order = (int)$order;

                if ($categoryId > 0) {
                    $this->categoryModel->updateCategory($categoryId, ['thu_tu' => $order]);
                }
            }

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Cập nhật thứ tự thành công'
            ]);
            exit;
        } catch (Exception $e) {
            error_log("Error in updateOrder: " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
            exit;
        }
    }
    
    // ========== PRIVATE HELPER METHODS ==========

    /**
     * Đếm số sách trong danh mục
     * 
     * @param int $categoryId
     * @return int
     */
    private function getBookCountByCategory($categoryId)
    {
        $query = "SELECT COUNT(*) as count FROM sach WHERE id_theloai = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $categoryId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return (int)$result['count'];
    }

    /**
     * Kiểm tra quyền admin
     * 
     * @return void
     */
    private function checkAdminAuth()
    {
        if (!SessionHelper::isAdminLoggedIn()) {
            SessionHelper::setFlash('error', 'Vui lòng đăng nhập với quyền admin');
            header('Location: /Admin/login.php');
            exit;
        }
    }
}
