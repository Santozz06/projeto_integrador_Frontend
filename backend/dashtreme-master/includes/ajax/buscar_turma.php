<?php
require_once '../../includes/bootstrap.php';
require_once '../../includes/conexao.php';
require_once '../../includes/crud/TurmaCRUD.php';

header('Content-Type: application/json');

try {
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        echo json_encode(['error' => 'ID não informado']);
        exit;
    }

    $turmaCRUD = new TurmaCRUD($pdo);
    $turma = $turmaCRUD->buscar($_GET['id']);
    
    if ($turma) {
        echo json_encode($turma);
    } else {
        echo json_encode(['error' => 'Turma não encontrada']);
    }
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>