<?php
/**
 * Customers Model Class
 * 
 * Handles all database operations related to customers/users
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
        
        $sql = "INSERT INTO customers (
                    full_name, email, password, phone, address, 
                    date_of_birth, gender, status
                ) VALUES (?, ?, ?, ?, ?, ?, ?, 1)";
        
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
        $sql = "SELECT id_customer, full_name, email, phone, address, 
                       date_of_birth, gender, status, created_at 
                FROM customers 
                WHERE id_customer = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }
    
    /**
     * Get customer by email
     * 
     * @param string $email Customer email
     * @return array|null Customer data or null if not found
     */
    public function getCustomerByEmail($email) {
        $sql = "SELECT * FROM customers WHERE email = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }
    
    /**
     * Check if email already exists
     * 
     * @param string $email Email to check
     * @return bool True if email exists
     */
    public function emailExists($email) {
        $sql = "SELECT COUNT(*) as count FROM customers WHERE email = ?";
        
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
        $sql = "UPDATE customers SET 
                    full_name = ?, 
                    phone = ?, 
                    address = ?, 
                    date_of_birth = ?, 
                    gender = ?,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id_customer = ?";
        
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
            if ($customer['status'] == 1) {
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
        
        $sql = "UPDATE customers SET 
                    password = ?,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id_customer = ?";
        
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
        
        $sql = "SELECT id_customer, full_name, email, phone, 
                       status, created_at 
                FROM customers 
                ORDER BY created_at DESC
                LIMIT ? OFFSET ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $customers = [];
        while ($row = $result->fetch_assoc()) {
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
        $sql = "SELECT COUNT(*) as total FROM customers";
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
        // Soft delete - set status to 0
        $sql = "UPDATE customers SET status = 0 WHERE id_customer = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
    
    /**
     * Activate customer account (Admin function)
     * 
     * @param int $id Customer ID
     * @return bool Success status
     */
    public function activateCustomer($id) {
        $sql = "UPDATE customers SET status = 1 WHERE id_customer = ?";
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
        
        $sql = "SELECT id_customer, full_name, email, phone, 
                       status, created_at 
                FROM customers 
                WHERE full_name LIKE ? OR email LIKE ?
                ORDER BY full_name ASC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $searchTerm, $searchTerm);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $customers = [];
        while ($row = $result->fetch_assoc()) {
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
                    SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) as active,
                    SUM(CASE WHEN DATE(created_at) = CURDATE() THEN 1 ELSE 0 END) as today,
                    SUM(CASE WHEN YEARWEEK(created_at) = YEARWEEK(NOW()) THEN 1 ELSE 0 END) as this_week
                FROM customers";
        
        $result = $this->conn->query($sql);
        return $result->fetch_assoc();
    }
}
?>
