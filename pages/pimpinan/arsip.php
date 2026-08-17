<?php
declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/config/database.php';
require_once dirname(__DIR__, 2) . '/includes/auth.php';
requireRole(4);

$q = trim((string) ($_GET['q'] ?? ''));
$unitId = (int) ($_GET['unit_id'] ?? 0);
$typeId = (int) ($_GET['jenis_surat_id'] ?? 0);
$classId = (int) ($_GET['klasifikasi_id'] ?? 0);
$status = trim((string) ($_GET['status'] ?? ''));
$from = trim((string) ($_GET['from'] ?? ''));
$to = trim((string) ($_GET['to'] ?? ''));
$units = $pdo->query('SELECT unit_id,nama_unit FROM unit_kerja ORDER BY nama_unit')->fetchAll();
$types = $pdo->query('SELECT jenis_surat_id,nama_jenis FROM jenis_surat ORDER BY nama_jenis')->fetchAll();
$classes = $pdo->query('SELECT klasifikasi_id,nama_klasifikasi FROM klasifikasi_arsip ORDER BY nama_klasifikasi')->fetchAll();
$sql = 'SELECT a.arsip_id,a.no_registrasi,a.no_surat,a.perihal,a.tanggal_surat,a.status_arsip,u.nama_unit,j.nama_jenis,COALESCE(k.nama_klasifikasi, \'-\') AS nama_klasifikasi FROM arsip a JOIN unit_kerja u ON u.unit_id=a.unit_id JOIN jenis_surat j ON j.jenis_surat_id=a.jenis_surat_id LEFT JOIN klasifikasi_arsip k ON k.klasifikasi_id=a.klasifikasi_id WHERE (a.no_registrasi LIKE :q1 OR a.no_surat LIKE :q2 OR a.perihal LIKE :q3)';
$params = ['q1' => "%$q%", 'q2' => "%$q%", 'q3' => "%$q%"];
foreach (['unit_id' => $unitId, 'jenis_surat_id' => $typeId, 'klasifikasi_id' => $classId] as $column => $value) {
    if ($value > 0) { $sql .= " AND a.$column = :$column"; $params[$column] = $value; }
}
if (in_array($status, ['Aktif', 'Permanen', 'Musnah'], true)) { $sql .= ' AND a.status_arsip = :status'; $params['status'] = $status; }
if ($from !== '') { $sql .= ' AND a.tanggal_surat >= :from'; $params['from'] = $from; }
if ($to !== '') { $sql .= ' AND a.tanggal_surat <= :to'; $params['to'] = $to; }
$statement = $pdo->prepare($sql . ' ORDER BY a.created_at DESC LIMIT 100');
$statement->execute($params);
$items = $statement->fetchAll();
?>
<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><?php require dirname(__DIR__, 2) . '/includes/favicon.php'; ?><link rel="stylesheet" href="assets/css/auth.css"><title>Monitoring Arsip</title></head><body class="dashboard-body"><main class="admin-content"><section class="panel user-list"><h2>Monitoring Arsip</h2><form class="filter-form filter-form-wrap" method="get"><input name="q" value="<?= e($q) ?>" placeholder="No registrasi, no surat, perihal"><select name="unit_id"><option value="0">Semua unit</option><?php foreach ($units as $unit): ?><option value="<?= (int) $unit['unit_id'] ?>" <?= (int) $unit['unit_id'] === $unitId ? 'selected' : '' ?>><?= e($unit['nama_unit']) ?></option><?php endforeach; ?></select><select name="jenis_surat_id"><option value="0">Semua jenis</option><?php foreach ($types as $type): ?><option value="<?= (int) $type['jenis_surat_id'] ?>" <?= (int) $type['jenis_surat_id'] === $typeId ? 'selected' : '' ?>><?= e($type['nama_jenis']) ?></option><?php endforeach; ?></select><select name="klasifikasi_id"><option value="0">Semua klasifikasi</option><?php foreach ($classes as $class): ?><option value="<?= (int) $class['klasifikasi_id'] ?>" <?= (int) $class['klasifikasi_id'] === $classId ? 'selected' : '' ?>><?= e($class['nama_klasifikasi']) ?></option><?php endforeach; ?></select><select name="status"><option value="">Semua status</option><?php foreach (['Aktif', 'Permanen', 'Musnah'] as $option): ?><option <?= $status === $option ? 'selected' : '' ?>><?= $option ?></option><?php endforeach; ?></select><input type="date" name="from" value="<?= e($from) ?>"><input type="date" name="to" value="<?= e($to) ?>"><button>Cari</button></form><div class="table-wrap"><table><thead><tr><th>Registrasi</th><th>Surat</th><th>Perihal</th><th>Tanggal</th><th>Unit</th><th>Jenis</th><th>Klasifikasi</th><th>Status</th><th>Aksi</th></tr></thead><tbody><?php foreach ($items as $item): ?><tr><td><?= e($item['no_registrasi']) ?></td><td><?= e($item['no_surat']) ?></td><td><?= e($item['perihal']) ?></td><td><?= e($item['tanggal_surat']) ?></td><td><?= e($item['nama_unit']) ?></td><td><?= e($item['nama_jenis']) ?></td><td><?= e($item['nama_klasifikasi']) ?></td><td><?= e($item['status_arsip']) ?></td><td><a href="arsip-detail.php?id=<?= (int) $item['arsip_id'] ?>">Detail</a></td></tr><?php endforeach; ?><?php if (!$items): ?><tr><td colspan="9">Tidak ada arsip.</td></tr><?php endif; ?></tbody></table></div></section></main></body></html>
