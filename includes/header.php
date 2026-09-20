<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/cart-functions.php';

$cartTotals = cart_totals($pdo);
$navCount = $cartTotals['count'];

$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="base-url" content="<?= e(BASE_URL) ?>">
    <title><?= e($pageTitle ?? APP_NAME) ?> — <?= e(APP_NAME) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/assets/css/themes.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/assets/css/custom.css" rel="stylesheet">
</head>
<body>

<div class="main-wrapper">

<!-- Header -->
<header class="site-header">
    <div class="header-container">
        <div class="header-brand">
            <a href="<?= BASE_URL ?>/index.php" class="brand-link">
                <div class="brand-icon">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <span class="brand-text"><?= e(APP_NAME) ?></span>
            </a>
        </div>
        
        <nav class="header-nav">
            <a href="<?= BASE_URL ?>/index.php" class="nav-link <?= $currentPage === 'index.php' ? 'active' : '' ?>">
                <i class="fas fa-book-open"></i>
                <span>الدورة</span>
            </a>
            <a href="<?= BASE_URL ?>/cart.php" class="nav-link cart-link <?= $currentPage === 'cart.php' ? 'active' : '' ?>">
                <i class="fas fa-shopping-bag"></i>
                <span>السلة</span>
                <?php if ($navCount > 0): ?>
                    <span class="cart-count"><?= $navCount ?></span>
                <?php endif; ?>
            </a>
            <a href="<?= BASE_URL ?>/admin/login.php" class="nav-link">
                <i class="fas fa-shield-halved"></i>
                <span>لوحة التحكم</span>
            </a>
        </nav>

        <div class="header-actions">
            <button class="theme-toggle" id="themeToggle" aria-label="تبديل المظهر">
                <i class="fas fa-moon" id="themeIcon"></i>
                <span id="themeLabel">داكن</span>
            </button>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navStore" aria-controls="navStore" aria-expanded="false" aria-label="القائمة">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
    </div>
</header>

<style>
@media (max-width: 991px) {
    .header-container {
        flex-wrap: wrap;
        height: auto;
        padding: 16px 24px;
    }
    
    .header-nav {
        order: 3;
        width: 100%;
        flex-direction: column;
        align-items: stretch;
        padding-top: 16px;
        margin-top: 16px;
        border-top: 1px solid var(--border);
        display: none;
    }
    
    .header-nav.show {
        display: flex;
    }
    
    .header-nav .nav-link {
        width: 100%;
        justify-content: flex-start;
        padding: 12px 16px !important;
    }
    
    .header-actions {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .theme-toggle {
        padding: 8px 14px;
        font-size: 0.85rem;
    }
    
    .theme-toggle span {
        display: none;
    }
}
</style>

<main class="container">
