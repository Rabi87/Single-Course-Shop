<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

redirect_if_admin();

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_guard();
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'يرجى إدخال اسم المستخدم وكلمة المرور';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ? LIMIT 1");
        $stmt->execute([$username]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password_hash'])) {
            secure_regenerate_session();
            $_SESSION['admin_id'] = (int)$admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['admin_name'] = $admin['full_name'];
            $_SESSION['admin_role'] = $admin['role'];

            $pdo->prepare("UPDATE admins SET last_login = NOW() WHERE id = ?")->execute([$admin['id']]);

            header('Location: ' . BASE_URL . '/admin/index.php');
            exit;
        } else {
            $error = 'اسم المستخدم أو كلمة المرور غير صحيحة';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول — لوحة التحكم</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800;900&display=swap" rel="stylesheet">
    <link href="<?= BASE_URL ?>/assets/css/themes.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/assets/css/admin.css" rel="stylesheet">
</head>
<body class="admin-login-body">
    <div class="login-wrap">
        <div class="login-card">
            <div class="login-logo"><i class="fas fa-shield-halved"></i></div>
            <h4>لوحة التحكم</h4>
            <p class="subtitle"><?= e(APP_NAME) ?></p>

            <?php if ($error): ?>
            <div class="admin-alert admin-alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?= e($error) ?>
            </div>
            <?php endif; ?>

            <form method="POST" action="login.php">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                <div class="mb-3">
                    <label class="form-label">اسم المستخدم</label>
                    <div class="input-group">
                        <input type="text" name="username" class="form-control" required autofocus placeholder="admin">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="form-label">كلمة المرور</label>
                    <div class="input-group">
                        <input type="password" name="password" class="form-control" required placeholder="••••••••">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary w-100 py-2" style="border-radius: var(--radius-sm);">
                    <i class="fas fa-sign-in-alt"></i> تسجيل الدخول
                </button>
            </form>

            <div class="text-center mt-3">
                <a href="<?= BASE_URL ?>/index.php" class="small text-muted text-decoration-none">
                    <i class="fas fa-arrow-right me-1"></i> العودة للمتجر
                </a>
            </div>

            <div class="login-hint">
                بعد تشغيل <strong>setup.php</strong>: admin / admin123
            </div>
        </div>
    </div>
</body>
</html>
<?php $pdo = null; ?>
