<?php
require_once '../conexao.php';
require_once '../crud/LocalidadeCRUD.php';

header('Content-Type: application/json');

try {
    if (isset($_GET['estado_id']) && !empty($_GET['estado_id'])) {
        $localidadeCRUD = new LocalidadeCRUD($pdo);
        $municipios = $localidadeCRUD->listarMunicipiosPorEstado($_GET['estado_id']);
        echo json_encode($municipios);
    } else {
        echo json_encode([]);
    }
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>