<?php
require_once '../../config/conexao.php';
require_once '../../crud/DisciplinaCRUD.php';

header('Content-Type: application/json');

try {
    $disciplinaCRUD = new DisciplinaCRUD($pdo);

    $id = isset($_POST['id_disciplina']) ? intval($_POST['id_disciplina']) : 0;
    $nome = isset($_POST['nome_disciplina']) ? trim($_POST['nome_disciplina']) : '';
    $carga = isset($_POST['carga_horaria']) && $_POST['carga_horaria'] !== '' ? intval($_POST['carga_horaria']) : null;
    $ano = isset($_POST['ano_letivo']) && $_POST['ano_letivo'] !== '' ? intval($_POST['ano_letivo']) : null;
    $etapa = isset($_POST['etapa']) ? trim($_POST['etapa']) : null;

    if ($id <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'ID inválido']);
        exit;
    }
    if ($nome === '' || $carga === null) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Nome e carga horária são obrigatórios']);
        exit;
    }

    $dados = [
        'Nome_Disciplina' => $nome,
        'Carga_Horaria' => $carga,
        'Etapa' => $etapa
    ];
    if ($ano !== null) { $dados['Ano_Letivo'] = $ano; }

    $ok = $disciplinaCRUD->atualizar($id, $dados);

    echo json_encode(['success' => (bool)$ok, 'message' => $ok ? 'Disciplina atualizada' : 'Nenhuma alteração aplicada']);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
