<?php
declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/config/database.php';
require_once dirname(__DIR__, 2) . '/includes/auth.php';
requireAdmin();

$errors = [];
$notice = '';
$roles = $pdo->query('SELECT role_id, role_name FROM roles ORDER BY role_name')->fetchAll();
$units = $pdo->query('SELECT unit_id, nama_unit FROM unit_kerja ORDER BY nama_unit')->fetchAll();
$editUser = null;

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    if (!verifyCsrfToken()) {
        $errors[] = 'Permintaan tidak valid. Silakan coba lagi.';
    } else {
        $action = (string) ($_POST['action'] ?? '');
        $userId = (int) ($_POST['user_id'] ?? 0);
        if ($action === 'status' && $userId > 0) {
            if ($userId === (int) $_SESSION['user_id']) {
                $errors[] = 'Status akun yang sedang digunakan tidak dapat diubah.';
            } else {
                $pdo->prepare('UPDATE users SET is_active = NOT is_active WHERE user_id = :user_id')->execute(['user_id' => $userId]);
                logActivity($pdo, (int) $_SESSION['user_id'], 'UPDATE');
                $notice = 'Status pengguna berhasil diperbarui.';
            }
        }
        if (in_array($action, ['create', 'update'], true)) {
            $values = [
                'nama_lengkap' => trim((string) ($_POST['nama_lengkap'] ?? '')),
                'username' => trim((string) ($_POST['username'] ?? '')),
                'email' => trim((string) ($_POST['email'] ?? '')),
                'role_id' => (int) ($_POST['role_id'] ?? 0),
                'unit_id' => ($_POST['unit_id'] ?? '') === '' ? null : (int) $_POST['unit_id'],
            ];
            $password = (string) ($_POST['password'] ?? '');
            if ($values['nama_lengkap'] === '' || $values['username'] === '' || $values['role_id'] === 0) $errors[] = 'Nama, username, dan role wajib diisi.';
            if ($values['email'] !== '' && !filter_var($values['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Format email tidak valid.';
            if ($values['email'] === '') $values['email'] = null;
            if ($action === 'create' && strlen($password) < 8) $errors[] = 'Password minimal 8 karakter.';
            if ($action === 'update' && $password !== '' && strlen($password) < 8) $errors[] = 'Password minimal 8 karakter.';
            $exists = $pdo->prepare('SELECT user_id FROM users WHERE (LOWER(username) = LOWER(:username) OR email = :email) AND user_id != :user_id');
            $exists->execute(['username' => $values['username'], 'email' => $values['email'] ?: null, 'user_id' => $userId]);
            if ($exists->fetch()) $errors[] = 'Username atau email sudah digunakan.';
            if (!$errors && $action === 'create') {
                $statement = $pdo->prepare('INSERT INTO users (username, password_hash, nama_lengkap, email, role_id, unit_id, is_active) VALUES (:username, :password_hash, :nama_lengkap, :email, :role_id, :unit_id, 1)');
                $statement->execute($values + ['password_hash' => password_hash($password, PASSWORD_DEFAULT)]);
                logActivity($pdo, (int) $_SESSION['user_id'], 'CREATE');
                $notice = 'Pengguna berhasil ditambahkan.';
            }
            if (!$errors && $action === 'update' && $userId > 0) {
                $sql = 'UPDATE users SET username = :username, nama_lengkap = :nama_lengkap, email = :email, role_id = :role_id, unit_id = :unit_id';
                if ($password !== '') $sql .= ', password_hash = :password_hash';
                $sql .= ' WHERE user_id = :user_id';
                $statement = $pdo->prepare($sql);
                $statement->execute($values + ['user_id' => $userId] + ($password !== '' ? ['password_hash' => password_hash($password, PASSWORD_DEFAULT)] : []));
                logActivity($pdo, (int) $_SESSION['user_id'], 'UPDATE');
                $notice = 'Pengguna berhasil diperbarui.';
            }
        }
    }
}

if (isset($_GET['edit'])) {
    $statement = $pdo->prepare('SELECT user_id, username, nama_lengkap, email, role_id, unit_id FROM users WHERE user_id = :user_id');
    $statement->execute(['user_id' => (int) $_GET['edit']]);
    $editUser = $statement->fetch() ?: null;
}

$query = trim((string) ($_GET['q'] ?? ''));
$roleFilter = (int) ($_GET['role_id'] ?? 0);
$statusFilter = (string) ($_GET['status'] ?? '');
$sql = 'SELECT u.user_id, u.nama_lengkap, u.username, u.email, u.role_id, r.role_name, COALESCE(uk.nama_unit, \'-\') AS unit_name, u.is_active, u.created_at FROM users u INNER JOIN roles r ON r.role_id = u.role_id LEFT JOIN unit_kerja uk ON uk.unit_id = u.unit_id WHERE 1=1';
$params = [];
if ($query !== '') { $sql .= ' AND (u.nama_lengkap LIKE :query OR u.username LIKE :query OR u.email LIKE :query)'; $params['query'] = "%{$query}%"; }
if ($roleFilter > 0) { $sql .= ' AND u.role_id = :role_id'; $params['role_id'] = $roleFilter; }
if (in_array($statusFilter, ['active', 'inactive'], true)) { $sql .= ' AND u.is_active = :is_active'; $params['is_active'] = $statusFilter === 'active' ? 1 : 0; }
$sql .= ' ORDER BY u.created_at DESC';
$statement = $pdo->prepare($sql);
$statement->execute($params);
$users = $statement->fetchAll();
?>
<!doctype html>
<html lang="id"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<?php require dirname(__DIR__, 2) . '/includes/favicon.php'; ?>
<title>Pengelolaan Pengguna | e-ARSIP KAI Divre III</title>
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Material+Symbols+Outlined" rel="stylesheet"><link rel="stylesheet" href="assets/css/auth.css">
</head><body class="dashboard-body">
<aside class="admin-sidebar"><div class="brand"><img src="assets/images/logo_kai.svg" alt="Logo KAI"><div><strong>e-ARSIP</strong><strong>KAI DIVRE III</strong><small>Web Pengarsipan Digital</small></div></div><nav><p>Menu Administrasi</p><a href="dashboard.php?role=admin"><i class="material-symbols-outlined">dashboard</i>Dashboard</a><span class="nav-active"><i class="material-symbols-outlined">group</i>Pengelolaan Pengguna</span><span><i class="material-symbols-outlined">key</i>Hak Akses</span></nav></aside>
<div class="admin-main"><header class="admin-header"><div><b>Pengelolaan Pengguna</b><small>Administrasi akun sistem</small></div><details class="user-menu"><summary><span class="user-avatar"><?=e(strtoupper(substr((string)($_SESSION['nama_lengkap'] ?? $_SESSION['username'] ?? 'U'),0,1)))?></span><span class="user-summary-text"><b><?=e((string)($_SESSION['nama_lengkap'] ?? $_SESSION['username'] ?? 'User'))?></b><small><?=e((string)($_SESSION['role_name'] ?? 'Admin'))?></small></span><i class="user-summary-chevron material-symbols-outlined">expand_more</i></summary><div class="user-dropdown"><div class="user-dropdown-header"><b><?=e((string)($_SESSION['nama_lengkap'] ?? $_SESSION['username'] ?? 'User'))?></b><small><?=e((string)($_SESSION['role_name'] ?? 'Admin'))?></small></div><a href="logout.php"><i class="material-symbols-outlined">logout</i>Logout</a></div></details></header><main class="admin-content">
<?php if ($notice): ?><p class="notice"><?= e($notice) ?></p><?php endif; ?><?php if ($errors): ?><div class="alert"><?php foreach ($errors as $error): ?><div><?= e($error) ?></div><?php endforeach; ?></div><?php endif; ?>
<section class="panel form-panel-admin"><h2><?= $editUser ? 'Edit Pengguna' : 'Tambah Pengguna' ?></h2><form method="post" class="admin-form"><input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>"><input type="hidden" name="action" value="<?= $editUser ? 'update' : 'create' ?>"><?php if ($editUser): ?><input type="hidden" name="user_id" value="<?= (int) $editUser['user_id'] ?>"><?php endif; ?><label>Nama Lengkap<input name="nama_lengkap" required value="<?= e($editUser['nama_lengkap'] ?? '') ?>"></label><label>Username<input name="username" required value="<?= e($editUser['username'] ?? '') ?>"></label><label>Email<input type="email" name="email" value="<?= e($editUser['email'] ?? '') ?>"></label><label>Password<?= $editUser ? ' <small>(kosongkan jika tidak diubah)</small>' : '' ?><input type="password" name="password" <?= $editUser ? '' : 'required minlength="8"' ?> minlength="8"></label><label>Role<select name="role_id" required><?php foreach ($roles as $role): ?><option value="<?= (int) $role['role_id'] ?>" <?= (int) ($editUser['role_id'] ?? 0) === (int) $role['role_id'] ? 'selected' : '' ?>><?= e($role['role_name']) ?></option><?php endforeach; ?></select></label><label>Unit Kerja<select name="unit_id"><option value="">Tidak ditetapkan</option><?php foreach ($units as $unit): ?><option value="<?= (int) $unit['unit_id'] ?>" <?= (int) ($editUser['unit_id'] ?? 0) === (int) $unit['unit_id'] ? 'selected' : '' ?>><?= e($unit['nama_unit']) ?></option><?php endforeach; ?></select></label><div><button class="primary-button" type="submit"><?= $editUser ? 'Simpan Perubahan' : 'Tambah Pengguna' ?></button><?php if ($editUser): ?><a href="users.php">Batal</a><?php endif; ?></div></form></section>
<section class="panel user-list"><h2>Daftar Pengguna</h2><form method="get" class="filter-form"><input name="q" value="<?= e($query) ?>" placeholder="Cari nama, username, email"><select name="role_id"><option value="">Semua role</option><?php foreach ($roles as $role): ?><option value="<?= (int) $role['role_id'] ?>" <?= $roleFilter === (int) $role['role_id'] ? 'selected' : '' ?>><?= e($role['role_name']) ?></option><?php endforeach; ?></select><select name="status"><option value="">Semua status</option><option value="active" <?= $statusFilter === 'active' ? 'selected' : '' ?>>Aktif</option><option value="inactive" <?= $statusFilter === 'inactive' ? 'selected' : '' ?>>Nonaktif</option></select><button type="submit">Filter</button></form><div class="table-wrap"><table><thead><tr><th>Nama</th><th>Username</th><th>Email</th><th>Role</th><th>Unit</th><th>Status</th><th>Aksi</th></tr></thead><tbody><?php foreach ($users as $user): ?><tr><td><?= e($user['nama_lengkap']) ?></td><td><?= e($user['username']) ?></td><td><?= e($user['email'] ?? '-') ?></td><td><?= e($user['role_name']) ?></td><td><?= e($user['unit_name']) ?></td><td><em class="status-active"><?= (int) $user['is_active'] ? 'Aktif' : 'Nonaktif' ?></em></td><td><a href="users.php?edit=<?= (int) $user['user_id'] ?>">Edit</a><form method="post" class="inline-form"><input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>"><input type="hidden" name="action" value="status"><input type="hidden" name="user_id" value="<?= (int) $user['user_id'] ?>"><button type="submit"><?= (int) $user['is_active'] ? 'Nonaktifkan' : 'Aktifkan' ?></button></form></td></tr><?php endforeach; ?></tbody></table></div></section>
</main></div></body></html>
