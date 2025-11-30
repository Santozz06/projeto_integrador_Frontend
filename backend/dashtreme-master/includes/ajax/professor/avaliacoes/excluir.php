<?php
require_once '../../../bootstrap.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'professor' || !isset($_SESSION['usuario_id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Acesso negado']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

$profId = (int)$_SESSION['usuario_id'];
$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID inválido']);
    exit;
}

try {

    // Exclui somente se o registro for do próprio professor
    $stmt = $pdo->prepare("DELETE FROM Avaliacoes WHERE ID_Avaliacao = ? AND ID_Professor = ?");
    $ok = $stmt->execute([$id, $profId]);
    echo json_encode(['success' => (bool)$ok]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
