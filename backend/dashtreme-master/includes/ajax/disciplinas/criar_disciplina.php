<?php
header('Content-Type: application/json');

try {
    require_once dirname(__DIR__, 2) . '/bootstrap.php';

    $ano = isset($_POST['ano_letivo']) ? intval($_POST['ano_letivo']) : null;
    $idProfessor = isset($_POST['id_professor']) ? intval($_POST['id_professor']) : null;
    $nome = isset($_POST['nome_disciplina']) ? trim($_POST['nome_disciplina']) : '';
    $carga = isset($_POST['carga_horaria']) && $_POST['carga_horaria'] !== '' ? intval($_POST['carga_horaria']) : null;
    $etapa = isset($_POST['etapa']) ? trim($_POST['etapa']) : null;

    if (!$ano || !$idProfessor || $nome === '' || $carga === null) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Campos obrigatórios não informados']);
        exit;
    }

    $dados = [
        'Nome_Disciplina' => $nome,
        'Carga_Horaria' => $carga,
        'Ano_Letivo' => $ano,
        'Etapa' => $etapa,
        'ID_Professor' => $idProfessor
    ];

    $id = $disciplinaCRUD->criarDisciplina($dados);

    echo json_encode(['success' => true, 'message' => 'Disciplina criada', 'id' => $id]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
