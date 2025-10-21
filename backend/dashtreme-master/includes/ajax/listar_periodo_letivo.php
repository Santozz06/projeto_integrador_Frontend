<?php
require_once '../../includes/bootstrap.php';
require_once '../../includes/conexao.php';

header('Content-Type: application/json');

$ano = isset($_GET['ano']) ? (int)$_GET['ano'] : 0;
if ($ano <= 0) {
    echo json_encode(['success' => false, 'message' => 'Parâmetro ano inválido']);
    exit;
}

try {
    $sql = "SELECT MIN(Data_Inicio) AS Data_Inicio, MAX(COALESCE(Data_Fim, Data_Inicio)) AS Data_Fim
            FROM Calendario_Academico WHERE Ano_Letivo = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$ano]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row && ($row['Data_Inicio'] || $row['Data_Fim'])) {
        echo json_encode(['success' => true, 'data' => [
            'Data_Inicio' => $row['Data_Inicio'],
            'Data_Fim' => $row['Data_Fim']
        ]]);
    } else {
        echo json_encode(['success' => true, 'data' => null]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
