<?php
require_once __DIR__ . '/includes/auth.php';

$dashboards = [
    'admin' => 'admin_dashboard.php',
    'teacher' => 'teacher_dashboard.php',
    'student' => 'student_dashboard.php',
    'parent' => 'parent_dashboard.php',
];

// Llogaritë demo që shfaqen në faqen e kyçjes (për prezantim publik)
$demo_accounts = [
    ['role' => 'role.admin', 'icon' => 'shield_person', 'username' => 'admin', 'password' => '123'],
    ['role' => 'role.teacher', 'icon' => 'co_present', 'username' => 'Qemajl', 'password' => '123'],
    ['role' => 'role.student', 'icon' => 'backpack', 'username' => 'arber', 'password' => '123'],
    ['role' => 'role.parent', 'icon' => 'family_restroom', 'username' => 'Shaban', 'password' => '123'],
];

// Nëse është i kyçur tashmë, dërgoje te paneli i vet
if (isset($_SESSION['user_id']) && isset($dashboards[$_SESSION['role'] ?? ''])) {
    header('Location: ' . $dashboards[$_SESSION['role']]);
    exit();
}

$error = null;

// Sesioni u mbyll automatikisht sepse orari i kyçjes përfundoi
if (isset($_GET['expired'])) {
    $error = t('login.expired');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember_me = isset($_POST['remember_me']);

    $stmt = $conn->prepare('SELECT user_id, username, password, role FROM users WHERE username = ?');
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    // Pranon hash-e moderne; fjalëkalimet e vjetra (tekst i thjeshtë) i
    // verifikon një herë të fundit dhe i konverton menjëherë në hash.
    $password_ok = false;
    if ($user) {
        if (password_verify($password, $user['password'])) {
            $password_ok = true;
            if (password_needs_rehash($user['password'], PASSWORD_DEFAULT)) {
                $new_hash = password_hash($password, PASSWORD_DEFAULT);
                $upd = $conn->prepare('UPDATE users SET password = ? WHERE user_id = ?');
                $upd->bind_param('si', $new_hash, $user['user_id']);
                $upd->execute();
            }
        } elseif (hash_equals($user['password'], $password)) {
            $password_ok = true;
            $new_hash = password_hash($password, PASSWORD_DEFAULT);
            $upd = $conn->prepare('UPDATE users SET password = ? WHERE user_id = ?');
            $upd->bind_param('si', $new_hash, $user['user_id']);
            $upd->execute();
        }
    }

    if (!$password_ok) {
        // Mesazh i njëjtë për të dyja rastet - nuk zbulojmë se cili ishte gabim
        $error = t('login.err_credentials');
    } else {
        // Kontrolli i orarit të kyçjes për rolin - PARA se të krijohet sesioni.
        // E njëjta logjikë (role_time_window) përdoret edhe gjatë sesionit.
        $window = role_time_window($conn, $user['role']);
        $day_shqip = t('day.' . date('l'));

        if (!$window['allowed'] && $window['code'] === 'day') {
            $error = sprintf(t('login.err_day'), $day_shqip);
        } elseif (!$window['allowed'] && $window['code'] === 'before') {
            $error = sprintf(t('login.err_before'), $day_shqip, substr($window['starts_at'], 0, 5));
        } elseif (!$window['allowed']) {
            $error = sprintf(t('login.err_after'), $day_shqip, substr($window['ends_at'], 0, 5));
        } else {
            // Gjithçka në rregull - krijo sesionin e ri
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int) $user['user_id'];
            $_SESSION['role'] = $user['role'];

            // "Ruaj llogarinë" ruan vetëm emrin e përdoruesit (kurrë fjalëkalimin)
            if ($remember_me) {
                setcookie('username', $user['username'], time() + 86400 * 30, '/', '', false, true);
            } else {
                setcookie('username', '', time() - 3600, '/');
            }

            header('Location: ' . $dashboards[$user['role']]);
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="<?= e(current_lang()) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(t('login.title')) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body class="login-body">
    <div class="login-wrapper">
        <div class="login-container">
            <div class="login-lang"><?= lang_switch_html() ?></div>
            <h2><?= e(t('login.title')) ?></h2>
            <form action="login.php<?= isset($_GET['lang']) ? '?lang=' . e(current_lang()) : '' ?>" method="POST">
                <?= csrf_field() ?>
                <div class="form-group">
                    <label for="username"><?= e(t('login.username')) ?></label>
                    <input type="text" name="username" id="username" autocomplete="username"
                        value="<?= e($_COOKIE['username'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label for="password"><?= e(t('login.password')) ?></label>
                    <input type="password" name="password" id="password" autocomplete="current-password" required>
                </div>
                <div class="form-group remember-me">
                    <input type="checkbox" name="remember_me" id="remember_me" <?= isset($_COOKIE['username']) ? 'checked' : '' ?>>
                    <label for="remember_me"><?= e(t('login.remember')) ?></label>
                </div>
                <button class="admin-button" type="submit"><?= e(t('login.submit')) ?></button>
            </form>

            <?php if ($error !== null): ?>
                <div class="error"><?= e($error) ?></div>
            <?php endif; ?>

            <div class="demo-accounts">
                <p class="demo-title"><span class="material-symbols-outlined">touch_app</span> <?= e(t('login.demo_title')) ?></p>
                <div class="demo-grid">
                    <?php foreach ($demo_accounts as $acc): ?>
                        <button type="button" class="demo-chip"
                            onclick="fillDemo('<?= e($acc['username']) ?>', '<?= e($acc['password']) ?>')">
                            <span class="material-symbols-outlined"><?= e($acc['icon']) ?></span>
                            <span class="demo-role"><?= e(t($acc['role'])) ?></span>
                            <span class="demo-cred"><?= e($acc['username']) ?> / <?= e($acc['password']) ?></span>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>
            <script>
                function fillDemo(username, password) {
                    document.getElementById('username').value = username;
                    document.getElementById('password').value = password;
                    document.getElementById('password').focus();
                }
            </script>
        </div>
    </div>
</body>
</html>
