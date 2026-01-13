<?php

require_once __DIR__ . '/../Model/EmailSender.php';

// Usage:
//   php scripts/test_email.php recipient@example.com "Optional Name"
// Requires config/email.local.php

$to = $argv[1] ?? '';
$toName = $argv[2] ?? 'Test Recipient';

if (empty($to)) {
    fwrite(STDERR, "Usage: php scripts/test_email.php recipient@example.com \"Optional Name\"\n");
    exit(1);
}

try {
    $emailSender = new EmailSender();
} catch (Throwable $e) {
    fwrite(STDERR, "FAILED: " . $e->getMessage() . "\n");
    fwrite(STDERR, "Tip: Copy config/email.local.php.example to config/email.local.php and set smtp_password.\n");
    exit(2);
}

$subject = 'BookStore SMTP Test';
$body = '<p>This is a test email from BookStore via SMTP.</p>';

$ok = $emailSender->sendEmail($to, $toName, $subject, $body);

if ($ok) {
    echo "OK: Email sent\n";
    exit(0);
}

echo "FAILED: Email not sent\n";
$err = $emailSender->getLastError();
if (!empty($err)) {
    echo "LastError: {$err}\n";
}

echo "Check tmp/logs/email.log for details.\n";
echo "Tip: Put your Resend API key in config/email.local.php (see config/email.local.php.example).\n";
exit(2);
