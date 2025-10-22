<?php
require_once '../../includes/bootstrap.php';
require_once '../../includes/conexao.php';
// Integração com Google desativada: mantendo apenas calendário local/ICS

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Método não permitido']);
        exit;
    }

    // Apenas admin por enquanto
    if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Acesso negado']);
        exit;
    }

    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    if ($id <= 0) {
        $raw = file_get_contents('php://input');
        $json = json_decode($raw, true);
        if ($json && isset($json['id'])) { $id = (int)$json['id']; }
    }

    if ($id <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'id é obrigatório']);
        exit;
    }

    // Apagar no banco
    $st = $pdo->prepare('DELETE FROM Calendario_Academico WHERE ID_Evento = ?');
    $st->execute([$id]);

    echo json_encode(['success' => true]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
