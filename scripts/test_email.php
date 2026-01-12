<?php

require_once __DIR__ . '/../Model/EmailSender.php';

// Usage:
//   php scripts/test_email.php recipient@example.com "Optional Name"
// Reads config from config/email.php or env vars.

$to = $argv[1] ?? '';
$toName = $argv[2] ?? 'Test Recipient';

if (empty($to)) {
    fwrite(STDERR, "Usage: php scripts/test_email.php recipient@example.com \"Optional Name\"\n");
    exit(1);
}

$emailSender = new EmailSender();

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
exit(2);
