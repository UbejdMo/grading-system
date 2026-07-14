<?php
require_once __DIR__ . '/../db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
    session_start();
}

/**
 * Lejon qasjen vetëm për rolin e dhënë, përndryshe ridrejton te kyçja.
 */
function require_role(string $role): void
{
    if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== $role) {
        header('Location: login.php');
        exit();
    }
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
