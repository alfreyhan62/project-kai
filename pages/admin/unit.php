<?php
declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/config/database.php';
require_once dirname(__DIR__, 2) . '/includes/auth.php';
requireAdmin();

$errors = [];
$notice = '';
$editUnit = null;

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    if (!verifyCsrfToken()) {
        $errors[] = 'Permintaan tidak valid. Silakan coba lagi.';
    } else {
        $action = (string) ($_POST['action'] ?? '');
        $unitId = (int) ($_POST['unit_id'] ?? 0);
        if ($action === 'delete' && $unitId > 0) {
            $check = $pdo->prepare('SELECT (SELECT COUNT(*) FROM unit_kerja WHERE parent_id = :parent_id) + (SELECT COUNT(*) FROM ruangan WHERE unit_id = :room_unit_id) + (SELECT COUNT(*) FROM users WHERE unit_id = :user_unit_id)');
            $check->execute(['parent_id' => $unitId, 'room_unit_id' => $unitId, 'user_unit_id' => $unitId]);
            if ((int) $check->fetchColumn() > 0) {
                $errors[] = 'Unit kerja masih digunakan oleh data lain dan tidak dapat dihapus.';
            } else {
                $pdo->prepare('DELETE FROM unit_kerja WHERE unit_id = :id')->execute(['id' => $unitId]);
                logActivity($pdo, (int) $_SESSION['user_id'], 'DELETE');
                $notice = 'Unit kerja berhasil dihapus.';
            }
        }
        if (in_array($action, ['create', 'update'], true)) {
            $values = [
                'kode_unit' => trim((string) ($_POST['kode_unit'] ?? '')),
                'nama_unit' => trim((string) ($_POST['nama_unit'] ?? '')),
                'parent_id' => ($_POST['parent_id'] ?? '') === '' ? null : (int) $_POST['parent_id'],
                'keterangan' => trim((string) ($_POST['keterangan'] ?? '')),
            ];
            if ($values['kode_unit'] === '' || $values['nama_unit'] === '') $errors[] = 'Kode dan nama unit wajib diisi.';
            if ($values['parent_id'] !== null && $values['parent_id'] === $unitId) $errors[] = 'Unit tidak dapat menjadi induk dirinya sendiri.';
            $exists = $pdo->prepare('SELECT unit_id FROM unit_kerja WHERE kode_unit = :kode AND unit_id != :id');
            $exists->execute(['kode' => $values['kode_unit'], 'id' => $unitId]);
            if ($exists->fetch()) $errors[] = 'Kode unit sudah digunakan.';
            if (!$errors && $action === 'create') {
                $statement = $pdo->prepare('INSERT INTO unit_kerja (kode_unit, nama_unit, parent_id, keterangan) VALUES (:kode_unit, :nama_unit, :parent_id, :keterangan)');
                $statement->execute($values);
                logActivity($pdo, (int) $_SESSION['user_id'], 'CREATE');
                $notice = 'Unit kerja berhasil ditambahkan.';
            }
            if (!$errors && $action === 'update' && $unitId > 0) {
                $statement = $pdo->prepare('UPDATE unit_kerja SET kode_unit = :kode_unit, nama_unit = :nama_unit, parent_id = :parent_id, keterangan = :keterangan WHERE unit_id = :id');
                $statement->execute($values + ['id' => $unitId]);
                logActivity($pdo, (int) $_SESSION['user_id'], 'UPDATE');
                $notice = 'Unit kerja berhasil diperbarui.';
            }
        }
    }
}

if (isset($_GET['edit'])) {
    $statement = $pdo->prepare('SELECT unit_id, kode_unit, nama_unit, parent_id, keterangan FROM unit_kerja WHERE unit_id = :id');
    $statement->execute(['id' => (int) $_GET['edit']]);
    $editUnit = $statement->fetch() ?: null;
}

$parents = $pdo->query('SELECT unit_id, kode_unit, nama_unit FROM unit_kerja ORDER BY nama_unit')->fetchAll();
$query = trim((string) ($_GET['q'] ?? ''));
$sql = 'SELECT u.unit_id, u.kode_unit, u.nama_unit, u.parent_id, u.keterangan, p.nama_unit AS parent_name, (SELECT COUNT(*) FROM ruangan r WHERE r.unit_id = u.unit_id) AS room_count, (SELECT COUNT(*) FROM users x WHERE x.unit_id = u.unit_id) AS user_count FROM unit_kerja u LEFT JOIN unit_kerja p ON p.unit_id = u.parent_id WHERE u.kode_unit LIKE :kode_query OR u.nama_unit LIKE :nama_query ORDER BY u.nama_unit';
$statement = $pdo->prepare($sql);
$statement->execute(['kode_query' => "%{$query}%", 'nama_query' => "%{$query}%"]);
$units = $statement->fetchAll();
?>
<!doctype html>
<html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<?php require dirname(__DIR__, 2) . '/includes/favicon.php'; ?><title>Unit Kerja | e-ARSIP KAI Divre III</title>
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Material+Symbols+Outlined" rel="stylesheet"><link rel="stylesheet" href="assets/css/auth.css"></head>
<body class="dashboard-body"><aside class="admin-sidebar"><div class="brand"><img src="assets/images/logo_kai.svg" alt="Logo KAI"><div><strong>e-ARSIP</strong><strong>KAI DIVRE III</strong><small>Web Pengarsipan Digital</small></div></div><nav><p>Menu Administrasi</p><a href="dashboard.php?role=admin"><i class="material-symbols-outlined">dashboard</i>Dashboard</a><a href="users.php"><i class="material-symbols-outlined">group</i>Pengelolaan Pengguna</a><span class="nav-active"><i class="material-symbols-outlined">domain</i>Unit Kerja</span></nav></aside>
<div class="admin-main"><header class="admin-header"><div><b>Unit Kerja</b><small>Master data struktur organisasi</small></div><details class="user-menu"><summary><span class="user-avatar"><?=e(strtoupper(substr((string)($_SESSION['nama_lengkap'] ?? $_SESSION['username'] ?? 'U'),0,1)))?></span><span class="user-summary-text"><b><?=e((string)($_SESSION['nama_lengkap'] ?? $_SESSION['username'] ?? 'User'))?></b><small><?=e((string)($_SESSION['role_name'] ?? 'Admin'))?></small></span><i class="user-summary-chevron material-symbols-outlined">expand_more</i></summary><div class="user-dropdown"><div class="user-dropdown-header"><b><?=e((string)($_SESSION['nama_lengkap'] ?? $_SESSION['username'] ?? 'User'))?></b><small><?=e((string)($_SESSION['role_name'] ?? 'Admin'))?></small></div><a href="logout.php"><i class="material-symbols-outlined">logout</i>Logout</a></div></details></header><main class="admin-content">
<?php if ($notice): ?><p class="notice"><?= e($notice) ?></p><?php endif; ?><?php if ($errors): ?><div class="alert"><?php foreach ($errors as $error): ?><div><?= e($error) ?></div><?php endforeach; ?></div><?php endif; ?>
<section class="panel master-form"><h2><?= $editUnit ? 'Edit Unit Kerja' : 'Tambah Unit Kerja' ?></h2><form method="post" class="admin-form"><input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>"><input type="hidden" name="action" value="<?= $editUnit ? 'update' : 'create' ?>"><?php if ($editUnit): ?><input type="hidden" name="unit_id" value="<?= (int) $editUnit['unit_id'] ?>"><?php endif; ?><label>Kode Unit<input name="kode_unit" maxlength="30" required value="<?= e($editUnit['kode_unit'] ?? '') ?>"></label><label>Nama Unit<input name="nama_unit" maxlength="150" required value="<?= e($editUnit['nama_unit'] ?? '') ?>"></label><label>Unit Induk<select name="parent_id"><option value="">Tidak ada</option><?php foreach ($parents as $parent): if ($editUnit && (int) $parent['unit_id'] === (int) $editUnit['unit_id']) continue; ?><option value="<?= (int) $parent['unit_id'] ?>" <?= (int) ($editUnit['parent_id'] ?? 0) === (int) $parent['unit_id'] ? 'selected' : '' ?>><?= e($parent['kode_unit'] . ' - ' . $parent['nama_unit']) ?></option><?php endforeach; ?></select></label><label>Keterangan<textarea name="keterangan" maxlength="255"><?= e($editUnit['keterangan'] ?? '') ?></textarea></label><div><button class="primary-button" type="submit"><?= $editUnit ? 'Simpan Perubahan' : 'Tambah Unit' ?></button><?php if ($editUnit): ?><a href="unit.php">Batal</a><?php endif; ?></div></form></section>
<section class="panel user-list"><h2>Daftar Unit Kerja</h2><form method="get" class="filter-form"><input name="q" value="<?= e($query) ?>" placeholder="Cari kode atau nama unit"><button type="submit">Cari</button></form><div class="table-wrap"><table><thead><tr><th>Kode</th><th>Nama Unit</th><th>Unit Induk</th><th>Ruangan</th><th>Pengguna</th><th>Keterangan</th><th>Aksi</th></tr></thead><tbody><?php foreach ($units as $unit): ?><tr><td><?= e($unit['kode_unit']) ?></td><td><?= e($unit['nama_unit']) ?></td><td><?= e($unit['parent_name'] ?? '-') ?></td><td><?= (int) $unit['room_count'] ?></td><td><?= (int) $unit['user_count'] ?></td><td><?= e($unit['keterangan'] ?? '-') ?></td><td><a href="unit.php?edit=<?= (int) $unit['unit_id'] ?>">Edit</a><form method="post" class="inline-form"><input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="unit_id" value="<?= (int) $unit['unit_id'] ?>"><button type="submit">Hapus</button></form></td></tr><?php endforeach; ?></tbody></table></div></section>
</main></div></body></html>
