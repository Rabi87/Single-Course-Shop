<?php
/**
 * setup.php
 * One-time installer: creates the admin account with a proper bcrypt hash.
 * Access via http://localhost:8080/courses/setup.php
 * DELETE THIS FILE AFTER RUNNING.
 */
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_guard();
    $username = trim($_POST['username'] ?? 'admin');
    $password = $_POST['password'] ?? '';

    if (strlen($password) < 6) {
        $error = 'كلمة المرور يجب أن تكون 6 أحرف على الأقل';
    } else {
        $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        try {
            $stmt = $pdo->prepare("SELECT id FROM admins WHERE username = ?");
            $stmt->execute([$username]);
            if ($stmt->fetch()) {
                $pdo->prepare("UPDATE admins SET password_hash = ? WHERE username = ?")
                    ->execute([$hash, $username]);
                $message = "تم تحديث كلمة مرور الحساب '$username' بنجاح";
            } else {
                $pdo->prepare("INSERT INTO admins (username, password_hash, full_name, role) VALUES (?, ?, 'مدير النظام', 'admin')")
                    ->execute([$username, $hash]);
                $message = "تم إنشاء حساب الأدمن '$username' بنجاح";
            }
        } catch (Exception $ex) {
            $error = 'خطأ في قاعدة البيانات: ' . $ex->getMessage();
        }
    }
}

// Check if any admin exists
$adminExists = (int)$pdo->query("SELECT COUNT(*) FROM admins")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إعداد النظام</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card border-0 shadow">
                    <div class="card-header bg-primary text-white text-center py-3">
                        <i class="fas fa-cog fa-2x mb-2"></i>
                        <h4 class="mb-0">إعداد النظام</h4>
                    </div>
                    <div class="card-body p-4">
                        <?php if ($message): ?>
                        <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= e($message) ?></div>
                        <?php endif; ?>
                        <?php if ($error): ?>
                        <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?= e($error) ?></div>
                        <?php endif; ?>

                        <p class="text-muted small mb-3">
                            أنشئ أو حدّث حساب الأدمن. الافتراضي: <strong>admin</strong>
                        </p>

                        <form method="POST" action="setup.php">
                            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                            <div class="mb-3">
                                <label class="form-label">اسم المستخدم</label>
                                <input type="text" name="username" class="form-control" value="<?= $adminExists ? 'admin' : 'admin' ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">كلمة المرور</label>
                                <input type="password" name="password" class="form-control" required minlength="6">
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-key"></i> إنشاء / تحديث الحساب
                            </button>
                        </form>

                        <div class="alert alert-warning small mt-3 mb-0">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>مهم:</strong> احذف ملف <code>setup.php</code> بعد إتمام الإعداد.
                        </div>
                    </div>
                </div>

                <div class="text-center mt-3">
                    <a href="<?= BASE_URL ?>/admin/login.php" class="text-primary">← الذهاب لتسجيل الدخول</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
<?php $pdo = null; ?>
