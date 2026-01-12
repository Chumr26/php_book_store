<?php

return [
    // Configure with your SMTP provider credentials.
    // Resend SMTP (recommended if you have a verified domain):
    // - Host: smtp.resend.com
    // - Username: resend
    // - Password: YOUR_RESEND_API_KEY (starts with re_...)
    // - STARTTLS ports: 25, 587, 2587
    // - SMTPS ports: 465, 2465
    'smtp_host' => getenv('SMTP_HOST') ?: 'smtp.resend.com',
    'smtp_port' => (int)(getenv('SMTP_PORT') ?: 587),
    'smtp_username' => getenv('SMTP_USERNAME') ?: 'resend',
    'smtp_password' => getenv('SMTP_PASSWORD') ?: '',

    // starttls (explicit TLS) or smtps (implicit TLS)
    'smtp_secure' => getenv('SMTP_SECURE') ?: 'starttls',

    // Sender shown to recipients (must be on your verified Resend domain)
    // Example: noreply@yourdomain.com
    'from_email' => getenv('SMTP_FROM_EMAIL') ?: 'noreply@bookstore.nguyenanhkhoa.me',
    'from_name' => getenv('SMTP_FROM_NAME') ?: 'BookStore',

    // Set to 1 to enable SMTP debug logging (dev only)
    'smtp_debug' => (int)(getenv('SMTP_DEBUG') ?: 0),
];
