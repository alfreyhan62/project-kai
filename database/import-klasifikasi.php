<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/includes/klasifikasi.php';

$preview = in_array('--apply', $argv, true) ? false : true;
$parsed = parseKlasifikasiArsip(dirname(__DIR__) . '/arsip_klasifikasi.txt');
$items = $parsed['items'];
$existing = [];
foreach ($pdo->query('SELECT klasifikasi_id,sumber_kunci FROM klasifikasi_arsip WHERE sumber_kunci IS NOT NULL') as $row) $existing[$row['sumber_kunci']] = (int) $row['klasifikasi_id'];
$created = 0;
$updated = 0;
$skipped = 0;
if (!$preview) {
    $pdo->beginTransaction();
    try {
        $upsert = $pdo->prepare('INSERT INTO klasifikasi_arsip(parent_id,tingkat,urutan,sumber_kunci,kode_klasifikasi,nama_klasifikasi,deskripsi) VALUES(NULL,:tingkat,:urutan,:sumber_kunci,:kode_klasifikasi,:nama_klasifikasi,:deskripsi) ON DUPLICATE KEY UPDATE tingkat=VALUES(tingkat),urutan=VALUES(urutan),kode_klasifikasi=VALUES(kode_klasifikasi),nama_klasifikasi=VALUES(nama_klasifikasi),deskripsi=VALUES(deskripsi)');
        foreach ($items as $item) {
            $upsert->execute(array_diff_key($item, ['parent_key' => true]));
            if (isset($existing[$item['sumber_kunci']])) $updated++; else $created++;
        }
        $ids = [];
        foreach ($pdo->query('SELECT klasifikasi_id,sumber_kunci FROM klasifikasi_arsip WHERE sumber_kunci IS NOT NULL') as $row) $ids[$row['sumber_kunci']] = (int) $row['klasifikasi_id'];
        $parent = $pdo->prepare('UPDATE klasifikasi_arsip SET parent_id=:parent_id WHERE sumber_kunci=:sumber_kunci');
        foreach ($items as $item) {
            $parentKey = $item['parent_key'];
            $parentId = $parentKey === null ? null : ($ids[$parentKey] ?? null);
            if ($parentKey !== null && $parentId === null && str_contains($parentKey, '.')) $parentId = $ids[substr($parentKey, 0, 2)] ?? null;
            if ($parentKey !== null && $parentId === null) { $skipped++; continue; }
            $parent->execute(['parent_id' => $parentId, 'sumber_kunci' => $item['sumber_kunci']]);
        }
        $pdo->commit();
    } catch (Throwable $exception) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        throw $exception;
    }
} else {
    foreach ($items as $item) isset($existing[$item['sumber_kunci']]) ? $updated++ : $created++;
}
echo json_encode(['mode' => $preview ? 'preview' : 'apply', 'baris' => $parsed['baris'], 'diproses' => count($items), 'dibuat' => $created, 'diperbarui' => $updated, 'dilewati' => $skipped, 'perlu_diperiksa' => $parsed['review']], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . PHP_EOL;
