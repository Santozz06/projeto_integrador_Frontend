<?php
require_once '../../bootstrap.php';

header('Content-Type: application/json');

try {
    if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Acesso negado']);
        exit;
    }
    $turmaId = isset($_GET['turma_id']) ? (int)$_GET['turma_id'] : null;
    $profId = isset($_GET['professor_id']) ? (int)$_GET['professor_id'] : null;
    $ano = isset($_GET['ano']) ? (int)$_GET['ano'] : null;

    $sql = "SELECT h.*, t.Nome_Turma, t.Ano_Letivo as Turma_Ano,
                   d.Nome_Disciplina, u.Nome_Completo AS Professor_Nome
            FROM Horarios h
            INNER JOIN Turmas t ON t.ID_Turma = h.ID_Turma
            INNER JOIN Disciplinas d ON d.ID_Disciplina = h.ID_Disciplina
            INNER JOIN Professores p ON p.ID_Professor = h.ID_Professor
            INNER JOIN Usuarios u ON u.ID_Usuario = p.ID_Professor
            WHERE 1=1";
    $params = [];
    if ($turmaId) { $sql .= " AND h.ID_Turma = ?"; $params[] = $turmaId; }
    if ($profId)  { $sql .= " AND h.ID_Professor = ?"; $params[] = $profId; }
    if ($ano)     { $sql .= " AND (h.Ano_Letivo = ? OR t.Ano_Letivo = ?)"; $params[] = $ano; $params[] = $ano; }
    $sql .= " ORDER BY h.Dia_Semana ASC, h.Hora_Inicio ASC";

    $st = $pdo->prepare($sql);
    $st->execute($params);
    $rows = $st->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'data' => $rows]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
