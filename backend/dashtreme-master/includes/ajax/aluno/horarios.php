<?php
require_once '../../bootstrap.php';

header('Content-Type: application/json');

try {
    if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'aluno' || !isset($_SESSION['usuario_id'])) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Acesso negado']);
        exit;
    }

    $alunoId = (int)$_SESSION['usuario_id'];
    $ano = isset($_GET['ano']) ? (int)$_GET['ano'] : null; 

    // Matriculas ativas do aluno 
    $params = [$alunoId];
    $sqlMat = "SELECT m.ID_Matricula, m.ID_Turma, m.Ano_Letivo, t.Nome_Turma
               FROM Matriculas m
               INNER JOIN Turmas t ON t.ID_Turma = m.ID_Turma
               WHERE m.ID_Aluno = ? AND m.Status = 'Ativa'";
    if ($ano) { $sqlMat .= " AND m.Ano_Letivo = ?"; $params[] = $ano; }
    $sqlMat .= " ORDER BY m.Ano_Letivo DESC";

    $stm = $pdo->prepare($sqlMat);
    $stm->execute($params);
    $mats = $stm->fetchAll(PDO::FETCH_ASSOC);

    if (!$mats) {
        // se não há ativa, usa a mais recente do ano (ou geral)
        $params2 = [$alunoId];
        $sqlMat2 = "SELECT m.ID_Matricula, m.ID_Turma, m.Ano_Letivo, t.Nome_Turma
                    FROM Matriculas m
                    INNER JOIN Turmas t ON t.ID_Turma = m.ID_Turma
                    WHERE m.ID_Aluno = ?";
        if ($ano) { $sqlMat2 .= " AND m.Ano_Letivo = ?"; $params2[] = $ano; }
        $sqlMat2 .= " ORDER BY m.Ano_Letivo DESC LIMIT 1";
        $stm2 = $pdo->prepare($sqlMat2);
        $stm2->execute($params2);
        $one = $stm2->fetchAll(PDO::FETCH_ASSOC);
        if (!$one) { echo json_encode(['success' => true, 'data' => []]); exit; }
        $mats = $one;
    }

    // Coletar IDs de turma
    $turmas = array_map(function($m){ return (int)$m['ID_Turma']; }, $mats);
    $place = implode(',', array_fill(0, count($turmas), '?'));

    $sql = "SELECT h.*, t.Nome_Turma, d.Nome_Disciplina
            FROM Horarios h
            INNER JOIN Turmas t ON t.ID_Turma = h.ID_Turma
            INNER JOIN Disciplinas d ON d.ID_Disciplina = h.ID_Disciplina
            WHERE h.ID_Turma IN ($place)";
    $params2 = $turmas;
    if ($ano) { $sql .= " AND (h.Ano_Letivo = ? OR t.Ano_Letivo = ?)"; $params2[] = $ano; $params2[] = $ano; }
    $sql .= " ORDER BY h.Dia_Semana ASC, h.Hora_Inicio ASC";

    $st = $pdo->prepare($sql);
    $st->execute($params2);
    $rows = $st->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'data' => $rows]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
