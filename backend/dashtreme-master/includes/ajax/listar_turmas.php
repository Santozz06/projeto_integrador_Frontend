<?php
require_once '../../includes/bootstrap.php';
require_once '../../includes/conexao.php';
require_once '../../includes/crud/TurmaCRUD.php';

header('Content-Type: application/json');

$ano = isset($_GET['ano']) ? (int)$_GET['ano'] : null;
$turno = isset($_GET['turno']) && $_GET['turno'] !== '' ? $_GET['turno'] : null;
$professorId = isset($_GET['professor_id']) && $_GET['professor_id'] !== '' ? (int)$_GET['professor_id'] : null;

try {
    $params = [];
    $isProfessor = isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'professor' && isset($_SESSION['usuario_id']);

    // Sempre use alias 't' para consistência nas condições
    $sql = "SELECT t.ID_Turma, t.Nome_Turma, t.Ano_Letivo, t.Turno, t.Etapa FROM Turmas t WHERE 1=1";

    if ($isProfessor) {
        $sql .= " AND EXISTS (SELECT 1 FROM Professores_Turmas pt WHERE pt.ID_Turma = t.ID_Turma AND pt.ID_Professor = ?)";
        $params[] = (int)$_SESSION['usuario_id'];
    } elseif ($professorId) {
        // Quando admin filtra por professor
        $sql .= " AND EXISTS (SELECT 1 FROM Professores_Turmas pt WHERE pt.ID_Turma = t.ID_Turma AND pt.ID_Professor = ?)";
        $params[] = $professorId;
    }

    if ($ano) { $sql .= " AND t.Ano_Letivo = ?"; $params[] = $ano; }
    if ($turno) { $sql .= " AND t.Turno = ?"; $params[] = $turno; }
    $sql .= " ORDER BY t.Nome_Turma";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $turmas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'data' => $turmas]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
