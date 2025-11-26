<?php
require_once '../../../config/conexao.php';
require_once '../../../crud/LocalidadeCRUD.php';

header('Content-Type: application/json');

try {
    $crud = new LocalidadeCRUD($pdo);
    $paises = $crud->listarPaises();
    echo json_encode(['success' => true, 'data' => $paises]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
