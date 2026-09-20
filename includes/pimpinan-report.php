<?php
declare(strict_types=1);

function pimpinanReportData(PDO $pdo, array $input): array
{
    $q = trim((string) ($input['q'] ?? ''));
    $unitId = (int) ($input['unit_id'] ?? 0);
    $typeId = (int) ($input['jenis_surat_id'] ?? 0);
    $classId = (int) ($input['klasifikasi_id'] ?? 0);
    $status = trim((string) ($input['status'] ?? ''));
    $from = trim((string) ($input['from'] ?? ''));
    $to = trim((string) ($input['to'] ?? ''));
    if (($from !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $from)) || ($to !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $to))) {
        http_response_code(422);
        exit('Periode laporan tidak valid.');
    }
    if ($from !== '' && $to !== '' && $from > $to) {
        http_response_code(422);
        exit('Tanggal awal tidak boleh melebihi tanggal akhir.');
    }

    $where = ['(a.no_registrasi LIKE :q1 OR a.no_surat LIKE :q2 OR a.perihal LIKE :q3)'];
    $params = ['q1' => "%{$q}%", 'q2' => "%{$q}%", 'q3' => "%{$q}%"];
    foreach (['unit_id' => $unitId, 'jenis_surat_id' => $typeId, 'klasifikasi_id' => $classId] as $column => $value) {
        if ($value > 0) {
            $where[] = "a.{$column} = :{$column}";
            $params[$column] = $value;
        }
    }
    if (in_array($status, ['Aktif', 'Permanen', 'Musnah'], true)) {
        $where[] = 'a.status_arsip = :status';
        $params['status'] = $status;
    }
    if ($from !== '') { $where[] = 'a.tanggal_surat >= :from'; $params['from'] = $from; }
    if ($to !== '') { $where[] = 'a.tanggal_surat <= :to'; $params['to'] = $to; }
    $filter = implode(' AND ', $where);
    $statement = $pdo->prepare("SELECT a.arsip_id,a.no_registrasi,a.no_surat,a.perihal,a.tanggal_surat,a.status_arsip,a.masa_simpan_tahun,a.tanggal_mulai_retensi,a.tanggal_jatuh_tempo,u.nama_unit,j.nama_jenis,COALESCE(k.nama_klasifikasi, '-') nama_klasifikasi,COALESCE(r.kode_rak, r.nama_rak, '-') rak FROM arsip a INNER JOIN unit_kerja u ON u.unit_id=a.unit_id INNER JOIN jenis_surat j ON j.jenis_surat_id=a.jenis_surat_id LEFT JOIN klasifikasi_arsip k ON k.klasifikasi_id=a.klasifikasi_id LEFT JOIN rak r ON r.rak_id=a.rak_id WHERE {$filter} ORDER BY a.created_at DESC");
    $statement->execute($params);
    $archives = $statement->fetchAll();
    $summary = ['total' => count($archives), 'aktif' => 0, 'permanen' => 0, 'musnah' => 0];
    foreach ($archives as $archive) { $key = strtolower((string) $archive['status_arsip']); if (isset($summary[$key])) $summary[$key]++; }

    $labels = ['unit' => 'Semua Unit', 'type' => 'Semua Jenis Surat', 'class' => 'Semua Klasifikasi'];
    foreach ([['unit_kerja', 'unit_id', 'nama_unit', $unitId, 'unit'], ['jenis_surat', 'jenis_surat_id', 'nama_jenis', $typeId, 'type'], ['klasifikasi_arsip', 'klasifikasi_id', 'nama_klasifikasi', $classId, 'class']] as [$table, $id, $name, $value, $label]) {
        if ($value > 0) { $lookup = $pdo->prepare("SELECT {$name} FROM {$table} WHERE {$id} = :id"); $lookup->execute(['id' => $value]); $labels[$label] = (string) ($lookup->fetchColumn() ?: $labels[$label]); }
    }
    return compact('q', 'unitId', 'typeId', 'classId', 'status', 'from', 'to', 'archives', 'summary', 'labels');
}
