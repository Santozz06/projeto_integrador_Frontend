<?php
require_once '../../bootstrap.php';

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

    // Lista extras no diretório de uploads
    $extras = [];
    $dir = @opendir($uploadAbs);
    if ($dir) {
        while (($e = readdir($dir)) !== false) {
            if ($e === '.' || $e === '..') continue;
            if (strtolower(pathinfo($e, PATHINFO_EXTENSION)) !== 'pdf') continue;
            $extras[] = [
                'name' => $e,
                'url' => $uploadRel . '/' . $e,
                'size' => @filesize($uploadAbs . DIRECTORY_SEPARATOR . $e),
                'mtime' => @filemtime($uploadAbs . DIRECTORY_SEPARATOR . $e),
            ];
        }
        closedir($dir);
    }

    echo json_encode(['success' => true, 'data' => [ 'map' => $map, 'extras' => $extras ]]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
