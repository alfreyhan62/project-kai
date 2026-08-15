<?php
declare(strict_types=1);

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
requireRole(4);

$q = trim((string) ($_GET['q'] ?? ''));
$unitId = (int) ($_GET['unit_id'] ?? 0);
$units = $pdo->query('SELECT unit_id,nama_unit FROM unit_kerja ORDER BY nama_unit')->fetchAll();
$sql = 'SELECT a.arsip_id,a.no_registrasi,a.no_surat,a.perihal,a.tanggal_surat,a.status_arsip,u.nama_unit,j.nama_jenis,COALESCE(k.nama_klasifikasi, \'-\') AS nama_klasifikasi FROM arsip a JOIN unit_kerja u ON u.unit_id=a.unit_id JOIN jenis_surat j ON j.jenis_surat_id=a.jenis_surat_id LEFT JOIN klasifikasi_arsip k ON k.klasifikasi_id=a.klasifikasi_id WHERE (a.no_registrasi LIKE :q1 OR a.no_surat LIKE :q2 OR a.perihal LIKE :q3)';
$params = ['q1' => "%$q%", 'q2' => "%$q%", 'q3' => "%$q%"];
if ($unitId > 0) {
    $sql .= ' AND a.unit_id = :unit_id';
    $params['unit_id'] = $unitId;
}
$statement = $pdo->prepare($sql . ' ORDER BY a.created_at DESC LIMIT 100');
$statement->execute($params);
$items = $statement->fetchAll();
?>
<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><?php require __DIR__.'/includes/favicon.php';?><link rel="stylesheet" href="assets/css/auth.css"><title>Monitoring Arsip</title></head><body class="dashboard-body"><main class="admin-content"><section class="panel user-list"><h2>Monitoring Arsip</h2><form class="filter-form" method="get"><input name="q" value="<?=e($q)?>" placeholder="Registrasi, surat, perihal"><select name="unit_id"><option value="0">Semua unit</option><?php foreach($units as $unit):?><option value="<?=(int)$unit['unit_id']?>" <?=(int)$unit['unit_id']===$unitId?'selected':''?>><?=e($unit['nama_unit'])?></option><?php endforeach;?></select><button>Cari</button></form><div class="table-wrap"><table><thead><tr><th>Registrasi</th><th>Surat</th><th>Perihal</th><th>Tanggal</th><th>Unit</th><th>Jenis</th><th>Klasifikasi</th><th>Status</th><th>Aksi</th></tr></thead><tbody><?php foreach($items as $item):?><tr><td><?=e($item['no_registrasi'])?></td><td><?=e($item['no_surat'])?></td><td><?=e($item['perihal'])?></td><td><?=e($item['tanggal_surat'])?></td><td><?=e($item['nama_unit'])?></td><td><?=e($item['nama_jenis'])?></td><td><?=e($item['nama_klasifikasi'])?></td><td><?=e($item['status_arsip'])?></td><td><a href="arsip-detail.php?id=<?=(int)$item['arsip_id']?>">Detail</a></td></tr><?php endforeach;?><?php if(!$items):?><tr><td colspan="9">Tidak ada arsip.</td></tr><?php endif;?></tbody></table></div></section></main></body></html>
