<?php
/**
 * BookController
 * Handles book browsing, searching, filtering, and detail viewing
 */

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../Model/Books.php';
require_once __DIR__ . '/../Model/Categories.php';
require_once __DIR__ . '/../Model/Authors.php';
require_once __DIR__ . '/../Model/Publishers.php';
require_once __DIR__ . '/../Model/Reviews.php';
require_once __DIR__ . '/../Model/Pagination.php';
require_once __DIR__ . '/helpers/SessionHelper.php';
require_once __DIR__ . '/helpers/Validator.php';

class BookController extends BaseController {
    private $booksModel;
    private $categoriesModel;
    private $authorsModel;
    private $publishersModel;
    private $reviewsModel;
    
    public function __construct($conn) {
        parent::__construct($conn);
        $this->booksModel = new Books($conn);
        $this->categoriesModel = new Categories($conn);
        $this->authorsModel = new Authors($conn);
        $this->publishersModel = new Publishers($conn);
        $this->reviewsModel = new Reviews($conn);
    }
    
    /**
     * List all books with pagination and filtering
     */
    public function listBooks() {
        try {
            SessionHelper::start();
            
            // Get filters from query string
            $category = isset($_GET['category']) ? (int)$_GET['category'] : 0;
            $priceRange = isset($_GET['price_range']) ? $_GET['price_range'] : '';
            $rating = isset($_GET['rating']) ? (int)$_GET['rating'] : 0;
            $search = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
            $sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';
            $page = isset($_GET['p']) ? (int)$_GET['p'] : 1;
            $limit = 12;
            
            // Get total count FIRST
            $totalBooks = $this->booksModel->countBooks($category, $priceRange, $rating, $search);
            
            // Initialize pagination with INTEGER total count
            $pagination = new Pagination($totalBooks, $limit, $page);
            
            // Get books for current page
            $offset = ($page - 1) * $limit;
            $books = $this->booksModel->getBooks($category, $priceRange, $rating, $search, $sort, $limit, $offset);
            
            // Get all categories for filter sidebar
            $categories = $this->categoriesModel->getAllCategories();
            
            // Get publishers for filter
            $publishers = $this->publishersModel->getAllPublishers();
            
            // Get bestsellers for sidebar
            $bestsellers = $this->booksModel->getTopSellingBooks(5);
            
            // Prepare data for view
            $data = [
                'books' => $books,
                'total' => $totalBooks,
                'current_page' => $page,
                'total_pages' => $pagination->getTotalPages(),
                'pagination' => $pagination,
                'categories' => $categories,
                'publishers' => $publishers ?? [],
                'bestsellers' => $bestsellers,
                'current_category' => $category,
                'current_price' => $priceRange,
                'current_rating' => $rating,
                'search_keyword' => $search,
                'sort_by' => $sort,
                'page_title' => 'Danh sách sách'
            ];
            
            return $data;
            
        } catch (Exception $e) {
            error_log("BookController::listBooks Error: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            SessionHelper::setFlash('error', 'Có lỗi xảy ra khi tải danh sách sách. Chi tiết: ' . $e->getMessage());
            return [
                'books' => [],
                'total' => 0,
                'current_page' => 1,
                'total_pages' => 0,
                'categories' => [],
                'publishers' => []
            ];
        }
    }
    
    /**
     * Filter books by category
     * @param int $categoryId Category ID
     */
    public function filterByCategory($categoryId) {
        try {
            SessionHelper::start();
            
            $page = isset($_GET['page_num']) ? (int)$_GET['page_num'] : 1;
            $sortBy = isset($_GET['sort']) ? $_GET['sort'] : 'ngay_tao';
            $order = isset($_GET['order']) ? $_GET['order'] : 'DESC';
            $limit = 12;
            
            // Get books by category
            $result = $this->booksModel->getBooksByCategory($categoryId, $page, $limit, $sortBy, $order);
            
            // Get category details
            $category = $this->categoriesModel->getCategoryById($categoryId);
            
            // Get all categories for sidebar
            $categories = $this->categoriesModel->getAllCategories();
            
            $data = [
                'books' => $result['data'] ?? [],
                'total' => $result['total'] ?? 0,
                'current_page' => $page,
                'total_pages' => $result['total_pages'] ?? 0,
                'categories' => $categories,
                'current_category' => $categoryId,
                'category_name' => $category['ten_theloai'] ?? 'Không xác định',
                'sort_by' => $sortBy,
                'order' => $order,
                'page_title' => 'Sách ' . ($category['ten_theloai'] ?? '')
            ];
            
            return $data;
            
        } catch (Exception $e) {
            error_log("BookController::filterByCategory Error: " . $e->getMessage());
            SessionHelper::setFlash('error', 'Có lỗi xảy ra khi lọc sách.');
            return [];
        }
    }
    
    /**
     * Search books by keyword
     * @param string $keyword Search keyword
     */
    public function searchBooks($keyword) {
        try {
            SessionHelper::start();
            
            if (empty($keyword)) {
                SessionHelper::setFlash('warning', 'Vui lòng nhập từ khóa tìm kiếm.');
                header('Location: index.php?page=books');
                exit;
            }
            
            $page = isset($_GET['page_num']) ? (int)$_GET['page_num'] : 1;
            $sortBy = isset($_GET['sort']) ? $_GET['sort'] : 'ten_sach';
            $order = isset($_GET['order']) ? $_GET['order'] : 'ASC';
            $limit = 12;
            
            // Search books
            $result = $this->booksModel->searchBooks($keyword, $page, $limit, $sortBy, $order);
            
            // Get categories for sidebar
            $categories = $this->categoriesModel->getAllCategories();
            
            $data = [
                'books' => $result['data'] ?? [],
                'total' => $result['total'] ?? 0,
                'current_page' => $page,
                'total_pages' => $result['total_pages'] ?? 0,
                'categories' => $categories,
                'keyword' => htmlspecialchars($keyword),
                'sort_by' => $sortBy,
                'order' => $order,
                'page_title' => 'Tìm kiếm: ' . htmlspecialchars($keyword)
            ];
            
            return $data;
            
        } catch (Exception $e) {
            error_log("BookController::searchBooks Error: " . $e->getMessage());
            SessionHelper::setFlash('error', 'Có lỗi xảy ra khi tìm kiếm sách.');
            return [];
        }
    }
    
    /**
     * Get single book details
     * @param int $bookId Book ID
     */
    public function getBookDetail($bookId) {
        try {
            SessionHelper::start();
            
            // Validate book ID
            if (empty($bookId) || !is_numeric($bookId)) {
                SessionHelper::setFlash('error', 'ID sách không hợp lệ.');
                header('Location: index.php?page=books');
                exit;
            }
            
            // Get book details (now includes author, publisher, category, and ratings)
            $book = $this->booksModel->getBookById($bookId);
            
            if (!$book) {
                SessionHelper::setFlash('error', 'Không tìm thấy sách.');
                header('Location: index.php?page=books');
                exit;
            }
            
            // Get book reviews
            $reviews = [];
            try {
                $sql = "SELECT dg.*, kh.ten_khachhang as ten_khach_hang
                        FROM danhgia dg
                        LEFT JOIN khachhang kh ON dg.id_khachhang = kh.id_khachhang
                        WHERE dg.id_sach = ?
                        ORDER BY dg.ngay_danhgia DESC";
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param("i", $bookId);
                $stmt->execute();
                $result = $stmt->get_result();
                while ($row = $result->fetch_assoc()) {
                    $reviews[] = [
                        'ten_khach_hang' => $row['ten_khach_hang'],
                        'diem' => $row['so_sao'],
                        'noi_dung' => $row['noi_dung'],
                        'ngay_danh_gia' => $row['ngay_danhgia']
                    ];
                }
            } catch (Exception $e) {
                error_log("Error getting reviews: " . $e->getMessage());
            }
            
            // Get related books (same category) - using correct table names
            $relatedBooks = [];
            if (!empty($book['ma_danh_muc'])) {
                try {
                    $sql = "SELECT s.id_sach as ma_sach, s.ten_sach, s.gia, s.gia_goc, s.isbn,
                                   tg.ten_tacgia as ten_tac_gia
                            FROM sach s
                            LEFT JOIN tacgia tg ON s.id_tacgia = tg.id_tacgia
                            WHERE s.id_theloai = ? AND s.id_sach != ?
                            ORDER BY RAND()
                            LIMIT 6";
                    $stmt = $this->conn->prepare($sql);
                    $stmt->bind_param("ii", $book['ma_danh_muc'], $bookId);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    while ($row = $result->fetch_assoc()) {
                        $relatedBooks[] = $row;
                    }
                } catch (Exception $e) {
                    error_log("Error getting related books: " . $e->getMessage());
                }
            }
            
            // Prepare data for view
            $data = [
                'book' => $book,
                'reviews' => $reviews,
                'related_books' => $relatedBooks,
                'page_title' => $book['ten_sach']
            ];
            
            return $data;
            
        } catch (Exception $e) {
            error_log("BookController::getBookDetail Error: " . $e->getMessage());
            SessionHelper::setFlash('error', 'Có lỗi xảy ra khi tải chi tiết sách.');
            header('Location: index.php?page=books');
            exit;
        }
    }
    
    /**
     * Sort books
     * @param string $sortBy Sort field
     * @param string $order Sort order (ASC/DESC)
     */
    public function sortBooks($sortBy, $order = 'ASC') {
        try {
            SessionHelper::start();
            
            // Validate sort parameters
            $allowedSortFields = ['ten_sach', 'gia', 'ngay_tao', 'luot_ban', 'luot_xem'];
            $allowedOrders = ['ASC', 'DESC'];
            
            if (!in_array($sortBy, $allowedSortFields)) {
                $sortBy = 'ngay_tao';
            }
            
            if (!in_array(strtoupper($order), $allowedOrders)) {
                $order = 'DESC';
            }
            
            // Redirect to list with sort parameters
            $params = $_GET;
            $params['sort'] = $sortBy;
            $params['order'] = $order;
            
            $queryString = http_build_query($params);
            header('Location: index.php?' . $queryString);
            exit;
            
        } catch (Exception $e) {
            error_log("BookController::sortBooks Error: " . $e->getMessage());
            SessionHelper::setFlash('error', 'Có lỗi xảy ra khi sắp xếp sách.');
            header('Location: index.php?page=books');
            exit;
        }
    }
    
    /**
     * Submit book review
     * POST action for customer reviews
     */
    public function submitReview() {
        try {
            SessionHelper::start();
            
            // Check if user is logged in
            if (!SessionHelper::isLoggedIn()) {
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Vui lòng đăng nhập để đánh giá sách.'
                    ]);
                    exit;
                }
                SessionHelper::setFlash('error', 'Vui lòng đăng nhập để đánh giá sách.');
                header('Location: index.php?page=login');
                exit;
            }
            
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Get form data
                $bookId = isset($_POST['book_id']) ? (int)$_POST['book_id'] : 0;
                $rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
                $title = isset($_POST['title']) ? trim($_POST['title']) : '';
                $content = isset($_POST['content']) ? trim($_POST['content']) : '';
                $customerId = SessionHelper::getCustomerId();
                
                // Validate input
                $validator = new Validator();
                $validator->required('book_id', $bookId);
                $validator->min('rating', $rating, 1);
                $validator->max('rating', $rating, 5);
                $validator->required('title', $title);
                $validator->minLength('title', $title, 3);
                $validator->required('content', $content);
                $validator->minLength('content', $content, 10);
                
                if ($validator->hasErrors()) {
                    echo json_encode([
                        'success' => false,
                        'message' => $validator->getFirstError()
                    ]);
                    exit;
                }
                
                // Add review
                $reviewData = [
                    'id_sach' => $bookId,
                    'id_khachhang' => $customerId,
                    'so_sao' => $rating,
                    'tieu_de' => Validator::sanitizeString($title),
                    'noi_dung' => Validator::sanitizeString($content)
                ];
                
                $result = $this->reviewsModel->addReview($reviewData);
                
                if ($result) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Đánh giá của bạn đã được gửi và đang chờ duyệt.'
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Có lỗi xảy ra khi gửi đánh giá.'
                    ]);
                }
                exit;
            }
            
        } catch (Exception $e) {
            error_log("BookController::submitReview Error: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi gửi đánh giá.'
            ]);
            exit;
        }
    }
}
