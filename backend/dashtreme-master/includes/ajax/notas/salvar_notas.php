<?php
require_once '../../bootstrap.php';
header('Content-Type: application/json');

try {
    // Exigir autenticado e permitir admin ou professor
    if (function_exists('verificarAuth')) { verificarAuth(null); }
    if (!isset($_SESSION['user_type']) || !in_array($_SESSION['user_type'], ['admin', 'professor'])) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Acesso negado']);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Método não permitido']);
        exit;
    }

    $raw = file_get_contents('php://input');
    $json = json_decode($raw, true);

    if (!$json && isset($_POST['disciplina_id']) && isset($_POST['updates'])) {
        $json = [
            'disciplina_id' => (int)$_POST['disciplina_id'],
            'updates' => is_string($_POST['updates']) ? json_decode($_POST['updates'], true) : $_POST['updates']
        ];
    }

    if (!is_array($json)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Payload inválido']);
        exit;
    }

    $disciplinaId = isset($json['disciplina_id']) ? (int)$json['disciplina_id'] : 0;
    $turmaId = isset($json['turma_id']) ? (int)$json['turma_id'] : 0;
    $trimestre = isset($json['trimestre']) ? (int)$json['trimestre'] : 1;
    $updates = isset($json['updates']) && is_array($json['updates']) ? $json['updates'] : [];

    if ($disciplinaId <= 0 || empty($updates)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Parâmetros obrigatórios ausentes (disciplina_id/updates)']);
        exit;
    }

    // Se professor, turma_id é obrigatório e deve estar vinculada a ele
    if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'professor') {
        if ($turmaId <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'turma_id é obrigatório para professor']);
            exit;
        }
        $stmtPT = $pdo->prepare('SELECT 1 FROM Professores_Turmas WHERE ID_Professor = ? AND ID_Turma = ?');
        $stmtPT->execute([(int)$_SESSION['usuario_id'], $turmaId]);
        if (!$stmtPT->fetchColumn()) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Acesso negado à turma informada']);
            exit;
        }
    }

    if ($trimestre < 1 || $trimestre > 4) { $trimestre = 1; }

    // Garantir coluna Trimestre na tabela Notas
    try {
        $check = $pdo->query("SHOW COLUMNS FROM Notas LIKE 'Trimestre'");
        if ($check->rowCount() === 0) {
            $pdo->exec("ALTER TABLE Notas ADD COLUMN Trimestre TINYINT NULL AFTER ID_Disciplina");
        }
    } catch (Throwable $e) {
    }

    $pdo->beginTransaction();

    // Consultar se coluna Trimestre existe agora
    $hasTri = false;
    try {
        $check2 = $pdo->query("SHOW COLUMNS FROM Notas LIKE 'Trimestre'");
        $hasTri = $check2->rowCount() > 0;
    } catch (Throwable $e) { $hasTri = false; }

    if ($hasTri) {
        $stmtSel = $pdo->prepare('SELECT ID_Nota FROM Notas WHERE ID_Matricula = ? AND ID_Disciplina = ? AND Trimestre = ? AND Etapa = ?');
        $stmtUpd = $pdo->prepare('UPDATE Notas SET Nota = ?, Observacoes = COALESCE(Observacoes, NULL) WHERE ID_Nota = ?');
        $stmtIns = $pdo->prepare('INSERT INTO Notas (ID_Matricula, ID_Disciplina, Trimestre, Etapa, Nota) VALUES (?, ?, ?, ?, ?)');
    } else {
        $stmtSel = $pdo->prepare('SELECT ID_Nota FROM Notas WHERE ID_Matricula = ? AND ID_Disciplina = ? AND Etapa = ?');
        $stmtUpd = $pdo->prepare('UPDATE Notas SET Nota = ?, Observacoes = COALESCE(Observacoes, NULL) WHERE ID_Nota = ?');
        $stmtIns = $pdo->prepare('INSERT INTO Notas (ID_Matricula, ID_Disciplina, Etapa, Nota) VALUES (?, ?, ?, ?)');
    }

    $countInserted = 0;
    $countUpdated = 0;

    // Se professor, validar que todos ID_Matricula pertencem à turma informada
    if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'professor' && $turmaId > 0) {
        $idsCheck = [];
        foreach ($updates as $u) {
            $idMatricula = isset($u['id_matricula']) ? (int)$u['id_matricula'] : 0;
            if ($idMatricula > 0) { $idsCheck[] = $idMatricula; }
        }
        if ($idsCheck) {
            $in = implode(',', array_fill(0, count($idsCheck), '?'));
            $stmtM = $pdo->prepare("SELECT ID_Matricula FROM Matriculas WHERE ID_Turma = ? AND ID_Matricula IN ($in)");
            $stmtM->execute(array_merge([$turmaId], $idsCheck));
            $valid = $stmtM->fetchAll(PDO::FETCH_COLUMN, 0);
            if (count($valid) !== count(array_unique($idsCheck))) {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'Uma ou mais matrículas não pertencem à turma informada']);
                exit;
            }
        }
    }

    foreach ($updates as $u) {
        $idMatricula = isset($u['id_matricula']) ? (int)$u['id_matricula'] : 0;
        $etapa = isset($u['etapa']) ? trim((string)$u['etapa']) : '';
        $nota = array_key_exists('nota', $u) && $u['nota'] !== '' ? $u['nota'] : null; // permitir null

        if ($idMatricula <= 0 || $etapa === '') {
            continue; 
        }

        if (!in_array($etapa, ['1', '2', '3', '4'], true)) {
            continue; 
        }

        // Normalizar nota
        if ($nota !== null) {
            if (!is_numeric($nota)) {
                continue; 
            }
            $nota = max(0.0, min(10.0, (float)$nota));
            $nota = number_format($nota, 2, '.', '');
        }

        // Verifica existência
        if ($hasTri) {
            $stmtSel->execute([$idMatricula, $disciplinaId, $trimestre, $etapa]);
        } else {
            $stmtSel->execute([$idMatricula, $disciplinaId, $etapa]);
        }
        $idNota = $stmtSel->fetchColumn();

        if ($idNota) {
            $stmtUpd->execute([$nota, $idNota]);
            $countUpdated += $stmtUpd->rowCount() > 0 ? 1 : 0;
        } else {
            if ($hasTri) {
                $stmtIns->execute([$idMatricula, $disciplinaId, $trimestre, $etapa, $nota]);
            } else {
                $stmtIns->execute([$idMatricula, $disciplinaId, $etapa, $nota]);
            }
            $countInserted += 1;
        }
    }

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Notas salvas com sucesso',
        'inserted' => $countInserted,
        'updated' => $countUpdated
    ]);
} catch (Throwable $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
