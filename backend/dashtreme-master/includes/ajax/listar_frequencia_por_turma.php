<?php
require_once '../../includes/bootstrap.php';
require_once '../../includes/conexao.php';

header('Content-Type: application/json');

$idTurma = isset($_GET['turma_id']) ? (int)$_GET['turma_id'] : 0;
$dataInicio = isset($_GET['data_inicio']) && $_GET['data_inicio'] !== '' ? $_GET['data_inicio'] : null;
$dataFim = isset($_GET['data_fim']) && $_GET['data_fim'] !== '' ? $_GET['data_fim'] : null;

if ($idTurma <= 0) {
    echo json_encode(['success' => false, 'message' => 'turma_id inválido']);
    exit;
}

try {
    // frequencia da turma
    $params = [$idTurma];
    $dateWhere = '';
    if ($dataInicio) { $dateWhere .= ' AND f.Data >= ?'; $params[] = $dataInicio; }
    if ($dataFim) { $dateWhere .= ' AND f.Data <= ?'; $params[] = $dataFim; }

    $sql = "SELECT 
                u.ID_Usuario AS ID_Aluno,
                u.Nome_Completo,
                a.Matricula,
                SUM(CASE WHEN f.Presenca = 1 THEN 1 ELSE 0 END) AS Presentes,
                SUM(CASE WHEN f.Presenca = 0 THEN 1 ELSE 0 END) AS Faltas,
                COUNT(f.ID_Frequencia) AS Total_Registros
            FROM Matriculas m
            INNER JOIN Alunos a ON a.ID_Aluno = m.ID_Aluno
            INNER JOIN Usuarios u ON u.ID_Usuario = a.ID_Aluno
            LEFT JOIN Frequencias f ON f.ID_Matricula = m.ID_Matricula $dateWhere
            WHERE m.ID_Turma = ? AND m.Status = 'Ativa'
            GROUP BY u.ID_Usuario, u.Nome_Completo, a.Matricula
            ORDER BY u.Nome_Completo";

    // ajusta ordem dos params
    $paramsFinal = [];
    if ($dataInicio) { $paramsFinal[] = $dataInicio; }
    if ($dataFim) { $paramsFinal[] = $dataFim; }
    $paramsFinal[] = $idTurma;

    $stmt = $pdo->prepare($sql);
    $stmt->execute($paramsFinal);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // calcula percentual
    foreach ($rows as &$r) {
        $total = (int)$r['Total_Registros'];
        $presentes = (int)$r['Presentes'];
        $faltas = (int)$r['Faltas'];
    $r['Justificadas'] = 0; // sem coluna
        $r['Percentual'] = $total > 0 ? round(($presentes / $total) * 100, 1) : 0.0;
    }

    echo json_encode(['success' => true, 'data' => $rows]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
