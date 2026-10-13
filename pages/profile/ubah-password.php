<?php
declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/config/database.php';
require_once dirname(__DIR__, 2) . '/includes/auth.php';
requireLogin();

$errors = [];
$notice = '';
$userId = (int) $_SESSION['user_id'];
$roleId = (int) $_SESSION['role_id'];

$profileStatement = $pdo->prepare('SELECT username, nama_lengkap, email, password_hash FROM users WHERE user_id = :user_id AND role_id = :role_id AND is_active = TRUE');
$profileStatement->execute(['user_id' => $userId, 'role_id' => $roleId]);
$user = $profileStatement->fetch();
if (!$user) {
    session_unset();
    session_destroy();
    header('Location: ../../login.php');
    exit;
}

require dirname(__DIR__, 2) . '/includes/password-change-handler.php';

$name = trim((string) ($user['nama_lengkap'] ?: $user['username']));
$role = (string) $_SESSION['role_name'];
$roleLabel = ucfirst(strtolower($role));
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
<link rel="stylesheet" href="<?= e($assetBase) ?>/assets/css/auth.css?v=visitor-2">
<title>Ubah Password</title>
<style>.visitor-content{max-width:760px;padding:32px}.password-card{padding:24px}.password-card h2{display:flex;align-items:center;gap:8px;margin:0;color:#061a43;font-size:16px}.card-caption{margin:7px 0 22px;color:#64748b;font-size:13px}.visitor-form{display:grid;gap:16px}.visitor-form label{margin:0;color:#1a1b1f;font-size:13px}.password-field{position:relative;margin-top:7px}.password-field input{width:100%;min-height:42px;padding:8px 44px 8px 10px;border:1px solid #c4c6d1;border-radius:7px;background:#fff;font:inherit}.password-field .toggle-password{position:absolute;top:50%;right:5px;display:grid;width:34px;height:34px;place-items:center;padding:0;border:0;background:transparent;color:#64748b;transform:translateY(-50%);cursor:pointer}.password-field .toggle-password i{font-size:19px}.form-actions{display:flex;justify-content:flex-end;gap:12px;margin-top:6px}.secondary-button{display:inline-flex;align-items:center;justify-content:center;min-height:40px;padding:0 15px;border:1px solid #c4c6d1;border-radius:7px;background:#fff;color:#061a43;font-size:13px;font-weight:600;text-decoration:none}.form-actions .primary-button{max-width:220px}.password-field input:focus{border-color:#3b82f6;box-shadow:0 0 0 3px #bfdbfe}.password-meter{display:flex;gap:4px;margin-top:8px}.password-meter i{height:5px;flex:1;border-radius:5px;background:#e2e8f0}.password-meter.weak i:nth-child(-n+1){background:#ef4444}.password-meter.medium i:nth-child(-n+2){background:#f59e0b}.password-meter.strong i{background:#22c55e}.password-hint,.match-status{display:block;margin-top:6px;color:#64748b;font-size:11px}.match-status.valid{color:#15803d}.match-status.invalid{color:#b42318}.profile-meta{display:grid;gap:10px;margin:0 0 22px;padding:16px;border:1px solid #e2e8f0;border-radius:8px;background:#f8fafc}.profile-meta div{display:grid;gap:3px}.profile-meta small{color:#64748b}.profile-breadcrumb{margin:0;color:#64748b;font-size:12px}@media(max-width:760px){.visitor-content{padding:20px 16px}.password-card{padding:18px}.form-actions{flex-direction:column}.form-actions>*{flex:1;width:100%}.secondary-button,.form-actions .primary-button{max-width:none}}</style>
</head>
<body class="dashboard-body visitor-body">
<aside class="visitor-sidebar">
    <div class="visitor-brand">
        <span class="visitor-brand-logo"><img src="<?= e($assetBase) ?>/assets/images/logofix.png" alt="KAI"></span>
        <strong><em>e-ARSIP</em><br>KAI DIVRE III</strong>
        <span>Web Pengarsipan Digital</span>
    </div>
    <nav>
        <p>Menu Utama</p>
        <a href="<?= e($backUrl) ?>"><i class="material-symbols-outlined">home</i>Beranda</a>
        <span class="nav-active"><i class="material-symbols-outlined">key</i>Ubah Password</span>
    </nav>
    <section class="visitor-access">
        <i class="material-symbols-outlined">folder_shared</i>
        <b>Akses Terbatas</b>
        <span>Kelola keamanan akun Anda dengan mengganti password secara berkala.</span>
    </section>
</aside>
<div class="visitor-main">
    <header class="visitor-header">
        <div><b>e-ARSIP KAI DIVRE III</b><small>Web Pengarsipan Digital - Kantor KAI Divre III</small></div>
        <details class="user-menu">
            <summary><span class="user-avatar"><?= profileAvatar('../..', $name) ?></span><span class="user-summary-text"><b><?= e($name) ?></b><small><?= e($roleLabel) ?></small></span><i class="user-summary-chevron material-symbols-outlined">expand_more</i></summary>
            <div class="user-dropdown"><div class="user-dropdown-header"><b><?= e($name) ?></b><small><?= e($roleLabel) ?></small></div><a href="ubah-profil.php"><i class="material-symbols-outlined">person</i>Ubah Profil</a><a href="ubah-password.php"><i class="material-symbols-outlined">key</i>Ubah Password</a><?= logoutForm('../../logout.php') ?></div>
        </details>
    </header>
    <main class="visitor-content">
        <section class="visitor-welcome">
            <div><p class="visitor-breadcrumb">Ubah Password</p><h1>UBAH PASSWORD</h1><p>Kelola keamanan akun Anda dengan mengganti password secara berkala.</p></div>
        </section>
        <section class="visitor-card password-card">
            <h2><i class="material-symbols-outlined">key</i>Ubah Password</h2>
            <p class="card-caption">Gunakan password yang kuat dan jangan bagikan ke pihak lain.</p>
            <?php if ($notice): ?><p class="notice" role="status"><?= e($notice) ?></p><?php endif; ?>
            <?php if ($errors): ?><div class="alert" role="alert"><?php foreach ($errors as $error): ?><div><?= e($error) ?></div><?php endforeach; ?></div><?php endif; ?>
            <form method="post" class="visitor-form password-form">
                <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
                <input type="hidden" name="action" value="password">
                <label>Password Lama<div class="password-field"><input type="password" name="current_password" autocomplete="current-password" required><button class="toggle-password" type="button" data-password-toggle="[name=current_password]" aria-label="Tampilkan password"><i class="material-symbols-outlined">visibility</i></button></div></label>
                <label>Password Baru<div class="password-field"><input id="password" type="password" name="password" autocomplete="new-password" minlength="8" required><button class="toggle-password" type="button" data-password-toggle="[name=password]" aria-label="Tampilkan password"><i class="material-symbols-outlined">visibility</i></button></div><span class="password-meter" id="password-meter"><i></i><i></i><i></i></span><small class="password-hint" id="password-hint">Minimal 8 karakter.</small></label>
                <label>Konfirmasi Password Baru<div class="password-field"><input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password" minlength="8" required><button class="toggle-password" type="button" data-password-toggle="[name=password_confirmation]" aria-label="Tampilkan password"><i class="material-symbols-outlined">visibility</i></button></div><small class="match-status" id="match-status">Ulangi password baru.</small></label>
                <div class="form-actions"><a class="secondary-button" href="<?= e($backUrl) ?>">Kembali</a><button class="primary-button" type="submit">Ubah Password</button></div>
            </form>
        </section>
    </main>
</div>
<script src="../../assets/js/auth.js"></script><script>const form=document.querySelector('.password-form'),password=document.getElementById('password'),confirmation=document.getElementById('password_confirmation'),meter=document.getElementById('password-meter'),hint=document.getElementById('password-hint'),match=document.getElementById('match-status');function score(value){return (value.length>=8)+( /[A-Za-z]/.test(value)?1:0)+( /\d/.test(value)?1:0);}function updateStrength(){const s=score(password.value);meter.className='password-meter '+(s<2?'weak':s<3?'medium':'strong');hint.textContent=s<2?'Password lemah. Minimal 8 karakter.':s<3?'Password sedang.':'Password kuat.';}function updateMatch(){const ok=confirmation.value&&confirmation.value===password.value;match.textContent=confirmation.value?(ok?'Password cocok.':'Password belum cocok.'):'Ulangi password baru.';match.className='match-status '+(confirmation.value?(ok?'valid':'invalid'):'');}password.addEventListener('input',()=>{updateStrength();updateMatch()});confirmation.addEventListener('input',updateMatch);form.addEventListener('submit',e=>{if(!form.checkValidity())return;e.submitter.disabled=true;e.submitter.textContent='Menyimpan...'});</script>
</body>
</html>