<?php
/**
 * includes/functions.php
 * Security & helper functions.
 */

/**
 * Output-escape helper (XSS protection).
 */
function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

/**
 * Generate a CSRF token and store it in the session.
 */
function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Validate the submitted CSRF token against the session token.
 */
function csrf_verify($token) {
    return isset($_SESSION['csrf_token'])
        && is_string($token)
        && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Require a valid CSRF token; otherwise terminate with a 403.
 */
function csrf_guard() {
    $token = $_POST['csrf_token'] ?? '';
    if (!csrf_verify($token)) {
        http_response_code(403);
        die('رمز CSRF غير صالح. يرجى إعادة المحاولة.');
    }
}

/**
 * Regenerate the session ID and CSRF token after a privilege change (login).
 */
function secure_regenerate_session() {
    session_regenerate_id(true);
    // Rotate CSRF token after login
    unset($_SESSION['csrf_token']);
    csrf_token();
}

/**
 * Check if the admin is logged in; redirect to login if not.
 */
function require_admin() {
    if (empty($_SESSION['admin_id'])) {
        header('Location: ' . BASE_URL . '/admin/login.php');
        exit;
    }
}

/**
 * Redirect a logged-in admin away from the login page.
 */
function redirect_if_admin() {
    if (!empty($_SESSION['admin_id'])) {
        header('Location: ' . BASE_URL . '/admin/index.php');
        exit;
    }
}

/**
 * Generate a unique human-friendly order number.
 */
function generate_order_number($pdo) {
    $datePart = date('Ymd');
    $prefix = 'ORD' . $datePart;
    // Count existing orders today to build a sequence
    $stmt = $pdo->prepare(
        "SELECT COUNT(*) AS cnt FROM orders WHERE order_number LIKE :prefix"
    );
    $stmt->execute(['prefix' => $prefix . '%']);
    $seq = (int)$stmt->fetch()['cnt'] + 1;
    return $prefix . '-' . str_pad((string)$seq, 4, '0', STR_PAD_LEFT);
}

/**
 * Build a WhatsApp notification link for a new paid order.
 */
function build_whatsapp_link($orderNumber, $amount, $txnId, $customerName) {
    $text = "تنبيه دفع جديد:\n"
        . "رقم الطلب: {$orderNumber}\n"
        . "المبلغ: {$amount} " . CURRENCY . "\n"
        . "رقم العملية: {$txnId}\n"
        . "من العميل: {$customerName}";
    return 'https://wa.me/' . ADMIN_WHATSAPP_NUMBER . '?text=' . rawurlencode($text);
}

/**
 * Format a number as currency.
 */
function money($amount) {
    return number_format((float)$amount, 2) . ' ' . CURRENCY;
}

/**
 * Translate order status to an Arabic label.
 */
function order_status_label($status) {
    $labels = [
        'pending'   => 'قيد الانتظار',
        'paid'      => 'مدفوع',
        'cancelled' => 'ملغي',
    ];
    return $labels[$status] ?? $status;
}

/**
 * Return a Bootstrap badge class for an order status.
 */
function order_status_badge($status) {
    $map = [
        'pending'   => 'bg-warning text-dark',
        'paid'      => 'bg-success',
        'cancelled' => 'bg-danger',
    ];
    return $map[$status] ?? 'bg-secondary';
}
