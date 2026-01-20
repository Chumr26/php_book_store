<?php

/**
 * AdminBookController - Quản lý sách (CRUD)
 * 
 * Các chức năng:
 * - Danh sách sách với tìm kiếm, lọc, phân trang
 * - Thêm sách mới
 * - Sửa thông tin sách
 * - Xóa sách (đơn lẻ và hàng loạt)
 * - Thay đổi trạng thái sách (Còn hàng/Hết hàng/Ngừng kinh doanh)
 * - Upload ảnh bìa sách
 * - Quản lý tồn kho
 */

require_once __DIR__ . '/../../Controller/BaseController.php';
require_once __DIR__ . '/../../Model/Books.php';
require_once __DIR__ . '/../../Model/Authors.php';
require_once __DIR__ . '/../../Model/Publishers.php';
require_once __DIR__ . '/../../Model/Categories.php';
require_once __DIR__ . '/../../Controller/helpers/SessionHelper.php';
require_once __DIR__ . '/../../Controller/helpers/Validator.php';

class AdminBookController extends BaseController
{
    private $bookModel;
    private $authorModel;
    private $publisherModel;
    private $categoryModel;

    private $allowedImageTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    private $maxImageSize = 5242880; // 5MB

    /**
     * Constructor
     * 
     * @param mysqli $db_connection Kết nối database
     */
    public function __construct($db_connection)
    {
        parent::__construct($db_connection);
        $this->bookModel = new Books($db_connection);
        $this->authorModel = new Authors($db_connection);
        $this->publisherModel = new Publishers($db_connection);
        $this->categoryModel = new Categories($db_connection);

        SessionHelper::start();
        $this->checkAdminAuth();
    }

    /**
     * Hiển thị danh sách sách
     * 
     * @return array
     */
    public function index()
    {
        try {
            $page = isset($_GET['p']) ? (int)$_GET['p'] : 1;
            $perPage = 20;
            $search = $_GET['search'] ?? '';
            $category = isset($_GET['category']) ? (int)$_GET['category'] : 0;
            $status = $_GET['status'] ?? '';
            $sortBy = $_GET['sort_by'] ?? 'ngay_tao';
            $order = $_GET['order'] ?? 'DESC';

            // Build query conditions
            $conditions = [];
            $params = [];
            $types = '';

            if (!empty($search)) {
                $conditions[] = "(ten_sach LIKE ? OR isbn LIKE ? OR tu_khoa LIKE ?)";
                $searchParam = "%$search%";
                $params[] = $searchParam;
                $params[] = $searchParam;
                $params[] = $searchParam;
                $types .= 'sss';
            }

            if ($category > 0) {
                $conditions[] = "ma_danh_muc = ?";
                $params[] = $category;
                $types .= 'i';
            }

            if (!empty($status)) {
                $conditions[] = "tinh_trang = ?";
                $params[] = $status;
                $types .= 's';
            }

            $whereClause = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';

            // Get total count
            $countQuery = "SELECT COUNT(*) as total FROM sach $whereClause";
            $stmt = $this->conn->prepare($countQuery);

            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }

            $stmt->execute();
            $totalBooks = $stmt->get_result()->fetch_assoc()['total'];
            $totalPages = ceil($totalBooks / $perPage);

            // Get books with pagination
            $offset = ($page - 1) * $perPage;

            // Validate sort column
            // Validate sort column
            $allowedSortColumns = ['ten_sach', 'gia', 'so_luong_ton', 'luot_ban', 'luot_xem', 'ngay_tao', 'ngay_them'];
            $sortBy = in_array($sortBy, $allowedSortColumns) ? $sortBy : 'ngay_them';

            // Map 'ngay_tao' to 'ngay_them' for DB compatibility
            if ($sortBy === 'ngay_tao') {
                $sortBy = 'ngay_them';
            }

            $order = strtoupper($order) === 'ASC' ? 'ASC' : 'DESC';

            $query = "SELECT s.id_sach AS ma_sach, s.ten_sach, s.gia, s.gia_goc, s.so_luong_ton, s.trang_thai AS tinh_trang, s.noi_bat, s.hinh_anh AS anh_bia, s.isbn,
                             d.ten_theloai AS ten_danh_muc, t.ten_tacgia AS ten_tac_gia, n.ten_nxb AS ten_nha_xuat_ban
                     FROM sach s
                     LEFT JOIN theloai d ON s.id_theloai = d.id_theloai
                     LEFT JOIN tacgia t ON s.id_tacgia = t.id_tacgia
                     LEFT JOIN nhaxuatban n ON s.id_nxb = n.id_nxb
                     $whereClause
                     ORDER BY s.$sortBy $order
                     LIMIT ? OFFSET ?";

            $stmt = $this->conn->prepare($query);
            $params[] = $perPage;
            $params[] = $offset;
            $types .= 'ii';

            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $result = $stmt->get_result();

            $books = [];
            while ($row = $result->fetch_assoc()) {
                $books[] = $row;
            }

            // Get filter data
            $categories = $this->categoryModel->getAllCategories();
            $statuses = ['Còn hàng', 'Hết hàng', 'Ngừng kinh doanh'];

            return [
                'books' => $books,
                'categories' => $categories,
                'statuses' => $statuses,
                'pagination' => [
                    'current_page' => $page,
                    'total_pages' => $totalPages,
                    'total_items' => $totalBooks,
                    'per_page' => $perPage
                ],
                'filters' => [
                    'search' => $search,
                    'category' => $category,
                    'status' => $status,
                    'sort_by' => $sortBy,
                    'order' => $order
                ],
                'csrf_token' => SessionHelper::generateCSRFToken()
            ];
        } catch (Exception $e) {
            error_log("Error in AdminBookController::index: " . $e->getMessage());
            SessionHelper::setFlash('error', 'Lỗi: ' . $e->getMessage());
            return [
                'books' => [],
                'categories' => [],
                'statuses' => [],
                'pagination' => [
                    'current_page' => 1,
                    'total_pages' => 0,
                    'total_items' => 0,
                    'per_page' => 20
                ],
                'filters' => [
                    'search' => '',
                    'category' => 0,
                    'status' => '',
                    'sort_by' => 'ngay_tao',
                    'order' => 'DESC'
                ],
                'csrf_token' => ''
            ];
        }
    }

    /**
     * Hiển thị form thêm sách
     * 
     * @return array
     */
    public function create()
    {
        try {
            return [
                'authors' => $this->authorModel->getAllAuthors(),
                'publishers' => $this->publisherModel->getAllPublishers(),
                'categories' => $this->categoryModel->getAllCategories(),
                'csrf_token' => SessionHelper::generateCSRFToken()
            ];
        } catch (Exception $e) {
            error_log("Error in create: " . $e->getMessage());
            SessionHelper::setFlash('error', 'Không thể tải form thêm sách');
            header('Location: index.php?page=books');
            exit;
        }
    }

    /**
     * Xử lý thêm sách mới
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

            $validator->required('ten_sach', $_POST['ten_sach'] ?? '', 'Vui lòng nhập tên sách');
            $validator->minLength('ten_sach', $_POST['ten_sach'] ?? '', 3, 'Tên sách phải có ít nhất 3 ký tự');

            $validator->required('isbn', $_POST['isbn'] ?? '', 'Vui lòng nhập ISBN');

            $validator->required('ma_tac_gia', $_POST['ma_tac_gia'] ?? '', 'Vui lòng chọn tác giả');
            $validator->integer('ma_tac_gia', $_POST['ma_tac_gia'] ?? '', 'Tác giả không hợp lệ');

            $validator->required('ma_nha_xuat_ban', $_POST['ma_nha_xuat_ban'] ?? '', 'Vui lòng chọn nhà xuất bản');
            $validator->integer('ma_nha_xuat_ban', $_POST['ma_nha_xuat_ban'] ?? '', 'Nhà xuất bản không hợp lệ');

            $validator->required('ma_danh_muc', $_POST['ma_danh_muc'] ?? '', 'Vui lòng chọn danh mục');
            $validator->integer('ma_danh_muc', $_POST['ma_danh_muc'] ?? '', 'Danh mục không hợp lệ');

            $validator->required('gia', $_POST['gia'] ?? '', 'Vui lòng nhập giá');
            $validator->numeric('gia', $_POST['gia'] ?? '', 'Giá phải là số');
            $validator->min('gia', $_POST['gia'] ?? '', 0, 'Giá phải lớn hơn 0');

            $validator->required('so_luong_ton', $_POST['so_luong_ton'] ?? '', 'Vui lòng nhập số lượng tồn');
            $validator->integer('so_luong_ton', $_POST['so_luong_ton'] ?? '', 'Số lượng phải là số nguyên');
            $validator->min('so_luong_ton', $_POST['so_luong_ton'] ?? '', 0, 'Số lượng không được âm');

            // Validate image if uploaded
            if (isset($_FILES['anh_bia']) && $_FILES['anh_bia']['error'] !== UPLOAD_ERR_NO_FILE) {
                $validator->image('anh_bia', $_FILES['anh_bia'], $this->maxImageSize, 'Ảnh bìa không hợp lệ');
            }

            if ($validator->hasErrors()) {
                SessionHelper::setFlash('error', $validator->getFirstError());
                header('Location: index.php?page=book_create');
                exit;
            }

            // Handle image upload
            $imagePath = null;
            if (isset($_FILES['anh_bia']) && $_FILES['anh_bia']['error'] === UPLOAD_ERR_OK) {
                $imagePath = $this->uploadImage($_FILES['anh_bia']);

                if (!$imagePath) {
                    throw new Exception('Không thể upload ảnh');
                }
            }

            // Prepare data
            $bookData = [
                'ten_sach' => Validator::sanitizeString($_POST['ten_sach']),
                'isbn' => Validator::sanitizeString($_POST['isbn']),
                'ma_tac_gia' => (int)$_POST['ma_tac_gia'],
                'ma_nha_xuat_ban' => (int)$_POST['ma_nha_xuat_ban'],
                'ma_danh_muc' => (int)$_POST['ma_danh_muc'],
                'gia' => (float)$_POST['gia'],
                'gia_goc' => isset($_POST['gia_goc']) ? (float)$_POST['gia_goc'] : null,
                'so_luong_ton' => (int)$_POST['so_luong_ton'],
                'so_trang' => isset($_POST['so_trang']) ? (int)$_POST['so_trang'] : null,
                'ngon_ngu' => Validator::sanitizeString($_POST['ngon_ngu'] ?? 'Tiếng Việt'),
                'nam_xuat_ban' => isset($_POST['nam_xuat_ban']) ? (int)$_POST['nam_xuat_ban'] : null,
                'mo_ta' => Validator::sanitizeString($_POST['mo_ta'] ?? ''),
                'tu_khoa' => Validator::sanitizeString($_POST['tu_khoa'] ?? ''),
                'anh_bia' => $imagePath,
                'noi_bat' => isset($_POST['noi_bat']) ? 1 : 0,
                'tinh_trang' => $_POST['tinh_trang'] ?? 'Còn hàng'
            ];

            // Create book
            $bookId = $this->bookModel->createBook($bookData);

            if ($bookId) {
                SessionHelper::setFlash('success', 'Thêm sách mới thành công');
                header('Location: index.php?page=books');
            } else {
                throw new Exception('Không thể thêm sách');
            }
        } catch (Exception $e) {
            error_log("Error in store: " . $e->getMessage());
            SessionHelper::setFlash('error', $e->getMessage());
            header('Location: index.php?page=book_create');
            exit;
        }
    }

    /**
     * Hiển thị form sửa sách
     * 
     * @return array
     */
    public function edit()
    {
        try {
            $bookId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

            if ($bookId <= 0) {
                throw new Exception('ID sách không hợp lệ');
            }

            $book = $this->bookModel->getBookById($bookId);

            if (!$book) {
                throw new Exception('Không tìm thấy sách');
            }

            return [
                'book' => $book,
                'authors' => $this->authorModel->getAllAuthors(),
                'publishers' => $this->publisherModel->getAllPublishers(),
                'categories' => $this->categoryModel->getAllCategories(),
                'csrf_token' => SessionHelper::generateCSRFToken()
            ];
        } catch (Exception $e) {
            error_log("Error in edit: " . $e->getMessage());
            SessionHelper::setFlash('error', $e->getMessage());
            header('Location: index.php?page=books');
            exit;
        }
    }

    /**
     * Xử lý cập nhật sách
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

            $bookId = isset($_POST['ma_sach']) ? (int)$_POST['ma_sach'] : 0;

            if ($bookId <= 0) {
                throw new Exception('ID sách không hợp lệ');
            }

            // Get existing book
            $existingBook = $this->bookModel->getBookById($bookId);

            if (!$existingBook) {
                throw new Exception('Không tìm thấy sách');
            }

            // Validate input (same as store)
            $validator = new Validator();

            $validator->required('ten_sach', $_POST['ten_sach'] ?? '', 'Vui lòng nhập tên sách');
            $validator->minLength('ten_sach', $_POST['ten_sach'] ?? '', 3, 'Tên sách phải có ít nhất 3 ký tự');

            $validator->required('isbn', $_POST['isbn'] ?? '', 'Vui lòng nhập ISBN');
            $validator->required('ma_tac_gia', $_POST['ma_tac_gia'] ?? '', 'Vui lòng chọn tác giả');
            $validator->required('ma_nha_xuat_ban', $_POST['ma_nha_xuat_ban'] ?? '', 'Vui lòng chọn nhà xuất bản');
            $validator->required('ma_danh_muc', $_POST['ma_danh_muc'] ?? '', 'Vui lòng chọn danh mục');
            $validator->required('gia', $_POST['gia'] ?? '', 'Vui lòng nhập giá');
            $validator->numeric('gia', $_POST['gia'] ?? '', 'Giá phải là số');
            $validator->required('so_luong_ton', $_POST['so_luong_ton'] ?? '', 'Vui lòng nhập số lượng tồn');

            if (isset($_FILES['anh_bia']) && $_FILES['anh_bia']['error'] !== UPLOAD_ERR_NO_FILE) {
                $validator->image('anh_bia', $_FILES['anh_bia'], $this->maxImageSize, 'Ảnh bìa không hợp lệ');
            }

            if ($validator->hasErrors()) {
                SessionHelper::setFlash('error', $validator->getFirstError());
                header('Location: index.php?page=book_edit&id=' . $bookId);
                exit;
            }

            // Handle image upload
            $imagePath = $existingBook['anh_bia'];

            if (isset($_FILES['anh_bia']) && $_FILES['anh_bia']['error'] === UPLOAD_ERR_OK) {
                $newImagePath = $this->uploadImage($_FILES['anh_bia']);

                if ($newImagePath) {
                    // Delete old image
                    if ($imagePath && file_exists(__DIR__ . '/../../' . $imagePath)) {
                        unlink(__DIR__ . '/../../' . $imagePath);
                    }

                    $imagePath = $newImagePath;
                }
            }

            // Prepare data
            $bookData = [
                'ten_sach' => Validator::sanitizeString($_POST['ten_sach']),
                'isbn' => Validator::sanitizeString($_POST['isbn']),
                'ma_tac_gia' => (int)$_POST['ma_tac_gia'],
                'ma_nha_xuat_ban' => (int)$_POST['ma_nha_xuat_ban'],
                'ma_danh_muc' => (int)$_POST['ma_danh_muc'],
                'gia' => (float)$_POST['gia'],
                'gia_goc' => isset($_POST['gia_goc']) ? (float)$_POST['gia_goc'] : null,
                'so_luong_ton' => (int)$_POST['so_luong_ton'],
                'so_trang' => isset($_POST['so_trang']) ? (int)$_POST['so_trang'] : null,
                'ngon_ngu' => Validator::sanitizeString($_POST['ngon_ngu'] ?? 'Tiếng Việt'),
                'nam_xuat_ban' => isset($_POST['nam_xuat_ban']) ? (int)$_POST['nam_xuat_ban'] : null,
                'mo_ta' => Validator::sanitizeString($_POST['mo_ta'] ?? ''),
                'tu_khoa' => Validator::sanitizeString($_POST['tu_khoa'] ?? ''),
                'anh_bia' => $imagePath,
                'noi_bat' => isset($_POST['noi_bat']) ? 1 : 0,
                'tinh_trang' => $_POST['tinh_trang'] ?? 'Còn hàng'
            ];

            // Update book
            $success = $this->bookModel->updateBook($bookId, $bookData);

            if ($success) {
                SessionHelper::setFlash('success', 'Cập nhật sách thành công');
                header('Location: index.php?page=books');
            } else {
                throw new Exception('Không thể cập nhật sách');
            }
        } catch (Exception $e) {
            error_log("Error in update: " . $e->getMessage());
            SessionHelper::setFlash('error', $e->getMessage());
            $bookId = $_POST['ma_sach'] ?? 0;
            header('Location: index.php?page=book_edit&id=' . $bookId);
            exit;
        }
    }

    /**
     * Xóa sách
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

            $bookId = isset($_POST['book_id']) ? (int)$_POST['book_id'] : 0;

            if ($bookId <= 0) {
                throw new Exception('ID sách không hợp lệ');
            }

            // Get book for image deletion
            $book = $this->bookModel->getBookById($bookId);

            // Delete book
            // Delete book
            $result = $this->bookModel->deleteBook($bookId);

            if ($result === 'deleted') {
                // Delete image file
                if ($book && $book['anh_bia'] && file_exists(__DIR__ . '/../../' . $book['anh_bia'])) {
                    unlink(__DIR__ . '/../../' . $book['anh_bia']);
                }

                SessionHelper::setFlash('success', 'Xóa sách thành công');
            } elseif ($result === 'discontinued') {
                SessionHelper::setFlash('warning', 'Sách đã có dữ liệu liên quan (đơn hàng/đánh giá), đã chuyển sang trạng thái "Ngừng kinh doanh" để bảo toàn dữ liệu.');
            } else {
                throw new Exception('Không thể xóa sách');
            }
        } catch (Exception $e) {
            error_log("Error in delete: " . $e->getMessage());
            SessionHelper::setFlash('error', $e->getMessage());
        }

        header('Location: index.php?page=books');
        exit;
    }

    /**
     * Xóa nhiều sách
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

            $bookIds = $_POST['book_ids'] ?? [];

            if (empty($bookIds) || !is_array($bookIds)) {
                throw new Exception('Vui lòng chọn sách cần xóa');
            }

            $deletedCount = 0;

            foreach ($bookIds as $bookId) {
                $bookId = (int)$bookId;

                if ($bookId <= 0) continue;

                // Get book for image deletion
                $book = $this->bookModel->getBookById($bookId);

                // Delete book
                // Delete book
                $result = $this->bookModel->deleteBook($bookId);

                if ($result === 'deleted') {
                    $deletedCount++;

                    // Delete image file
                    if ($book && $book['anh_bia'] && file_exists(__DIR__ . '/../../' . $book['anh_bia'])) {
                        unlink(__DIR__ . '/../../' . $book['anh_bia']);
                    }
                } elseif ($result === 'discontinued') {
                    // Count as processed/modified but not deleted physically
                    // Maybe we want to track this separately?
                }
            }

            SessionHelper::setFlash('success', "Đã xử lý xóa/cập nhật trạng thái cho các sách đã chọn.");
        } catch (Exception $e) {
            error_log("Error in bulkDelete: " . $e->getMessage());
            SessionHelper::setFlash('error', $e->getMessage());
        }

        header('Location: index.php?page=books');
        exit;
    }

    /**
     * Thay đổi trạng thái sách
     * 
     * @return void
     */
    public function toggleStatus()
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

            $bookId = isset($_POST['book_id']) ? (int)$_POST['book_id'] : 0;
            $status = $_POST['status'] ?? '';

            if ($bookId <= 0) {
                throw new Exception('ID sách không hợp lệ');
            }

            $allowedStatuses = ['Còn hàng', 'Hết hàng', 'Ngừng kinh doanh'];
            if (!in_array($status, $allowedStatuses)) {
                throw new Exception('Trạng thái không hợp lệ');
            }

            // Update status
            $success = $this->bookModel->updateBookStatus($bookId, $status);

            if ($success) {
                $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
                    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

                if ($isAjax) {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => true,
                        'message' => 'Cập nhật trạng thái thành công'
                    ]);
                    exit;
                } else {
                    SessionHelper::setFlash('success', 'Cập nhật trạng thái thành công');
                }
            } else {
                throw new Exception('Không thể cập nhật trạng thái');
            }
        } catch (Exception $e) {
            error_log("Error in toggleStatus: " . $e->getMessage());

            $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
                strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
                exit;
            } else {
                SessionHelper::setFlash('error', $e->getMessage());
            }
        }

        header('Location: index.php?page=books');
        exit;
    }
    
    // ========== PRIVATE HELPER METHODS ==========

    /**
     * Upload ảnh bìa sách
     * 
     * @param array $file
     * @return string|false
     */
    private function uploadImage($file)
    {
        try {
            // Validate file type
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);

            if (!in_array($mimeType, $this->allowedImageTypes)) {
                throw new Exception('Định dạng ảnh không được hỗ trợ');
            }

            // Generate unique filename
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = uniqid('book_') . '.' . $extension;

            // Upload directory
            $uploadDir = __DIR__ . '/../../Content/images/books/';

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $uploadPath = $uploadDir . $filename;

            // Move uploaded file
            if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                return 'Content/images/books/' . $filename;
            }

            return false;
        } catch (Exception $e) {
            error_log("Error in uploadImage: " . $e->getMessage());
            return false;
        }
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
