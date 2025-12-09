<?php
/**
 * Reviews Model Class
 * 
 * Handles all book review operations
 */

class Reviews {
    private $conn;
    
    /**
     * Constructor - Initialize database connection
     */
    public function __construct($connection) {
        $this->conn = $connection;
    }
    
    /**
     * Add new review
     * 
     * @param array $data Review data
     * @return int|false New review ID or false
     */
    public function addReview($data) {
        $sql = "INSERT INTO reviews (id_book, id_customer, rating, title, content, status) 
                VALUES (?, ?, ?, ?, ?, 'pending')";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            "iiiss",
            $data['id_book'],
            $data['id_customer'],
            $data['rating'],
            $data['title'],
            $data['content']
        );
        
        if ($stmt->execute()) {
            return $this->conn->insert_id;
        }
        
        return false;
    }
    
    /**
     * Get reviews for a book
     * 
     * @param int $bookId Book ID
     * @param string $status Filter by status (approved/pending/all)
     * @return array Reviews
     */
    public function getBookReviews($bookId, $status = 'approved') {
        if ($status === 'all') {
            $sql = "SELECT r.*, c.full_name 
                    FROM reviews r
                    INNER JOIN customers c ON r.id_customer = c.id_customer
                    WHERE r.id_book = ?
                    ORDER BY r.created_at DESC";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $bookId);
        } else {
            $sql = "SELECT r.*, c.full_name 
                    FROM reviews r
                    INNER JOIN customers c ON r.id_customer = c.id_customer
                    WHERE r.id_book = ? AND r.status = ?
                    ORDER BY r.created_at DESC";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("is", $bookId, $status);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        $reviews = [];
        while ($row = $result->fetch_assoc()) {
            $reviews[] = $row;
        }
        
        return $reviews;
    }
    
    /**
     * Get average rating for a book
     * 
     * @param int $bookId Book ID
     * @return float Average rating
     */
    public function getAverageRating($bookId) {
        $sql = "SELECT AVG(rating) as avg_rating, COUNT(*) as review_count 
                FROM reviews 
                WHERE id_book = ? AND status = 'approved'";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $bookId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        return [
            'average' => round($row['avg_rating'], 1),
            'count' => $row['review_count']
        ];
    }
    
    /**
     * Get all pending reviews (Admin function)
     * 
     * @return array Pending reviews
     */
    public function getPendingReviews() {
        $sql = "SELECT r.*, c.full_name, b.title 
                FROM reviews r
                INNER JOIN customers c ON r.id_customer = c.id_customer
                INNER JOIN books b ON r.id_book = b.id_book
                WHERE r.status = 'pending'
                ORDER BY r.created_at DESC";
        
        $result = $this->conn->query($sql);
        
        $reviews = [];
        while ($row = $result->fetch_assoc()) {
            $reviews[] = $row;
        }
        
        return $reviews;
    }
    
    /**
     * Approve review (Admin function)
     * 
     * @param int $reviewId Review ID
     * @return bool Success status
     */
    public function approveReview($reviewId) {
        $sql = "UPDATE reviews SET status = 'approved' WHERE id_review = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $reviewId);
        return $stmt->execute();
    }
    
    /**
     * Delete review (Admin function)
     * 
     * @param int $reviewId Review ID
     * @return bool Success status
     */
    public function deleteReview($reviewId) {
        $sql = "DELETE FROM reviews WHERE id_review = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $reviewId);
        return $stmt->execute();
    }
    
    /**
     * Check if customer has already reviewed a book
     * 
     * @param int $customerId Customer ID
     * @param int $bookId Book ID
     * @return bool True if review exists
     */
    public function hasReviewed($customerId, $bookId) {
        $sql = "SELECT COUNT(*) as count FROM reviews 
                WHERE id_customer = ? AND id_book = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $customerId, $bookId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        return ($row['count'] > 0);
    }
}
?>
