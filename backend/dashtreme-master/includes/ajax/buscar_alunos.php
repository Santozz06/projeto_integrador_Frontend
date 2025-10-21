<?php
require_once '../../includes/bootstrap.php';
require_once '../../includes/conexao.php';

header('Content-Type: application/json');

$q = isset($_GET['q']) ? trim($_GET['q']) : '';
if ($q === '' || mb_strlen($q) < 2) {
    echo json_encode(['success' => false, 'message' => 'Parâmetro q inválido']);
    exit;
}

try {
    // Busca alunos por nome ou matrícula e sua matrícula ativa mais recente
    $sql = "SELECT 
                u.ID_Usuario AS ID_Aluno,
                u.Nome_Completo,
                u.Email,
                u.Telefone,
                u.Endereco,
                a.Matricula,
                m.ID_Matricula,
                t.ID_Turma,
                t.Nome_Turma,
                t.Turno,
                t.Ano_Letivo,
                t.Etapa
            FROM Usuarios u
            INNER JOIN Alunos a ON a.ID_Aluno = u.ID_Usuario
            LEFT JOIN Matriculas m ON m.ID_Matricula = (
                SELECT m2.ID_Matricula FROM Matriculas m2
                WHERE m2.ID_Aluno = a.ID_Aluno AND m2.Status = 'Ativa'
                ORDER BY m2.Ano_Letivo DESC, m2.Data_Matricula DESC
                LIMIT 1
            )
            LEFT JOIN Turmas t ON t.ID_Turma = m.ID_Turma
            WHERE u.Nome_Completo LIKE :like OR a.Matricula LIKE :likeExact
            ORDER BY u.Nome_Completo
            LIMIT 30";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':like', '%' . $q . '%');
    $stmt->bindValue(':likeExact', '%' . $q . '%');
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'data' => $rows]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
