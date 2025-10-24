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

    // Apenas admin pode enviar
    if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Acesso negado']);
        exit;
    }

    // Light migration
    $pdo->exec("CREATE TABLE IF NOT EXISTS Documentos (
        ID_Documento INT AUTO_INCREMENT PRIMARY KEY,
        Tipo VARCHAR(30) NOT NULL,
        Titulo VARCHAR(200) NOT NULL,
        Descricao TEXT NULL,
        Data_Vigencia DATE NULL,
        Arquivo_Nome VARCHAR(255) NOT NULL,
        Arquivo_Caminho VARCHAR(255) NOT NULL,
        Mime_Type VARCHAR(100) NULL,
        Tamanho_Bytes BIGINT NULL,
        Ativo TINYINT(1) DEFAULT 1,
        Criado_Em DATETIME DEFAULT CURRENT_TIMESTAMP,
        Atualizado_Em DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $tipo = isset($_POST['tipo']) ? trim($_POST['tipo']) : '';
    $titulo = isset($_POST['titulo']) ? trim($_POST['titulo']) : '';
    $descricao = isset($_POST['descricao']) ? trim($_POST['descricao']) : null;
    $dataVigencia = isset($_POST['data_vigencia']) && $_POST['data_vigencia'] !== '' ? substr($_POST['data_vigencia'],0,10) : null;

    if ($tipo === '' || $titulo === '') {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Campos obrigatórios ausentes (tipo/título)']);
        exit;
    }

    if (!isset($_FILES['arquivo']) || $_FILES['arquivo']['error'] !== UPLOAD_ERR_OK) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Arquivo inválido ou ausente']);
        exit;
    }

    $file = $_FILES['arquivo'];
    $origName = $file['name'];
    $size = (int)$file['size'];
    $mime = mime_content_type($file['tmp_name']);

    $allowed = ['application/pdf','application/msword','application/vnd.openxmlformats-officedocument.wordprocessingml.document','application/vnd.ms-excel','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet','application/vnd.ms-powerpoint','application/vnd.openxmlformats-officedocument.presentationml.presentation'];
    $extAllowed = ['pdf','doc','docx','xls','xlsx','ppt','pptx'];

    $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
    if (!in_array($ext, $extAllowed, true)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Extensão não permitida']);
        exit;
    }

    // Caminho de upload
    $root = dirname(__DIR__, 3); // .../backend/dashtreme-master
    $dir = $root . '/uploads/documentos';
    if (!is_dir($dir)) { @mkdir($dir, 0775, true); }

    // Nome de arquivo seguro
    $slug = preg_replace('/[^a-z0-9\-]+/i', '-', strtolower(iconv('UTF-8','ASCII//TRANSLIT',$titulo)));
    $rand = bin2hex(random_bytes(4));
    $safeName = $slug . '-' . date('YmdHis') . '-' . $rand . '.' . $ext;

    $destPath = $dir . '/' . $safeName;
    if (!move_uploaded_file($file['tmp_name'], $destPath)) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Falha ao salvar arquivo no servidor']);
        exit;
    }

    $relPath = 'uploads/documentos/' . $safeName;

    $st = $pdo->prepare("INSERT INTO Documentos (Tipo, Titulo, Descricao, Data_Vigencia, Arquivo_Nome, Arquivo_Caminho, Mime_Type, Tamanho_Bytes)
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?)" );
    $st->execute([$tipo, $titulo, $descricao, $dataVigencia, $origName, $relPath, $mime, $size]);
    $id = (int)$pdo->lastInsertId();

    echo json_encode(['success' => true, 'id' => $id, 'path' => $relPath]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
