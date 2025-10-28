<?php
require_once '../../../bootstrap.php';
require_once '../../../conexao.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'professor' || !isset($_SESSION['usuario_id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Acesso negado']);
    exit;
}

// Somente POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

$profId = (int)$_SESSION['usuario_id'];
$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$idTurma = isset($_POST['turma_id']) ? (int)$_POST['turma_id'] : 0;
$disciplina = isset($_POST['disciplina']) ? trim($_POST['disciplina']) : '';
$tipo = isset($_POST['tipo']) ? trim($_POST['tipo']) : '';
$data = isset($_POST['data']) ? trim($_POST['data']) : '';

if ($idTurma <= 0 || $disciplina === '' || $tipo === '' || $data === '') {
    echo json_encode(['success' => false, 'message' => 'Campos obrigatórios ausentes']);
    exit;
}

try {
    // Garante tabela
    $pdo->exec("CREATE TABLE IF NOT EXISTS Avaliacoes (
        ID_Avaliacao INT NOT NULL AUTO_INCREMENT,
        ID_Turma INT NOT NULL,
        ID_Professor INT NOT NULL,
        Disciplina VARCHAR(100) NOT NULL,
        Tipo VARCHAR(50) NOT NULL,
        Data DATE NOT NULL,
        Ano_Letivo INT NOT NULL,
        PRIMARY KEY (ID_Avaliacao),
        KEY idx_turma (ID_Turma),
        KEY idx_prof (ID_Professor),
        KEY idx_ano (Ano_Letivo)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // Ano letivo da turma
    $stmt = $pdo->prepare("SELECT Ano_Letivo FROM Turmas WHERE ID_Turma = ?");
    $stmt->execute([$idTurma]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $ano = $row ? (int)$row['Ano_Letivo'] : null;
    if (!$ano) { $ano = (int)date('Y'); }

    if ($id > 0) {
        // Atualiza somente se o registro for do próprio professor
        $sql = "UPDATE Avaliacoes SET Disciplina = ?, Tipo = ?, Data = ? WHERE ID_Avaliacao = ? AND ID_Professor = ?";
        $stmt = $pdo->prepare($sql);
        $ok = $stmt->execute([$disciplina, $tipo, $data, $id, $profId]);
        echo json_encode(['success' => (bool)$ok]);
    } else {
        // Cria
        $sql = "INSERT INTO Avaliacoes (ID_Turma, ID_Professor, Disciplina, Tipo, Data, Ano_Letivo) VALUES (?,?,?,?,?,?)";
        $stmt = $pdo->prepare($sql);
        $ok = $stmt->execute([$idTurma, $profId, $disciplina, $tipo, $data, $ano]);
        echo json_encode(['success' => (bool)$ok, 'id' => $pdo->lastInsertId()]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
