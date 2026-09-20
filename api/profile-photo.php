<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/includes/auth.php';
requireLogin();

$userId = (int) $_SESSION['user_id'];

$statement = $pdo->prepare('SELECT file_path FROM profile_photos WHERE user_id = :user_id');
$statement->execute(['user_id' => $userId]);
$filePath = (string) ($statement->fetchColumn() ?: '');
$storageRoot = realpath(dirname(__DIR__) . '/storage/profile-photos');
$absolutePath = $storageRoot && $filePath ? realpath(dirname(__DIR__) . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $filePath)) : false;
if (!$storageRoot || !$absolutePath || !str_starts_with($absolutePath, $storageRoot . DIRECTORY_SEPARATOR) || !is_file($absolutePath)) {
    http_response_code(404);
    exit;
}

$mime = (new finfo(FILEINFO_MIME_TYPE))->file($absolutePath);
if (!in_array($mime, ['image/jpeg', 'image/png'], true)) {
    http_response_code(404);
    exit;
}

header('Content-Type: ' . $mime);
$size = filesize($absolutePath);
if ($size === false) {
    http_response_code(404);
    exit;
}
header('Content-Length: ' . (string) $size);
header('Content-Disposition: inline; filename="profile-photo"');
header('Cache-Control: private, max-age=0, must-revalidate');
header('X-Content-Type-Options: nosniff');
readfile($absolutePath);
exit;
