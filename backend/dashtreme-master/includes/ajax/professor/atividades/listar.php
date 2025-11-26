<?php
require_once '../../../bootstrap.php';

header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'professor' || !isset($_SESSION['usuario_id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Acesso negado']);
    exit;
}

$idTurma = isset($_GET['turma_id']) ? (int)$_GET['turma_id'] : 0;
$filtroDisciplina = isset($_GET['disciplina']) ? trim($_GET['disciplina']) : '';
if ($idTurma <= 0) {
    echo json_encode(['success' => false, 'message' => 'turma_id inválido']);
    exit;
}

try {

    // Ano letivo da turma
    $stmt = $pdo->prepare('SELECT Ano_Letivo FROM Turmas WHERE ID_Turma = ?');
    $stmt->execute([$idTurma]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $ano = $row ? (int)$row['Ano_Letivo'] : null;

    $params = [$idTurma];
    $sql = 'SELECT ID_Atividade, ID_Turma, ID_Professor, Titulo, Disciplina, Data, Ano_Letivo FROM Atividades WHERE ID_Turma = ?';
    if ($ano) { $sql .= ' AND Ano_Letivo = ?'; $params[] = $ano; }
    if ($filtroDisciplina !== '' && strcasecmp($filtroDisciplina, 'Todas') !== 0) { $sql .= ' AND Disciplina = ?'; $params[] = $filtroDisciplina; }
    $sql .= ' ORDER BY Data ASC, ID_Atividade DESC';

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'data' => $rows]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
