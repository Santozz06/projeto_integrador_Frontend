<?php
require_once '../../../config/conexao.php';

header('Content-Type: application/json');

$alunoId = isset($_GET['aluno_id']) ? (int)$_GET['aluno_id'] : 0;
if ($alunoId <= 0) {
    echo json_encode(['success' => false, 'message' => 'aluno_id inválido']);
    exit;
}

try {
    $sql = "SELECT m.ID_Matricula, m.Ano_Letivo, t.Nome_Turma, t.Turno, t.Etapa
            FROM Matriculas m
            INNER JOIN Turmas t ON t.ID_Turma = m.ID_Turma
            WHERE m.ID_Aluno = ? AND m.Status = 'Ativa'
            ORDER BY m.Ano_Letivo DESC, m.Data_Matricula DESC
            LIMIT 1";
    $st = $pdo->prepare($sql);
    $st->execute([$alunoId]);
    $row = $st->fetch(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'data' => $row]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
