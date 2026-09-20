<?php
/**
 * process-payment.php
 * AJAX endpoint: validates CSRF, saves the order as paid with txn id,
 * records order items, generates access token, and notifies admin via WhatsApp.
 */
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/cart-functions.php';

header('Content-Type: application/json');

// Only POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'طريقة غير مسموحة']);
    exit;
}

// CSRF guard
csrf_guard();

// Validate customer data
$customerName = trim($_POST['customer_name'] ?? '');
$customerEmail = trim($_POST['customer_email'] ?? '');
$customerPhone = trim($_POST['customer_phone'] ?? '');
$customerAddress = trim($_POST['customer_address'] ?? '');
$customerCity = trim($_POST['customer_city'] ?? '');
$notes = trim($_POST['notes'] ?? '');
$txnId = trim($_POST['payment_txn_id'] ?? '');

$errors = [];
if ($customerName === '') $errors[] = 'الاسم الكامل مطلوب';
if (!filter_var($customerEmail, FILTER_VALIDATE_EMAIL)) $errors[] = 'بريد إلكتروني غير صالح';
if (!preg_match('/^[0-9+\-\s]{7,20}$/', $customerPhone)) $errors[] = 'رقم هاتف غير صالح';
if ($customerAddress === '' || $customerCity === '') $errors[] = 'العنوان مطلوب';
if ($txnId === '') $errors[] = 'رقم عملية الدفع مطلوب';

if (!empty($errors)) {
    echo json_encode(['success' => false, 'message' => implode('، ', $errors)]);
    exit;
}

// Cart snapshot from session
$items = $_SESSION['checkout_cart'] ?? [];
$total = isset($_SESSION['checkout_total']) ? (float)$_SESSION['checkout_total'] : 0.0;

if (empty($items) || $total <= 0) {
    echo json_encode(['success' => false, 'message' => 'سلة التسوق فارغة']);
    exit;
}

try {
    $pdo->beginTransaction();

    $orderNumber = generate_order_number($pdo);
    $fullAddress = $customerAddress . ($customerCity !== '' ? ' - ' . $customerCity : '');

    // Generate a unique access token for the course
    $accessToken = bin2hex(random_bytes(32));

    $stmt = $pdo->prepare(
        "INSERT INTO orders (order_number, customer_name, customer_email, customer_phone, customer_address, notes, total_amount, payment_txn_id, status, course_access_token, course_sent)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'paid', ?, 0)"
    );
    $stmt->execute([
        $orderNumber,
        $customerName,
        $customerEmail,
        $customerPhone,
        $fullAddress,
        $notes !== '' ? $notes : null,
        $total,
        $txnId,
        $accessToken,
    ]);
    $orderId = (int)$pdo->lastInsertId();

    // Insert order items
    $stmtItem = $pdo->prepare(
        "INSERT INTO order_items (order_id, course_id, quantity, price) VALUES (?, ?, ?, ?)"
    );
    $courseName = '';
    foreach ($items as $cid => $item) {
        $courseName = $item['name'];
        $stmtItem->execute([
            $orderId,
            (int)$item['id'],
            (int)$item['quantity'],
            (float)$item['price'],
        ]);
    }

    $pdo->commit();
} catch (Exception $ex) {
    $pdo->rollBack();
    error_log("Payment processing error: " . $ex->getMessage());
    echo json_encode(['success' => false, 'message' => 'حدث خطأ أثناء معالجة الطلب. يرجى المحاولة لاحقاً.']);
    exit;
}

// Build WhatsApp notification for admin only (no customer link)
$whatsappAdminText = "📦 طلب جديد مدفوع:\n"
    . "رقم الطلب: {$orderNumber}\n"
    . "العميل: {$customerName}\n"
    . "الهاتف: {$customerPhone}\n"
    . "البريد: {$customerEmail}\n"
    . "المبلغ: " . money($total) . "\n"
    . "رقم العملية: {$txnId}\n"
    . "الدورة: {$courseName}\n"
    . "\nللوصول إلى لوحة التحكم: " . BASE_URL . "/admin/orders.php";

$whatsappAdminLink = 'https://wa.me/' . ADMIN_WHATSAPP_NUMBER . '?text=' . rawurlencode($whatsappAdminText);

// Clear cart and session
cart_clear();
unset($_SESSION['checkout_cart'], $_SESSION['checkout_total']);

// Return response with only admin WhatsApp link
echo json_encode([
    'success' => true,
    'order_id' => $orderId,
    'order_number' => $orderNumber,
    'whatsapp_admin_link' => $whatsappAdminLink,
    'redirect' => BASE_URL . '/order-success.php?order=' . urlencode($orderNumber),
]);