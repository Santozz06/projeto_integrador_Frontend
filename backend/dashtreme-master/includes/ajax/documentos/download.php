<?php
require_once '../../config/conexao.php';

try {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    if ($id <= 0) { http_response_code(400); echo 'ID inválido'; exit; }

    $st = $pdo->prepare('SELECT Titulo, Arquivo_Nome, Arquivo_Conteudo, Mime_Type FROM Documentos WHERE ID_Documento = ? AND Ativo = 1');
    $st->execute([$id]);
    $row = $st->fetch(PDO::FETCH_ASSOC);
    if (!$row) { http_response_code(404); echo 'Documento não encontrado'; exit; }

    $mime = $row['Mime_Type'] ?: 'application/octet-stream';
    $conteudo = $row['Arquivo_Conteudo'];
    
    header('Content-Description: File Transfer');
    header('Content-Type: ' . $mime);
    header('Content-Disposition: attachment; filename="' . basename($row['Arquivo_Nome']) . '"');
    header('Content-Length: ' . strlen($conteudo));
    header('Cache-Control: no-cache');

    echo $conteudo;
    exit;
} catch (Throwable $e) {
    http_response_code(500);
    echo 'Erro ao baixar: ' . $e->getMessage();
}

