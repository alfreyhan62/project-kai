<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/archive.php';
requireRole(1, 2, 4);
$role = currentRoleId();
$unit = (int) ($_SESSION['unit_id'] ?? 0);
$id = (int) ($_GET['id'] ?? 0);
$where = 'a.arsip_id = :id';
$params = ['id' => $id];
if ($role === 2) {
    if (!$unit) {
        http_response_code(403);
        exit('Unit kerja Petugas belum ditetapkan.');
    }
    $where .= ' AND a.unit_id = :unit';
    $params['unit'] = $unit;
}
$sql = 'SELECT a.*,u.nama_unit,j.nama_jenis,COALESCE(k.nama_klasifikasi,\'-\') nama_klasifikasi,COALESCE(k.kode_klasifikasi,\'-\') kode_klasifikasi,COALESCE(ru.nama_ruangan,\'-\') nama_ruangan,COALESCE(l.nama_lemari,l.kode_lemari,\'-\') nama_lemari,COALESCE(r.kode_rak,\'-\') kode_rak,COALESCE(r.nama_rak,\'-\') nama_rak,COALESCE(us.nama_lengkap,\'-\') created_name FROM arsip a JOIN unit_kerja u ON u.unit_id=a.unit_id JOIN jenis_surat j ON j.jenis_surat_id=a.jenis_surat_id LEFT JOIN klasifikasi_arsip k ON k.klasifikasi_id=a.klasifikasi_id LEFT JOIN rak r ON r.rak_id=a.rak_id LEFT JOIN lemari l ON l.lemari_id=r.lemari_id LEFT JOIN ruangan ru ON ru.ruangan_id=l.ruangan_id LEFT JOIN users us ON us.user_id=a.created_by WHERE ' . $where;
$statement = $pdo->prepare($sql);
$statement->execute($params);
$a = $statement->fetch();
if (!$a) {
    http_response_code(403);
    exit('Arsip tidak dapat diakses.');
}
$files = $pdo->prepare('SELECT file_id,nama_file,ekstensi,mime_type,ukuran_file,is_primary,uploaded_at FROM file_arsip WHERE arsip_id=:id ORDER BY uploaded_at DESC');
$files->execute(['id' => $id]);
$files = $files->fetchAll();
?>
<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><?php require dirname(__DIR__).'/includes/favicon.php';?><link rel="stylesheet" href="assets/css/auth.css"><title>Detail Arsip</title></head><body class="dashboard-body"><main class="admin-content"><section class="panel"><h2><?=e($a['no_registrasi'])?></h2><div class="table-wrap"><table><tbody><tr><th>No Registrasi</th><td><?=e($a['no_registrasi'])?></td></tr><tr><th>No Surat</th><td><?=e($a['no_surat'])?></td></tr><tr><th>Perihal</th><td><?=e($a['perihal'])?></td></tr><tr><th>Tanggal Surat</th><td><?=e($a['tanggal_surat'])?></td></tr><tr><th>Unit Kerja</th><td><?=e($a['nama_unit'])?></td></tr><tr><th>Ruangan</th><td><?=e($a['nama_ruangan'])?></td></tr><tr><th>Lemari</th><td><?=e($a['nama_lemari'])?></td></tr><tr><th>Rak</th><td><?=e(trim($a['kode_rak'].' '.$a['nama_rak']))?></td></tr><tr><th>Jenis Surat</th><td><?=e($a['nama_jenis'])?></td></tr><tr><th>Klasifikasi</th><td><?=e($a['kode_klasifikasi'].' - '.$a['nama_klasifikasi'])?></td></tr><tr><th>Status Arsip</th><td><?=e($a['status_arsip'])?></td></tr><tr><th>Masa Simpan Tahun</th><td><?=e((string)($a['masa_simpan_tahun']??'-'))?></td></tr><tr><th>Mulai Retensi</th><td><?=e($a['tanggal_mulai_retensi']??'-')?></td></tr><tr><th>Jatuh Tempo</th><td><?=e($a['tanggal_jatuh_tempo']??'-')?></td></tr><tr><th>Penyimpanan Cloud</th><td><?=e($a['penyimpanan_cloud']??'-')?></td></tr><tr><th>Detail Arsip</th><td><?=e($a['detail_arsip']??'-')?></td></tr><tr><th>Keterangan</th><td><?=e($a['keterangan']??'-')?></td></tr><tr><th>Dibuat Oleh</th><td><?=e($a['created_name'])?></td></tr><tr><th>Tanggal Dibuat</th><td><?=e($a['created_at'])?></td></tr><tr><th>Tanggal Diperbarui</th><td><?=e($a['updated_at'])?></td></tr></tbody></table></div></section><section class="panel"><h2>Dokumen</h2><?php if($role===2):?><form method="post" action="upload-arsip.php" enctype="multipart/form-data" class="admin-form"><input type="hidden" name="csrf_token" value="<?=e(csrfToken())?>"><input type="hidden" name="arsip_id" value="<?=$id?>"><label>Pilih File<input type="file" name="document" accept=".pdf,.jpg,.jpeg,.png" required></label><p>Maksimal 20 MB per file. Format: PDF, JPG, JPEG, PNG.</p><button class="primary-button" type="submit">Upload</button></form><?php endif;?><div class="table-wrap"><table><thead><tr><th>Nama File</th><th>Ukuran</th><th>Extension</th><th>MIME Type</th><th>Tanggal Upload</th><th>Primary</th><th>Aksi</th></tr></thead><tbody><?php foreach($files as $file):?><tr><td><?=e($file['nama_file'])?></td><td><?=(int)$file['ukuran_file']?> bytes</td><td><?=e($file['ekstensi']??'-')?></td><td><?=e($file['mime_type']??'-')?></td><td><?=e($file['uploaded_at'])?></td><td><?=(int)$file['is_primary']?'Ya':'Tidak'?></td><td><a href="preview-arsip.php?id=<?=(int)$file['file_id']?>" target="_blank" rel="noopener">Preview</a> <a href="download-arsip.php?id=<?=(int)$file['file_id']?>">Download</a></td></tr><?php endforeach;?><?php if(!$files):?><tr><td colspan="7">Belum ada dokumen.</td></tr><?php endif;?></tbody></table></div></section></main></body></html>
