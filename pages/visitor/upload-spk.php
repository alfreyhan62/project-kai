<?php
declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/config/database.php';
require_once dirname(__DIR__, 2) . '/includes/auth.php';
require_once dirname(__DIR__, 2) . '/includes/archive.php';
requireVisitor();

$errors = [];
$visitorUnit = $pdo->prepare('SELECT uk.unit_id, uk.nama_unit FROM users u INNER JOIN unit_kerja uk ON uk.unit_id = u.unit_id WHERE u.user_id = :user_id AND u.role_id = :role_id AND u.is_active = TRUE');
$visitorUnit->execute(['user_id' => (int) $_SESSION['user_id'], 'role_id' => (int) $_SESSION['role_id']]);
$visitorUnit = $visitorUnit->fetch() ?: null;
$units = $pdo->query('SELECT unit_id,nama_unit FROM unit_kerja ORDER BY nama_unit')->fetchAll();
$spkStatuses = ['Draft', 'Aktif', 'Selesai', 'Dibatalkan'];
$defaultJenisSuratId = (int) $pdo->query('SELECT jenis_surat_id FROM jenis_surat ORDER BY jenis_surat_id LIMIT 1')->fetchColumn();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken()) $errors[] = 'Permintaan tidak valid.';
    $nilaiSpk = preg_replace('/[^0-9]/', '', (string) ($_POST['nilai_spk'] ?? ''));
    $mulai = (string) ($_POST['tanggal_mulai_pekerjaan'] ?? '');
    $selesai = (string) ($_POST['tanggal_selesai_pekerjaan'] ?? '');
    $jangkaWaktu = preg_match('/^\d{4}-\d{2}-\d{2}$/', $mulai) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $selesai) ? (new DateTimeImmutable($mulai))->diff(new DateTimeImmutable($selesai))->days + 1 : null;
    $unitId = (int) ($visitorUnit['unit_id'] ?? 0);
    $penanggungJawabUnitId = (int) ($_POST['penanggung_jawab_unit_id'] ?? 0);
    $data = ['unit_id' => $unitId, 'no_registrasi' => '', 'no_surat' => '', 'perihal' => '', 'tanggal_surat' => '', 'jenis_surat_id' => $defaultJenisSuratId, 'klasifikasi_id' => null, 'detail_arsip' => null, 'keterangan' => null, 'nomor_spk' => trim((string) ($_POST['nomor_spk'] ?? '')), 'tanggal_spk' => (string) ($_POST['tanggal_spk'] ?? ''), 'vendor' => trim((string) ($_POST['vendor'] ?? '')), 'nama_pekerjaan' => trim((string) ($_POST['nama_pekerjaan'] ?? '')), 'nilai_spk' => $nilaiSpk === '' ? null : (int) $nilaiSpk, 'kategori_nilai' => $nilaiSpk !== '' && (int) $nilaiSpk > 500000000 ? '> Rp500 Juta' : '≤ Rp500 Juta', 'tanggal_mulai_pekerjaan' => $mulai, 'tanggal_selesai_pekerjaan' => $selesai, 'jangka_waktu' => $jangkaWaktu, 'penanggung_jawab_unit_id' => $penanggungJawabUnitId, 'status_spk' => (string) ($_POST['status_spk'] ?? '')];
    $data['no_registrasi'] = 'SPK-' . $data['nomor_spk'];
    $data['no_surat'] = $data['nomor_spk'];
    $data['perihal'] = $data['nama_pekerjaan'];
    $data['tanggal_surat'] = $data['tanggal_spk'];
    if (!$visitorUnit) $errors[] = 'Akun Visitor belum terhubung ke unit kerja.';
    if (!$data['jenis_surat_id']) $errors[] = 'Jenis surat default belum tersedia. Hubungi administrator.';
    if ($data['nomor_spk'] === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $data['tanggal_spk']) || $data['vendor'] === '' || $data['nama_pekerjaan'] === '' || $data['nilai_spk'] === null || $data['nilai_spk'] <= 0 || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $mulai) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $selesai) || !$data['penanggung_jawab_unit_id'] || !in_array($data['status_spk'], $spkStatuses, true)) $errors[] = 'Field wajib Data SPK belum lengkap atau tidak valid.';
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $mulai) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $selesai) && $selesai < $mulai) $errors[] = 'Tanggal selesai pekerjaan tidak boleh lebih awal dari tanggal mulai.';
    $check = $pdo->prepare('SELECT 1 FROM unit_kerja WHERE unit_id = :id'); $check->execute(['id' => $data['unit_id']]); if (!$check->fetchColumn()) $errors[] = 'Unit tujuan tidak valid.';
    $check = $pdo->prepare('SELECT 1 FROM unit_kerja WHERE unit_id = :id'); $check->execute(['id' => $data['penanggung_jawab_unit_id']]); if (!$check->fetchColumn()) $errors[] = 'Penanggung jawab/unit tidak valid.';
    $check = $pdo->prepare('SELECT 1 FROM jenis_surat WHERE jenis_surat_id = :id'); $check->execute(['id' => $data['jenis_surat_id']]); if (!$check->fetchColumn()) $errors[] = 'Jenis surat tidak valid.';
    if ($data['klasifikasi_id'] !== null) { $check = $pdo->prepare('SELECT 1 FROM klasifikasi_arsip WHERE klasifikasi_id = :id'); $check->execute(['id' => $data['klasifikasi_id']]); if (!$check->fetchColumn()) $errors[] = 'Klasifikasi tidak valid.'; }
    $check = $pdo->prepare('SELECT 1 FROM arsip WHERE no_registrasi = :no_registrasi'); $check->execute(['no_registrasi' => $data['no_registrasi']]); if ($check->fetchColumn()) $errors[] = 'Nomor registrasi sudah digunakan.';
    $file = $_FILES['document'] ?? null;
    if (!$file || $file['error'] !== UPLOAD_ERR_OK) $errors[] = 'Dokumen wajib diunggah.';
    $mime = $file && $file['error'] === UPLOAD_ERR_OK ? (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']) : '';
    if ($file && $file['error'] === UPLOAD_ERR_OK && !archiveUploadAllowed((string) $file['name'], $mime, (int) $file['size'])) $errors[] = 'File harus PDF/JPG/JPEG/PNG maksimal 20 MB.';
    if (!$errors) {
        $extension = strtolower(pathinfo((string) $file['name'], PATHINFO_EXTENSION));
        $relative = archiveStoragePath($extension);
        $absolute = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relative);
        if (!move_uploaded_file($file['tmp_name'], $absolute)) $errors[] = 'File gagal disimpan.';
        if (!$errors) { $pdo->beginTransaction(); try {
            $statement = $pdo->prepare('INSERT INTO arsip(unit_id,jenis_surat_id,klasifikasi_id,no_registrasi,no_surat,perihal,tanggal_surat,status_arsip,detail_arsip,keterangan,created_by,nomor_spk,tanggal_spk,vendor,nama_pekerjaan,nilai_spk,kategori_nilai,tanggal_mulai_pekerjaan,tanggal_selesai_pekerjaan,jangka_waktu,penanggung_jawab_unit_id,status_spk) VALUES(:unit_id,:jenis_surat_id,:klasifikasi_id,:no_registrasi,:no_surat,:perihal,:tanggal_surat,\'Inaktif\',:detail_arsip,:keterangan,:created_by,:nomor_spk,:tanggal_spk,:vendor,:nama_pekerjaan,:nilai_spk,:kategori_nilai,:tanggal_mulai_pekerjaan,:tanggal_selesai_pekerjaan,:jangka_waktu,:penanggung_jawab_unit_id,:status_spk)');
            $statement->execute($data + ['created_by' => (int) $_SESSION['user_id']]);
            $arsipId = (int) $pdo->lastInsertId();
            $pdo->prepare('INSERT INTO file_arsip(arsip_id,nama_file,path_file,ekstensi,mime_type,ukuran_file,checksum,is_primary,uploaded_by) VALUES(:arsip_id,:nama_file,:path_file,:ekstensi,:mime_type,:ukuran_file,:checksum,1,:uploaded_by)')->execute(['arsip_id' => $arsipId, 'nama_file' => basename((string) $file['name']), 'path_file' => $relative, 'ekstensi' => $extension, 'mime_type' => $mime, 'ukuran_file' => (int) $file['size'], 'checksum' => hash_file('sha256', $absolute), 'uploaded_by' => (int) $_SESSION['user_id']]);
            $pdo->prepare("INSERT INTO riwayat_arsip(arsip_id,user_id,aksi,keterangan) VALUES(:arsip_id,:user_id,'CREATE','Data pokok surat dibuat oleh Visitor')")->execute(['arsip_id' => $arsipId, 'user_id' => (int) $_SESSION['user_id']]);
            $pdo->prepare("INSERT INTO riwayat_arsip(arsip_id,user_id,aksi,keterangan) VALUES(:arsip_id,:user_id,'UPLOAD','Dokumen SPK diunggah oleh Visitor')")->execute(['arsip_id' => $arsipId, 'user_id' => (int) $_SESSION['user_id']]);
            $pdo->commit(); header('Location: arsip-detail.php?id=' . $arsipId); exit;
        } catch (Throwable $exception) { $pdo->rollBack(); @unlink($absolute); $errors[] = 'Data gagal disimpan. Silakan coba lagi.'; } }
    }
}
$visitorUsername = trim((string) ($_SESSION['username'] ?? '')) ?: 'Visitor';
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<?php require dirname(__DIR__, 2) . '/includes/favicon.php'; ?>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Material+Symbols+Outlined" rel="stylesheet">
<link rel="stylesheet" href="../../assets/css/auth.css?v=visitor-2">
<style>.visitor-content{max-width:1120px;padding:32px}.upload-section{padding-top:22px;border-top:1px solid #e2e8f0}.upload-section h2{display:flex;align-items:center;gap:8px;margin:0;color:#061a43;font-size:16px}.upload-section h2 i{color:#1459cf}.upload-section select[readonly],.upload-section input[readonly]{background:#f8fafc;color:#475569}.visitor-form label .material-symbols-outlined{font-size:18px}.upload-card{padding:24px}.upload-card h2,.file-box h3,.password-card h2{display:flex;align-items:center;gap:8px;margin:0;color:#061a43;font-size:16px}.card-caption{margin:7px 0 22px;color:#64748b;font-size:13px}.visitor-form{display:grid;gap:22px}.form-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px}.visitor-form label{margin:0;color:#1a1b1f;font-size:13px}.visitor-form input,.visitor-form select,.visitor-form textarea{width:100%;min-height:42px;margin-top:7px;padding:8px 10px;border:1px solid #c4c6d1;border-radius:7px;background:#fff;font:inherit}.visitor-form textarea{min-height:88px;resize:vertical}.field-wide{grid-column:1/-1}.file-box{padding:18px;border:1px dashed #9fb5dc;border-radius:10px;background:#f8fbff}.file-box h3{font-size:14px}.file-box p,.file-box small{display:block;margin:7px 0;color:#64748b;font-size:12px}.file-input-wrap{display:inline-flex!important;margin-top:8px!important;cursor:pointer}.file-input-wrap span,.secondary-button{display:inline-flex;align-items:center;justify-content:center;min-height:40px;padding:0 15px;border:1px solid #c4c6d1;border-radius:7px;background:#fff;color:#061a43;font-size:13px;font-weight:600;text-decoration:none}.file-input-wrap input{position:absolute;width:1px;height:1px;opacity:0}.form-actions{display:flex;justify-content:flex-end;gap:12px}.form-actions .primary-button{max-width:220px}.upload-card{box-shadow:0 8px 24px #0f357d0d}.file-box{transition:border-color .2s,background .2s,box-shadow .2s}.file-box.is-dragging{border-color:#1459cf;background:#edf4ff;box-shadow:0 0 0 3px #bfdbfe}.file-input-wrap span{transition:background .2s,color .2s}.file-input-wrap:hover span,.file-input-wrap:focus-within span{background:#edf4ff;color:#1459cf}.file-meta{display:flex;align-items:center;gap:10px;margin-top:10px;padding:10px;border-radius:8px;background:#fff;color:#334155}.file-meta i{color:#1459cf}.file-meta strong{display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.file-error{color:#b42318!important}.field-error{display:block;min-height:16px;margin-top:5px;color:#b42318;font-size:11px}.visitor-form input:invalid:not(:placeholder-shown),.visitor-form select:invalid:not(:focus){border-color:#fca5a5}@media(max-width:760px){.visitor-content{padding:20px 16px}.upload-card{padding:18px}.form-grid{grid-template-columns:1fr}.field-wide{grid-column:auto}.form-actions{justify-content:stretch}.form-actions>*{flex:1}}</style>
<style>.upload-card{border:1px solid #e5e7eb!important;border-radius:12px!important;background:#fff!important;box-shadow:0 8px 24px #0f357d0d!important}.upload-section{padding-top:0!important;border-top:0!important}.upload-section h2,.file-box h3{color:#061a43!important;text-transform:uppercase;letter-spacing:.04em}.upload-section h2 i,.file-box h3 i{color:#1459cf!important}.visitor-form{gap:22px!important}.visitor-form .form-grid{grid-template-columns:repeat(2,minmax(0,1fr))!important;gap:18px!important}.visitor-form label{color:#1e293b!important;font-size:13px!important;font-weight:600!important}.visitor-form input,.visitor-form select,.visitor-form textarea{min-height:46px!important;margin-top:8px!important;padding:10px 12px!important;border:1px solid #D1D5DB!important;border-radius:9px!important;background:#fff!important;color:#111827!important;box-shadow:none!important;outline:0!important;font:inherit!important}.visitor-form input:focus,.visitor-form select:focus,.visitor-form textarea:focus{border-color:#1459cf!important;box-shadow:0 0 0 3px #dbeafe!important}.visitor-form input:user-invalid,.visitor-form select:user-invalid,.visitor-form textarea:user-invalid{border-color:#dc2626!important;box-shadow:0 0 0 3px #fee2e2!important}.upload-section select[readonly],.upload-section input[readonly]{background:#f8fafc!important;color:#475569!important}.file-box{display:grid!important;place-items:center!important;gap:10px!important;padding:28px!important;border:1.5px dashed #b8c7df!important;border-radius:12px!important;background:#f8fbff!important;text-align:center!important;transition:border-color .2s,background .2s,box-shadow .2s!important}.file-box:hover,.file-box.is-dragging{border-color:#1459cf!important;background:#edf4ff!important;box-shadow:0 0 0 3px #dbeafe!important}.file-box p{margin:0!important;color:#64748b!important}.file-input-wrap span,.secondary-button{border-color:#D1D5DB!important;border-radius:9px!important;background:#fff!important;color:#061a43!important}.file-input-wrap:hover span,.secondary-button:hover{background:#f8fafc!important;border-color:#9ca3af!important}.form-actions .primary-button{border:0!important;border-radius:9px!important;background:#001d4b!important;color:#fff!important;font-weight:700!important}.form-actions .primary-button:hover{background:#07306b!important}.file-error{color:#dc2626!important}@media(max-width:760px){.visitor-form .form-grid{grid-template-columns:1fr!important}.visitor-content{padding:18px!important}}</style>
<title>Upload SPK</title>
</head>
<body class="dashboard-body visitor-body">
<aside class="visitor-sidebar">
    <div class="visitor-brand">
        <span class="visitor-brand-logo"><img src="../../assets/images/logofix.png" alt="KAI"></span>
        <strong><em>e-ARSIP</em><br>KAI DIVRE III</strong>
        <span>Web Pengarsipan Digital</span>
    </div>
    <nav>
        <p>Menu Utama</p>
        <a href="dashboard.php"><i class="material-symbols-outlined">home</i>Beranda</a>
        <a href="arsip.php"><i class="material-symbols-outlined">search</i>Pencarian Arsip</a>
        <span class="nav-active"><i class="material-symbols-outlined">upload_file</i>Upload SPK</span>
    </nav>
    <section class="visitor-access">
        <i class="material-symbols-outlined">folder_shared</i>
        <b>Akses Terbatas</b>
        <span>Gunakan upload untuk mengirim data pokok surat.</span>
    </section>
</aside>
<div class="visitor-main">
    <header class="visitor-header">
        <div><b>e-ARSIP KAI DIVRE III</b><small>Web Pengarsipan Digital - Kantor KAI Divre III</small></div>
        <details class="user-menu">
            <summary><span class="user-avatar"><?= profileAvatar('../..', $visitorUsername) ?></span><span class="user-summary-text"><b><?= e($visitorUsername) ?></b><small>Visitor</small></span><i class="user-summary-chevron material-symbols-outlined">expand_more</i></summary>
            <div class="user-dropdown"><div class="user-dropdown-header"><b><?= e($visitorUsername) ?></b><small>Visitor</small></div><a href="../profile/ubah-profil.php"><i class="material-symbols-outlined">person</i>Ubah Profil</a><a href="../profile/ubah-password.php"><i class="material-symbols-outlined">key</i>Ubah Password</a><?= logoutForm('../../logout.php') ?></div>
        </details>
    </header>
    <main class="visitor-content">
        <?php if ($errors): ?><div class="visitor-card alert" role="alert"><?php foreach ($errors as $error): ?><div><?= e($error) ?></div><?php endforeach; ?></div><?php endif; ?>
        <section class="visitor-card upload-card">
            <form method="post" enctype="multipart/form-data" class="visitor-form" id="upload-spk-form">
                <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
                <section class="upload-section"><h2><i class="material-symbols-outlined">work</i>Data SPK</h2><div class="form-grid"><label>Nomor SPK<input name="nomor_spk" required value="<?= e($_POST['nomor_spk'] ?? '') ?>"></label><label>Tanggal SPK<input type="date" name="tanggal_spk" required value="<?= e($_POST['tanggal_spk'] ?? '') ?>"></label><label>Pihak Penyedia/Vendor<input name="vendor" required value="<?= e($_POST['vendor'] ?? '') ?>"></label><label>Nama Pekerjaan<input name="nama_pekerjaan" required value="<?= e($_POST['nama_pekerjaan'] ?? '') ?>"></label><label>Nilai SPK<input name="nilai_spk" inputmode="numeric" required value="<?= e($_POST['nilai_spk'] ?? '') ?>"></label><label>Kategori Nilai<select name="kategori_nilai" id="kategori_nilai" readonly><option value="≤ Rp500 Juta" <?= (string) ($_POST['kategori_nilai'] ?? '') === '≤ Rp500 Juta' ? 'selected' : '' ?>>≤ Rp500 Juta</option><option value="> Rp500 Juta" <?= (string) ($_POST['kategori_nilai'] ?? '') === '> Rp500 Juta' ? 'selected' : '' ?>>> Rp500 Juta</option></select></label><label>Tanggal Mulai Pekerjaan<input type="date" name="tanggal_mulai_pekerjaan" required value="<?= e($_POST['tanggal_mulai_pekerjaan'] ?? '') ?>"></label><label>Tanggal Selesai Pekerjaan<input type="date" name="tanggal_selesai_pekerjaan" required value="<?= e($_POST['tanggal_selesai_pekerjaan'] ?? '') ?>"></label><label>Jangka Waktu<input name="jangka_waktu" id="jangka_waktu" readonly value="<?= e((string) ($_POST['jangka_waktu'] ?? '')) ?>"></label><label>Penanggung Jawab / Unit<select name="penanggung_jawab_unit_id" required><option value="">Pilih unit</option><?php foreach ($units as $unit): ?><option value="<?= (int) $unit['unit_id'] ?>" <?= (int) ($_POST['penanggung_jawab_unit_id'] ?? 0) === (int) $unit['unit_id'] ? 'selected' : '' ?>><?= e($unit['nama_unit']) ?></option><?php endforeach; ?></select></label><label>Status SPK<select name="status_spk" required><option value="">Pilih status</option><?php foreach ($spkStatuses as $status): ?><option value="<?= e($status) ?>" <?= (string) ($_POST['status_spk'] ?? '') === $status ? 'selected' : '' ?>><?= e($status) ?></option><?php endforeach; ?></select></label></div></section>
                <section class="file-box">
                    <h3><i class="material-symbols-outlined">attach_file</i>Dokumen SPK</h3>
                    <p>PDF, JPG, JPEG, atau PNG. Maksimal 20 MB.</p>
                    <label class="file-input-wrap" id="drop-zone"><i class="material-symbols-outlined">upload_file</i><span>Upload Dokumen SPK</span><input id="document" type="file" name="document" accept=".pdf,.jpg,.jpeg,.png,application/pdf,image/jpeg,image/png" required></label>
                    <small id="file-status">Belum ada file dipilih.</small><div id="file-meta" class="file-meta" hidden><i class="material-symbols-outlined">description</i><div><strong id="file-name"></strong><small id="file-details"></small></div><button id="file-reset" class="secondary-button" type="button">Hapus</button></div>
                </section>
                <div class="form-actions"><a class="secondary-button" href="dashboard.php">Batal</a><button class="primary-button" type="submit" id="submit-btn">Simpan SPK</button></div>
            </form>
        </section>
    </main>
</div>
<script>const form=document.getElementById('upload-spk-form'),input=document.getElementById('document'),zone=document.getElementById('drop-zone'),status=document.getElementById('file-status'),meta=document.getElementById('file-meta'),nameEl=document.getElementById('file-name'),details=document.getElementById('file-details'),reset=document.getElementById('file-reset'),nilai=form.elements.nilai_spk,kategori=document.getElementById('kategori_nilai'),mulai=form.elements.tanggal_mulai_pekerjaan,selesai=form.elements.tanggal_selesai_pekerjaan,jangka=document.getElementById('jangka_waktu'),allowed=['application/pdf','image/jpeg','image/png'];function showFile(file){if(!file)return;const valid=file.size<=20971520&&allowed.includes(file.type);status.textContent=valid?'File siap diunggah.':file.size>20971520?'File melebihi batas 20 MB.':'Format file tidak didukung. Gunakan PDF, JPG, JPEG, atau PNG.';status.classList.toggle('file-error',!valid);meta.hidden=!valid;nameEl.textContent=file.name;details.textContent=(file.size/1048576).toFixed(2)+' MB · '+(file.type||'Tipe tidak diketahui');}function syncNilai(){const number=Number(nilai.value.replace(/\D/g,''));kategori.value=number>500000000?'> Rp500 Juta':'≤ Rp500 Juta';nilai.value=number?`Rp${number.toLocaleString('id-ID')}`:''}function syncWaktu(){if(!mulai.value||!selesai.value){jangka.value='';selesai.setCustomValidity('');return}if(selesai.value<mulai.value){selesai.setCustomValidity('Tanggal selesai tidak boleh lebih awal dari tanggal mulai.');jangka.value='';return}selesai.setCustomValidity('');jangka.value=`${Math.floor((new Date(`${selesai.value}T00:00:00`)-new Date(`${mulai.value}T00:00:00`))/86400000)+1} Hari`}input.addEventListener('change',()=>showFile(input.files[0]));nilai.addEventListener('input',syncNilai);[mulai,selesai].forEach(x=>x.addEventListener('change',syncWaktu));['dragenter','dragover'].forEach(event=>zone.addEventListener(event,e=>{e.preventDefault();zone.parentElement.classList.add('is-dragging')}));['dragleave','drop'].forEach(event=>zone.addEventListener(event,e=>{e.preventDefault();zone.parentElement.classList.remove('is-dragging')}));zone.addEventListener('drop',e=>{input.files=e.dataTransfer.files;showFile(input.files[0])});reset.addEventListener('click',()=>{input.value='';meta.hidden=true;status.textContent='Belum ada file dipilih.';status.classList.remove('file-error')});form.addEventListener('submit',e=>{syncNilai();syncWaktu();if(!form.checkValidity()||!input.files[0]||input.files[0].size>20971520||!allowed.includes(input.files[0].type)){e.preventDefault();form.reportValidity();return}e.submitter.disabled=true;e.submitter.textContent='Menyimpan...'});syncNilai();syncWaktu();</script>
</body>
</html>
