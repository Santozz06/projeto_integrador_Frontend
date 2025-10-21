<?php
require_once '../../includes/bootstrap.php';
require_once '../../includes/conexao.php';

header('Content-Type: application/json');

$idTurma = isset($_GET['turma_id']) ? (int)$_GET['turma_id'] : 0;
if ($idTurma <= 0) {
    echo json_encode(['success' => false, 'message' => 'turma_id inválido']);
    exit;
}

try {
    // Disciplinas (com professor, quando atribuído via Disciplinas.ID_Professor)
    $sql = "SELECT DISTINCT d.ID_Disciplina, d.Nome_Disciplina,
                   u.Nome_Completo AS Professor,
                   d.Carga_Horaria, d.Etapa
            FROM Disciplinas d
            LEFT JOIN Professores p ON p.ID_Professor = d.ID_Professor
            LEFT JOIN Usuarios u ON u.ID_Usuario = p.ID_Professor
            WHERE d.Ano_Letivo = (SELECT Ano_Letivo FROM Turmas WHERE ID_Turma = ?)
            ORDER BY d.Nome_Disciplina";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$idTurma]);
    $disciplinas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'data' => $disciplinas]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
