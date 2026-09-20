<?php
/**
 * cart-ajax.php
 * AJAX endpoint for cart operations (add, update, remove, get).
 * Returns JSON.
 */
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/cart-functions.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? $_POST['action'] ?? '';

// CSRF check for state-changing actions (not for "get")
if (in_array($action, ['add', 'update', 'remove'], true)) {
    csrf_guard();
}

switch ($action) {
    case 'add':
        $courseId = (int)($_POST['course_id'] ?? 0);
        $qty = max(1, (int)($_POST['quantity'] ?? 1));
        // verify course exists
        $stmt = $pdo->prepare("SELECT id FROM courses WHERE id = ?");
        $stmt->execute([$courseId]);
        if (!$stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'الدورة غير موجودة']);
            exit;
        }
        cart_add($courseId, $qty);
        $t = cart_totals($pdo);
        echo json_encode(['success' => true, 'message' => 'تمت الإضافة للسلة', 'count' => $t['count'], 'total' => number_format($t['total'],2)]);
        break;

    case 'update':
        $courseId = (int)($_POST['course_id'] ?? 0);
        $qty = (int)($_POST['quantity'] ?? 1);
        cart_update($courseId, $qty);
        $t = cart_totals($pdo);
        echo json_encode(['success' => true, 'count' => $t['count'], 'total' => number_format($t['total'],2)]);
        break;

    case 'remove':
        $courseId = (int)($_POST['course_id'] ?? 0);
        cart_remove($courseId);
        $t = cart_totals($pdo);
        echo json_encode(['success' => true, 'count' => $t['count'], 'total' => number_format($t['total'],2)]);
        break;

    case 'get':
    default:
        $t = cart_totals($pdo);
        echo json_encode(['success' => true, 'count' => $t['count'], 'total' => number_format($t['total'],2)]);
        break;
}
