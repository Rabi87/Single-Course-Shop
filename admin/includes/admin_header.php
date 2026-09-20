<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/functions.php';

require_admin();

$adminName = $_SESSION['admin_name'] ?? 'مدير';
$adminRole = $_SESSION['admin_role'] ?? 'admin';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'لوحة التحكم') ?> - <?= e(APP_NAME) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- استخدام نفس ملفات الثيمات والستايلات العامة -->
    <link href="<?= BASE_URL ?>/assets/css/themes.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/assets/css/custom.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/assets/css/admin.css" rel="stylesheet">
    <style>
        /* ============================================================
           ADMIN PANEL STYLES (using CSS variables from themes.css)
           ============================================================ */
        body.admin-panel {
            background: var(--bg-body);
            color: var(--text-primary);
            min-height: 100vh;
        }

        /* ---------- Sidebar ---------- */
        .admin-sidebar {
            position: fixed;
            top: 0;
            right: 0;
            height: 100vh;
            width: 240px;
            background: var(--bg-card);
            border-left: 1px solid var(--border-subtle);
            z-index: 1000;
            transition: transform 0.3s ease;
            padding-top: 1.5rem;
            box-shadow: var(--shadow-card);
        }

        .sidebar-brand {
            padding: 1rem 1.5rem;
            font-size: 1.1rem;
            font-weight: 800;
            color: var(--text-primary);
            border-bottom: 1px solid var(--border-subtle);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar-brand .brand-icon {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--accent-dark), var(--accent-light));
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 0.9rem;
            flex-shrink: 0;
        }

        .admin-nav {
            padding: 1rem;
        }

        .admin-nav a {
            display: block;
            color: var(--text-muted);
            padding: 0.7rem 1rem;
            border-radius: 8px;
            text-decoration: none;
            margin-bottom: 4px;
            transition: var(--transition);
        }

        .admin-nav a:hover {
            background: var(--bg-card-hover);
            color: var(--text-primary);
        }

        .admin-nav a.active {
            background: var(--accent-subtle);
            color: var(--accent);
            font-weight: 600;
        }

        .admin-nav a i {
            margin-left: 8px;
            color: var(--accent);
            width: 18px;
            text-align: center;
        }

        .logout-form {
            margin-top: 20px;
        }

        .admin-logout-btn {
            width: 100%;
            background: transparent;
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #f87171;
            padding: 0.7rem 1rem;
            border-radius: 8px;
            cursor: pointer;
            transition: var(--transition);
        }

        .admin-logout-btn:hover {
            background: rgba(239, 68, 68, 0.1);
        }

        /* ---------- Main area ---------- */
        .admin-main {
            margin-right: 240px;
        }

        .admin-topbar {
            background: var(--bg-card);
            border-bottom: 1px solid var(--border-subtle);
            padding: 0.8rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .admin-topbar .btn-outline-secondary {
            border-color: var(--border-subtle);
            color: var(--text-muted);
        }

        .admin-topbar .btn-outline-secondary:hover {
            background: var(--bg-card-hover);
            color: var(--text-primary);
        }

        .admin-content {
            padding: 1.5rem 2rem;
        }

        /* ---------- Admin Cards ---------- */
        .admin-card {
            background: var(--bg-card);
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius);
            box-shadow: var(--shadow-card);
            padding: 1.5rem;
        }

        .admin-card .card-header {
            background: var(--bg-card-hover);
            border-bottom: 1px solid var(--border-subtle);
            color: var(--text-primary);
            font-weight: 700;
            padding: 1rem 1.5rem;
            margin: -1.5rem -1.5rem 1rem -1.5rem;
            border-radius: var(--radius) var(--radius) 0 0;
        }

        /* ---------- Admin Tables ---------- */
        .admin-table {
            color: var(--text-primary);
        }

        .admin-table thead {
            background: var(--bg-input);
        }

        .admin-table th {
            color: var(--text-secondary);
            font-weight: 600;
            border-bottom: 1px solid var(--border-subtle);
        }

        .admin-table td {
            border-bottom: 1px solid var(--border-subtle);
            color: var(--text-primary);
        }

        .admin-table tbody tr:hover {
            background: var(--bg-card-hover);
        }

        /* ---------- Admin Forms ---------- */
        .admin-form .form-control {
            background: var(--bg-input);
            border: 1px solid var(--border-subtle);
            color: var(--text-primary);
            border-radius: 10px;
            padding: 0.7rem 1rem;
            transition: var(--transition);
        }

        .admin-form .form-control:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px var(--accent-glow);
        }

        .admin-form .form-label {
            color: var(--text-muted);
            font-weight: 500;
        }

        /* ---------- Admin Alerts ---------- */
        .admin-alert-success {
            background: rgba(52, 211, 153, 0.1);
            border: 1px solid #34d399;
            color: #34d399;
            padding: 0.75rem 1rem;
            border-radius: 8px;
        }

        .admin-alert-danger {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid #ef4444;
            color: #ef4444;
            padding: 0.75rem 1rem;
            border-radius: 8px;
        }

        .admin-alert-info {
            background: var(--accent-subtle);
            border: 1px solid rgba(13, 148, 136, 0.2);
            color: var(--accent);
            padding: 0.75rem 1rem;
            border-radius: 8px;
        }

        /* ---------- Admin Stats Cards ---------- */
        .stat-card {
            border-radius: var(--radius);
            padding: 1.5rem;
            position: relative;
            overflow: hidden;
            background: var(--bg-card);
            border: 1px solid var(--border-subtle);
            transition: var(--transition);
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-card);
        }

        .stat-card .stat-icon {
            position: absolute;
            top: 10px;
            left: 15px;
            font-size: 2rem;
            opacity: 0.15;
            color: var(--text-primary);
        }

        .stat-card .stat-value {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--text-primary);
        }

        .stat-card .stat-label {
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .stat-card.stat-primary { border-left: 4px solid var(--accent); }
        .stat-card.stat-success { border-left: 4px solid #34d399; }
        .stat-card.stat-warning { border-left: 4px solid #fbbf24; }
        .stat-card.stat-danger { border-left: 4px solid #f87171; }

        /* ---------- Responsive ---------- */
        @media (max-width: 768px) {
            .admin-sidebar {
                transform: translateX(100%);
            }
            .admin-sidebar.open {
                transform: translateX(0);
            }
            .admin-main {
                margin-right: 0;
            }
            .admin-content {
                padding: 1rem;
            }
        }
    </style>
</head>
<body class="admin-panel">
<div class="admin-sidebar" id="adminSidebar">
    <div class="sidebar-brand">
        <span class="brand-icon"><i class="fas fa-graduation-cap"></i></span>
        لوحة التحكم
    </div>
    <nav class="admin-nav">
        <a href="<?= BASE_URL ?>/admin/index.php" class="<?= basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : '' ?>">
            <i class="fas fa-home"></i> لوحة القيادة
        </a>
        <a href="<?= BASE_URL ?>/admin/edit-course.php" class="<?= basename($_SERVER['PHP_SELF']) === 'edit-course.php' ? 'active' : '' ?>">
            <i class="fas fa-book"></i> إدارة الدورة
        </a>
        <a href="<?= BASE_URL ?>/admin/orders.php" class="<?= basename($_SERVER['PHP_SELF']) === 'orders.php' ? 'active' : '' ?>">
            <i class="fas fa-shopping-bag"></i> الطلبات
        </a>
        <a href="<?= BASE_URL ?>/index.php" target="_blank">
            <i class="fas fa-store"></i> عرض المتجر
        </a>
        <form method="POST" action="<?= BASE_URL ?>/admin/logout.php" class="logout-form">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <button type="submit" class="admin-logout-btn">
                <i class="fas fa-sign-out-alt"></i> تسجيل الخروج
            </button>
        </form>
    </nav>
</div>

<div class="admin-main">
    <div class="admin-topbar">
        <button class="btn btn-outline-secondary d-lg-none" id="sidebarToggle"><i class="fas fa-bars"></i></button>
        <div class="ms-auto d-flex align-items-center gap-2">
            <i class="fas fa-user-circle fs-4" style="color: var(--accent);"></i>
            <div>
                <div class="fw-bold small" style="color: var(--text-primary);"><?= e($adminName) ?></div>
                <div class="text-muted small"><?= $adminRole === 'admin' ? 'مدير' : 'مدير مالي' ?></div>
            </div>
        </div>
    </div>
    <div class="admin-content">