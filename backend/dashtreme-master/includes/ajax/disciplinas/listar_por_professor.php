<?php
require_once '../../config/conexao.php';
header('Content-Type: application/json');

try {

    $professorId = isset($_GET['professor_id']) && $_GET['professor_id'] !== '' ? intval($_GET['professor_id']) : 0;
    $ano = isset($_GET['ano']) && $_GET['ano'] !== '' ? intval($_GET['ano']) : null;
    if ($professorId <= 0) { echo json_encode(['success' => true, 'data' => []]); exit; }

    $sql = "SELECT d.ID_Disciplina, d.Nome_Disciplina, d.Carga_Horaria, d.Etapa, d.Ano_Letivo
            FROM Professores_Disciplinas pd
            INNER JOIN Disciplinas d ON d.ID_Disciplina = pd.ID_Disciplina
            WHERE pd.ID_Professor = ?";
    $params = [$professorId];
    if ($ano !== null) { $sql .= " AND (pd.Ano_Letivo = ? OR d.Ano_Letivo = ? )"; $params[] = $ano; $params[] = $ano; }
    $sql .= " ORDER BY d.Nome_Disciplina";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'data' => $rows]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
