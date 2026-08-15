<?php
declare(strict_types=1);

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
startSession();

if (isLoggedIn()) {
    header('Location: ' . dashboardUrl((string) $_SESSION['role_name']));
    exit;
}

$errors = [];
$values = ['nama_lengkap' => '', 'username' => '', 'email' => ''];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($values as $field => $_) {
        $values[$field] = trim((string) ($_POST[$field] ?? ''));
    }
    $password = (string) ($_POST['password'] ?? '');
    $confirmation = (string) ($_POST['password_confirmation'] ?? '');

    if (in_array('', $values, true) || $password === '' || $confirmation === '') $errors[] = 'Semua field wajib diisi.';
    if ($values['email'] !== '' && !filter_var($values['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Format email tidak valid.';
    if (strlen($password) < 8) $errors[] = 'Password minimal 8 karakter.';
    if ($password !== $confirmation) $errors[] = 'Konfirmasi password tidak sama.';

    if (!$errors) {
        $check = $pdo->prepare('SELECT username, email FROM users WHERE LOWER(username) = LOWER(:username) OR email = :email');
        $check->execute(['username' => $values['username'], 'email' => $values['email']]);
        foreach ($check as $existing) {
            if (strcasecmp($existing['username'], $values['username']) === 0) $errors[] = 'Username sudah digunakan.';
            if ($existing['email'] === $values['email']) $errors[] = 'Email sudah digunakan.';
        }
    }
    if (!$errors) {
        $visitor = $pdo->prepare('SELECT role_id FROM roles WHERE LOWER(role_name) = :role_name LIMIT 1');
        $visitor->execute(['role_name' => 'visitor']);
        $role = $visitor->fetch();
        if (!$role) {
            $errors[] = 'Role Visitor belum tersedia. Hubungi administrator sistem.';
        } else {
            $insert = $pdo->prepare('INSERT INTO users (role_id, username, password_hash, nama_lengkap, email, is_active) VALUES (:role_id, :username, :password_hash, :nama_lengkap, :email, 1)');
            $insert->execute(['role_id' => $role['role_id'], 'username' => $values['username'], 'password_hash' => password_hash($password, PASSWORD_DEFAULT), 'nama_lengkap' => $values['nama_lengkap'], 'email' => $values['email']]);
            header('Location: login.php?registered=1');
            exit;
        }
    }
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php require __DIR__ . '/includes/favicon.php'; ?>
    <title>Daftar | e-ARSIP KAI Divre III</title>
    <link rel="stylesheet" href="assets/css/login.css">
</head>
<body>
<main class="login-shell register-shell">
    <section class="brand-panel">
        <div class="brand-content">
            <div>
                <h1><strong>e-ARSIP</strong></h1>
                <p class="subtitle">Electronic Archive System – KAI Divre III</p>
                <i class="accent-line"></i>
                <p class="description">Pengarsipan dokumen dan surat KAI secara digital, terstruktur, dan mudah dicari</p>
            </div>
            <ul class="benefits">
                <li><span class="benefit-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M3 7v10a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-6l-2-2H5a2 2 0 0 0-2 2Z"/></svg></span><div><b>Digital</b><span>Tersimpan secara elektronik<br>dan aman</span></div></li>
                <li><span class="benefit-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M19 21V5a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v16M3 21h18M9 7h1m4 0h1M9 11h1m4 0h1M9 21v-5a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v5"/></svg></span><div><b>Terstruktur</b><span>Dokumen tertata rapi<br>dan sistematis</span></div></li>
                <li><span class="benefit-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="6"/><path d="m16 16 4 4"/></svg></span><div><b>Mudah Dicari</b><span>Pencarian cepat dan<br>akurat kapan saja</span></div></li>
            </ul>
        </div>
    </section>
    <section class="form-panel">
        <div class="auth-card">
            <header class="form-header">
                <img class="system-logo" src="assets/images/logo_kai.svg" alt="KAI">
                <h2>Buat Akun Visitor</h2>
                <p>Lengkapi data untuk mendaftar</p>
            </header>
            <?php if ($errors): ?><div class="alert" role="alert"><?php foreach ($errors as $error): ?><div><?= e($error) ?></div><?php endforeach; ?></div><?php endif; ?>
            <form method="post" data-auth-form>
                <label for="nama_lengkap">Nama Lengkap</label>
                <div class="field"><span aria-hidden="true"><svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="3"/><path d="M5 21a7 7 0 0 1 14 0"/></svg></span><input id="nama_lengkap" name="nama_lengkap" autocomplete="name" value="<?= e($values['nama_lengkap']) ?>" placeholder="Masukkan nama lengkap" required></div>
                <label for="username">Username</label>
                <div class="field"><span aria-hidden="true"><svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="3"/><path d="M5 21a7 7 0 0 1 14 0"/></svg></span><input id="username" name="username" autocomplete="username" value="<?= e($values['username']) ?>" placeholder="Masukkan username" required></div>
                <label for="email">Email</label>
                <div class="field"><span aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M3 6h18v12H3z"/><path d="m3 7 9 6 9-6"/></svg></span><input id="email" type="email" name="email" autocomplete="email" value="<?= e($values['email']) ?>" placeholder="Masukkan email" required></div>
                <label for="password">Password</label>
                <div class="field"><span aria-hidden="true"><svg viewBox="0 0 24 24"><rect x="5" y="10" width="14" height="10" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3M12 14v2"/></svg></span><input id="password" type="password" name="password" autocomplete="new-password" placeholder="Minimal 8 karakter" minlength="8" required><button class="toggle-password" type="button" data-password-toggle="#password" aria-label="Tampilkan password"><svg viewBox="0 0 24 24"><path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"/><circle cx="12" cy="12" r="2.5"/></svg></button></div>
                <label for="password_confirmation">Konfirmasi Password</label>
                <div class="field"><span aria-hidden="true"><svg viewBox="0 0 24 24"><rect x="5" y="10" width="14" height="10" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3M12 14v2"/></svg></span><input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password" placeholder="Ulangi password" minlength="8" required><button class="toggle-password" type="button" data-password-toggle="#password_confirmation" aria-label="Tampilkan password"><svg viewBox="0 0 24 24"><path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"/><circle cx="12" cy="12" r="2.5"/></svg></button></div>
                <button class="primary-button" type="submit">Daftar <svg aria-hidden="true" viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"/></svg></button>
            </form>
            <div class="divider"><span>atau</span></div>
            <a class="register-link" href="login.php"><svg aria-hidden="true" viewBox="0 0 24 24"><path d="M12 3v18M3 12h18"/></svg> Sudah memiliki akun? <strong>Masuk sekarang</strong></a>
            <p class="help">Butuh bantuan? Hubungi <a href="#">Administrator Sistem</a></p>
        </div>
    </section>
</main>
<script src="assets/js/auth.js"></script>
</body>
</html>
