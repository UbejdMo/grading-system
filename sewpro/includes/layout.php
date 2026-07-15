<?php
require_once __DIR__ . '/auth.php';

/**
 * Koka e përbashkët e faqeve me shiritin e navigimit.
 *
 * @param string $title Titulli i faqes
 * @param array  $nav   Lista e linqeve: [['href' => ..., 'icon' => ..., 'label' => ...], ...]
 *                      Linku "Çkyçu" shtohet automatikisht.
 */
function page_header(string $title, array $nav = []): void
{
    $nav[] = ['href' => 'logout.php', 'icon' => 'logout', 'label' => t('nav.logout')];
    ?>
<!DOCTYPE html>
<html lang="<?= e(current_lang()) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<header class="topbar">
    <div class="topbar-inner">
        <span class="brand"><span class="material-symbols-outlined">school</span> SEW</span>
        <nav class="menu">
            <?php if (!empty($GLOBALS['session_ends_at'])): ?>
                <span class="session-timer" id="session-timer" hidden>
                    <span class="material-symbols-outlined">schedule</span>
                    <span id="session-timer-text"></span>
                </span>
            <?php endif; ?>
            <?php foreach ($nav as $item): ?>
                <a href="<?= e($item['href']) ?>">
                    <span class="material-symbols-outlined icons"><?= e($item['icon']) ?></span>
                    <span><?= e($item['label']) ?></span>
                </a>
            <?php endforeach; ?>
            <?= lang_switch_html() ?>
        </nav>
    </div>
</header>
<?php if (!empty($GLOBALS['session_ends_at'])):
    // Sekondat e mbetura deri në mbylljen e orarit - llogaritet nga serveri
    $remaining = max(0, strtotime(date('Y-m-d ') . $GLOBALS['session_ends_at']) - time());
?>
<script>
    (function () {
        let remaining = <?= (int) $remaining ?>;
        const chip = document.getElementById('session-timer');
        const text = document.getElementById('session-timer-text');
        const label = <?= json_encode(t('timer.closes')) ?>;

        function tick() {
            if (remaining <= 0) {
                window.location.href = 'logout.php?expired=1';
                return;
            }
            // Numëratori shfaqet vetëm në orën e fundit
            if (remaining <= 3600 && chip) {
                chip.hidden = false;
                const m = Math.floor(remaining / 60);
                const s = remaining % 60;
                text.textContent = label + ' ' + m + ':' + String(s).padStart(2, '0');
                if (remaining <= 300) {
                    chip.classList.add('urgent');
                }
            }
            remaining--;
            setTimeout(tick, 1000);
        }
        tick();
    })();
</script>
<?php endif; ?>
<main class="page">
    <?php
}

function page_footer(): void
{
    ?>
</main>
<footer class="footer">
    <p>&copy; <?= date('Y') ?> <?= e(t('footer.rights')) ?></p>
</footer>
</body>
</html>
    <?php
}
