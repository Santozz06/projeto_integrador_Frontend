<?php
require_once '../../bootstrap.php';
require_once '../../conexao.php';

try {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    if ($id <= 0) { http_response_code(400); echo 'ID inválido'; exit; }

    $st = $pdo->prepare('SELECT Titulo, Arquivo_Nome, Arquivo_Caminho, Mime_Type FROM Documentos WHERE ID_Documento = ? AND Ativo = 1');
    $st->execute([$id]);
    $row = $st->fetch(PDO::FETCH_ASSOC);
    if (!$row) { http_response_code(404); echo 'Documento não encontrado'; exit; }

    $root = dirname(__DIR__, 3);
    $full = $root . '/' . $row['Arquivo_Caminho'];
    if (!is_file($full)) { http_response_code(404); echo 'Arquivo não encontrado'; exit; }

    $mime = $row['Mime_Type'] ?: 'application/octet-stream';
    header('Content-Description: File Transfer');
    header('Content-Type: ' . $mime);
    header('Content-Disposition: attachment; filename="' . basename($row['Arquivo_Nome']) . '"');
    header('Content-Length: ' . filesize($full));
    header('Cache-Control: no-cache');

    readfile($full);
    exit;
} catch (Throwable $e) {
    http_response_code(500);
    echo 'Erro ao baixar: ' . $e->getMessage();
}
