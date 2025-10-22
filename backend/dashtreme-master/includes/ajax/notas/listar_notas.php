<?php
header('Content-Type: application/json');

try {
    require_once dirname(__DIR__, 2) . '/bootstrap.php';

    $idTurma = isset($_GET['turma_id']) ? (int)$_GET['turma_id'] : 0;
    $idDisc = isset($_GET['disciplina_id']) ? (int)$_GET['disciplina_id'] : 0;
    $trimestre = isset($_GET['trimestre']) ? (int)$_GET['trimestre'] : 1;
    if ($idTurma <= 0 || $idDisc <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Parâmetros inválidos']);
        exit;
    }

    // Se professor, garantir que ele tenha acesso à turma
    if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'professor' && isset($_SESSION['usuario_id'])) {
        $stmtChk = $pdo->prepare('SELECT 1 FROM Professores_Turmas WHERE ID_Professor = ? AND ID_Turma = ?');
        $stmtChk->execute([(int)$_SESSION['usuario_id'], $idTurma]);
        if (!$stmtChk->fetchColumn()) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Acesso negado à turma']);
            exit;
        }
    }

    // Buscar alunos ativos da turma
    $sqlAlunos = "SELECT 
            u.ID_Usuario AS ID_Aluno,
            u.Nome_Completo,
            a.Matricula AS Codigo_Matricula,
            m.ID_Matricula,
            t.Nome_Turma
        FROM Matriculas m
        INNER JOIN Alunos a ON m.ID_Aluno = a.ID_Aluno
        INNER JOIN Usuarios u ON a.ID_Aluno = u.ID_Usuario
        INNER JOIN Turmas t ON t.ID_Turma = m.ID_Turma
        WHERE m.ID_Turma = ? AND m.Status = 'Ativa'
        ORDER BY u.Nome_Completo";

    $stmt = $pdo->prepare($sqlAlunos);
    $stmt->execute([$idTurma]);
    $alunos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$alunos) {
        echo json_encode(['success' => true, 'data' => []]);
        exit;
    }

    // Mapear por ID_Matricula
    $idsMat = array_column($alunos, 'ID_Matricula');
    $in = implode(',', array_fill(0, count($idsMat), '?'));

    // Buscar notas por matrícula e etapa para a disciplina
    // Garantir coluna Trimestre se possível (migração leve)
    $hasTri = false;
    try {
        $check = $pdo->query("SHOW COLUMNS FROM Notas LIKE 'Trimestre'");
        $hasTri = $check->rowCount() > 0;
    } catch (Throwable $e) { $hasTri = false; }

    $sqlNotas = "SELECT ID_Matricula, Etapa, Nota" . ($hasTri ? ", Trimestre" : "") .
                 " FROM Notas WHERE ID_Disciplina = ? AND ID_Matricula IN ($in)" .
                 ($hasTri ? " AND Trimestre = ?" : "");
    $params = array_merge([$idDisc], $idsMat);
    if ($hasTri) { $params[] = $trimestre; }
    $stn = $pdo->prepare($sqlNotas);
    $stn->execute($params);
    $rowsNotas = $stn->fetchAll(PDO::FETCH_ASSOC);

    $mapNotas = [];
    foreach ($rowsNotas as $rn) {
        $mid = $rn['ID_Matricula'];
        if (!isset($mapNotas[$mid])) $mapNotas[$mid] = [];
        $etapa = (string)$rn['Etapa'];
        $mapNotas[$mid][$etapa] = $rn['Nota'] !== null ? floatval($rn['Nota']) : null;
    }

    // Montar resposta
    $data = [];
    foreach ($alunos as $a) {
        $mid = $a['ID_Matricula'];
        $notas = [
            '1' => isset($mapNotas[$mid]['1']) ? $mapNotas[$mid]['1'] : null,
            '2' => isset($mapNotas[$mid]['2']) ? $mapNotas[$mid]['2'] : null,
            '3' => isset($mapNotas[$mid]['3']) ? $mapNotas[$mid]['3'] : null,
            '4' => isset($mapNotas[$mid]['4']) ? $mapNotas[$mid]['4'] : null,
        ];
        $data[] = [
            'ID_Aluno' => (int)$a['ID_Aluno'],
            'Nome' => $a['Nome_Completo'],
            'Matricula' => $a['Codigo_Matricula'],
            'ID_Matricula' => (int)$mid,
            'Turma' => $a['Nome_Turma'],
            'Notas' => $notas
        ];
    }

    echo json_encode(['success' => true, 'data' => $data]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
