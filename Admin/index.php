<?php
/**
 * Admin Panel - Entry Point
 * 
 * This is the entry point for the Admin management panel.
 * All admin requests are routed through this file.
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
define('ADMIN_BASE_URL', 'http://localhost/book_store/Admin/');
define('ADMIN_BASE_PATH', __DIR__ . '/');
define('BASE_URL', 'http://localhost/book_store/');

// Include database connection
require_once ADMIN_BASE_PATH . 'Model/connect.php';

// Check if admin is logged in
// if (!isset($_SESSION['admin_id'])) {
//     header('Location: ' . ADMIN_BASE_URL . 'View/login.php');
//     exit();
// }

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - BookStore Management</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>Content/CSS/bookstore.css">
</head>
<body>
    <div class="admin-container">
        <?php
        // Include admin header
        // include_once ADMIN_BASE_PATH . 'View/header.php';
        ?>
        
        <main class="admin-content">
            <h1>Welcome to Admin Panel</h1>
            <p>Admin panel is under development.</p>
        </main>
        
        <?php
        // Include admin footer
        // include_once ADMIN_BASE_PATH . 'View/footer.php';
        ?>
    </div>
    
    <!-- jQuery and Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
