<?php
require_once '../../../bootstrap.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'professor' || !isset($_SESSION['usuario_id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Acesso negado']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

$profId = (int)$_SESSION['usuario_id'];
$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$idTurma = isset($_POST['turma_id']) ? (int)$_POST['turma_id'] : 0;
$titulo = isset($_POST['titulo']) ? trim($_POST['titulo']) : '';
$disciplina = isset($_POST['disciplina']) ? trim($_POST['disciplina']) : '';
$data = isset($_POST['data']) ? trim($_POST['data']) : '';

if ($idTurma <= 0 || $titulo === '' || $disciplina === '' || $data === '') {
    echo json_encode(['success' => false, 'message' => 'Campos obrigatórios ausentes']);
    exit;
}

// Verifica e converte os dados para UTF-8
$titulo = mb_convert_encoding($titulo, 'UTF-8', mb_detect_encoding($titulo));
$disciplina = mb_convert_encoding($disciplina, 'UTF-8', mb_detect_encoding($disciplina));

try {
    // Ano letivo da turma
    $stmt = $pdo->prepare('SELECT Ano_Letivo FROM Turmas WHERE ID_Turma = ?');
    $stmt->execute([$idTurma]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $ano = $row ? (int)$row['Ano_Letivo'] : (int)date('Y');

    if ($id > 0) {
        // Atualiza somente se o registro for do próprio professor
        $sql = 'UPDATE Atividades SET Titulo = ?, Disciplina = ?, Data = ? WHERE ID_Atividade = ? AND ID_Professor = ?';
        $stmt = $pdo->prepare($sql);
        $ok = $stmt->execute([$titulo, $disciplina, $data, $id, $profId]);
        echo json_encode(['success' => (bool)$ok]);
    } else {
        $sql = 'INSERT INTO Atividades (ID_Turma, ID_Professor, Titulo, Disciplina, Data, Ano_Letivo) VALUES (?,?,?,?,?,?)';
        $stmt = $pdo->prepare($sql);
        $ok = $stmt->execute([$idTurma, $profId, $titulo, $disciplina, $data, $ano]);
        echo json_encode(['success' => (bool)$ok, 'id' => $pdo->lastInsertId()]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
