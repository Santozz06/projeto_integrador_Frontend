<?php
require_once '../../../../bootstrap.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'professor' || !isset($_SESSION['usuario_id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Acesso negado']);
    exit;
}

try {
    // Cria tabela Ocorrencias se não existir
    $pdo->exec("CREATE TABLE IF NOT EXISTS Ocorrencias (
        ID_Ocorrencia INT NOT NULL AUTO_INCREMENT,
        ID_Turma INT NOT NULL,
        ID_Matricula INT NOT NULL,
        Data DATE NOT NULL,
        Tipo VARCHAR(100) NOT NULL,
        Descricao TEXT NOT NULL,
        ID_Professor INT NOT NULL,
        DataHoraRegistro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (ID_Ocorrencia),
        KEY idx_turma (ID_Turma),
        KEY idx_matricula (ID_Matricula),
        KEY idx_data (Data),
        KEY idx_prof (ID_Professor)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $raw = file_get_contents('php://input');
    $payload = json_decode($raw, true);
    if (!is_array($payload)) { throw new Exception('JSON inválido'); }

    $turmaId = isset($payload['turma_id']) ? (int)$payload['turma_id'] : 0;
    $itens = isset($payload['ocorrencias']) && is_array($payload['ocorrencias']) ? $payload['ocorrencias'] : [];
    if ($turmaId <= 0 || empty($itens)) {
        echo json_encode(['success' => false, 'message' => 'Parâmetros inválidos']);
        exit;
    }

    $profId = (int)$_SESSION['usuario_id'];

    // Verifica acesso do professor à turma
    $stmtChk = $pdo->prepare('SELECT 1 FROM Professores_Turmas WHERE ID_Professor = ? AND ID_Turma = ?');
    $stmtChk->execute([$profId, $turmaId]);
    if (!$stmtChk->fetch()) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Você não tem acesso a esta turma']);
        exit;
    }

    // Validação e inserção
    $stmtValidaMat = $pdo->prepare("SELECT 1 FROM Matriculas WHERE ID_Matricula = ? AND ID_Turma = ? AND Status = 'Ativa'");
    $stmtIns = $pdo->prepare('INSERT INTO Ocorrencias (ID_Turma, ID_Matricula, Data, Tipo, Descricao, ID_Professor) VALUES (?,?,?,?,?,?)');

    $inseridos = 0;
    foreach ($itens as $i) {
        $idMat = isset($i['id_matricula']) ? (int)$i['id_matricula'] : 0;
        $data = isset($i['data']) ? trim($i['data']) : '';
        $tipo = isset($i['tipo']) ? trim($i['tipo']) : '';
        $desc = isset($i['descricao']) ? trim($i['descricao']) : '';

        if ($idMat <= 0 || $data === '' || $tipo === '' || $desc === '') { continue; }
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data)) { continue; }
        if (mb_strlen($tipo) > 100) { $tipo = mb_substr($tipo, 0, 100); }

        $stmtValidaMat->execute([$idMat, $turmaId]);
        if (!$stmtValidaMat->fetch()) { continue; }

        $stmtIns->execute([$turmaId, $idMat, $data, $tipo, $desc, $profId]);
        $inseridos += ($stmtIns->rowCount() > 0 ? 1 : 0);
    }

    echo json_encode(['success' => true, 'inserted' => $inseridos]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
