<?php
declare(strict_types=1);

function syncArchiveLifecycle(PDO $pdo): void
{
    $pdo->exec("INSERT IGNORE INTO jadwal_pemusnahan (arsip_id, tanggal_jatuh_tempo) SELECT arsip_id, tanggal_jatuh_tempo FROM arsip WHERE status_arsip = 'Musnah' AND tanggal_jatuh_tempo IS NOT NULL");
    $statement = $pdo->prepare("INSERT IGNORE INTO notifikasi (arsip_id, jenis_notifikasi, tanggal_notifikasi) SELECT arsip_id, :jenis, DATE_SUB(tanggal_jatuh_tempo, INTERVAL 1 YEAR) FROM arsip WHERE status_arsip = 'Musnah' AND tanggal_jatuh_tempo IS NOT NULL AND DATE_SUB(tanggal_jatuh_tempo, INTERVAL 1 YEAR) <= CURDATE()");
    $statement->execute(['jenis' => '1 Tahun']);
    $statement = $pdo->prepare("INSERT IGNORE INTO notifikasi (arsip_id, jenis_notifikasi, tanggal_notifikasi) SELECT arsip_id, :jenis, DATE_SUB(tanggal_jatuh_tempo, INTERVAL 1 MONTH) FROM arsip WHERE status_arsip = 'Musnah' AND tanggal_jatuh_tempo IS NOT NULL AND DATE_SUB(tanggal_jatuh_tempo, INTERVAL 1 MONTH) <= CURDATE()");
    $statement->execute(['jenis' => '1 Bulan']);
    $statement = $pdo->prepare("INSERT IGNORE INTO notifikasi (arsip_id, jenis_notifikasi, tanggal_notifikasi) SELECT arsip_id, :jenis, DATE_SUB(tanggal_jatuh_tempo, INTERVAL 7 DAY) FROM arsip WHERE status_arsip = 'Musnah' AND tanggal_jatuh_tempo IS NOT NULL AND DATE_SUB(tanggal_jatuh_tempo, INTERVAL 7 DAY) <= CURDATE()");
    $statement->execute(['jenis' => 'H-7']);
}

function unreadArchiveReminders(PDO $pdo, ?int $unitId = null): array
{
    $sql = 'SELECT n.notifikasi_id, a.no_registrasi, n.jenis_notifikasi, n.tanggal_notifikasi FROM notifikasi n INNER JOIN arsip a ON a.arsip_id = n.arsip_id WHERE n.read_at IS NULL';
    $params = [];
    if ($unitId !== null) {
        $sql .= ' AND a.unit_id = :unit_id';
        $params['unit_id'] = $unitId;
    }
    $sql .= ' ORDER BY n.tanggal_notifikasi DESC';
    $statement = $pdo->prepare($sql);
    $statement->execute($params);
    return $statement->fetchAll();
}
