<?php
$pageTitle = 'إدارة الدورة';
require_once __DIR__ . '/includes/admin_header.php';

$stmt = $pdo->query("SELECT * FROM courses ORDER BY id ASC LIMIT 1");
$course = $stmt->fetch();

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_guard();

    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = (float)($_POST['price'] ?? 0);

    if ($name === '' || $price <= 0) {
        $error = 'اسم الدورة والسعر مطلوبان';
    } else {
        $imagePath = $course['image'] ?? null;

        if (!empty($_FILES['image']['name'])) {
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];
            $tmp = $_FILES['image']['tmp_name'];
            $size = $_FILES['image']['size'];
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

            if ($size > 2 * 1024 * 1024) {
                $error = 'حجم الصورة يتجاوز 2 ميجابايت';
            } elseif (!in_array($ext, $allowed, true)) {
                $error = 'صيغة الصورة غير مسموحة (jpg, jpeg, png, webp)';
            } elseif (!@getimagesize($tmp)) {
                $error = 'الملف ليس صورة صالحة';
            } else {
                $newName = 'course_' . bin2hex(random_bytes(8)) . '.' . $ext;
                $uploadDir = __DIR__ . '/../assets/images/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0775, true);
                if (move_uploaded_file($tmp, $uploadDir . $newName)) {
                    $imagePath = 'assets/images/' . $newName;
                } else {
                    $error = 'تعذر رفع الصورة';
                }
            }
        }

        if (!$error) {
            $stmt = $pdo->prepare(
                "UPDATE courses SET name = ?, description = ?, price = ?, image = ? WHERE id = ?"
            );
            $stmt->execute([$name, $description, $price, $imagePath, $course['id']]);
            $message = 'تم حفظ التعديلات بنجاح';

            $stmt = $pdo->query("SELECT * FROM courses ORDER BY id ASC LIMIT 1");
            $course = $stmt->fetch();
        }
    }
}
?>

<div class="page-header-bar d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <h1>إدارة الدورة</h1>
        <p>تعديل بيانات الدورة المعروضة في المتجر</p>
    </div>
</div>

<?php if ($message): ?>
<div class="admin-alert admin-alert-success"><i class="fas fa-check-circle"></i> <?= e($message) ?></div>
<?php endif; ?>
<?php if ($error): ?>
<div class="admin-alert admin-alert-danger"><i class="fas fa-exclamation-circle"></i> <?= e($error) ?></div>
<?php endif; ?>

<div class="admin-card">
    <form method="POST" action="edit-course.php" enctype="multipart/form-data" class="admin-form">
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

        <div class="row g-4">
            <div class="col-md-7">
                <div class="mb-3">
                    <label class="form-label">اسم الدورة</label>
                    <input type="text" name="name" class="form-control" required value="<?= e($course['name']) ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">الوصف</label>
                    <textarea name="description" class="form-control" rows="6"><?= e($course['description']) ?></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">السعر (<?= e(CURRENCY) ?>)</label>
                    <input type="number" name="price" class="form-control" step="0.01" min="0" required value="<?= e($course['price']) ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">صورة الدورة</label>
                    <input type="file" name="image" class="form-control" accept=".jpg,.jpeg,.png,.webp">
                    <div class="form-text">يُسمح بصور jpg, jpeg, png, webp بحد أقصى 2MB. اتركه فارغاً للإبقاء على الصورة الحالية.</div>
                </div>
            </div>

            <div class="col-md-5">
                <label class="form-label d-block">الصورة الحالية</label>
                <?php if ($course['image'] && file_exists(__DIR__ . '/../' . $course['image'])): ?>
                    <img src="<?= BASE_URL ?>/<?= e($course['image']) ?>" alt="الدورة" class="img-fluid rounded-3 border mb-3" style="max-height:220px;">
                <?php else: ?>
                    <div class="d-flex align-items-center justify-content-center mb-3" style="height:180px; background: var(--bg-input); border-radius: var(--radius-sm); border: 2px dashed var(--border-subtle);">
                        <i class="fas fa-image text-muted" style="font-size: 2rem;"></i>
                    </div>
                <?php endif; ?>

                <div class="admin-alert admin-alert-info">
                    <i class="fas fa-info-circle"></i>
                    <div>
                        <strong>رقم واتساب المدير:</strong> <?= e(ADMIN_WHATSAPP_NUMBER) ?><br>
                        <strong>مسار رمز QR:</strong> <?= e(QR_IMAGE_PATH) ?><br>
                        <strong>ملاحظة:</strong> QR يُعرض تلقائياً عبر واجهة المُولّد إن لم يوجد الملف.<br>
                        <small class="text-muted">يمكن تغيير هذه الإعدادات من <code>includes/config.php</code></small>
                    </div>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary px-4 mt-3">
            <i class="fas fa-save"></i> حفظ التعديلات
        </button>
    </form>
</div>
<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
