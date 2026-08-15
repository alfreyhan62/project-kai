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

function requireLogin(): void
{
    if (!isLoggedIn()) {
        header('Location: login.php');
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

function dashboardUrl(string $roleName): string
{
    return 'dashboard.php?role=' . rawurlencode(strtolower($roleName));
}

function logActivity(PDO $pdo, int $userId, string $activity): void
{
    try {
        $columns = $pdo->query("SHOW COLUMNS FROM log_aktivitas")->fetchAll(PDO::FETCH_COLUMN);
        $activityColumn = in_array('aktivitas', $columns, true) ? 'aktivitas' : (in_array('activity', $columns, true) ? 'activity' : null);
        if ($activityColumn === null || !in_array('user_id', $columns, true)) {
            return;
        }
        $statement = $pdo->prepare("INSERT INTO log_aktivitas (user_id, {$activityColumn}) VALUES (:user_id, :activity)");
        $statement->execute(['user_id' => $userId, 'activity' => $activity]);
    } catch (PDOException $exception) {
        error_log('Gagal mencatat log aktivitas.');
    }
}
