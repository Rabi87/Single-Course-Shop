<?php
$pageTitle = 'سلة التسوق';
require_once __DIR__ . '/includes/header.php';

$totals = cart_totals($pdo);
$items = $totals['items'];
?>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <div class="page-header-content">
            <h1><i class="fas fa-shopping-bag"></i> سلة التسوق</h1>
            <p>راجع اختياراتك قبل إتمام عملية الشراء</p>
        </div>
    </div>
</section>

<!-- Cart Section -->
<section class="cart-section">
    <div class="container">
        <?php if (empty($items)): ?>
            <div class="empty-cart">
                <div class="empty-cart-icon">
                    <i class="fas fa-shopping-bag"></i>
                </div>
                <h2>سلتك فارغة</h2>
                <p>لم تضف أي دورة بعد. ابدأ بتصفح الدورة التدريبية المتاحة.</p>
                <a href="<?= BASE_URL ?>/index.php" class="btn btn-primary">
                    <i class="fas fa-book-open"></i>
                    <span>تصفح الدورات</span>
                </a>
            </div>
        <?php else: ?>
            <div class="cart-grid">
                <div class="cart-items">
                    <div class="cart-header">
                        <h2>عناصر السلة (<?= $totals['count'] ?>)</h2>
                    </div>
                    <div class="cart-list">
                        <?php foreach ($items as $courseId => $item): ?>
                            <div class="cart-card">
                                <div class="cart-card-image">
                                    <?php if ($item['image'] && file_exists($item['image'])): ?>
                                        <img src="<?= BASE_URL ?>/<?= e($item['image']) ?>" alt="<?= e($item['name']) ?>">
                                    <?php else: ?>
                                        <div class="cart-placeholder">
                                            <i class="fas fa-graduation-cap"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="cart-card-body">
                                    <h3><?= e($item['name']) ?></h3>
                                    <p class="cart-item-price"><?= money($item['price']) ?> / مقعد</p>
                                </div>
                                <div class="cart-card-actions">
                                    <div class="cart-item-total"><?= money($item['line_total']) ?></div>
                                    <button type="button" class="btn-remove" data-course-id="<?= (int)$item['id'] ?>" title="حذف">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="cart-summary">
                    <div class="summary-card">
                        <h3>ملخص الطلب</h3>
                        <div class="summary-row">
                            <span>عدد المقاعد</span>
                            <span><?= $totals['count'] ?></span>
                        </div>
                        <div class="summary-row">
                            <span>المجموع الفرعي</span>
                            <span><?= money($totals['total']) ?></span>
                        </div>
                        <div class="summary-row total">
                            <span>الإجمالي</span>
                            <span><?= money($totals['total']) ?></span>
                        </div>
                        <a href="<?= BASE_URL ?>/checkout.php" class="btn btn-checkout">
                            <i class="fas fa-arrow-left"></i>
                            <span>إتمام الشراء</span>
                        </a>
                        <a href="<?= BASE_URL ?>/index.php" class="btn btn-continue">
                            <i class="fas fa-arrow-right"></i>
                            <span>متابعة التسوق</span>
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
