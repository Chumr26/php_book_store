<?php

/**
 * Admin Panel - Main Entry Point
 * 
 * This is the main entry point for the Admin management panel.
 * All admin requests are routed through this file using MVC pattern.
 * 
 * Routing Logic:
 * - GET parameter 'page' determines which controller/action to load
 * - All routes require admin authentication
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
define('ADMIN_BASE_URL', 'http://localhost/book_store/Admin/');
define('ADMIN_BASE_PATH', __DIR__ . '/');
define('BASE_URL', 'http://localhost/book_store/');
define('BASE_PATH', dirname(__DIR__) . '/');

// Include database connection
require_once ADMIN_BASE_PATH . 'Model/connect.php';

// Include SessionHelper
require_once BASE_PATH . 'Controller/helpers/SessionHelper.php';

// Start session via SessionHelper
SessionHelper::start();

// Get routing parameters
$page = $_GET['page'] ?? '';

// If no page specified and not logged in, default to login
if (empty($page) && !SessionHelper::isAdminLoggedIn()) {
    $page = 'login';
} elseif (empty($page)) {
    $page = 'dashboard';
}

// Check admin authentication (except for login + dev quick login)
// dev_quick_login is a localhost-only helper invoked from the login page.
if (!in_array($page, ['login', 'dev_quick_login'], true) && !SessionHelper::isAdminLoggedIn()) {
    SessionHelper::setFlash('warning', 'Vui lòng đăng nhập để tiếp tục');
    header('Location: ' . ADMIN_BASE_URL . 'index.php?page=login');
    exit;
}

// Initialize variables for view
$pageTitle = 'Admin - BookStore Management';
$viewData = [];
$viewFile = null;

try {
    // Route to appropriate admin controller
    switch ($page) {
        // ========== AUTHENTICATION ==========
        case 'login':
            require_once ADMIN_BASE_PATH . 'Controller/AdminAuthController.php';
            $controller = new AdminAuthController($conn);
            $viewData = $controller->login();

            $viewFile = 'View/login.php';
            $pageTitle = 'Admin Login - BookStore';
            break;

        case 'dev_quick_login':
            require_once ADMIN_BASE_PATH . 'Controller/AdminAuthController.php';
            $controller = new AdminAuthController($conn);
            $controller->devQuickLogin();
            break;

        case 'logout':
            require_once ADMIN_BASE_PATH . 'Controller/AdminAuthController.php';
            $controller = new AdminAuthController($conn);
            $controller->logout();
            break;

        // ========== PROFILE ==========
        case 'profile':
            require_once ADMIN_BASE_PATH . 'Controller/AdminProfileController.php';
            $controller = new AdminProfileController($conn);

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->handlePost();
            } else {
                $viewData = $controller->show();
                $viewFile = 'View/profile.php';
                $pageTitle = 'Hồ sơ - Admin BookStore';
            }
            break;

        // ========== DASHBOARD ==========
        case 'dashboard':
            require_once ADMIN_BASE_PATH . 'Controller/AdminDashboardController.php';
            $controller = new AdminDashboardController($conn);
            $viewData = $controller->index();
            $viewFile = 'View/dashboard.php';
            $pageTitle = 'Dashboard - Admin BookStore';
            break;

        case 'export_statistics':
            require_once ADMIN_BASE_PATH . 'Controller/AdminDashboardController.php';
            $controller = new AdminDashboardController($conn);
            $controller->exportStatistics();
            break;

        // ========== BOOK MANAGEMENT ==========
        case 'books':
            require_once ADMIN_BASE_PATH . 'Controller/AdminBookController.php';
            $controller = new AdminBookController($conn);
            $viewData = $controller->index();
            $viewFile = 'View/books/index.php';
            $pageTitle = 'Quản lý sách - Admin BookStore';
            break;

        case 'book_create':
            require_once ADMIN_BASE_PATH . 'Controller/AdminBookController.php';
            $controller = new AdminBookController($conn);

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->store();
            } else {
                $viewData = $controller->create();
                $viewFile = 'View/books/create.php';
                $pageTitle = 'Thêm sách mới - Admin BookStore';
            }
            break;

        case 'book_edit':
            require_once ADMIN_BASE_PATH . 'Controller/AdminBookController.php';
            $controller = new AdminBookController($conn);

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->update();
            } else {
                $viewData = $controller->edit();
                $viewFile = 'View/books/edit.php';
                $pageTitle = 'Sửa sách - Admin BookStore';
            }
            break;

        case 'book_delete':
            require_once ADMIN_BASE_PATH . 'Controller/AdminBookController.php';
            $controller = new AdminBookController($conn);
            $controller->delete();
            break;

        case 'book_bulk_delete':
            require_once ADMIN_BASE_PATH . 'Controller/AdminBookController.php';
            $controller = new AdminBookController($conn);
            $controller->bulkDelete();
            break;

        case 'book_toggle_status':
            require_once ADMIN_BASE_PATH . 'Controller/AdminBookController.php';
            $controller = new AdminBookController($conn);
            $controller->toggleStatus();
            break;

        // ========== ORDER MANAGEMENT ==========
        case 'orders':
            require_once ADMIN_BASE_PATH . 'Controller/AdminOrderController.php';
            $controller = new AdminOrderController($conn);
            $viewData = $controller->index();
            $viewFile = 'View/orders/index.php';
            $pageTitle = 'Quản lý đơn hàng - Admin BookStore';
            break;

        case 'order_detail':
            require_once ADMIN_BASE_PATH . 'Controller/AdminOrderController.php';
            $controller = new AdminOrderController($conn);
            $viewData = $controller->show();
            $viewFile = 'View/orders/detail.php';
            $pageTitle = 'Chi tiết đơn hàng - Admin BookStore';
            break;

        case 'order_update_status':
            require_once ADMIN_BASE_PATH . 'Controller/AdminOrderController.php';
            $controller = new AdminOrderController($conn);
            $controller->updateStatus();
            break;

        case 'orders_export':
            require_once ADMIN_BASE_PATH . 'Controller/AdminOrderController.php';
            $controller = new AdminOrderController($conn);
            $controller->exportOrders();
            break;

        case 'order_print_invoice':
            require_once ADMIN_BASE_PATH . 'Controller/AdminOrderController.php';
            $controller = new AdminOrderController($conn);
            $controller->printInvoice();
            break;

        // ========== CATEGORY MANAGEMENT ==========
        case 'categories':
            require_once ADMIN_BASE_PATH . 'Controller/AdminCategoryController.php';
            $controller = new AdminCategoryController($conn);
            $viewData = $controller->index();
            $viewFile = 'View/categories/index.php';
            $pageTitle = 'Quản lý danh mục - Admin BookStore';
            break;

        case 'category_create':
            require_once ADMIN_BASE_PATH . 'Controller/AdminCategoryController.php';
            $controller = new AdminCategoryController($conn);

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->store();
            } else {
                $viewData = $controller->create();
                $viewFile = 'View/categories/create.php';
                $pageTitle = 'Thêm danh mục - Admin BookStore';
            }
            break;

        case 'category_edit':
            require_once ADMIN_BASE_PATH . 'Controller/AdminCategoryController.php';
            $controller = new AdminCategoryController($conn);

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->update();
            } else {
                $viewData = $controller->edit();
                $viewFile = 'View/categories/edit.php';
                $pageTitle = 'Sửa danh mục - Admin BookStore';
            }
            break;

        case 'category_delete':
            require_once ADMIN_BASE_PATH . 'Controller/AdminCategoryController.php';
            $controller = new AdminCategoryController($conn);
            $controller->delete();
            break;

        case 'category_update_order':
            require_once ADMIN_BASE_PATH . 'Controller/AdminCategoryController.php';
            $controller = new AdminCategoryController($conn);
            $controller->updateOrder();
            break;

        case 'category_bulk_delete':
            require_once ADMIN_BASE_PATH . 'Controller/AdminCategoryController.php';
            $controller = new AdminCategoryController($conn);
            $controller->bulkDelete();
            break;

        // ========== CUSTOMER MANAGEMENT ==========
        case 'customers':
            require_once ADMIN_BASE_PATH . 'Controller/AdminCustomerController.php';
            $controller = new AdminCustomerController($conn);
            $viewData = $controller->index();
            $viewFile = 'View/customers/index.php';
            $pageTitle = 'Quản lý khách hàng - Admin BookStore';
            break;

        case 'customer_detail':
            require_once ADMIN_BASE_PATH . 'Controller/AdminCustomerController.php';
            $controller = new AdminCustomerController($conn);
            $viewData = $controller->show();
            $viewFile = 'View/customers/detail.php';
            $pageTitle = 'Chi tiết khách hàng - Admin BookStore';
            break;

        case 'customer_update_status':
            require_once ADMIN_BASE_PATH . 'Controller/AdminCustomerController.php';
            $controller = new AdminCustomerController($conn);
            $controller->updateStatus();
            break;

        case 'customer_delete':
            require_once ADMIN_BASE_PATH . 'Controller/AdminCustomerController.php';
            $controller = new AdminCustomerController($conn);
            $controller->delete();
            break;

        case 'customer_bulk_delete':
            require_once ADMIN_BASE_PATH . 'Controller/AdminCustomerController.php';
            $controller = new AdminCustomerController($conn);
            $controller->bulkDelete();
            break;

        case 'customers_export':
            require_once ADMIN_BASE_PATH . 'Controller/AdminCustomerController.php';
            $controller = new AdminCustomerController($conn);
            $controller->exportCustomers();
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
    error_log("Error in admin routing: " . $e->getMessage());

    // Show error page
    SessionHelper::setFlash('error', 'Đã xảy ra lỗi. Vui lòng thử lại.');
    $viewFile = 'View/error.php';
    $pageTitle = 'Lỗi - Admin BookStore';
    $viewData['error'] = $e->getMessage();
}

// Render view if not already handled by controller (redirect/JSON response)
if ($viewFile && file_exists(ADMIN_BASE_PATH . $viewFile)) {
?>
    <!DOCTYPE html>
    <html lang="vi">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo htmlspecialchars($pageTitle); ?></title>
        <link rel="stylesheet" href="<?php echo BASE_URL; ?>Content/CSS/bookstore.css">
        <link rel="stylesheet" href="<?php echo BASE_URL; ?>Content/CSS/admin.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
    </head>

    <body class="admin-body">
        <script>
            // Check for saved sidebar state immediately to prevent FOUC
            (function() {
                var sidebar = localStorage.getItem('sb|sidebar-toggle');
                if (sidebar === 'true') {
                    document.body.classList.add('sidebar-toggled');
                }
            })();
        </script>
        <div class="admin-wrapper">
            <?php
            // Include admin sidebar (if exists and user is logged in)
            if (SessionHelper::isAdminLoggedIn() && file_exists(ADMIN_BASE_PATH . 'View/sidebar.php')) {
                include_once ADMIN_BASE_PATH . 'View/sidebar.php';
            }
            ?>

            <div class="admin-content-wrapper">
                <?php
                // Include admin header (if exists and user is logged in)
                if (SessionHelper::isAdminLoggedIn() && file_exists(ADMIN_BASE_PATH . 'View/header.php')) {
                    include_once ADMIN_BASE_PATH . 'View/header.php';
                }
                ?>

                <main class="admin-content">
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
                    include ADMIN_BASE_PATH . $viewFile;
                    ?>
                </main>

                <?php
                // Include admin footer (if exists)
                if (file_exists(ADMIN_BASE_PATH . 'View/footer.php')) {
                    include_once ADMIN_BASE_PATH . 'View/footer.php';
                }
                ?>
            </div>
        </div>

        <!-- jQuery and Bootstrap JS -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.js"></script>

        <!-- Custom Admin JavaScript -->

        <script src="<?php echo BASE_URL; ?>Content/JS/admin.js"></script>
        <script>
            // Fallback: ensure modal helpers exist on all admin pages
            (function() {
                if (typeof window.showMessageModal !== 'function') {
                    window.showMessageModal = function(title, message) {
                        if (typeof jQuery === 'undefined') return;
                        jQuery('#globalMessageTitle').text(title || 'Thông báo');
                        jQuery('#globalMessageContent').html(message || '');
                        jQuery('#globalMessageModal').modal('show');
                    };
                }

                if (typeof window.showConfirmModal !== 'function') {
                    var globalConfirmCallback = null;
                    window.showConfirmModal = function(message, callback) {
                        if (typeof jQuery === 'undefined') return;
                        jQuery('#globalConfirmMessage').text(message || 'Bạn có chắc chắn muốn thực hiện hành động này?');
                        jQuery('#globalConfirmModal').modal('show');
                        globalConfirmCallback = callback || null;
                    };

                    jQuery(document).on('click', '#globalConfirmBtn', function() {
                        if (!globalConfirmCallback) {
                            jQuery('#globalConfirmModal').modal('hide');
                            return;
                        }
                        var cb = globalConfirmCallback;
                        globalConfirmCallback = null;
                        jQuery('#globalConfirmModal').modal('hide');
                        cb();
                    });
                }
            })();
        </script>
        <script>
            // AJAX setup for CSRF tokens
            $.ajaxSetup({
                headers: {
                    'X-CSRF-Token': '<?php echo SessionHelper::get("csrf_token", ""); ?>'
                }
            });

            // Initialize DataTables
            $(document).ready(function() {
                if ($.fn.DataTable) {
                    $('.data-table').DataTable({
                        "language": {
                            "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/vi.json"
                        }
                    });
                }
            });
        </script>

        <script>
            // Global form submit UX: disable submit + show loading label (POST only)
            // - Skips forms that preventDefault (AJAX flows)
            // - Skips forms opting out via data-no-loading="1"
            (function() {
                function isPostForm(form) {
                    var method = (form.getAttribute('method') || '').toLowerCase();
                    return method === 'post';
                }

                document.addEventListener('submit', function(e) {
                    try {
                        var form = e.target;
                        if (!form || form.nodeName !== 'FORM') return;
                        if (!isPostForm(form)) return;
                        if (form.getAttribute('data-no-loading') === '1') return;
                        if (e.defaultPrevented) return;

                        var submitButtons = form.querySelectorAll('button[type="submit"], input[type="submit"]');
                        if (!submitButtons || submitButtons.length === 0) return;

                        submitButtons.forEach(function(btn) {
                            if (btn.disabled) return;
                            btn.disabled = true;

                            var loadingText = btn.getAttribute('data-loading-text') || 'Đang xử lý...';

                            if (!btn.getAttribute('data-original-label')) {
                                btn.setAttribute('data-original-label', btn.innerHTML || btn.value || '');
                            }

                            if (btn.tagName === 'INPUT') {
                                btn.value = loadingText;
                                return;
                            }

                            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>' + loadingText;
                        });
                    } catch (err) {
                        // no-op
                    }
                }, false);
            })();
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
