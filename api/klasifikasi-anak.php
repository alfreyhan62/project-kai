<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/includes/auth.php';
requireRole(2);

$parentId = (int) ($_GET['parent_id'] ?? 0);
if ($parentId < 0) {
    http_response_code(400);
    exit;
}
if ($parentId === 0) {
    $statement = $pdo->query('SELECT klasifikasi_id,kode_klasifikasi,nama_klasifikasi FROM klasifikasi_arsip WHERE parent_id IS NULL ORDER BY urutan,kode_klasifikasi,nama_klasifikasi');
} else {
    $statement = $pdo->prepare('SELECT klasifikasi_id,kode_klasifikasi,nama_klasifikasi FROM klasifikasi_arsip WHERE parent_id=:parent_id ORDER BY urutan,kode_klasifikasi,nama_klasifikasi');
    $statement->execute(['parent_id' => $parentId]);
}
header('Content-Type: application/json; charset=utf-8');
echo json_encode($statement->fetchAll(), JSON_UNESCAPED_UNICODE);
