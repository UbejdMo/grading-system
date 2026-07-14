<?php
require_once __DIR__ . '/includes/layout.php';
require_role('parent');

page_header('Moduli i Prindit');
?>
<h1 style="text-align: center;">Moduli i Prindit</h1>
<p class="muted" style="text-align: center;">Këtu mund të shihni notat e fëmijës suaj (vetëm lexim).</p>
<div class="dashboard-grid">
    <a class="dashboard-tile" href="raporti_ditor.php">
        <span class="material-symbols-outlined">today</span>
        Raporti Ditor
    </a>
    <a class="dashboard-tile" href="final_gradesP.php">
        <span class="material-symbols-outlined">calendar_month</span>
        Notat Përfundimtare
    </a>
</div>
<?php page_footer(); ?>
