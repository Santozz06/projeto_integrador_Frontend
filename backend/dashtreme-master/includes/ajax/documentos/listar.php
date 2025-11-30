<?php
require_once '../../config/conexao.php';

header('Content-Type: application/json');

try {
    $tipo = isset($_GET['tipo']) && $_GET['tipo'] !== '' ? trim($_GET['tipo']) : null;

    $sql = "SELECT ID_Documento, Tipo, Titulo, Descricao, Data_Vigencia, Arquivo_Nome, Mime_Type, Tamanho_Bytes, Ativo, Criado_Em, Atualizado_Em
            FROM Documentos WHERE Ativo = 1";
    $params = [];
    if ($tipo) { $sql .= " AND Tipo = ?"; $params[] = $tipo; }
    $sql .= " ORDER BY COALESCE(Data_Vigencia, Criado_Em) DESC, ID_Documento DESC";

    $st = $pdo->prepare($sql);
    $st->execute($params);
    $rows = $st->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'data' => $rows]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
