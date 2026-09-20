<?php
declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/config/database.php';
require_once dirname(__DIR__, 2) . '/includes/auth.php';
requireLogin();

$userId = (int) $_SESSION['user_id'];
$roleId = (int) $_SESSION['role_id'];
$errors = [];
$notice = '';
$statement = $pdo->prepare('SELECT u.username, u.nama_lengkap, u.email, uk.nama_unit, r.role_name FROM users u JOIN roles r ON r.role_id = u.role_id LEFT JOIN unit_kerja uk ON uk.unit_id = u.unit_id WHERE u.user_id = :user_id AND u.role_id = :role_id AND u.is_active = 1');
$statement->execute(['user_id' => $userId, 'role_id' => $roleId]);
$user = $statement->fetch();
if (!$user) {
    session_unset();
    session_destroy();
    header('Location: ../../login.php');
    exit;
}
$photoStatement = $pdo->prepare('SELECT file_path, updated_at FROM profile_photos WHERE user_id = :user_id');
$photoStatement->execute(['user_id' => $userId]);
$photoRecord = $photoStatement->fetch() ?: [];
$profilePhoto = (string) ($photoRecord['file_path'] ?? '');
$photoVersion = $profilePhoto ? (string) strtotime((string) ($photoRecord['updated_at'] ?? '')) : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken()) {
        $errors[] = 'Permintaan tidak valid.';
    } else {
        $action = (string) ($_POST['action'] ?? '');
        if ($action === 'profile') {
            $name = trim((string) ($_POST['nama_lengkap'] ?? ''));
            $email = trim((string) ($_POST['email'] ?? ''));
            if ($name === '' || strlen($name) > 150) $errors[] = 'Nama lengkap wajib diisi dan maksimal 150 karakter.';
            if ($email !== '' && (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 150)) $errors[] = 'Email tidak valid.';
            if (!$errors) {
                $pdo->prepare('UPDATE users SET nama_lengkap = :nama_lengkap, email = :email WHERE user_id = :user_id AND role_id = :role_id AND is_active = 1')->execute(['nama_lengkap' => $name, 'email' => $email ?: null, 'user_id' => $userId, 'role_id' => $roleId]);
                $_SESSION['nama_lengkap'] = $name;
                $user['nama_lengkap'] = $name;
                $user['email'] = $email;
                $notice = 'Informasi profil berhasil diperbarui.';
            }
        } elseif ($action === 'photo') {
            $photo = $_FILES['profile_photo'] ?? null;
            $mime = $photo && ($photo['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK ? (new finfo(FILEINFO_MIME_TYPE))->file($photo['tmp_name']) : '';
            $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png'];
            if (!$photo || ($photo['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK || !isset($allowed[$mime]) || (int) $photo['size'] < 1 || (int) $photo['size'] > 20971520 || !getimagesize($photo['tmp_name'])) $errors[] = 'Foto harus JPG atau PNG, maksimal 20 MB.';
            if (!$errors) {
                $newPath = 'storage/profile-photos/' . bin2hex(random_bytes(16)) . '.' . $allowed[$mime];
                $absolutePath = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $newPath);
                if (!move_uploaded_file($photo['tmp_name'], $absolutePath)) {
                    $errors[] = 'Foto profil gagal disimpan.';
                } else {
                    try {
                        $pdo->beginTransaction();
                        $pdo->prepare('INSERT INTO profile_photos(user_id,file_name,file_path) VALUES(:user_id,:file_name,:file_path) ON DUPLICATE KEY UPDATE file_name=VALUES(file_name),file_path=VALUES(file_path),updated_at=CURRENT_TIMESTAMP')->execute(['user_id' => $userId, 'file_name' => basename((string) $photo['name']), 'file_path' => $newPath]);
                        $photoStatement->execute(['user_id' => $userId]);
                        $photoRecord = $photoStatement->fetch() ?: [];
                        $pdo->commit();
                        if ($profilePhoto && $profilePhoto !== $newPath) {
                            $oldPath = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $profilePhoto);
                            if (is_file($oldPath)) unlink($oldPath);
                        }
                        $profilePhoto = $newPath;
                        $photoVersion = (string) strtotime((string) ($photoRecord['updated_at'] ?? ''));
                        $notice = 'Foto profil berhasil diperbarui.';
                    } catch (Throwable $exception) {
                        if ($pdo->inTransaction()) $pdo->rollBack();
                        if (is_file($absolutePath)) unlink($absolutePath);
                        $errors[] = 'Foto profil gagal diperbarui.';
                    }
                }
            }
        } else {
            $errors[] = 'Aksi tidak valid.';
        }
    }
}

$name = trim((string) ($user['nama_lengkap'] ?: $user['username']));
$roleLabel = ucfirst(strtolower((string) $user['role_name']));
$backUrl = '../../dashboard.php';
$assetBase = '../..';
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<?php require dirname(__DIR__, 2) . '/includes/favicon.php'; ?>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Material+Symbols+Outlined" rel="stylesheet">
<link rel="stylesheet" href="<?= e($assetBase) ?>/assets/css/auth.css?v=visitor-1">
<title>Ubah Profil</title>
<style>.visitor-content{max-width:760px;padding:32px}.profile-card{padding:24px;margin-bottom:20px}.profile-card h2{display:flex;align-items:center;gap:8px;margin:0;color:#061a43;font-size:16px}.card-caption{margin:7px 0 22px;color:#64748b;font-size:13px}.photo-form,.profile-form{display:grid;gap:16px}.photo-row{display:flex;align-items:center;gap:18px}.profile-header-avatar{display:grid!important;place-items:center;flex:0 0 30px!important;width:30px!important;min-width:30px!important;max-width:30px!important;height:30px!important;min-height:30px!important;max-height:30px!important;overflow:hidden!important;border-radius:50%!important}.profile-header-avatar img{display:block!important;width:100%!important;min-width:100%!important;max-width:100%!important;height:100%!important;min-height:100%!important;max-height:100%!important;object-fit:cover!important;border-radius:inherit!important}.profile-photo-preview{display:grid;flex:0 0 96px;place-items:center;width:96px;height:96px;min-width:96px;max-width:96px;min-height:96px;max-height:96px;overflow:hidden;border-radius:50%;background:linear-gradient(135deg,#00173e,#1459cf);color:#fff;font-size:30px;font-weight:700}.profile-photo-preview img{display:block;width:100%;height:100%;object-fit:cover;border-radius:inherit}.photo-controls{display:grid;gap:9px}.photo-controls input{max-width:100%;font:inherit}.photo-controls small,.info-grid small{color:#64748b;font-size:11px}.profile-form label{display:grid;gap:7px;color:#1a1b1f;font-size:13px}.profile-form input{width:100%;min-height:42px;padding:8px 10px;border:1px solid #c4c6d1;border-radius:7px;background:#fff;font:inherit}.profile-form input:focus{border-color:#3b82f6;box-shadow:0 0 0 3px #bfdbfe}.info-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;margin-top:4px}.info-grid div{display:grid;gap:3px;padding:12px;border:1px solid #e2e8f0;border-radius:8px;background:#f8fafc}.form-actions{display:flex;justify-content:flex-end;gap:12px;margin-top:6px}.secondary-button{display:inline-flex;align-items:center;justify-content:center;min-height:40px;padding:0 15px;border:1px solid #c4c6d1;border-radius:7px;background:#fff;color:#061a43;font-size:13px;font-weight:600;text-decoration:none}.form-actions .primary-button{max-width:220px}@media(max-width:760px){.visitor-content{padding:20px 16px}.profile-card{padding:18px}.photo-row{align-items:flex-start;flex-direction:column}.info-grid{grid-template-columns:1fr}.form-actions{align-items:stretch;flex-direction:column-reverse}.form-actions>*{width:100%}}</style>
</head>
<body class="dashboard-body visitor-body">
<aside class="visitor-sidebar"><div class="visitor-brand"><span class="visitor-brand-logo"><img src="<?= e($assetBase) ?>/assets/images/logofix.png" alt="KAI"></span><strong><em>e-ARSIP</em><br>KAI DIVRE III</strong><span>Web Pengarsipan Digital</span></div><nav><p>Menu Utama</p><a href="<?= e($backUrl) ?>"><i class="material-symbols-outlined">home</i>Beranda</a><span class="nav-active"><i class="material-symbols-outlined">person</i>Ubah Profil</span></nav><section class="visitor-access"><i class="material-symbols-outlined">manage_accounts</i><b>Profil Akun</b><span>Kelola informasi profil dan foto akun Anda.</span></section></aside>
<div class="visitor-main"><header class="visitor-header"><div><b>e-ARSIP KAI DIVRE III</b><small>Web Pengarsipan Digital - Kantor KAI Divre III</small></div><details class="user-menu"><summary><span id="header-avatar-wrap" class="user-avatar profile-header-avatar"><?php if ($profilePhoto): ?><img id="header-avatar" src="<?= e($assetBase) ?>/api/profile-photo.php?t=<?= e($photoVersion) ?>" alt="Foto profil <?= e($name) ?>"><?php else: ?><?= e(profileInitial($name)) ?><?php endif; ?></span><span class="user-summary-text"><b><?= e($name) ?></b><small><?= e($roleLabel) ?></small></span><i class="user-summary-chevron material-symbols-outlined">expand_more</i></summary><div class="user-dropdown"><div class="user-dropdown-header"><b><?= e($name) ?></b><small><?= e($roleLabel) ?></small></div><a href="ubah-profil.php"><i class="material-symbols-outlined">person</i>Ubah Profil</a><a href="ubah-password.php"><i class="material-symbols-outlined">key</i>Ubah Password</a><a href="../../logout.php"><i class="material-symbols-outlined">logout</i>Logout</a></div></details></header>
<main class="visitor-content"><section class="visitor-welcome"><div><p class="visitor-breadcrumb">Profil</p><h1>UBAH PROFIL</h1><p>Kelola informasi profil dan foto akun Anda.</p></div></section><?php if ($notice): ?><p class="notice" role="status"><?= e($notice) ?></p><?php endif; ?><?php if ($errors): ?><div class="alert" role="alert"><?php foreach ($errors as $error): ?><div><?= e($error) ?></div><?php endforeach; ?></div><?php endif; ?><section class="visitor-card profile-card"><h2><i class="material-symbols-outlined">account_circle</i>FOTO PROFIL</h2><p class="card-caption">Gunakan foto JPG atau PNG, maksimal 20 MB.</p><form method="post" enctype="multipart/form-data" class="photo-form"><input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>"><input type="hidden" name="action" value="photo"><div class="photo-row"><span class="profile-photo-preview"><?php if ($profilePhoto): ?><img id="profile-photo-preview" src="<?= e($assetBase) ?>/api/profile-photo.php?t=<?= e($photoVersion) ?>" alt="Foto profil <?= e($name) ?>"><?php else: ?><span id="profile-photo-initial"><?= e(strtoupper(substr($name, 0, 1))) ?></span><?php endif; ?></span><div class="photo-controls"><label class="secondary-button" for="profile_photo">Pilih Foto</label><input id="profile_photo" type="file" name="profile_photo" accept="image/jpeg,image/png" hidden><small>Foto baru akan menggantikan foto sebelumnya.</small><button class="primary-button" type="submit">Simpan Foto</button></div></div></form></section><section class="visitor-card profile-card"><h2><i class="material-symbols-outlined">badge</i>Informasi Profil</h2><p class="card-caption">Data berikut dapat diperbarui untuk akun Anda.</p><form method="post" class="profile-form"><input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>"><input type="hidden" name="action" value="profile"><label>Nama Lengkap<input name="nama_lengkap" value="<?= e((string) $user['nama_lengkap']) ?>" maxlength="150" required></label><label>Email<input type="email" name="email" value="<?= e((string) $user['email']) ?>" maxlength="150"></label><div class="form-actions"><a class="secondary-button" href="<?= e($backUrl) ?>">Kembali</a><button class="primary-button" type="submit">Simpan Perubahan</button></div></form></section><section class="visitor-card profile-card"><h2><i class="material-symbols-outlined">info</i>Informasi Akun</h2><p class="card-caption">Data akun dan akses dikelola oleh administrator.</p><div class="info-grid"><div><small>Username</small><b><?= e((string) $user['username']) ?></b></div><div><small>Role</small><b><?= e($roleLabel) ?></b></div><div><small>Unit</small><b><?= e((string) ($user['nama_unit'] ?: '-')) ?></b></div><div><small>Status Akun</small><b>Aktif</b></div></div></section></main></div>
<script>const photoInput=document.getElementById('profile_photo'),photoPreview=document.querySelector('.profile-photo-preview'),headerAvatar=document.getElementById('header-avatar'),headerAvatarWrap=document.getElementById('header-avatar-wrap');photoInput.addEventListener('change',function(){const file=this.files[0];if(!file)return;if(!['image/jpeg','image/png'].includes(file.type))return;const url=URL.createObjectURL(file);const preview=document.getElementById('profile-photo-preview');const initial=document.getElementById('profile-photo-initial');if(preview){preview.src=url}else{if(initial)initial.remove();const image=document.createElement('img');image.id='profile-photo-preview';image.alt='Preview foto profil';image.src=url;photoPreview.appendChild(image)}if(headerAvatar){headerAvatar.src=url}else{headerAvatarWrap.textContent='';const image=document.createElement('img');image.id='header-avatar';image.alt='Preview foto profil';image.src=url;headerAvatarWrap.appendChild(image)}});</script></body></html>
