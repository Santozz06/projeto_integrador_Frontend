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
$anoLetivo = isset($_POST['ano_letivo']) ? (int)$_POST['ano_letivo'] : 0;
$novaTurmaId = isset($_POST['nova_turma_id']) ? (int)$_POST['nova_turma_id'] : 0;
$telefone = isset($_POST['telefone']) ? trim($_POST['telefone']) : null;
$endereco = isset($_POST['endereco']) ? trim($_POST['endereco']) : null;
$email = isset($_POST['email']) ? trim($_POST['email']) : null;

if ($alunoId <= 0 || $anoLetivo <= 0 || $novaTurmaId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Parâmetros inválidos']);
    exit;
}

try {
    // Inicia transação para garantir consistência
    $pdo->beginTransaction();
    // Checa se já existe matrícula ativa para o ano informado
    $stAtiva = $pdo->prepare('SELECT 1 FROM Matriculas WHERE ID_Aluno = ? AND Ano_Letivo = ? AND Status = "Ativa" LIMIT 1');
    $stAtiva->execute([$alunoId, $anoLetivo]);
    if ($stAtiva->fetch()) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Aluno já possui matrícula ativa para este ano letivo.']);
        exit;
    }

    // Valida a turma
    $stTurma = $pdo->prepare('SELECT ID_Turma, Ano_Letivo, Turno, Nome_Turma FROM Turmas WHERE ID_Turma = ?');
    $stTurma->execute([$novaTurmaId]);
    $turma = $stTurma->fetch(PDO::FETCH_ASSOC);
    if (!$turma) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Turma de destino não encontrada.']);
        exit;
    }
    if ((int)$turma['Ano_Letivo'] !== $anoLetivo) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'A turma selecionada não corresponde ao ano letivo informado.']);
        exit;
    }

    // Atualiza dados de contato do aluno (opcional)
    if ($telefone !== null || $endereco !== null || $email !== null) {
        $campos = [];
        $params = [];
        if ($telefone !== null) { $campos[] = 'Telefone = ?'; $params[] = $telefone; }
        if ($endereco !== null) { $campos[] = 'Endereco = ?'; $params[] = $endereco; }
        if ($email !== null) { $campos[] = 'Email = ?'; $params[] = $email; $campos[] = 'Login = ?'; $params[] = $email; }
        if (!empty($campos)) {
            $sqlUpd = 'UPDATE Usuarios SET ' . implode(', ', $campos) . ' WHERE ID_Usuario = ?';
            $params[] = $alunoId;
            $stUpd = $pdo->prepare($sqlUpd);
            $stUpd->execute($params);
        }
    }

    // Desvincula matrículas ativas anteriores do aluno para evitar duplicidade
    // Tenta detectar a coluna Data_Saida
    $hasDataSaida = false;
    try {
        $col = $pdo->query("SHOW COLUMNS FROM Matriculas LIKE 'Data_Saida'");
        $hasDataSaida = (bool) $col->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $ie) {
        $hasDataSaida = false;
    }
    if ($hasDataSaida) {
        $sqlClose = "UPDATE Matriculas SET Status='Inativa', Data_Saida = IFNULL(Data_Saida, CURDATE()) WHERE ID_Aluno = ? AND Status = 'Ativa'";
    } else {
        $sqlClose = "UPDATE Matriculas SET Status='Inativa' WHERE ID_Aluno = ? AND Status = 'Ativa'";
    }
    $stClose = $pdo->prepare($sqlClose);
    $stClose->execute([$alunoId]);

    // Cria a nova matrícula para o ano informado (status Ativa)
    require_once '../../includes/crud/MatriculaCRUD.php';
    $crud = new MatriculaCRUD($pdo);
    $novaId = $crud->matricularAluno($alunoId, $novaTurmaId, 'Rematricula', $anoLetivo);

    $pdo->commit();
    echo json_encode(['success' => true, 'message' => 'Rematrícula realizada com sucesso. Matrícula anterior encerrada.', 'id_matricula' => $novaId]);
} catch (Exception $e) {
    if ($pdo->inTransaction()) { $pdo->rollBack(); }
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
