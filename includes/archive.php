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

function archiveUploadAllowed(string $name, string $mime, int $size): bool
{
    $allowedTypes = [
        'pdf' => 'application/pdf',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
    ];
    $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));
    return $size > 0 && $size <= 20971520 && isset($allowedTypes[$extension]) && hash_equals($allowedTypes[$extension], $mime);
}

function archiveStoragePath(string $extension): string
{
    $extension = strtolower($extension);
    return 'storage/uploads/' . bin2hex(random_bytes(16)) . '.' . $extension;
}
