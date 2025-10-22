<?php
// Conexão ao banco com suporte a variáveis de ambiente e fallback seguro.
// Funciona em Docker (usa env do serviço) e em XAMPP (usa valores padrão),
// e opcionalmente lê um arquivo .env na raiz do projeto, se existir.

// Carregar .env da raiz do projeto, se existir (opcional)
$rootDir = dirname(__DIR__, 3); // .../projeto_integrador_Frontend
$envPath = $rootDir . DIRECTORY_SEPARATOR . '.env';
if (is_file($envPath) && is_readable($envPath)) {
    $lines = @file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines !== false) {
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || $line[0] === '#') continue;
            $pos = strpos($line, '=');
            if ($pos === false) continue;
            $key = trim(substr($line, 0, $pos));
            $val = trim(substr($line, $pos + 1));
            $val = trim($val, "\"' ");
            if ($key !== '') {
                // Popular em getenv/$_ENV/$_SERVER
                putenv("{$key}={$val}");
                $_ENV[$key] = $val;
                if (!isset($_SERVER[$key])) {
                    $_SERVER[$key] = $val;
                }
            }
        }
    }
}

$host = getenv('DB_HOST') ?: 'db';
$dbname = getenv('DB_NAME') ?: 'escola_db';
$username = getenv('DB_USER') ?: 'escola_user';
$password = getenv('DB_PASS') ?: 'escola_password';

try {
    $pdo = new PDO("mysql:host={$host};dbname={$dbname};charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}
?>
