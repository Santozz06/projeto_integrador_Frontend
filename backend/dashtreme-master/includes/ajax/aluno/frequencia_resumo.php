<?php
require_once '../../bootstrap.php';
require_once '../../conexao.php';

header('Content-Type: application/json');

try {
    if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'aluno' || !isset($_SESSION['usuario_id'])) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Acesso negado']);
        exit;
    }

    $alunoId = (int)$_SESSION['usuario_id'];
    $ano = isset($_GET['ano']) ? (int)$_GET['ano'] : null; // opcional

    // Coleta TODAS as matrículas relevantes do aluno
    // Prioridade: ativas no ano -> ativas (qualquer ano) -> qualquer status no ano -> qualquer (mais recente)
    $mats = [];
    $lastMat = null; // referência para preencher turma/matricula na resposta

    // 1) Ativas no ano
    $params = [$alunoId];
    $sql = "SELECT ID_Matricula, ID_Turma, Ano_Letivo FROM Matriculas WHERE ID_Aluno = ? AND Status = 'Ativa'";
    if ($ano) { $sql .= " AND Ano_Letivo = ?"; $params[] = $ano; }
    $sql .= " ORDER BY Ano_Letivo DESC";
    $st = $pdo->prepare($sql); $st->execute($params);
    $mats = $st->fetchAll(PDO::FETCH_ASSOC);

    // 2) Se vazio, ativas (qualquer ano)
    if (!$mats) {
        $st = $pdo->prepare("SELECT ID_Matricula, ID_Turma, Ano_Letivo FROM Matriculas WHERE ID_Aluno = ? AND Status = 'Ativa' ORDER BY Ano_Letivo DESC");
        $st->execute([$alunoId]);
        $mats = $st->fetchAll(PDO::FETCH_ASSOC);
    }
    // 3) Se ainda vazio, qualquer status no ano
    if (!$mats) {
        $params = [$alunoId];
        $sql = "SELECT ID_Matricula, ID_Turma, Ano_Letivo FROM Matriculas WHERE ID_Aluno = ?";
        if ($ano) { $sql .= " AND Ano_Letivo = ?"; $params[] = $ano; }
        $sql .= " ORDER BY Ano_Letivo DESC";
        $st = $pdo->prepare($sql); $st->execute($params);
        $mats = $st->fetchAll(PDO::FETCH_ASSOC);
    }
    // 4) Se ainda vazio, pega a mais recente (qualquer)
    if (!$mats) {
        $st = $pdo->prepare("SELECT ID_Matricula, ID_Turma, Ano_Letivo FROM Matriculas WHERE ID_Aluno = ? ORDER BY Ano_Letivo DESC LIMIT 1");
        $st->execute([$alunoId]);
        $one = $st->fetchAll(PDO::FETCH_ASSOC);
        $mats = $one ?: [];
    }

    if (!$mats) {
        echo json_encode(['success' => true, 'data' => [
            'ano' => $ano ?: null,
            'matricula' => null,
            'turma' => null,
            'presencas' => 0,
            'total' => 0,
            'percentual' => null
        ]]);
        exit;
    }

    // Usa a matrícula mais recente como referência para exibição (ano/turma)
    $lastMat = $mats[0];
    $anoLetivo = isset($lastMat['Ano_Letivo']) ? (int)$lastMat['Ano_Letivo'] : ($ano ?: null);
    $turmaNome = null;
    $matriculaIdRef = isset($lastMat['ID_Matricula']) ? (int)$lastMat['ID_Matricula'] : null;
    $matriculaCodigoRef = null;
    try {
        if (isset($lastMat['ID_Turma'])) {
            $stT = $pdo->prepare("SELECT Nome_Turma FROM Turmas WHERE ID_Turma = ?");
            $stT->execute([(int)$lastMat['ID_Turma']]);
            $rT = $stT->fetch(PDO::FETCH_ASSOC);
            $turmaNome = $rT ? $rT['Nome_Turma'] : null;
        }
        if ($matriculaIdRef) {
            $stM = $pdo->prepare("SELECT a.Matricula FROM Matriculas m INNER JOIN Alunos a ON a.ID_Aluno = m.ID_Aluno WHERE m.ID_Matricula = ? LIMIT 1");
            $stM->execute([$matriculaIdRef]);
            $rM = $stM->fetch(PDO::FETCH_ASSOC);
            if ($rM && isset($rM['Matricula'])) { $matriculaCodigoRef = $rM['Matricula']; }
        }
    } catch (Throwable $e) { }

    // Resumo com base na tabela Presencas (usada no caderno de chamada)
    // Contabiliza P/A/J por matrícula e calcula % conforme relatório mensal (P / (P+A+J))
    $totais = ['P' => 0, 'A' => 0, 'J' => 0];
    try {
        // Soma para todas as matrículas coletadas
        $ids = array_map(function($m){ return (int)$m['ID_Matricula']; }, $mats);
        $place = implode(',', array_fill(0, count($ids), '?'));
        $sqlS = "SELECT Status, COUNT(*) AS Qtde FROM Presencas WHERE ID_Matricula IN ($place) GROUP BY Status";
        $st = $pdo->prepare($sqlS);
        $st->execute($ids);
        while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
            $s = strtoupper(trim($row['Status']));
            $q = (int)$row['Qtde'];
            if (isset($totais[$s])) { $totais[$s] = $q; }
        }
    } catch (Throwable $e) {
        // Se tabela não existir ainda, mantém zeros
    }
    $reg = $totais['P'] + $totais['A'] + $totais['J'];
    $pres = $totais['P'];
    $total = $reg;
    $perc = ($reg > 0) ? round(($pres / $reg) * 100, 1) : null;

    echo json_encode(['success' => true, 'data' => [
        'ano' => $anoLetivo,
        'matricula' => $matriculaCodigoRef ?: $matriculaIdRef,
        'turma' => $turmaNome,
        'presencas' => $pres,
        'total' => $total,
        'percentual' => $perc
    ]]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
