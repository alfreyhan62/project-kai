<?php

declare(strict_types=1);

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
requireLogin();

$roleId = currentRoleId();
if ($roleId === 1) {
    header('Location: pages/admin/dashboard.php');
    exit;
}
if ($roleId === 2) {
    header('Location: pages/petugas/dashboard.php');
    exit;
}
if ($roleId === 4) {
    header('Location: pages/pimpinan/dashboard.php');
    exit;
}
if (strtolower((string) ($_SESSION['role_name'] ?? '')) === 'visitor') {
    header('Location: pages/visitor/dashboard.php');
    exit;
}
http_response_code(403);
exit('Anda tidak memiliki hak akses ke halaman ini.');
