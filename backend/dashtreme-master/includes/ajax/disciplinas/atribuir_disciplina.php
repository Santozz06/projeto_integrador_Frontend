<?php
require_once '../../config/conexao.php';
header('Content-Type: application/json');

try {

    $idDisc = isset($_POST['id_disciplina']) ? intval($_POST['id_disciplina']) : 0;
    $idProf = isset($_POST['id_professor']) ? intval($_POST['id_professor']) : 0;
    $ano = isset($_POST['ano_letivo']) && $_POST['ano_letivo'] !== '' ? intval($_POST['ano_letivo']) : null;

    if ($idDisc <= 0 || $idProf <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Disciplina e Professor são obrigatórios']);
        exit;
    }

    // Evitar duplicidade
    $stmt = $pdo->prepare("SELECT 1 FROM Professores_Disciplinas WHERE ID_Professor = ? AND ID_Disciplina = ? AND ((? IS NULL AND Ano_Letivo IS NULL) OR Ano_Letivo = ? ) LIMIT 1");
    $stmt->execute([$idProf, $idDisc, $ano, $ano]);
    if ($stmt->fetchColumn()) {
        echo json_encode(['success' => true, 'message' => 'Já atribuída']);
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO Professores_Disciplinas (ID_Professor, ID_Disciplina, Ano_Letivo) VALUES (?, ?, ?)");
    $ok = $stmt->execute([$idProf, $idDisc, $ano]);
    echo json_encode(['success' => (bool)$ok, 'message' => $ok ? 'Disciplina atribuída ao professor' : 'Não foi possível atribuir']);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
