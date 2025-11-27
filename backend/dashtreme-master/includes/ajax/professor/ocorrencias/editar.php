<?php
require_once '../../../bootstrap.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'professor' || !isset($_SESSION['usuario_id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Acesso negado']);
    exit;
}

try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS Ocorrencias (
        ID_Ocorrencia INT NOT NULL AUTO_INCREMENT,
        ID_Turma INT NOT NULL,
        ID_Matricula INT NOT NULL,
        Data DATE NOT NULL,
        Tipo VARCHAR(100) NOT NULL,
        Descricao TEXT NOT NULL,
        ID_Professor INT NOT NULL,
        DataHoraRegistro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (ID_Ocorrencia)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $raw = file_get_contents('php://input');
    $payload = json_decode($raw, true);
    if (!is_array($payload)) { throw new Exception('JSON inválido'); }

    $id = isset($payload['id_ocorrencia']) ? (int)$payload['id_ocorrencia'] : 0;
    $data = isset($payload['data']) ? trim($payload['data']) : null;
    $tipo = isset($payload['tipo']) ? trim($payload['tipo']) : null;
    $descricao = isset($payload['descricao']) ? trim($payload['descricao']) : null;

    if ($id <= 0) { echo json_encode(['success' => false, 'message' => 'ID inválido']); exit; }

    // Descobre turma da ocorrência e verifica acesso
    $stmtInfo = $pdo->prepare('SELECT ID_Turma FROM Ocorrencias WHERE ID_Ocorrencia = ?');
    $stmtInfo->execute([$id]);
    $row = $stmtInfo->fetch(PDO::FETCH_ASSOC);
    if (!$row) { echo json_encode(['success' => false, 'message' => 'Ocorrência não encontrada']); exit; }

    $turmaId = (int)$row['ID_Turma'];
    $profId = (int)$_SESSION['usuario_id'];

    $stmtChk = $pdo->prepare('SELECT 1 FROM Professores_Turmas WHERE ID_Professor = ? AND ID_Turma = ?');
    $stmtChk->execute([$profId, $turmaId]);
    if (!$stmtChk->fetch()) { http_response_code(403); echo json_encode(['success' => false, 'message' => 'Sem acesso à turma']); exit; }

    $sets = [];
    $params = [];
    if ($data !== null) {
        if (!preg_match('/^\\d{4}-\\d{2}-\\d{2}$/', $data)) { echo json_encode(['success' => false, 'message' => 'Data inválida']); exit; }
        $sets[] = 'Data = ?';
        $params[] = $data;
    }
    if ($tipo !== null) {
        if ($tipo === '') { echo json_encode(['success' => false, 'message' => 'Tipo obrigatório']); exit; }
        if (mb_strlen($tipo) > 100) { $tipo = mb_substr($tipo, 0, 100); }
        $sets[] = 'Tipo = ?';
        $params[] = $tipo;
    }
    if ($descricao !== null) {
        if ($descricao === '') { echo json_encode(['success' => false, 'message' => 'Descrição obrigatória']); exit; }
        $sets[] = 'Descricao = ?';
        $params[] = $descricao;
    }

    if (empty($sets)) { echo json_encode(['success' => false, 'message' => 'Nada para atualizar']); exit; }

    $params[] = $id;
    $sql = 'UPDATE Ocorrencias SET ' . implode(', ', $sets) . ' WHERE ID_Ocorrencia = ?';
    $stmtUp = $pdo->prepare($sql);
    $stmtUp->execute($params);

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
