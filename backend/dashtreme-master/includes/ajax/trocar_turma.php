<?php
require_once '../../includes/bootstrap.php';
require_once '../../includes/conexao.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

$alunoId = isset($_POST['aluno_id']) ? (int)$_POST['aluno_id'] : 0;
$novaTurmaId = isset($_POST['nova_turma_id']) ? (int)$_POST['nova_turma_id'] : 0;

if ($alunoId <= 0 || $novaTurmaId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Parâmetros inválidos']);
    exit;
}

try {
    // Busca matrícula ativa atual
    $sqlAtual = "SELECT m.ID_Matricula, m.Ano_Letivo, m.ID_Turma FROM Matriculas m
                 WHERE m.ID_Aluno = ? AND m.Status = 'Ativa'
                 ORDER BY m.Ano_Letivo DESC, m.Data_Matricula DESC
                 LIMIT 1";
    $st = $pdo->prepare($sqlAtual);
    $st->execute([$alunoId]);
    $mat = $st->fetch(PDO::FETCH_ASSOC);
    if (!$mat) {
        echo json_encode(['success' => false, 'message' => 'Aluno sem matrícula ativa.']);
        exit;
    }

    $idMatricula = (int)$mat['ID_Matricula'];
    $anoAtual = (int)$mat['Ano_Letivo'];

    // Valida que a nova turma é do mesmo ano letivo
    $stT = $pdo->prepare('SELECT ID_Turma, Ano_Letivo, Turno, Nome_Turma, Etapa FROM Turmas WHERE ID_Turma = ?');
    $stT->execute([$novaTurmaId]);
    $turmaNova = $stT->fetch(PDO::FETCH_ASSOC);
    if (!$turmaNova) {
        echo json_encode(['success' => false, 'message' => 'Turma destino não encontrada.']);
        exit;
    }
    if ((int)$turmaNova['Ano_Letivo'] !== $anoAtual) {
        echo json_encode(['success' => false, 'message' => 'A nova turma deve ser do mesmo ano letivo.']);
        exit;
    }

    // Evita duplicidade: já está na turma de destino?
    $stDup = $pdo->prepare('SELECT 1 FROM Matriculas WHERE ID_Aluno = ? AND ID_Turma = ? AND Status = "Ativa" LIMIT 1');
    $stDup->execute([$alunoId, $novaTurmaId]);
    if ($stDup->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Aluno já está matriculado na turma escolhida.']);
        exit;
    }

    // Usa o método de transferência: marca a antiga como Transferida e cria nova matrícula
    require_once '../../includes/crud/MatriculaCRUD.php';
    $crud = new MatriculaCRUD($pdo);
    $novaId = $crud->transferirAluno($idMatricula, $novaTurmaId);

    echo json_encode(['success' => true, 'message' => 'Troca de turma realizada com sucesso.', 'id_matricula_nova' => $novaId]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
