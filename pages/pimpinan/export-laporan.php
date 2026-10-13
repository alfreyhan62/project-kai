<?php
declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/config/database.php';
require_once dirname(__DIR__, 2) . '/includes/auth.php';
require_once dirname(__DIR__, 2) . '/includes/xlsx.php';
require_once dirname(__DIR__, 2) . '/includes/pimpinan-report.php';
requireRole(4);

extract(pimpinanReportData($pdo, $_GET));
$period = ($from !== '' ? date('d-m-Y', strtotime($from)) : 'Awal data') . ' s.d. ' . ($to !== '' ? date('d-m-Y', strtotime($to)) : 'Saat ini');
$printedAt = date('d-m-Y H:i');
$reportHeader = [['PT KERETA API INDONESIA (PERSERO)', 2], ['LAPORAN PENGELOLAAN ARSIP', 2], ['Periode: ' . $period, 0], ['Tanggal cetak/export: ' . $printedAt, 0], ['Role: Pimpinan', 0], ['', 0]];
$unitSummary = [];
foreach ($archives as $archive) {
    $unit = $archive['nama_unit'];
    $unitSummary[$unit] ??= ['nama_unit' => $unit, 'jumlah' => 0, 'aktif' => 0, 'permanen' => 0, 'musnah' => 0];
    $unitSummary[$unit]['jumlah']++;
    $key = strtolower((string) $archive['status_arsip']);
    if (isset($unitSummary[$unit][$key])) $unitSummary[$unit][$key]++;
}
ksort($unitSummary);

$summaryRows = $reportHeader;
foreach ([['Total Arsip', $summary['total']], ['Arsip Inaktif', $summary['aktif']], ['Arsip Permanen', $summary['permanen']], ['Arsip Musnah', $summary['musnah']], ['Total Unit', count($unitSummary)]] as $item) $summaryRows[] = [[$item[0], 3], [$item[1], 3, true]];
$archiveRows = $reportHeader;
$archiveRows[] = array_map(static fn(string $value): array => [$value, 1], ['No', 'No Registrasi', 'No Surat', 'Perihal', 'Tanggal Surat', 'Unit', 'Jenis Surat', 'Klasifikasi', 'Status Arsip', 'Rak', 'Masa Simpan', 'Tanggal Jatuh Tempo']);
foreach ($archives as $index => $archive) $archiveRows[] = [[(string) ($index + 1), 3, true], [$archive['no_registrasi'], 3], [$archive['no_surat'], 3], [$archive['perihal'], 3], [$archive['tanggal_surat'] ? date('d-m-Y', strtotime($archive['tanggal_surat'])) : '-', 3], [$archive['nama_unit'], 3], [$archive['nama_jenis'], 3], [$archive['nama_klasifikasi'], 3], [$archive['status_arsip'], 3], [$archive['rak'], 3], [$archive['masa_simpan_tahun'] === null ? '-' : $archive['masa_simpan_tahun'] . ' tahun', 3], [$archive['tanggal_jatuh_tempo'] ? date('d-m-Y', strtotime($archive['tanggal_jatuh_tempo'])) : '-', 3]];
$retentionRows = $reportHeader;
$retentionRows[] = array_map(static fn(string $value): array => [$value, 1], ['No', 'No Registrasi', 'Perihal', 'Unit', 'Status Arsip', 'Tanggal Mulai Retensi', 'Tanggal Jatuh Tempo', 'Status Retensi']);
$retentionIndex = 0;
foreach ($archives as $archive) {
    if (!$archive['tanggal_jatuh_tempo']) continue;
    $due = strtotime($archive['tanggal_jatuh_tempo']); $today = strtotime('today');
    $retentionStatus = $archive['status_arsip'] === 'Musnah' ? 'Sudah dimusnahkan' : ($due < $today ? 'Siap ditindaklanjuti' : ($due === $today ? 'Jatuh tempo' : ($due <= strtotime('+30 days') ? 'Mendekati jatuh tempo' : 'Belum jatuh tempo')));
    $retentionRows[] = [[(string) (++$retentionIndex), 3, true], [$archive['no_registrasi'], 3], [$archive['perihal'], 3], [$archive['nama_unit'], 3], [$archive['status_arsip'], 3], [$archive['tanggal_mulai_retensi'] ? date('d-m-Y', strtotime($archive['tanggal_mulai_retensi'])) : '-', 3], [date('d-m-Y', $due), 3], [$retentionStatus, 3]];
}
$unitRows = $reportHeader;
$unitRows[] = array_map(static fn(string $value): array => [$value, 1], ['Unit', 'Jumlah Arsip', 'Inaktif', 'Permanen', 'Musnah']);
foreach ($unitSummary as $unit) $unitRows[] = [[$unit['nama_unit'], 3], [$unit['jumlah'], 3, true], [$unit['aktif'], 3, true], [$unit['permanen'], 3, true], [$unit['musnah'], 3, true]];
xlsxDownload('Laporan_Arsip_Pimpinan_' . date('Y-m-d_His') . '.xlsx', [['name' => 'Ringkasan', 'rows' => $summaryRows, 'widths' => [32, 20], 'headers' => [1], 'landscape' => false], ['name' => 'Daftar Arsip', 'rows' => $archiveRows, 'widths' => [7, 18, 18, 42, 15, 24, 22, 24, 16, 14, 14, 18], 'headers' => [1, 2, 3, 4, 5, 6, 7], 'landscape' => true], ['name' => 'Retensi & Pemusnahan', 'rows' => $retentionRows, 'widths' => [7, 18, 42, 24, 16, 20, 20, 25], 'headers' => [1, 2, 3, 4, 5, 6, 7], 'landscape' => true], ['name' => 'Rekap Per Unit', 'rows' => $unitRows, 'widths' => [32, 17, 14, 14, 14], 'headers' => [1, 2, 3, 4, 5, 6, 7], 'landscape' => false]]);
