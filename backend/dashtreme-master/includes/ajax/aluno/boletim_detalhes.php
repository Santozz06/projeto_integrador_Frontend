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
    $ano = isset($_GET['ano']) ? (int)$_GET['ano'] : 0;
    if ($ano <= 0) {
        echo json_encode(['success' => false, 'message' => 'Ano inválido']);
        exit;
    }

    // Encontrar matrícula do aluno para o ano solicitado 
    $st = $pdo->prepare("SELECT m.ID_Matricula, m.ID_Turma, COALESCE(m.Ano_Letivo, t.Ano_Letivo) AS Ano_Letivo, m.Status, t.Nome_Turma, t.Etapa
                         FROM Matriculas m
                         LEFT JOIN Turmas t ON t.ID_Turma = m.ID_Turma
                         WHERE m.ID_Aluno = ? AND COALESCE(m.Ano_Letivo, t.Ano_Letivo) = ?
                         ORDER BY (m.Status = 'Ativa') DESC, m.ID_Matricula DESC LIMIT 1");
    $st->execute([$alunoId, $ano]);
    $mat = $st->fetch(PDO::FETCH_ASSOC);
    if (!$mat) {
        echo json_encode(['success' => false, 'message' => 'Matrícula não encontrada para o ano informado']);
        exit;
    }

    $idMat = (int)$mat['ID_Matricula'];
    $anoLetivo = (int)$mat['Ano_Letivo'];
    $etapaTurma = isset($mat['Etapa']) ? trim((string)$mat['Etapa']) : '';

    // Cabeçalho do aluno
    $stA = $pdo->prepare("SELECT u.Nome_Completo, a.Matricula
                           FROM Alunos a INNER JOIN Usuarios u ON u.ID_Usuario = a.ID_Aluno
                           WHERE a.ID_Aluno = ?");
    $stA->execute([$alunoId]);
    $alu = $stA->fetch(PDO::FETCH_ASSOC) ?: [];

    // Checar se existe coluna Trimestre
    $hasTri = false;
    try {
        $chk = $pdo->query("SHOW COLUMNS FROM Notas LIKE 'Trimestre'");
        $hasTri = $chk && $chk->rowCount() > 0;
    } catch (Throwable $e) { $hasTri = false; }

    $disciplinas = [];
    if ($hasTri) {
        // Listar todas as disciplinas do ano/etapa da turma e juntar notas por trimestre (left join)
        $sql = "SELECT d.Nome_Disciplina,
                       d.Carga_Horaria,
                       t1.Media AS T1,
                       t2.Media AS T2,
                       t3.Media AS T3,
                       CASE WHEN ((t1.Media IS NOT NULL) + (t2.Media IS NOT NULL) + (t3.Media IS NOT NULL)) > 0
                            THEN ROUND((IFNULL(t1.Media,0)+IFNULL(t2.Media,0)+IFNULL(t3.Media,0)) /
                                       ((t1.Media IS NOT NULL) + (t2.Media IS NOT NULL) + (t3.Media IS NOT NULL)), 2)
                            ELSE NULL END AS Media_Final
                FROM Disciplinas d
                LEFT JOIN (
                    SELECT ID_Disciplina, AVG(Nota) AS Media FROM Notas WHERE ID_Matricula = ? AND Trimestre = 1 GROUP BY ID_Disciplina
                ) t1 ON t1.ID_Disciplina = d.ID_Disciplina
                LEFT JOIN (
                    SELECT ID_Disciplina, AVG(Nota) AS Media FROM Notas WHERE ID_Matricula = ? AND Trimestre = 2 GROUP BY ID_Disciplina
                ) t2 ON t2.ID_Disciplina = d.ID_Disciplina
                LEFT JOIN (
                    SELECT ID_Disciplina, AVG(Nota) AS Media FROM Notas WHERE ID_Matricula = ? AND Trimestre = 3 GROUP BY ID_Disciplina
                ) t3 ON t3.ID_Disciplina = d.ID_Disciplina
                WHERE d.Ano_Letivo = ?";
        $params = [$idMat, $idMat, $idMat, $anoLetivo];
        if ($etapaTurma !== '') {
            $sql .= " AND (d.Etapa = ? OR d.Etapa IS NULL)";
            $params[] = $etapaTurma;
        }
        $sql .= " ORDER BY d.Nome_Disciplina";
        $st = $pdo->prepare($sql);
        $st->execute($params);
        while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
            $disciplinas[] = [
                'nome' => $row['Nome_Disciplina'],
                'ch' => isset($row['Carga_Horaria']) ? (int)$row['Carga_Horaria'] : null,
                't1' => isset($row['T1']) ? (float)$row['T1'] : null,
                't2' => isset($row['T2']) ? (float)$row['T2'] : null,
                't3' => isset($row['T3']) ? (float)$row['T3'] : null,
                'final' => isset($row['Media_Final']) ? (float)$row['Media_Final'] : null
            ];
        }
        // Fallback: se não retornou disciplinas por Disciplinas, derivar apenas as com nota na matrícula
        if (empty($disciplinas)) {
            $sql = "SELECT d.Nome_Disciplina,
                           d.Carga_Horaria,
                           t1.Media AS T1,
                           t2.Media AS T2,
                           t3.Media AS T3,
                           CASE WHEN ((t1.Media IS NOT NULL) + (t2.Media IS NOT NULL) + (t3.Media IS NOT NULL)) > 0
                                THEN ROUND((IFNULL(t1.Media,0)+IFNULL(t2.Media,0)+IFNULL(t3.Media,0)) /
                                           ((t1.Media IS NOT NULL) + (t2.Media IS NOT NULL) + (t3.Media IS NOT NULL)), 2)
                                ELSE NULL END AS Media_Final
                    FROM (
                        SELECT DISTINCT ID_Disciplina FROM Notas WHERE ID_Matricula = ?
                    ) nd
                    INNER JOIN Disciplinas d ON d.ID_Disciplina = nd.ID_Disciplina
                    LEFT JOIN (
                        SELECT ID_Disciplina, AVG(Nota) AS Media FROM Notas WHERE ID_Matricula = ? AND Trimestre = 1 GROUP BY ID_Disciplina
                    ) t1 ON t1.ID_Disciplina = d.ID_Disciplina
                    LEFT JOIN (
                        SELECT ID_Disciplina, AVG(Nota) AS Media FROM Notas WHERE ID_Matricula = ? AND Trimestre = 2 GROUP BY ID_Disciplina
                    ) t2 ON t2.ID_Disciplina = d.ID_Disciplina
                    LEFT JOIN (
                        SELECT ID_Disciplina, AVG(Nota) AS Media FROM Notas WHERE ID_Matricula = ? AND Trimestre = 3 GROUP BY ID_Disciplina
                    ) t3 ON t3.ID_Disciplina = d.ID_Disciplina
                    ORDER BY d.Nome_Disciplina";
            $st = $pdo->prepare($sql);
            $st->execute([$idMat, $idMat, $idMat, $idMat]);
            while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
                $disciplinas[] = [
                    'nome' => $row['Nome_Disciplina'],
                    'ch' => isset($row['Carga_Horaria']) ? (int)$row['Carga_Horaria'] : null,
                    't1' => isset($row['T1']) ? (float)$row['T1'] : null,
                    't2' => isset($row['T2']) ? (float)$row['T2'] : null,
                    't3' => isset($row['T3']) ? (float)$row['T3'] : null,
                    'final' => isset($row['Media_Final']) ? (float)$row['Media_Final'] : null
                ];
            }
        }
    } else {
        // Sem coluna de trimestre: calcular média geral por disciplina (left join para trazer disciplinas sem notas)
        $sql = "SELECT d.Nome_Disciplina, d.Carga_Horaria, tn.Media_Final
                FROM Disciplinas d
                LEFT JOIN (
                    SELECT ID_Disciplina, ROUND(AVG(Nota),2) AS Media_Final
                    FROM Notas WHERE ID_Matricula = ? GROUP BY ID_Disciplina
                ) tn ON tn.ID_Disciplina = d.ID_Disciplina
                WHERE d.Ano_Letivo = ?";
        $params = [$idMat, $anoLetivo];
        if ($etapaTurma !== '') {
            $sql .= " AND (d.Etapa = ? OR d.Etapa IS NULL)";
            $params[] = $etapaTurma;
        }
        $sql .= " ORDER BY d.Nome_Disciplina";
        $st = $pdo->prepare($sql);
        $st->execute($params);
        while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
            $disciplinas[] = [
                'nome' => $row['Nome_Disciplina'],
                'ch' => isset($row['Carga_Horaria']) ? (int)$row['Carga_Horaria'] : null,
                't1' => null,
                't2' => null,
                't3' => null,
                'final' => isset($row['Media_Final']) ? (float)$row['Media_Final'] : null
            ];
        }
        // se não houver Disciplinas do ano/etapa, usar apenas as com nota
        if (empty($disciplinas)) {
            $sql = "SELECT d.Nome_Disciplina, d.Carga_Horaria, ROUND(AVG(n.Nota),2) AS Media_Final
                    FROM Notas n INNER JOIN Disciplinas d ON d.ID_Disciplina = n.ID_Disciplina
                    WHERE n.ID_Matricula = ?
                    GROUP BY d.Nome_Disciplina, d.Carga_Horaria
                    ORDER BY d.Nome_Disciplina";
            $st = $pdo->prepare($sql);
            $st->execute([$idMat]);
            while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
                $disciplinas[] = [
                    'nome' => $row['Nome_Disciplina'],
                    'ch' => isset($row['Carga_Horaria']) ? (int)$row['Carga_Horaria'] : null,
                    't1' => null,
                    't2' => null,
                    't3' => null,
                    'final' => isset($row['Media_Final']) ? (float)$row['Media_Final'] : null
                ];
            }
        }
    }

    // Mapear status
    $statusMat = isset($mat['Status']) ? strtolower(trim($mat['Status'])) : '';
    $situacao = 'APROVADO';
    if ($statusMat === 'ativa') $situacao = 'MATRICULADO';
    elseif ($statusMat === 'reprovado') $situacao = 'REPROVADO';
    elseif ($statusMat === 'transferido') $situacao = 'TRANSFERIDO';

    echo json_encode([
        'success' => true,
        'cabecalho' => [
            'nome' => $alu['Nome_Completo'] ?? null,
            'matricula' => $alu['Matricula'] ?? null,
            'turma' => $mat['Nome_Turma'] ?? null,
            'serie' => $mat['Etapa'] ?? ($mat['Nome_Turma'] ?? null),
            'ano' => (int)$mat['Ano_Letivo'],
            'status' => $situacao
        ],
        'disciplinas' => $disciplinas
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
