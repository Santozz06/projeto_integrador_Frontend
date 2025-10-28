<?php
require_once '../../bootstrap.php';
require_once '../../conexao.php';

header('Content-Type: application/json');

try {
    if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'professor') {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Acesso negado']);
        exit;
    }

    $profId = (int)$_SESSION['usuario_id'];

    // Garante tabela
    $pdo->exec("CREATE TABLE IF NOT EXISTS Horarios (
        ID_Horario INT AUTO_INCREMENT PRIMARY KEY,
        ID_Turma INT NOT NULL,
        ID_Disciplina INT NOT NULL,
        ID_Professor INT NOT NULL,
        Dia_Semana TINYINT NOT NULL,
        Hora_Inicio TIME NOT NULL,
        Hora_Fim TIME NOT NULL,
        Sala VARCHAR(20) NULL,
        Ano_Letivo INT NULL,
        Observacao VARCHAR(255) NULL,
        CONSTRAINT fk_horarios_turma FOREIGN KEY (ID_Turma) REFERENCES Turmas(ID_Turma) ON DELETE CASCADE,
        CONSTRAINT fk_horarios_disc FOREIGN KEY (ID_Disciplina) REFERENCES Disciplinas(ID_Disciplina),
        CONSTRAINT fk_horarios_prof FOREIGN KEY (ID_Professor) REFERENCES Professores(ID_Professor)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $ano = isset($_GET['ano']) ? (int)$_GET['ano'] : null;

    // Seja resiliente: considere horários vinculados diretamente ao professor (h.ID_Professor)
    // OU vinculados pela disciplina (d.ID_Professor), pois o admin pode não ter ajustado o campo na criação.
    $sql = "SELECT h.*, t.Nome_Turma, d.Nome_Disciplina
        FROM Horarios h
        INNER JOIN Turmas t ON t.ID_Turma = h.ID_Turma
        INNER JOIN Disciplinas d ON d.ID_Disciplina = h.ID_Disciplina
        WHERE (h.ID_Professor = ? OR d.ID_Professor = ?)";
    $params = [$profId, $profId];
    // Filtro de ano: quando informado (home passa o ano atual), retorna apenas horários do ano solicitado
    if ($ano) {
        $sql .= " AND (h.Ano_Letivo = ? OR t.Ano_Letivo = ?)";
        $params[] = $ano; $params[] = $ano;
    }
    $sql .= " ORDER BY h.Dia_Semana ASC, h.Hora_Inicio ASC";

    $st = $pdo->prepare($sql);
    $st->execute($params);
    $rows = $st->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'data' => $rows]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
