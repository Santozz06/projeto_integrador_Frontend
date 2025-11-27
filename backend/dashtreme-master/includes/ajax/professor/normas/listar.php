<?php
require_once '../../../bootstrap.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'professor') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Acesso negado']);
    exit;
}

try {
    // Diretórios relativos ao arquivo normas.php (que fica em user_professor)
    $baseFromPage = '..'; // de user_professor para backend/dashtreme-master
    $uploadRel = $baseFromPage . '/uploads/normas';
    $samplesRel = '../user_professor/ArquivosParaExemplos';

    // Caminhos absolutos no filesystem
    $root = realpath(__DIR__ . '/../../../..'); // .../backend/dashtreme-master
    $uploadAbs = $root . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'normas';
    if (!is_dir($uploadAbs)) {
        @mkdir($uploadAbs, 0775, true);
    }

    $stdNames = [
        'normas' => 'normas_academicas.pdf',
        'avaliacoes' => 'avaliacoes_recuperacoes.pdf',
        'frequencia' => 'frequencia_pontualidade.pdf',
    ];

    $map = [];
    foreach ($stdNames as $key => $fname) {
        $candidate = $uploadAbs . DIRECTORY_SEPARATOR . $fname;
        if (is_file($candidate)) {
            $map[$key] = $uploadRel . '/' . $fname;
        } else {
            $map[$key] = $samplesRel . '/' . $fname;
        }
    }

    // Lista extras enviados (do banco de dados)
    $extras = [];
    $stmt = $pdo->prepare("SELECT Arquivo_Nome, Tamanho_Bytes, Atualizado_Em, Criado_Em FROM Documentos WHERE Tipo = 'norma' AND Ativo = 1 ORDER BY Criado_Em DESC");
    $stmt->execute();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $extras[] = [
            'name' => $row['Arquivo_Nome'],
            'size' => $row['Tamanho_Bytes'],
            'mtime' => strtotime($row['Atualizado_Em'] ?? $row['Criado_Em']),
            'url' => 'download_norma.php?name=' . urlencode($row['Arquivo_Nome'])
        ];
    }

    echo json_encode(['success' => true, 'data' => [ 'map' => $map, 'extras' => $extras ]]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
