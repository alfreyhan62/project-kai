<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php require __DIR__ . '/favicon.php'; ?>
    <title>e-ARSIP KAI Divre III Login</title>
    <link rel="stylesheet" href="assets/css/login.css">
</head>
<body>
<main class="login-shell">
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
                <h2>Masuk ke Sistem</h2>
                <p>Silakan masuk untuk melanjutkan</p>
            </header>
            <?php if ($registered): ?><p class="notice" role="status">Pendaftaran berhasil. Silakan masuk.</p><?php endif; ?>
            <?php if ($error !== ''): ?><p class="alert" role="alert"><?= e($error) ?></p><?php endif; ?>
            <form method="post" data-auth-form>
                <label for="username">Username</label>
                <div class="field"><span aria-hidden="true"><svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="3"/><path d="M5 21a7 7 0 0 1 14 0"/></svg></span><input id="username" name="username" autocomplete="username" value="<?= e($_POST['username'] ?? '') ?>" placeholder="Masukkan username" required></div>
                <label for="password">Password</label>
                <div class="field"><span aria-hidden="true"><svg viewBox="0 0 24 24"><rect x="5" y="10" width="14" height="10" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3M12 14v2"/></svg></span><input id="password" type="password" name="password" autocomplete="current-password" placeholder="Masukkan password" required><button class="toggle-password" type="button" data-password-toggle="#password" aria-label="Tampilkan password"><svg viewBox="0 0 24 24"><path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"/><circle cx="12" cy="12" r="2.5"/></svg></button></div>
                <div class="form-options"><label><input type="checkbox" name="remember"> Ingat saya</label><a href="#">Lupa password?</a></div>
                <button class="primary-button" type="submit">Masuk <svg aria-hidden="true" viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"/></svg></button>
            </form>
            <div class="divider"><span>atau</span></div>
            <a class="register-link" href="register.php"><svg aria-hidden="true" viewBox="0 0 24 24"><circle cx="12" cy="8" r="3"/><path d="M5 21a7 7 0 0 1 14 0"/></svg> Belum memiliki akun? <strong>Daftar sekarang</strong></a>
            <p class="help">Butuh bantuan? Hubungi <a href="#">Administrator Sistem</a></p>
        </div>
    </section>
</main>
<script src="assets/js/auth.js"></script>
</body>
</html>
