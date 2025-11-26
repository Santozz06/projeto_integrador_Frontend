<?php
require_once '../../bootstrap.php';

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

    // Ler conteúdo do arquivo para salvar no BD
    $conteudo = file_get_contents($file['tmp_name']);
    if ($conteudo === false) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Falha ao ler arquivo temporário']);
        exit;
    }

    // Inserir documento diretamente no banco
    $st = $pdo->prepare("INSERT INTO Documentos (Tipo, Titulo, Descricao, Data_Vigencia, Arquivo_Nome, Arquivo_Conteudo, Mime_Type, Tamanho_Bytes)
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $st->execute([$tipo, $titulo, $descricao, $dataVigencia, $origName, $conteudo, $mime, $size]);
    $id = (int)$pdo->lastInsertId();

    echo json_encode(['success' => true, 'id' => $id, 'nome' => $origName, 'tamanho' => $size]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
