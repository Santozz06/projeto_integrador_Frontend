<?php
require_once '../../includes/bootstrap.php';
require_once '../../includes/conexao.php';
require_once '../../includes/crud/TurmaCRUD.php';

header('Content-Type: application/json');

$ano = isset($_GET['ano']) ? (int)$_GET['ano'] : null;
$turno = isset($_GET['turno']) && $_GET['turno'] !== '' ? $_GET['turno'] : null;

try {
    $params = [];
    $sql = "SELECT ID_Turma, Nome_Turma, Ano_Letivo, Turno, Etapa FROM Turmas WHERE 1=1";
    if ($ano) { $sql .= " AND Ano_Letivo = ?"; $params[] = $ano; }
    if ($turno) { $sql .= " AND Turno = ?"; $params[] = $turno; }
    $sql .= " ORDER BY Nome_Turma";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $turmas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'data' => $turmas]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
