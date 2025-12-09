<?php
/**
 * Database Connection Test Script
 * 
 * This script tests both front-end and admin database connections
 * Run this file in your browser: http://localhost/book_store/test_connection.php
 */

echo "<!DOCTYPE html>
<html lang='vi'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Database Connection Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f4f4f4; }
        .container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); max-width: 800px; margin: 0 auto; }
        h1 { color: #333; border-bottom: 3px solid #007bff; padding-bottom: 10px; }
        .test-section { margin: 20px 0; padding: 15px; border-left: 4px solid #007bff; background: #f8f9fa; }
        .success { color: #28a745; font-weight: bold; }
        .error { color: #dc3545; font-weight: bold; }
        .info { color: #17a2b8; }
        pre { background: #e9ecef; padding: 10px; border-radius: 4px; overflow-x: auto; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🔍 Database Connection Test</h1>";

// Test 1: Front-end Connection
echo "<div class='test-section'>
        <h2>Test 1: Front-end Connection (Model/connect.php)</h2>";

try {
    require_once 'Model/connect.php';
    
    if ($conn->ping()) {
        echo "<p class='success'>✅ Front-end connection successful!</p>";
        echo "<p class='info'>Server Info: " . $conn->server_info . "</p>";
        echo "<p class='info'>Host: " . $conn->host_info . "</p>";
        echo "<p class='info'>Character Set: " . $conn->character_set_name() . "</p>";
        
        // Test database exists
        $result = $conn->query("SELECT DATABASE()");
        if ($result) {
            $row = $result->fetch_row();
            echo "<p class='info'>Database: " . $row[0] . "</p>";
        }
        
        // Check if bookstore database has tables
        $result = $conn->query("SHOW TABLES");
        if ($result) {
            $table_count = $result->num_rows;
            echo "<p class='info'>Tables found: " . $table_count . "</p>";
            
            if ($table_count > 0) {
                echo "<p class='success'>✅ Database structure exists</p>";
                echo "<details><summary>View Tables</summary><pre>";
                while ($row = $result->fetch_row()) {
                    echo "- " . $row[0] . "\n";
                }
                echo "</pre></details>";
            } else {
                echo "<p class='error'>⚠️ No tables found. Run Phase 1.3 to create database schema.</p>";
            }
        }
    } else {
        echo "<p class='error'>❌ Connection failed</p>";
    }
    
    $conn->close();
    
} catch (Exception $e) {
    echo "<p class='error'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "</div>";

// Test 2: Admin Connection
echo "<div class='test-section'>
        <h2>Test 2: Admin Connection (Admin/Model/connect.php)</h2>";

try {
    require_once 'Admin/Model/connect.php';
    
    if ($conn->ping()) {
        echo "<p class='success'>✅ Admin connection successful!</p>";
        echo "<p class='info'>Server Info: " . $conn->server_info . "</p>";
        echo "<p class='info'>Host: " . $conn->host_info . "</p>";
        echo "<p class='info'>Character Set: " . $conn->character_set_name() . "</p>";
        
        // Test database exists
        $result = $conn->query("SELECT DATABASE()");
        if ($result) {
            $row = $result->fetch_row();
            echo "<p class='info'>Database: " . $row[0] . "</p>";
        }
    } else {
        echo "<p class='error'>❌ Connection failed</p>";
    }
    
    $conn->close();
    
} catch (Exception $e) {
    echo "<p class='error'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "</div>";

// Test 3: MySQL/Apache Status
echo "<div class='test-section'>
        <h2>Test 3: Server Status</h2>";

$apache_running = function_exists('apache_get_version');
$mysql_extension = extension_loaded('mysqli');

echo "<p class='info'>Apache: " . ($apache_running ? "✅ Running (" . apache_get_version() . ")" : "⚠️ Status unknown") . "</p>";
echo "<p class='info'>MySQLi Extension: " . ($mysql_extension ? "✅ Loaded" : "❌ Not loaded") . "</p>";
echo "<p class='info'>PHP Version: " . phpversion() . "</p>";

echo "</div>";

// Instructions
echo "<div class='test-section'>
        <h2>📋 Next Steps</h2>
        <ol>
            <li>If connections are successful: ✅ <strong>Phase 1.4 Complete!</strong></li>
            <li>If 'No tables found': Run Phase 1.3 to create the database schema</li>
            <li>If connection fails: 
                <ul>
                    <li>Check MySQL is running in XAMPP Control Panel</li>
                    <li>Verify database 'bookstore' exists in phpMyAdmin</li>
                    <li>Check connection parameters in connect.php files</li>
                </ul>
            </li>
            <li>Delete this test file after verification: <code>test_connection.php</code></li>
        </ol>
    </div>";

echo "    </div>
</body>
</html>";
?>
