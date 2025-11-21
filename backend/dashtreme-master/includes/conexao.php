<?php
$host = getenv('DB_HOST') ?: 'db';
$dbname = getenv('DB_NAME') ?: 'escola_db';
$username = getenv('DB_USER') ?: 'escola_user';
$password = getenv('DB_PASS') ?: 'escola_password';

// tenta várias vezes 
$maxAttempts = (int) (getenv('DB_CONNECT_RETRIES') ?: 30);
$delaySeconds = (int) (getenv('DB_CONNECT_DELAY') ?: 2);

$attempt = 0;
while (true) {
    try {
        $pdo = new PDO("mysql:host={$host};dbname={$dbname};charset=utf8", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    break; // deu certo
    } catch (PDOException $e) {
        $attempt++;
    // se falhar e ainda tiver tentativas, espera e tenta de novo
        if ($attempt < $maxAttempts) {
            error_log("[conexao.php] Tentativa {$attempt}/{$maxAttempts} falhou: " . $e->getMessage());
            sleep($delaySeconds);
            continue;
        }
    // se esgotar as tentativas, para e mostra o erro
        die("Erro na conexão ao banco após {$maxAttempts} tentativas: " . $e->getMessage());
    }
}
?>
