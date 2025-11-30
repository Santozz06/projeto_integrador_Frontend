<?php
require_once '../../../bootstrap.php';

header('Content-Type: application/json');

// Somente professores autenticados
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'professor' || !isset($_SESSION['usuario_id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Acesso negado']);
    exit;
}

try {
   
    $profId = (int)$_SESSION['usuario_id'];

    // Parâmetros opcionais
    $limite = isset($_GET['limit']) ? max(1, (int)$_GET['limit']) : 10;
    $apenasFuturas = isset($_GET['futuras']) ? (int)$_GET['futuras'] : 1;

    $params = [$profId];
    $sql = "SELECT a.ID_Avaliacao, a.ID_Turma, a.Disciplina, a.Tipo, a.Data, a.Ano_Letivo,
                   t.Nome_Turma, t.Turno
            FROM Avaliacoes a
            JOIN Turmas t ON t.ID_Turma = a.ID_Turma
            WHERE a.ID_Professor = ?";

    if ($apenasFuturas) {
        $sql .= " AND a.Data >= CURDATE()";
    }

    $sql .= " ORDER BY a.Data ASC, a.ID_Avaliacao DESC LIMIT " . (int)$limite;

    $st = $pdo->prepare($sql);
    $st->execute($params);
    $rows = $st->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'data' => $rows]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
