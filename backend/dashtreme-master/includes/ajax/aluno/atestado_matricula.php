<?php
require_once '../../bootstrap.php';

header('Content-Type: application/json');

try {
    if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'aluno' || !isset($_SESSION['usuario_id'])) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Acesso negado']);
        exit;
    }

    $alunoId = (int)$_SESSION['usuario_id'];

    $hasCol = function($table, $column) use ($pdo) {
        $sql = "SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ? LIMIT 1";
        $st = $pdo->prepare($sql);
        $st->execute([$table, $column]);
        return (bool)$st->fetchColumn();
    };

    $cpfParts = [];
    if ($hasCol('Usuarios', 'CPF')) $cpfParts[] = 'u.CPF';
    if ($hasCol('Alunos', 'CPF')) $cpfParts[] = 'a.CPF';
    
    $cpfSelect = !empty($cpfParts) ? 'COALESCE(' . implode(', ', $cpfParts) . ') AS CPF' : 'NULL AS CPF';

    $st = $pdo->prepare("SELECT u.Nome_Completo, a.Matricula, $cpfSelect
                         FROM Alunos a INNER JOIN Usuarios u ON u.ID_Usuario = a.ID_Aluno
                         WHERE a.ID_Aluno = ?");
    $st->execute([$alunoId]);
    $aluno = $st->fetch(PDO::FETCH_ASSOC) ?: [];

    $turmasFields = ['Nome_Turma', 'Etapa', 'Turno', 'Modalidade', 'Nivel'];
    $turmasSelect = [];
    foreach ($turmasFields as $field) {
        if ($hasCol('Turmas', $field)) {
            $turmasSelect[] = "t.$field";
        } else {
            $turmasSelect[] = "NULL AS $field";
        }
    }

    $st = $pdo->prepare("SELECT m.ID_Matricula, m.Ano_Letivo, m.Status, m.ID_Turma,
                                " . implode(', ', $turmasSelect) . "
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

