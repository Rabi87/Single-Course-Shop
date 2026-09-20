<?php
/**
 * includes/config.php
 * Database connection + application constants + secure session bootstrap.
 */

// Start output buffering to allow redirects after headers are partially sent
ob_start();

// ---- Secure session settings (must be set before session_start) ----
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', '1');
    // Enable Secure flag only over HTTPS (production). Disabled for local http dev.
    ini_set('session.cookie_secure', isset($_SERVER['HTTPS']) ? '1' : '0');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.cookie_samesite', 'Strict');
    session_start();
}

// ---- Database configuration ----
define('DB_HOST', 'db');
define('DB_PORT', '3306');
define('DB_NAME', 'course_store_db');
define('DB_USER', 'root');
define('DB_PASS', 'root_secure_password_123');
define('DB_CHARSET', 'utf8mb4');

// ---- Application settings ----
define('BASE_URL', 'http://localhost:8084/courses');
define('CURRENCY', 'ل.س');
define('APP_NAME', 'متجر الدورات التدريبية');

// ---- Payment / WhatsApp settings ----
// QR code image that represents the payment info (wallet phone / IBAN).
// Place your real QR image in assets/qr/ (png/jpg/svg) and update this path.
define('QR_IMAGE_PATH', 'assets/qr/qr-code.svg');

// Admin WhatsApp number (international format, digits only, no + or spaces)
define('ADMIN_WHATSAPP_NUMBER', '+963956969459');

// Payment instruction text shown in the modal
define('PAYMENT_INSTRUCTIONS', 'يرجى مسح رمز QR باستخدام تطبيق الدفع، ثم إدخال رقم عملية الدفع بعد إتمام التحويل.');

// ---- Timezone ----
date_default_timezone_set('Asia/Riyadh');

// ---- Error reporting (disable display in production) ----
ini_set('display_errors', '0');
error_reporting(E_ALL);
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/../logs/php_errors.log');

// ---- PDO connection ----
try {
    $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $pdo = new PDO(
        $dsn,
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    error_log("DB connection error: " . $e->getMessage());
    die('عذراً، تعذر الاتصال بقاعدة البيانات. يرجى المحاولة لاحقاً.');
}
