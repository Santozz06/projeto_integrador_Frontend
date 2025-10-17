<?php
require_once '../../includes/bootstrap.php';
require_once '../../includes/conexao.php';

header('Content-Type: application/json');

try {
    $sql = "SELECT DISTINCT Area_Atuacao FROM Professores WHERE Area_Atuacao IS NOT NULL AND Area_Atuacao <> '' ORDER BY Area_Atuacao";
    $stmt = $pdo->query($sql);
    $deps = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    echo json_encode(['success' => true, 'data' => $deps]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
