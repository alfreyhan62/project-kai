<?php
$appBase = rtrim(str_replace('\\', '/', dirname((string) ($_SERVER['SCRIPT_NAME'] ?? ''))), '/');
$appBase = preg_replace('~/pages/admin$~', '', $appBase) ?: '';
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1"><?php require __DIR__ . '/favicon.php'; ?><title>Dashboard Admin | e-ARSIP</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Material+Symbols+Outlined" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/auth.css?v=avatar-3">
    <link rel="stylesheet" href="../../assets/css/admin-ui.css?v=2">
    <link rel="stylesheet" href="../../assets/css/admin-dashboard.css?v=1">
    <script defer src="../../assets/js/admin-shell.js?v=2"></script>
</head>

<body class="dashboard-body">
    <aside class="admin-sidebar">
        <div class="brand"><span style="display:block;border-radius:10px;overflow:hidden;clip-path:inset(0 round 10px)"><img src="../../assets/images/logofix.png" alt="KAI" style="display:block;border-radius:10px;object-fit:contain;clip-path:inset(0 round 10px)"></span>
            <div><strong>e-ARSIP</strong><strong>KAI DIVRE III</strong><small>Web Pengarsipan Digital</small></div>
        </div>
        <nav>
            <p>Menu Administrasi</p><a class="nav-active" data-admin-route="dashboard" href="#dashboard">Dashboard</a><a data-admin-route="users" href="#users">Pengelolaan Pengguna</a>
            <p>Master Data</p><a data-admin-route="unit" href="#unit">Unit Kerja</a><a data-admin-route="ruangan" href="#ruangan">Ruangan</a><a data-admin-route="lemari" href="#lemari">Lemari</a><a data-admin-route="rak" href="#rak">Rak</a><a data-admin-route="jenis-surat" href="#jenis-surat">Jenis Surat</a><a data-admin-route="klasifikasi" href="#klasifikasi">Klasifikasi Arsip</a>
        </nav>
    </aside>
    <div class="admin-main">
        <header class="admin-header">
            <div><b>e-ARSIP KAI DIVRE III</b><small>Web Pengarsipan Digital</small></div>
            <details class="user-menu">
                <summary><span class="user-avatar"><?= profileAvatar('../..', (string) ($_SESSION['nama_lengkap'] ?? $_SESSION['username'] ?? 'User')) ?></span><span class="user-summary-text"><b><?= e((string)($_SESSION['nama_lengkap'] ?? $_SESSION['username'] ?? 'User')) ?></b><small><?= e((string)($_SESSION['role_name'] ?? 'Role')) ?></small></span><i class="user-summary-chevron material-symbols-outlined">expand_more</i></summary>
                <div class="user-dropdown">
                    <div class="user-dropdown-header"><b><?= e((string)($_SESSION['nama_lengkap'] ?? $_SESSION['username'] ?? 'User')) ?></b><small><?= e((string)($_SESSION['role_name'] ?? 'Role')) ?></small></div><a href="<?= e($appBase) ?>/pages/profile/ubah-profil.php"><i class="material-symbols-outlined">person</i>Ubah Profil</a><a href="<?= e($appBase) ?>/pages/profile/ubah-password.php"><i class="material-symbols-outlined">key</i>Ubah Password</a><a href="<?= e($appBase) ?>/logout.php"><i class="material-symbols-outlined">logout</i>Logout</a>
                </div>
            </details>
        </header>
        <main id="admin-shell-content" class="admin-content admin-dashboard-content" data-source="dashboard.php">
            <section class="admin-quick-actions" aria-label="Aksi cepat">
                <div class="admin-quick-actions-copy"><span>Aksi Cepat</span><small>Akses administrasi yang sering digunakan.</small></div>
                <div class="admin-quick-actions-list"><a href="#users" data-admin-route="users"><i class="material-symbols-outlined">person_add</i>Tambah Pengguna</a><a href="#unit" data-admin-route="unit"><i class="material-symbols-outlined">account_tree</i>Kelola Unit</a><a href="#klasifikasi" data-admin-route="klasifikasi"><i class="material-symbols-outlined">folder_managed</i>Klasifikasi Arsip</a></div>
            </section>
            <section class="welcome">
                <div>
                    <h1>Selamat datang, <?= e((string)$_SESSION['nama_lengkap']) ?></h1>
                    <p>Administrator sistem.</p>
                </div>
            </section>
            <section class="stat-grid">
                <article><i class="material-symbols-outlined">group</i><small>Total Pengguna</small><strong><?= (int)$totals['total_users'] ?></strong></article>
                <article><i class="material-symbols-outlined">how_to_reg</i><small>Aktif</small><strong><?= (int)$totals['active_users'] ?></strong></article>
                <article><i class="material-symbols-outlined">person_off</i><small>Nonaktif</small><strong><?= (int)$totals['inactive_users'] ?></strong></article>
            </section>
            <section class="panel">
                <h2>Pengguna Terbaru</h2>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Username</th>
                                <th>Role</th>
                                <th>Unit</th>
                            </tr>
                        </thead>
                        <tbody><?php foreach ($latestUsers as $user): ?><tr>
                                    <td><?= e($user['nama_lengkap']) ?></td>
                                    <td><?= e($user['username']) ?></td>
                                    <td><?= e($user['role_name']) ?></td>
                                    <td><?= e($user['unit_name']) ?></td>
                                </tr><?php endforeach; ?></tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>
</body>

</html>