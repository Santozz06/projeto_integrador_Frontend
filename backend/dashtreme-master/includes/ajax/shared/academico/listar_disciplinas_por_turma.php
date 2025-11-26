<?php
require_once '../../../config/conexao.php';

header('Content-Type: application/json');

$idTurma = isset($_GET['turma_id']) ? (int)$_GET['turma_id'] : 0;
if ($idTurma <= 0) {
    echo json_encode(['success' => false, 'message' => 'turma_id inválido']);
    exit;
}

try {
    // 1) Preferir disciplinas vinculadas à turma via Horarios
    $disciplinas = [];
    $params = [$idTurma];
    $sqlHor = "SELECT DISTINCT 
                    d.ID_Disciplina, d.Nome_Disciplina,
                    up.Nome_Completo AS Professor,
                    d.Carga_Horaria, d.Etapa
               FROM Horarios h
               INNER JOIN Disciplinas d ON d.ID_Disciplina = h.ID_Disciplina
               LEFT JOIN Professores pp ON pp.ID_Professor = COALESCE(d.ID_Professor, h.ID_Professor)
               LEFT JOIN Usuarios up ON up.ID_Usuario = pp.ID_Professor
               WHERE h.ID_Turma = ?";
    // Se professor, restringe às disciplinas que ele ministra (via d.ID_Professor ou h.ID_Professor)
    if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'professor' && isset($_SESSION['usuario_id'])) {
        $sqlHor .= " AND (d.ID_Professor = ? OR h.ID_Professor = ?)";
        $params[] = (int)$_SESSION['usuario_id'];
        $params[] = (int)$_SESSION['usuario_id'];
    }
    $sqlHor .= " ORDER BY d.Nome_Disciplina";

    $stmt = $pdo->prepare($sqlHor);
    $stmt->execute($params);
    $disciplinas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 2) Fallback: se não houver horários cadastrados, lista por ano da turma
    if (!$disciplinas) {
        $sql = "SELECT DISTINCT d.ID_Disciplina, d.Nome_Disciplina,
                           u.Nome_Completo AS Professor,
                           d.Carga_Horaria, d.Etapa
                    FROM Disciplinas d
                    LEFT JOIN Professores p ON p.ID_Professor = d.ID_Professor
                    LEFT JOIN Usuarios u ON u.ID_Usuario = p.ID_Professor
                    WHERE d.Ano_Letivo = (SELECT Ano_Letivo FROM Turmas WHERE ID_Turma = ?)";

        $params = [$idTurma];
        if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'professor' && isset($_SESSION['usuario_id'])) {
            $sql .= " AND (d.ID_Professor = ? OR d.ID_Professor IS NULL)";
            $params[] = (int)$_SESSION['usuario_id'];
        }
        $sql .= " ORDER BY d.Nome_Disciplina";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $disciplinas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    echo json_encode(['success' => true, 'data' => $disciplinas]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
