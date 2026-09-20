<?php
$pageTitle = 'تم استلام طلبك';
require_once __DIR__ . '/includes/header.php';

$orderNumber = trim($_GET['order'] ?? '');
?>

<section class="success-section">
    <div class="container">
        <div class="success-page">
            <div class="success-icon-wrap">
                <i class="fas fa-check-circle"></i>
            </div>

            <h1>تم استلام طلبك بنجاح!</h1>
            <p class="lead text-muted">شكراً لثقتك بنا. سيتم التواصل معك قريباً لتأكيد التسجيل في الدورة.</p>

            <?php if ($orderNumber !== ''): ?>
            <div class="order-number-card">
                <span class="label">رقم طلبك</span>
                <span class="number"><?= e($orderNumber) ?></span>
            </div>
            <?php endif; ?>

            <div class="success-steps">
                <div class="success-step-item">
                    <div class="icon"><i class="fas fa-check"></i></div>
                    <span>تم تأكيد الطلب</span>
                </div>
                <div class="success-step-item">
                    <div class="icon"><i class="fab fa-whatsapp"></i></div>
                    <span>إشعار للمدير</span>
                </div>
                <div class="success-step-item">
                    <div class="icon"><i class="fas fa-link"></i></div>
                    <span>استلام رابط الدورة</span>
                </div>
            </div>

            <a href="<?= BASE_URL ?>/index.php" class="btn btn-primary btn-lg">
                <i class="fas fa-home"></i> العودة للرئيسية
            </a>

            <p class="text-muted small mt-4">
                <i class="fas fa-info-circle"></i>
                إذا لم يصلك تأكيد خلال 24 ساعة، راسلنا عبر واتساب.
            </p>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
