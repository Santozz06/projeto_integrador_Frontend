<?php
// Conexão simples e funcional sem arquivo .env.
// - Em Docker: usa variáveis de ambiente do serviço (se existirem).
// - Fora do Docker: usa valores padrão seguros (host db/local conforme necessidade).

$host = getenv('DB_HOST') ?: 'db';
$dbname = getenv('DB_NAME') ?: 'escola_db';
$username = getenv('DB_USER') ?: 'escola_user';
$password = getenv('DB_PASS') ?: 'escola_password';

// Parâmetros de retry para lidar com o tempo de inicialização do MySQL/importação do dump
$maxAttempts = (int) (getenv('DB_CONNECT_RETRIES') ?: 30); // ~1 min com delay padrão
$delaySeconds = (int) (getenv('DB_CONNECT_DELAY') ?: 2);

$attempt = 0;
while (true) {
    try {
        $pdo = new PDO("mysql:host={$host};dbname={$dbname};charset=utf8", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        break; // sucesso
    } catch (PDOException $e) {
        $attempt++;
        // Se for erro transitório de inicialização, tentar novamente
        if ($attempt < $maxAttempts) {
            error_log("[conexao.php] Tentativa {$attempt}/{$maxAttempts} falhou: " . $e->getMessage());
            sleep($delaySeconds);
            continue;
        }
        // Última tentativa: aborta com mensagem clara
        die("Erro na conexão ao banco após {$maxAttempts} tentativas: " . $e->getMessage());
    }
}
?>
