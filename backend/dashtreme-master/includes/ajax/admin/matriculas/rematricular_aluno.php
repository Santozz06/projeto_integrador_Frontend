<?php
require_once '../../../config/conexao.php';

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
$cep = isset($_POST['cep']) ? trim($_POST['cep']) : null;
$logradouro = isset($_POST['logradouro']) ? trim($_POST['logradouro']) : null;
$numero = isset($_POST['numero']) ? trim($_POST['numero']) : null;
$complemento = isset($_POST['complemento']) ? trim($_POST['complemento']) : null;
$bairro = isset($_POST['bairro']) ? trim($_POST['bairro']) : null;
$ufEndereco = isset($_POST['uf_endereco']) ? trim($_POST['uf_endereco']) : null;
$municipio = isset($_POST['municipio']) ? trim($_POST['municipio']) : null;
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
    if ($telefone !== null || $cep !== null || $logradouro !== null || $numero !== null || 
        $complemento !== null || $bairro !== null || $ufEndereco !== null || $municipio !== null || $email !== null) {
        $campos = [];
        $params = [];
        if ($telefone !== null) { $campos[] = 'Telefone = ?'; $params[] = $telefone; }
        if ($cep !== null) { $campos[] = 'CEP = ?'; $params[] = $cep; }
        if ($logradouro !== null) { $campos[] = 'Logradouro = ?'; $params[] = $logradouro; }
        if ($numero !== null) { $campos[] = 'Numero = ?'; $params[] = $numero; }
        if ($complemento !== null) { $campos[] = 'Complemento = ?'; $params[] = $complemento; }
        if ($bairro !== null) { $campos[] = 'Bairro = ?'; $params[] = $bairro; }
        if ($ufEndereco !== null) { $campos[] = 'UF_Endereco = ?'; $params[] = $ufEndereco; }
        if ($municipio !== null) { $campos[] = 'Municipio_Endereco = ?'; $params[] = $municipio; }
        
        // Monta o campo Endereco concatenado para compatibilidade
        if ($logradouro !== null && $numero !== null && $bairro !== null) {
            $enderecoCompleto = $logradouro . ', ' . $numero . ' - ' . $bairro;
            $campos[] = 'Endereco = ?';
            $params[] = $enderecoCompleto;
        }
        
        if ($email !== null) { 
            $campos[] = 'Email = ?'; 
            $params[] = $email;
        }
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
