<?php
require_once __DIR__ . '/includes/layout.php';
require_role('admin');

page_header('Moduli i Administratorit');
?>
<h1 style="text-align: center;">Moduli i Administratorit</h1>
<div class="dashboard-grid">
    <a class="dashboard-tile" href="manage_users.php">
        <span class="material-symbols-outlined">group</span>
        Menaxho Përdorues
    </a>
    <a class="dashboard-tile" href="manage_classes.php">
        <span class="material-symbols-outlined">school</span>
        Menaxho Klasat
    </a>
    <a class="dashboard-tile" href="manage_time_limits.php">
        <span class="material-symbols-outlined">schedule</span>
        Menaxho Orarin e Kyçjes
    </a>
</div>
<?php page_footer(); ?>
