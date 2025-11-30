<?php
require_once '../../../bootstrap.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'professor' || !isset($_SESSION['usuario_id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Acesso negado']);
    exit;
}

$turmaId = isset($_GET['turma_id']) ? (int)$_GET['turma_id'] : 0;
$idMatricula = isset($_GET['id_matricula']) ? (int)$_GET['id_matricula'] : 0;
$inicio = isset($_GET['inicio']) ? trim($_GET['inicio']) : '';
$fim = isset($_GET['fim']) ? trim($_GET['fim']) : '';

if ($turmaId <= 0) {
    echo json_encode(['success' => false, 'message' => 'turma_id inválido']);
    exit;
}

try {
    $profId = (int)$_SESSION['usuario_id'];
    // Verifica acesso do professor à turma
    $stmtChk = $pdo->prepare('SELECT 1 FROM Professores_Turmas WHERE ID_Professor = ? AND ID_Turma = ?');
    $stmtChk->execute([$profId, $turmaId]);
    if (!$stmtChk->fetch()) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Você não tem acesso a esta turma']);
        exit;
    }

    // Janela padrão: últimos 30 dias
    if ($inicio === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $inicio)) {
        $inicio = (new DateTime('-30 days'))->format('Y-m-d');
    }
    if ($fim === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fim)) {
        $fim = (new DateTime())->format('Y-m-d');
    }

    $params = [$turmaId, $inicio, $fim];
    $whereMat = '';
    if ($idMatricula > 0) { $whereMat = ' AND o.ID_Matricula = ?'; $params[] = $idMatricula; }

    $sql = "SELECT 
                o.ID_Ocorrencia, o.ID_Turma, o.ID_Matricula, o.Data, o.Tipo, o.Descricao, o.ID_Professor, o.DataHoraRegistro,
                u.Nome_Completo AS Nome_Aluno, a.Matricula
            FROM Ocorrencias o
            INNER JOIN Matriculas m ON m.ID_Matricula = o.ID_Matricula
            INNER JOIN Alunos a ON a.ID_Aluno = m.ID_Aluno
            INNER JOIN Usuarios u ON u.ID_Usuario = a.ID_Aluno
            WHERE o.ID_Turma = ? AND o.Data BETWEEN ? AND ?" . $whereMat . "
            ORDER BY o.Data DESC, o.ID_Ocorrencia DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'data' => $rows]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
