<?php
declare(strict_types=1);

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
startSession();

if (isLoggedIn()) {
    header('Location: ' . dashboardUrl((string) $_SESSION['role_name']));
    exit;
}

$error = '';
$registered = isset($_GET['registered']) && $_GET['registered'] === '1';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim((string) ($_POST['username'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        $error = 'Username atau password salah.';
    } else {
        $statement = $pdo->prepare('SELECT u.user_id, u.role_id, u.unit_id, u.username, u.password_hash, u.nama_lengkap, u.is_active, r.role_name FROM users u INNER JOIN roles r ON r.role_id = u.role_id WHERE LOWER(u.username) = LOWER(:username) LIMIT 1');
        $statement->execute(['username' => $username]);
        $user = $statement->fetch();

        if (!$user || !(bool) $user['is_active'] || !password_verify($password, $user['password_hash'])) {
            $error = 'Username atau password salah.';
        } else {
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int) $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
            $_SESSION['role_id'] = (int) $user['role_id'];
            $_SESSION['role_name'] = $user['role_name'];
            $_SESSION['unit_id'] = $user['unit_id'] === null ? null : (int) $user['unit_id'];
            $pdo->prepare('UPDATE users SET last_login = NOW() WHERE user_id = :user_id')->execute(['user_id' => $user['user_id']]);
            logActivity($pdo, (int) $user['user_id'], 'LOGIN');
            header('Location: ' . dashboardUrl($user['role_name']));
            exit;
        }
    }
}

require __DIR__ . '/includes/login-template.php';
