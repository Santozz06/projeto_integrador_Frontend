<?php
require_once '../../../bootstrap.php';

header('Content-Type: application/json');

// Somente professores autenticados
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'professor' || !isset($_SESSION['usuario_id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Acesso negado']);
    exit;
}

$idTurma = isset($_GET['turma_id']) ? (int)$_GET['turma_id'] : 0;
if ($idTurma <= 0) {
    echo json_encode(['success' => false, 'message' => 'turma_id inválido']);
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

    // Descobre ano letivo da turma
    $stmt = $pdo->prepare("SELECT Ano_Letivo FROM Turmas WHERE ID_Turma = ?");
    $stmt->execute([$idTurma]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $ano = $row ? (int)$row['Ano_Letivo'] : null;

    // Lista avaliações da turma (de todos os professores) no ano da turma
    $params = [$idTurma];
    $sql = "SELECT ID_Avaliacao, ID_Turma, ID_Professor, Disciplina, Tipo, Data, Ano_Letivo
            FROM Avaliacoes WHERE ID_Turma = ?";
    if ($ano) { $sql .= " AND Ano_Letivo = ?"; $params[] = $ano; }
    $sql .= " ORDER BY Data ASC, ID_Avaliacao DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'data' => $rows]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
