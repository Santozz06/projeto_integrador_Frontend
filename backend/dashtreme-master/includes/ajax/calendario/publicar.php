<?php
require_once '../../bootstrap.php';

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Método não permitido']);
        exit;
    }

    // Apenas admin
    if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Acesso negado']);
        exit;
    }

    $publico = isset($_POST['publico']) ? strtolower(trim($_POST['publico'])) : '';
    $start = isset($_POST['start']) ? substr($_POST['start'], 0, 10) : null; // YYYY-MM-DD
    $end   = isset($_POST['end']) ? substr($_POST['end'], 0, 10) : null;     // YYYY-MM-DD
    $tipo  = isset($_POST['tipo']) && $_POST['tipo'] !== 'all' ? trim($_POST['tipo']) : null;

    if (!in_array($publico, ['todos','professores','alunos'], true)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Público inválido']);
        exit;
    }

    if (!$start || !$end) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Período (start/end) é obrigatório']);
        exit;
    }

    // Garantir existência da coluna
    try {
        $chk = $pdo->query("SHOW COLUMNS FROM Calendario_Academico LIKE 'Publico_Alvo'");
        if ($chk->rowCount() === 0) {
            $pdo->exec("ALTER TABLE Calendario_Academico ADD COLUMN Publico_Alvo VARCHAR(20) DEFAULT 'todos' AFTER Tipo_Evento");
        }
    } catch (Throwable $e) { }

    $sql = "UPDATE Calendario_Academico
            SET Publico_Alvo = :publico
            WHERE Data_Inicio >= :start AND (Data_Fim IS NULL OR Data_Fim <= :end)";
    $params = [
        ':publico' => $publico,
        ':start' => $start,
        ':end' => $end
    ];

    if ($tipo) { $sql .= " AND Tipo_Evento = :tipo"; $params[':tipo'] = $tipo; }

    $st = $pdo->prepare($sql);
    $st->execute($params);

    echo json_encode(['success' => true, 'updated' => $st->rowCount()]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
