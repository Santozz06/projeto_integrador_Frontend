<?php
// Handler para remoção de vínculos de aluno/professor com turma
// Caminhos relativos ajustados ao diretório atual (includes/ajax)
require_once '../../config/conexao.php';
require_once '../crud/VinculoCRUD.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

$tipo = $_POST['tipo'] ?? '';

try {
    $vinculoCRUD = new VinculoCRUD($pdo);

    if ($tipo === 'aluno') {
        $idMatricula = $_POST['id_matricula'] ?? '';
        if (empty($idMatricula)) {
            echo json_encode(['success' => false, 'message' => 'ID da matrícula não informado']);
            exit;
        }
        $ok = $vinculoCRUD->removerVinculoAluno($idMatricula);
        echo json_encode(['success' => (bool)$ok]);
        exit;
    } elseif ($tipo === 'professor') {
        $idProfessor = $_POST['id_professor'] ?? '';
        $idTurma = $_POST['id_turma'] ?? '';
        if (empty($idProfessor) || empty($idTurma)) {
            echo json_encode(['success' => false, 'message' => 'IDs de professor e turma são obrigatórios']);
            exit;
        }
        $ok = $vinculoCRUD->removerVinculoProfessor($idProfessor, $idTurma);
        echo json_encode(['success' => (bool)$ok]);
        exit;
    } else {
        echo json_encode(['success' => false, 'message' => 'Tipo inválido']);
        exit;
    }
} catch (Exception $e) {
    error_log('Erro ao remover vínculo: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>