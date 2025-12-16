<?php
/**
 * Validator Class
 * Provides validation methods for user input
 * Handles data sanitization and format validation
 */

class Validator {
    private $errors = [];
    
    /**
     * Check if validation has errors
     * @return bool True if there are errors
     */
    public function hasErrors() {
        return !empty($this->errors);
    }
    
    /**
     * Get all validation errors
     * @return array Array of errors
     */
    public function getErrors() {
        return $this->errors;
    }
    
    /**
     * Get first error message
     * @return string|null First error or null
     */
    public function getFirstError() {
        return !empty($this->errors) ? reset($this->errors) : null;
    }
    
    /**
     * Add an error message
     * @param string $field Field name
     * @param string $message Error message
     */
    public function addError($field, $message) {
        $this->errors[$field] = $message;
    }
    
    /**
     * Clear all errors
     */
    public function clearErrors() {
        $this->errors = [];
    }
    
    /**
     * Validate required field
     * @param string $field Field name
     * @param mixed $value Field value
     * @param string $message Custom error message
     * @return bool True if valid
     */
    public function required($field, $value, $message = null) {
        if (empty($value) && $value !== '0') {
            $this->errors[$field] = $message ?? "Trường {$field} là bắt buộc.";
            return false;
        }
        return true;
    }
    
    /**
     * Validate email format
     * @param string $field Field name
     * @param string $email Email address
     * @param string $message Custom error message
     * @return bool True if valid
     */
    public function email($field, $email, $message = null) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = $message ?? "Email không hợp lệ.";
            return false;
        }
        return true;
    }
    
    /**
     * Validate password strength
     * @param string $field Field name
     * @param string $password Password
     * @param int $minLength Minimum length (default: 8)
     * @param string $message Custom error message
     * @return bool True if valid
     */
    public function password($field, $password, $minLength = 8, $message = null) {
        if (strlen($password) < $minLength) {
            $this->errors[$field] = $message ?? "Mật khẩu phải có ít nhất {$minLength} ký tự.";
            return false;
        }
        
        // Check for at least one letter and one number
        if (!preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password)) {
            $this->errors[$field] = "Mật khẩu phải chứa cả chữ và số.";
            return false;
        }
        
        return true;
    }
    
    /**
     * Validate password confirmation match
     * @param string $field Field name
     * @param string $password Password
     * @param string $confirmation Password confirmation
     * @param string $message Custom error message
     * @return bool True if valid
     */
    public function passwordMatch($field, $password, $confirmation, $message = null) {
        if ($password !== $confirmation) {
            $this->errors[$field] = $message ?? "Mật khẩu xác nhận không khớp.";
            return false;
        }
        return true;
    }
    
    /**
     * Validate phone number (Vietnamese format)
     * @param string $field Field name
     * @param string $phone Phone number
     * @param string $message Custom error message
     * @return bool True if valid
     */
    public function phone($field, $phone, $message = null) {
        // Remove spaces and dashes
        $phone = preg_replace('/[\s\-]/', '', $phone);
        
        // Vietnamese phone: 10 digits starting with 0
        if (!preg_match('/^0[0-9]{9}$/', $phone)) {
            $this->errors[$field] = $message ?? "Số điện thoại không hợp lệ (phải có 10 chữ số, bắt đầu bằng 0).";
            return false;
        }
        return true;
    }
    
    /**
     * Validate minimum length
     * @param string $field Field name
     * @param string $value Value to check
     * @param int $minLength Minimum length
     * @param string $message Custom error message
     * @return bool True if valid
     */
    public function minLength($field, $value, $minLength, $message = null) {
        if (strlen($value) < $minLength) {
            $this->errors[$field] = $message ?? "Trường {$field} phải có ít nhất {$minLength} ký tự.";
            return false;
        }
        return true;
    }
    
    /**
     * Validate maximum length
     * @param string $field Field name
     * @param string $value Value to check
     * @param int $maxLength Maximum length
     * @param string $message Custom error message
     * @return bool True if valid
     */
    public function maxLength($field, $value, $maxLength, $message = null) {
        if (strlen($value) > $maxLength) {
            $this->errors[$field] = $message ?? "Trường {$field} không được vượt quá {$maxLength} ký tự.";
            return false;
        }
        return true;
    }
    
    /**
     * Validate numeric value
     * @param string $field Field name
     * @param mixed $value Value to check
     * @param string $message Custom error message
     * @return bool True if valid
     */
    public function numeric($field, $value, $message = null) {
        if (!is_numeric($value)) {
            $this->errors[$field] = $message ?? "Trường {$field} phải là số.";
            return false;
        }
        return true;
    }
    
    /**
     * Validate integer value
     * @param string $field Field name
     * @param mixed $value Value to check
     * @param string $message Custom error message
     * @return bool True if valid
     */
    public function integer($field, $value, $message = null) {
        if (!filter_var($value, FILTER_VALIDATE_INT)) {
            $this->errors[$field] = $message ?? "Trường {$field} phải là số nguyên.";
            return false;
        }
        return true;
    }
    
    /**
     * Validate minimum value
     * @param string $field Field name
     * @param numeric $value Value to check
     * @param numeric $min Minimum value
     * @param string $message Custom error message
     * @return bool True if valid
     */
    public function min($field, $value, $min, $message = null) {
        if ($value < $min) {
            $this->errors[$field] = $message ?? "Trường {$field} phải lớn hơn hoặc bằng {$min}.";
            return false;
        }
        return true;
    }
    
    /**
     * Validate maximum value
     * @param string $field Field name
     * @param numeric $value Value to check
     * @param numeric $max Maximum value
     * @param string $message Custom error message
     * @return bool True if valid
     */
    public function max($field, $value, $max, $message = null) {
        if ($value > $max) {
            $this->errors[$field] = $message ?? "Trường {$field} không được vượt quá {$max}.";
            return false;
        }
        return true;
    }
    
    /**
     * Validate file upload
     * @param string $field Field name
     * @param array $file $_FILES array element
     * @param array $allowedTypes Allowed MIME types
     * @param int $maxSize Maximum file size in bytes
     * @param string $message Custom error message
     * @return bool True if valid
     */
    public function file($field, $file, $allowedTypes = [], $maxSize = 2097152, $message = null) {
        // Check if file was uploaded
        if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
            $this->errors[$field] = $message ?? "Vui lòng chọn tệp tin.";
            return false;
        }
        
        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $this->errors[$field] = "Lỗi khi tải lên tệp tin.";
            return false;
        }
        
        // Check file size
        if ($file['size'] > $maxSize) {
            $maxSizeMB = round($maxSize / 1048576, 2);
            $this->errors[$field] = "Kích thước tệp tin không được vượt quá {$maxSizeMB} MB.";
            return false;
        }
        
        // Check file type
        if (!empty($allowedTypes)) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);
            
            if (!in_array($mimeType, $allowedTypes)) {
                $this->errors[$field] = "Định dạng tệp tin không được hỗ trợ.";
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Validate image file
     * @param string $field Field name
     * @param array $file $_FILES array element
     * @param int $maxSize Maximum file size in bytes (default: 2MB)
     * @param string $message Custom error message
     * @return bool True if valid
     */
    public function image($field, $file, $maxSize = 2097152, $message = null) {
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        return $this->file($field, $file, $allowedTypes, $maxSize, $message);
    }
    
    /**
     * Sanitize string input
     * @param string $input Input string
     * @return string Sanitized string
     */
    public static function sanitizeString($input) {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Sanitize email
     * @param string $email Email address
     * @return string Sanitized email
     */
    public static function sanitizeEmail($email) {
        return filter_var(trim($email), FILTER_SANITIZE_EMAIL);
    }
    
    /**
     * Sanitize integer
     * @param mixed $input Input value
     * @return int Sanitized integer
     */
    public static function sanitizeInt($input) {
        return filter_var($input, FILTER_SANITIZE_NUMBER_INT);
    }
    
    /**
     * Sanitize URL
     * @param string $url URL string
     * @return string Sanitized URL
     */
    public static function sanitizeUrl($url) {
        return filter_var(trim($url), FILTER_SANITIZE_URL);
    }
    
    /**
     * Validate date format
     * @param string $field Field name
     * @param string $date Date string
     * @param string $format Date format (default: Y-m-d)
     * @param string $message Custom error message
     * @return bool True if valid
     */
    public function date($field, $date, $format = 'Y-m-d', $message = null) {
        $d = DateTime::createFromFormat($format, $date);
        if (!$d || $d->format($format) !== $date) {
            $this->errors[$field] = $message ?? "Định dạng ngày không hợp lệ.";
            return false;
        }
        return true;
    }
    
    /**
     * Validate value is in array
     * @param string $field Field name
     * @param mixed $value Value to check
     * @param array $allowedValues Allowed values
     * @param string $message Custom error message
     * @return bool True if valid
     */
    public function inArray($field, $value, $allowedValues, $message = null) {
        if (!in_array($value, $allowedValues)) {
            $this->errors[$field] = $message ?? "Giá trị không hợp lệ.";
            return false;
        }
        return true;
    }
    
    /**
     * Validate URL format
     * @param string $field Field name
     * @param string $url URL string
     * @param string $message Custom error message
     * @return bool True if valid
     */
    public function url($field, $url, $message = null) {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            $this->errors[$field] = $message ?? "URL không hợp lệ.";
            return false;
        }
        return true;
    }
}
