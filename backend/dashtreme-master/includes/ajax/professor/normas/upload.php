<?php
require_once '../../../bootstrap.php';

// Para upload de arquivos
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'professor') {
    http_response_code(403);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Acesso negado']);
    exit;
}

$root = realpath(__DIR__ . '/../../../..');
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


    // Define o nome de destino, evitando duplicidade
    $destName = null;
    if ($categoria && isset($stdNames[$categoria])) {
        $baseName = pathinfo($stdNames[$categoria], PATHINFO_FILENAME);
        $ext = '.pdf';
    } else {
        $baseName = pathinfo($file['name'], PATHINFO_FILENAME);
        $baseName = preg_replace('/[^a-zA-Z0-9_-]+/', '_', $baseName);
        if ($baseName === '' || $baseName === null) { $baseName = 'norma'; }
        $ext = '.pdf';
    }

    $destName = $baseName . $ext;
    $i = 1;
    $stmtCheck = $pdo->prepare('SELECT COUNT(*) FROM Documentos WHERE Arquivo_Nome = ?');
    while (true) {
        $stmtCheck->execute([$destName]);
        $count = $stmtCheck->fetchColumn();
        if ($count == 0) break;
        $i++;
        $destName = $baseName . '-' . $i . $ext;
    }

    // Lê o conteúdo do arquivo
    $conteudo = file_get_contents($file['tmp_name']);
    if ($conteudo === false) {
        throw new Exception('Não foi possível ler o arquivo enviado');
    }

    // Salva no banco de dados 
    $stmt = $pdo->prepare('INSERT INTO Documentos (Tipo, Titulo, Descricao, Data_Vigencia, Arquivo_Nome, Arquivo_Conteudo, Mime_Type, Tamanho_Bytes, Ativo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)');
    $tipo = 'norma';
    $titulo = $destName;
    $descricao = $categoria;
    $dataVigencia = date('Y-m-d');
    $mimeType = $mime;
    $tamanho = strlen($conteudo);
    $stmt->bindParam(1, $tipo);
    $stmt->bindParam(2, $titulo);
    $stmt->bindParam(3, $descricao);
    $stmt->bindParam(4, $dataVigencia);
    $stmt->bindParam(5, $destName);
    $stmt->bindParam(6, $conteudo, PDO::PARAM_LOB);
    $stmt->bindParam(7, $mimeType);
    $stmt->bindParam(8, $tamanho, PDO::PARAM_INT);
    $stmt->execute();

    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'file' => [ 'name' => $destName ]]);
} catch (Exception $e) {
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
