<?php
require_once '../../../config/conexao.php';

header('Content-Type: application/json');

$idTurma = isset($_GET['turma_id']) ? (int)$_GET['turma_id'] : 0;
if ($idTurma <= 0) {
    echo json_encode(['success' => false, 'message' => 'turma_id inválido']);
    exit;
}

try {
    // Alunos ativos na turma, com dados básicos
    $sql = "SELECT 
                u.ID_Usuario AS ID_Aluno,
                u.Nome_Completo,
                u.Email,
                u.Telefone,
                a.Matricula,
                m.ID_Matricula,
                m.Status,
                    m.Data_Matricula
            FROM Matriculas m
            INNER JOIN Alunos a ON m.ID_Aluno = a.ID_Aluno
            INNER JOIN Usuarios u ON a.ID_Aluno = u.ID_Usuario
            WHERE m.ID_Turma = ? AND m.Status = 'Ativa'
            ORDER BY u.Nome_Completo";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$idTurma]);
    $alunos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'data' => $alunos]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
