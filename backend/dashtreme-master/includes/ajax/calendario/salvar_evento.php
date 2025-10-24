<?php
require_once '../../bootstrap.php';
require_once '../../conexao.php';
// Integração com Google desativada: mantendo apenas calendário local/ICS

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Método não permitido']);
        exit;
    }

    // Permitir apenas admin por enquanto
    if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Acesso negado']);
        exit;
    }

    $raw = file_get_contents('php://input');
    $json = json_decode($raw, true);
    if (!is_array($json)) { $json = $_POST; }

    $id = isset($json['id']) ? (int)$json['id'] : 0;
    $titulo = isset($json['title']) ? trim($json['title']) : '';
    $tipo = isset($json['tipo']) ? trim($json['tipo']) : 'evento';
    $descricao = isset($json['descricao']) ? trim($json['descricao']) : null;
    $inicio = isset($json['inicio']) ? substr($json['inicio'], 0, 10) : null; // YYYY-MM-DD
    $fim = isset($json['fim']) && $json['fim'] !== '' ? substr($json['fim'], 0, 10) : null;
    $publico = isset($json['publico']) ? trim($json['publico']) : 'todos'; // 'todos','professores','alunos'
    $anoLetivo = isset($json['ano_letivo']) && $json['ano_letivo'] !== '' ? (int)$json['ano_letivo'] : null;

    if ($titulo === '' || !$inicio) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Campos obrigatórios ausentes (título/início)']);
        exit;
    }

    // Light migration: garantir apenas coluna Publico_Alvo (usada pelos feeds ICS)
    try {
        $chk = $pdo->query("SHOW COLUMNS FROM Calendario_Academico LIKE 'Publico_Alvo'");
        if ($chk->rowCount() === 0) {
            $pdo->exec("ALTER TABLE Calendario_Academico ADD COLUMN Publico_Alvo VARCHAR(20) DEFAULT 'todos' AFTER Tipo_Evento");
        }
    } catch (Throwable $e) { /* ignore */ }

    if ($id > 0) {
        $st = $pdo->prepare("UPDATE Calendario_Academico
                              SET Nome_Evento = ?, Descricao = ?, Data_Inicio = ?, Data_Fim = ?,
                                  Tipo_Evento = ?, Ano_Letivo = ?, Publico_Alvo = ?
                              WHERE ID_Evento = ?");
        $st->execute([$titulo, $descricao, $inicio, $fim, $tipo, $anoLetivo, $publico, $id]);
    } else {
        $st = $pdo->prepare("INSERT INTO Calendario_Academico
            (Nome_Evento, Descricao, Data_Inicio, Data_Fim, Tipo_Evento, Ano_Letivo, Publico_Alvo)
            VALUES (?, ?, ?, ?, ?, ?, ?)");
        $st->execute([$titulo, $descricao, $inicio, $fim, $tipo, $anoLetivo, $publico]);
        $id = (int)$pdo->lastInsertId();
    }

    // Sem push para Google: ICS continuará refletindo os eventos locais

    echo json_encode(['success' => true, 'id' => $id]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
