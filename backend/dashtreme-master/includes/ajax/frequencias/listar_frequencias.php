<?php
header('Content-Type: application/json');

try {
    require_once dirname(__DIR__, 2) . '/bootstrap.php';

    $turmaId = isset($_GET['turma_id']) ? (int)$_GET['turma_id'] : 0;
    $dataIni = isset($_GET['data_ini']) && $_GET['data_ini'] !== '' ? $_GET['data_ini'] : null; // 'YYYY-MM-DD'
    $dataFim = isset($_GET['data_fim']) && $_GET['data_fim'] !== '' ? $_GET['data_fim'] : null;

    if ($turmaId <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'turma_id é obrigatório']);
        exit;
    }

    // Default: mês corrente
    if (!$dataIni || !$dataFim) {
        $firstDay = (new DateTime('first day of this month'))->format('Y-m-d');
        $lastDay = (new DateTime('last day of this month'))->format('Y-m-d');
        $dataIni = $dataIni ?: $firstDay;
        $dataFim = $dataFim ?: $lastDay;
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

    $stA = $pdo->prepare($sqlAlunos);
    $stA->execute([$turmaId]);
    $alunos = $stA->fetchAll(PDO::FETCH_ASSOC);

    if (!$alunos) {
        echo json_encode(['success' => true, 'data' => [], 'periodo' => [$dataIni, $dataFim]]);
        exit;
    }

    $idsMat = array_column($alunos, 'ID_Matricula');
    $in = implode(',', array_fill(0, count($idsMat), '?'));

    // Unificar com a tabela Presencas usada pelo caderno de chamada e painel do aluno
    // Mapeia Status: 'P' (presente), 'A' (ausente), 'J' (justificado)
    $sqlPres = "SELECT ID_Matricula, Status
                FROM Presencas
                WHERE ID_Turma = ? AND ID_Matricula IN ($in) AND Data BETWEEN ? AND ?";
    $params = array_merge([$turmaId], $idsMat, [$dataIni, $dataFim]);
    $stP = $pdo->prepare($sqlPres);
    try {
        $stP->execute($params);
        $rows = $stP->fetchAll(PDO::FETCH_ASSOC);
    } catch (Throwable $e) {
        // Compatibilidade: se Presencas não existir, retorna vazio (evita quebrar UI)
        $rows = [];
    }

    $aggr = [];
    foreach ($idsMat as $mid) { $aggr[$mid] = ['total'=>0,'presentes'=>0,'faltas'=>0]; }
    foreach ($rows as $r) {
        $mid = (int)$r['ID_Matricula'];
        $st = strtoupper(trim((string)$r['Status']));
        if (!isset($aggr[$mid])) { $aggr[$mid] = ['total'=>0,'presentes'=>0,'faltas'=>0]; }
        $aggr[$mid]['total'] += 1; // conta P, A e J no total
        if ($st === 'P') {
            $aggr[$mid]['presentes'] += 1;
        } else {
            // Trata 'A' e 'J' como ausências para o campo 'Faltas' (mantém compatibilidade da UI)
            $aggr[$mid]['faltas'] += 1;
        }
    }

    $data = [];
    foreach ($alunos as $a) {
        $mid = (int)$a['ID_Matricula'];
        $tot = $aggr[$mid]['total'];
        $pres = $aggr[$mid]['presentes'];
        $falt = $aggr[$mid]['faltas'];
        $perc = $tot > 0 ? round(($pres / $tot) * 100, 1) : null;
        $data[] = [
            'ID_Aluno' => (int)$a['ID_Aluno'],
            'Nome' => $a['Nome_Completo'],
            'Matricula' => $a['Codigo_Matricula'],
            'ID_Matricula' => $mid,
            'Turma' => $a['Nome_Turma'],
            'Total' => $tot,
            'Presentes' => $pres,
            'Faltas' => $falt,
            'Percentual' => $perc
        ];
    }

    echo json_encode(['success' => true, 'data' => $data, 'periodo' => [$dataIni, $dataFim]]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
