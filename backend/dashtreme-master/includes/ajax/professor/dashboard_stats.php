<?php
require_once '../../bootstrap.php';
require_once '../../conexao.php';

header('Content-Type: application/json');

try {
    if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'professor') {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Acesso negado']);
        exit;
    }

        $idProfessor = (int)$_SESSION['usuario_id']; // Professores.ID_Professor == Usuarios.ID_Usuario
        $ano = isset($_GET['ano']) ? (int)$_GET['ano'] : null;

        // Determina o conjunto de turmas do professor, considerando:
        // - Vínculos explícitos em Professores_Turmas
        // - Horários cadastrados (h.ID_Professor) ou disciplinas atribuídas (d.ID_Professor)
            $sqlTurmas = "SELECT COUNT(*) AS total FROM (
                                            SELECT DISTINCT pt.ID_Turma
                                            FROM Professores_Turmas pt
                                            " . ($ano ? "INNER JOIN Turmas tt ON tt.ID_Turma = pt.ID_Turma AND tt.Ano_Letivo = :ano" : "") . "
                                            WHERE pt.ID_Professor = :prof
                                            UNION
                                            SELECT DISTINCT h.ID_Turma
                                            FROM Horarios h
                                            INNER JOIN Disciplinas d ON d.ID_Disciplina = h.ID_Disciplina
                                            INNER JOIN Turmas t ON t.ID_Turma = h.ID_Turma
                                            WHERE (h.ID_Professor = :prof2 OR d.ID_Professor = :prof3)
                                            " . ($ano ? "AND (COALESCE(h.Ano_Letivo, t.Ano_Letivo) = :ano2)" : "") . "
                                        ) tt";
            $st = $pdo->prepare($sqlTurmas);
            if ($ano) { $st->bindValue(':ano', $ano, PDO::PARAM_INT); }
            $st->bindValue(':prof', $idProfessor, PDO::PARAM_INT);
            $st->bindValue(':prof2', $idProfessor, PDO::PARAM_INT);
            $st->bindValue(':prof3', $idProfessor, PDO::PARAM_INT);
            if ($ano) { $st->bindValue(':ano2', $ano, PDO::PARAM_INT); }
            $st->execute();
        $turmas = (int)($st->fetchColumn() ?: 0);

        // Total de alunos: matriculas ativas nas turmas do conjunto acima
            $sqlAlunos = "SELECT COUNT(DISTINCT m.ID_Aluno) AS total
                                        FROM Matriculas m
                                        WHERE m.Status = 'Ativa'
                                            " . ($ano ? "AND COALESCE(m.Ano_Letivo, (SELECT t2.Ano_Letivo FROM Turmas t2 WHERE t2.ID_Turma = m.ID_Turma)) = :anoAl" : "") . "
                                            AND m.ID_Turma IN (
                                                SELECT tID FROM (
                                                    SELECT DISTINCT pt.ID_Turma AS tID
                                                    FROM Professores_Turmas pt
                                                    " . ($ano ? "INNER JOIN Turmas t3 ON t3.ID_Turma = pt.ID_Turma AND t3.Ano_Letivo = :anoAl2" : "") . "
                                                    WHERE pt.ID_Professor = :profAl
                                                    UNION
                                                    SELECT DISTINCT h.ID_Turma AS tID
                                                    FROM Horarios h
                                                    INNER JOIN Disciplinas d ON d.ID_Disciplina = h.ID_Disciplina
                                                    INNER JOIN Turmas t ON t.ID_Turma = h.ID_Turma
                                                    WHERE (h.ID_Professor = :profAl2 OR d.ID_Professor = :profAl3)
                                                        " . ($ano ? "AND (COALESCE(h.Ano_Letivo, t.Ano_Letivo) = :anoAl3)" : "") . "
                                                ) x
                                        )";
            $st = $pdo->prepare($sqlAlunos);
            if ($ano) {
                    $st->bindValue(':anoAl', $ano, PDO::PARAM_INT);
                    $st->bindValue(':anoAl2', $ano, PDO::PARAM_INT);
            }
            $st->bindValue(':profAl', $idProfessor, PDO::PARAM_INT);
            $st->bindValue(':profAl2', $idProfessor, PDO::PARAM_INT);
            $st->bindValue(':profAl3', $idProfessor, PDO::PARAM_INT);
            if ($ano) { $st->bindValue(':anoAl3', $ano, PDO::PARAM_INT); }
            $st->execute();
        $alunos = (int)($st->fetchColumn() ?: 0);

        // Total de disciplinas: atribuídas diretamente ou presentes em horários do professor
        $hasAtivo = false;
        try {
                $c = $pdo->query("SHOW COLUMNS FROM Disciplinas LIKE 'Ativo'");
                $hasAtivo = (bool)$c->fetch(PDO::FETCH_ASSOC);
    } catch (Throwable $e) { }

            $sqlDisc = "SELECT COUNT(*) AS total FROM (
                                        SELECT DISTINCT d.ID_Disciplina
                                        FROM Disciplinas d
                                        WHERE d.ID_Professor = :profD " . ($hasAtivo ? " AND COALESCE(d.Ativo,1)=1" : "") . ($ano ? " AND COALESCE(d.Ano_Letivo, :anoD) = :anoD" : "") . "
                                        UNION
                                        SELECT DISTINCT h.ID_Disciplina
                                        FROM Horarios h
                                        INNER JOIN Disciplinas d ON d.ID_Disciplina = h.ID_Disciplina
                                        INNER JOIN Turmas t ON t.ID_Turma = h.ID_Turma
                                        WHERE (h.ID_Professor = :profD2 OR d.ID_Professor = :profD3) " . ($hasAtivo ? " AND COALESCE(d.Ativo,1)=1" : "") . ($ano ? " AND (COALESCE(h.Ano_Letivo, t.Ano_Letivo) = :anoD2)" : "") . "
                                    ) dd";
            $st = $pdo->prepare($sqlDisc);
            $st->bindValue(':profD', $idProfessor, PDO::PARAM_INT);
            $st->bindValue(':profD2', $idProfessor, PDO::PARAM_INT);
            $st->bindValue(':profD3', $idProfessor, PDO::PARAM_INT);
            if ($ano) { $st->bindValue(':anoD', $ano, PDO::PARAM_INT); $st->bindValue(':anoD2', $ano, PDO::PARAM_INT); }
            $st->execute();
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
