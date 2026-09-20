<?php
$pageTitle = 'لوحة القيادة';
require_once __DIR__ . '/includes/admin_header.php';

// Stats
$totalOrders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$paidOrders  = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'paid'")->fetchColumn();
$pendingOrders = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'")->fetchColumn();
$cancelledOrders = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'cancelled'")->fetchColumn();
$totalRevenue = $pdo->query("SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE status = 'paid'")->fetchColumn();

// Recent orders
$recentOrders = $pdo->query("SELECT o.*, GROUP_CONCAT(c.name SEPARATOR '، ') AS items
    FROM orders o
    LEFT JOIN order_items oi ON oi.order_id = o.id
    LEFT JOIN courses c ON c.id = oi.course_id
    GROUP BY o.id
    ORDER BY o.created_at DESC
    LIMIT 5")->fetchAll();

// Course info
$course = $pdo->query("SELECT * FROM courses ORDER BY id ASC LIMIT 1")->fetch();
?>

<div class="page-header-bar">
    <h1>لوحة القيادة</h1>
    <p>نظرة عامة على أداء المتجر</p>
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
        <div class="stat-label">المدفوعة</div>
    </div>
    <div class="stat-card stat-warning">
        <i class="fas fa-clock stat-icon"></i>
        <div class="stat-value"><?= (int)$pendingOrders ?></div>
        <div class="stat-label">قيد الانتظار</div>
    </div>
    <div class="stat-card stat-danger">
        <i class="fas fa-times-circle stat-icon"></i>
        <div class="stat-value"><?= (int)$cancelledOrders ?></div>
        <div class="stat-label">الملغاة</div>
    </div>
    <div class="stat-card stat-primary">
        <i class="fas fa-coins stat-icon"></i>
        <div class="stat-value"><?= money($totalRevenue) ?></div>
        <div class="stat-label">إجمالي الإيرادات</div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-7">
        <div class="admin-card">
            <div class="card-header">
                <i class="fas fa-clock-rotate-left"></i> آخر الطلبات
            </div>
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>الطلب</th>
                            <th>العميل</th>
                            <th>المبلغ</th>
                            <th>الحالة</th>
                            <th>التاريخ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentOrders as $order): ?>
                        <tr>
                            <td style="font-weight: 700;"><?= e($order['order_number']) ?></td>
                            <td>
                                <?= e($order['customer_name']) ?><br>
                                <small class="text-muted"><?= e($order['customer_phone']) ?></small>
                            </td>
                            <td style="color: var(--accent); font-weight: 700;"><?= money($order['total_amount']) ?></td>
                            <td><span class="badge badge-<?= e($order['status']) ?>"><?= order_status_label($order['status']) ?></span></td>
                            <td class="text-muted small"><?= e(date('Y-m-d H:i', strtotime($order['created_at']))) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($recentOrders)): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted" style="padding: 2rem;">
                                <i class="fas fa-inbox" style="font-size: 1.5rem; margin-bottom: 0.5rem; display: block; opacity: 0.5;"></i>
                                لا توجد طلبات بعد
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div style="text-align: center; margin-top: 1rem;">
                <a href="<?= BASE_URL ?>/admin/orders.php" class="btn btn-outline-secondary">
                    عرض كل الطلبات <i class="fas fa-arrow-left ms-1"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-5">
        <div class="admin-card" style="margin-bottom: 1.5rem;">
            <div class="card-header">
                <i class="fas fa-bolt"></i> إجراءات سريعة
            </div>
            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                <a href="<?= BASE_URL ?>/admin/edit-course.php" class="btn btn-outline-secondary" style="justify-content: flex-start; text-align: right;">
                    <i class="fas fa-book-open" style="color: var(--primary);"></i> تعديل بيانات الدورة
                </a>
                <a href="<?= BASE_URL ?>/admin/orders.php" class="btn btn-outline-secondary" style="justify-content: flex-start; text-align: right;">
                    <i class="fas fa-shopping-bag" style="color: var(--accent);"></i> إدارة الطلبات
                </a>
                <a href="<?= BASE_URL ?>/index.php" target="_blank" class="btn btn-outline-secondary" style="justify-content: flex-start; text-align: right;">
                    <i class="fas fa-store" style="color: var(--primary);"></i> عرض المتجر
                </a>
            </div>
        </div>

        <div class="admin-card">
            <div class="card-header">
                <i class="fas fa-circle-info"></i> معلومات الدورة
            </div>
            <?php if ($course): ?>
            <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                <?php if ($course['image'] && file_exists(__DIR__ . '/../' . $course['image'])): ?>
                    <img src="<?= BASE_URL ?>/<?= e($course['image']) ?>" alt="" style="width: 64px; height: 64px; border-radius: var(--radius-sm); object-fit: cover; border: 1px solid var(--border-subtle);">
                <?php else: ?>
                    <div style="width: 64px; height: 64px; border-radius: var(--radius-sm); background: var(--bg-input); display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-image text-muted"></i>
                    </div>
                <?php endif; ?>
                <div>
                    <div style="font-weight: 700; color: var(--text-primary);"><?= e($course['name']) ?></div>
                    <div style="color: var(--accent); font-weight: 800; font-size: 1.1rem;"><?= money($course['price']) ?></div>
                </div>
            </div>
            <p class="text-muted small" style="line-height: 1.7;"><?= e(mb_substr($course['description'] ?? '', 0, 120)) ?><?= mb_strlen($course['description'] ?? '') > 120 ? '...' : '' ?></p>
            <?php else: ?>
            <p class="text-muted">لا توجد دورة مضافة.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
