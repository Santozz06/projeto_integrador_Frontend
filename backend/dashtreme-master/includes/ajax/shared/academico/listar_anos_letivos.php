<?php
require_once '../../../config/conexao.php';

header('Content-Type: application/json');

try {
    $sql = "SELECT DISTINCT Ano_Letivo FROM Turmas ORDER BY Ano_Letivo DESC";
    $stmt = $pdo->query($sql);
    $anos = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    echo json_encode(['success' => true, 'data' => $anos]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
