<?php

require_once '../../../bootstrap.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'professor') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Acesso negado']);
    exit;
}

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método inválido');
    }

    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);
    if (!is_array($data)) { $data = $_POST; }

    $name = isset($data['name']) ? trim($data['name']) : '';
    if ($name === '') { throw new Exception('Nome do arquivo é obrigatório'); }

    // Segurança básica: não permitir path traversal e exigir extensão pdf
    if (strpos($name, '..') !== false || strpos($name, '/') !== false || strpos($name, '\\') !== false) {
        throw new Exception('Nome de arquivo inválido');
    }
    if (strtolower(pathinfo($name, PATHINFO_EXTENSION)) !== 'pdf') {
        throw new Exception('Apenas PDFs podem ser removidos');
    }

    // Exclui do banco de dados (Documentos)
    $stmt = $pdo->prepare('DELETE FROM Documentos WHERE Arquivo_Nome = ?');
    $stmt->execute([$name]);
    if ($stmt->rowCount() === 0) {
        throw new Exception('Arquivo não encontrado no banco de dados');
    }

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
