<?php
require_once '../../config/conexao.php';
header('Content-Type: application/json');

try {

    $professorId = isset($_GET['professor_id']) && $_GET['professor_id'] !== '' ? intval($_GET['professor_id']) : 0;
    $ano = isset($_GET['ano']) && $_GET['ano'] !== '' ? intval($_GET['ano']) : null;

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
            FROM Disciplinas d
            WHERE 1=1";
    $params = [];
    if ($ano !== null) { $sql .= " AND (d.Ano_Letivo = ? OR d.Ano_Letivo IS NULL)"; $params[] = $ano; }

    if ($professorId > 0) {
        $sql .= " AND d.ID_Disciplina NOT IN (
            SELECT pd.ID_Disciplina FROM Professores_Disciplinas pd WHERE pd.ID_Professor = ?";
        $params[] = $professorId;
        if ($ano !== null) { $sql .= " AND (pd.Ano_Letivo = ? OR pd.Ano_Letivo IS NULL)"; $params[] = $ano; }
        $sql .= ")";
    }

    $sql .= " ORDER BY d.Nome_Disciplina";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'data' => $rows]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
