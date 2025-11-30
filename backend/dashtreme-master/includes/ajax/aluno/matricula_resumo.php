<?php
require_once '../../bootstrap.php';

header('Content-Type: application/json');

try {
    if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'aluno' || !isset($_SESSION['usuario_id'])) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Acesso negado']);
        exit;
    }

    $alunoId = (int) $_SESSION['usuario_id'];

    $st = $pdo->prepare("SELECT u.Nome_Completo, a.Matricula, m.Ano_Letivo, t.Nome_Turma, t.Turno
        FROM Alunos a
        INNER JOIN Usuarios u ON u.ID_Usuario = a.ID_Aluno
        INNER JOIN Matriculas m ON m.ID_Aluno = a.ID_Aluno
        LEFT JOIN Turmas t ON t.ID_Turma = m.ID_Turma
        WHERE a.ID_Aluno = ?
        ORDER BY (m.Status = 'Ativa') DESC, m.Ano_Letivo DESC, m.ID_Matricula DESC
        LIMIT 1");
    $st->execute([$alunoId]);
    $row = $st->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        echo json_encode([
            'success' => true,
            'data' => [
                'nome' => $row['Nome_Completo'],
                'matricula' => $row['Matricula'],
                'ano' => $row['Ano_Letivo'],
                'turma' => $row['Nome_Turma'],
                'turno' => $row['Turno']
            ]
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Dados não encontrados']);
    }
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
