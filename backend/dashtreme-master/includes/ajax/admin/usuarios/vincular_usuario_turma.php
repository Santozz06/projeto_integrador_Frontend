<?php
require_once __DIR__ . '/../../../config/conexao.php';
require_once __DIR__ . '/../../../crud/VinculoCRUD.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

$tipo = $_POST['tipo'] ?? '';
$usuario_id = $_POST['usuario_id'] ?? '';
$turma_id = $_POST['turma_id'] ?? '';

if (empty($tipo) || empty($usuario_id) || empty($turma_id)) {
    echo json_encode(['success' => false, 'message' => 'Dados incompletos']);
    exit;
}

try {
    $vinculoCRUD = new VinculoCRUD($pdo);

    if ($tipo === 'aluno') {
        $vinculoCRUD->vincularAluno($usuario_id, $turma_id);
    } else {
        $vinculoCRUD->vincularProfessor($usuario_id, $turma_id);
    }

    echo json_encode(['success' => true, 'message' => 'Vínculo realizado com sucesso']);
    
} catch (Exception $e) {
    error_log("Erro ao vincular usuário à turma: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>