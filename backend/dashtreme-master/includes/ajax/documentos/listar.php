<?php
require_once '../../bootstrap.php';
require_once '../../conexao.php';

header('Content-Type: application/json');

try {
    // Light migration: ensure table exists
    $pdo->exec("CREATE TABLE IF NOT EXISTS Documentos (
        ID_Documento INT AUTO_INCREMENT PRIMARY KEY,
        Tipo VARCHAR(30) NOT NULL,
        Titulo VARCHAR(200) NOT NULL,
        Descricao TEXT NULL,
        Data_Vigencia DATE NULL,
        Arquivo_Nome VARCHAR(255) NOT NULL,
        Arquivo_Caminho VARCHAR(255) NOT NULL,
        Mime_Type VARCHAR(100) NULL,
        Tamanho_Bytes BIGINT NULL,
        Ativo TINYINT(1) DEFAULT 1,
        Criado_Em DATETIME DEFAULT CURRENT_TIMESTAMP,
        Atualizado_Em DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $tipo = isset($_GET['tipo']) && $_GET['tipo'] !== '' ? trim($_GET['tipo']) : null;

    $sql = "SELECT ID_Documento, Tipo, Titulo, Descricao, Data_Vigencia, Arquivo_Nome, Arquivo_Caminho, Mime_Type, Tamanho_Bytes, Ativo, Criado_Em, Atualizado_Em
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
