<?php

/**
 * Database Connection File - Front-end
 * 
 * This file establishes a connection to the MySQL database
 * using MySQLi with prepared statement support.
 * 
 * Configuration:
 * - Server: localhost
 * - Username: root
 * - Password: (blank for default XAMPP)
 * - Database: bookstore
 * - Charset: utf8mb4 (supports international characters)
 */

// Database configuration parameters

$servername = getenv('DB_HOST') ?: "localhost";
$username = getenv('DB_USER') ?: "root";
$password = getenv('DB_PASS') !== false ? getenv('DB_PASS') : ""; // Blank for default XAMPP
$dbname = getenv('DB_NAME') ?: "bookstore";
$port = getenv('DB_PORT') ?: 3306;

// Create connection using MySQLi
$conn = mysqli_init();

// Enable SSL if configured (Required for TiDB Cloud)
// Enable SSL if configured OR if using TiDB Cloud (which requires SSL)
$is_tidb = strpos($servername, 'tidbcloud') !== false;
if (getenv('DB_SSL') === 'true' || $is_tidb) {
    // TiDB requires SSL
    $conn->ssl_set(NULL, NULL, NULL, NULL, NULL);
    if (!$conn->real_connect($servername, $username, $password, $dbname, (int)$port, NULL, MYSQLI_CLIENT_SSL)) {
        error_log("Database Connection Failed (SSL): " . $conn->connect_error);
        die("Không thể kết nối đến cơ sở dữ liệu (SSL). Vui lòng thử lại sau.");
    }
} else {
    // Standard connection for local/non-SSL
    if (!$conn->real_connect($servername, $username, $password, $dbname, (int)$port)) {
        error_log("Database Connection Failed: " . $conn->connect_error);
        die("Không thể kết nối đến cơ sở dữ liệu. Vui lòng thử lại sau.");
    }
}




// Set character set to UTF-8 (utf8mb4 for full Unicode support including emojis)
if (!$conn->set_charset("utf8mb4")) {
    error_log("Error loading character set utf8mb4: " . $conn->error);
    die("Lỗi thiết lập mã hóa ký tự.");
}

// Optional: Set timezone (adjust as needed)
// $conn->query("SET time_zone = '+07:00'"); // UTC+7 for Vietnam

// Connection successful - no output needed
// The $conn object is now ready to use throughout the application

/**
 * Usage Example:
 * 
 * require_once 'Model/connect.php';
 * 
 * // Using prepared statement
 * $stmt = $conn->prepare("SELECT * FROM books WHERE id_book = ?");
 * $stmt->bind_param("i", $book_id);
 * $stmt->execute();
 * $result = $stmt->get_result();
 * 
 * // Always close connection when done
 * $conn->close();
 */
