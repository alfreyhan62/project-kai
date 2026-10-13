<?php
declare(strict_types=1);

function archiveScopeCondition(): string
{
    $roleId = currentRoleId();
    if ($roleId === 1 || $roleId === 4) return '1=1';
    if ($roleId === 2) return 'a.unit_id = :scope_unit_id';
    return '1=0';
}

function archiveScopeParams(): array
{
    $roleId = currentRoleId();
    if ($roleId === 2) return ['scope_unit_id' => (int) ($_SESSION['unit_id'] ?? 0)];
    return [];
}

function validArchiveLocation(PDO $pdo, int $unitId, int $rakId): bool
{
    $sql = 'SELECT COUNT(*) FROM rak k INNER JOIN lemari l ON l.lemari_id = k.lemari_id INNER JOIN ruangan r ON r.ruangan_id = l.ruangan_id WHERE k.rak_id = :rak_id AND r.unit_id = :unit_id';
    $statement = $pdo->prepare($sql);
    $statement->execute(['rak_id' => $rakId, 'unit_id' => $unitId]);
    return (int) $statement->fetchColumn() > 0;
}

function archiveLocationId(PDO $pdo, int $unitId, string $ruangan, string $lemari, string $rak): ?int
{
    if ($ruangan === '' && $lemari === '' && $rak === '') return null;
    if ($ruangan === '' || $lemari === '' || $rak === '') throw new InvalidArgumentException('Ruangan, Lemari, dan Rak harus diisi lengkap.');
    $pdo->prepare('INSERT INTO ruangan(unit_id,kode_ruangan,nama_ruangan) VALUES(:unit_id,:kode,:nama) ON DUPLICATE KEY UPDATE nama_ruangan=VALUES(nama_ruangan)')->execute(['unit_id' => $unitId, 'kode' => $ruangan, 'nama' => $ruangan]);
    $findRuangan = $pdo->prepare('SELECT ruangan_id FROM ruangan WHERE unit_id=:unit_id AND kode_ruangan=:kode');
    $findRuangan->execute(['unit_id' => $unitId, 'kode' => $ruangan]);
    $ruanganId = (int) $findRuangan->fetchColumn();
    $pdo->prepare('INSERT INTO lemari(ruangan_id,kode_lemari,nama_lemari) VALUES(:ruangan_id,:kode,:nama) ON DUPLICATE KEY UPDATE nama_lemari=VALUES(nama_lemari)')->execute(['ruangan_id' => $ruanganId, 'kode' => $lemari, 'nama' => $lemari]);
    $findLemari = $pdo->prepare('SELECT lemari_id FROM lemari WHERE ruangan_id=:ruangan_id AND kode_lemari=:kode');
    $findLemari->execute(['ruangan_id' => $ruanganId, 'kode' => $lemari]);
    $lemariId = (int) $findLemari->fetchColumn();
    $pdo->prepare('INSERT INTO rak(lemari_id,kode_rak,nama_rak) VALUES(:lemari_id,:kode,:nama) ON DUPLICATE KEY UPDATE nama_rak=VALUES(nama_rak)')->execute(['lemari_id' => $lemariId, 'kode' => $rak, 'nama' => $rak]);
    $findRak = $pdo->prepare('SELECT rak_id FROM rak WHERE lemari_id=:lemari_id AND kode_rak=:kode');
    $findRak->execute(['lemari_id' => $lemariId, 'kode' => $rak]);
    return (int) $findRak->fetchColumn();
}

function archiveUploadAllowed(string $name, string $mime, int $size): bool
{
    $allowedTypes = [
        'pdf' => 'application/pdf',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'xls' => 'application/vnd.ms-excel',
        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    ];
    $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));
    return $size > 0 && $size <= 20971520 && isset($allowedTypes[$extension]) && hash_equals($allowedTypes[$extension], $mime);
}

function archiveStoragePath(string $extension): string
{
    $extension = strtolower($extension);
    return 'storage/uploads/' . bin2hex(random_bytes(16)) . '.' . $extension;
}
