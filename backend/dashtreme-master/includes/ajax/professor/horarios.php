<?php
require_once '../../bootstrap.php';

header('Content-Type: application/json');

try {
    if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'professor') {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Acesso negado']);
        exit;
    }

    $profId = (int)$_SESSION['usuario_id'];

    $ano = isset($_GET['ano']) ? (int)$_GET['ano'] : null;

    $sql = "SELECT h.*, t.Nome_Turma, d.Nome_Disciplina
        FROM Horarios h
        INNER JOIN Turmas t ON t.ID_Turma = h.ID_Turma
        INNER JOIN Disciplinas d ON d.ID_Disciplina = h.ID_Disciplina
        WHERE (h.ID_Professor = ? OR d.ID_Professor = ?)";
    $params = [$profId, $profId];

    if ($ano) {
        $sql .= " AND (h.Ano_Letivo = ? OR t.Ano_Letivo = ?)";
        $params[] = $ano; $params[] = $ano;
    }
    $sql .= " ORDER BY h.Dia_Semana ASC, h.Hora_Inicio ASC";

    $st = $pdo->prepare($sql);
    $st->execute($params);
    $rows = $st->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'data' => $rows]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
