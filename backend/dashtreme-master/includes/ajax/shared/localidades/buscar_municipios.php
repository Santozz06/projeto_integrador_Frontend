<?php
header('Content-Type: application/json');
require_once '../../config/conexao.php';
require_once '../crud/LocalidadeCRUD.php';

try {
    $search = $_GET['search'] ?? '';
    
    $pdo = conectarBanco();
    $localidadeCRUD = new LocalidadeCRUD($pdo);
    
    $sql = "SELECT codigo_ibge as id, nome FROM municipios WHERE nome LIKE ? ORDER BY nome LIMIT 50";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(["%$search%"]);
    $municipios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $results = [];
    foreach ($municipios as $municipio) {
        $results[] = [
            'id' => $municipio['id'],
            'text' => $municipio['nome']
        ];
    }
    
    echo json_encode($results);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>