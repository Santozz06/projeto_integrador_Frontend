<?php
require_once '../../../bootstrap.php';

header('Content-Type: application/json');

try {
    if (!isset($_SESSION['usuario_id'])) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Sessão expirada']);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método inválido');
    }

    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);
    if (!is_array($data)) { $data = $_POST; }

    $idUsuario = (int)$_SESSION['usuario_id'];
    $label = isset($data['label']) ? trim($data['label']) : '';
    $cor = isset($data['cor']) ? trim($data['cor']) : '';
    $nome = isset($data['nome']) ? trim($data['nome']) : '';
    $old = isset($data['old']) ? trim($data['old']) : '';

    if ($label === '' || $cor === '') { throw new Exception('Label e cor são obrigatórios'); }

    // Normaliza nome se não informado (slug do label)
    if ($nome === '') {
        $nome = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $label));
        $nome = trim($nome, '-');
        if ($nome === '') $nome = 'tipo';
    }

    // Defaults reservados não podem ser alterados
    $reservados = ['feriado','reuniao','evento','conselho','formacao'];
    if (in_array($nome, $reservados, true) || ($old && in_array($old, $reservados, true))) {
        throw new Exception('Tipos padrão não podem ser editados');
    }

    // Garantir tabela
    $pdo->exec("CREATE TABLE IF NOT EXISTS Tipos_Eventos (
        ID INT AUTO_INCREMENT PRIMARY KEY,
        ID_Usuario INT NOT NULL,
        Nome VARCHAR(64) NOT NULL,
        Label VARCHAR(128) NOT NULL,
        Cor VARCHAR(16) NOT NULL,
        UNIQUE KEY uq_user_nome (ID_Usuario, Nome)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    if ($old !== '' && $old !== $nome) {
        // Renomear: excluir/atualizar chave
        $stmt = $pdo->prepare('SELECT 1 FROM Tipos_Eventos WHERE ID_Usuario = ? AND Nome = ?');
        $stmt->execute([$idUsuario, $old]);
        if (!$stmt->fetchColumn()) {
            throw new Exception('Tipo a editar não encontrado');
        }
        // Verificar conflito com novo nome
        $stmt = $pdo->prepare('SELECT 1 FROM Tipos_Eventos WHERE ID_Usuario = ? AND Nome = ?');
        $stmt->execute([$idUsuario, $nome]);
        if ($stmt->fetchColumn()) {
            throw new Exception('Já existe um tipo com esse nome');
        }
        // Atualiza registrando o novo nome
        $stmt = $pdo->prepare('UPDATE Tipos_Eventos SET Nome = ?, Label = ?, Cor = ? WHERE ID_Usuario = ? AND Nome = ?');
        $stmt->execute([$nome, $label, $cor, $idUsuario, $old]);
    } else {
        // Upsert simples por (ID_Usuario, Nome)
        $stmt = $pdo->prepare('SELECT 1 FROM Tipos_Eventos WHERE ID_Usuario = ? AND Nome = ?');
        $stmt->execute([$idUsuario, $nome]);
        if ($stmt->fetchColumn()) {
            $stmt = $pdo->prepare('UPDATE Tipos_Eventos SET Label = ?, Cor = ? WHERE ID_Usuario = ? AND Nome = ?');
            $stmt->execute([$label, $cor, $idUsuario, $nome]);
        } else {
            $stmt = $pdo->prepare('INSERT INTO Tipos_Eventos (ID_Usuario, Nome, Label, Cor) VALUES (?,?,?,?)');
            $stmt->execute([$idUsuario, $nome, $label, $cor]);
        }
    }

    echo json_encode(['success' => true, 'data' => ['nome' => $nome, 'label' => $label, 'cor' => $cor]]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
