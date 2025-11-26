<?php
require_once '../../../config/conexao.php';

header('Content-Type: application/json');

$ano = isset($_GET['ano']) && $_GET['ano'] !== '' ? (int)$_GET['ano'] : null;

try {
    $params = [];
    $sql = "SELECT DISTINCT Nome_Disciplina FROM Disciplinas WHERE Nome_Disciplina IS NOT NULL AND Nome_Disciplina <> ''";
    if ($ano) { $sql .= " AND Ano_Letivo = ?"; $params[] = $ano; }
    $sql .= " ORDER BY Nome_Disciplina";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $disciplinas = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);

    // Fallback: se nada encontrado (ou tabela sem dados para o ano), buscar via Notas/Matriculas/Turmas
    if (empty($disciplinas)) {
        $params2 = [];
        $sql2 = "SELECT DISTINCT d.Nome_Disciplina
                 FROM Notas n
                 INNER JOIN Disciplinas d ON d.ID_Disciplina = n.ID_Disciplina
                 INNER JOIN Matriculas m ON m.ID_Matricula = n.ID_Matricula
                 INNER JOIN Turmas t ON t.ID_Turma = m.ID_Turma
                 WHERE d.Nome_Disciplina IS NOT NULL AND d.Nome_Disciplina <> ''";
        if ($ano) { $sql2 .= " AND t.Ano_Letivo = ?"; $params2[] = $ano; }
        $sql2 .= " ORDER BY d.Nome_Disciplina";
        $stmt2 = $pdo->prepare($sql2);
        $stmt2->execute($params2);
        $disciplinas = $stmt2->fetchAll(PDO::FETCH_COLUMN, 0);
    }

    echo json_encode(['success' => true, 'data' => $disciplinas]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
