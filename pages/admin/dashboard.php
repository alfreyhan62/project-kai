<?php
declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/config/database.php';
require_once dirname(__DIR__, 2) . '/includes/auth.php';
requireAdmin();

$totals = $pdo->query('SELECT COUNT(*) AS total_users, SUM(is_active = 1) AS active_users, SUM(is_active = 0) AS inactive_users FROM users')->fetch();
$latestUsers = $pdo->query('SELECT u.nama_lengkap, u.username, r.role_name, COALESCE(uk.nama_unit, \'-\') AS unit_name, u.is_active, u.created_at FROM users u INNER JOIN roles r ON r.role_id = u.role_id LEFT JOIN unit_kerja uk ON uk.unit_id = u.unit_id ORDER BY u.created_at DESC LIMIT 5')->fetchAll();
require dirname(__DIR__, 2) . '/includes/dashboard-admin-view.php';
