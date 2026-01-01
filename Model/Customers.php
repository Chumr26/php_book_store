<?php
/**
 * Customers Model Class
 * 
 * Handles all database operations related to customers/users using 'khachhang' table
 */

class Customers {
    private $conn;
    
    /**
     * Constructor - Initialize database connection
     */
    public function __construct($connection) {
        $this->conn = $connection;
    }
    
    /**
     * Register new customer
     * 
     * @param array $data Customer data
     * @return int|false New customer ID or false on failure
     */
    public function registerCustomer($data) {
        // Hash password before storing
        $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);
        
        $sql = "INSERT INTO khachhang (
                    ten_khachhang, email, password, dien_thoai, dia_chi, 
                    ngay_sinh, gioi_tinh, trang_thai
                ) VALUES (?, ?, ?, ?, ?, ?, ?, 'active')";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            "sssssss",
            $data['full_name'],
            $data['email'],
            $hashedPassword,
            $data['phone'],
            $data['address'],
            $data['date_of_birth'],
            $data['gender']
        );
        
        if ($stmt->execute()) {
            return $this->conn->insert_id;
        }
        
        return false;
    }
    
    /**
     * Get customer by ID
     * 
     * @param int $id Customer ID
     * @return array|null Customer data or null if not found
     */
    public function getCustomerById($id) {
        $sql = "SELECT id_khachhang, ten_khachhang, email, dien_thoai, dia_chi, 
                       ngay_sinh, gioi_tinh, trang_thai, ngay_dang_ky 
                FROM khachhang 
                WHERE id_khachhang = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $row = $result->fetch_assoc();
        
        // Map to English keys for compatibility with Controllers if needed
        if ($row) {
            $row['id_customer'] = $row['id_khachhang'];
            $row['full_name'] = $row['ten_khachhang'];
            $row['phone'] = $row['dien_thoai'];
            $row['address'] = $row['dia_chi'];
            $row['date_of_birth'] = $row['ngay_sinh'];
            $row['gender'] = $row['gioi_tinh'];
            $row['status'] = $row['trang_thai'];
            $row['created_at'] = $row['ngay_dang_ky'];
            
            // Aliases expected by some views
            $row['ho_ten'] = $row['ten_khachhang'];
            $row['so_dien_thoai'] = $row['dien_thoai'];
            $row['ngay_tao'] = $row['ngay_dang_ky'];
        }
        
        return $row;
    }
    
    /**
     * Get customer by email
     * 
     * @param string $email Customer email
     * @return array|null Customer data or null if not found
     */
    public function getCustomerByEmail($email) {
        $sql = "SELECT * FROM khachhang WHERE email = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $row = $result->fetch_assoc();
        
        // Map to English keys
        if ($row) {
            $row['password'] = $row['password']; // Needed for verification
            $row['status'] = ($row['trang_thai'] == 'active') ? 1 : 0; // rough mapping
            $row['trang_thai_code'] = $row['trang_thai'];
            
            // Standard mapping
            $row['id_customer'] = $row['id_khachhang'];
            $row['full_name'] = $row['ten_khachhang'];
        }
        
        return $row;
    }
    
    /**
     * Check if email already exists
     * 
     * @param string $email Email to check
     * @return bool True if email exists
     */
    public function emailExists($email) {
        $sql = "SELECT COUNT(*) as count FROM khachhang WHERE email = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        return ($row['count'] > 0);
    }
    
    /**
     * Update customer profile
     * 
     * @param int $id Customer ID
     * @param array $data Updated customer data
     * @return bool Success status
     */
    public function updateCustomer($id, $data) {
        // Warning: schema has no 'updated_at' column in khachhang table based on sql file? 
        // SQL file timestamp default current_timestamp on create, update not mentioned.
        
        $sql = "UPDATE khachhang SET 
                    ten_khachhang = ?, 
                    dien_thoai = ?, 
                    dia_chi = ?, 
                    ngay_sinh = ?, 
                    gioi_tinh = ?
                WHERE id_khachhang = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            "sssssi",
            $data['full_name'],
            $data['phone'],
            $data['address'],
            $data['date_of_birth'],
            $data['gender'],
            $id
        );
        
        return $stmt->execute();
    }
    
    /**
     * Verify customer login credentials
     * 
     * @param string $email Customer email
     * @param string $password Plain text password
     * @return array|false Customer data or false if invalid
     */
    public function verifyPassword($email, $password) {
        $customer = $this->getCustomerByEmail($email);
        
        if ($customer && password_verify($password, $customer['password'])) {
            // Check if account is active
            if ($customer['trang_thai_code'] == 'active') {
                // Don't return password in the result
                unset($customer['password']);
                return $customer;
            }
        }
        
        return false;
    }
    
    /**
     * Update customer password
     * 
     * @param int $id Customer ID
     * @param string $newPassword New plain text password
     * @return bool Success status
     */
    public function updatePassword($id, $newPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
        
        $sql = "UPDATE khachhang SET 
                    password = ?
                WHERE id_khachhang = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $hashedPassword, $id);
        
        return $stmt->execute();
    }
    
    /**
     * Get all customers (Admin function)
     * 
     * @param int $page Current page
     * @param int $limit Items per page
     * @return array Customers data
     */
    public function getAllCustomers($page = 1, $limit = 20) {
        $offset = ($page - 1) * $limit;
        
        $sql = "SELECT id_khachhang, ten_khachhang, email, 
                       trang_thai, ngay_dang_ky 
                FROM khachhang 
                ORDER BY ngay_dang_ky DESC
                LIMIT ? OFFSET ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $customers = [];
        while ($row = $result->fetch_assoc()) {
            // Map keys
            $row['id_customer'] = $row['id_khachhang'];
            $row['full_name'] = $row['ten_khachhang'];
            $row['status'] = $row['trang_thai'];
            $row['created_at'] = $row['ngay_dang_ky'];
            
            $customers[] = $row;
        }
        
        return $customers;
    }
    
    /**
     * Get total customer count
     * 
     * @return int Total count
     */
    public function getTotalCustomers() {
        $sql = "SELECT COUNT(*) as total FROM khachhang";
        $result = $this->conn->query($sql);
        $row = $result->fetch_assoc();
        return $row['total'];
    }
    
    /**
     * Delete customer (Admin function)
     * 
     * @param int $id Customer ID
     * @return bool Success status
     */
    public function deleteCustomer($id) {
        // Soft delete - set status to inactive/blocked? Or 'inactive'
        $sql = "UPDATE khachhang SET trang_thai = 'inactive' WHERE id_khachhang = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
    
    /**
     * Update customer status (Admin function)
     */
    public function updateCustomerStatus($id, $status) {
         $sql = "UPDATE khachhang SET trang_thai = ? WHERE id_khachhang = ?";
         $stmt = $this->conn->prepare($sql);
         $stmt->bind_param("si", $status, $id);
         return $stmt->execute();
    }
    
    /**
     * Activate customer account (Admin function)
     * 
     * @param int $id Customer ID
     * @return bool Success status
     */
    public function activateCustomer($id) {
        $sql = "UPDATE khachhang SET trang_thai = 'active' WHERE id_khachhang = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
    
    /**
     * Search customers by name or email (Admin function)
     * 
     * @param string $keyword Search keyword
     * @return array Matching customers
     */
    public function searchCustomers($keyword) {
        $searchTerm = "%{$keyword}%";
        
        $sql = "SELECT id_khachhang, ten_khachhang, email, dien_thoai, 
                       trang_thai, ngay_dang_ky 
                FROM khachhang 
                WHERE ten_khachhang LIKE ? OR email LIKE ?
                ORDER BY ten_khachhang ASC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $searchTerm, $searchTerm);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $customers = [];
        while ($row = $result->fetch_assoc()) {
             // Map keys
            $row['id_customer'] = $row['id_khachhang'];
            $row['full_name'] = $row['ten_khachhang'];
            $row['phone'] = $row['dien_thoai'];
            $row['status'] = $row['trang_thai'];
            $row['created_at'] = $row['ngay_dang_ky'];
            $customers[] = $row;
        }
        
        return $customers;
    }
    
    /**
     * Get customer statistics (Admin function)
     * 
     * @return array Statistics data
     */
    public function getCustomerStats() {
        $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN trang_thai = 'active' THEN 1 ELSE 0 END) as active,
                    SUM(CASE WHEN DATE(ngay_dang_ky) = CURDATE() THEN 1 ELSE 0 END) as today,
                    SUM(CASE WHEN YEARWEEK(ngay_dang_ky) = YEARWEEK(NOW()) THEN 1 ELSE 0 END) as this_week
                FROM khachhang";
        
        $result = $this->conn->query($sql);
        return $result->fetch_assoc();
    }
}
?>
