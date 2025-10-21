<?php
header('Content-Type: application/json');

try {
    require_once dirname(__DIR__, 2) . '/bootstrap.php';

    $professorId = isset($_GET['professor_id']) && $_GET['professor_id'] !== '' ? intval($_GET['professor_id']) : 0;
    $ano = isset($_GET['ano']) && $_GET['ano'] !== '' ? intval($_GET['ano']) : null;
    if ($professorId <= 0) { echo json_encode(['success' => true, 'data' => []]); exit; }

    // Garante a existência da tabela de relação
    $pdo->exec("CREATE TABLE IF NOT EXISTS Professores_Disciplinas (
        ID_ProfDisc INT AUTO_INCREMENT PRIMARY KEY,
        ID_Professor INT NOT NULL,
        ID_Disciplina INT NOT NULL,
        Ano_Letivo INT NULL,
        ID_Turma INT NULL,
        UNIQUE KEY uq_prof_disc_ano_turma (ID_Professor, ID_Disciplina, Ano_Letivo, ID_Turma)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $sql = "SELECT d.ID_Disciplina, d.Nome_Disciplina, d.Carga_Horaria, d.Etapa, d.Ano_Letivo
            FROM Professores_Disciplinas pd
            INNER JOIN Disciplinas d ON d.ID_Disciplina = pd.ID_Disciplina
            WHERE pd.ID_Professor = ?";
    $params = [$professorId];
    if ($ano !== null) { $sql .= " AND (pd.Ano_Letivo = ? OR d.Ano_Letivo = ? )"; $params[] = $ano; $params[] = $ano; }
    $sql .= " ORDER BY d.Nome_Disciplina";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'data' => $rows]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
