<?php
/**
 * SessionHelper Class
 * Handles all session management operations
 * Provides secure session handling with timeout and security features
 */

class SessionHelper {
    private static $sessionStarted = false;
    private static $sessionTimeout = 1800; // 30 minutes in seconds
    
    /**
     * Start session with security settings
     */
    public static function start() {
        if (self::$sessionStarted) {
            return true;
        }
        
        // Configure secure session settings
        if (session_status() === PHP_SESSION_NONE) {
            ini_set('session.cookie_httponly', 1);
            ini_set('session.use_only_cookies', 1);
            ini_set('session.cookie_samesite', 'Lax');
            
            // Start session
            session_start();
            self::$sessionStarted = true;
            
            // Initialize last activity time if not set
            if (!isset($_SESSION['LAST_ACTIVITY'])) {
                $_SESSION['LAST_ACTIVITY'] = time();
            }
            
            // Check for session timeout
            self::checkTimeout();
            
            // Regenerate session ID periodically for security
            if (!isset($_SESSION['CREATED'])) {
                $_SESSION['CREATED'] = time();
            } else if (time() - $_SESSION['CREATED'] > 3600) {
                // Regenerate session ID every hour
                self::regenerateId();
                $_SESSION['CREATED'] = time();
            }
        }
        
        return true;
    }
    
    /**
     * Set a session variable
     * @param string $key Session key
     * @param mixed $value Session value
     */
    public static function set($key, $value) {
        self::start();
        $_SESSION[$key] = $value;
    }
    
    /**
     * Get a session variable
     * @param string $key Session key
     * @param mixed $default Default value if key doesn't exist
     * @return mixed Session value or default
     */
    public static function get($key, $default = null) {
        self::start();
        return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
    }
    
    /**
     * Check if a session variable exists
     * @param string $key Session key
     * @return bool True if exists
     */
    public static function has($key) {
        self::start();
        return isset($_SESSION[$key]);
    }
    
    /**
     * Remove a session variable
     * @param string $key Session key
     */
    public static function remove($key) {
        self::start();
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }
    
    /**
     * Destroy entire session
     */
    public static function destroy() {
        self::start();
        
        // Unset all session variables
        $_SESSION = array();
        
        // Delete session cookie
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
        
        // Destroy session
        session_destroy();
        self::$sessionStarted = false;
    }
    
    /**
     * Regenerate session ID for security
     */
    public static function regenerateId() {
        self::start();
        session_regenerate_id(true);
    }
    
    /**
     * Set flash message (one-time message)
     * @param string $type Message type (success, error, warning, info)
     * @param string $message Message content
     */
    public static function setFlash($type, $message) {
        self::start();
        $_SESSION['flash'][$type] = $message;
    }
    
    /**
     * Get flash message and remove it
     * @param string $type Message type
     * @return string|null Flash message or null
     */
    public static function getFlash($type) {
        self::start();
        if (isset($_SESSION['flash'][$type])) {
            $message = $_SESSION['flash'][$type];
            unset($_SESSION['flash'][$type]);
            return $message;
        }
        return null;
    }
    
    /**
     * Check if there are any flash messages
     * @return bool True if flash messages exist
     */
    public static function hasFlash() {
        self::start();
        return isset($_SESSION['flash']) && !empty($_SESSION['flash']);
    }
    
    /**
     * Get all flash messages
     * @return array All flash messages
     */
    public static function getAllFlash() {
        self::start();
        $messages = [];
        if (isset($_SESSION['flash']) && is_array($_SESSION['flash'])) {
            $messages = $_SESSION['flash'];
            unset($_SESSION['flash']);
        }
        return $messages;
    }
    
    /**
     * Check session timeout
     * Destroys session if inactive for too long
     */
    private static function checkTimeout() {
        if (isset($_SESSION['LAST_ACTIVITY'])) {
            $elapsed = time() - $_SESSION['LAST_ACTIVITY'];
            
            if ($elapsed > self::$sessionTimeout) {
                // Session has expired
                self::destroy();
                return false;
            }
        }
        
        // Update last activity time
        $_SESSION['LAST_ACTIVITY'] = time();
        return true;
    }
    
    /**
     * Set session timeout duration
     * @param int $seconds Timeout in seconds
     */
    public static function setTimeout($seconds) {
        self::$sessionTimeout = $seconds;
    }
    
    /**
     * Check if user is logged in
     * @return bool True if logged in
     */
    public static function isLoggedIn() {
        self::start();
        return isset($_SESSION['customer_id']) && !empty($_SESSION['customer_id']);
    }
    
    /**
     * Check if admin is logged in
     * @return bool True if admin logged in
     */
    public static function isAdminLoggedIn() {
        self::start();
        return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
    }

    /**
     * Determine whether the current request is coming from the local machine.
     * Used to safely enable developer-only helpers (e.g., quick login).
     *
     * @return bool
     */
    public static function isLocalRequest() {
        $remoteAddr = $_SERVER['REMOTE_ADDR'] ?? '';
        $serverName = $_SERVER['SERVER_NAME'] ?? '';

        if ($remoteAddr === '127.0.0.1' || $remoteAddr === '::1') {
            return true;
        }

        // Common XAMPP setup
        if ($serverName === 'localhost') {
            return true;
        }

        return false;
    }
    
    /**
     * Set user login session
     * @param int $customerId Customer ID
     * @param string $email Customer email
     * @param string $name Customer name
     */
    public static function setCustomerLogin($customerId, $email, $name) {
        self::start();
        self::regenerateId(); // Regenerate session ID on login for security
        $_SESSION['customer_id'] = $customerId;
        $_SESSION['customer_email'] = $email;
        $_SESSION['customer_name'] = $name;
        $_SESSION['login_time'] = time();
    }
    
    /**
     * Set admin login session
     * @param int $adminId Admin ID
     * @param string $username Admin username
     * @param string $email Admin email
     */
    public static function setAdminLogin($adminId, $username, $email) {
        self::start();
        self::regenerateId(); // Regenerate session ID on login for security
        $_SESSION['admin_id'] = $adminId;
        $_SESSION['admin_username'] = $username;
        $_SESSION['admin_email'] = $email;
        $_SESSION['login_time'] = time();
    }
    
    /**
     * Get current customer ID
     * @return int|null Customer ID or null
     */
    public static function getCustomerId() {
        return self::get('customer_id');
    }
    
    /**
     * Get current admin ID
     * @return int|null Admin ID or null
     */
    public static function getAdminId() {
        return self::get('admin_id');
    }
    
    /**
     * Logout user
     */
    public static function logout() {
        self::start();
        self::remove('customer_id');
        self::remove('customer_email');
        self::remove('customer_name');
        self::remove('login_time');
    }
    
    /**
     * Logout admin
     */
    public static function logoutAdmin() {
        self::start();
        self::remove('admin_id');
        self::remove('admin_username');
        self::remove('admin_email');
        self::remove('login_time');
    }
    
    /**
     * Generate CSRF token
     * @return string CSRF token
     */
    public static function generateCSRFToken() {
        self::start();
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Verify CSRF token
     * @param string $token Token to verify
     * @return bool True if valid
     */
    public static function verifyCSRFToken($token) {
        self::start();
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
}
