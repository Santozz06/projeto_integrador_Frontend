<?php
require_once '../../../config/conexao.php';

header('Content-Type: application/json');

try {
    // filtros básicos
    $ano = isset($_GET['ano']) && $_GET['ano'] !== '' ? (int) $_GET['ano'] : null;
    $statusParam = isset($_GET['status']) ? trim($_GET['status']) : '';
    $statusFilter = null;
    if ($statusParam === 'Ativo') {
        $statusFilter = 1;
    } elseif ($statusParam === 'Inativo') {
        $statusFilter = 0;
    }

    // monta a query agregando disciplinas e turmas
    $sql = "
        SELECT
            p.ID_Professor,
            u.Nome_Completo,
            p.Matricula,
            p.Formacao AS Formacao_Academica,
            p.Area_Atuacao,
            p.Data_Ingresso,
            u.Ativo,
            -- disciplinas: tenta por Horarios, se não rolar usa Disciplinas
            COALESCE(
                NULLIF(
                    (
                        SELECT GROUP_CONCAT(DISTINCT d.Nome_Disciplina ORDER BY d.Nome_Disciplina SEPARATOR ', ')
                        FROM Horarios h
                        INNER JOIN Disciplinas d ON d.ID_Disciplina = h.ID_Disciplina
                        WHERE h.ID_Professor = p.ID_Professor
                          " . ($ano !== null ? "AND h.Ano_Letivo = :ano1" : "") . "
                    ), ''
                ),
                NULLIF(
                    (
                        SELECT GROUP_CONCAT(DISTINCT d2.Nome_Disciplina ORDER BY d2.Nome_Disciplina SEPARATOR ', ')
                        FROM Disciplinas d2
                        WHERE d2.ID_Professor = p.ID_Professor
                          " . ($ano !== null ? "AND d2.Ano_Letivo = :ano2" : "") . "
                    ), ''
                )
            ) AS Disciplinas,
            -- turmas: tenta por Horarios, se não rolar usa Professores_Turmas
            COALESCE(
                NULLIF(
                    (
                        SELECT GROUP_CONCAT(DISTINCT t.Nome_Turma ORDER BY t.Nome_Turma SEPARATOR ', ')
                        FROM Horarios h2
                        INNER JOIN Turmas t ON t.ID_Turma = h2.ID_Turma
                        WHERE h2.ID_Professor = p.ID_Professor
                          " . ($ano !== null ? "AND h2.Ano_Letivo = :ano3" : "") . "
                    ), ''
                ),
                NULLIF(
                    (
                        SELECT GROUP_CONCAT(DISTINCT t2.Nome_Turma ORDER BY t2.Nome_Turma SEPARATOR ', ')
                        FROM Professores_Turmas pt
                        INNER JOIN Turmas t2 ON t2.ID_Turma = pt.ID_Turma
                        WHERE pt.ID_Professor = p.ID_Professor
                          " . ($ano !== null ? "AND t2.Ano_Letivo = :ano4" : "") . "
                    ), ''
                )
            ) AS Turmas
        FROM Professores p
        INNER JOIN Usuarios u ON u.ID_Usuario = p.ID_Professor
        " . ($statusFilter !== null ? "WHERE u.Ativo = :ativo" : "") . "
        ORDER BY u.Nome_Completo
    ";

    $stmt = $pdo->prepare($sql);
    if ($statusFilter !== null) {
        $stmt->bindValue(':ativo', $statusFilter, PDO::PARAM_INT);
    }
    if ($ano !== null) {
        // mesmo ano em vários subselects
        $stmt->bindValue(':ano1', $ano, PDO::PARAM_INT);
        $stmt->bindValue(':ano2', $ano, PDO::PARAM_INT);
        $stmt->bindValue(':ano3', $ano, PDO::PARAM_INT);
        $stmt->bindValue(':ano4', $ano, PDO::PARAM_INT);
    }
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // ajuste pro front
    foreach ($rows as &$r) {
        // status em texto
        $r['Status'] = isset($r['Ativo']) && (int)$r['Ativo'] === 1 ? 'Ativo' : 'Inativo';
        unset($r['Ativo']);
    }

    echo json_encode(['success' => true, 'data' => $rows]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
