<?php
require_once '../../bootstrap.php';

header('Content-Type: application/json');

try {
    if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Acesso negado']);
        exit;
    }

    try {
        $stCol = $pdo->query("SELECT EXTRA FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'Horarios' AND COLUMN_NAME = 'ID_Horario'");
        $extra = $stCol ? $stCol->fetchColumn() : '';
        if ($extra === false || stripos((string)$extra, 'auto_increment') === false) {
            $stPk = $pdo->query("SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'Horarios' AND CONSTRAINT_TYPE = 'PRIMARY KEY'");
            $hasPk = $stPk && (int)$stPk->fetchColumn() > 0;
            if (!$hasPk) {
                $pdo->exec("ALTER TABLE Horarios MODIFY ID_Horario INT NOT NULL");
                $pdo->exec("ALTER TABLE Horarios ADD PRIMARY KEY (ID_Horario)");
            }
            $pdo->exec("ALTER TABLE Horarios MODIFY ID_Horario INT NOT NULL AUTO_INCREMENT");
        }
    } catch (Throwable $eMig) {
    }

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

    // Normaliza formato de hora para HH:MM:SS se vier HH:MM
    if (preg_match('/^\d{2}:\d{2}$/', $hin)) { $hin .= ':00'; }
    if (preg_match('/^\d{2}:\d{2}$/', $hfi)) { $hfi .= ':00'; }

    // Validação: término deve ser maior que início
    if (strtotime($hfi) <= strtotime($hin)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Hora de término deve ser maior que a hora de início']);
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
    } catch (Throwable $e) { }
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
    $msg = $e->getMessage();
    if (strpos($msg, 'foreign key') !== false || strpos($msg, 'constraint') !== false) {
        $msg = 'Referências inválidas: verifique Turma, Disciplina e Professor selecionados.';
    }
    echo json_encode(['success' => false, 'message' => $msg]);
}
