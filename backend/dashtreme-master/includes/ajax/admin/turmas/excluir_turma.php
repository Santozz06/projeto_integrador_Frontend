<?php
require_once '../../../config/conexao.php';
require_once '../../../crud/TurmaCRUD.php';

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id_turma'])) {
        echo json_encode(['success' => false, 'message' => 'Método não permitido ou ID não informado']);
        exit;
    }

    $turmaCRUD = new TurmaCRUD($pdo);
    $result = $turmaCRUD->excluir($_POST['id_turma']);
    
    if ($result) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao excluir turma']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>