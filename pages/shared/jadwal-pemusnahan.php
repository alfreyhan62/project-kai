<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/config/database.php';
require_once dirname(__DIR__, 2) . '/includes/auth.php';
requireRole(1, 2, 4);

$role = currentRoleId();
$unit = (int) ($_SESSION['unit_id'] ?? 0);
if ($role === 2 && !$unit) {
    http_response_code(403);
    exit('Unit kerja belum ditetapkan.');
}

$baseSql = 'FROM view_jadwal_pemusnahan v INNER JOIN arsip a ON a.arsip_id = v.arsip_id WHERE 1=1';
$params = [];
if ($role === 2) {
    $baseSql .= ' AND a.unit_id = :unit_scope';
    $params['unit_scope'] = $unit;
}

$summarySql = 'SELECT COUNT(*) AS total, COALESCE(SUM(v.tanggal_jatuh_tempo BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)), 0) AS segera, COALESCE(SUM(YEAR(v.tanggal_jatuh_tempo) = YEAR(CURDATE()) AND MONTH(v.tanggal_jatuh_tempo) = MONTH(CURDATE())), 0) AS bulan_ini, COALESCE(SUM(YEAR(v.tanggal_jatuh_tempo) = YEAR(CURDATE())), 0) AS tahun_ini ' . $baseSql;
$listSql = 'SELECT v.* ' . $baseSql . ' ORDER BY v.tanggal_jatuh_tempo ASC';

$summaryStmt = $pdo->prepare($summarySql);
$summaryStmt->execute($params);
$summary = $summaryStmt->fetch() ?: ['total' => 0, 'segera' => 0, 'bulan_ini' => 0, 'tahun_ini' => 0];

$listStmt = $pdo->prepare($listSql);
$listStmt->execute($params);
$items = $listStmt->fetchAll();

function schedule_badge(string $status): array
{
    $statusLower = mb_strtolower(trim($status));
    return match (true) {
        str_contains($statusLower, 'musnah') => ['label' => $status ?: 'MUSNAH', 'class' => 'badge-danger'],
        str_contains($statusLower, 'proses') => ['label' => $status ?: 'DALAM PROSES', 'class' => 'badge-info'],
        str_contains($statusLower, 'tempo') => ['label' => $status ?: 'JATUH TEMPO', 'class' => 'badge-warning'],
        str_contains($statusLower, 'akan') => ['label' => $status ?: 'AKAN DIMUSNAHKAN', 'class' => 'badge-primary'],
        default => ['label' => $status !== '' ? $status : '-', 'class' => 'badge-neutral'],
    };
}

function schedule_indicator(?string $date): array
{
    if (!$date) return ['label' => '-', 'class' => 'date-muted'];
    $target = new DateTimeImmutable($date);
    $days = (int) (new DateTimeImmutable('today'))->diff($target)->format('%r%a');
    if ($days < 0) return ['label' => 'Lewat ' . abs($days) . ' hari', 'class' => 'date-overdue'];
    if ($days === 0) return ['label' => 'H-0', 'class' => 'date-danger'];
    if ($days === 1) return ['label' => 'H-1', 'class' => 'date-danger'];
    if ($days <= 7) return ['label' => 'H-' . $days, 'class' => 'date-warning'];
    return ['label' => $target->format('d M Y'), 'class' => 'date-normal'];
}
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<?php require dirname(__DIR__, 2) . '/includes/favicon.php'; ?>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Material+Symbols+Outlined" rel="stylesheet">
<link rel="stylesheet" href="../../assets/css/auth.css">
<title>Jadwal Pemusnahan Arsip</title>
<style>
.destruction-schedule{background:#f4f6fb;color:#12224a}.schedule-page{max-width:1600px;padding:18px 22px 28px}.schedule-hero{margin-bottom:16px;padding:24px;border:1px solid #e6ebf3;border-radius:16px;box-shadow:0 1px 2px #0f172a0a,0 10px 24px #0f172a06}.schedule-hero .breadcrumb{margin:0 0 9px;color:#7182a1;font-size:11px}.schedule-hero h1{margin:0 0 6px;color:#12224a;font-size:24px}.schedule-hero p{margin:0;color:#5f6d86;font-size:13px}.schedule-stats{grid-template-columns:repeat(4,minmax(0,1fr));gap:14px;margin-bottom:16px}.schedule-stats article,.schedule-table-panel{border:1px solid #e6ebf3;border-radius:16px;box-shadow:0 1px 2px #0f172a0a,0 10px 24px #0f172a06}.schedule-stats article{padding:18px}.schedule-stats i{width:54px;height:54px;border-radius:14px;background:#eaf1ff;color:#2563eb}.schedule-stats article:nth-child(2) i{background:#fff2e6;color:#f97316}.schedule-stats article:nth-child(3) i{background:#e9f7ef;color:#16a34a}.schedule-stats article:nth-child(4) i{background:#f0eaff;color:#7c3aed}.schedule-stats small{color:#7182a1}.schedule-stats span{color:#64748b}.schedule-table-panel{overflow:hidden}.schedule-table th{background:#07306b;color:#fff;font-size:10px;letter-spacing:.04em}.schedule-table td{padding:15px 16px;color:#334155;vertical-align:middle}.schedule-table tbody tr{transition:background .15s ease}.schedule-table tbody tr:hover{background:#f8faff}.schedule-date,.schedule-badge{display:inline-flex;align-items:center;min-height:27px;padding:4px 9px;border-radius:20px;font-size:10px;font-weight:700;white-space:nowrap}.date-normal{color:#17669c;background:#e8f4fb}.date-warning{color:#b45309;background:#fff4df}.date-danger,.date-overdue{color:#c9282d;background:#fff0f0}.date-muted{color:#64748b;background:#f1f5f9}.badge-primary{color:#174cc5;background:#edf3ff}.badge-warning{color:#b45309;background:#fff4df}.badge-danger{color:#c9282d;background:#fff0f0}.badge-info{color:#17669c;background:#e8f4fb}.badge-neutral{color:#475569;background:#f1f5f9}.schedule-table .archive-detail-link{padding:6px 8px;border-radius:7px;background:#edf3ff}.schedule-table .archive-empty{padding:62px 20px}.schedule-table .archive-empty h3{font-size:16px}@media(max-width:760px){.schedule-page{padding:16px}.schedule-hero{padding:19px}.schedule-hero h1{font-size:20px}.schedule-stats{grid-template-columns:1fr;gap:12px}.schedule-table td{white-space:normal}.schedule-table th,.schedule-table td{min-width:130px}.schedule-table td:nth-child(3){min-width:220px}}
</style>
</head>
<body class="dashboard-body destruction-schedule">
<main class="admin-content schedule-page">
    <section class="schedule-hero panel">
        <div>
            <nav class="breadcrumb" aria-label="breadcrumb">Dashboard / Jadwal Pemusnahan</nav>
            <h1>Jadwal Pemusnahan Arsip</h1>
            <p>Monitoring arsip yang telah memasuki atau mendekati masa pemusnahan.</p>
        </div>
    </section>
    <section class="schedule-stats stat-grid">
        <article><i class="material-symbols-outlined">event_note</i><small>Total Jadwal</small><strong><?= e((string) $summary['total']) ?></strong><span>Seluruh jadwal</span></article>
        <article><i class="material-symbols-outlined">warning</i><small>Segera Dimusnahkan</small><strong><?= e((string) $summary['segera']) ?></strong><span>Dalam waktu dekat</span></article>
        <article><i class="material-symbols-outlined">calendar_month</i><small>Bulan Ini</small><strong><?= e((string) $summary['bulan_ini']) ?></strong><span>Jadwal bulan ini</span></article>
        <article><i class="material-symbols-outlined">event_available</i><small>Tahun Ini</small><strong><?= e((string) $summary['tahun_ini']) ?></strong><span>Jadwal tahun ini</span></article>
    </section>
    <section class="panel schedule-table-panel">
        <div class="table-wrap schedule-table-wrap">
            <table class="schedule-table">
                <thead><tr><th>No Registrasi</th><th>No Surat</th><th>Perihal</th><th>Unit</th><th>Jatuh Tempo</th><th>Status</th><th>Aksi</th></tr></thead>
                <tbody>
                <?php if ($items): ?>
                    <?php foreach ($items as $x): ?>
                        <?php $badge = schedule_badge((string) ($x['status_jadwal'] ?? '')); $indicator = schedule_indicator($x['tanggal_jatuh_tempo'] ?? null); ?>
                        <tr><td><?= e((string) ($x['no_registrasi'] ?? '-')) ?></td><td><?= e((string) ($x['no_surat'] ?? '-')) ?></td><td><?= e((string) ($x['perihal'] ?? '-')) ?></td><td><?= e((string) ($x['nama_unit'] ?? '-')) ?></td><td><span class="schedule-date <?= e($indicator['class']) ?>"><?= e($indicator['label']) ?></span></td><td><span class="schedule-badge <?= e($badge['class']) ?>"><?= e($badge['label']) ?></span></td><td><a class="archive-detail-link" href="../../api/arsip-detail.php?id=<?= e((string) ($x['arsip_id'] ?? 0)) ?>"><i class="material-symbols-outlined">visibility</i>Lihat Detail</a></td></tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="7"><div class="archive-empty"><i class="material-symbols-outlined">event_busy</i><h3>Belum Ada Jadwal Pemusnahan</h3><p>Belum terdapat arsip yang masuk ke daftar jadwal pemusnahan.</p></div></td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>
</body>
</html>
