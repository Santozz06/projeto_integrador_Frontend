<?php
require_once '../../bootstrap.php';
require_once '../../conexao.php';

header('Content-Type: application/json');

try {
    // Params from FullCalendar
        $start = isset($_GET['start']) ? $_GET['start'] : null; // FullCalendar ISO
        $end = isset($_GET['end']) ? $_GET['end'] : null;
    $tipo = isset($_GET['tipo']) && $_GET['tipo'] !== 'all' ? trim($_GET['tipo']) : null;
    $ano = isset($_GET['ano']) && $_GET['ano'] !== '' ? (int)$_GET['ano'] : null;
    $publico = isset($_GET['publico']) && $_GET['publico'] !== 'all' ? trim($_GET['publico']) : null; // 'todos','professores','alunos'

    // Light migration: ensure Publico_Alvo column exists
    try {
        $chk = $pdo->query("SHOW COLUMNS FROM Calendario_Academico LIKE 'Publico_Alvo'");
        if ($chk->rowCount() === 0) {
            $pdo->exec("ALTER TABLE Calendario_Academico ADD COLUMN Publico_Alvo VARCHAR(20) DEFAULT 'todos' AFTER Tipo_Evento");
        }
    } catch (Throwable $e) { }

    $params = [];
    $sql = "SELECT ID_Evento, Nome_Evento, Descricao, Data_Inicio, Data_Fim, Tipo_Evento, Ano_Letivo, 
                   COALESCE(Publico_Alvo, 'todos') AS Publico_Alvo
            FROM Calendario_Academico WHERE 1=1";

    // Date window: include events that OVERLAP the [start, end] range
    // If both provided: (single-day between) OR (multi-day overlaps)
    if ($start && $end) {
        $s = normalizarIsoParaMysql($start);
        $e = normalizarIsoParaMysql($end);
        $sql .= " AND ((Data_Fim IS NULL AND Data_Inicio BETWEEN ? AND ?) OR (Data_Fim IS NOT NULL AND Data_Inicio <= ? AND Data_Fim >= ?))";
        $params[] = $s; $params[] = $e; $params[] = $e; $params[] = $s;
    } elseif ($start) {
        $s = normalizarIsoParaMysql($start);
        $sql .= " AND ((Data_Fim IS NULL AND Data_Inicio >= ?) OR (Data_Fim IS NOT NULL AND Data_Fim >= ?))";
        $params[] = $s; $params[] = $s;
    } elseif ($end) {
        $e = normalizarIsoParaMysql($end);
        $sql .= " AND ((Data_Fim IS NULL AND Data_Inicio <= ?) OR (Data_Fim IS NOT NULL AND Data_Inicio <= ?))";
        $params[] = $e; $params[] = $e;
    }
    if ($tipo)  { $sql .= " AND Tipo_Evento = ?"; $params[] = $tipo; }
    if ($ano)   { $sql .= " AND (Ano_Letivo = ? OR Ano_Letivo IS NULL)"; $params[] = $ano; }

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
            // Professores visualizam tudo por padrão no dashboard: 'todos', 'professores' e 'alunos'
            $sql .= " AND (Publico_Alvo IN ('todos','professores','alunos') OR Publico_Alvo IS NULL)";
        } else {
            $sql .= " AND (Publico_Alvo IN ('todos','alunos') OR Publico_Alvo IS NULL)";
        }
    }

    $sql .= " ORDER BY Data_Inicio ASC, ID_Evento ASC";

    $st = $pdo->prepare($sql);
    $st->execute($params);
    $rows = $st->fetchAll(PDO::FETCH_ASSOC);
    
    // DEBUG: Log query and results
    error_log("[DEBUG listar_eventos] SQL: " . $sql);
    error_log("[DEBUG listar_eventos] Params: " . json_encode($params));
    error_log("[DEBUG listar_eventos] Rows found: " . count($rows));
    error_log("[DEBUG listar_eventos] User type: " . ($_SESSION['user_type'] ?? 'NOT SET'));

    // Map to FullCalendar shape
        $events = array_map(function($r) {
            $startDt = date('c', strtotime($r['Data_Inicio']));
            $endDt = $r['Data_Fim'] ? date('c', strtotime($r['Data_Fim'])) : null;
            return [
                'id' => (int)$r['ID_Evento'],
                'title' => $r['Nome_Evento'],
                'start' => $startDt,
                'end' => $endDt,
                'allDay' => false,
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

function normalizarIsoParaMysql($iso) {
    // Aceita formatos 2025-11-18 ou 2025-11-18T00:00:00Z ou 2025-11-18T12:30:00
    $iso = trim($iso);
    // Remove timezone Z ou offset
    $iso = preg_replace('/Z$/', '', $iso); // remove Z final
    $iso = preg_replace('/[+-]\d{2}:?\d{2}$/', '', $iso); // remove offset +hh:mm
    if (strpos($iso, 'T') !== false) {
        $iso = str_replace('T', ' ', $iso);
    }
    // Se vier só data, adiciona 00:00:00
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $iso)) {
        $iso .= ' 00:00:00';
    }
    // Se vier sem segundos, adiciona :00
    if (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}$/', $iso)) {
        $iso .= ':00';
    }
    return $iso;
}
