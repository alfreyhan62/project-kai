<?php
declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/config/database.php';
require_once dirname(__DIR__, 2) . '/includes/auth.php';
requireVisitor();

$id = (int) ($_GET['id'] ?? 0);
$statement = $pdo->prepare('SELECT a.no_registrasi,a.no_surat,a.perihal,a.tanggal_surat,a.status_arsip,u.nama_unit,j.nama_jenis,COALESCE(k.kode_klasifikasi, \'-\') AS kode_klasifikasi,COALESCE(k.nama_klasifikasi, \'-\') AS nama_klasifikasi FROM arsip a JOIN unit_kerja u ON u.unit_id=a.unit_id JOIN jenis_surat j ON j.jenis_surat_id=a.jenis_surat_id LEFT JOIN klasifikasi_arsip k ON k.klasifikasi_id=a.klasifikasi_id WHERE a.arsip_id=:id');
$statement->execute(['id' => $id]);
$archive = $statement->fetch();
if (!$archive) {
    http_response_code(403);
    exit('Arsip tidak dapat diakses.');
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
<title>Detail Arsip | e-ARSIP</title>
<style>
.visitor-detail-page{max-width:1120px;margin:0 auto;padding:28px 22px 48px;color:#172554}.detail-topbar{display:flex;align-items:flex-start;justify-content:space-between;gap:20px;margin-bottom:24px}.breadcrumb{display:flex;gap:8px;align-items:center;margin:0 0 10px;color:#71809a;font-size:12px}.breadcrumb span:last-child{color:#2563eb;font-weight:700}.detail-topbar h1{margin:0;color:#10234d;font-size:28px;letter-spacing:-.03em}.detail-topbar p{margin:7px 0 0;color:#64748b;font-size:14px}.back-link{display:inline-flex;align-items:center;gap:6px;padding:9px 12px;border:1px solid #dbe4f0;border-radius:9px;background:#fff;color:#334155;font-size:13px;font-weight:600;text-decoration:none;white-space:nowrap}.back-link:hover{border-color:#93b4e8;color:#1d4ed8}.archive-hero,.detail-card{border:1px solid #e2e8f0;border-radius:16px;background:#fff;box-shadow:0 8px 26px rgba(15,35,74,.06)}.archive-hero{padding:26px;margin-bottom:20px}.hero-label{display:flex;align-items:center;gap:7px;color:#2563eb;font-size:11px;font-weight:800;letter-spacing:.07em;text-transform:uppercase}.hero-label i,.card-title i{font-size:20px}.hero-heading{display:flex;justify-content:space-between;gap:18px;margin:14px 0 18px}.hero-heading h2{margin:0;color:#10234d;font-size:21px}.hero-heading p{margin:6px 0 0;color:#475569;font-size:14px;line-height:1.55}.status-badge{display:inline-flex;align-items:center;justify-content:center;min-height:29px;padding:5px 11px;border-radius:999px;font-size:11px;font-weight:800;white-space:nowrap}.status-inaktif{background:#dcfce7;color:#166534}.status-permanen{background:#dbeafe;color:#1d4ed8}.status-musnah{background:#fee2e2;color:#b91c1c}.status-default{background:#eef2f7;color:#475569}.hero-meta{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px;padding-top:18px;border-top:1px solid #eef2f7}.hero-meta div,.metadata-grid div{min-width:0}.hero-meta small,.metadata-grid small{display:block;margin-bottom:5px;color:#71809a;font-size:10px;font-weight:700;letter-spacing:.05em;text-transform:uppercase}.hero-meta b,.metadata-grid b{display:block;color:#1e293b;font-size:13px;line-height:1.45;overflow-wrap:anywhere}.detail-card{padding:24px}.card-title{display:flex;align-items:center;gap:9px;margin:0 0 4px;color:#10234d;font-size:17px}.card-subtitle{margin:0 0 20px;color:#71809a;font-size:12px}.metadata-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:1px;border:1px solid #edf1f6;border-radius:12px;overflow:hidden}.metadata-grid div{padding:16px;background:#fff}.metadata-grid div:nth-child(4n+1),.metadata-grid div:nth-child(4n+2){background:#f8faff}@media(max-width:700px){.visitor-detail-page{padding:20px 14px 32px}.detail-topbar{display:block}.back-link{margin-top:16px}.detail-topbar h1{font-size:24px}.archive-hero,.detail-card{padding:18px;border-radius:13px}.hero-heading{display:block}.hero-heading .status-badge{margin-top:13px}.hero-meta,.metadata-grid{grid-template-columns:1fr}.metadata-grid div:nth-child(n){background:#fff}.metadata-grid div:nth-child(odd){background:#f8faff}}
</style>
</head>
<body class="dashboard-body">
<main class="visitor-detail-page">
<header class="detail-topbar"><div><nav class="breadcrumb" aria-label="Breadcrumb"><span>Dashboard</span><span>/</span><span>Arsip</span><span>/</span><span>Detail Arsip</span></nav><h1>Detail Arsip</h1><p>Informasi lengkap mengenai arsip yang dipilih.</p></div><a class="back-link" href="arsip.php"><i class="material-symbols-outlined">arrow_back</i>Kembali</a></header>
<section class="archive-hero"><span class="hero-label"><i class="material-symbols-outlined">folder_open</i>Arsip Terpilih</span><div class="hero-heading"><div><h2><?= e($archive['no_registrasi']) ?></h2><p><?= e($archive['perihal']) ?></p></div><span class="status-badge status-<?= e(in_array(strtolower((string) $archive['status_arsip']), ['aktif', 'permanen', 'musnah'], true) ? strtolower((string) $archive['status_arsip']) : 'default') ?>"><?= e($archive['status_arsip']) ?></span></div><div class="hero-meta"><div><small>No. Surat</small><b><?= e($archive['no_surat']) ?></b></div><div><small>Unit Kerja</small><b><?= e($archive['nama_unit']) ?></b></div><div><small>Jenis Surat</small><b><?= e($archive['nama_jenis']) ?></b></div></div></section>
<section class="detail-card"><h2 class="card-title"><i class="material-symbols-outlined">description</i>Informasi Arsip</h2><p class="card-subtitle">Identitas dan klasifikasi arsip yang tersedia untuk dibaca.</p><div class="metadata-grid"><div><small>No. Registrasi</small><b><?= e($archive['no_registrasi']) ?></b></div><div><small>No. Surat</small><b><?= e($archive['no_surat']) ?></b></div><div><small>Perihal</small><b><?= e($archive['perihal']) ?></b></div><div><small>Tanggal Surat</small><b><?= e($archive['tanggal_surat']) ?></b></div><div><small>Unit Kerja</small><b><?= e($archive['nama_unit']) ?></b></div><div><small>Jenis Surat</small><b><?= e($archive['nama_jenis']) ?></b></div><div><small>Klasifikasi</small><b><?= e($archive['kode_klasifikasi'] . ' - ' . $archive['nama_klasifikasi']) ?></b></div><div><small>Status Arsip</small><b><?= e($archive['status_arsip']) ?></b></div></div></section>
</main>
</body>
</html>
