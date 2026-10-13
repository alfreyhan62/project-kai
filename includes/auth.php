<?php
declare(strict_types=1);

function startSession(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_set_cookie_params([
            'httponly' => true,
            'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
            'samesite' => 'Lax',
        ]);
        session_start();
    }
}

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function isLoggedIn(): bool
{
    startSession();
    return isset($_SESSION['user_id'], $_SESSION['role_id'], $_SESSION['role_name'])
        && (int) $_SESSION['user_id'] > 0
        && (int) $_SESSION['role_id'] > 0;
}

function currentRoleId(): int
{
    return (int) ($_SESSION['role_id'] ?? 0);
}

function profileInitial(string $name): string
{
    $name = $name ?: 'U';
    return function_exists('mb_strtoupper') ? mb_strtoupper(mb_substr($name, 0, 1)) : strtoupper(substr($name, 0, 1));
}

function profileAvatar(string $assetBase, string $name): string
{
    global $pdo;
    $initial = profileInitial($name);
    if (!isset($pdo) || !isLoggedIn()) return e($initial);
    $statement = $pdo->prepare('SELECT updated_at FROM profile_photos WHERE user_id = :user_id');
    $statement->execute(['user_id' => (int) $_SESSION['user_id']]);
    $updatedAt = $statement->fetchColumn();
    if (!$updatedAt) return e($initial);
    $separator = str_contains($assetBase, '?') ? '&' : '?';
    $src = rtrim($assetBase, '/') . '/api/profile-photo.php' . $separator . 't=' . rawurlencode((string) strtotime((string) $updatedAt));
    return '<img src="' . e($src) . '" alt="Foto profil ' . e($name) . '">';
}

function passwordChangeUrl(): string
{
    $script = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? ''));
    $pagesPosition = strpos($script, '/pages/');
    return ($pagesPosition === false ? rtrim(dirname($script), '/') : substr($script, 0, $pagesPosition)) . '/pages/profile/ubah-password.php';
}

function passwordIsExpired(): bool
{
    global $pdo;
    if (!isset($pdo) || !isLoggedIn()) return false;
    $statement = $pdo->prepare('SELECT password_changed_at FROM users WHERE user_id = :user_id AND role_id = :role_id AND is_active = TRUE');
    $statement->execute(['user_id' => (int) $_SESSION['user_id'], 'role_id' => (int) $_SESSION['role_id']]);
    $changedAt = $statement->fetchColumn();
    return !$changedAt || strtotime((string) $changedAt) <= strtotime('-3 months');
}

function requireLogin(): void
{
    if (!isLoggedIn()) {
        $depth = substr_count(trim(str_replace('\\', '/', dirname((string) ($_SERVER['SCRIPT_NAME'] ?? ''))), '/'), '/');
        header('Location: ' . str_repeat('../', $depth) . 'login.php');
        exit;
    }
    $script = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? ''));
    if (!str_ends_with($script, '/pages/profile/ubah-password.php') && passwordIsExpired()) {
        header('Location: ' . passwordChangeUrl());
        exit;
    }
}

function requireRole(int ...$roleIds): void
{
    requireLogin();
    if ($roleIds === [] || !in_array(currentRoleId(), $roleIds, true)) {
        http_response_code(403);
        exit('Anda tidak memiliki hak akses ke halaman ini.');
    }
}

function requireAdmin(): void
{
    requireRole(1);
}

function isVisitor(): bool
{
    requireLogin();
    return strtolower((string) ($_SESSION['role_name'] ?? '')) === 'visitor';
}

function requireVisitor(): void
{
    if (!isVisitor()) {
        http_response_code(403);
        exit('Anda tidak memiliki hak akses ke halaman ini.');
    }
}

function csrfToken(): string
{
    startSession();
    $_SESSION['csrf_token'] ??= bin2hex(random_bytes(32));
    return $_SESSION['csrf_token'];
}

function verifyCsrfToken(): bool
{
    return hash_equals((string) ($_SESSION['csrf_token'] ?? ''), (string) ($_POST['csrf_token'] ?? ''));
}

function logoutForm(string $action): string
{
    return '<form method="post" action="' . e($action) . '" class="logout-form"><input type="hidden" name="csrf_token" value="' . e(csrfToken()) . '"><button type="submit"><i class="material-symbols-outlined">logout</i>Logout</button></form>';
}

function dashboardUrl(string $roleName): string
{
    return strtolower($roleName) === 'admin' ? 'pages/admin/dashboard.php' : 'dashboard.php';
}
