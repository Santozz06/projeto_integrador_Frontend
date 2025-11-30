<?php
require_once '../../../bootstrap.php';

// formato: json (default) | csv
$format = isset($_GET['formato']) ? strtolower(trim($_GET['formato'])) : 'json';

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'professor' || !isset($_SESSION['usuario_id'])) {
    if ($format === 'json') {
        header('Content-Type: application/json');
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Acesso negado']);
    } else {
        http_response_code(403);
        echo 'Acesso negado';
    }
    exit;
}

$turmaId = isset($_GET["turma_id"]) ? (int)$_GET["turma_id"] : 0;
$mes = isset($_GET["mes"]) ? trim($_GET["mes"]) : '';
$incluirSabados = isset($_GET['incluir_sabados']) && $_GET['incluir_sabados'] == '1';

if ($turmaId <= 0 || !preg_match('/^\d{4}-\d{2}$/', $mes)) {
    if ($format === 'json') {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Parâmetros inválidos']);
    } else {
        echo 'Parâmetros inválidos';
    }
    exit;
}

try {
    // Nome da turma
    $turmaNome = null;
    $stTurma = $pdo->prepare('SELECT Nome_Turma, Turno FROM Turmas WHERE ID_Turma = ?');
    $stTurma->execute([$turmaId]);
    if ($rowT = $stTurma->fetch(PDO::FETCH_ASSOC)) {
        $turmaNome = $rowT['Nome_Turma'] . (!empty($rowT['Turno']) ? ' (' . $rowT['Turno'] . ')' : '');
    }
    // Datas do mês
    $ano = (int)substr($mes, 0, 4);
    $mesNum = (int)substr($mes, 5, 2);
    $primeiroDia = new DateTime("$ano-$mesNum-01");
    $ultimoDia = clone $primeiroDia; $ultimoDia->modify('last day of this month');

    // Gera lista de dias letivos 
    $dias = [];
    $cursor = clone $primeiroDia;
    while ($cursor <= $ultimoDia) {
        $dow = (int)$cursor->format('N');
        if ($dow >= 1 && $dow <= 5) { $dias[] = $cursor->format('Y-m-d'); }
        elseif ($incluirSabados && $dow === 6) { $dias[] = $cursor->format('Y-m-d'); }
        $cursor->modify('+1 day');
    }

    // Busca alunos da turma
    $sqlAlunos = "SELECT 
                    u.ID_Usuario AS ID_Aluno,
                    u.Nome_Completo,
                    a.Matricula,
                    m.ID_Matricula
                  FROM Matriculas m
                  INNER JOIN Alunos a ON m.ID_Aluno = a.ID_Aluno
                  INNER JOIN Usuarios u ON a.ID_Aluno = u.ID_Usuario
                  WHERE m.ID_Turma = ? AND m.Status = 'Ativa'
                  ORDER BY u.Nome_Completo";
    $stAlunos = $pdo->prepare($sqlAlunos);
    $stAlunos->execute([$turmaId]);
    $alunos = $stAlunos->fetchAll(PDO::FETCH_ASSOC);

    // Busca presenças no período
    $sqlPres = 'SELECT ID_Matricula, Data, Status FROM Presencas WHERE ID_Turma = ? AND Data BETWEEN ? AND ?';
    $stPres = $pdo->prepare($sqlPres);
    $stPres->execute([$turmaId, $primeiroDia->format('Y-m-d'), $ultimoDia->format('Y-m-d')]);
    $presRows = $stPres->fetchAll(PDO::FETCH_ASSOC);

    $map = [];
    foreach ($presRows as $r) {
        $idm = (int)$r['ID_Matricula'];
        $dt = $r['Data'];
        $st = $r['Status']; // 'P','A','J'
        if (!isset($map[$idm])) $map[$idm] = [];
        $map[$idm][$dt] = $st;
    }

    // Monta linhas
    $linhas = [];
    foreach ($alunos as $al) {
        $idm = (int)$al['ID_Matricula'];
        $row = [
            'Matricula' => $al['Matricula'],
            'Nome' => $al['Nome_Completo'],
            'dias' => [],
            'totais' => ['P' => 0, 'A' => 0, 'J' => 0]
        ];
        foreach ($dias as $d) {
            $st = isset($map[$idm][$d]) ? $map[$idm][$d] : '';
            $row['dias'][$d] = $st;
            if ($st === 'P') $row['totais']['P']++;
            elseif ($st === 'A') $row['totais']['A']++;
            elseif ($st === 'J') $row['totais']['J']++;
        }
        // % Presença 
        $reg = $row['totais']['P'] + $row['totais']['A'] + $row['totais']['J'];
        $perc = $reg > 0 ? round(($row['totais']['P'] / $reg) * 100, 1) : null;
        $row['percentual'] = $perc;
        $linhas[] = $row;
    }

    if ($format === 'csv') {
        $nomeArquivo = 'chamada_' . $turmaId . '_' . $mes . '.csv';
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $nomeArquivo);
        $out = fopen('php://output', 'w');
        fprintf($out, "\xEF\xBB\xBF");
        // Cabeçalhos
        $cab = array_merge(['Matrícula','Nome'], array_map(function($d){ $p = explode('-', $d); return $p[2].'/'.$p[1]; }, $dias), ['P','A','J','% Presença']);
        if ($turmaNome) {
            fputcsv($out, ["Turma:", $turmaNome, "Turma ID:", $turmaId, "Mês:", $mes]);
        } else {
            fputcsv($out, ["Turma ID:", $turmaId, "Mês:", $mes]);
        }
        fputcsv($out, $cab);
        foreach ($linhas as $l) {
            $vals = [$l['Matricula'], $l['Nome']];
            foreach ($dias as $d) {
                $st = isset($l['dias'][$d]) ? $l['dias'][$d] : '';
                if ($st === 'P') $vals[] = 'P';
                elseif ($st === 'A') $vals[] = 'A';
                elseif ($st === 'J') $vals[] = 'J';
                else $vals[] = '';
            }
            $vals[] = $l['totais']['P'];
            $vals[] = $l['totais']['A'];
            $vals[] = $l['totais']['J'];
            $vals[] = ($l['percentual'] !== null ? $l['percentual'].'%' : '');
            fputcsv($out, $vals);
        }
        fclose($out);
        exit;
    }

    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'data' => [
            'turma_id' => $turmaId,
            'turma_nome' => $turmaNome,
            'mes' => $mes,
            'dias' => $dias,
            'linhas' => $linhas
        ]
    ]);

} catch (Exception $e) {
    if ($format === 'json') {
        header('Content-Type: application/json');
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    } else {
        http_response_code(500);
        echo 'Erro: ' . $e->getMessage();
    }
}
