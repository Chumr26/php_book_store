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
            
            // Prepare data for view
            $data = [
                'featured_books' => $featuredBooks,
                'bestselling_books' => $bestsellingBooks,
                'new_arrivals' => $newArrivals,
                'banners' => $banners,
                'categories' => $categories,
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
            // Get books ordered by creation date (newest first)
            $books = $this->booksModel->getAllBooks(1, $limit, 'ngay_tao', 'DESC');
            return $books['data'] ?? [];
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
}
