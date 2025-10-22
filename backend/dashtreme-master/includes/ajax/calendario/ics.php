<?php
// Public ICS feed for calendar events
require_once '../../includes/conexao.php';

header('Content-Type: text/calendar; charset=utf-8');
header('Content-Disposition: inline; filename="calendario_escolar.ics"');

function ics_escape($text){
    $text = str_replace(["\\", ",", ";", "\r\n", "\n", "\r"], ["\\\\", "\\,", "\\;", "\\n", "\\n", "\\n"], (string)$text);
    return $text;
}

$publico = isset($_GET['publico']) ? strtolower(trim($_GET['publico'])) : 'todos';
$tipo = isset($_GET['tipo']) && $_GET['tipo'] !== '' ? strtolower(trim($_GET['tipo'])) : null;

$params = [];
$sql = "SELECT ID_Evento, Nome_Evento, Descricao, Data_Inicio, Data_Fim, Tipo_Evento, COALESCE(Publico_Alvo,'todos') AS Publico_Alvo
        FROM Calendario_Academico WHERE 1=1";

if (in_array($publico, ['todos','professores','alunos'], true)) {
    if ($publico === 'todos') {
        $sql .= " AND (Publico_Alvo = 'todos' OR Publico_Alvo IS NULL)";
    } else {
        $sql .= " AND Publico_Alvo = ?"; $params[] = $publico;
    }
}
if ($tipo) { $sql .= " AND LOWER(Tipo_Evento) = ?"; $params[] = $tipo; }

$sql .= " ORDER BY Data_Inicio ASC, ID_Evento ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Output ICS
$lines = [];
$lines[] = 'BEGIN:VCALENDAR';
$lines[] = 'VERSION:2.0';
$lines[] = 'PRODID:-//Escola//Calendario Escolar//PT-BR';
$lines[] = 'CALSCALE:GREGORIAN';
$lines[] = 'METHOD:PUBLISH';

foreach ($rows as $r) {
    $id = (int)$r['ID_Evento'];
    $title = ics_escape($r['Nome_Evento']);
    $desc = isset($r['Descricao']) ? ics_escape($r['Descricao']) : '';
    $tipoEv = isset($r['Tipo_Evento']) ? strtolower($r['Tipo_Evento']) : '';
    $pub = isset($r['Publico_Alvo']) ? strtolower($r['Publico_Alvo']) : 'todos';

    $start = $r['Data_Inicio'];
    $end = $r['Data_Fim'];
    if (!$start) { continue; }

    // All-day events: DTSTART/DTEND as VALUE=DATE, DTEND is exclusive
    $dtStart = date('Ymd', strtotime($start));
    $dtEnd = date('Ymd', strtotime(($end ?: $start) . ' +1 day'));

    $lines[] = 'BEGIN:VEVENT';
    $lines[] = 'UID:evento-' . $id . '@escola';
    $lines[] = 'SUMMARY:' . $title;
    if ($desc !== '') { $lines[] = 'DESCRIPTION:' . $desc; }
    $lines[] = 'DTSTART;VALUE=DATE:' . $dtStart;
    $lines[] = 'DTEND;VALUE=DATE:' . $dtEnd;
    if ($tipoEv) { $lines[] = 'CATEGORIES:' . ics_escape($tipoEv); }
    $lines[] = 'X-PUBLICO:' . ics_escape($pub);
    $lines[] = 'END:VEVENT';
}

$lines[] = 'END:VCALENDAR';

echo implode("\r\n", $lines);
