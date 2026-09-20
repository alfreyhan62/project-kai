<?php
declare(strict_types=1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken()) {
        $errors[] = 'Permintaan tidak valid.';
    } elseif ((string) ($_POST['action'] ?? '') !== 'password') {
        $errors[] = 'Aksi tidak valid.';
    } else {
        $currentPassword = (string) ($_POST['current_password'] ?? '');
        $password = (string) ($_POST['password'] ?? '');
        $passwordConfirmation = (string) ($_POST['password_confirmation'] ?? '');

        if (!password_verify($currentPassword, (string) $user['password_hash'])) $errors[] = 'Password lama tidak sesuai.';
        if (strlen($password) < 8) $errors[] = 'Password baru minimal 8 karakter.';
        if ($password !== $passwordConfirmation) $errors[] = 'Konfirmasi password baru tidak sesuai.';

        if (!$errors) {
            $statement = $pdo->prepare('UPDATE users SET password_hash = :password_hash, password_changed_at = NOW() WHERE user_id = :user_id AND role_id = :role_id AND is_active = 1');
            $statement->execute(['password_hash' => password_hash($password, PASSWORD_DEFAULT), 'user_id' => $userId, 'role_id' => $roleId]);
            $notice = 'Password berhasil diubah.';
        }
    }
}
