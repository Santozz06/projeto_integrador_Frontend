<?php
require_once '../../../config/conexao.php';

header('Content-Type: application/json');
        
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

$alunoId = isset($_POST['aluno_id']) ? (int) $_POST['aluno_id'] : 0;
$dataTransferencia = isset($_POST['data_transferencia']) && $_POST['data_transferencia'] !== ''
    ? $_POST['data_transferencia']
    : date('Y-m-d');
$escolaDestino = isset($_POST['escola_destino']) ? trim($_POST['escola_destino']) : '';
$municipioUF = isset($_POST['municipio_uf']) ? trim($_POST['municipio_uf']) : '';

if ($alunoId <= 0) {
    echo json_encode(['success' => false, 'message' => 'aluno_id inválido']);
        exit;
}

try {
    // Encontra a matrícula ativa mais recente do aluno
        $sqlMat = "SELECT ID_Matricula FROM Matriculas 
               WHERE ID_Aluno = ? AND Status = 'Ativa'
               ORDER BY Ano_Letivo DESC, Data_Matricula DESC
               LIMIT 1";
    $stmt = $pdo->prepare($sqlMat);
    $stmt->execute([$alunoId]);
    $mat = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$mat) {
        echo json_encode(['success' => false, 'message' => 'Aluno não possui matrícula ativa.']);
        exit;
    }

    $idMatricula = (int) $mat['ID_Matricula'];

    // Tenta registrar saída com data
    try {
        $sqlUp = "UPDATE Matriculas SET Status = 'Inativa', Data_Saida = ? WHERE ID_Matricula = ?";
        $stUp = $pdo->prepare($sqlUp);
        $stUp->execute([$dataTransferencia, $idMatricula]);
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Unknown column') !== false) {
            $sqlUp2 = "UPDATE Matriculas SET Status = 'Inativa' WHERE ID_Matricula = ?";
            $stUp2 = $pdo->prepare($sqlUp2);
            $stUp2->execute([$idMatricula]);
        } else {
            throw $e;
        }
    }

    echo json_encode([
        'success' => true,
        'message' => 'Transferência registrada com sucesso.',
        'transferencia' => [
            'aluno_id' => $alunoId,
            'id_matricula' => $idMatricula,
            'data_transferencia' => $dataTransferencia,
            'escola_destino' => $escolaDestino,
            'municipio_uf' => $municipioUF
        ]
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>