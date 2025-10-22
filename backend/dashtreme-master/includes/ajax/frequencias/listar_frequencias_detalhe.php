<?php
header('Content-Type: application/json');

try {
    require_once dirname(__DIR__, 2) . '/bootstrap.php';

    $matriculaId = isset($_GET['matricula_id']) ? (int)$_GET['matricula_id'] : 0;
    $dataIni = isset($_GET['data_ini']) && $_GET['data_ini'] !== '' ? $_GET['data_ini'] : null; // 'YYYY-MM-DD'
    $dataFim = isset($_GET['data_fim']) && $_GET['data_fim'] !== '' ? $_GET['data_fim'] : null;

    if ($matriculaId <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'matricula_id é obrigatório']);
        exit;
    }

    // Default: mês corrente
    if (!$dataIni || !$dataFim) {
        $firstDay = (new DateTime('first day of this month'))->format('Y-m-d');
        $lastDay = (new DateTime('last day of this month'))->format('Y-m-d');
        $dataIni = $dataIni ?: $firstDay;
        $dataFim = $dataFim ?: $lastDay;
    }

    $sql = "SELECT Data, Presenca FROM Frequencias WHERE ID_Matricula = ? AND Data BETWEEN ? AND ? ORDER BY Data";
    $st = $pdo->prepare($sql);
    $st->execute([$matriculaId, $dataIni, $dataFim]);
    $rows = $st->fetchAll(PDO::FETCH_ASSOC);

    // Normaliza tipos
    $data = array_map(function($r){
        return [
            'Data' => $r['Data'],
            'Presenca' => (int)$r['Presenca'] ? 1 : 0,
        ];
    }, $rows);

    echo json_encode(['success' => true, 'data' => $data, 'periodo' => [$dataIni, $dataFim]]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
