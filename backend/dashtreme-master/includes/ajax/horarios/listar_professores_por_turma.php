<?php
require_once '../../bootstrap.php';

header('Content-Type: application/json');

try {
    if (!isset($_GET['turma_id']) || !is_numeric($_GET['turma_id'])) {
        echo json_encode(['success' => false, 'message' => 'Parâmetro turma_id é obrigatório e deve ser numérico.']);
        exit;
    }

    $turmaId = (int) $_GET['turma_id'];

    $stmt = $pdo->prepare('SELECT p.ID_Professor, u.Nome_Completo
                           FROM Professores_Turmas pt
                           INNER JOIN Professores p ON pt.ID_Professor = p.ID_Professor
                           INNER JOIN Usuarios u ON u.ID_Usuario = p.ID_Professor
                           WHERE pt.ID_Turma = ?');
    $stmt->execute([$turmaId]);

    $professores = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'data' => $professores]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}