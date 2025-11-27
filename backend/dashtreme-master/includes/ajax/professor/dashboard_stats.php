<?php
require_once '../../bootstrap.php';

header('Content-Type: application/json');

try {
    if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'professor') {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Acesso negado']);
        exit;
    }

    $idProfessor = (int)$_SESSION['usuario_id'];
    $ano = isset($_GET['ano']) ? (int)$_GET['ano'] : null;

    // === TURMAS ===
    $sqlTurmas = "SELECT COUNT(*) AS total FROM (
        SELECT DISTINCT pt.ID_Turma
        FROM Professores_Turmas pt
        INNER JOIN Turmas tt ON tt.ID_Turma = pt.ID_Turma
        WHERE pt.ID_Professor = ?";
    
    if ($ano) {
        $sqlTurmas .= " AND tt.Ano_Letivo = ?";
    }
    
    $sqlTurmas .= "
        UNION
        SELECT DISTINCT h.ID_Turma
        FROM Horarios h
        INNER JOIN Disciplinas d ON d.ID_Disciplina = h.ID_Disciplina
        INNER JOIN Turmas t ON t.ID_Turma = h.ID_Turma
        WHERE (h.ID_Professor = ? OR d.ID_Professor = ?)";
    
    if ($ano) {
        $sqlTurmas .= " AND (COALESCE(h.Ano_Letivo, t.Ano_Letivo) = ?)";
    }
    
    $sqlTurmas .= "
    ) tt";
    
    $paramsTurmas = [$idProfessor];
    if ($ano) $paramsTurmas[] = $ano;
    $paramsTurmas[] = $idProfessor;
    $paramsTurmas[] = $idProfessor;
    if ($ano) $paramsTurmas[] = $ano;
    
    $st = $pdo->prepare($sqlTurmas);
    $st->execute($paramsTurmas);
    $turmas = (int)($st->fetchColumn() ?: 0);


    // === ALUNOS ===
    $sqlAlunos = "SELECT COUNT(DISTINCT m.ID_Aluno) AS total
        FROM Matriculas m
        WHERE m.Status = 'Ativa'";
    
    if ($ano) {
        $sqlAlunos .= " AND COALESCE(m.Ano_Letivo, (SELECT t2.Ano_Letivo FROM Turmas t2 WHERE t2.ID_Turma = m.ID_Turma)) = ?";
    }
    
    $sqlAlunos .= "
        AND m.ID_Turma IN (
            SELECT tID FROM (
                SELECT DISTINCT pt.ID_Turma AS tID
                FROM Professores_Turmas pt
                INNER JOIN Turmas t3 ON t3.ID_Turma = pt.ID_Turma
                WHERE pt.ID_Professor = ?";
    
    if ($ano) {
        $sqlAlunos .= " AND t3.Ano_Letivo = ?";
    }
    
    $sqlAlunos .= "
                UNION
                SELECT DISTINCT h.ID_Turma AS tID
                FROM Horarios h
                INNER JOIN Disciplinas d ON d.ID_Disciplina = h.ID_Disciplina
                INNER JOIN Turmas t ON t.ID_Turma = h.ID_Turma
                WHERE (h.ID_Professor = ? OR d.ID_Professor = ?)";
    
    if ($ano) {
        $sqlAlunos .= " AND (COALESCE(h.Ano_Letivo, t.Ano_Letivo) = ?)";
    }
    
    $sqlAlunos .= "
            ) x
        )";
    
    $paramsAlunos = [];
    if ($ano) $paramsAlunos[] = $ano;
    $paramsAlunos[] = $idProfessor;
    if ($ano) $paramsAlunos[] = $ano;
    $paramsAlunos[] = $idProfessor;
    $paramsAlunos[] = $idProfessor;
    if ($ano) $paramsAlunos[] = $ano;
    
    $st = $pdo->prepare($sqlAlunos);
    $st->execute($paramsAlunos);
    $alunos = (int)($st->fetchColumn() ?: 0);


    // === DISCIPLINAS ===
    $hasAtivo = false;
    try {
        $c = $pdo->query("SHOW COLUMNS FROM Disciplinas LIKE 'Ativo'");
        $hasAtivo = (bool)$c->fetch(PDO::FETCH_ASSOC);
    } catch (Throwable $e) { }

    $sqlDisc = "SELECT COUNT(*) AS total FROM (
        SELECT DISTINCT d.ID_Disciplina
        FROM Disciplinas d
        WHERE d.ID_Professor = ?";
    
    if ($hasAtivo) {
        $sqlDisc .= " AND COALESCE(d.Ativo, 1) = 1";
    }
    
    if ($ano) {
        $sqlDisc .= " AND COALESCE(d.Ano_Letivo, ?) = ?";
    }
    
    $sqlDisc .= "
        UNION
        SELECT DISTINCT h.ID_Disciplina
        FROM Horarios h
        INNER JOIN Disciplinas d ON d.ID_Disciplina = h.ID_Disciplina
        INNER JOIN Turmas t ON t.ID_Turma = h.ID_Turma
        WHERE (h.ID_Professor = ? OR d.ID_Professor = ?)";
    
    if ($hasAtivo) {
        $sqlDisc .= " AND COALESCE(d.Ativo, 1) = 1";
    }
    
    if ($ano) {
        $sqlDisc .= " AND (COALESCE(h.Ano_Letivo, t.Ano_Letivo) = ?)";
    }
    
    $sqlDisc .= "
    ) dd";
    
    $paramsDisc = [$idProfessor];
    if ($ano) {
        $paramsDisc[] = $ano;
        $paramsDisc[] = $ano;
    }
    $paramsDisc[] = $idProfessor;
    $paramsDisc[] = $idProfessor;
    if ($ano) $paramsDisc[] = $ano;
    
    $st = $pdo->prepare($sqlDisc);
    $st->execute($paramsDisc);
    $disciplinas = (int)($st->fetchColumn() ?: 0);

    echo json_encode(['success' => true, 'data' => [
        'turmas' => $turmas,
        'alunos' => $alunos,
        'disciplinas' => $disciplinas
    ]]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
