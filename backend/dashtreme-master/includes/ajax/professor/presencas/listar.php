<?php
require_once '../../../bootstrap.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'professor' || !isset($_SESSION['usuario_id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Acesso negado']);
    exit;
}

$turmaId = isset($_GET['turma_id']) ? (int)$_GET['turma_id'] : 0;
$data = isset($_GET['data']) ? trim($_GET['data']) : '';
if ($turmaId <= 0 || $data === '') {
    echo json_encode(['success' => false, 'message' => 'Parâmetros inválidos']);
    exit;
}

try {
    $sql = 'SELECT ID_Matricula, Status FROM Presencas WHERE ID_Turma = ? AND Data = ?';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$turmaId, $data]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'data' => $rows]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
