<?php
require_once '../../bootstrap.php';
require_once '../../conexao.php';

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Método não permitido']);
        exit;
    }

    if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Acesso negado']);
        exit;
    }

    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    if ($id <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'id inválido']);
        exit;
    }

    $st = $pdo->prepare('SELECT Arquivo_Caminho FROM Documentos WHERE ID_Documento = ?');
    $st->execute([$id]);
    $row = $st->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Documento não encontrado']);
        exit;
    }

    // Excluir arquivo
    $root = dirname(__DIR__, 3);
    $full = $root . '/' . $row['Arquivo_Caminho'];
    if (is_file($full)) { @unlink($full); }

    // Excluir do banco
    $del = $pdo->prepare('DELETE FROM Documentos WHERE ID_Documento = ?');
    $del->execute([$id]);

    echo json_encode(['success' => true]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
