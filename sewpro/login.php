<?php
require_once __DIR__ . '/includes/auth.php';

$days_in_shqip = [
    'Sunday' => 'e Diel', 'Monday' => 'e Hënë', 'Tuesday' => 'e Martë',
    'Wednesday' => 'e Mërkurë', 'Thursday' => 'e Enjte', 'Friday' => 'e Premte',
    'Saturday' => 'e Shtunë',
];

$dashboards = [
    'admin' => 'admin_dashboard.php',
    'teacher' => 'teacher_dashboard.php',
    'student' => 'student_dashboard.php',
    'parent' => 'parent_dashboard.php',
];

// Nëse është i kyçur tashmë, dërgoje te paneli i vet
if (isset($_SESSION['user_id']) && isset($dashboards[$_SESSION['role'] ?? ''])) {
    header('Location: ' . $dashboards[$_SESSION['role']]);
    exit();
}

$error = null;

// Sesioni u mbyll automatikisht sepse orari i kyçjes përfundoi
if (isset($_GET['expired'])) {
    $error = 'Orari i kyçjes përfundoi dhe sesioni u mbyll automatikisht.';
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
        $error = 'Përdoruesi ose fjalëkalimi është gabim.';
    } else {
        // Kontrolli i orarit të kyçjes për rolin - PARA se të krijohet sesioni.
        // E njëjta logjikë (role_time_window) përdoret edhe gjatë sesionit.
        $window = role_time_window($conn, $user['role']);
        $current_day_shqip = $days_in_shqip[date('l')];

        if (!$window['allowed'] && $window['code'] === 'day') {
            $error = "Ditën $current_day_shqip ju nuk mund të kyçeni.";
        } elseif (!$window['allowed'] && $window['code'] === 'before') {
            $error = "Për ditën $current_day_shqip, orari i kyçjes nuk ka filluar. Orari fillon në orën " . substr($window['starts_at'], 0, 5) . '.';
        } elseif (!$window['allowed']) {
            $error = "Për ditën $current_day_shqip, orari i kyçjes ka kaluar. Orari ka përfunduar në orën " . substr($window['ends_at'], 0, 5) . '.';
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
<html lang="sq">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kyçja në sistem</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="login-body">
    <div class="login-wrapper">
        <div class="login-container">
            <h2>Kyçja në sistem</h2>
            <form action="login.php" method="POST">
                <?= csrf_field() ?>
                <div class="form-group">
                    <label for="username">Përdoruesi:</label>
                    <input type="text" name="username" id="username" autocomplete="username"
                        value="<?= e($_COOKIE['username'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label for="password">Fjalëkalimi:</label>
                    <input type="password" name="password" id="password" autocomplete="current-password" required>
                </div>
                <div class="form-group remember-me">
                    <input type="checkbox" name="remember_me" id="remember_me" <?= isset($_COOKIE['username']) ? 'checked' : '' ?>>
                    <label for="remember_me">Ruaj llogarinë</label>
                </div>
                <button class="admin-button" type="submit">Kyçu</button>
            </form>

            <?php if ($error !== null): ?>
                <div class="error"><?= e($error) ?></div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
