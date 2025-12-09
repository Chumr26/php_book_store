<?php
/**
 * BookStore - Main Entry Point
 * 
 * This is the main entry point for the BookStore application.
 * All requests are routed through this file.
 * 
 * @author BookStore Development Team
 * @version 1.0
 * @since 2025-12-09
 */

// Start session
session_start();

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define base constants
define('BASE_URL', 'http://localhost/book_store/');
define('BASE_PATH', __DIR__ . '/');

// Include database connection
require_once BASE_PATH . 'Model/connect.php';

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookStore - Cửa hàng sách trực tuyến</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>Content/CSS/bookstore.css">
</head>
<body>
    <?php
    // Include header
    include_once BASE_PATH . 'View/header.php';
    ?>
    
    <main class="main-content">
        <?php
        // Include homepage
        include_once BASE_PATH . 'View/home.php';
        ?>
    </main>
    
    <?php
    // Include footer
    include_once BASE_PATH . 'View/footer.php';
    ?>
    
    <!-- jQuery and Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
