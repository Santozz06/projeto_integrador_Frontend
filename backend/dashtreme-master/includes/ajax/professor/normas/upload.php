<?php
require_once '../../bootstrap.php';

// Para upload de arquivos
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'professor') {
    http_response_code(403);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Acesso negado']);
    exit;
}

$root = realpath(__DIR__ . '/../../../..'); // .../backend/dashtreme-master
$uploadAbs = $root . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'normas';
if (!is_dir($uploadAbs)) {
    @mkdir($uploadAbs, 0775, true);
}

$stdNames = [
    'normas' => 'normas_academicas.pdf',
    'avaliacoes' => 'avaliacoes_recuperacoes.pdf',
    'frequencia' => 'frequencia_pontualidade.pdf',
];

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método inválido');
    }

    if (!isset($_FILES['arquivo'])) {
        throw new Exception('Arquivo não enviado');
    }

    $categoria = isset($_POST['categoria']) ? trim($_POST['categoria']) : '';
    $file = $_FILES['arquivo'];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('Falha no upload (código ' . $file['error'] . ')');
    }

    // Validação básica de tipo
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    if ($mime !== 'application/pdf') {
        throw new Exception('Apenas PDFs são permitidos');
    }

    // Define o nome de destino
    $destName = null;
    if ($categoria && isset($stdNames[$categoria])) {
        $destName = $stdNames[$categoria]; // sobrescreve o padrão
    } else {
        // nome aleatório preservando extensão
        $base = pathinfo($file['name'], PATHINFO_FILENAME);
        $base = preg_replace('/[^a-zA-Z0-9_-]+/', '_', $base);
        if ($base === '' || $base === null) { $base = 'norma'; }
        $destName = $base . '_' . date('Ymd_His') . '.pdf';
    }

    $destAbs = $uploadAbs . DIRECTORY_SEPARATOR . $destName;

    if (!move_uploaded_file($file['tmp_name'], $destAbs)) {
        throw new Exception('Não foi possível salvar o arquivo');
    }

    // URL relativa a partir da página user_professor
    $webUrl = '..' . '/uploads/normas/' . $destName;

    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'file' => [ 'name' => $destName, 'url' => $webUrl ]]);
} catch (Exception $e) {
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
