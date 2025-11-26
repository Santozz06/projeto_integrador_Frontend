<?php
require_once '../../../bootstrap.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'professor' || !isset($_SESSION['usuario_id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Acesso negado']);
    exit;
}

$turmaId = isset($_GET['turma_id']) ? (int)$_GET['turma_id'] : 0;
$data = isset($_GET['data']) ? trim($_GET['data']) : '';
if ($turmaId <= 0 || $data === '') {
    echo json_encode(['success' => false, 'message' => 'Parâmetros inválidos']);
    exit;
}

try {
    // Garante tabela
    $pdo->exec("CREATE TABLE IF NOT EXISTS Presencas (
        ID_Presenca INT NOT NULL AUTO_INCREMENT,
        ID_Turma INT NOT NULL,
        ID_Matricula INT NOT NULL,
        Data DATE NOT NULL,
        Status CHAR(1) NOT NULL,
        ID_Professor INT NOT NULL,
        DataHoraRegistro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (ID_Presenca),
        UNIQUE KEY uq_turma_matricula_data (ID_Turma, ID_Matricula, Data),
        KEY idx_turma (ID_Turma),
        KEY idx_data (Data),
        KEY idx_prof (ID_Professor)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $sql = 'SELECT ID_Matricula, Status FROM Presencas WHERE ID_Turma = ? AND Data = ?';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$turmaId, $data]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'data' => $rows]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
