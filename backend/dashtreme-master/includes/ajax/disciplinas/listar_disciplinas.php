<?php
header('Content-Type: application/json');

try {
    require_once dirname(__DIR__, 2) . '/bootstrap.php';

    $ano = isset($_GET['ano']) && $_GET['ano'] !== '' ? intval($_GET['ano']) : null;
    $professorId = isset($_GET['professor_id']) && $_GET['professor_id'] !== '' ? intval($_GET['professor_id']) : null;

    $sql = "SELECT ID_Disciplina, Nome_Disciplina, Carga_Horaria, Etapa, Ano_Letivo, ID_Professor
            FROM Disciplinas WHERE 1=1";
    $params = [];

    if ($professorId) {
        $sql .= " AND ID_Professor = ?";
        $params[] = $professorId;
    }
    if ($ano) {
        $sql .= " AND Ano_Letivo = ?";
        $params[] = $ano;
    }

    $sql .= " ORDER BY Nome_Disciplina";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'data' => $rows]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
