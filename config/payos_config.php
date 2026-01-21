<?php
// PayOS Configuration
// Prefer environment variables in production
define('PAYOS_CLIENT_ID', getenv('PAYOS_CLIENT_ID') ?: 'b43512eb-1c35-4831-b21e-909ed7093313');
define('PAYOS_API_KEY', getenv('PAYOS_API_KEY') ?: '07606807-7b31-4828-9193-ffd22cff7203');
define('PAYOS_CHECKSUM_KEY', getenv('PAYOS_CHECKSUM_KEY') ?: 'bb28ab62d8b3038861e8fc4e777da80f9f2ebf4814a80a6c4a9825f22f2343a0');

// Optional: base app URL for production (e.g., https://your-domain.com)
define('PAYOS_APP_URL', rtrim(getenv('PAYOS_APP_URL') ?: '', '/'));
