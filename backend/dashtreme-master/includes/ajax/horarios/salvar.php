<?php
require_once '../../bootstrap.php';
require_once '../../conexao.php';

header('Content-Type: application/json');

try {
    if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Acesso negado']);
        exit;
    }

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

    $id = isset($_POST['id']) && $_POST['id'] !== '' ? (int)$_POST['id'] : null;
    $turma = (int)($_POST['turma_id'] ?? 0);
    $disc = (int)($_POST['disciplina_id'] ?? 0);
    $prof = (int)($_POST['professor_id'] ?? 0);
    $dia  = (int)($_POST['dia_semana'] ?? 0);
    $hin  = trim($_POST['hora_inicio'] ?? '');
    $hfi  = trim($_POST['hora_fim'] ?? '');
    $sala = isset($_POST['sala']) ? trim($_POST['sala']) : null;
    $ano  = isset($_POST['ano']) && $_POST['ano'] !== '' ? (int)$_POST['ano'] : null;
    $obs  = isset($_POST['observacao']) ? trim($_POST['observacao']) : null;

    if (!$turma || !$disc || !$prof || $dia < 1 || $dia > 7 || $hin === '' || $hfi === '') {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Campos obrigatórios faltando']);
        exit;
    }

    // Se ano não foi informado, herda o ano letivo da Turma para evitar inconsistência com filtro da home do professor
    if ($ano === null && $turma) {
        try {
            $stmtAno = $pdo->prepare("SELECT Ano_Letivo FROM Turmas WHERE ID_Turma = ?");
            $stmtAno->execute([$turma]);
            $rowAno = $stmtAno->fetch(PDO::FETCH_ASSOC);
            if ($rowAno && $rowAno['Ano_Letivo'] !== null) {
                $ano = (int)$rowAno['Ano_Letivo'];
            }
        } catch (Throwable $e) { /* segue sem ano explícito */ }
    }

    if ($id) {
        $sql = "UPDATE Horarios SET ID_Turma=?, ID_Disciplina=?, ID_Professor=?, Dia_Semana=?, Hora_Inicio=?, Hora_Fim=?, Sala=?, Ano_Letivo=?, Observacao=? WHERE ID_Horario=?";
        $st = $pdo->prepare($sql);
        $ok = $st->execute([$turma, $disc, $prof, $dia, $hin, $hfi, $sala, $ano, $obs, $id]);
    } else {
        $sql = "INSERT INTO Horarios (ID_Turma, ID_Disciplina, ID_Professor, Dia_Semana, Hora_Inicio, Hora_Fim, Sala, Ano_Letivo, Observacao)
                VALUES (?,?,?,?,?,?,?,?,?)";
        $st = $pdo->prepare($sql);
        $ok = $st->execute([$turma, $disc, $prof, $dia, $hin, $hfi, $sala, $ano, $obs]);
        $id = $ok ? (int)$pdo->lastInsertId() : null;
    }

    if (!$ok) { throw new Exception('Falha ao salvar'); }

    echo json_encode(['success' => true, 'id' => $id]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
