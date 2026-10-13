<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/includes/auth.php';
startSession();

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id'], $_SESSION['role_id'], $_SESSION['role_name']) || (int) $_SESSION['user_id'] <= 0 || (int) $_SESSION['role_id'] !== 3 || strtolower((string) $_SESSION['role_name']) !== 'visitor') {
    http_response_code(403);
    echo json_encode(['message' => 'Sesi Visitor tidak valid. Silakan masuk kembali.']);
    exit;
}

$month = (string) ($_GET['month'] ?? '');
$monthDate = DateTimeImmutable::createFromFormat('!Y-m', $month);
if (!preg_match('/^\d{4}-\d{2}$/', $month) || !$monthDate || $monthDate->format('Y-m') !== $month) {
    http_response_code(422);
    echo json_encode(['message' => 'Bulan kalender tidak valid.']);
    exit;
}

try {
    $from = $month . '-01';
    $to = (new DateTimeImmutable($from))->modify('+1 month')->format('Y-m-d');
    $statement = $pdo->prepare("SELECT tanggal_surat,status_arsip,COUNT(*) AS jumlah FROM arsip WHERE tanggal_surat >= :from AND tanggal_surat < :to AND status_arsip IN ('Inaktif','Permanen','Musnah') GROUP BY tanggal_surat,status_arsip");
    $statement->execute(['from' => $from, 'to' => $to]);
    $days = [];
    foreach ($statement->fetchAll() as $row) {
        $date = (string) $row['tanggal_surat'];
        $status = ucfirst(strtolower((string) $row['status_arsip']));
        $days[$date]['total'] = ($days[$date]['total'] ?? 0) + (int) $row['jumlah'];
        $days[$date]['statuses'][$status] = (int) $row['jumlah'];
    }
    echo json_encode(['days' => $days], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
} catch (Throwable $exception) {
    http_response_code(500);
    echo json_encode(['message' => 'Data kalender belum dapat dimuat. Silakan coba lagi.']);
}
