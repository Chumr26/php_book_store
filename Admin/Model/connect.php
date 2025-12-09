<?php
/**
 * Database Connection File - Admin Panel
 * 
 * This file establishes a connection to the MySQL database
 * for the admin panel using MySQLi with prepared statement support.
 * 
 * Configuration:
 * - Server: localhost
 * - Username: root
 * - Password: (blank for default XAMPP)
 * - Database: bookstore
 * - Charset: utf8mb4 (supports international characters)
 */

// Database configuration parameters
$servername = "localhost";
$username = "root";
$password = ""; // Blank for default XAMPP installation
$dbname = "bookstore";

// Create connection using MySQLi
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    // Log error and display user-friendly message
    error_log("Admin Database Connection Failed: " . $conn->connect_error);
    die("Không thể kết nối đến cơ sở dữ liệu. Vui lòng kiểm tra cấu hình.");
}

// Set character set to UTF-8 (utf8mb4 for full Unicode support including emojis)
if (!$conn->set_charset("utf8mb4")) {
    error_log("Error loading character set utf8mb4: " . $conn->error);
    die("Lỗi thiết lập mã hóa ký tự.");
}

// Optional: Set timezone (adjust as needed)
// $conn->query("SET time_zone = '+07:00'"); // UTC+7 for Vietnam

// Connection successful - no output needed
// The $conn object is now ready to use throughout the admin application

/**
 * Usage Example:
 * 
 * require_once 'Admin/Model/connect.php';
 * 
 * // Using prepared statement for secure queries
 * $stmt = $conn->prepare("INSERT INTO books (title, price) VALUES (?, ?)");
 * $stmt->bind_param("sd", $title, $price);
 * $stmt->execute();
 * 
 * // Always close connection when done
 * $conn->close();
 */
?>
