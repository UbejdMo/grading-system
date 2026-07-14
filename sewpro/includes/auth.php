<?php
require_once __DIR__ . '/../db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
    session_start();
}

/**
 * Kontrollon orarin e lejuar të kyçjes për një rol në këtë moment.
 *
 * @return array ['allowed' => bool, 'code' => 'day'|'before'|'after'|null,
 *                'ends_at' => 'HH:MM:SS'|null, 'starts_at' => 'HH:MM:SS'|null]
 */
function role_time_window(mysqli $conn, string $role): array
{
    $stmt = $conn->prepare('SELECT days, start_time, end_time FROM user_time_limits WHERE role = ?');
    $stmt->bind_param('s', $role);
    $stmt->execute();
    $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    $today = date('l');
    $now = date('H:i:s');

    // Vetëm rregullat që vlejnë për ditën e sotme
    $today_rows = array_filter($rows, function ($row) use ($today) {
        return in_array($today, array_map('trim', explode(',', $row['days'])), true);
    });

    if (!$today_rows) {
        return ['allowed' => false, 'code' => 'day', 'ends_at' => null, 'starts_at' => null];
    }

    // Brenda ndonjë dritareje? Merr fundin më të vonë të dritareve aktive.
    $ends_at = null;
    foreach ($today_rows as $row) {
        if ($now >= $row['start_time'] && $now <= $row['end_time']) {
            if ($ends_at === null || $row['end_time'] > $ends_at) {
                $ends_at = $row['end_time'];
            }
        }
    }
    if ($ends_at !== null) {
        return ['allowed' => true, 'code' => null, 'ends_at' => $ends_at, 'starts_at' => null];
    }

    // Jashtë orarit: para fillimit të radhës apo pas përfundimit?
    $next_start = null;
    $last_end = null;
    foreach ($today_rows as $row) {
        if ($row['start_time'] > $now && ($next_start === null || $row['start_time'] < $next_start)) {
            $next_start = $row['start_time'];
        }
        if ($last_end === null || $row['end_time'] > $last_end) {
            $last_end = $row['end_time'];
        }
    }
    if ($next_start !== null) {
        return ['allowed' => false, 'code' => 'before', 'ends_at' => null, 'starts_at' => $next_start];
    }
    return ['allowed' => false, 'code' => 'after', 'ends_at' => $last_end, 'starts_at' => null];
}

/**
 * Lejon qasjen vetëm për rolin e dhënë dhe vetëm brenda orarit të kyçjes.
 * Kur orari përfundon, sesioni mbyllet automatikisht në kërkesën e radhës.
 */
function require_role(string $role): void
{
    global $conn;

    if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== $role) {
        header('Location: login.php');
        exit();
    }

    $window = role_time_window($conn, $role);
    if (!$window['allowed']) {
        $_SESSION = [];
        session_destroy();
        header('Location: login.php?expired=1');
        exit();
    }

    // Layout-i e përdor për numëratorin e çkyçjes automatike
    $GLOBALS['session_ends_at'] = $window['ends_at'];
}

function current_user_id(): int
{
    return (int) $_SESSION['user_id'];
}

/** Escape për HTML - përdoret në çdo output. */
function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/** Fusha e fshehur për formularët POST. */
function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

/** Verifikon token-in CSRF për çdo kërkesë POST. */
function csrf_check(): void
{
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
        http_response_code(403);
        exit('Kërkesa u refuzua për arsye sigurie. Rifreskoni faqen dhe provoni përsëri.');
    }
}
