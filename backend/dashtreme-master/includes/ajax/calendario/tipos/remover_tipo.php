<?php
require_once '../../bootstrap.php';

header('Content-Type: application/json');

try {
    if (!isset($_SESSION['usuario_id'])) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Sessão expirada']);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método inválido');
    }

    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);
    if (!is_array($data)) { $data = $_POST; }

    $idUsuario = (int)$_SESSION['usuario_id'];
    $nome = isset($data['nome']) ? trim($data['nome']) : '';

    if ($nome === '') { throw new Exception('Nome do tipo é obrigatório'); }

    // Defaults são imutáveis
    $reservados = ['feriado','reuniao','evento','conselho','formacao'];
    if (in_array($nome, $reservados, true)) {
        throw new Exception('Tipos padrão não podem ser removidos');
    }
    
    $stmt = $pdo->prepare('DELETE FROM Tipos_Eventos WHERE ID_Usuario = ? AND Nome = ?');
    $stmt->execute([$idUsuario, $nome]);

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
