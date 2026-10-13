<?php
declare(strict_types=1);

function parseKlasifikasiArsip(string $path): array
{
    if (!is_file($path) || !is_readable($path)) throw new RuntimeException('File klasifikasi tidak tersedia atau tidak dapat dibaca.');
    $lines = file($path, FILE_IGNORE_NEW_LINES);
    if ($lines === false) throw new RuntimeException('File klasifikasi gagal dibaca.');
    $items = [];
    $current = null;
    $detailParent = null;
    $details = [];
    $review = [];
    foreach ($lines as $lineNumber => $line) {
        $line = trim(preg_replace('/\s+/u', ' ', $line) ?? '');
        if ($line === '') continue;
        if (preg_match('/^([A-Z]{2}(?:\.\d{3})?)\s*(?:-|—|=)?\s*(.+)$/u', $line, $match)) {
            if ($current !== null) $items[$current]['nama_klasifikasi'] .= $details ? '' : '';
            $code = $match[1];
            $parentKey = null;
            $level = 1;
            if (str_contains($code, '.')) {
                $root = substr($code, 0, 2);
                $number = (int) substr($code, 3);
                $parentKey = $number % 100 === 0 ? $root : $root . '.' . (intdiv($number, 100) * 100);
                $level = $number % 100 === 0 ? 2 : 3;
            }
            $items[$code] = ['sumber_kunci' => $code, 'kode_klasifikasi' => $code, 'nama_klasifikasi' => $match[2], 'deskripsi' => null, 'parent_key' => $parentKey, 'tingkat' => $level, 'urutan' => count($items) + 1];
            $current = $code;
            $detailParent = $code;
            $details = [];
            continue;
        }
        if (preg_match('/^([a-zA-Z])\.|^\|\./u', $line, $match) && $detailParent !== null) {
            $letter = strtolower($match[1] ?? 'l');
            $key = $detailParent . '#' . $letter;
            $items[$key] = ['sumber_kunci' => $key, 'kode_klasifikasi' => null, 'nama_klasifikasi' => preg_replace('/^[a-zA-Z|]\.?\s*/u', '', $line), 'deskripsi' => null, 'parent_key' => $detailParent, 'tingkat' => $items[$detailParent]['tingkat'] + 1, 'urutan' => count($items) + 1];
            $current = $key;
            $details = [];
            continue;
        }
        if (preg_match('/^K\)\.202\b/u', $line)) {
            $review[] = ['baris' => $lineNumber + 1, 'isi' => $line, 'alasan' => 'Kode sumber tidak konsisten'];
            continue;
        }
        if (preg_match('/^[A-Z][A-Z ]+$/u', $line)) {
            $current = null;
            $detailParent = null;
            $review[] = ['baris' => $lineNumber + 1, 'isi' => $line, 'alasan' => 'Judul bagian tanpa kode'];
            continue;
        }
        if ($current === null) {
            $review[] = ['baris' => $lineNumber + 1, 'isi' => $line, 'alasan' => 'Baris tanpa klasifikasi induk'];
            continue;
        }
        $items[$current]['nama_klasifikasi'] .= ' ' . $line;
    }
    return ['items' => array_values($items), 'review' => $review, 'baris' => count($lines)];
}

function klasifikasiPathValid(PDO $pdo, array $path): bool
{
    $path = array_values(array_filter(array_map('intval', $path), static fn(int $id): bool => $id > 0));
    if (!$path) return true;
    $statement = $pdo->prepare('SELECT klasifikasi_id,parent_id FROM klasifikasi_arsip WHERE klasifikasi_id=:id');
    $parent = null;
    foreach ($path as $id) {
        $statement->execute(['id' => $id]);
        $row = $statement->fetch();
        if (!$row || ($parent !== null && (int) $row['parent_id'] !== $parent)) return false;
        if ($parent === null && $row['parent_id'] !== null) return false;
        $parent = (int) $row['klasifikasi_id'];
    }
    return true;
}
