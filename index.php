<?php
/**
 * BookStore - Main Entry Point
 * 
 * This is the main entry point for the BookStore application.
 * All requests are routed through this file using MVC pattern.
 * 
 * Routing Logic:
 * - GET parameter 'page' determines which controller/action to load
 * - GET parameter 'action' for AJAX requests
 * - POST requests are handled by controllers with CSRF protection
 * 
 * @author BookStore Development Team
 * @version 2.0
 * @since 2025-12-16
 */

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define base constants
define('BASE_URL', 'http://localhost/book_store/');
define('BASE_PATH', __DIR__ . '/');

// Include database connection
require_once BASE_PATH . 'Model/connect.php';

// Include SessionHelper (must be before any session operations)
require_once BASE_PATH . 'Controller/helpers/SessionHelper.php';

// Start session via SessionHelper
SessionHelper::start();

// Get routing parameters
$page = $_GET['page'] ?? 'home';
$action = $_GET['action'] ?? 'index';

// Initialize variables for view
$pageTitle = 'BookStore - Cửa hàng sách trực tuyến';
$viewData = [];
$viewFile = null;

try {
    // Route to appropriate controller
    switch ($page) {
        // ========== HOME ==========
        case 'home':
            require_once BASE_PATH . 'Controller/HomeController.php';
            $controller = new HomeController($conn);
            $viewData = $controller->index();
            $viewFile = 'View/home.php';
            $pageTitle = 'Trang chủ - BookStore';
            break;
        
        // ========== BOOKS ==========
        case 'books':
            require_once BASE_PATH . 'Controller/BookController.php';
            $controller = new BookController($conn);
            $viewData = $controller->listBooks();
            $viewFile = 'View/books.php';
            $pageTitle = 'Danh sách sách - BookStore';
            break;
        
        case 'book_detail':
            require_once BASE_PATH . 'Controller/BookController.php';
            $controller = new BookController($conn);
            $bookId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            $viewData = $controller->getBookDetail($bookId);
            $viewFile = 'View/book_detail.php';
            $pageTitle = ($viewData['book']['ten_sach'] ?? 'Chi tiết sách') . ' - BookStore';
            break;
        
        case 'search':
            require_once BASE_PATH . 'Controller/BookController.php';
            $controller = new BookController($conn);
            $viewData = $controller->searchBooks($_GET['keyword'] ?? '');
            $viewFile = 'View/books.php';
            $pageTitle = 'Tìm kiếm sách - BookStore';
            break;
        
        case 'category':
            require_once BASE_PATH . 'Controller/BookController.php';
            $controller = new BookController($conn);
            $categoryId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            $viewData = $controller->filterByCategory($categoryId);
            $viewFile = 'View/books.php';
            $pageTitle = 'Sách theo danh mục - BookStore';
            break;
        
        // ========== AUTHENTICATION ==========
        case 'login':
            require_once BASE_PATH . 'Controller/LoginController.php';
            $controller = new LoginController($conn);
            
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->login();
            } else {
                $viewData = $controller->showForm();
                $viewFile = 'View/login.php';
                $pageTitle = 'Đăng nhập - BookStore';
            }
            break;
        
        case 'logout':
            require_once BASE_PATH . 'Controller/LoginController.php';
            $controller = new LoginController($conn);
            $controller->logout();
            break;
        
        case 'register':
            require_once BASE_PATH . 'Controller/RegistrationController.php';
            $controller = new RegistrationController($conn);
            
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->register();
            } else {
                $viewData = $controller->showForm();
                $viewFile = 'View/register.php';
                $pageTitle = 'Đăng ký tài khoản - BookStore';
            }
            break;
        
        case 'forgot_password':
            require_once BASE_PATH . 'Controller/ForgetController.php';
            $controller = new ForgetController($conn);
            
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->requestPasswordReset();
            } else {
                $viewData = $controller->showRequestForm();
                $viewFile = 'View/forgot_password.php';
                $pageTitle = 'Quên mật khẩu - BookStore';
            }
            break;
        
        case 'reset_password':
            require_once BASE_PATH . 'Controller/ForgetController.php';
            $controller = new ForgetController($conn);
            
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->resetPassword();
            } else {
                $viewData = $controller->showResetForm();
                $viewFile = 'View/reset_password.php';
                $pageTitle = 'Đặt lại mật khẩu - BookStore';
            }
            break;
        
        // ========== SHOPPING CART ==========
        case 'cart':
            require_once BASE_PATH . 'Controller/CartController.php';
            $controller = new CartController($conn);
            $viewData = $controller->showCart();
            $viewFile = 'View/cart.php';
            $pageTitle = 'Giỏ hàng - BookStore';
            break;
        
        case 'add_to_cart':
            require_once BASE_PATH . 'Controller/CartController.php';
            $controller = new CartController($conn);
            $controller->addToCart();
            break;
        
        case 'update_cart':
            require_once BASE_PATH . 'Controller/CartController.php';
            $controller = new CartController($conn);
            $controller->updateQuantity();
            break;
        
        case 'remove_from_cart':
            require_once BASE_PATH . 'Controller/CartController.php';
            $controller = new CartController($conn);
            $controller->removeItem();
            break;
        
        case 'clear_cart':
            require_once BASE_PATH . 'Controller/CartController.php';
            $controller = new CartController($conn);
            $controller->clearCart();
            break;
        
        // ========== CHECKOUT & ORDERS ==========
        case 'checkout':
            require_once BASE_PATH . 'Controller/OrderController.php';
            $controller = new OrderController($conn);
            
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Process payment - redirect to payment gateway
                $controller->processPayment();
            } else {
                $viewData = $controller->showCheckout();
                $viewFile = 'View/checkout.php';
                $pageTitle = 'Thanh toán - BookStore';
            }
            break;
        
        case 'create_order':
            require_once BASE_PATH . 'Controller/OrderController.php';
            $controller = new OrderController($conn);
            $controller->createOrder();
            break;
        
        case 'payment_callback':
            require_once BASE_PATH . 'Controller/OrderController.php';
            $controller = new OrderController($conn);
            $controller->handlePaymentCallback();
            break;
        
        case 'order_confirmation':
            require_once BASE_PATH . 'Controller/OrderController.php';
            $controller = new OrderController($conn);
            $viewData = $controller->confirmOrder();
            $viewFile = 'View/order_confirmation.php';
            $pageTitle = 'Xác nhận đơn hàng - BookStore';
            break;
        
        case 'orders':
            require_once BASE_PATH . 'Controller/OrderController.php';
            $controller = new OrderController($conn);
            $viewData = $controller->viewOrders();
            $viewFile = 'View/orders.php';
            $pageTitle = 'Đơn hàng của tôi - BookStore';
            break;
        
        case 'order_detail':
            require_once BASE_PATH . 'Controller/OrderController.php';
            $controller = new OrderController($conn);
            $viewData = $controller->viewOrderDetail();
            $viewFile = 'View/order_detail.php';
            $pageTitle = 'Chi tiết đơn hàng - BookStore';
            break;
        
        case 'cancel_order':
            require_once BASE_PATH . 'Controller/OrderController.php';
            $controller = new OrderController($conn);
            $controller->cancelOrder();
            break;
        
        // ========== AJAX ACTIONS ==========
        case 'ajax_quick_search':
            require_once BASE_PATH . 'Controller/HomeController.php';
            $controller = new HomeController($conn);
            $controller->quickSearch($_GET['keyword'] ?? '');
            break;
        
        case 'ajax_submit_review':
            require_once BASE_PATH . 'Controller/BookController.php';
            $controller = new BookController($conn);
            $controller->submitReview();
            break;
        
        case 'ajax_validate_email':
            require_once BASE_PATH . 'Controller/RegistrationController.php';
            $controller = new RegistrationController($conn);
            $controller->validateEmail();
            break;
        
        case 'ajax_cart_summary':
            require_once BASE_PATH . 'Controller/CartController.php';
            $controller = new CartController($conn);
            $controller->getCartSummary();
            break;

        // ========== BOOK COVER REDIRECT (PROXY) ==========
        // Returns a 302 redirect to a real image URL resolved from the hosted BookCover API.
        // Usage: ?page=cover&isbn=9780345376596
        case 'cover':
            require_once BASE_PATH . 'Controller/CoverController.php';
            $controller = new CoverController($conn);
            $controller->redirectByIsbn($_GET['isbn'] ?? '');
            break;
        
        // ========== 404 NOT FOUND ==========
        default:
            http_response_code(404);
            $viewFile = 'View/404.php';
            $pageTitle = '404 - Không tìm thấy trang';
            break;
    }
    
} catch (Exception $e) {
    // Log error
    error_log("Error in routing: " . $e->getMessage());
    
    // Show error page
    SessionHelper::setFlash('error', 'Đã xảy ra lỗi. Vui lòng thử lại.');
    $viewFile = 'View/error.php';
    $pageTitle = 'Lỗi - BookStore';
    $viewData['error'] = $e->getMessage();
}

// Render view if not already handled by controller (redirect/JSON response)
if ($viewFile && file_exists(BASE_PATH . $viewFile)) {
    ?>
    <!DOCTYPE html>
    <html lang="vi">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo htmlspecialchars($pageTitle); ?></title>
        <link rel="stylesheet" href="<?php echo BASE_URL; ?>Content/CSS/bookstore.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    </head>
    <body>
        <?php
        // Include header (if exists)
        if (file_exists(BASE_PATH . 'View/header.php')) {
            include_once BASE_PATH . 'View/header.php';
        }
        ?>
        
        <main class="main-content">
            <?php
            // Display flash messages
            $flashMessages = SessionHelper::getAllFlash();
            if (!empty($flashMessages)) {
                foreach ($flashMessages as $type => $message) {
                    $alertClass = $type === 'error' ? 'danger' : $type;
                    echo "<div class='alert alert-{$alertClass} alert-dismissible fade show' role='alert'>";
                    echo htmlspecialchars($message);
                    echo "<button type='button' class='close' data-dismiss='alert'>&times;</button>";
                    echo "</div>";
                }
            }
            
            // Extract view data to make variables available in view
            if (isset($viewData) && is_array($viewData)) {
                extract($viewData);
            }
            
            // Include the view file
            include BASE_PATH . $viewFile;
            ?>
        </main>
        
        <?php
        // Include footer (if exists)
        if (file_exists(BASE_PATH . 'View/footer.php')) {
            include_once BASE_PATH . 'View/footer.php';
        }
        ?>
        
        <!-- jQuery and Bootstrap JS -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>
        
        <!-- Custom JavaScript -->
        <script>
            // AJAX setup for CSRF tokens
            $.ajaxSetup({
                headers: {
                    'X-CSRF-Token': '<?php echo SessionHelper::get("csrf_token", ""); ?>'
                }
            });
        </script>
    </body>
    </html>
    <?php
} elseif ($viewFile) {
    // View file doesn't exist
    http_response_code(404);
    echo "<h1>404 - View file not found</h1>";
    echo "<p>The requested view file does not exist: " . htmlspecialchars($viewFile) . "</p>";
}
