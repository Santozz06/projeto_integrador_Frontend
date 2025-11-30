<?php
require_once '../../../bootstrap.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'professor' || !isset($_SESSION['usuario_id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Acesso negado']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

$profId = (int)$_SESSION['usuario_id'];

// Suporta JSON no corpo
$raw = file_get_contents('php://input');
$payload = json_decode($raw, true);
if (!is_array($payload)) {
    $payload = $_POST; 
}

$turmaId = isset($payload['turma_id']) ? (int)$payload['turma_id'] : 0;
$data = isset($payload['data']) ? trim($payload['data']) : '';
$updates = isset($payload['updates']) && is_array($payload['updates']) ? $payload['updates'] : [];

if ($turmaId <= 0 || $data === '' || empty($updates)) {
    echo json_encode(['success' => false, 'message' => 'Parâmetros obrigatórios ausentes']);
    exit;
}

try {

    $pdo->beginTransaction();

    $sql = 'INSERT INTO Presencas (ID_Turma, ID_Matricula, Data, Status, ID_Professor) VALUES (?,?,?,?,?)
            ON DUPLICATE KEY UPDATE Status = VALUES(Status), ID_Professor = VALUES(ID_Professor), DataHoraRegistro = CURRENT_TIMESTAMP';
    $stmt = $pdo->prepare($sql);

    foreach ($updates as $u) {
        $idMatricula = isset($u['id_matricula']) ? (int)$u['id_matricula'] : 0;
        $statusText = isset($u['status']) ? strtolower(trim($u['status'])) : '';
        if ($idMatricula <= 0) { continue; }
        // Converte status textual para código
        $code = 'P';
        if ($statusText === 'ausente' || $statusText === 'a') $code = 'A';
        elseif ($statusText === 'justificado' || $statusText === 'j') $code = 'J';
        $stmt->execute([$turmaId, $idMatricula, $data, $code, $profId]);
    }

    $pdo->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    if ($pdo && $pdo->inTransaction()) { $pdo->rollBack(); }
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
