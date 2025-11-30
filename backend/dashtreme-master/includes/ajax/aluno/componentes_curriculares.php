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
    $anoParam = isset($_GET['ano']) ? (int)$_GET['ano'] : null;

    // Obter anos de matrícula do aluno 
    $sqlAnos = "SELECT DISTINCT COALESCE(m.Ano_Letivo, t.Ano_Letivo) AS Ano
                FROM Matriculas m
                LEFT JOIN Turmas t ON t.ID_Turma = m.ID_Turma
                WHERE m.ID_Aluno = ? AND COALESCE(m.Ano_Letivo, t.Ano_Letivo) IS NOT NULL
                ORDER BY Ano DESC";
    $stAnos = $pdo->prepare($sqlAnos);
    $stAnos->execute([$alunoId]);
    $anos = array_map(function($r){ return (int)$r['Ano']; }, $stAnos->fetchAll(PDO::FETCH_ASSOC));

    if (!$anoParam && !empty($anos)) {
        $anoParam = (int)$anos[0]; 
    }

    if (!$anoParam) {
        echo json_encode(['success' => true, 'anos' => $anos, 'ano_selecionado' => null, 'componentes' => []]);
        exit;
    }

    // Turmas do aluno no ano selecionado
    $sqlTurmas = "SELECT m.ID_Turma, t.Nome_Turma, t.Etapa
                  FROM Matriculas m
                  LEFT JOIN Turmas t ON t.ID_Turma = m.ID_Turma
                  WHERE m.ID_Aluno = ? AND COALESCE(m.Ano_Letivo, t.Ano_Letivo) = ? AND m.Status = 'Ativa'";
    $stT = $pdo->prepare($sqlTurmas);
    $stT->execute([$alunoId, $anoParam]);
    $turmas = $stT->fetchAll(PDO::FETCH_ASSOC);

    if (!$turmas) {
        echo json_encode(['success' => true, 'anos' => $anos, 'ano_selecionado' => $anoParam, 'componentes' => []]);
        exit;
    }

    $turmaIds = array_values(array_unique(array_map(function($r){ return (int)$r['ID_Turma']; }, $turmas)));
    $inPlaceholders = implode(',', array_fill(0, count($turmaIds), '?'));

    // Disciplinas distintas associadas às turmas via Horarios
    $params = $turmaIds;
    $sqlComp = "SELECT d.ID_Disciplina, d.Nome_Disciplina, d.Carga_Horaria,
              d.ID_Professor, u.Nome_Completo AS Professor_Nome,
              h.ID_Turma, t.Nome_Turma, t.Etapa
          FROM Horarios h
          INNER JOIN Disciplinas d ON d.ID_Disciplina = h.ID_Disciplina
          INNER JOIN Turmas t ON t.ID_Turma = h.ID_Turma
          LEFT JOIN Professores p ON p.ID_Professor = d.ID_Professor
          LEFT JOIN Usuarios u ON u.ID_Usuario = p.ID_Professor
          WHERE h.ID_Turma IN ($inPlaceholders) AND (h.Ano_Letivo = ? OR h.Ano_Letivo IS NULL)
          ORDER BY d.Nome_Disciplina ASC";
    $stC = $pdo->prepare($sqlComp);
    $execParams = array_merge($params, [$anoParam]);
    $stC->execute($execParams);
    $rows = $stC->fetchAll(PDO::FETCH_ASSOC);

    // Consolidar por disciplina
    $map = [];
    foreach ($rows as $r) {
        $id = (int)$r['ID_Disciplina'];
        if (!isset($map[$id])) {
            $map[$id] = [
                'id' => $id,
                'nome' => $r['Nome_Disciplina'],
                'carga_horaria' => isset($r['Carga_Horaria']) ? (int)$r['Carga_Horaria'] : null,
                'professor' => $r['Professor_Nome'] ?? null,
                'turmas' => []
            ];
        }
        $map[$id]['turmas'][] = [
            'id_turma' => (int)$r['ID_Turma'],
            'nome_turma' => $r['Nome_Turma'],
            'etapa' => $r['Etapa']
        ];
    }

    $componentes = array_values($map);

    echo json_encode([
        'success' => true,
        'anos' => $anos,
        'ano_selecionado' => $anoParam,
        'componentes' => $componentes
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
