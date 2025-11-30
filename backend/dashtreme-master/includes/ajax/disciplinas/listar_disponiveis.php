<?php
require_once '../../config/conexao.php';
header('Content-Type: application/json');

try {

    $professorId = isset($_GET['professor_id']) && $_GET['professor_id'] !== '' ? intval($_GET['professor_id']) : 0;
    $ano = isset($_GET['ano']) && $_GET['ano'] !== '' ? intval($_GET['ano']) : null;
    
    $sql = "SELECT d.ID_Disciplina, d.Nome_Disciplina, d.Carga_Horaria, d.Etapa, d.Ano_Letivo
            FROM Disciplinas d
            WHERE 1=1";
    $params = [];
    if ($ano !== null) { $sql .= " AND (d.Ano_Letivo = ? OR d.Ano_Letivo IS NULL)"; $params[] = $ano; }

    if ($professorId > 0) {
        $sql .= " AND d.ID_Disciplina NOT IN (
            SELECT pd.ID_Disciplina FROM Professores_Disciplinas pd WHERE pd.ID_Professor = ?";
        $params[] = $professorId;
        if ($ano !== null) { $sql .= " AND (pd.Ano_Letivo = ? OR pd.Ano_Letivo IS NULL)"; $params[] = $ano; }
        $sql .= ")";
    }

    $sql .= " ORDER BY d.Nome_Disciplina";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'data' => $rows]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
