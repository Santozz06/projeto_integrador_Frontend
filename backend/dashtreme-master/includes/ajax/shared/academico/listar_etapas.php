<?php
require_once '../../../config/conexao.php';

header('Content-Type: application/json');

try {
    $ano = isset($_GET['ano']) && $_GET['ano'] !== '' ? (int)$_GET['ano'] : null;

    $sql = "SELECT DISTINCT Etapa FROM Turmas WHERE Etapa IS NOT NULL AND Etapa <> ''";
    $params = [];
    if ($ano) {
        $sql .= " AND Ano_Letivo = ?";
        $params[] = $ano;
    }
    $sql .= " ORDER BY Etapa";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);

    echo json_encode(['success' => true, 'data' => $rows]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
