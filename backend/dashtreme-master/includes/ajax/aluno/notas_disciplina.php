<?php
require_once '../../bootstrap.php';

header('Content-Type: application/json');

try {
    // Apenas aluno autenticado
    if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'aluno' || !isset($_SESSION['usuario_id'])) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Acesso negado']);
        exit;
    }

    $alunoId = (int)$_SESSION['usuario_id'];
    $disciplinaId = isset($_GET['disciplina_id']) ? (int)$_GET['disciplina_id'] : 0;
    $trimestre = isset($_GET['trimestre']) ? (int)$_GET['trimestre'] : 1;
    $ano = isset($_GET['ano']) ? (int)$_GET['ano'] : null;

    if ($disciplinaId <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'disciplina_id é obrigatório']);
        exit;
    }
    if ($trimestre < 1 || $trimestre > 4) { $trimestre = 1; }

    // Selecionar a matrícula do aluno para o ano informado (ou mais recente)
    if ($ano) {
        $sqlMat = "SELECT m.ID_Matricula, COALESCE(m.Ano_Letivo, t.Ano_Letivo) AS Ano
                   FROM Matriculas m
                   LEFT JOIN Turmas t ON t.ID_Turma = m.ID_Turma
                   WHERE m.ID_Aluno = ? AND COALESCE(m.Ano_Letivo, t.Ano_Letivo) = ?
                   ORDER BY (m.Status = 'Ativa') DESC, m.ID_Matricula DESC
                   LIMIT 1";
        $stMat = $pdo->prepare($sqlMat);
        $stMat->execute([$alunoId, $ano]);
    } else {
        $sqlMat = "SELECT m.ID_Matricula, COALESCE(m.Ano_Letivo, t.Ano_Letivo) AS Ano
                   FROM Matriculas m
                   LEFT JOIN Turmas t ON t.ID_Turma = m.ID_Turma
                   WHERE m.ID_Aluno = ?
                   ORDER BY COALESCE(m.Ano_Letivo, t.Ano_Letivo) DESC, (m.Status = 'Ativa') DESC, m.ID_Matricula DESC
                   LIMIT 1";
        $stMat = $pdo->prepare($sqlMat);
        $stMat->execute([$alunoId]);
    }
    $mat = $stMat->fetch(PDO::FETCH_ASSOC);
    if (!$mat) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Matrícula não encontrada para o aluno/ano informado']);
        exit;
    }
    $idMatricula = (int)$mat['ID_Matricula'];

    // Verificar se coluna Trimestre existe em Notas
    $hasTri = false;
    try {
        $check = $pdo->query("SHOW COLUMNS FROM Notas LIKE 'Trimestre'");
        $hasTri = $check->rowCount() > 0;
    } catch (Throwable $e) { $hasTri = false; }

    // Buscar notas
    $sqlNotas = "SELECT Etapa, Nota" . ($hasTri ? ", Trimestre" : "") .
                " FROM Notas WHERE ID_Matricula = ? AND ID_Disciplina = ?" .
                ($hasTri ? " AND Trimestre = ?" : "");
    $params = [$idMatricula, $disciplinaId];
    if ($hasTri) { $params[] = $trimestre; }
    $st = $pdo->prepare($sqlNotas);
    $st->execute($params);
    $rows = $st->fetchAll(PDO::FETCH_ASSOC);

    $map = ['1' => null, '2' => null, '3' => null, '4' => null];
    foreach ($rows as $r) {
        $e = trim((string)$r['Etapa']);
        $e = preg_replace('/[^0-9]/', '', $e);
        if ($e !== '' && array_key_exists($e, $map)) {
            $map[$e] = $r['Nota'] !== null ? (float)$r['Nota'] : null;
        }
    }

    // Obter nome da disciplina para cabeçalho
    $nomeDisc = null;
    try {
        $sd = $pdo->prepare('SELECT Nome_Disciplina FROM Disciplinas WHERE ID_Disciplina = ?');
        $sd->execute([$disciplinaId]);
        $nomeDisc = $sd->fetchColumn();
    } catch (Throwable $e) {}

    echo json_encode([
        'success' => true,
        'data' => [
            'disciplina_id' => $disciplinaId,
            'nome_disciplina' => $nomeDisc,
            'trimestre' => $trimestre,
            'etapas' => $map
        ]
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
