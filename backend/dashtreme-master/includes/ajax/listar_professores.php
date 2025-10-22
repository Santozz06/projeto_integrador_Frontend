<?php
require_once '../../includes/bootstrap.php';
require_once '../../includes/conexao.php';

header('Content-Type: application/json');

try {
    // Consulta simples e compatível com o schema atual
    $sql = "SELECT p.ID_Professor, u.Nome_Completo
            FROM Professores p
            INNER JOIN Usuarios u ON u.ID_Usuario = p.ID_Professor
            ORDER BY u.Nome_Completo";
    $stmt = $pdo->query($sql);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'data' => $rows]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
