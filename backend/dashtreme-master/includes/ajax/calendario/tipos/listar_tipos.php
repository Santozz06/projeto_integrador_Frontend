<?php
require_once '../../bootstrap.php';

header('Content-Type: application/json');

try {
    if (!isset($_SESSION['usuario_id'])) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Sessão expirada']);
        exit;
    }

    $idUsuario = (int)$_SESSION['usuario_id'];

    // Tipos padrão (globais, não editáveis)
    $defaults = [
        ['nome' => 'feriado',  'label' => 'Feriado',                'cor' => '#ffc107', 'is_default' => true],
        ['nome' => 'reuniao',  'label' => 'Reunião',                'cor' => '#28a745', 'is_default' => true],
        ['nome' => 'evento',   'label' => 'Evento Institucional',   'cor' => '#6f42c1', 'is_default' => true],
        ['nome' => 'conselho', 'label' => 'Conselho de Classe',     'cor' => '#17a2b8', 'is_default' => true],
        ['nome' => 'formacao', 'label' => 'Formação Pedagógica',    'cor' => '#6610f2', 'is_default' => true],
    ];

    // Garantir tabela
    $pdo->exec("CREATE TABLE IF NOT EXISTS Tipos_Eventos (
        ID INT AUTO_INCREMENT PRIMARY KEY,
        ID_Usuario INT NOT NULL,
        Nome VARCHAR(64) NOT NULL,
        Label VARCHAR(128) NOT NULL,
        Cor VARCHAR(16) NOT NULL,
        UNIQUE KEY uq_user_nome (ID_Usuario, Nome)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // Buscar tipos do usuário
    $stmt = $pdo->prepare('SELECT Nome, Label, Cor FROM Tipos_Eventos WHERE ID_Usuario = ? ORDER BY Label');
    $stmt->execute([$idUsuario]);
    $custom = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Ignorar tentativas de sobrescrever defaults por segurança
        $isDefault = in_array($row['Nome'], array_column($defaults, 'nome'), true);
        if ($isDefault) continue;
        $custom[] = [
            'nome' => $row['Nome'],
            'label' => $row['Label'],
            'cor' => $row['Cor'],
            'is_default' => false
        ];
    }

    // Junta defaults + custom
    $all = array_merge($defaults, $custom);

    echo json_encode(['success' => true, 'data' => $all]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
