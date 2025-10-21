<?php
header('Content-Type: application/json');

try {
    require_once dirname(__DIR__, 2) . '/bootstrap.php';

    $idDisc = isset($_POST['id_disciplina']) ? intval($_POST['id_disciplina']) : 0;
    $idProf = isset($_POST['id_professor']) ? intval($_POST['id_professor']) : 0;
    $ano = isset($_POST['ano_letivo']) && $_POST['ano_letivo'] !== '' ? intval($_POST['ano_letivo']) : null;

    if ($idDisc <= 0 || $idProf <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Parâmetros inválidos']);
        exit;
    }

    // Garante a existência da tabela de relação
    $pdo->exec("CREATE TABLE IF NOT EXISTS Professores_Disciplinas (
        ID_ProfDisc INT AUTO_INCREMENT PRIMARY KEY,
        ID_Professor INT NOT NULL,
        ID_Disciplina INT NOT NULL,
        Ano_Letivo INT NULL,
        ID_Turma INT NULL,
        UNIQUE KEY uq_prof_disc_ano_turma (ID_Professor, ID_Disciplina, Ano_Letivo, ID_Turma)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $sql = "DELETE FROM Professores_Disciplinas WHERE ID_Professor = ? AND ID_Disciplina = ? AND ( ( ? IS NULL AND Ano_Letivo IS NULL ) OR Ano_Letivo = ? )";
    $stmt = $pdo->prepare($sql);
    $ok = $stmt->execute([$idProf, $idDisc, $ano, $ano]);

    echo json_encode(['success' => (bool)$ok, 'message' => $ok ? 'Atribuição removida' : 'Nenhuma alteração aplicada']);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
