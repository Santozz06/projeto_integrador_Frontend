<?php
require_once '../../config/conexao.php';
header('Content-Type: application/json');

try {

    $turmaId = isset($_GET['turma_id']) ? (int)$_GET['turma_id'] : 0;
    $dataIni = isset($_GET['data_ini']) && $_GET['data_ini'] !== '' ? $_GET['data_ini'] : null; // 'YYYY-MM-DD'
    $dataFim = isset($_GET['data_fim']) && $_GET['data_fim'] !== '' ? $_GET['data_fim'] : null;

    if ($turmaId <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'turma_id é obrigatório']);
        exit;
    }

    // Default: mês corrente
    if (!$dataIni || !$dataFim) {
        $firstDay = (new DateTime('first day of this month'))->format('Y-m-d');
        $lastDay = (new DateTime('last day of this month'))->format('Y-m-d');
        $dataIni = $dataIni ?: $firstDay;
        $dataFim = $dataFim ?: $lastDay;
    }

    // Matriculas ativas
    $stA = $pdo->prepare("SELECT ID_Matricula FROM Matriculas WHERE ID_Turma = ? AND Status = 'Ativa'");
    $stA->execute([$turmaId]);
    $idsMat = $stA->fetchAll(PDO::FETCH_COLUMN, 0);

    $resumo = [ '1' => ['total'=>0, 'presentes'=>0], '2' => ['total'=>0, 'presentes'=>0], '3' => ['total'=>0, 'presentes'=>0] ];

    if ($idsMat) {
        $in = implode(',', array_fill(0, count($idsMat), '?'));
        $sql = "SELECT ID_Matricula, Data, Presenca FROM Frequencias WHERE ID_Matricula IN ($in) AND Data BETWEEN ? AND ?";
        $params = array_merge($idsMat, [$dataIni, $dataFim]);
        $st = $pdo->prepare($sql);
        $st->execute($params);
        while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
            $data = $row['Data'];
            $mes = (int)date('n', strtotime($data));
            $tri = ($mes <= 3) ? '1' : (($mes <= 6) ? '2' : '3');
            $resumo[$tri]['total'] += 1;
            $resumo[$tri]['presentes'] += ((int)$row['Presenca'] ? 1 : 0);
        }
    }

    echo json_encode(['success' => true, 'data' => $resumo, 'periodo' => [$dataIni, $dataFim]]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
