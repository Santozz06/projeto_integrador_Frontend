<?php
require_once '../../config/conexao.php';
require_once '../../crud/DisciplinaCRUD.php';

header('Content-Type: application/json');

try {
    $disciplinaCRUD = new DisciplinaCRUD($pdo);

    $id = isset($_POST['id_disciplina']) ? intval($_POST['id_disciplina']) : 0;
    if ($id <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'ID inválido']);
        exit;
    }

    $ok = $disciplinaCRUD->excluir($id);
    echo json_encode(['success' => $ok, 'message' => $ok ? 'Excluído' : 'Não foi possível excluir']);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
