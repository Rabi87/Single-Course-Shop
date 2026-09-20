<?php
$pageTitle = 'محتوى الدورة';
require_once __DIR__ . '/includes/header.php';

$token = trim($_GET['token'] ?? '');

if ($token === '') {
    header('Location: ' . BASE_URL . '/index.php');
    exit;
}

$stmt = $pdo->prepare("
    SELECT o.*, c.name as course_name, c.description as course_description, c.image
    FROM orders o
    JOIN order_items oi ON oi.order_id = o.id
    JOIN courses c ON c.id = oi.course_id
    WHERE o.course_access_token = ? AND o.status = 'paid'
    LIMIT 1
");
$stmt->execute([$token]);
$order = $stmt->fetch();

if (!$order) {
    echo '<div class="alert alert-danger mt-4">رابط غير صالح أو منتهي الصلاحية.</div>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

$lessons = [
    'الدرس الأول: مقدمة في الدورة',
    'الدرس الثاني: الأساسيات والمفاهيم',
    'الدرس الثالث: التطبيق العملي',
    'الدرس الرابع: المشروع النهائي',
];
?>

<div class="page-head">
    <h1><i class="fas fa-play-circle"></i> محتوى الدورة</h1>
    <p>مرحباً <?= e($order['customer_name']) ?>، إليك محتوى الدورة التي اشتريتها</p>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="glass-card">
            <div class="card-body p-4">
                <h2 class="fw-bold mb-2" style="font-size:1.5rem;"><?= e($order['course_name']) ?></h2>
                <p class="text-muted mb-4"><?= nl2br(e($order['course_description'])) ?></p>

                <h5 class="fw-bold mb-3"><i class="fas fa-list-ul text-accent me-2"></i>دروس الدورة</h5>
                <ul class="lesson-list">
                    <?php foreach ($lessons as $i => $lesson): ?>
                    <li class="lesson-item">
                        <span class="lesson-num"><?= $i + 1 ?></span>
                        <span class="lesson-title"><?= e($lesson) ?></span>
                        <i class="fas fa-play-circle lesson-play"></i>
                    </li>
                    <?php endforeach; ?>
                </ul>

                <div class="info-banner">
                    <i class="fas fa-info-circle mt-1"></i>
                    <span>هذا الرابط مخصص لك. يرجى حفظه للرجوع إليه في أي وقت.</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="glass-card order-sidebar">
            <div class="card-header"><i class="fas fa-receipt me-2"></i> بيانات الطلب</div>
            <div class="card-body">
                <p><strong>العميل:</strong> <?= e($order['customer_name']) ?></p>
                <p><strong>البريد:</strong> <?= e($order['customer_email']) ?></p>
                <p><strong>رقم الطلب:</strong> <?= e($order['order_number']) ?></p>
                <p><strong>تاريخ الشراء:</strong> <?= e(date('Y-m-d', strtotime($order['created_at']))) ?></p>
                <a href="<?= BASE_URL ?>/index.php" class="btn btn-primary w-100 mt-3">
                    <i class="fas fa-home"></i> العودة للرئيسية
                </a>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
