<?php
declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/includes/auth.php';
requireLogin();
header('Location: ../profile/ubah-password.php');
exit;
