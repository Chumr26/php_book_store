<?php
/**
 * Email Sender Class
 * 
 * Wrapper for PHPMailer to send emails
 * Uses Composer's PHPMailer library
 */

// Include Composer autoloader
require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class EmailSender {
    private $mail;
    private $smtpHost;
    private $smtpPort;
    private $smtpUsername;
    private $smtpPassword;
    private $smtpSecure;
    private $fromEmail;
    private $fromName;
    private $smtpDebug;
    private $lastError;
    private $isConfigured;
    
    /**
     * Constructor - Initialize PHPMailer
     * 
     * @param array $config SMTP configuration (optional, uses defaults if not provided)
     */
    public function __construct($config = []) {
        $this->mail = new PHPMailer(true);

        $this->lastError = null;
        $this->isConfigured = false;

        // Load configuration from env and optional config file, then override with provided $config
        $baseConfig = $this->loadConfig();
        if (is_array($config) && !empty($config)) {
            $baseConfig = array_merge($baseConfig, $config);
        }
        
        // Default SMTP configuration (Resend-first; can be overridden)
        $this->smtpHost = $baseConfig['smtp_host'] ?? 'smtp.resend.com';
        $this->smtpPort = (int)($baseConfig['smtp_port'] ?? 587);
        $this->smtpUsername = $baseConfig['smtp_username'] ?? 'resend';
        $this->smtpPassword = $baseConfig['smtp_password'] ?? '';
        $this->smtpSecure = $baseConfig['smtp_secure'] ?? 'starttls';
        $this->fromEmail = $baseConfig['from_email'] ?? '';
        $this->fromName = $baseConfig['from_name'] ?? 'BookStore';
        $this->smtpDebug = (int)($baseConfig['smtp_debug'] ?? 0);
        
        // Configure SMTP
        $this->configureTransport();
    }

    /**
     * Load config from required local config file.
     *
     * This project keeps SMTP secrets out of git by using config/email.local.php.
     * Constructor parameter overrides the file.
     *
     * @return array
     */
    private function loadConfig() {
        $localConfigFile = __DIR__ . '/../config/email.local.php';

        if (!file_exists($localConfigFile)) {
            throw new RuntimeException(
                'Missing required SMTP config file: config/email.local.php. ' .
                'Copy config/email.local.php.example to config/email.local.php and set smtp_password.'
            );
        }

        $localFileConfig = include $localConfigFile;
        if (!is_array($localFileConfig)) {
            throw new RuntimeException('Invalid SMTP config in config/email.local.php (expected a PHP array).');
        }

        return $localFileConfig;
    }

    private function logMailError($message) {
        // Always send to PHP error log
        error_log($message);

        // Additionally write to tmp/logs/email.log (useful on XAMPP where error log location is unclear)
        $logDir = __DIR__ . '/../tmp/logs';
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0775, true);
        }

        $logFile = $logDir . '/email.log';
        $line = '[' . date('Y-m-d H:i:s') . '] ' . $message . PHP_EOL;
        @file_put_contents($logFile, $line, FILE_APPEND);
    }
    
    /**
     * Configure SMTP settings
     */
    private function configureTransport() {
        try {
            $this->isConfigured = false;

            // If SMTP credentials are missing, we cannot authenticate to most SMTP servers (e.g., Gmail).
            // Keep behavior explicit and log a clear message.
            if (empty($this->smtpUsername) || empty($this->smtpPassword)) {
                $this->lastError = 'Missing SMTP credentials (smtp_username / smtp_password) in config/email.local.php.';
                $this->logMailError('EmailSender not configured: ' . $this->lastError);
                return;
            }

            if (empty($this->fromEmail)) {
                $this->lastError = 'Missing sender address (from_email) in config/email.local.php.';
                $this->logMailError('EmailSender not configured: ' . $this->lastError);
                return;
            }

            // Server settings
            if ($this->smtpDebug > 0) {
                $this->mail->SMTPDebug = SMTP::DEBUG_SERVER;
            }
            $this->mail->isSMTP();
            $this->mail->Host = $this->smtpHost;
            $this->mail->SMTPAuth = true;
            $this->mail->Username = $this->smtpUsername;
            $this->mail->Password = $this->smtpPassword;
            $secure = strtolower(trim((string)$this->smtpSecure));
            if ($secure === 'smtps' || $secure === 'ssl' || $secure === 'implicit') {
                $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            } elseif ($secure === 'starttls' || $secure === 'tls' || $secure === 'explicit') {
                $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            } else {
                // No encryption (not recommended)
                $this->mail->SMTPSecure = '';
                $this->mail->SMTPAutoTLS = false;
            }
            $this->mail->Port = $this->smtpPort;
            $this->mail->CharSet = 'UTF-8';
            
            // Set default sender
            $this->mail->setFrom($this->fromEmail, $this->fromName);

            $this->isConfigured = true;
            
        } catch (Exception $e) {
            $this->lastError = $e->getMessage();
            $this->logMailError("PHPMailer configuration error: {$e->getMessage()}");
        }
    }
    
    /**
     * Send email
     * 
     * @param string $to Recipient email
     * @param string $toName Recipient name
     * @param string $subject Email subject
     * @param string $body Email body (HTML)
     * @param string $altBody Alternative plain text body (optional)
     * @return bool Success status
     */
    public function sendEmail($to, $toName, $subject, $body, $altBody = '') {
        try {
            $this->lastError = null;

            if (!$this->isConfigured) {
                $this->lastError = $this->lastError ?: 'EmailSender is not configured.';
                $this->logMailError('Email send skipped: ' . $this->lastError);
                return false;
            }

            if (empty($to) || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
                $this->lastError = 'Invalid recipient email.';
                $this->logMailError('Email send failed: ' . $this->lastError . ' to=' . (string)$to);
                return false;
            }

            // Clear any previous state (important if the same instance is reused)
            $this->mail->clearAllRecipients();
            $this->mail->clearAttachments();

            // Recipients
            $this->mail->addAddress($to, $toName);
            
            // Content
            $this->mail->isHTML(true);
            $this->mail->Subject = $subject;
            $this->mail->Body = $body;
            $this->mail->AltBody = $altBody ?: strip_tags($body);
            
            // Send
            $result = $this->mail->send();

            // Clear recipients for next email
            $this->mail->clearAllRecipients();
            
            return $result;
            
        } catch (Exception $e) {
            $info = $this->mail->ErrorInfo;
            $this->lastError = $info ?: $e->getMessage();
            $this->logMailError("Email sending failed: {$this->lastError}");
            return false;
        }
    }

    /**
     * Get last mail error (best-effort).
     *
     * @return string|null
     */
    public function getLastError() {
        return $this->lastError;
    }
    
    /**
     * Send registration confirmation email
     * 
     * @param string $email Customer email
     * @param string $name Customer name
     * @return bool Success status
     */
    public function sendRegistrationConfirmation($email, $name) {
        $subject = "Chào mừng đến với BookStore!";
        
        $body = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #007bff; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background-color: #f9f9f9; }
                .button { background-color: #28a745; color: white; padding: 10px 20px; text-decoration: none; display: inline-block; margin: 10px 0; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>BookStore</h1>
                </div>
                <div class='content'>
                    <h2>Xin chào {$name}!</h2>
                    <p>Cảm ơn bạn đã đăng ký tài khoản tại BookStore.</p>
                    <p>Tài khoản của bạn đã được tạo thành công. Bạn có thể đăng nhập và bắt đầu mua sắm ngay bây giờ!</p>
                    <a href='http://localhost/book_store/login.php' class='button'>Đăng nhập ngay</a>
                    <p>Nếu bạn có bất kỳ câu hỏi nào, vui lòng liên hệ với chúng tôi.</p>
                    <p>Trân trọng,<br>Đội ngũ BookStore</p>
                </div>
            </div>
        </body>
        </html>";
        
        return $this->sendEmail($email, $name, $subject, $body);
    }

    /**
     * Send email verification mail (account activation)
     *
     * @param string $email Customer email
     * @param string $name Customer name
     * @param string $verifyUrl Verification URL
     * @param int $expiresMinutes Token TTL in minutes
     * @return bool
     */
    public function sendEmailVerification($email, $name, $verifyUrl, $expiresMinutes = 30) {
        $subject = "Xác minh email của bạn - BookStore";

        $safeName = htmlspecialchars((string)$name, ENT_QUOTES, 'UTF-8');
        $safeUrl = htmlspecialchars((string)$verifyUrl, ENT_QUOTES, 'UTF-8');
        $expiresMinutes = (int)$expiresMinutes;

        $body = "
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #007bff; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background-color: #f9f9f9; }
                .button { background-color: #28a745; color: white; padding: 10px 20px; text-decoration: none; display: inline-block; margin: 10px 0; border-radius: 6px; }
                .muted { color: #666; font-size: 13px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>BookStore</h1>
                </div>
                <div class='content'>
                    <h2>Xin chào {$safeName}!</h2>
                    <p>Vui lòng xác minh email để kích hoạt tài khoản của bạn.</p>
                    <p><a href='{$safeUrl}' class='button'>Xác minh email</a></p>
                    <p class='muted'>Liên kết này sẽ hết hạn sau {$expiresMinutes} phút.</p>
                    <p class='muted'>Nếu bạn không tạo tài khoản, vui lòng bỏ qua email này.</p>
                    <p class='muted'>Trân trọng,<br>Đội ngũ BookStore</p>
                </div>
            </div>
        </body>
        </html>";

        return $this->sendEmail($email, $name, $subject, $body);
    }
    
    /**
     * Send password reset email using a pre-built reset link (MVC route)
     *
     * @param string $email Customer email
     * @param string $name Customer name
     * @param string $resetLink Full reset link
     * @return bool
     */
    public function sendPasswordResetEmail($email, $name, $resetLink) {
        $subject = "Đặt lại mật khẩu - BookStore";

        $body = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #007bff; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background-color: #f9f9f9; }
                .button { background-color: #ffc107; color: black; padding: 10px 20px; text-decoration: none; display: inline-block; margin: 10px 0; }
                .warning { color: #dc3545; font-weight: bold; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>BookStore</h1>
                </div>
                <div class='content'>
                    <h2>Xin chào {$name}!</h2>
                    <p>Bạn đã yêu cầu đặt lại mật khẩu cho tài khoản BookStore của mình.</p>
                    <p>Vui lòng nhấp vào nút bên dưới để đặt lại mật khẩu:</p>
                    <a href='{$resetLink}' class='button'>Đặt lại mật khẩu</a>
                    <p class='warning'>Liên kết này sẽ hết hạn sau 1 giờ.</p>
                    <p>Nếu bạn không yêu cầu đặt lại mật khẩu, vui lòng bỏ qua email này.</p>
                    <p>Trân trọng,<br>Đội ngũ BookStore</p>
                </div>
            </div>
        </body>
        </html>";

        return $this->sendEmail($email, $name, $subject, $body);
    }
    
    /**
     * Send order confirmation email
     * 
     * @param string $email Customer email
     * @param string $name Customer name
     * @param array $orderData Order details
     * @return bool Success status
     */
    public function sendOrderConfirmation($email, $name, $orderData) {
        $subject = "Xác nhận đơn hàng #{$orderData['order_number']} - BookStore";
        
        // Build items list
        $itemsHtml = '';
        foreach ($orderData['items'] as $item) {
            $itemsHtml .= "<tr>
                <td>{$item['title']}</td>
                <td>{$item['quantity']}</td>
                <td>" . number_format($item['unit_price'], 0, ',', '.') . " đ</td>
                <td>" . number_format($item['total_price'], 0, ',', '.') . " đ</td>
            </tr>";
        }
        
        $body = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #28a745; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background-color: #f9f9f9; }
                table { width: 100%; border-collapse: collapse; margin: 15px 0; }
                th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
                th { background-color: #007bff; color: white; }
                .total { font-size: 18px; font-weight: bold; text-align: right; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Đơn hàng đã được xác nhận!</h1>
                </div>
                <div class='content'>
                    <h2>Xin chào {$name}!</h2>
                    <p>Cảm ơn bạn đã đặt hàng tại BookStore. Đơn hàng của bạn đã được xác nhận.</p>
                    <p><strong>Mã đơn hàng:</strong> {$orderData['order_number']}</p>
                    <p><strong>Ngày đặt:</strong> {$orderData['order_date']}</p>
                    
                    <h3>Chi tiết đơn hàng:</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Sản phẩm</th>
                                <th>Số lượng</th>
                                <th>Đơn giá</th>
                                <th>Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            {$itemsHtml}
                        </tbody>
                    </table>
                    
                    <p class='total'>Tổng cộng: " . number_format($orderData['total_amount'], 0, ',', '.') . " đ</p>
                    
                    <h3>Thông tin giao hàng:</h3>
                    <p><strong>Người nhận:</strong> {$orderData['recipient_name']}<br>
                    <strong>Địa chỉ:</strong> {$orderData['delivery_address']}<br>
                    <strong>Điện thoại:</strong> {$orderData['phone']}</p>
                    
                    <p>Chúng tôi sẽ liên hệ với bạn sớm nhất để xác nhận giao hàng.</p>
                    <p>Trân trọng,<br>Đội ngũ BookStore</p>
                </div>
            </div>
        </body>
        </html>";
        
        return $this->sendEmail($email, $name, $subject, $body);
    }
}
?>
