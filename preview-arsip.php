<?php
declare(strict_types=1);
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
requireRole(1, 2, 4);
$role = currentRoleId();
$unit = (int) ($_SESSION['unit_id'] ?? 0);
$fileId = (int) ($_GET['id'] ?? 0);
$where = 'f.file_id = :file_id';
$params = ['file_id' => $fileId];
if ($role === 2) {
    if (!$unit) {
        http_response_code(403);
        exit('Unit kerja Petugas belum ditetapkan.');
    }
    $where .= ' AND a.unit_id = :unit';
    $params['unit'] = $unit;
}
$statement = $pdo->prepare('SELECT f.nama_file,f.path_file,f.mime_type FROM file_arsip f JOIN arsip a ON a.arsip_id=f.arsip_id WHERE ' . $where);
$statement->execute($params);
$file = $statement->fetch();
$allowedMimes = ['application/pdf', 'image/jpeg', 'image/png'];
if (!$file || !in_array($file['mime_type'], $allowedMimes, true)) {
    http_response_code(403);
    exit('File tidak dapat dipratinjau.');
}
$storageRoot = realpath(__DIR__ . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'uploads');
$path = realpath(__DIR__ . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $file['path_file']));
if ($storageRoot === false || $path === false || strpos($path, $storageRoot . DIRECTORY_SEPARATOR) !== 0 || !is_file($path)) {
    http_response_code(404);
    exit('File tidak ditemukan.');
}
header('Content-Type: ' . $file['mime_type']);
header('Content-Disposition: inline; filename="' . rawurlencode($file['nama_file']) . '"');
header('X-Content-Type-Options: nosniff');
readfile($path);
