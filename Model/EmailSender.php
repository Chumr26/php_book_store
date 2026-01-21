<?php
/**
 * Email Sender Class
 * 
 * Wrapper for PHPMailer to send emails
 * Uses Composer's PHPMailer library
 */

class EmailSender {
    private $resendApiKey;
    private $fromEmail;
    private $fromName;
    private $lastError;
    private $isConfigured;
    
    /**
     * Constructor - Initialize PHPMailer
     * 
     * @param array $config SMTP configuration (optional, uses defaults if not provided)
     */
    public function __construct($config = []) {
        $this->lastError = null;
        $this->isConfigured = false;

        // Load configuration from env and optional config file, then override with provided $config
        $baseConfig = $this->loadConfig();
        if (is_array($config) && !empty($config)) {
            $baseConfig = array_merge($baseConfig, $config);
        }

        // Resend HTTP API configuration
        $this->resendApiKey = $baseConfig['resend_api_key'] ?? '';
        $this->fromEmail = $baseConfig['from_email'] ?? '';
        $this->fromName = $baseConfig['from_name'] ?? 'BookStore';

        // Validate config
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

        $envConfig = [
            'resend_api_key' => getenv('RESEND_API_KEY') ?: null,
            'from_email' => getenv('RESEND_FROM_EMAIL') ?: null,
            'from_name' => getenv('RESEND_FROM_NAME') ?: null,
        ];

        $envConfig = array_filter($envConfig, fn($v) => $v !== null && $v !== '');

        $localFileConfig = [];
        if (file_exists($localConfigFile)) {
            $localFileConfig = include $localConfigFile;
            if (!is_array($localFileConfig)) {
                throw new RuntimeException('Invalid SMTP config in config/email.local.php (expected a PHP array).');
            }
        }

        $finalConfig = array_merge($envConfig, $localFileConfig);

        if (empty($finalConfig['resend_api_key']) || empty($finalConfig['from_email'])) {
            throw new RuntimeException(
                'Missing Resend configuration. Provide RESEND_API_KEY and RESEND_FROM_EMAIL (and optional RESEND_FROM_NAME) '
                . 'in environment variables or config/email.local.php.'
            );
        }

        return $finalConfig;
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
        $this->isConfigured = false;

        if (empty($this->resendApiKey)) {
            $this->lastError = 'Missing Resend API key (RESEND_API_KEY).';
            $this->logMailError('EmailSender not configured: ' . $this->lastError);
            return;
        }

        if (empty($this->fromEmail)) {
            $this->lastError = 'Missing sender address (RESEND_FROM_EMAIL).';
            $this->logMailError('EmailSender not configured: ' . $this->lastError);
            return;
        }

        $this->isConfigured = true;
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

            if (!function_exists('curl_init')) {
                $this->lastError = 'cURL extension is not available.';
                $this->logMailError('Email send failed: ' . $this->lastError);
                return false;
            }

            $payload = [
                'from' => $this->fromName
                    ? $this->fromName . ' <' . $this->fromEmail . '>'
                    : $this->fromEmail,
                'to' => [$to],
                'subject' => $subject,
                'html' => $body,
                'text' => $altBody ?: strip_tags($body)
            ];

            $ch = curl_init('https://api.resend.com/emails');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $this->resendApiKey,
                'Content-Type: application/json'
            ]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            if ($response === false || $httpCode < 200 || $httpCode >= 300) {
                $errorMessage = $curlError ?: ('HTTP ' . $httpCode . ' response');
                $decoded = json_decode((string)$response, true);
                if (is_array($decoded) && !empty($decoded['message'])) {
                    $errorMessage .= ' - ' . $decoded['message'];
                }
                $this->lastError = $errorMessage;
                $this->logMailError("Email sending failed: {$this->lastError}");
                return false;
            }

            return true;

        } catch (Exception $e) {
            $this->lastError = $e->getMessage();
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
     * Render a consistent, modern HTML email layout.
     *
     * @param string $title
     * @param string $greetingHtml
     * @param string $contentHtml
     * @param string|null $ctaText
     * @param string|null $ctaUrl
     * @param string|null $noteHtml
     * @return string
     */
    private function renderEmailLayout($title, $greetingHtml, $contentHtml, $ctaText = null, $ctaUrl = null, $noteHtml = null) {
        $safeTitle = htmlspecialchars((string)$title, ENT_QUOTES, 'UTF-8');

        $ctaBlock = '';
        if (!empty($ctaText) && !empty($ctaUrl)) {
            $safeCtaText = htmlspecialchars((string)$ctaText, ENT_QUOTES, 'UTF-8');
            $safeCtaUrl = htmlspecialchars((string)$ctaUrl, ENT_QUOTES, 'UTF-8');
            $ctaBlock = "<div style='margin: 18px 0 6px;'>
                <a href='{$safeCtaUrl}' style='display:inline-block;background:#007bff;color:#fff;text-decoration:none;padding:12px 18px;border-radius:10px;font-weight:600'>
                    {$safeCtaText}
                </a>
            </div>";
        }

        $noteBlock = '';
        if (!empty($noteHtml)) {
            $noteBlock = "<div style='margin-top: 14px; padding: 12px 14px; background:#f8f9fa; border:1px solid #e9ecef; border-radius:12px; color:#6c757d; font-size:13px;'>
                {$noteHtml}
            </div>";
        }

        return "
        <html>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        </head>
        <body style='margin:0;padding:0;background:#f1f3f5;font-family: Arial, sans-serif;line-height:1.6;color:#212529;'>
            <div style='max-width: 640px; margin: 0 auto; padding: 28px 16px;'>
                <div style='background:#ffffff;border-radius:16px;overflow:hidden;border:1px solid #e9ecef;'>
                    <div style='background:#0b5ed7;color:#ffffff;padding:22px 24px;'>
                        <div style='font-size:18px;font-weight:700;letter-spacing:.2px'>BookStore</div>
                        <div style='opacity:.95;font-size:13px;margin-top:4px'>{$safeTitle}</div>
                    </div>

                    <div style='padding: 22px 24px;'>
                        <div style='font-size:16px;font-weight:700;margin-bottom:10px'>{$greetingHtml}</div>
                        <div style='font-size:14px;color:#343a40'>{$contentHtml}</div>
                        {$ctaBlock}
                        {$noteBlock}
                        <div style='margin-top: 18px; font-size: 13px; color:#6c757d'>Trân trọng,<br>Đội ngũ BookStore</div>
                    </div>
                </div>

                <div style='text-align:center;color:#868e96;font-size:12px;margin-top:14px'>
                    Nếu bạn không yêu cầu email này, bạn có thể bỏ qua.
                </div>
            </div>
        </body>
        </html>";
    }

    private function renderGreeting($name) {
        $safeName = htmlspecialchars((string)$name, ENT_QUOTES, 'UTF-8');
        return "Xin chào {$safeName}!";
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

        $loginUrl = (defined('BASE_URL') ? BASE_URL : '') . 'index.php?page=login';
        $greeting = $this->renderGreeting($name);
        $content = "<p>Cảm ơn bạn đã đăng ký tài khoản tại BookStore.</p>
                    <p>Tài khoản của bạn đã được tạo thành công. Bạn có thể đăng nhập và bắt đầu mua sắm.</p>";

        $body = $this->renderEmailLayout(
            'Chào mừng bạn đến với BookStore',
            $greeting,
            $content,
            'Đăng nhập',
            $loginUrl,
            "<strong>Lưu ý:</strong> Nếu bạn vừa đăng ký tài khoản, bạn có thể cần xác minh email trước khi đăng nhập."
        );

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

        $greeting = $this->renderGreeting($name);
        $content = "<p>Vui lòng xác minh email để kích hoạt tài khoản của bạn.</p>
                    <p>Nhấp vào nút bên dưới để hoàn tất xác minh.</p>";

        $expiresMinutes = (int)$expiresMinutes;
        if ($expiresMinutes <= 0) $expiresMinutes = 30;

        $note = "Liên kết này sẽ hết hạn sau <strong>{$expiresMinutes} phút</strong>.";

        $body = $this->renderEmailLayout(
            'Xác minh email',
            $greeting,
            $content,
            'Xác minh email',
            $verifyUrl,
            $note
        );

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

        $greeting = $this->renderGreeting($name);
        $content = "<p>Bạn đã yêu cầu đặt lại mật khẩu cho tài khoản BookStore của mình.</p>
                    <p>Nhấp vào nút bên dưới để đặt lại mật khẩu.</p>";

        $note = "Liên kết này sẽ hết hạn sau <strong>1 giờ</strong>.";

        $body = $this->renderEmailLayout(
            'Đặt lại mật khẩu',
            $greeting,
            $content,
            'Đặt lại mật khẩu',
            $resetLink,
            $note
        );

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
            $title = htmlspecialchars((string)($item['title'] ?? ''), ENT_QUOTES, 'UTF-8');
            $qty = (int)($item['quantity'] ?? 0);
            $unit = (float)($item['unit_price'] ?? 0);
            $total = (float)($item['total_price'] ?? 0);
            $itemsHtml .= "<tr>
                <td>{$title}</td>
                <td>{$qty}</td>
                <td>" . number_format($unit, 0, ',', '.') . " đ</td>
                <td>" . number_format($total, 0, ',', '.') . " đ</td>
            </tr>";
        }

        $greeting = $this->renderGreeting($name);

        $orderNumber = htmlspecialchars((string)($orderData['order_number'] ?? ''), ENT_QUOTES, 'UTF-8');
        $orderDate = htmlspecialchars((string)($orderData['order_date'] ?? ''), ENT_QUOTES, 'UTF-8');
        $recipientName = htmlspecialchars((string)($orderData['recipient_name'] ?? ''), ENT_QUOTES, 'UTF-8');
        $deliveryAddress = htmlspecialchars((string)($orderData['delivery_address'] ?? ''), ENT_QUOTES, 'UTF-8');
        $phone = htmlspecialchars((string)($orderData['phone'] ?? ''), ENT_QUOTES, 'UTF-8');
        $totalAmount = (float)($orderData['total_amount'] ?? 0);

        $content = "
            <p>Cảm ơn bạn đã đặt hàng tại BookStore. Đơn hàng của bạn đã được xác nhận.</p>
            <p><strong>Mã đơn hàng:</strong> {$orderNumber}<br>
               <strong>Ngày đặt:</strong> {$orderDate}</p>

            <div style='margin:14px 0;'>
                <div style='font-weight:700;margin-bottom:8px;'>Chi tiết đơn hàng</div>
                <table style='width:100%;border-collapse:collapse;border:1px solid #e9ecef;border-radius:12px;overflow:hidden;'>
                    <thead>
                        <tr style='background:#f8f9fa;'>
                            <th style='padding:10px;border-bottom:1px solid #e9ecef;text-align:left;'>Sản phẩm</th>
                            <th style='padding:10px;border-bottom:1px solid #e9ecef;text-align:left;'>SL</th>
                            <th style='padding:10px;border-bottom:1px solid #e9ecef;text-align:left;'>Đơn giá</th>
                            <th style='padding:10px;border-bottom:1px solid #e9ecef;text-align:left;'>Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        {$itemsHtml}
                    </tbody>
                </table>
                <div style='margin-top:10px;text-align:right;font-weight:700;'>Tổng cộng: " . number_format($totalAmount, 0, ',', '.') . " đ</div>
            </div>

            <div style='margin-top:14px;'>
                <div style='font-weight:700;margin-bottom:6px;'>Thông tin giao hàng</div>
                <div><strong>Người nhận:</strong> {$recipientName}</div>
                <div><strong>Địa chỉ:</strong> {$deliveryAddress}</div>
                <div><strong>Điện thoại:</strong> {$phone}</div>
            </div>
        ";

        $body = $this->renderEmailLayout(
            'Xác nhận đơn hàng',
            $greeting,
            $content,
            null,
            null,
            null
        );

        return $this->sendEmail($email, $name, $subject, $body);
    }
}
?>
