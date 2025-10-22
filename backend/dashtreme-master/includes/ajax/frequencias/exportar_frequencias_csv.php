<?php
// Gera um CSV com a agregação de frequências por aluno para uma turma e período
try {
    require_once dirname(__DIR__, 2) . '/bootstrap.php';

    $turmaId = isset($_GET['turma_id']) ? (int)$_GET['turma_id'] : 0;
    $dataIni = isset($_GET['data_ini']) && $_GET['data_ini'] !== '' ? $_GET['data_ini'] : null; // 'YYYY-MM-DD'
    $dataFim = isset($_GET['data_fim']) && $_GET['data_fim'] !== '' ? $_GET['data_fim'] : null;

    if ($turmaId <= 0) {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'turma_id é obrigatório']);
        exit;
    }

    if (!$dataIni || !$dataFim) {
        $firstDay = (new DateTime('first day of this month'))->format('Y-m-d');
        $lastDay = (new DateTime('last day of this month'))->format('Y-m-d');
        $dataIni = $dataIni ?: $firstDay;
        $dataFim = $dataFim ?: $lastDay;
    }

    // Buscar alunos ativos da turma
    $sqlAlunos = "SELECT 
            u.Nome_Completo AS Nome,
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

    $idsMat = array_column($alunos, 'ID_Matricula');

    $aggr = [];
    if ($idsMat) {
        $in = implode(',', array_fill(0, count($idsMat), '?'));
        $sqlFreq = "SELECT ID_Matricula, Presenca FROM Frequencias WHERE ID_Matricula IN ($in) AND Data BETWEEN ? AND ?";
        $params = array_merge($idsMat, [$dataIni, $dataFim]);
        $stF = $pdo->prepare($sqlFreq);
        $stF->execute($params);
        $rows = $stF->fetchAll(PDO::FETCH_ASSOC);
        foreach ($idsMat as $mid) { $aggr[$mid] = ['total'=>0,'presentes'=>0,'faltas'=>0]; }
        foreach ($rows as $r) {
            $mid = (int)$r['ID_Matricula'];
            $pres = (int)$r['Presenca'] ? 1 : 0;
            if (!isset($aggr[$mid])) { $aggr[$mid] = ['total'=>0,'presentes'=>0,'faltas'=>0]; }
            $aggr[$mid]['total'] += 1;
            $aggr[$mid]['presentes'] += $pres;
            $aggr[$mid]['faltas'] += (1 - $pres);
        }
    }

    // Cabeçalhos CSV
    $filename = 'frequencias_turma_' . $turmaId . '_' . $dataIni . '_a_' . $dataFim . '.csv';
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename=' . $filename);

    $out = fopen('php://output', 'w');
    // BOM UTF-8
    fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));
    // Header
    fputcsv($out, ['Nome', 'Matricula', 'Turma', 'Presentes', 'Faltas', 'Total', 'Percentual(%)']);

    foreach ($alunos as $a) {
        $mid = (int)$a['ID_Matricula'];
        $tot = isset($aggr[$mid]) ? $aggr[$mid]['total'] : 0;
        $pres = isset($aggr[$mid]) ? $aggr[$mid]['presentes'] : 0;
        $falt = isset($aggr[$mid]) ? $aggr[$mid]['faltas'] : 0;
        $perc = $tot > 0 ? round(($pres / $tot) * 100, 1) : 0;
        fputcsv($out, [
            $a['Nome'],
            $a['Codigo_Matricula'],
            $a['Nome_Turma'],
            $pres,
            $falt,
            $tot,
            $perc
        ]);
    }

    fclose($out);
    exit;
} catch (Throwable $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
