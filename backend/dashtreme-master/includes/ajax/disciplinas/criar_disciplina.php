<?php
require_once '../../config/conexao.php';
require_once '../../crud/DisciplinaCRUD.php';

header('Content-Type: application/json');

try {
    $disciplinaCRUD = new DisciplinaCRUD($pdo);

    $ano = isset($_POST['ano_letivo']) && $_POST['ano_letivo'] !== '' ? intval($_POST['ano_letivo']) : null;
    $idProfessor = isset($_POST['id_professor']) && $_POST['id_professor'] !== '' ? intval($_POST['id_professor']) : null;
    $nome = isset($_POST['nome_disciplina']) ? trim($_POST['nome_disciplina']) : '';
    $carga = isset($_POST['carga_horaria']) && $_POST['carga_horaria'] !== '' ? intval($_POST['carga_horaria']) : null;
    $etapa = isset($_POST['etapa']) ? trim($_POST['etapa']) : null;

    // Agora apenas nome e carga são obrigatórios; ano e professor são opcionais
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
    // Não vincular professor aqui; fluxo de atribuição é em outra tela

    $id = $disciplinaCRUD->criarDisciplina($dados);

    echo json_encode(['success' => true, 'message' => 'Disciplina criada', 'id' => $id]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
