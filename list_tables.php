<?php
require_once 'Model/connect.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "Connected successfully\n";

$result = $conn->query("SHOW TABLES");

if ($result->num_rows > 0) {
    echo "Tables in database:\n";
    while($row = $result->fetch_array()) {
        echo "- " . $row[0] . "\n";
        
        // Show columns for each table
        $tableName = $row[0];
        $cols = $conn->query("SHOW COLUMNS FROM $tableName");
        if ($cols) {
             while($col = $cols->fetch_assoc()) {
                 echo "  - " . $col['Field'] . " (" . $col['Type'] . ")\n";
             }
        }
    }
} else {
    echo "0 tables found\n";
}

$conn->close();
?>
