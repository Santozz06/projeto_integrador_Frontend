<?php
require_once '../../../config/conexao.php';

header('Content-Type: application/json');

try {
    $stmt = $pdo->query("SELECT codigo_uf as id, nome, uf FROM estados ORDER BY nome");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'data' => $rows]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
