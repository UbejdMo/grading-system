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
    $nav[] = ['href' => 'logout.php', 'icon' => 'logout', 'label' => 'Çkyçu'];
    ?>
<!DOCTYPE html>
<html lang="sq">
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
            <?php foreach ($nav as $item): ?>
                <a href="<?= e($item['href']) ?>">
                    <span class="material-symbols-outlined icons"><?= e($item['icon']) ?></span>
                    <span><?= e($item['label']) ?></span>
                </a>
            <?php endforeach; ?>
        </nav>
    </div>
</header>
<main class="page">
    <?php
}

function page_footer(): void
{
    ?>
</main>
<footer class="footer">
    <p>&copy; <?= date('Y') ?> Sistemi i Vetëvlerësimit. Të gjitha të drejtat janë të rezervuara.</p>
</footer>
</body>
</html>
    <?php
}
