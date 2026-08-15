<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
startSession();

if (isLoggedIn()) {
    header('Location: ' . dashboardUrl((string) $_SESSION['role_name']));
    exit;
}

header('Location: login.php');
exit;
