<?php
/**
 * Authors Model Class
 * 
 * Handles all author-related operations
 */

class Authors {
    private $conn;
    
    /**
     * Constructor - Initialize database connection
     */
    public function __construct($connection) {
        $this->conn = $connection;
    }
    
    /**
     * Get all authors
     * 
     * @return array Authors
     */
    public function getAllAuthors() {
        $sql = "SELECT * FROM authors ORDER BY author_name ASC";
        $result = $this->conn->query($sql);
        
        $authors = [];
        while ($row = $result->fetch_assoc()) {
            $authors[] = $row;
        }
        
        return $authors;
    }
    
    /**
     * Get author by ID
     * 
     * @param int $id Author ID
     * @return array|null Author data or null
     */
    public function getAuthorById($id) {
        $sql = "SELECT * FROM authors WHERE id_author = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }
    
    /**
     * Add new author (Admin function)
     * 
     * @param array $data Author data
     * @return int|false New author ID or false
     */
    public function addAuthor($data) {
        $sql = "INSERT INTO authors (author_name, pen_name, biography, date_of_birth, nationality) 
                VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            "sssss",
            $data['author_name'],
            $data['pen_name'],
            $data['biography'],
            $data['date_of_birth'],
            $data['nationality']
        );
        
        if ($stmt->execute()) {
            return $this->conn->insert_id;
        }
        
        return false;
    }
    
    /**
     * Update author (Admin function)
     * 
     * @param int $id Author ID
     * @param array $data Updated data
     * @return bool Success status
     */
    public function updateAuthor($id, $data) {
        $sql = "UPDATE authors 
                SET author_name = ?, pen_name = ?, biography = ?, 
                    date_of_birth = ?, nationality = ? 
                WHERE id_author = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            "sssssi",
            $data['author_name'],
            $data['pen_name'],
            $data['biography'],
            $data['date_of_birth'],
            $data['nationality'],
            $id
        );
        
        return $stmt->execute();
    }
    
    /**
     * Delete author (Admin function)
     * 
     * @param int $id Author ID
     * @return bool Success status
     */
    public function deleteAuthor($id) {
        $sql = "DELETE FROM authors WHERE id_author = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
    
    /**
     * Search authors by name
     * 
     * @param string $keyword Search keyword
     * @return array Matching authors
     */
    public function searchAuthors($keyword) {
        $searchTerm = "%{$keyword}%";
        
        $sql = "SELECT * FROM authors 
                WHERE author_name LIKE ? OR pen_name LIKE ?
                ORDER BY author_name ASC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $searchTerm, $searchTerm);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $authors = [];
        while ($row = $result->fetch_assoc()) {
            $authors[] = $row;
        }
        
        return $authors;
    }
}
?>
