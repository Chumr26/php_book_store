<?php
/**
 * BaseController
 * Base class for all controllers providing common functionality
 */

abstract class BaseController {
    /**
     * @var mysqli Database connection
     */
    protected $conn;
    
    /**
     * Constructor
     * @param mysqli $conn Database connection
     */
    public function __construct($conn) {
        if (!$conn instanceof mysqli) {
            throw new InvalidArgumentException('Database connection must be a mysqli instance');
        }
        
        if ($conn->connect_error) {
            throw new Exception('Database connection failed: ' . $conn->connect_error);
        }
        
        $this->conn = $conn;
    }
    
    /**
     * Get database connection
     * @return mysqli
     */
    protected function getConnection() {
        return $this->conn;
    }
    
    /**
     * Load a view file
     * @param string $view View file name (without .php extension)
     * @param array $data Data to pass to the view
     */
    protected function loadView($view, $data = []) {
        // Extract data array to variables
        extract($data);
        
        // Build view path
        $viewPath = __DIR__ . '/../View/' . $view . '.php';
        
        if (!file_exists($viewPath)) {
            throw new Exception("View file not found: {$view}");
        }
        
        require $viewPath;
    }
    
    /**
     * Redirect to another page
     * @param string $url URL to redirect to
     * @param int $statusCode HTTP status code (default: 302)
     */
    protected function redirect($url, $statusCode = 302) {
        header('Location: ' . $url, true, $statusCode);
        exit;
    }
    
    /**
     * Return JSON response
     * @param array $data Data to encode as JSON
     * @param int $statusCode HTTP status code (default: 200)
     */
    protected function json($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    /**
     * Check if request is AJAX
     * @return bool
     */
    protected function isAjax() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
    
    /**
     * Get request method
     * @return string (GET, POST, PUT, DELETE, etc.)
     */
    protected function getRequestMethod() {
        return $_SERVER['REQUEST_METHOD'] ?? 'GET';
    }
    
    /**
     * Check if request method is POST
     * @return bool
     */
    protected function isPost() {
        return $this->getRequestMethod() === 'POST';
    }
    
    /**
     * Check if request method is GET
     * @return bool
     */
    protected function isGet() {
        return $this->getRequestMethod() === 'GET';
    }
}
