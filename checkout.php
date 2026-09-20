<?php
$pageTitle = 'إتمام الطلب';
require_once __DIR__ . '/includes/header.php';

$totals = cart_totals($pdo);
$items = $totals['items'];

if (empty($items)) {
    header('Location: ' . BASE_URL . '/cart.php');
    exit;
}

$_SESSION['checkout_cart'] = $items;
$_SESSION['checkout_total'] = $totals['total'];
?>

<div class="checkout-section">
    <div class="container">
        <div class="checkout-steps page-header">
            <div class="checkout-step done">
                <span class="step-num"><i class="fas fa-check fa-xs"></i></span>
                <span>السلة</span>
            </div>
            <div class="step-connector done"></div>
            <div class="checkout-step active">
                <span class="step-num">2</span>
                <span>البيانات</span>
            </div>
            <div class="step-connector"></div>
            <div class="checkout-step">
                <span class="step-num">3</span>
                <span>الدفع</span>
            </div>
        </div>

        <div class="page-head">
            <h1><i class="fas fa-file-signature"></i> إتمام الطلب</h1>
            <p>أكمل بياناتك الشخصية ثم انتقل إلى خطوة الدفع</p>
        </div>

        <div class="checkout-grid">
            <div class="checkout-form-col">
                <form id="checkoutForm" novalidate>
                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

                    <div class="checkout-card mb-4">
                        <div class="card-header"><i class="fas fa-user me-2"></i> بيانات العميل</div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">الاسم الكامل <span class="text-danger">*</span></label>
                                    <input type="text" name="customer_name" class="form-control" required placeholder="أدخل اسمك الكامل">
                                    <div class="invalid-feedback">الاسم مطلوب</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">البريد الإلكتروني <span class="text-danger">*</span></label>
                                    <input type="email" name="customer_email" class="form-control" required placeholder="example@email.com">
                                    <div class="invalid-feedback">بريد إلكتروني غير صالح</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">رقم الهاتف <span class="text-danger">*</span></label>
                                    <input type="tel" name="customer_phone" class="form-control" required pattern="[0-9+\s-]{7,20}" placeholder="05xxxxxxxx">
                                    <div class="invalid-feedback">رقم هاتف غير صالح</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">المدينة <span class="text-danger">*</span></label>
                                    <input type="text" name="customer_city" class="form-control" required placeholder="الرياض">
                                    <div class="invalid-feedback">المدينة مطلوبة</div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">العنوان الكامل <span class="text-danger">*</span></label>
                                    <textarea name="customer_address" class="form-control" rows="3" required placeholder="الحي، الشارع، رقم المبنى"></textarea>
                                    <div class="invalid-feedback">العنوان مطلوب</div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">ملاحظات <span class="text-muted">(اختياري)</span></label>
                                    <textarea name="notes" class="form-control" rows="2" placeholder="أي ملاحظات إضافية..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between flex-wrap gap-2">
                        <a href="<?= BASE_URL ?>/cart.php" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-right"></i> العودة للسلة
                        </a>
                        <button type="submit" class="btn btn-primary btn-lg" id="proceedBtn">
                            <i class="fas fa-qrcode"></i> تقديم الطلب والدفع
                        </button>
                    </div>
                </form>
            </div>

            <div class="checkout-summary-col">
                <div class="checkout-card sticky-top" style="top: calc(var(--navbar-height) + 1rem);">
                    <div class="card-header"><i class="fas fa-receipt me-2"></i> ملخص الطلب</div>
                    <div class="card-body">
                        <?php foreach ($items as $courseId => $item): ?>
                        <div class="cart-summary-row">
                            <span><?= e($item['name']) ?> × <?= (int)$item['quantity'] ?></span>
                            <span><?= money($item['line_total']) ?></span>
                        </div>
                        <?php endforeach; ?>
                        <div class="cart-summary-row total">
                            <span>الإجمالي</span>
                            <span class="value"><?= money($totals['total']) ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-qrcode text-accent"></i> الدفع عبر رمز QR</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
            </div>
            <div class="modal-body text-center">
                <p class="text-muted mb-3"><?= e(PAYMENT_INSTRUCTIONS) ?></p>
                <div class="qr-container">
                    <img src="<?= BASE_URL ?>/<?= e(QR_IMAGE_PATH) ?>" alt="QR Code" class="qr-img"
                         onerror="this.onerror=null;this.src='https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=<?= e(ADMIN_WHATSAPP_NUMBER) ?>'">
                </div>
                <div class="text-start mt-3">
                    <label class="form-label">رقم عملية الدفع <span class="text-danger">*</span></label>
                    <input type="text" name="payment_txn_id" id="paymentTxnId" class="form-control form-control-lg" required placeholder="مثال: TXN-123456789">
                    <div class="invalid-feedback">يرجى إدخال رقم عملية الدفع</div>
                </div>
                <div id="paymentMsg" class="text-danger small mt-2"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-primary btn-lg" id="confirmPaymentBtn">
                    <i class="fas fa-check-circle"></i> تأكيد الدفع
                </button>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
