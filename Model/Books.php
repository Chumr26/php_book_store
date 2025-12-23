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
        $sql = "SELECT s.*, 
                       s.id_sach as ma_sach,
                       s.ten_sach,
                       s.gia,
                       s.gia_goc,
                       s.so_luong_ton,
                       s.mo_ta,
                       s.isbn,
                       s.so_trang,
                       s.nam_xuat_ban,
                       CASE 
                           WHEN s.so_luong_ton > 0 THEN 'Còn hàng'
                           ELSE 'Hết hàng'
                       END as tinh_trang,
                       tg.ten_tacgia as ten_tac_gia,
                       tg.but_danh,
                       nxb.ten_nxb,
                       tl.ten_theloai as ten_danh_muc,
                       tl.id_theloai as ma_danh_muc,
                       COALESCE(AVG(CASE WHEN dg.trang_thai = 'approved' THEN dg.so_sao END), 0) as diem_trung_binh,
                       COUNT(CASE WHEN dg.trang_thai = 'approved' THEN 1 END) as so_luong_danh_gia
                FROM sach s
                LEFT JOIN tacgia tg ON s.id_tacgia = tg.id_tacgia
                LEFT JOIN nhaxuatban nxb ON s.id_nxb = nxb.id_nxb
                LEFT JOIN theloai tl ON s.id_theloai = tl.id_theloai
                LEFT JOIN danhgia dg ON s.id_sach = dg.id_sach
                WHERE s.id_sach = ?
                GROUP BY s.id_sach, s.ten_sach, s.gia, s.gia_goc, s.so_luong_ton, s.mo_ta, 
                         s.isbn, s.so_trang, s.nam_xuat_ban, s.id_tacgia, s.id_nxb, s.id_theloai,
                         tg.ten_tacgia, tg.but_danh, nxb.ten_nxb, tl.ten_theloai, tl.id_theloai";
        
        $stmt = $this->conn->prepare($sql);
        
        if (!$stmt) {
            error_log("SQL Prepare Error in getBookById: " . $this->conn->error);
            return null;
        }
        
        $stmt->bind_param("i", $id);
        
        if (!$stmt->execute()) {
            error_log("SQL Execute Error in getBookById: " . $stmt->error);
            return null;
        }
        
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
    
    /**
     * Count total books matching filters (for pagination)
     * 
     * @param int $categoryId Category filter (0 = all)
     * @param string $priceRange Price range filter (e.g., "0-100000")
     * @param int $rating Minimum rating filter
     * @param string $search Search keyword
     * @return int Total count
     */
    public function countBooks($categoryId = 0, $priceRange = '', $rating = 0, $search = '') {
        $sql = "SELECT COUNT(DISTINCT s.id_sach) as total 
                FROM sach s
                LEFT JOIN theloai tl ON s.id_theloai = tl.id_theloai
                LEFT JOIN tacgia tg ON s.id_tacgia = tg.id_tacgia
                LEFT JOIN nhaxuatban nxb ON s.id_nxb = nxb.id_nxb
                WHERE s.so_luong_ton > 0 AND s.trang_thai = 'available'";
        
        $params = [];
        $types = '';
        
        // Category filter
        if ($categoryId > 0) {
            $sql .= " AND s.id_theloai = ?";
            $params[] = $categoryId;
            $types .= 'i';
        }
        
        // Price range filter
        if ($priceRange && strpos($priceRange, '-') !== false) {
            list($min, $max) = explode('-', $priceRange);
            $sql .= " AND s.gia BETWEEN ? AND ?";
            $params[] = (int)$min;
            $params[] = (int)$max;
            $types .= 'dd';
        }
        
        // Rating filter (assuming there's a rating column or we skip this)
        // if ($rating > 0) {
        //     $sql .= " AND s.danh_gia >= ?";
        //     $params[] = $rating;
        //     $types .= 'i';
        // }
        
        // Search filter
        if ($search) {
            $sql .= " AND (s.ten_sach LIKE ? OR tg.ten_tacgia LIKE ? OR nxb.ten_nxb LIKE ? OR s.isbn LIKE ?)";
            $searchTerm = "%{$search}%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $types .= 'ssss';
        }
        
        $stmt = $this->conn->prepare($sql);
        
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        return (int)($row['total'] ?? 0);
    }
    
    /**
     * Get books with filters and pagination
     * 
     * @param int $categoryId Category filter (0 = all)
     * @param string $priceRange Price range filter
     * @param int $rating Minimum rating filter
     * @param string $search Search keyword
     * @param string $sort Sort by (newest, price_asc, price_desc, bestseller, name)
     * @param int $limit Items per page
     * @param int $offset Offset for pagination
     * @return array Books array
     */
    public function getBooks($categoryId = 0, $priceRange = '', $rating = 0, $search = '', $sort = 'newest', $limit = 12, $offset = 0) {
        $sql = "SELECT s.id_sach as ma_sach,
                       s.ten_sach,
                       s.isbn,
                       s.gia,
                       s.gia_goc,
                       s.hinh_anh,
                       s.mo_ta,
                       s.so_trang,
                       s.nam_xuat_ban,
                       s.so_luong_ton,
                       s.luot_xem,
                       s.luot_ban,
                       s.trang_thai as tinh_trang,
                       s.noi_bat,
                       s.ngay_them as ngay_tao,
                       tg.ten_tacgia as ten_tac_gia,
                       tg.but_danh,
                       nxb.ten_nxb as ten_nha_xuat_ban,
                       tl.ten_theloai as ten_the_loai,
                       0 as giam_gia,
                       0 as diem_trung_binh,
                       0 as so_luong_danh_gia
                FROM sach s
                LEFT JOIN theloai tl ON s.id_theloai = tl.id_theloai
                LEFT JOIN tacgia tg ON s.id_tacgia = tg.id_tacgia
                LEFT JOIN nhaxuatban nxb ON s.id_nxb = nxb.id_nxb
                WHERE s.so_luong_ton > 0 AND s.trang_thai = 'available'";
        
        $params = [];
        $types = '';
        
        // Category filter
        if ($categoryId > 0) {
            $sql .= " AND s.id_theloai = ?";
            $params[] = $categoryId;
            $types .= 'i';
        }
        
        // Price range filter
        if ($priceRange && strpos($priceRange, '-') !== false) {
            list($min, $max) = explode('-', $priceRange);
            $sql .= " AND s.gia BETWEEN ? AND ?";
            $params[] = (int)$min;
            $params[] = (int)$max;
            $types .= 'dd';
        }
        
        // Search filter
        if ($search) {
            $sql .= " AND (s.ten_sach LIKE ? OR tg.ten_tacgia LIKE ? OR nxb.ten_nxb LIKE ? OR s.isbn LIKE ?)";
            $searchTerm = "%{$search}%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $types .= 'ssss';
        }
        
        // Sort
        switch ($sort) {
            case 'price_asc':
                $sql .= " ORDER BY s.gia ASC";
                break;
            case 'price_desc':
                $sql .= " ORDER BY s.gia DESC";
                break;
            case 'bestseller':
                $sql .= " ORDER BY s.luot_ban DESC";
                break;
            case 'name':
                $sql .= " ORDER BY s.ten_sach ASC";
                break;
            case 'newest':
            default:
                $sql .= " ORDER BY s.ngay_them DESC";
                break;
        }
        
        $sql .= " LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        $types .= 'ii';
        
        $stmt = $this->conn->prepare($sql);
        
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $this->conn->error);
        }
        
        if (!empty($params)) {
            if (!$stmt->bind_param($types, ...$params)) {
                throw new Exception("Bind param failed: " . $stmt->error);
            }
        }
        
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }
        
        $result = $stmt->get_result();
        
        $books = [];
        while ($row = $result->fetch_assoc()) {
            $books[] = $row;
        }
        
        return $books;
    }
}
?>
