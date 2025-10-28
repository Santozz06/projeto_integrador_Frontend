<?php
require_once '../../../bootstrap.php';
require_once '../../../conexao.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'professor' || !isset($_SESSION['usuario_id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Acesso negado']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

$profId = (int)$_SESSION['usuario_id'];
$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID inválido']);
    exit;
}

try {
    // Garante tabela
    $pdo->exec("CREATE TABLE IF NOT EXISTS Avaliacoes (
        ID_Avaliacao INT NOT NULL AUTO_INCREMENT,
        ID_Turma INT NOT NULL,
        ID_Professor INT NOT NULL,
        Disciplina VARCHAR(100) NOT NULL,
        Tipo VARCHAR(50) NOT NULL,
        Data DATE NOT NULL,
        Ano_Letivo INT NOT NULL,
        PRIMARY KEY (ID_Avaliacao),
        KEY idx_turma (ID_Turma),
        KEY idx_prof (ID_Professor),
        KEY idx_ano (Ano_Letivo)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // Exclui somente se o registro for do próprio professor
    $stmt = $pdo->prepare("DELETE FROM Avaliacoes WHERE ID_Avaliacao = ? AND ID_Professor = ?");
    $ok = $stmt->execute([$id, $profId]);
    echo json_encode(['success' => (bool)$ok]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
