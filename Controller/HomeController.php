<?php
/**
 * HomeController
 * Handles homepage display with featured content
 */

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../Model/Books.php';
require_once __DIR__ . '/../Model/Banners.php';
require_once __DIR__ . '/../Model/Categories.php';
require_once __DIR__ . '/helpers/SessionHelper.php';

class HomeController extends BaseController {
    private $booksModel;
    private $bannersModel;
    private $categoriesModel;
    
    public function __construct($conn) {
        parent::__construct($conn);
        $this->booksModel = new Books($conn);
        $this->bannersModel = new Banners($conn);
        $this->categoriesModel = new Categories($conn);
    }
    
    /**
     * Load homepage with all content
     */
    public function index() {
        try {
            // Start session
            SessionHelper::start();
            
            // Get featured books (limit 8)
            $featuredBooks = $this->getFeaturedBooks(8);
            
            // Get best-selling books (limit 8)
            $bestsellingBooks = $this->getTopSellingBooks(8);
            
            // Get new arrivals (limit 8)
            $newArrivals = $this->getNewArrivals(8);
            
            // Get active banners
            $banners = $this->getBanners();
            
            // Get categories for navigation
            $categories = $this->getCategories();
            
            // Get additional data for new sections
            $statistics = $this->getStatistics();
            $featuredAuthors = $this->getFeaturedAuthors(6);
            $dealsOfTheDay = $this->getDealsOfTheDay(8);
            
            // Prepare data for view
            $data = [
                // Views expect these camelCase keys
                'featuredBooks' => $featuredBooks,
                'topSellingBooks' => $bestsellingBooks,
                'newArrivals' => $newArrivals,
                'banners' => $banners,
                'categories' => $categories,
                'statistics' => $statistics,
                'featuredAuthors' => $featuredAuthors,
                'dealsOfTheDay' => $dealsOfTheDay,
                'page_title' => 'Trang chủ - Nhà sách trực tuyến'
            ];
            
            return $data;
            
        } catch (Exception $e) {
            error_log("HomeController Error: " . $e->getMessage());
            SessionHelper::setFlash('error', 'Có lỗi xảy ra khi tải trang chủ.');
            return [];
        }
    }
    
    /**
     * Get featured books
     * @param int $limit Number of books to retrieve
     * @return array Featured books
     */
    public function getFeaturedBooks($limit = 8) {
        try {
            return $this->booksModel->getFeaturedBooks($limit);
        } catch (Exception $e) {
            error_log("Error getting featured books: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get top-selling books
     * @param int $limit Number of books to retrieve
     * @return array Best-selling books
     */
    public function getTopSellingBooks($limit = 8) {
        try {
            return $this->booksModel->getTopSellingBooks($limit);
        } catch (Exception $e) {
            error_log("Error getting top-selling books: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get new arrival books (recently added)
     * @param int $limit Number of books to retrieve
     * @return array New books
     */
    public function getNewArrivals($limit = 8) {
        try {
            // Get books ordered by newest first
            return $this->booksModel->getBooks(0, '', 0, '', 'newest', $limit, 0);
        } catch (Exception $e) {
            error_log("Error getting new arrivals: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get active promotional banners
     * @return array Active banners
     */
    public function getBanners() {
        try {
            return $this->bannersModel->getActiveBanners();
        } catch (Exception $e) {
            error_log("Error getting banners: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get all active categories
     * @return array Categories
     */
    public function getCategories() {
        try {
            return $this->categoriesModel->getAllCategories();
        } catch (Exception $e) {
            error_log("Error getting categories: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Search books (quick search from header)
     * @param string $keyword Search keyword
     * @return array Search results
     */
    public function quickSearch($keyword) {
        try {
            if (empty($keyword)) {
                return [];
            }
            
            // Limit quick search results to 5 items
            $results = $this->booksModel->searchBooks($keyword, 1, 5);
            
            // Return JSON for AJAX requests
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'data' => $results['data'] ?? []
                ]);
                exit;
            }
            
            return $results['data'] ?? [];
            
        } catch (Exception $e) {
            error_log("Error in quick search: " . $e->getMessage());
            
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'Có lỗi xảy ra khi tìm kiếm.'
                ]);
                exit;
            }
            
            return [];
        }
    }
    
    /**
     * Get site statistics for homepage
     * @return array Statistics data
     */
    public function getStatistics() {
        try {
            $stats = [];
            
            // Total books (all active books)
            $sql = "SELECT COUNT(*) as total FROM sach";
            $result = $this->conn->query($sql);
            $stats['total_books'] = $result->fetch_assoc()['total'] ?? 0;
            
            // Total customers
            $sql = "SELECT COUNT(*) as total FROM khachhang";
            $result = $this->conn->query($sql);
            $stats['total_customers'] = $result->fetch_assoc()['total'] ?? 0;
            
            // Total orders (all orders)
            $sql = "SELECT COUNT(*) as total FROM hoadon";
            $result = $this->conn->query($sql);
            $stats['total_orders'] = $result->fetch_assoc()['total'] ?? 0;
            
            // Total authors
            $sql = "SELECT COUNT(*) as total FROM tacgia";
            $result = $this->conn->query($sql);
            $stats['total_authors'] = $result->fetch_assoc()['total'] ?? 0;
            
            return $stats;
        } catch (Exception $e) {
            error_log("Error getting statistics: " . $e->getMessage());
            return [
                'total_books' => 0,
                'total_customers' => 0,
                'total_orders' => 0,
                'total_authors' => 0
            ];
        }
    }
    
    /**
     * Get featured authors with their book counts
     * @param int $limit Number of authors to retrieve
     * @return array Featured authors
     */
    public function getFeaturedAuthors($limit = 6) {
        try {
            $sql = "SELECT 
                        tg.id_tacgia as ma_tac_gia,
                        tg.ten_tacgia as ten_tac_gia,
                        tg.but_danh,
                        tg.tieu_su,
                        tg.quoc_tich,
                        COUNT(s.id_sach) as so_luong_sach,
                        0 as tong_ban
                    FROM tacgia tg
                    LEFT JOIN sach s ON tg.id_tacgia = s.id_tacgia
                    GROUP BY tg.id_tacgia
                    HAVING so_luong_sach > 0
                    ORDER BY so_luong_sach DESC
                    LIMIT ?";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $limit);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $authors = [];
            while ($row = $result->fetch_assoc()) {
                $authors[] = $row;
            }
            
            return $authors;
        } catch (Exception $e) {
            error_log("Error getting featured authors: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get discounted books (deals of the day)
     * @param int $limit Number of books to retrieve
     * @return array Discounted books
     */
    public function getDealsOfTheDay($limit = 8) {
        try {
            $sql = "SELECT 
                        s.*,
                        tg.ten_tacgia as ten_tac_gia,
                        tl.ten_theloai as ten_danh_muc,
                        nxb.ten_nxb,
                        (s.gia_goc - s.gia) as tiet_kiem,
                        ROUND(((s.gia_goc - s.gia) / s.gia_goc * 100), 0) as phan_tram_giam
                    FROM sach s
                    LEFT JOIN tacgia tg ON s.id_tacgia = tg.id_tacgia
                    LEFT JOIN theloai tl ON s.id_theloai = tl.id_theloai
                    LEFT JOIN nhaxuatban nxb ON s.id_nxb = nxb.id_nxb
                    WHERE s.gia_goc > s.gia
                    AND s.so_luong > 0
                    ORDER BY phan_tram_giam DESC
                    LIMIT ?";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $limit);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $deals = [];
            while ($row = $result->fetch_assoc()) {
                $deals[] = $row;
            }
            
            return $deals;
        } catch (Exception $e) {
            error_log("Error getting deals: " . $e->getMessage());
            return [];
        }
    }
}
