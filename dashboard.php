<?php
declare(strict_types=1);

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
requireLogin();

$roleId = currentRoleId();
if ($roleId === 1) {
    $role = (string) $_SESSION['role_name'];
    $requestedRole = strtolower((string) ($_GET['role'] ?? ''));
    if ($requestedRole !== strtolower($role)) {
        header('Location: ' . dashboardUrl($role));
        exit;
    }
    $totals = $pdo->query('SELECT COUNT(*) AS total_users, COALESCE(SUM(is_active = 1), 0) AS active_users, COALESCE(SUM(is_active = 0), 0) AS inactive_users FROM users')->fetch();
    $activityToday = (int) $pdo->query('SELECT COUNT(*) FROM log_aktivitas WHERE DATE(created_at) = CURDATE()')->fetchColumn();
    $latestUsers = $pdo->query('SELECT u.nama_lengkap, u.username, r.role_name, COALESCE(uk.nama_unit, \'-\') AS unit_name, u.is_active, u.created_at FROM users u INNER JOIN roles r ON r.role_id = u.role_id LEFT JOIN unit_kerja uk ON uk.unit_id = u.unit_id ORDER BY u.created_at DESC LIMIT 5')->fetchAll();
    $latestActivities = $pdo->query('SELECT l.aktivitas, l.keterangan, l.created_at, COALESCE(u.nama_lengkap, \'Sistem\') AS nama_lengkap FROM log_aktivitas l LEFT JOIN users u ON u.user_id = l.user_id ORDER BY l.created_at DESC LIMIT 5')->fetchAll();
    $masterTables = ['unit_kerja' => 'Unit Kerja', 'ruangan' => 'Ruangan', 'lemari' => 'Lemari', 'rak' => 'Rak', 'jenis_surat' => 'Jenis Surat', 'klasifikasi_arsip' => 'Klasifikasi Arsip'];
    $masterCounts = [];
    foreach ($masterTables as $table => $label) { $masterCounts[$label] = (int) $pdo->query("SELECT COUNT(*) FROM {$table}")->fetchColumn(); }
    require __DIR__ . '/includes/dashboard-admin-view.php';
    exit;
}
if ($roleId === 2) { header('Location: pages/petugas/dashboard.php'); exit; }
if ($roleId === 4) { header('Location: pages/pimpinan/dashboard.php'); exit; }
if (strtolower((string) ($_SESSION['role_name'] ?? '')) === 'visitor') { header('Location: pages/visitor/dashboard.php'); exit; }
http_response_code(403);
exit('Anda tidak memiliki hak akses ke halaman ini.');
