<?php
require_once '../../bootstrap.php';
require_once '../../conexao.php';
// Integração com Google desativada: mantendo apenas calendário local/ICS

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Método não permitido']);
        exit;
    }

    // Permitir admin e professor (professor só apaga o que é dele)
    if (!isset($_SESSION['user_type']) || !in_array($_SESSION['user_type'], ['admin','professor'])) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Acesso negado']);
        exit;
    }

    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    if ($id <= 0) {
        $raw = file_get_contents('php://input');
        $json = json_decode($raw, true);
        if ($json && isset($json['id'])) { $id = (int)$json['id']; }
    }

    if ($id <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'id é obrigatório']);
        exit;
    }

    $isAdmin = isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin';
    $isProfessor = isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'professor';
    $usuarioId = isset($_SESSION['usuario_id']) ? (int)$_SESSION['usuario_id'] : null;

    if ($isProfessor) {
        // Garante coluna Criado_Por
        try {
            $chk = $pdo->query("SHOW COLUMNS FROM Calendario_Academico LIKE 'Criado_Por'");
            if ($chk->rowCount() === 0) {
                $pdo->exec("ALTER TABLE Calendario_Academico ADD COLUMN Criado_Por INT NULL AFTER Publico_Alvo");
            }
        } catch (Throwable $e) { /* ignore */ }

        // Verifica propriedade
        $stChk = $pdo->prepare('SELECT Criado_Por FROM Calendario_Academico WHERE ID_Evento = ?');
        $stChk->execute([$id]);
        $own = $stChk->fetch(PDO::FETCH_ASSOC);
        if (!$own || (int)$own['Criado_Por'] !== $usuarioId) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Você não tem permissão para excluir este evento.']);
            exit;
        }
    }

    // Apagar no banco (admin: qualquer; professor: passou pela verificação)
    $st = $pdo->prepare('DELETE FROM Calendario_Academico WHERE ID_Evento = ?');
    $st->execute([$id]);

    echo json_encode(['success' => true]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
