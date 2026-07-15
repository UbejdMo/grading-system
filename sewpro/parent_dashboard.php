<?php
require_once __DIR__ . '/includes/layout.php';
require_role('parent');

page_header(t('parent.title'));
?>
<h1 style="text-align: center;"><?= e(t('parent.title')) ?></h1>
<p class="muted" style="text-align: center;"><?= e(t('parent.intro')) ?></p>
<div class="dashboard-grid">
    <a class="dashboard-tile" href="raporti_ditor.php">
        <span class="material-symbols-outlined">today</span>
        <?= e(t('parent.daily')) ?>
    </a>
    <a class="dashboard-tile" href="final_gradesP.php">
        <span class="material-symbols-outlined">calendar_month</span>
        <?= e(t('parent.final')) ?>
    </a>
</div>
<?php page_footer(); ?>
