<?php
require_once '../../bootstrap.php';
// Integração com Google desativada: mantendo apenas calendário local/ICS

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Método não permitido']);
        exit;
    }

    // Permitir admin e professor (professor só pode criar/editar seus próprios eventos)
    if (!isset($_SESSION['user_type']) || !in_array($_SESSION['user_type'], ['admin','professor'])) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Acesso negado']);
        exit;
    }

    $raw = file_get_contents('php://input');
    $json = json_decode($raw, true);
    if (!is_array($json)) { $json = $_POST; }

    // Garantir tabela base caso ainda não exista (instalação nova)
    $pdo->exec("CREATE TABLE IF NOT EXISTS Calendario_Academico (
        ID_Evento INT AUTO_INCREMENT PRIMARY KEY,
        Nome_Evento VARCHAR(255) NOT NULL,
        Descricao TEXT NULL,
        Data_Inicio DATE NOT NULL,
        Data_Fim DATE NULL,
        Tipo_Evento VARCHAR(64) NOT NULL,
        Ano_Letivo INT NULL,
        Publico_Alvo VARCHAR(20) DEFAULT 'todos',
        Criado_Por INT NULL,
        INDEX idx_inicio (Data_Inicio),
        INDEX idx_tipo (Tipo_Evento),
        INDEX idx_publico (Publico_Alvo)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $id = isset($json['id']) ? (int)$json['id'] : 0;
    $titulo = isset($json['title']) ? trim($json['title']) : '';
    $tipo = isset($json['tipo']) ? trim($json['tipo']) : 'evento';
    $descricao = isset($json['descricao']) ? trim($json['descricao']) : null;
    // Normaliza datetime-local (YYYY-MM-DDTHH:MM) para DATETIME (YYYY-MM-DD HH:MM:SS)
    $inicio = isset($json['inicio']) ? normalizarDateTime($json['inicio']) : null;
    $fim = (isset($json['fim']) && $json['fim'] !== '') ? normalizarDateTime($json['fim']) : null;
    $publico = isset($json['publico']) ? trim($json['publico']) : 'todos'; // 'todos','professores','alunos'
    $anoLetivo = isset($json['ano_letivo']) && $json['ano_letivo'] !== '' ? (int)$json['ano_letivo'] : null;

    if ($titulo === '' || !$inicio) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Campos obrigatórios ausentes (título/início)']);
        exit;
    }

    // Light migration: garantir colunas necessárias
    try {
        $chk = $pdo->query("SHOW COLUMNS FROM Calendario_Academico LIKE 'Publico_Alvo'");
        if ($chk->rowCount() === 0) {
            $pdo->exec("ALTER TABLE Calendario_Academico ADD COLUMN Publico_Alvo VARCHAR(20) DEFAULT 'todos' AFTER Tipo_Evento");
        }
        $chk2 = $pdo->query("SHOW COLUMNS FROM Calendario_Academico LIKE 'Criado_Por'");
        if ($chk2->rowCount() === 0) {
            $pdo->exec("ALTER TABLE Calendario_Academico ADD COLUMN Criado_Por INT NULL AFTER Publico_Alvo");
        }
    } catch (Throwable $e) { }

    $isAdmin = isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin';
    $isProfessor = isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'professor';
    $usuarioId = isset($_SESSION['usuario_id']) ? (int)$_SESSION['usuario_id'] : null;

    if ($isProfessor) {
        // Força público padrão para eventos de professor
        $publico = 'professores';
    }

    if ($id > 0) {
        if ($isProfessor) {
            $chkEvt = $pdo->prepare("SELECT Criado_Por FROM Calendario_Academico WHERE ID_Evento = ?");
            $chkEvt->execute([$id]);
            $own = $chkEvt->fetch(PDO::FETCH_ASSOC);
            if (!$own || (int)$own['Criado_Por'] !== $usuarioId) {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'Você não tem permissão para editar este evento.']);
                exit;
            }
        }
        $st = $pdo->prepare("UPDATE Calendario_Academico
                              SET Nome_Evento = ?, Descricao = ?, Data_Inicio = ?, Data_Fim = ?,
                                  Tipo_Evento = ?, Ano_Letivo = ?, Publico_Alvo = ?
                              WHERE ID_Evento = ?");
        $st->execute([$titulo, $descricao, $inicio, $fim, $tipo, $anoLetivo, $publico, $id]);
    } else {
        if ($isProfessor) {
            $st = $pdo->prepare("INSERT INTO Calendario_Academico
                (Nome_Evento, Descricao, Data_Inicio, Data_Fim, Tipo_Evento, Ano_Letivo, Publico_Alvo, Criado_Por)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $st->execute([$titulo, $descricao, $inicio, $fim, $tipo, $anoLetivo, $publico, $usuarioId]);
        } else {
            $st = $pdo->prepare("INSERT INTO Calendario_Academico
                (Nome_Evento, Descricao, Data_Inicio, Data_Fim, Tipo_Evento, Ano_Letivo, Publico_Alvo)
                VALUES (?, ?, ?, ?, ?, ?, ?)");
            $st->execute([$titulo, $descricao, $inicio, $fim, $tipo, $anoLetivo, $publico]);
        }
        $id = (int)$pdo->lastInsertId();
    }

    // Sem push para Google: ICS continuará refletindo os eventos locais

    echo json_encode(['success' => true, 'id' => $id]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erro ao salvar evento: ' . $e->getMessage(),
        'trace' => (defined('DEBUG') && DEBUG) ? $e->getTraceAsString() : null
    ]);
}

function normalizarDateTime($valor) {
    $valor = trim($valor);
    // Aceita formatos: YYYY-MM-DDTHH:MM ou YYYY-MM-DD HH:MM[:SS]
    if (preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/', $valor)) {
        $valor = str_replace('T', ' ', $valor) . ':00';
    } elseif (preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}$/', $valor)) {
        $valor = str_replace('T', ' ', $valor);
    } elseif (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}$/', $valor)) {
        $valor .= ':00';
    }
    // Validação final
    $dt = DateTime::createFromFormat('Y-m-d H:i:s', $valor);
    if (!$dt) { return null; }
    return $dt->format('Y-m-d H:i:s');
}
