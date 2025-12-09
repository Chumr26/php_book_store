<?php
/**
 * Books Model Class
 * 
 * Handles all database operations related to books
 * Used by both customer-facing pages and admin panel
 */

class Books {
    private $conn;
    
    /**
     * Constructor - Initialize database connection
     */
    public function __construct($connection) {
        $this->conn = $connection;
    }
    
    /**
     * Get all books with pagination
     * 
     * @param int $page Current page number
     * @param int $limit Items per page
     * @return array Books data
     */
    public function getAllBooks($page = 1, $limit = 12) {
        $offset = ($page - 1) * $limit;
        
        $sql = "SELECT b.*, 
                       a.author_name, 
                       p.publisher_name, 
                       c.category_name
                FROM books b
                LEFT JOIN authors a ON b.id_author = a.id_author
                LEFT JOIN publishers p ON b.id_publisher = p.id_publisher
                LEFT JOIN categories c ON b.id_category = c.id_category
                WHERE b.status = 1
                ORDER BY b.created_at DESC
                LIMIT ? OFFSET ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $books = [];
        while ($row = $result->fetch_assoc()) {
            $books[] = $row;
        }
        
        return $books;
    }
    
    /**
     * Get total count of active books
     * 
     * @return int Total count
     */
    public function getTotalBooks() {
        $sql = "SELECT COUNT(*) as total FROM books WHERE status = 1";
        $result = $this->conn->query($sql);
        $row = $result->fetch_assoc();
        return $row['total'];
    }
    
    /**
     * Get single book by ID
     * 
     * @param int $id Book ID
     * @return array|null Book data or null if not found
     */
    public function getBookById($id) {
        $sql = "SELECT b.*, 
                       a.author_name, 
                       a.pen_name,
                       p.publisher_name, 
                       c.category_name
                FROM books b
                LEFT JOIN authors a ON b.id_author = a.id_author
                LEFT JOIN publishers p ON b.id_publisher = p.id_publisher
                LEFT JOIN categories c ON b.id_category = c.id_category
                WHERE b.id_book = ? AND b.status = 1";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }
    
    /**
     * Search books by keyword (title, author, ISBN)
     * 
     * @param string $keyword Search keyword
     * @param int $page Current page
     * @param int $limit Items per page
     * @return array Books matching search
     */
    public function searchBooks($keyword, $page = 1, $limit = 12) {
        $offset = ($page - 1) * $limit;
        $searchTerm = "%{$keyword}%";
        
        $sql = "SELECT b.*, 
                       a.author_name, 
                       p.publisher_name, 
                       c.category_name
                FROM books b
                LEFT JOIN authors a ON b.id_author = a.id_author
                LEFT JOIN publishers p ON b.id_publisher = p.id_publisher
                LEFT JOIN categories c ON b.id_category = c.id_category
                WHERE b.status = 1 
                AND (b.title LIKE ? OR a.author_name LIKE ? OR b.isbn LIKE ?)
                ORDER BY b.title ASC
                LIMIT ? OFFSET ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sssii", $searchTerm, $searchTerm, $searchTerm, $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $books = [];
        while ($row = $result->fetch_assoc()) {
            $books[] = $row;
        }
        
        return $books;
    }
    
    /**
     * Get books by category
     * 
     * @param int $categoryId Category ID
     * @param int $page Current page
     * @param int $limit Items per page
     * @return array Books in category
     */
    public function getBooksByCategory($categoryId, $page = 1, $limit = 12) {
        $offset = ($page - 1) * $limit;
        
        $sql = "SELECT b.*, 
                       a.author_name, 
                       p.publisher_name, 
                       c.category_name
                FROM books b
                LEFT JOIN authors a ON b.id_author = a.id_author
                LEFT JOIN publishers p ON b.id_publisher = p.id_publisher
                LEFT JOIN categories c ON b.id_category = c.id_category
                WHERE b.id_category = ? AND b.status = 1
                ORDER BY b.created_at DESC
                LIMIT ? OFFSET ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iii", $categoryId, $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $books = [];
        while ($row = $result->fetch_assoc()) {
            $books[] = $row;
        }
        
        return $books;
    }
    
    /**
     * Get featured books
     * 
     * @param int $limit Number of books to return
     * @return array Featured books
     */
    public function getFeaturedBooks($limit = 8) {
        $sql = "SELECT b.*, 
                       a.author_name, 
                       p.publisher_name, 
                       c.category_name
                FROM books b
                LEFT JOIN authors a ON b.id_author = a.id_author
                LEFT JOIN publishers p ON b.id_publisher = p.id_publisher
                LEFT JOIN categories c ON b.id_category = c.id_category
                WHERE b.is_featured = 1 AND b.status = 1
                ORDER BY b.created_at DESC
                LIMIT ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $books = [];
        while ($row = $result->fetch_assoc()) {
            $books[] = $row;
        }
        
        return $books;
    }
    
    /**
     * Get top selling books
     * 
     * @param int $limit Number of books to return
     * @return array Best-selling books
     */
    public function getTopSellingBooks($limit = 8) {
        $sql = "SELECT b.*, 
                       a.author_name, 
                       p.publisher_name, 
                       c.category_name
                FROM books b
                LEFT JOIN authors a ON b.id_author = a.id_author
                LEFT JOIN publishers p ON b.id_publisher = p.id_publisher
                LEFT JOIN categories c ON b.id_category = c.id_category
                WHERE b.status = 1
                ORDER BY b.sale_count DESC, b.view_count DESC
                LIMIT ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $books = [];
        while ($row = $result->fetch_assoc()) {
            $books[] = $row;
        }
        
        return $books;
    }
    
    /**
     * Get new arrival books
     * 
     * @param int $limit Number of books to return
     * @return array Newest books
     */
    public function getNewArrivals($limit = 8) {
        $sql = "SELECT b.*, 
                       a.author_name, 
                       p.publisher_name, 
                       c.category_name
                FROM books b
                LEFT JOIN authors a ON b.id_author = a.id_author
                LEFT JOIN publishers p ON b.id_publisher = p.id_publisher
                LEFT JOIN categories c ON b.id_category = c.id_category
                WHERE b.status = 1
                ORDER BY b.created_at DESC
                LIMIT ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $books = [];
        while ($row = $result->fetch_assoc()) {
            $books[] = $row;
        }
        
        return $books;
    }
    
    /**
     * Add new book (Admin function)
     * 
     * @param array $data Book data
     * @return int|false New book ID or false on failure
     */
    public function addBook($data) {
        $sql = "INSERT INTO books (
                    title, id_author, id_publisher, id_category, isbn, 
                    price, original_price, cover_image, description, pages, 
                    publication_year, language, stock_quantity, is_featured, status
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            "siiisddsssissii",
            $data['title'],
            $data['id_author'],
            $data['id_publisher'],
            $data['id_category'],
            $data['isbn'],
            $data['price'],
            $data['original_price'],
            $data['cover_image'],
            $data['description'],
            $data['pages'],
            $data['publication_year'],
            $data['language'],
            $data['stock_quantity'],
            $data['is_featured'],
            $data['status']
        );
        
        if ($stmt->execute()) {
            return $this->conn->insert_id;
        }
        
        return false;
    }
    
    /**
     * Update book (Admin function)
     * 
     * @param int $id Book ID
     * @param array $data Updated book data
     * @return bool Success status
     */
    public function updateBook($id, $data) {
        $sql = "UPDATE books SET 
                    title = ?, 
                    id_author = ?, 
                    id_publisher = ?, 
                    id_category = ?, 
                    isbn = ?, 
                    price = ?, 
                    original_price = ?, 
                    cover_image = ?, 
                    description = ?, 
                    pages = ?, 
                    publication_year = ?, 
                    language = ?, 
                    stock_quantity = ?, 
                    is_featured = ?, 
                    status = ?,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id_book = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            "siiisddsssissiii",
            $data['title'],
            $data['id_author'],
            $data['id_publisher'],
            $data['id_category'],
            $data['isbn'],
            $data['price'],
            $data['original_price'],
            $data['cover_image'],
            $data['description'],
            $data['pages'],
            $data['publication_year'],
            $data['language'],
            $data['stock_quantity'],
            $data['is_featured'],
            $data['status'],
            $id
        );
        
        return $stmt->execute();
    }
    
    /**
     * Delete book (Admin function)
     * 
     * @param int $id Book ID
     * @return bool Success status
     */
    public function deleteBook($id) {
        // Soft delete - just set status to 0
        $sql = "UPDATE books SET status = 0 WHERE id_book = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
    
    /**
     * Update stock quantity after order
     * 
     * @param int $id Book ID
     * @param int $quantity Quantity to reduce
     * @return bool Success status
     */
    public function updateStock($id, $quantity) {
        $sql = "UPDATE books SET 
                    stock_quantity = stock_quantity - ?,
                    sale_count = sale_count + ?
                WHERE id_book = ? AND stock_quantity >= ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iiii", $quantity, $quantity, $id, $quantity);
        return $stmt->execute();
    }
    
    /**
     * Increment view count
     * 
     * @param int $id Book ID
     * @return bool Success status
     */
    public function incrementViewCount($id) {
        $sql = "UPDATE books SET view_count = view_count + 1 WHERE id_book = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
    
    /**
     * Check if book has sufficient stock
     * 
     * @param int $id Book ID
     * @param int $quantity Quantity needed
     * @return bool True if stock available
     */
    public function checkStock($id, $quantity) {
        $sql = "SELECT stock_quantity FROM books WHERE id_book = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $book = $result->fetch_assoc();
        
        return ($book && $book['stock_quantity'] >= $quantity);
    }
}
?>
