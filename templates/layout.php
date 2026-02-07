<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Admin') ?> – <?= e(config('app_name')) ?></title>
    <link rel="stylesheet" href="<?= base_url('/assets/css/style.css') ?>">
</head>
<body>
<?php if (empty($hideNav) && \App\Auth::isLoggedIn()): ?>
<nav class="main-nav">
    <div class="nav-brand"><?= e(config('app_name')) ?></div>
    <ul class="nav-links">
        <li><a href="<?= base_url('/admin/dashboard') ?>" <?= ($template ?? '') === 'dashboard/index' ? 'class="active"' : '' ?>>Dashboard</a></li>
        <li><a href="<?= base_url('/admin/persons') ?>" <?= strpos($template ?? '', 'persons/') === 0 ? 'class="active"' : '' ?>>Personen</a></li>
        <li><a href="<?= base_url('/admin/photos') ?>" <?= strpos($template ?? '', 'photos/') === 0 ? 'class="active"' : '' ?>>Fotos</a></li>
        <li><a href="<?= base_url('/admin/devices') ?>" <?= strpos($template ?? '', 'devices/') === 0 ? 'class="active"' : '' ?>>Geraete</a></li>
        <li><a href="<?= base_url('/admin/logs') ?>" <?= strpos($template ?? '', 'logs/') === 0 ? 'class="active"' : '' ?>>Logs</a></li>
    </ul>
    <div class="nav-user">
        <?= e(\App\Auth::adminUsername()) ?>
        <a href="<?= base_url('/admin/logout') ?>" class="btn btn-sm btn-outline">Logout</a>
    </div>
</nav>
<?php endif; ?>

<main class="container">
    <?php if (!empty($flash)): ?>
        <div class="alert alert-success"><?= e($flash) ?></div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <div class="alert alert-error"><?= e($error) ?></div>
    <?php endif; ?>

    <?php
    if (!empty($template)) {
        include TEMPLATE_PATH . '/' . $template . '.php';
    }
    ?>
</main>

<script src="<?= base_url('/assets/js/admin.js') ?>"></script>
</body>
</html>
