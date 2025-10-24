<?php
require_once '../../bootstrap.php';
require_once '../../conexao.php';

header('Content-Type: application/json');

try {
    // Params from FullCalendar
    $start = isset($_GET['start']) ? $_GET['start'] : null; // ISO date
    $end = isset($_GET['end']) ? $_GET['end'] : null;       // ISO date
    $tipo = isset($_GET['tipo']) && $_GET['tipo'] !== 'all' ? trim($_GET['tipo']) : null;
    $publico = isset($_GET['publico']) && $_GET['publico'] !== 'all' ? trim($_GET['publico']) : null; // 'todos','professores','alunos'

    // Light migration: ensure Publico_Alvo column exists
    try {
        $chk = $pdo->query("SHOW COLUMNS FROM Calendario_Academico LIKE 'Publico_Alvo'");
        if ($chk->rowCount() === 0) {
            $pdo->exec("ALTER TABLE Calendario_Academico ADD COLUMN Publico_Alvo VARCHAR(20) DEFAULT 'todos' AFTER Tipo_Evento");
        }
    } catch (Throwable $e) { /* ignore */ }

    $params = [];
    $sql = "SELECT ID_Evento, Nome_Evento, Descricao, Data_Inicio, Data_Fim, Tipo_Evento, Ano_Letivo, 
                   COALESCE(Publico_Alvo, 'todos') AS Publico_Alvo
            FROM Calendario_Academico WHERE 1=1";

    if ($start) { $sql .= " AND Data_Inicio >= ?"; $params[] = substr($start, 0, 10); }
    if ($end)   { $sql .= " AND (Data_Fim IS NULL OR Data_Fim <= ?)"; $params[] = substr($end, 0, 10); }
    if ($tipo)  { $sql .= " AND Tipo_Evento = ?"; $params[] = $tipo; }

    // Role-based default filter when not admin (future proof)
    $isAdmin = isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin';
    if ($publico) {
        // explicit filter from UI
        if ($publico === 'todos') {
            $sql .= " AND (Publico_Alvo = 'todos' OR Publico_Alvo IS NULL)";
        } else {
            $sql .= " AND Publico_Alvo = ?"; $params[] = $publico;
        }
    } else if (!$isAdmin) {
        // professors or students see only their audience + 'todos'
        $aud = isset($_SESSION['user_type']) ? $_SESSION['user_type'] : 'alunos';
        if ($aud === 'professor' || $aud === 'professores') {
            $sql .= " AND (Publico_Alvo IN ('todos','professores') OR Publico_Alvo IS NULL)";
        } else {
            $sql .= " AND (Publico_Alvo IN ('todos','alunos') OR Publico_Alvo IS NULL)";
        }
    }

    $sql .= " ORDER BY Data_Inicio ASC, ID_Evento ASC";

    $st = $pdo->prepare($sql);
    $st->execute($params);
    $rows = $st->fetchAll(PDO::FETCH_ASSOC);

    // Map to FullCalendar shape
    $events = array_map(function($r) {
        return [
            'id' => (int)$r['ID_Evento'],
            'title' => $r['Nome_Evento'],
            'start' => $r['Data_Inicio'],
            'end' => $r['Data_Fim'],
            'allDay' => true, // using DATE fields
            'extendedProps' => [
                'tipo' => $r['Tipo_Evento'],
                'description' => $r['Descricao'],
                'publico' => $r['Publico_Alvo'] ?: 'todos',
                'ano_letivo' => $r['Ano_Letivo']
            ]
        ];
    }, $rows);

    echo json_encode(['success' => true, 'data' => $events]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
