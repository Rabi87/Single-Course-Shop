<?php
/**
 * includes/cart-functions.php
 * Session-based shopping cart helpers.
 */

/**
 * Ensure the cart session array exists.
 */
function cart_init() {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
}

/**
 * Add a course to the cart.
 * في هذا المتجر (منتج واحد)، نضع الكمية دائماً = 1، ولا نسمح بتكرار المنتج.
 */
function cart_add($courseId, $quantity = 1) {
    cart_init();
    $courseId = (int)$courseId;
    // تجاهل القيمة المدخلة وضبط الكمية على 1 دائماً
    $_SESSION['cart'][$courseId] = 1; 
}

/**
 * Update the quantity of a cart item.
 * (تم تعديلها لتتوافق مع منطق الكمية = 1، لكنها تبقى للتوافق).
 */
function cart_update($courseId, $quantity) {
    cart_init();
    $courseId = (int)$courseId;
    $quantity = (int)$quantity;
    if ($quantity <= 0) {
        cart_remove($courseId);
        return;
    }
    // في هذا السياق، نضع الكمية 1 دائماً
    if (isset($_SESSION['cart'][$courseId])) {
        $_SESSION['cart'][$courseId] = 1;
    }
}

/**
 * Remove a course from the cart.
 */
function cart_remove($courseId) {
    cart_init();
    unset($_SESSION['cart'][(int)$courseId]);
}

/**
 * Clear the entire cart.
 */
function cart_clear() {
    $_SESSION['cart'] = [];
}

/**
 * Get the cart contents as [courseId => quantity].
 */
function cart_items() {
    cart_init();
    return $_SESSION['cart'];
}

/**
 * Get the total number of items in the cart.
 */
function cart_count() {
    cart_init();
    return array_sum($_SESSION['cart']);
}

/**
 * Get the cart total amount based on the DB course prices.
 * Returns [count, subtotal, items].
 */
function cart_totals($pdo) {
    cart_init();
    $items = $_SESSION['cart'];
    $count = 0;
    $total = 0.0;
    $detailed = [];

    if (!empty($items)) {
        $ids = array_keys($items);
        $placeholders = rtrim(str_repeat('?,', count($ids)), ',');
        $stmt = $pdo->prepare("SELECT id, name, price, image FROM courses WHERE id IN ($placeholders)");
        $stmt->execute($ids);
        $courses = $stmt->fetchAll();
        foreach ($courses as $course) {
            $qty = $items[$course['id']];
            $lineTotal = (float)$course['price'] * $qty;
            $count += $qty;
            $total += $lineTotal;
            $detailed[$course['id']] = [
                'id'       => $course['id'],
                'name'     => $course['name'],
                'price'    => (float)$course['price'],
                'image'    => $course['image'],
                'quantity' => $qty,
                'line_total' => $lineTotal,
            ];
        }
    }

    return ['count' => $count, 'total' => $total, 'items' => $detailed];
}