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
<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><?php require dirname(__DIR__, 2).'/includes/favicon.php';?><link rel="stylesheet" href="../../assets/css/auth.css"><title>Detail Arsip</title></head><body class="dashboard-body"><main class="admin-content"><section class="panel"><h2>Detail Arsip</h2><div class="table-wrap"><table><tbody><tr><th>No Registrasi</th><td><?=e($archive['no_registrasi'])?></td></tr><tr><th>No Surat</th><td><?=e($archive['no_surat'])?></td></tr><tr><th>Perihal</th><td><?=e($archive['perihal'])?></td></tr><tr><th>Tanggal Surat</th><td><?=e($archive['tanggal_surat'])?></td></tr><tr><th>Unit</th><td><?=e($archive['nama_unit'])?></td></tr><tr><th>Jenis</th><td><?=e($archive['nama_jenis'])?></td></tr><tr><th>Klasifikasi</th><td><?=e($archive['kode_klasifikasi'].' - '.$archive['nama_klasifikasi'])?></td></tr><tr><th>Status</th><td><?=e($archive['status_arsip'])?></td></tr></tbody></table></div><p><a href="arsip.php">Kembali ke pencarian</a></p></section></main></body></html>
