<?php
declare(strict_types=1);

$envFile = dirname(__DIR__) . '/.env';
if (is_file($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (!str_starts_with(trim($line), '#') && str_contains($line, '=')) {
            [$name, $value] = explode('=', $line, 2);
            if (getenv(trim($name)) === false) putenv(trim($name) . '=' . trim($value));
        }
    }
}

$connection = getenv('DB_CONNECTION') ?: 'mysql';
$isProduction = strtolower((string) getenv('APP_ENV')) === 'production';
$required = ['DB_HOST', 'DB_PORT', 'DB_DATABASE', 'DB_USERNAME', 'DB_PASSWORD'];
if ($isProduction && array_filter($required, static fn (string $variable): bool => getenv($variable) === false || getenv($variable) === '')) {
    http_response_code(500);
    exit('Konfigurasi database production tidak lengkap.');
}

$host = getenv('DB_HOST') ?: 'localhost';
$port = getenv('DB_PORT') ?: ($connection === 'pgsql' ? '5432' : '3306');
$dbname = getenv('DB_DATABASE') ?: 'arsip_digital';
$username = getenv('DB_USERNAME') ?: 'root';
$password = getenv('DB_PASSWORD') ?: '';

try {
    $dsn = $connection === 'pgsql'
        ? "pgsql:host={$host};port={$port};dbname={$dbname};sslmode=" . (getenv('DB_SSLMODE') ?: 'require')
        : "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";
    $pdo = new PDO(
        $dsn,
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $exception) {
    http_response_code(500);
    exit('Koneksi database tidak tersedia. Hubungi administrator sistem.');
}
