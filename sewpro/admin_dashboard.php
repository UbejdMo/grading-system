<?php
require_once __DIR__ . '/includes/layout.php';
require_role('admin');

page_header(t('admin.title'));
?>
<h1 style="text-align: center;"><?= e(t('admin.title')) ?></h1>
<div class="dashboard-grid">
    <a class="dashboard-tile" href="manage_users.php">
        <span class="material-symbols-outlined">group</span>
        <?= e(t('admin.users')) ?>
    </a>
    <a class="dashboard-tile" href="manage_classes.php">
        <span class="material-symbols-outlined">school</span>
        <?= e(t('admin.classes')) ?>
    </a>
    <a class="dashboard-tile" href="manage_time_limits.php">
        <span class="material-symbols-outlined">schedule</span>
        <?= e(t('admin.schedule')) ?>
    </a>
</div>
<?php page_footer(); ?>
