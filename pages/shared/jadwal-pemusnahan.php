<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/config/database.php';
require_once dirname(__DIR__, 2) . '/includes/auth.php';
require_once dirname(__DIR__, 2) . '/includes/reminder.php';
requireRole(1, 2, 4);

$role = currentRoleId();
$dashboardHref = match ($role) { 2 => '../petugas/dashboard.php', 4 => '../pimpinan/dashboard.php', default => '../../dashboard.php' };
$profileName = (string) ($_SESSION['nama_lengkap'] ?? $_SESSION['username'] ?? 'User');
$profileRole = (string) ($_SESSION['role_name'] ?? 'Petugas');
$isPetugas = $role === 2;
$unit = (int) ($_SESSION['unit_id'] ?? 0);
if ($role === 2 && !$unit) {
    http_response_code(403);
    exit('Unit kerja belum ditetapkan.');
}

syncArchiveLifecycle($pdo);
$baseSql = "FROM view_jadwal_pemusnahan v INNER JOIN arsip a ON a.arsip_id = v.arsip_id WHERE a.status_arsip NOT IN ('Permanen', 'Musnah') AND v.status_jadwal NOT IN ('Sudah Dimusnahkan', 'Dibatalkan')";
$params = [];
if ($role === 2) {
    $baseSql .= ' AND a.unit_id = :unit_scope';
    $params['unit_scope'] = $unit;
}

$summarySql = "SELECT COUNT(*) AS total, SUM(v.tanggal_jatuh_tempo > CURRENT_DATE AND v.tanggal_jatuh_tempo <= DATE_ADD(CURDATE(), INTERVAL 7 DAY)) AS segera, SUM(YEAR(v.tanggal_jatuh_tempo) = YEAR(CURDATE()) AND MONTH(v.tanggal_jatuh_tempo) = MONTH(CURDATE())) AS bulan_ini, SUM(YEAR(v.tanggal_jatuh_tempo) = YEAR(CURDATE())) AS tahun_ini " . $baseSql;
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
.destruction-schedule{background:#f4f6fb;color:#12224a}.petugas-sidebar{position:fixed;inset:0 auto 0 0;z-index:2;width:264px;min-width:264px;max-width:264px;box-sizing:border-box;padding:18px 14px;background:linear-gradient(180deg,#07306b,#08285a);color:#fff;display:flex;flex-direction:column}.petugas-brand{display:flex;flex-direction:column;align-items:flex-start;gap:0;padding:5px 10px 28px}.petugas-brand>.petugas-logo{display:block;width:112px;height:56px;margin:0 0 12px;border-radius:10px;overflow:hidden;clip-path:inset(0 round 10px)}.petugas-brand img{display:block;width:112px;height:56px;object-fit:contain;border-radius:10px}.petugas-brand b,.petugas-brand small,.petugas-brand span{display:block}.petugas-brand b{color:#f97316;font-size:20px;font-weight:800;line-height:1.1}.petugas-brand small{margin-top:3px;color:#f4f7ff;font-size:15px;font-weight:800;letter-spacing:.04em;line-height:1.15}.petugas-brand>div span{margin-top:7px;color:#c7d6ff;font-size:11px;line-height:1.35}.petugas-sidebar nav{display:grid;gap:5px}.petugas-sidebar nav p{margin:18px 10px 10px;color:#c7d6ff;font-size:10px;font-weight:700;letter-spacing:.08em;text-transform:uppercase}.petugas-menu{display:flex;align-items:center;gap:12px;padding:11px 12px;border-radius:10px;color:#f4f7ff;font-size:13px;font-weight:600;text-decoration:none}.petugas-menu:hover,.petugas-menu.active{background:#1357d0}.petugas-menu i{font-size:20px}.petugas-access{margin-top:auto;padding:15px;border:1px solid #2c5e9b;border-radius:16px;background:#0c3a7d}.petugas-access i{color:#a9caff}.petugas-access b,.petugas-access span,.petugas-access small{display:block}.petugas-access b{margin:9px 0 4px;font-size:12px}.petugas-access span,.petugas-access small{color:#c7d6ff;font-size:11px;line-height:1.5}.petugas-access small{margin-top:6px;font-size:10px;font-weight:700}.with-petugas-sidebar .schedule-header,.with-petugas-sidebar .schedule-page{margin-left:264px}.schedule-header{min-height:84px;padding:14px 28px;border-bottom:1px solid #e6ebf3;background:#fff;display:flex;align-items:center;justify-content:flex-end}.user-menu{position:relative}.user-menu summary{display:flex;align-items:center;gap:10px;cursor:pointer;list-style:none}.user-menu summary::-webkit-details-marker{display:none}.user-avatar{display:grid;place-items:center;width:36px;height:36px;border-radius:50%;background:#eaf2ff;color:#2563eb;font-weight:800}.user-copy b,.user-copy small{display:block}.user-copy small{margin-top:3px;color:#62708c;font-size:11px}.user-menu>summary>i{color:#62708c;font-size:18px}.user-dropdown{position:absolute;top:calc(100% + 10px);right:0;z-index:10;min-width:190px;padding:8px;border:1px solid #e2e8f0;border-radius:12px;background:#fff;box-shadow:0 12px 28px #0f172a1c}.user-dropdown-header{padding:8px 10px;border-bottom:1px solid #eef2f7}.user-dropdown-header b,.user-dropdown-header small{display:block}.user-dropdown-header small{margin-top:3px;color:#62708c;font-size:11px}.user-dropdown a{display:flex;align-items:center;gap:8px;margin-top:5px;padding:9px 10px;border-radius:8px;color:#334155;font-size:12px;font-weight:700;text-decoration:none}.user-dropdown a:hover{background:#eff6ff;color:#1d4ed8}.user-dropdown a i{font-size:18px}@media(max-width:760px){.petugas-sidebar{position:static;width:100%;min-height:auto}.petugas-sidebar nav{display:none}.with-petugas-sidebar .schedule-header,.with-petugas-sidebar .schedule-page{margin-left:0}.petugas-access{display:none}.schedule-header{min-height:72px;padding:12px 16px}.user-copy{display:none}.schedule-page{padding:16px}}.schedule-page{max-width:1600px;padding:18px 22px 28px}.schedule-hero{margin-bottom:16px;padding:24px;border:1px solid #e6ebf3;border-radius:16px;box-shadow:0 1px 2px #0f172a0a,0 10px 24px #0f172a06}.back-dashboard{display:inline-flex;align-items:center;gap:6px;margin:0 0 14px;padding:8px 11px;border:1px solid #bfdbfe;border-radius:9px;background:#fff;color:#1d4ed8;font-size:12px;font-weight:700;text-decoration:none}.back-dashboard:hover{background:#eff6ff;border-color:#93c5fd}.back-dashboard i{font-size:17px}.schedule-hero .breadcrumb{margin:0 0 9px;color:#7182a1;font-size:11px}.schedule-hero h1{margin:0 0 6px;color:#12224a;font-size:24px}.schedule-hero p{margin:0;color:#5f6d86;font-size:13px}.schedule-stats{grid-template-columns:repeat(4,minmax(0,1fr));gap:14px;margin-bottom:16px}.schedule-stats article,.schedule-table-panel{border:1px solid #e6ebf3;border-radius:16px;box-shadow:0 1px 2px #0f172a0a,0 10px 24px #0f172a06}.schedule-stats article{padding:18px}.schedule-stats i{width:54px;height:54px;border-radius:14px;background:#eaf1ff;color:#2563eb}.schedule-stats article:nth-child(2) i{background:#fff2e6;color:#f97316}.schedule-stats article:nth-child(3) i{background:#e9f7ef;color:#16a34a}.schedule-stats article:nth-child(4) i{background:#f0eaff;color:#7c3aed}.schedule-stats small{color:#7182a1}.schedule-stats span{color:#64748b}.schedule-table-panel{overflow:hidden}.schedule-table th{background:#07306b;color:#fff;font-size:10px;letter-spacing:.04em}.schedule-table td{padding:15px 16px;color:#334155;vertical-align:middle}.schedule-table tbody tr{transition:background .15s ease}.schedule-table tbody tr:hover{background:#f8faff}.schedule-date,.schedule-badge{display:inline-flex;align-items:center;min-height:27px;padding:4px 9px;border-radius:20px;font-size:10px;font-weight:700;white-space:nowrap}.date-normal{color:#17669c;background:#e8f4fb}.date-warning{color:#b45309;background:#fff4df}.date-danger,.date-overdue{color:#c9282d;background:#fff0f0}.date-muted{color:#64748b;background:#f1f5f9}.badge-primary{color:#174cc5;background:#edf3ff}.badge-warning{color:#b45309;background:#fff4df}.badge-danger{color:#c9282d;background:#fff0f0}.badge-info{color:#17669c;background:#e8f4fb}.badge-neutral{color:#475569;background:#f1f5f9}.schedule-table .archive-detail-link{padding:6px 8px;border-radius:7px;background:#edf3ff}.schedule-table .archive-empty{padding:62px 20px}.schedule-table .archive-empty h3{font-size:16px}@media(max-width:760px){.schedule-page{padding:16px}.schedule-hero{padding:19px}.schedule-hero h1{font-size:20px}.schedule-stats{grid-template-columns:1fr;gap:12px}.schedule-table td{white-space:normal}.schedule-table th,.schedule-table td{min-width:130px}.schedule-table td:nth-child(3){min-width:220px}}
.with-petugas-sidebar{background:#f4f6fb;color:#12224a}.with-petugas-sidebar .petugas-sidebar{background:linear-gradient(180deg,#07306b,#08285a)}.with-petugas-sidebar .petugas-menu:hover,.with-petugas-sidebar .petugas-menu.active{background:#1357d0}.with-petugas-sidebar .petugas-access{border-color:#1357d0;background:#08285a}.with-petugas-sidebar .schedule-header,.with-petugas-sidebar .schedule-page{color:#12224a}.with-petugas-sidebar .schedule-page h1,.with-petugas-sidebar .schedule-page h2,.with-petugas-sidebar .schedule-page h3{color:#12224a}.with-petugas-sidebar .schedule-page p,.with-petugas-sidebar .schedule-page small,.with-petugas-sidebar .breadcrumb{color:#64748b}.with-petugas-sidebar .schedule-page a{color:#1357d0}.with-petugas-sidebar .schedule-page .schedule-badge.badge-primary,.with-petugas-sidebar .schedule-page .badge-info{background:#edf3ff;color:#174cc5}.with-petugas-sidebar .schedule-page .schedule-badge.badge-warning{background:#fff4df;color:#b45309}.user-avatar{display:grid;place-items:center;overflow:hidden;width:30px;height:30px;border-radius:999px;background:linear-gradient(135deg,#00173e,#1459cf);color:#fff;font-size:12px;font-weight:700}.user-avatar img{display:block!important;flex:0 0 100%!important;width:100%!important;min-width:100%!important;max-width:100%!important;height:100%!important;min-height:100%!important;max-height:100%!important;object-fit:cover!important;border-radius:inherit}</style>
</head>
<body class="dashboard-body destruction-schedule<?= $isPetugas ? ' with-petugas-sidebar' : '' ?>">
<?php if ($isPetugas): ?><aside class="petugas-sidebar"><div class="petugas-brand"><span class="petugas-logo"><img src="../../assets/images/logofix.png" alt="KAI"></span><div><b>e-ARSIP</b><small>KAI DIVRE III</small><span>Web Pengarsipan Digital</span></div></div><nav><p>Menu Utama</p><a class="petugas-menu" href="../petugas/dashboard.php"><i class="material-symbols-outlined">dashboard</i>Dashboard</a><a class="petugas-menu" href="../petugas/arsip.php"><i class="material-symbols-outlined">folder_open</i>Arsip Digital</a><a class="petugas-menu active" href="jadwal-pemusnahan.php"><i class="material-symbols-outlined">event</i>Jadwal Pemusnahan</a></nav><section class="petugas-access"><i class="material-symbols-outlined">verified_user</i><b>Akses Petugas</b><span>Anda login sebagai Petugas.</span><span>Kelola arsip sesuai unit kerja Anda.</span><small>Unit scoped</small></section></aside><?php endif; ?>
<header class="schedule-header"><details class="user-menu"><summary><span class="user-avatar"><?= profileAvatar('../..', $profileName) ?></span><span class="user-copy"><b><?= e($profileName) ?></b><small><?= e($profileRole) ?></small></span><i class="material-symbols-outlined">expand_more</i></summary><div class="user-dropdown"><div class="user-dropdown-header"><b><?= e($profileName) ?></b><small><?= e($profileRole) ?></small></div><a href="../profile/ubah-profil.php"><i class="material-symbols-outlined">person</i>Ubah Profil</a><a href="../profile/ubah-password.php"><i class="material-symbols-outlined">key</i>Ubah Password</a><?= logoutForm('../../logout.php') ?></div></details></header>
<main class="admin-content schedule-page">
    <section class="schedule-hero panel">
        <div>
            <a class="back-dashboard" href="<?= e($dashboardHref) ?>"><i class="material-symbols-outlined">arrow_back</i>Kembali ke Dashboard</a>
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
