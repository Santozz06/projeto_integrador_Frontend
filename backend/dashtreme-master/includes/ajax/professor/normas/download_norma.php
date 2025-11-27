<?php
require_once '../../../bootstrap.php';

if (!isset($_GET['name']) || trim($_GET['name']) === '') {
    http_response_code(400);
    echo 'Nome do arquivo não especificado.';
    exit;
}

$name = trim($_GET['name']);
// Segurança básica
if (strpos($name, '..') !== false || strpos($name, '/') !== false || strpos($name, '\\') !== false) {
    http_response_code(400);
    echo 'Nome de arquivo inválido.';
    exit;
}

$stmt = $pdo->prepare('SELECT Arquivo_Conteudo, Mime_Type, Tamanho_Bytes FROM Documentos WHERE Arquivo_Nome = ? AND Tipo = "norma" AND Ativo = 1');
$stmt->execute([$name]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    http_response_code(404);
    echo 'Arquivo não encontrado.';
    exit;
}

header('Content-Type: ' . ($row['Mime_Type'] ?: 'application/pdf'));
header('Content-Length: ' . $row['Tamanho_Bytes']);
header('Content-Disposition: attachment; filename="' . basename($name) . '"');
echo $row['Arquivo_Conteudo'];
exit;
