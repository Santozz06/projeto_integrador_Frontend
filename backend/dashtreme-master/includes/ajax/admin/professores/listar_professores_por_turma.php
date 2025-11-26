<?php
require_once '../../../config/conexao.php';

header('Content-Type: application/json');

$idTurma = isset($_GET['turma_id']) ? (int)$_GET['turma_id'] : 0;
if ($idTurma <= 0) {
    echo json_encode(['success' => false, 'message' => 'turma_id inválido']);
    exit;
}

try {
    $sql = "SELECT DISTINCT u.ID_Usuario AS ID_Professor, u.Nome_Completo
            FROM Professores_Turmas pt
            INNER JOIN Professores p ON p.ID_Professor = pt.ID_Professor
            INNER JOIN Usuarios u ON u.ID_Usuario = p.ID_Professor
            WHERE pt.ID_Turma = ?
            ORDER BY u.Nome_Completo";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$idTurma]);
    $professores = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'data' => $professores]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
