<?php
require_once '../../../bootstrap.php';
require_once '../../../crud/TurmaCRUD.php';

header('Content-Type: application/json');

$ano = isset($_GET['ano']) ? (int)$_GET['ano'] : null;
$turno = isset($_GET['turno']) && $_GET['turno'] !== '' ? $_GET['turno'] : null;
$professorId = isset($_GET['professor_id']) && $_GET['professor_id'] !== '' ? (int)$_GET['professor_id'] : null;
$listAll = isset($_GET['all']) && $_GET['all'] == '1';
// debug removido

try {
    $params = [];
    $isProfessor = isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'professor' && isset($_SESSION['usuario_id']);

    // Sempre use alias 't' para consistência nas condições
    $sql = "SELECT t.ID_Turma, t.Nome_Turma, t.Ano_Letivo, t.Turno, t.Etapa FROM Turmas t WHERE 1=1";

    if ($isProfessor && !$listAll) {
        $profId = (int)$_SESSION['usuario_id'];
        // Permite ver turmas vinculadas pelo vínculo direto OU por horários/disciplinas do professor
        $sql .= " AND ( 
            EXISTS (SELECT 1 FROM Professores_Turmas pt WHERE pt.ID_Turma = t.ID_Turma AND pt.ID_Professor = ?) 
            OR EXISTS (
                SELECT 1 FROM Horarios h 
                LEFT JOIN Disciplinas d ON d.ID_Disciplina = h.ID_Disciplina 
                WHERE h.ID_Turma = t.ID_Turma AND (h.ID_Professor = ? OR d.ID_Professor = ?)
            )
        )";
        array_push($params, $profId, $profId, $profId);
    } elseif ($professorId && !$listAll) {
        // Quando admin filtra por professor, aplica mesma lógica ampliada
        $sql .= " AND ( 
            EXISTS (SELECT 1 FROM Professores_Turmas pt WHERE pt.ID_Turma = t.ID_Turma AND pt.ID_Professor = ?) 
            OR EXISTS (
                SELECT 1 FROM Horarios h 
                LEFT JOIN Disciplinas d ON d.ID_Disciplina = h.ID_Disciplina 
                WHERE h.ID_Turma = t.ID_Turma AND (h.ID_Professor = ? OR d.ID_Professor = ?)
            )
        )";
        array_push($params, $professorId, $professorId, $professorId);
    }

    if ($ano) {
        // Considera turmas cujo Ano_Letivo é o informado OU que possuem horários naquele ano
        $sql .= " AND (t.Ano_Letivo = ? OR EXISTS (SELECT 1 FROM Horarios h WHERE h.ID_Turma = t.ID_Turma AND h.Ano_Letivo = ?))";
        $params[] = $ano;
        $params[] = $ano;
    }
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
