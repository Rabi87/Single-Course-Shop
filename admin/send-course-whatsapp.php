<?php
/**
 * admin/send-course-whatsapp.php
 * Admin manually sends the course access link to the customer via WhatsApp.
 */
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/includes/admin_header.php';

// CSRF guard
csrf_guard();

$orderId = (int)($_POST['order_id'] ?? 0);

if ($orderId <= 0) {
    header('Location: orders.php?error=معرف طلب غير صالح');
    exit;
}

// Fetch order details
$stmt = $pdo->prepare("
    SELECT o.*, c.name as course_name
    FROM orders o
    JOIN order_items oi ON oi.order_id = o.id
    JOIN courses c ON c.id = oi.course_id
    WHERE o.id = ?
");
$stmt->execute([$orderId]);
$order = $stmt->fetch();

if (!$order || $order['status'] !== 'paid') {
    header('Location: orders.php?error=الطلب غير مدفوع أو غير موجود');
    exit;
}

// Ensure access token exists
if (empty($order['course_access_token'])) {
    $accessToken = bin2hex(random_bytes(32));
    $pdo->prepare("UPDATE orders SET course_access_token = ? WHERE id = ?")
        ->execute([$accessToken, $orderId]);
    $order['course_access_token'] = $accessToken;
}

// Build course link
$courseLink = BASE_URL . '/course-access.php?token=' . urlencode($order['course_access_token']);

// WhatsApp message for customer
$customerPhone = ltrim($order['customer_phone'], '+');
$whatsappText = "🎓 مرحباً {$order['customer_name']}،\n\n"
    . "نشكرك على شرائك دورة '{$order['course_name']}' من " . APP_NAME . ".\n"
    . "يمكنك الآن مشاهدة محتوى الدورة من خلال الرابط التالي:\n"
    . $courseLink . "\n\n"
    . "مع خالص التقدير،\nفريق " . APP_NAME;

$whatsappLink = 'https://wa.me/' . $customerPhone . '?text=' . rawurlencode($whatsappText);

// Update order: mark as sent
$pdo->prepare("UPDATE orders SET course_sent = 1 WHERE id = ?")->execute([$orderId]);

// Redirect to WhatsApp
header('Location: ' . $whatsappLink);
exit;