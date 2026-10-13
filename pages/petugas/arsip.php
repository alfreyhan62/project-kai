<?php

declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/config/database.php';
require_once dirname(__DIR__, 2) . '/includes/auth.php';
require_once dirname(__DIR__, 2) . '/includes/archive.php';
require_once dirname(__DIR__, 2) . '/includes/reminder.php';
requireRole(2);
$unit = (int) ($_SESSION['unit_id'] ?? 0);
if (!$unit) {
    http_response_code(403);
    exit('Unit kerja Petugas belum ditetapkan.');
}
$name = (string) ($_SESSION['nama_lengkap'] ?? $_SESSION['username'] ?? 'Petugas');
$errors = [];
$notice = '';
$edit = null;
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    if (!verifyCsrfToken()) $errors[] = 'Permintaan tidak valid.';
    else {
        $action = (string) ($_POST['action'] ?? '');
        $id = (int) ($_POST['arsip_id'] ?? 0);
        if (in_array($action, ['create', 'update'], true)) {
            $data = ['no_registrasi' => trim((string) ($_POST['no_registrasi'] ?? '')), 'no_surat' => trim((string) ($_POST['no_surat'] ?? '')), 'perihal' => trim((string) ($_POST['perihal'] ?? '')), 'tanggal_surat' => (string) ($_POST['tanggal_surat'] ?? ''), 'jenis_surat_id' => (int) ($_POST['jenis_surat_id'] ?? 0), 'klasifikasi_id' => ($_POST['klasifikasi_id'] ?? '') === '' ? null : (int) $_POST['klasifikasi_id'], 'rak_id' => ($_POST['rak_id'] ?? '') === '' ? null : (int) $_POST['rak_id'], 'masa_simpan_tahun' => ($_POST['masa_simpan_tahun'] ?? '') === '' ? null : (int) $_POST['masa_simpan_tahun'], 'tanggal_mulai_retensi' => ($_POST['tanggal_mulai_retensi'] ?? '') === '' ? null : (string) $_POST['tanggal_mulai_retensi'], 'tanggal_jatuh_tempo' => ($_POST['tanggal_jatuh_tempo'] ?? '') === '' ? null : (string) $_POST['tanggal_jatuh_tempo'], 'status_arsip' => (string) ($_POST['status_arsip'] ?? 'Inaktif'), 'detail_arsip' => trim((string) ($_POST['detail_arsip'] ?? '')) ?: null, 'keterangan' => trim((string) ($_POST['keterangan'] ?? '')) ?: null];
            if ($data['no_registrasi'] === '' || $data['no_surat'] === '' || $data['perihal'] === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $data['tanggal_surat']) || !$data['jenis_surat_id']) $errors[] = 'Field wajib belum lengkap atau tanggal tidak valid.';
            if (!in_array($data['status_arsip'], ['Inaktif', 'Permanen', 'Musnah'], true)) $errors[] = 'Status arsip tidak valid.';
            if ($data['status_arsip'] === 'Musnah' && (!$data['masa_simpan_tahun'] || !$data['tanggal_jatuh_tempo'])) $errors[] = 'Arsip Musnah wajib memiliki masa simpan dan tanggal jatuh tempo.';
            if ($data['status_arsip'] === 'Permanen') {
                $data['masa_simpan_tahun'] = null;
                $data['tanggal_mulai_retensi'] = null;
                $data['tanggal_jatuh_tempo'] = null;
            }
            $check = $pdo->prepare('SELECT 1 FROM jenis_surat WHERE jenis_surat_id=:id');
            $check->execute(['id' => $data['jenis_surat_id']]);
            if (!$check->fetchColumn()) $errors[] = 'Jenis surat tidak valid.';
            if ($data['klasifikasi_id'] !== null) {
                $check = $pdo->prepare('SELECT 1 FROM klasifikasi_arsip WHERE klasifikasi_id=:id');
                $check->execute(['id' => $data['klasifikasi_id']]);
                if (!$check->fetchColumn()) $errors[] = 'Klasifikasi tidak valid.';
            }
            if ($data['rak_id'] !== null && !validArchiveLocation($pdo, $unit, $data['rak_id'])) $errors[] = 'Rak tidak berada pada unit kerja Petugas.';
            $check = $pdo->prepare('SELECT 1 FROM arsip WHERE no_registrasi=:n AND arsip_id!=:id');
            $check->execute(['n' => $data['no_registrasi'], 'id' => $id]);
            if ($check->fetchColumn()) $errors[] = 'Nomor registrasi sudah digunakan.';
            $upload = $_FILES['document'] ?? null;
            $uploadPath = null;
            if ($action === 'create' && $upload && (int) $upload['error'] !== UPLOAD_ERR_NO_FILE) {
                if ((int) $upload['error'] !== UPLOAD_ERR_OK) $errors[] = 'File arsip gagal diunggah.';
                else {
                    $mime = (new finfo(FILEINFO_MIME_TYPE))->file((string) $upload['tmp_name']);
                    if (!archiveUploadAllowed((string) $upload['name'], $mime, (int) $upload['size'])) $errors[] = 'File harus PDF, JPG, JPEG, PNG, XLS, atau XLSX maksimal 20 MB.';
                    else {
                        $extension = strtolower(pathinfo((string) $upload['name'], PATHINFO_EXTENSION));
                        $uploadPath = archiveStoragePath($extension);
                    }
                }
            }
            if (!$errors && $action === 'create') {
                $pdo->beginTransaction();
                try {
                    if ($uploadPath) {
                        $uploadAbsolutePath = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $uploadPath);
                        if (!move_uploaded_file((string) $upload['tmp_name'], $uploadAbsolutePath)) throw new RuntimeException('File gagal disimpan.');
                    }
                    $statement = $pdo->prepare('INSERT INTO arsip(unit_id,jenis_surat_id,klasifikasi_id,rak_id,no_registrasi,no_surat,perihal,tanggal_surat,masa_simpan_tahun,tanggal_mulai_retensi,tanggal_jatuh_tempo,status_arsip,detail_arsip,keterangan,created_by) VALUES(:unit_id,:jenis_surat_id,:klasifikasi_id,:rak_id,:no_registrasi,:no_surat,:perihal,:tanggal_surat,:masa_simpan_tahun,:tanggal_mulai_retensi,:tanggal_jatuh_tempo,:status_arsip,:detail_arsip,:keterangan,:created_by)');
                    $statement->execute($data + ['unit_id' => $unit, 'created_by' => (int) $_SESSION['user_id']]);
                    $newId = (int) $pdo->lastInsertId();
                    if ($uploadPath) $pdo->prepare('INSERT INTO file_arsip(arsip_id,nama_file,path_file,ekstensi,mime_type,ukuran_file,checksum,is_primary,uploaded_by) VALUES(:arsip_id,:nama_file,:path_file,:ekstensi,:mime_type,:ukuran_file,:checksum,0,:uploaded_by)')->execute(['arsip_id' => $newId, 'nama_file' => basename((string) $upload['name']), 'path_file' => $uploadPath, 'ekstensi' => $extension, 'mime_type' => $mime, 'ukuran_file' => (int) $upload['size'], 'checksum' => hash_file('sha256', $uploadAbsolutePath), 'uploaded_by' => (int) $_SESSION['user_id']]);
                    $pdo->prepare("INSERT INTO riwayat_arsip(arsip_id,user_id,aksi,keterangan) VALUES(:a,:u,'CREATE','Arsip dibuat')")->execute(['a' => $newId, 'u' => (int) $_SESSION['user_id']]);
                    $pdo->commit();
                    syncArchiveLifecycle($pdo);
                    header('Location: arsip.php?edit=' . $newId);
                    exit;
                } catch (Throwable $e) {
                    if ($pdo->inTransaction()) $pdo->rollBack();
                    if (isset($uploadAbsolutePath) && is_file($uploadAbsolutePath)) unlink($uploadAbsolutePath);
                    $errors[] = 'Data atau file gagal disimpan. Silakan coba lagi.';
                }
            }
            if (!$errors && $action === 'update' && $id) {
                $statement = $pdo->prepare('UPDATE arsip SET jenis_surat_id=:jenis_surat_id,klasifikasi_id=:klasifikasi_id,rak_id=:rak_id,no_surat=:no_surat,perihal=:perihal,tanggal_surat=:tanggal_surat,masa_simpan_tahun=:masa_simpan_tahun,tanggal_mulai_retensi=:tanggal_mulai_retensi,tanggal_jatuh_tempo=:tanggal_jatuh_tempo,status_arsip=:status_arsip,detail_arsip=:detail_arsip,keterangan=:keterangan WHERE arsip_id=:id AND unit_id=:unit');
                $statement->execute($data + ['id' => $id, 'unit' => $unit]);
                if (!$statement->rowCount()) $errors[] = 'Arsip tidak ditemukan atau di luar unit kerja.';
                else {
                    $pdo->prepare("INSERT INTO riwayat_arsip(arsip_id,user_id,aksi,keterangan) VALUES(:a,:u,'UPDATE','Arsip diperbarui')")->execute(['a' => $id, 'u' => (int) $_SESSION['user_id']]);
                    syncArchiveLifecycle($pdo);
                    $notice = 'Arsip berhasil diperbarui.';
                }
            }
        }
    }
}
if (isset($_GET['edit'])) {
    $statement = $pdo->prepare('SELECT * FROM arsip WHERE arsip_id=:id AND unit_id=:unit');
    $statement->execute(['id' => (int) $_GET['edit'], 'unit' => $unit]);
    $edit = $statement->fetch();
    if (!$edit) {
        http_response_code(403);
        exit('Arsip tidak dapat diakses.');
    }
}
$jenis = $pdo->query('SELECT jenis_surat_id,nama_jenis FROM jenis_surat ORDER BY nama_jenis')->fetchAll();
$klas = $pdo->query('SELECT klasifikasi_id,kode_klasifikasi,nama_klasifikasi FROM klasifikasi_arsip ORDER BY kode_klasifikasi')->fetchAll();
$locationsStatement = $pdo->prepare('SELECT r.nama_ruangan,l.lemari_id,COALESCE(l.nama_lemari,l.kode_lemari) nama_lemari,k.rak_id,k.kode_rak FROM ruangan r JOIN lemari l ON l.ruangan_id=r.ruangan_id JOIN rak k ON k.lemari_id=l.lemari_id WHERE r.unit_id=:u ORDER BY r.nama_ruangan,l.kode_lemari,k.kode_rak');
$locationsStatement->execute(['u' => $unit]);
$locations = $locationsStatement->fetchAll();
$q = trim((string) ($_GET['q'] ?? ''));
$jenisFilter = (int) ($_GET['jenis_surat_id'] ?? 0);
$klasFilter = (int) ($_GET['klasifikasi_id'] ?? 0);
$statusFilter = (string) ($_GET['status_arsip'] ?? '');
$from = (string) ($_GET['from'] ?? '');
$to = (string) ($_GET['to'] ?? '');
syncArchiveLifecycle($pdo);
$sql = 'SELECT a.*,j.nama_jenis,k.nama_klasifikasi FROM arsip a JOIN jenis_surat j ON j.jenis_surat_id=a.jenis_surat_id LEFT JOIN klasifikasi_arsip k ON k.klasifikasi_id=a.klasifikasi_id WHERE a.unit_id=:unit AND (a.no_registrasi LIKE :q1 OR a.no_surat LIKE :q2 OR a.perihal LIKE :q3)';
$params = ['unit' => $unit, 'q1' => "%$q%", 'q2' => "%$q%", 'q3' => "%$q%"];
if ($jenisFilter > 0) {
    $sql .= ' AND a.jenis_surat_id=:jenis';
    $params['jenis'] = $jenisFilter;
}
if ($klasFilter > 0) {
    $sql .= ' AND a.klasifikasi_id=:klas';
    $params['klas'] = $klasFilter;
}
if (in_array($statusFilter, ['Inaktif', 'Permanen', 'Musnah'], true)) {
    $sql .= ' AND a.status_arsip=:status';
    $params['status'] = $statusFilter;
}
if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $from)) {
    $sql .= ' AND a.tanggal_surat>=:from';
    $params['from'] = $from;
}
if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $to)) {
    $sql .= ' AND a.tanggal_surat<=:to';
    $params['to'] = $to;
}
$sql .= ' ORDER BY a.created_at DESC';
$statement = $pdo->prepare($sql);
$statement->execute($params);
$items = $statement->fetchAll();
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1"><?php require dirname(__DIR__, 2) . '/includes/favicon.php'; ?><title>Arsip Digital | e-ARSIP</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Material+Symbols+Outlined" rel="stylesheet">
    <style>
        :root {
            --navy: #07306b;
            --navy-dark: #08285a;
            --blue: #2563eb;
            --ink: #12224a;
            --muted: #62708c;
            --line: #e6ebf3;
            --bg: #f4f6fb
        }

        * {
            box-sizing: border-box
        }

        body {
            margin: 0;
            background: var(--bg);
            color: var(--ink);
            font: 14px Inter, "Segoe UI", Arial, sans-serif
        }

        .sidebar {
            position: fixed;
            inset: 0 auto 0 0;
            z-index: 2;
            width: 264px;
            min-width: 264px;
            max-width: 264px;
            flex: 0 0 264px;
            box-sizing: border-box;
            padding: 18px 14px;
            background: linear-gradient(180deg, var(--navy), var(--navy-dark));
            color: #fff;
            display: flex;
            flex-direction: column
        }

        .brand {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 0;
            padding: 5px 10px 28px
        }

        .brand>.petugas-logo {
            display: block;
            width: 112px;
            height: 56px;
            margin: 0 0 12px;
            border-radius: 10px;
            overflow: hidden;
            clip-path: inset(0 round 10px)
        }

        .brand img {
            display: block;
            width: 112px;
            height: 56px;
            object-fit: contain;
            border-radius: 10px
        }

        .brand b,
        .brand small,
        .brand span {
            display: block
        }

        .brand b {
            color: #f97316;
            font-size: 20px;
            font-weight: 800;
            line-height: 1.1
        }

        .brand small {
            margin-top: 3px;
            color: #f4f7ff;
            font-size: 15px;
            font-weight: 800;
            letter-spacing: .04em;
            line-height: 1.15
        }

        .brand>div span {
            margin-top: 7px;
            color: #dbeafe;
            font-size: 11px;
            line-height: 1.35
        }

        nav {
            display: grid;
            gap: 5px
        }

        nav p {
            margin: 18px 10px 10px;
            color: #dbeafe;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase
        }

        .menu {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 12px;
            border-radius: 10px;
            color: #f4f7ff;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none
        }

        .menu:hover,
        .menu.active {
            background: var(--blue)
        }

        .menu i {
            font-size: 20px
        }

        .access {
            margin-top: auto;
            padding: 15px;
            border: 1px solid var(--blue);
            border-radius: 16px;
            background: var(--navy-dark)
        }

        .access i {
            color: #86aefb
        }

        .access b,
        .access span,
        .access small {
            display: block
        }

        .access b {
            margin: 9px 0 4px;
            font-size: 12px
        }

        .access span,
        .access small {
            color: #dbeafe;
            font-size: 11px;
            line-height: 1.5
        }

        .access small {
            margin-top: 6px;
            font-size: 10px;
            font-weight: 700
        }

        .archive-status-badge {
            display: inline-block;
            padding: 5px 9px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700
        }

        .archive-status-badge.status-inaktif {
            background: #dcfce7 !important;
            color: #166534
        }

        .archive-status-badge.status-permanen {
            background: #f3e8ff !important;
            color: #6b21a8
        }

        .archive-status-badge.status-musnah {
            background: #fee2e2 !important;
            color: #991b1b
        }

        .archive-status-badge.status-retensi {
            background: #ffedd5 !important;
            color: #9a3412
        }

        .main {
            min-height: 100vh;
            margin-left: 264px
        }

        .header {
            min-height: 84px;
            padding: 14px 28px;
            border-bottom: 1px solid var(--line);
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px
        }

        .header-brand b,
        .header-brand small {
            display: block
        }

        .header-brand b {
            font-size: 18px
        }

        .header-brand small {
            margin-top: 3px;
            color: var(--muted);
            font-size: 11px
        }

        .user-menu {
            position: relative;
            margin-left: auto
        }

        .user-menu summary {
            display: flex;
            align-items: center;
            gap: 10px;
            list-style: none;
            padding: 8px 10px;
            border: 1px solid #d9e2f2;
            border-radius: 999px;
            background: #fff;
            color: #00173e;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: background .15s ease, box-shadow .15s ease, border-color .15s ease;
            white-space: nowrap
        }

        .user-menu summary::-webkit-details-marker {
            display: none
        }

        .user-menu summary:hover {
            border-color: #b9c8e6;
            box-shadow: 0 6px 16px #00000010;
            background: #f8fbff
        }

        .user-avatar {
            display: grid;
            place-items: center;
            overflow: hidden;
            width: 30px;
            height: 30px;
            border-radius: 999px;
            background: linear-gradient(135deg, #00173e, #1459cf);
            color: #fff;
            font-size: 12px;
            font-weight: 700
        }

        .user-avatar img {
            display: block !important;
            flex: 0 0 100% !important;
            width: 100% !important;
            min-width: 100% !important;
            max-width: 100% !important;
            height: 100% !important;
            min-height: 100% !important;
            max-height: 100% !important;
            object-fit: cover !important;
            border-radius: inherit
        }

        .user-summary-text {
            display: grid;
            gap: 2px;
            min-width: 0;
            text-align: left
        }

        .user-summary-text b,
        .user-summary-text small {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap
        }

        .user-summary-text small {
            color: #667085;
            font-size: 10px;
            font-weight: 500
        }

        .user-summary-chevron {
            color: #667085;
            font-size: 18px
        }

        .user-dropdown {
            position: absolute;
            right: 0;
            top: calc(100% + 10px);
            z-index: 30;
            width: min(280px, calc(100vw - 32px));
            padding: 8px;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            background: #fff;
            box-shadow: 0 14px 34px #00000018
        }

        .user-dropdown-header {
            display: grid;
            gap: 2px;
            padding: 10px 12px 12px
        }

        .user-dropdown-header b {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            color: #101828;
            font-size: 14px
        }

        .user-dropdown-header small {
            color: #667085;
            font-size: 11px;
            font-weight: 500
        }

        .user-dropdown a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: 10px;
            color: #101828;
            text-decoration: none;
            transition: background .15s ease, color .15s ease
        }

        .user-dropdown a:hover {
            background: #fff1f2;
            color: #b42318
        }

        .user-dropdown .material-symbols-outlined {
            font-size: 18px
        }

        .content {
            max-width: 1440px;
            padding: 32px 38px;
            margin: auto
        }

        .hero {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            align-items: center;
            margin-bottom: 22px
        }

        .hero h1 {
            margin: 0;
            font-size: 25px
        }

        .hero p {
            margin: 7px 0 0;
            color: var(--muted)
        }

        .hero .button {
            white-space: nowrap
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
            margin-bottom: 20px
        }

        .summary-card {
            padding: 18px 20px;
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 14px;
            box-shadow: 0 7px 22px #10234d08
        }

        .summary-card small {
            display: block;
            color: var(--muted);
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .04em
        }

        .summary-card strong {
            display: block;
            margin-top: 8px;
            font-size: 25px
        }

        .panel-title {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px
        }

        .panel-title h2 {
            margin-bottom: 0
        }

        .filters-panel {
            background: #f9fbff
        }

        .empty-state {
            padding: 54px 20px !important;
            text-align: center;
            color: var(--muted)
        }

        .empty-state strong {
            display: block;
            margin-bottom: 6px;
            color: var(--ink);
            font-size: 15px
        }

        .empty-state .material-symbols-outlined {
            display: block;
            margin-bottom: 12px;
            color: #9bb3d8;
            font-size: 42px
        }

        @media(max-width:560px) {
            .summary-grid {
                grid-template-columns: 1fr 1fr
            }

            .summary-card:last-child {
                grid-column: span 2
            }
        }

        .panel {
            padding: 22px;
            margin-bottom: 20px;
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 14px;
            box-shadow: 0 7px 22px #10234d08
        }

        .panel h2 {
            margin: 0 0 18px;
            font-size: 16px
        }

        .form-grid,
        .filters {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px
        }

        .form-grid .wide {
            grid-column: span 2
        }

        label {
            display: grid;
            gap: 6px;
            color: #506078;
            font-size: 12px;
            font-weight: 700
        }

        input,
        select,
        textarea,
        button {
            font: inherit
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 10px 11px;
            border: 1px solid #dce4ee;
            border-radius: 8px;
            background: #fff;
            color: var(--ink)
        }

        textarea {
            min-height: 80px;
            resize: vertical
        }

        .actions {
            display: flex;
            gap: 10px;
            align-items: end
        }

        .button {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            padding: 10px 14px;
            border: 0;
            border-radius: 8px;
            background: var(--blue);
            color: #fff;
            text-decoration: none;
            font-weight: 700;
            cursor: pointer
        }

        .button.secondary {
            background: #eef4fb;
            color: #1459a5
        }

        .notice,
        .alert {
            padding: 12px 14px;
            border-radius: 9px
        }

        .notice {
            background: #dcfce7;
            color: #166534
        }

        .alert {
            background: #fee2e2;
            color: #b91c1c
        }

        .table-wrap {
            overflow: auto
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 800px
        }

        th,
        td {
            padding: 13px 10px;
            border-bottom: 1px solid var(--line);
            text-align: left
        }

        th {
            color: var(--muted);
            font-size: 11px;
            text-transform: uppercase
        }

        td a {
            color: #1463b8;
            font-weight: 700;
            text-decoration: none
        }

        .badge {
            padding: 5px 9px;
            border-radius: 999px;
            background: #eaf3ff;
            color: #1459a5;
            font-size: 11px;
            font-weight: 800
        }

        .empty {
            text-align: center;
            color: var(--muted)
        }

        @media(max-width:900px) {
            .sidebar {
                position: static;
                width: auto
            }

            .main {
                margin: 0
            }

            .header,
            .content {
                padding-left: 20px;
                padding-right: 20px
            }

            .form-grid,
            .filters {
                grid-template-columns: repeat(2, minmax(0, 1fr))
            }

            .sidebar nav {
                display: flex;
                overflow: auto
            }

            .access {
                display: none
            }
        }

        @media(max-width:560px) {
            .header {
                height: auto;
                padding-top: 14px;
                padding-bottom: 14px
            }

            .content {
                padding: 20px
            }

            .hero {
                display: block
            }

            .hero h1 {
                font-size: 21px
            }

            .form-grid,
            .filters {
                grid-template-columns: 1fr
            }

            .form-grid .wide {
                grid-column: auto
            }

            .actions {
                align-items: stretch
            }

            .button {
                width: 100%
            }
        }

        @media(max-width:760px) {
            .user-avatar img {
                display: block !important;
                flex: 0 0 100% !important;
                width: 100% !important;
                min-width: 100% !important;
                max-width: 100% !important;
                height: 100% !important;
                min-height: 100% !important;
                max-height: 100% !important;
                object-fit: cover !important;
                border-radius: inherit
            }

            .user-summary-text {
                display: none
            }

            .user-menu summary {
                padding: 7px
            }

            .user-summary-chevron {
                display: none
            }
        }

        .content {
            max-width: 1540px;
            margin: 0 auto;
            padding: 28px 32px 44px
        }

        .hero {
            align-items: flex-start;
            margin-bottom: 22px
        }

        .hero h1 {
            margin: 5px 0 8px;
            font-size: 28px;
            letter-spacing: -.035em
        }

        .hero p {
            margin: 0;
            color: var(--muted);
            font-size: 14px
        }

        .breadcrumb {
            font-size: 11px;
            font-weight: 700;
            color: #8a97ae;
            letter-spacing: .04em;
            text-transform: uppercase
        }

        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            min-height: 40px;
            padding: 0 15px;
            border: 0;
            border-radius: 10px;
            background: #f97316;
            color: #fff;
            font: 700 13px Inter, "Segoe UI", Arial, sans-serif;
            text-decoration: none;
            box-shadow: 0 7px 16px #f9731633;
            cursor: pointer;
            transition: transform .15s ease, background .15s ease, box-shadow .15s ease
        }

        .button:hover {
            background: #ea670b;
            box-shadow: 0 9px 20px #f9731644;
            transform: translateY(-1px)
        }

        .button:active {
            transform: translateY(0);
            box-shadow: none
        }

        .stat-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
            margin-bottom: 16px
        }

        .stat {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 18px;
            border: 1px solid var(--line);
            border-radius: 16px;
            background: #fff;
            box-shadow: 0 1px 2px #0f172a0a, 0 10px 24px #0f172a06
        }

        .stat small,
        .stat strong {
            display: block
        }

        .stat small {
            color: #7182a1;
            font-size: 12px
        }

        .stat strong {
            margin-top: 7px;
            font-size: 28px
        }

        .stat i {
            display: grid;
            place-items: center;
            width: 50px;
            height: 50px;
            border: 1px solid;
            border-radius: 14px;
            font-size: 24px
        }

        .s-blue i {
            background: #eff6ff;
            color: #2563eb;
            border-color: #dbeafe
        }

        .s-cyan i {
            background: #ecfeff;
            color: #0891b2;
            border-color: #cffafe
        }

        .s-purple i {
            background: #f5f3ff;
            color: #7c3aed;
            border-color: #e9d5ff
        }

        .s-red i {
            background: #fef2f2;
            color: #dc2626;
            border-color: #fee2e2
        }

        @media(max-width:1050px) {
            .stat-grid {
                grid-template-columns: repeat(2, 1fr)
            }
        }

        @media(max-width:760px) {
            .stat-grid {
                grid-template-columns: 1fr
            }
        }

        .panel {
            overflow: hidden;
            margin-bottom: 20px;
            border: 1px solid var(--line);
            border-radius: 14px;
            background: #fff;
            box-shadow: 0 5px 18px #102a5608
        }

        .filters-panel {
            padding: 0
        }

        .panel-title {
            padding: 18px 20px;
            border-bottom: 1px solid var(--line)
        }

        .panel-title h2 {
            font-size: 15px;
            letter-spacing: -.015em
        }

        .panel-subtitle {
            color: var(--muted);
            font-size: 11px;
            font-weight: 500
        }

        .filters {
            display: grid;
            grid-template-columns: minmax(240px, 2fr) repeat(3, minmax(140px, 1fr)) minmax(130px, 1fr) minmax(130px, 1fr) auto;
            gap: 12px;
            padding: 18px 20px;
            background: #fbfcfe
        }

        .filters label {
            gap: 6px;
            color: #53617b;
            font-size: 11px;
            font-weight: 700
        }

        .filters input,
        .filters select,
        .form-grid input,
        .form-grid select,
        .form-grid textarea {
            min-height: 40px;
            border: 1px solid #d9e2f0;
            border-radius: 9px;
            background: #fff;
            color: var(--ink);
            font: 13px Inter, "Segoe UI", Arial, sans-serif
        }

        .filters input:focus,
        .filters select:focus,
        .form-grid input:focus,
        .form-grid select:focus,
        .form-grid textarea:focus {
            outline: 0;
            border-color: #f97316;
            box-shadow: 0 0 0 3px #f9731620
        }

        .filters label:first-child {
            position: relative
        }

        .filters label:first-child:before {
            content: "search";
            position: absolute;
            left: 10px;
            bottom: 10px;
            color: #74829b;
            font-family: "Material Symbols Outlined";
            font-size: 19px
        }

        .filters label:first-child input {
            padding-left: 38px
        }

        .filters .actions {
            align-self: end
        }

        .filters .button {
            min-width: 92px
        }

        .filters.search-only {
            grid-template-columns: 420px auto;
            align-items: end
        }

        .filters.search-only label {
            max-width: 420px
        }

        .filters.search-only .actions {
            display: flex;
            gap: 9px
        }

        .filters.search-only .secondary {
            background: #eef3f9;
            color: #506078;
            box-shadow: none
        }

        @media(max-width:760px) {
            .filters.search-only {
                grid-template-columns: 1fr
            }

            .filters.search-only .actions {
                width: 100%
            }

            .filters.search-only .button {
                flex: 1
            }
        }

        .table-wrap {
            overflow-x: auto
        }

        .table-wrap table {
            min-width: 900px;
            border-collapse: separate;
            border-spacing: 0
        }

        .table-wrap th {
            padding: 13px 16px;
            background: #f8fafc;
            color: #65738c;
            font-size: 10px;
            letter-spacing: .06em;
            text-transform: uppercase
        }

        .table-wrap td {
            padding: 15px 16px;
            border-top: 1px solid #eef2f7;
            color: #2b3b59;
            vertical-align: middle
        }

        .table-wrap tbody tr {
            transition: background .15s ease
        }

        .table-wrap tbody tr:hover {
            background: #f8fbff
        }

        .archive-status-badge {
            display: inline-flex;
            align-items: center;
            min-height: 25px;
            padding: 3px 9px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700
        }

        .status-inaktif {
            background: #e9f8ef;
            color: #168044
        }

        .status-retensi {
            background: #fff5df;
            color: #b66300
        }

        .status-permanen {
            background: #eaf2ff;
            color: #1c63c7
        }

        .status-musnah {
            background: #fceced;
            color: #c2414b
        }

        .empty-state {
            padding: 52px 20px !important;
            color: #75829a !important;
            text-align: center
        }

        .empty-state .material-symbols-outlined {
            display: block;
            margin-bottom: 9px;
            color: #a6b2c6;
            font-size: 34px
        }

        .empty-state strong {
            display: block;
            margin-bottom: 5px;
            color: #40516d;
            font-size: 14px
        }

        .table-wrap a:not(.button) {
            display: inline-flex;
            align-items: center;
            min-height: 31px;
            margin: 2px;
            padding: 0 9px;
            border: 1px solid #dce5f2;
            border-radius: 8px;
            color: #195bbd;
            font-size: 11px;
            font-weight: 700;
            text-decoration: none;
            transition: all .15s ease
        }

        .table-wrap a:not(.button):hover {
            border-color: #b9cdea;
            background: #edf4ff
        }

        .form-grid {
            padding: 20px
        }

        .form-grid .actions {
            padding-top: 6px
        }

        .form-section {
            padding: 20px;
            border-top: 1px solid var(--line)
        }

        .form-section:first-of-type {
            border-top: 0
        }

        .form-section-header {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin-bottom: 16px
        }

        .form-section-header .material-symbols-outlined {
            display: grid;
            place-items: center;
            width: 34px;
            height: 34px;
            border-radius: 10px;
            background: #eff6ff;
            color: #2563eb;
            font-size: 19px
        }

        .form-section-header h3 {
            margin: 0;
            color: var(--ink);
            font-size: 14px
        }

        .form-section-header p {
            margin: 4px 0 0;
            color: var(--muted);
            font-size: 11px;
            font-weight: 500
        }

        .form-section .form-grid {
            padding: 0;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px
        }

        .form-section .form-grid .wide {
            grid-column: 1/-1
        }

        .form-section.retention-section {
            background: #fffaf4
        }

        .form-section.retention-section .form-section-header .material-symbols-outlined {
            background: #fff1df;
            color: #d97706
        }

        .form-section.detail-section textarea {
            min-height: 112px
        }

        .form-field {
            min-width: 0
        }

        .form-field input,
        .form-field select,
        .form-field textarea {
            margin-top: 1px
        }

        .form-field small {
            color: var(--muted);
            font-size: 10px;
            font-weight: 500
        }

        .status-select.status-inaktif {
            border-color: #a7dfba;
            background: #f3fcf6;
            color: #168044
        }

        .status-select.status-permanen {
            border-color: #c8b8ee;
            background: #faf7ff;
            color: #7048b8
        }

        .status-select.status-musnah {
            border-color: #efb4b9;
            background: #fff7f7;
            color: #c2414b
        }

        .form-actions {
            justify-content: flex-end;
            padding: 20px;
            border-top: 1px solid var(--line)
        }

        .form-actions .button {
            width: auto
        }

        .form-actions .button.secondary {
            box-shadow: none
        }

        .archive-upload {
            display: grid;
            gap: 10px;
            min-width: 0
        }

        .archive-upload-area {
            display: grid;
            justify-items: center;
            gap: 8px;
            min-height: 174px;
            padding: 22px 18px;
            border: 1px dashed #b9cbe5;
            border-radius: 12px;
            background: #f8fbff;
            text-align: center;
            transition: border-color .15s ease, background .15s ease, box-shadow .15s ease
        }

        .archive-upload-area:hover,
        .archive-upload-area:focus-within {
            border-color: #2563eb;
            background: #f4f8ff;
            box-shadow: 0 0 0 3px #2563eb12
        }

        .archive-upload-icon {
            display: grid;
            place-items: center;
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: #eaf2ff;
            color: #2563eb
        }

        .archive-upload-icon .material-symbols-outlined {
            font-size: 23px
        }

        .archive-upload-title {
            color: var(--ink);
            font-size: 13px;
            font-weight: 700
        }

        .archive-upload-text,
        .archive-upload-helper {
            color: var(--muted);
            font-size: 11px;
            font-weight: 500
        }

        .archive-upload-input {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0
        }

        .archive-upload-button {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            min-height: 36px;
            padding: 0 12px;
            border: 1px solid #c8d8ef;
            border-radius: 9px;
            background: #fff;
            color: #1459a5;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: border-color .15s ease, background .15s ease, color .15s ease
        }

        .archive-upload-button:hover {
            border-color: #8db1df;
            background: #edf4ff;
            color: #07306b
        }

        .archive-upload-button:focus-visible {
            outline: 3px solid #bfdbfe;
            outline-offset: 2px
        }

        .archive-upload-button .material-symbols-outlined {
            font-size: 17px
        }

        .archive-upload-file {
            display: grid;
            grid-template-columns: auto minmax(0, 1fr);
            gap: 9px;
            align-items: center;
            padding: 10px 12px;
            border: 1px solid #d9e5f3;
            border-radius: 10px;
            background: #fff
        }

        .archive-upload-file[hidden] {
            display: none
        }

        .archive-upload-file .material-symbols-outlined {
            color: #2563eb;
            font-size: 21px
        }

        .archive-upload-file-details {
            min-width: 0;
            text-align: left
        }

        .archive-upload-file-name {
            overflow: hidden;
            color: var(--ink);
            font-size: 12px;
            font-weight: 700;
            text-overflow: ellipsis;
            white-space: nowrap
        }

        .archive-upload-file-size {
            display: block;
            margin-top: 2px;
            color: var(--muted);
            font-size: 10px;
            font-weight: 500
        }

        .document-subsection {
            margin: 0 20px 20px;
            padding: 18px;
            border: 1px solid var(--line);
            border-radius: 12px;
            background: #f9fbff
        }

        .document-heading {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin-bottom: 14px
        }

        .document-heading .material-symbols-outlined {
            display: grid;
            place-items: center;
            width: 34px;
            height: 34px;
            border-radius: 10px;
            background: #eef4fb;
            color: #1459a5;
            font-size: 19px
        }

        .document-heading h3 {
            margin: 0;
            color: var(--ink);
            font-size: 14px
        }

        .document-heading p {
            margin: 4px 0 0;
            color: var(--muted);
            font-size: 11px;
            font-weight: 500
        }

        .document-upload {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 14px;
            align-items: end
        }

        .document-upload .wide {
            grid-column: auto
        }

        @media(max-width:760px) {
            .form-section {
                padding: 17px
            }

            .form-section .form-grid {
                grid-template-columns: 1fr
            }

            .form-section .form-grid .wide {
                grid-column: auto
            }

            .form-actions {
                justify-content: stretch
            }

            .form-actions .button {
                width: 100%
            }
        }

        @media(max-width:1200px) {
            .filters {
                grid-template-columns: repeat(3, minmax(160px, 1fr))
            }

            .filters label:first-child {
                grid-column: span 3
            }
        }

        @media(max-width:760px) {
            .content {
                padding: 20px 16px 32px
            }

            .hero {
                display: grid;
                gap: 14px
            }

            .hero h1 {
                font-size: 24px
            }

            .hero .button {
                width: 100%
            }

            .summary-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 10px
            }

            .summary-card {
                min-height: 88px;
                padding: 14px
            }

            .filters {
                grid-template-columns: 1fr;
                padding: 15px
            }

            .filters label:first-child {
                grid-column: auto
            }

            .filters .actions,
            .filters .button {
                width: 100%
            }

            .panel-title {
                padding: 15px
            }

            .table-wrap th,
            .table-wrap td {
                padding: 13px 14px
            }
        }

        h1,
        h2,
        h3,
        .section-title {
            color: var(--ink)
        }

        p,
        small,
        .muted {
            color: var(--muted)
        }

        a:not(.menu) {
            color: var(--blue)
        }

        .content .button,
        .content .btn,
        .content .primary-button {
            background: var(--blue);
            color: #fff
        }

        .content .button:hover,
        .content .btn:hover,
        .content .primary-button:hover,
        .content .button:focus-visible,
        .content .btn:focus-visible,
        .content .primary-button:focus-visible {
            background: var(--navy);
            outline: 3px solid #bfdbfe;
            outline-offset: 2px
        }

        .content .button:disabled,
        .content .btn:disabled,
        .content .primary-button:disabled {
            background: var(--muted);
            color: #fff;
            cursor: not-allowed;
            opacity: .7
        }

        .badge,
        .status,
        .icon-box,
        .chart-icon {
            color: var(--blue)
        }

        .progress-bar,
        .progress-fill {
            background: var(--blue)
        }
    </style>
</head>

<body>
    <aside class="sidebar">
        <div class="brand"><span class="petugas-logo"><img src="../../assets/images/logofix.png" alt="KAI"></span>
            <div><b>e-ARSIP</b><small>KAI DIVRE III</small><span>Web Pengarsipan Digital</span></div>
        </div>
        <nav>
            <p>Petugas Unit</p><a class="menu" href="dashboard.php"><span class="material-symbols-outlined">dashboard</span>Dashboard</a><a class="menu active" href="arsip.php"><span class="material-symbols-outlined">folder_open</span>Arsip Digital</a><a class="menu" href="../shared/jadwal-pemusnahan.php"><span class="material-symbols-outlined">event</span>Jadwal Pemusnahan</a>
        </nav>
        <section class="access"><i class="material-symbols-outlined">verified_user</i><b>Akses Petugas</b><span>Anda login sebagai Petugas.</span><span>Kelola arsip sesuai unit kerja Anda.</span><small>Unit scoped</small></section>
    </aside>
    <div class="main">
        <header class="header">
            <div class="header-brand"><b>e-ARSIP KAI DIVRE III</b><small>ARSIP DIGITAL PETUGAS</small></div>
            <details class="user-menu">
                <summary><span class="user-avatar"><?= profileAvatar('../..', $name) ?></span><span class="user-summary-text"><b><?= e($name) ?></b><small>Petugas</small></span><i class="user-summary-chevron material-symbols-outlined">expand_more</i></summary>
                <div class="user-dropdown">
                    <div class="user-dropdown-header"><b><?= e($name) ?></b><small>Petugas</small></div><a href="../profile/ubah-profil.php"><i class="material-symbols-outlined">person</i>Ubah Profil</a><a href="../profile/ubah-password.php"><i class="material-symbols-outlined">key</i>Ubah Password</a><?= logoutForm('../../logout.php') ?>
                </div>
            </details>
        </header>
        <main class="content">
            <div class="hero">
                <div><small class="breadcrumb">Dashboard / Arsip Digital</small>
                    <h1>Arsip Digital</h1>
                    <p>Kelola dan pantau arsip digital sesuai unit kerja Anda.</p>
                </div><a class="button" href="#form-arsip"><span class="material-symbols-outlined" style="font-size:18px;margin-right:6px">add</span>Tambah Arsip</a>
            </div>
            <section class="stat-grid">
                <article class="stat s-blue">
                    <div><small>Total Arsip</small><strong><?= count($items) ?></strong></div><i class="material-symbols-outlined">inventory_2</i>
                </article>
                <article class="stat s-cyan">
                    <div><small>Arsip Inaktif</small><strong><?= count(array_filter($items, static fn($item): bool => $item['status_arsip'] === 'Inaktif')) ?></strong></div><i class="material-symbols-outlined">task_alt</i>
                </article>
                <article class="stat s-purple">
                    <div><small>Permanen</small><strong><?= count(array_filter($items, static fn($item): bool => $item['status_arsip'] === 'Permanen')) ?></strong></div><i class="material-symbols-outlined">lock</i>
                </article>
            </section><?php if ($notice): ?><p class="notice"><?= e($notice) ?></p><?php endif; ?><?php if ($errors): ?><div class="alert"><?php foreach ($errors as $error): ?><div><?= e($error) ?></div><?php endforeach; ?></div><?php endif; ?><section class="panel" id="form-arsip">
                <div class="panel-title">
                    <h2><?= $edit ? 'Edit' : 'Tambah' ?> Arsip</h2><span class="panel-subtitle">Masukkan informasi arsip dengan lengkap dan benar.</span>
                </div>
                <form method="post" enctype="multipart/form-data"><input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>"><input type="hidden" name="action" value="<?= $edit ? 'update' : 'create' ?>"><?php if ($edit): ?><input type="hidden" name="arsip_id" value="<?= (int)$edit['arsip_id'] ?>"><?php endif; ?><section class="form-section">
                        <div class="form-section-header"><span class="material-symbols-outlined">description</span>
                            <div>
                                <h3>Informasi Arsip</h3>
                                <p>Identitas surat dan dokumen arsip.</p>
                            </div>
                        </div>
                        <div class="form-grid"><label class="form-field">No. Registrasi<input name="no_registrasi" required value="<?= e($edit['no_registrasi'] ?? '') ?>" <?= $edit ? 'readonly' : '' ?>></label><label class="form-field">No. Surat<input name="no_surat" required value="<?= e($edit['no_surat'] ?? '') ?>"></label><label class="form-field wide">Perihal<input name="perihal" required value="<?= e($edit['perihal'] ?? '') ?>"></label><label class="form-field">Tanggal Surat<input id="tanggal-surat" type="date" name="tanggal_surat" required value="<?= e($edit['tanggal_surat'] ?? '') ?>"></label><label class="form-field">Jenis Surat<select name="jenis_surat_id" required><?php foreach ($jenis as $x): ?><option value="<?= (int)$x['jenis_surat_id'] ?>" <?= ((int)($edit['jenis_surat_id'] ?? 0) === (int)$x['jenis_surat_id']) ? 'selected' : '' ?>><?= e($x['nama_jenis']) ?></option><?php endforeach; ?></select></label></div>
                    </section>
                    <section class="form-section">
                        <div class="form-section-header"><span class="material-symbols-outlined">folder_managed</span>
                            <div>
                                <h3>Klasifikasi &amp; Penyimpanan</h3>
                                <p>Atur klasifikasi dan lokasi penyimpanan fisik.</p>
                            </div>
                        </div>
                        <div class="form-grid"><label class="form-field">Klasifikasi<select name="klasifikasi_id">
                                    <option value="">-</option><?php foreach ($klas as $x): ?><option value="<?= (int)$x['klasifikasi_id'] ?>" <?= ((int)($edit['klasifikasi_id'] ?? 0) === (int)$x['klasifikasi_id']) ? 'selected' : '' ?>><?= e($x['kode_klasifikasi'] . ' - ' . $x['nama_klasifikasi']) ?></option><?php endforeach; ?>
                                </select></label><label class="form-field">Rak<select name="rak_id">
                                    <option value="">-</option><?php foreach ($locations as $x): ?><option value="<?= (int)$x['rak_id'] ?>" <?= ((int)($edit['rak_id'] ?? 0) === (int)$x['rak_id']) ? 'selected' : '' ?>><?= e($x['nama_ruangan'] . ' / ' . $x['nama_lemari'] . ' / ' . $x['kode_rak']) ?></option><?php endforeach; ?>
                                </select></label></div>
                    </section>
                    <section class="form-section retention-section">
                        <div class="form-section-header"><span class="material-symbols-outlined">schedule</span>
                            <div>
                                <h3>Retensi Arsip</h3>
                                <p>Informasi masa simpan dan tanggal jatuh tempo.</p>
                            </div>
                        </div>
                        <div class="form-grid"><label class="form-field">Status<select class="status-select status-<?= strtolower((string)($edit['status_arsip'] ?? 'Inaktif')) ?>" name="status_arsip"><?php foreach (['Inaktif', 'Permanen', 'Musnah'] as $x): ?><option <?= $x === ($edit['status_arsip'] ?? 'Inaktif') ? 'selected' : '' ?>><?= $x ?></option><?php endforeach; ?></select></label><label class="form-field">Masa Simpan Tahun<input id="masa-simpan-tahun" type="number" min="1" max="10" name="masa_simpan_tahun" value="<?= e((string)($edit['masa_simpan_tahun'] ?? '')) ?>"><small>Masukkan jumlah tahun sesuai ketentuan.</small></label><label class="form-field">Jatuh Tempo<input id="tanggal-jatuh-tempo" type="date" name="tanggal_jatuh_tempo" value="<?= e($edit['tanggal_jatuh_tempo'] ?? '') ?>"></label></div>
                    </section>
                    <section class="form-section detail-section">
                        <div class="form-section-header"><span class="material-symbols-outlined">notes</span>
                            <div>
                                <h3>Detail Arsip</h3>
                                <p>Tambahkan informasi pendukung jika diperlukan.</p>
                            </div>
                        </div>
                        <div class="form-grid"><label class="form-field wide">Detail<textarea name="detail_arsip"><?= e($edit['detail_arsip'] ?? '') ?></textarea></label><label class="form-field wide">Keterangan<textarea name="keterangan"><?= e($edit['keterangan'] ?? '') ?></textarea></label><?php if (!$edit): ?><div class="form-field wide archive-upload"><span>Dokumen Arsip / Upload File</span><div class="archive-upload-area"><span class="archive-upload-icon"><span class="material-symbols-outlined">upload_file</span></span><span class="archive-upload-title">Pilih dokumen untuk diunggah</span><span class="archive-upload-text">PDF, JPG, JPEG, PNG, XLS, XLSX · Maks. 20 MB</span><input class="archive-upload-input" id="archive-document" type="file" name="document" accept=".pdf,.jpg,.jpeg,.png,.xls,.xlsx"><label class="archive-upload-button" for="archive-document"><span class="material-symbols-outlined">upload</span>Pilih File</label></div><div class="archive-upload-file" id="document-file" hidden><span class="material-symbols-outlined">description</span><span class="archive-upload-file-details"><span class="archive-upload-file-name" id="document-name"></span><small class="archive-upload-file-size" id="document-size"></small></span></div><small class="archive-upload-helper">Opsional · PDF, JPG, JPEG, PNG, XLS, atau XLSX · Maks. 20 MB</small></div><?php endif; ?></div>
                    </section>
                    <div class="actions form-actions"><button class="button"><span class="material-symbols-outlined">save</span><?= $edit ? 'Simpan Perubahan' : 'Simpan Arsip' ?></button><?php if ($edit): ?><a class="button secondary" href="arsip.php"><span class="material-symbols-outlined">close</span>Batal</a><?php endif; ?></div>
                </form><?php if ($edit): ?><div class="document-subsection">
                        <div class="document-heading"><span class="material-symbols-outlined">attach_file</span>
                            <div>
                                <h3>Dokumen Arsip</h3>
                                <p>Tambahkan dokumen pendukung arsip.</p>
                            </div>
                        </div>
                        <form method="post" action="../../actions/upload-arsip.php" enctype="multipart/form-data" class="document-upload"><input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>"><input type="hidden" name="arsip_id" value="<?= (int)$edit['arsip_id'] ?>"><label class="wide">Pilih File<input type="file" name="document" accept=".pdf,.jpg,.jpeg,.png,.xls,.xlsx" required></label>
                            <div class="actions"><button class="button">Upload</button><a class="button secondary" href="../../api/arsip-detail.php?id=<?= (int)$edit['arsip_id'] ?>">Detail</a></div>
                        </form>
                    </div><?php endif; ?>
            </section>
            <section class="panel filters-panel">
                <div class="panel-title">
                    <h2>Cari Arsip</h2><span class="badge"><?= count($items) ?> arsip ditemukan</span>
                </div>
                <form method="get" class="filters search-only"><label>Cari berdasarkan Nomor Surat<input name="q" value="<?= e($q) ?>" placeholder="Cari nomor surat..."></label>
                    <div class="actions"><button class="button"><span class="material-symbols-outlined">search</span>Cari</button><a class="button secondary" href="arsip.php">Reset</a></div>
                </form>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Registrasi</th>
                                <th>Surat</th>
                                <th>Perihal</th>
                                <th>Jenis</th>
                                <th>Klasifikasi</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody><?php foreach ($items as $x): ?><tr>
                                    <td><?= e($x['no_registrasi']) ?></td>
                                    <td><?= e($x['no_surat']) ?></td>
                                    <td><?= e($x['perihal']) ?></td>
                                    <td><?= e($x['nama_jenis']) ?></td>
                                    <td><?= e($x['nama_klasifikasi'] ?? '-') ?></td>
                                    <td><?= e($x['tanggal_surat']) ?></td>
                                    <td><?php $statusClass = ['Inaktif' => 'aktif', 'Permanen' => 'permanen', 'Musnah' => 'musnah', 'Dalam Retensi' => 'retensi'][$x['status_arsip']] ?? ''; ?><span class="archive-status-badge<?= $statusClass !== '' ? ' status-' . $statusClass : '' ?>"><?= e($x['status_arsip']) ?></span></td>
                                    <td><a href="arsip.php?edit=<?= (int)$x['arsip_id'] ?>">Edit</a> · <a href="../../api/arsip-detail.php?id=<?= (int)$x['arsip_id'] ?>">Detail</a></td>
                                </tr><?php endforeach; ?><?php if (!$items): ?><tr>
                                    <td colspan="8" class="empty-state"><span class="material-symbols-outlined">inventory_2</span><strong>Belum ada arsip digital</strong>Arsip yang sesuai pencarian atau filter akan tampil di sini.</td>
                                </tr><?php endif; ?></tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>
    <div class="archive-confirm-modal" id="archive-confirm-modal" hidden>
        <div class="archive-confirm-dialog" role="dialog" aria-modal="true" aria-labelledby="archive-confirm-title" aria-describedby="archive-confirm-description">
            <div class="archive-confirm-icon"><i class="material-symbols-outlined">task_alt</i></div>
            <h2 id="archive-confirm-title">Simpan Arsip?</h2>
            <p id="archive-confirm-description">Pastikan data arsip yang dimasukkan sudah benar sebelum disimpan.</p>
            <div class="archive-confirm-actions"><button type="button" class="archive-confirm-cancel">Batal</button><button type="button" class="archive-confirm-submit"><i class="material-symbols-outlined">save</i>Simpan Arsip</button></div>
        </div>
    </div>
    <style>
        .archive-confirm-modal {
            position: fixed;
            inset: 0;
            z-index: 100;
            display: grid;
            place-items: center;
            padding: 20px;
            background: #0f172a80;
            backdrop-filter: blur(3px)
        }

        .archive-confirm-modal[hidden] {
            display: none
        }

        .archive-confirm-dialog {
            width: min(420px, 100%);
            padding: 28px;
            border: 1px solid #e6ebf3;
            border-radius: 20px;
            background: #fff;
            box-shadow: 0 24px 64px #0f172a40;
            text-align: center
        }

        .archive-confirm-icon {
            display: grid;
            place-items: center;
            width: 52px;
            height: 52px;
            margin: 0 auto 16px;
            border-radius: 16px;
            background: #fff3e8;
            color: #f97316
        }

        .archive-confirm-icon i {
            font-size: 29px
        }

        .archive-confirm-dialog h2 {
            margin: 0;
            color: var(--ink);
            font-size: 20px
        }

        .archive-confirm-dialog p {
            margin: 9px 0 24px;
            color: var(--muted);
            font-size: 13px;
            line-height: 1.6
        }

        .archive-confirm-actions {
            display: flex;
            justify-content: center;
            gap: 10px
        }

        .archive-confirm-actions button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            min-height: 42px;
            padding: 0 17px;
            border-radius: 11px;
            font: 600 13px Inter, "Segoe UI", Arial, sans-serif;
            cursor: pointer
        }

        .archive-confirm-cancel {
            border: 1px solid #d9e2f2;
            background: #fff;
            color: var(--ink)
        }

        .archive-confirm-submit {
            border: 1px solid #ea6b00;
            background: #f97316;
            color: #fff;
            box-shadow: 0 5px 12px #f9731640
        }

        .archive-confirm-submit:hover {
            background: #ea6b00
        }

        .archive-confirm-actions button:disabled {
            cursor: wait;
            opacity: .7
        }

        @media(max-width:480px) {
            .archive-confirm-modal {
                padding: 16px
            }

            .archive-confirm-dialog {
                padding: 24px 20px
            }

            .archive-confirm-actions {
                flex-direction: column-reverse
            }

            .archive-confirm-actions button {
                width: 100%
            }
        }
    </style>
    <script>
        const archiveForm = document.querySelector('#form-arsip form[method="post"]'),
            archiveModal = document.getElementById('archive-confirm-modal'),
            archiveCancel = archiveModal.querySelector('.archive-confirm-cancel'),
            archiveSubmit = archiveModal.querySelector('.archive-confirm-submit');
        const documentInput = document.getElementById('archive-document'), documentFile = document.getElementById('document-file'), documentName = document.getElementById('document-name'), documentSize = document.getElementById('document-size');
        if (documentInput && documentFile && documentName && documentSize) documentInput.addEventListener('change', function() {
            const file = this.files[0];
            documentFile.hidden = !file;
            if (!file) return;
            documentName.textContent = file.name;
            documentSize.textContent = file.size >= 1024 * 1024 ? `${(file.size / 1024 / 1024).toFixed(1)} MB` : `${Math.max(1, Math.round(file.size / 1024))} KB`
        });
        const tanggalSurat = document.getElementById('tanggal-surat'), masaSimpanTahun = document.getElementById('masa-simpan-tahun'), tanggalJatuhTempo = document.getElementById('tanggal-jatuh-tempo'), statusArsip = archiveForm?.querySelector('[name="status_arsip"]');
        function isiTanggalJatuhTempo() {
            const years = Number(masaSimpanTahun?.value);
            if (!tanggalSurat?.value || statusArsip?.value !== 'Inaktif' || !Number.isInteger(years) || years < 1 || years > 10) {
                if (tanggalJatuhTempo && (!statusArsip?.value || statusArsip.value === 'Inaktif')) tanggalJatuhTempo.value = '';
                return;
            }
            const [year, month, day] = tanggalSurat.value.split('-').map(Number), due = new Date(year + years, month - 1, day);
            if (due.getMonth() !== month - 1) due.setDate(0);
            tanggalJatuhTempo.value = `${due.getFullYear()}-${String(due.getMonth() + 1).padStart(2, '0')}-${String(due.getDate()).padStart(2, '0')}`;
        }
        [tanggalSurat, masaSimpanTahun, statusArsip].forEach(input => input?.addEventListener('input', isiTanggalJatuhTempo));
        [tanggalSurat, masaSimpanTahun, statusArsip].forEach(input => input?.addEventListener('change', isiTanggalJatuhTempo));
        isiTanggalJatuhTempo();
        let archiveConfirmed = false;
        if (archiveForm) {
            archiveForm.addEventListener('submit', function(event) {
                if (archiveConfirmed) return;
                event.preventDefault();
                archiveModal.hidden = false;
                archiveSubmit.focus()
            });
            archiveCancel.addEventListener('click', function() {
                archiveModal.hidden = true
            });
            archiveModal.addEventListener('click', function(event) {
                if (event.target === archiveModal) archiveModal.hidden = true
            });
            archiveSubmit.addEventListener('click', function() {
                archiveConfirmed = true;
                archiveSubmit.disabled = true;
                archiveCancel.disabled = true;
                archiveForm.submit()
            });
            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape' && !archiveModal.hidden) {
                    archiveModal.hidden = true;
                    archiveForm.querySelector('[type="submit"]').focus()
                }
            })
        }
    </script>
</body>

</html>