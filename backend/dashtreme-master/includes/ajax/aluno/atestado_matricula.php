<?php
require_once '../../bootstrap.php';
require_once '../../conexao.php';

header('Content-Type: application/json');

try {
    if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'aluno' || !isset($_SESSION['usuario_id'])) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Acesso negado']);
        exit;
    }

    $alunoId = (int)$_SESSION['usuario_id'];

    // Dados básicos do aluno
    $st = $pdo->prepare("SELECT u.Nome_Completo, a.Matricula, COALESCE(u.CPF, a.CPF) AS CPF
                         FROM Alunos a INNER JOIN Usuarios u ON u.ID_Usuario = a.ID_Aluno
                         WHERE a.ID_Aluno = ?");
    $st->execute([$alunoId]);
    $aluno = $st->fetch(PDO::FETCH_ASSOC) ?: [];

    // Matrícula ativa mais recente (ou última, se não ativa)
    $st = $pdo->prepare("SELECT m.ID_Matricula, m.Ano_Letivo, m.Status, m.ID_Turma,
                                t.Nome_Turma, t.Etapa, t.Turno, t.Modalidade, t.Nivel
                         FROM Matriculas m
                         LEFT JOIN Turmas t ON t.ID_Turma = m.ID_Turma
                         WHERE m.ID_Aluno = ?
                         ORDER BY (m.Status = 'Ativa') DESC, m.Ano_Letivo DESC, m.ID_Matricula DESC
                         LIMIT 1");
    $st->execute([$alunoId]);
    $mat = $st->fetch(PDO::FETCH_ASSOC) ?: [];

    $out = [
        'nome' => $aluno['Nome_Completo'] ?? null,
        'matricula' => $aluno['Matricula'] ?? null,
        'cpf' => $aluno['CPF'] ?? null,
        'ano' => isset($mat['Ano_Letivo']) ? (int)$mat['Ano_Letivo'] : null,
        'turma' => $mat['Nome_Turma'] ?? null,
        'serie' => $mat['Etapa'] ?? ($mat['Nome_Turma'] ?? null),
        'turno' => $mat['Turno'] ?? null,
        'modalidade' => $mat['Modalidade'] ?? null,
        'nivel' => $mat['Nivel'] ?? null,
        'status' => $mat['Status'] ?? null
    ];

    echo json_encode(['success' => true, 'data' => $out]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
