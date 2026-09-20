<?php
$pageTitle = 'إدارة الطلبات';
require_once __DIR__ . '/includes/admin_header.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    csrf_guard();
    $orderId = (int)($_POST['order_id'] ?? 0);
    $status = $_POST['status'] ?? '';

    $allowed = ['pending', 'paid', 'cancelled'];
    if (in_array($status, $allowed, true)) {
        $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute([$status, $orderId]);
        $message = 'تم تحديث حالة الطلب بنجاح';
    } else {
        $error = 'حالة غير صالحة';
    }
}

$filter = $_GET['status'] ?? '';
$allowedFilters = ['', 'pending', 'paid', 'cancelled'];
if (!in_array($filter, $allowedFilters, true)) $filter = '';

$sql = "SELECT o.*, GROUP_CONCAT(CONCAT(oi.quantity, ' x ', c.name) SEPARATOR '، ') AS items
        FROM orders o
        LEFT JOIN order_items oi ON oi.order_id = o.id
        LEFT JOIN courses c ON c.id = oi.course_id";
if ($filter !== '') {
    $sql .= " WHERE o.status = " . $pdo->quote($filter);
}
$sql .= " GROUP BY o.id ORDER BY o.created_at DESC";
$orders = $pdo->query($sql)->fetchAll();

// Stats
$totalOrders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$paidOrders  = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'paid'")->fetchColumn();
$pendingOrders = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'")->fetchColumn();
$totalRevenue = $pdo->query("SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE status = 'paid'")->fetchColumn();
?>

<div class="page-header-bar d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <h1>إدارة الطلبات</h1>
        <p>متابعة الطلبات وحالات الدفع</p>
    </div>
    <div class="btn-group" role="group">
        <a href="orders.php" class="btn btn-sm <?= $filter === '' ? 'btn-primary' : 'btn-outline-secondary' ?>">الكل</a>
        <a href="orders.php?status=pending" class="btn btn-sm <?= $filter === 'pending' ? 'btn-outline-secondary' : 'btn-outline-secondary' ?>">قيد الانتظار</a>
        <a href="orders.php?status=paid" class="btn btn-sm <?= $filter === 'paid' ? 'btn-primary' : 'btn-outline-secondary' ?>">مدفوع</a>
        <a href="orders.php?status=cancelled" class="btn btn-sm <?= $filter === 'cancelled' ? 'btn-outline-secondary' : 'btn-outline-secondary' ?>">ملغي</a>
    </div>
</div>

<div class="stats-grid">
    <div class="stat-card stat-primary">
        <i class="fas fa-shopping-bag stat-icon"></i>
        <div class="stat-value"><?= (int)$totalOrders ?></div>
        <div class="stat-label">إجمالي الطلبات</div>
    </div>
    <div class="stat-card stat-success">
        <i class="fas fa-check-circle stat-icon"></i>
        <div class="stat-value"><?= (int)$paidOrders ?></div>
        <div class="stat-label">الطلبات المدفوعة</div>
    </div>
    <div class="stat-card stat-warning">
        <i class="fas fa-clock stat-icon"></i>
        <div class="stat-value"><?= (int)$pendingOrders ?></div>
        <div class="stat-label">قيد الانتظار</div>
    </div>
    <div class="stat-card stat-primary">
        <i class="fas fa-coins stat-icon"></i>
        <div class="stat-value"><?= money($totalRevenue) ?></div>
        <div class="stat-label">إجمالي الإيرادات</div>
    </div>
</div>

<?php if ($message): ?>
    <div class="admin-alert admin-alert-success"><i class="fas fa-check-circle"></i> <?= e($message) ?></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="admin-alert admin-alert-danger"><i class="fas fa-exclamation-circle"></i> <?= e($error) ?></div>
<?php endif; ?>

<div class="admin-table-wrap">
    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>الطلب</th>
                    <th>العميل</th>
                    <th>العناصر</th>
                    <th>المبلغ</th>
                    <th>رقم العملية</th>
                    <th>الحالة</th>
                    <th>التاريخ</th>
                    <th>تغيير الحالة</th>
                    <th>إرسال الرابط</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td class="fw-bold"><a href="#" data-bs-toggle="modal" data-bs-target="#orderModal<?= $order['id'] ?>" style="color: var(--accent); text-decoration: none;"><?= e($order['order_number']) ?></a></td>
                        <td>
                            <?= e($order['customer_name']) ?><br>
                            <small class="text-muted"><?= e($order['customer_phone']) ?></small>
                        </td>
                        <td class="small text-muted"><?= e($order['items'] ?? '—') ?></td>
                        <td style="color: var(--accent); font-weight: 700;"><?= money($order['total_amount']) ?></td>
                        <td class="small text-muted"><?= e($order['payment_txn_id'] ?? '—') ?></td>
                        <td><span class="badge badge-<?= e($order['status']) ?>"><?= order_status_label($order['status']) ?></span></td>
                        <td class="text-muted small"><?= e(date('Y-m-d H:i', strtotime($order['created_at']))) ?></td>
                        <td>
                            <form method="POST" action="orders.php" class="d-flex gap-1" style="flex-wrap: nowrap;">
                                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                <input type="hidden" name="update_status" value="1">
                                <input type="hidden" name="order_id" value="<?= (int)$order['id'] ?>">
                                <select name="status" class="form-select form-select-sm" style="width:auto; min-width: 120px;">
                                    <option value="pending" <?= $order['status'] === 'pending' ? 'selected' : '' ?>>قيد الانتظار</option>
                                    <option value="paid" <?= $order['status'] === 'paid' ? 'selected' : '' ?>>مدفوع</option>
                                    <option value="cancelled" <?= $order['status'] === 'cancelled' ? 'selected' : '' ?>>ملغي</option>
                                </select>
                                <button type="submit" class="btn btn-sm btn-outline-primary" title="تحديث"><i class="fas fa-check"></i></button>
                            </form>
                        </td>
                        <td>
                            <?php if ($order['status'] === 'paid'): ?>
                                <?php if ($order['course_sent']): ?>
                                    <span class="badge badge-paid">تم الإرسال</span>
                                <?php else: ?>
                                    <form method="POST" action="send-course-whatsapp.php" style="display:inline;" target="_blank">
                                        <input type="hidden" name="order_id" value="<?= (int)$order['id'] ?>">
                                        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                        <button type="submit" class="btn btn-sm btn-primary" title="إرسال رابط الدورة عبر واتساب">
                                            <i class="fab fa-whatsapp"></i> إرسال
                                        </button>
                                    </form>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="text-muted small">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>

                    <!-- Order detail modal -->
                    <div class="modal fade" id="orderModal<?= $order['id'] ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">تفاصيل الطلب <?= e($order['order_number']) ?></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"><i class="fas fa-times"></i></button>
                                </div>
                                <div class="modal-body">
                                    <p><strong>العميل:</strong> <span style="color: var(--text-primary);"><?= e($order['customer_name']) ?></span></p>
                                    <p><strong>البريد:</strong> <span style="color: var(--text-primary);"><?= e($order['customer_email']) ?></span></p>
                                    <p><strong>الهاتف:</strong> <span style="color: var(--text-primary);"><?= e($order['customer_phone']) ?></span></p>
                                    <p><strong>العنوان:</strong> <span style="color: var(--text-primary);"><?= e($order['customer_address']) ?></span></p>
                                    <p><strong>الملاحظات:</strong> <span style="color: var(--text-primary);"><?= e($order['notes'] ?? '—') ?></span></p>
                                    <p><strong>العناصر:</strong> <span style="color: var(--text-primary);"><?= e($order['items'] ?? '—') ?></span></p>
                                    <p><strong>المبلغ:</strong> <span style="color: var(--accent); font-weight: 700;"><?= money($order['total_amount']) ?></span></p>
                                    <p><strong>رقم العملية:</strong> <span style="color: var(--text-primary);"><?= e($order['payment_txn_id'] ?? '—') ?></span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

                <?php if (empty($orders)): ?>
                    <tr>
                        <td colspan="9" class="text-center text-muted" style="padding: 3rem;">
                            <i class="fas fa-inbox" style="font-size: 2rem; margin-bottom: 0.5rem; display: block; opacity: 0.5;"></i>
                            لا توجد طلبات
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
