<?php
require_once '../../../bootstrap.php';
require_once '../../../conexao.php';

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

try {
    // Garante tabela
    $pdo->exec("CREATE TABLE IF NOT EXISTS Atividades (
        ID_Atividade INT NOT NULL AUTO_INCREMENT,
        ID_Turma INT NOT NULL,
        ID_Professor INT NOT NULL,
        Titulo VARCHAR(150) NOT NULL,
        Disciplina VARCHAR(100) NOT NULL,
        Data DATE NOT NULL,
        Ano_Letivo INT NOT NULL,
        PRIMARY KEY (ID_Atividade),
        KEY idx_turma (ID_Turma),
        KEY idx_prof (ID_Professor),
        KEY idx_ano (Ano_Letivo)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

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
