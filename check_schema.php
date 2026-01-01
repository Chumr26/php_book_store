<?php
require_once 'Model/connect.php';

$tables_to_check = [
    'sach', 'books', 
    'donhang', 'don_hang', 'orders', 
    'khachhang', 'khach_hang', 'customers', 
    'theloai', 'danh_muc', 'categories',
    'tacgia', 'tac_gia', 'authors',
    'nxb', 'nha_xuat_ban', 'publishers',
    'admin', 'admins', 'quantri'
];

foreach ($tables_to_check as $table) {
    $result = $conn->query("SHOW TABLES LIKE '$table'");
    if ($result && $result->num_rows > 0) {
        echo "FOUND TABLE: $table\n";
        $cols = $conn->query("SHOW COLUMNS FROM $table");
        while($col = $cols->fetch_assoc()) {
            echo "  - " . $col['Field'] . "\n";
        }
        echo "\n";
    }
}
$conn->close();
?>
