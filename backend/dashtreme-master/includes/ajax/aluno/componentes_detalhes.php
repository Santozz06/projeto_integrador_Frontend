<?php
require_once '../../bootstrap.php';
require_once '../../conexao.php';

header('Content-Type: application/json');

try {
    if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'aluno' || !isset($_SESSION['usuario_id'])) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Acesso negado']);
        exit;
    }

    $alunoId = (int)$_SESSION['usuario_id'];
    $ano = isset($_GET['ano']) ? (int)$_GET['ano'] : null;
    $disciplinaId = isset($_GET['disciplina']) ? (int)$_GET['disciplina'] : null;

    if (!$ano || !$disciplinaId) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Parâmetros ausentes (ano, disciplina)']);
        exit;
    }

    // Recuperar a turma/matrícula do aluno nesse ano
    $sqlMat = "SELECT m.ID_Matricula, m.ID_Turma, t.Nome_Turma, t.Etapa
               FROM Matriculas m
               LEFT JOIN Turmas t ON t.ID_Turma = m.ID_Turma
               WHERE m.ID_Aluno = ? AND COALESCE(m.Ano_Letivo, t.Ano_Letivo) = ?
               ORDER BY (m.Status = 'Ativa') DESC, m.ID_Matricula DESC";
    $stM = $pdo->prepare($sqlMat);
    $stM->execute([$alunoId, $ano]);
    $mats = $stM->fetchAll(PDO::FETCH_ASSOC);

    if (!$mats) {
        echo json_encode(['success' => false, 'message' => 'Nenhuma matrícula encontrada para o ano informado']);
        exit;
    }

    // Usar a primeira matrícula (preferindo ativa) para cabeçalho e frequência
    $mat = $mats[0];
    $idMatricula = (int)$mat['ID_Matricula'];
    $idTurma = (int)$mat['ID_Turma'];

    // Informação da disciplina e professor
    $sqlDisc = "SELECT d.Nome_Disciplina, d.Carga_Horaria, u.Nome_Completo AS Professor_Nome
                FROM Disciplinas d
                LEFT JOIN Professores p ON p.ID_Professor = d.ID_Professor
                LEFT JOIN Usuarios u ON u.ID_Usuario = p.ID_Professor
                WHERE d.ID_Disciplina = ?";
    $stD = $pdo->prepare($sqlDisc);
    $stD->execute([$disciplinaId]);
    $disc = $stD->fetch(PDO::FETCH_ASSOC);
    if (!$disc) {
        echo json_encode(['success' => false, 'message' => 'Disciplina não encontrada']);
        exit;
    }

    // Frequência por disciplina (preferencial). Fallback para geral se coluna/registro não existir.
    $temColDisc = false;
    try {
        $stCol = $pdo->prepare("SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'Presencas' AND COLUMN_NAME = 'ID_Disciplina'");
        $stCol->execute();
        $temColDisc = (int)$stCol->fetchColumn() > 0;
    } catch (Throwable $e) {
        $temColDisc = false;
    }

    $total = 0; $presentes = 0; $ausencias = 0; $perc = null; $frequenciaFonte = 'geral';
    if ($temColDisc) {
        $sqlFreqDisc = "SELECT 
                            COUNT(*) AS total,
                            SUM(CASE WHEN Status = 'P' THEN 1 ELSE 0 END) AS presentes,
                            SUM(CASE WHEN Status IN ('A','J') THEN 1 ELSE 0 END) AS ausencias
                        FROM Presencas
                        WHERE ID_Matricula = ? AND ID_Turma = ? AND ID_Disciplina = ?";
        $stFD = $pdo->prepare($sqlFreqDisc);
        $stFD->execute([$idMatricula, $idTurma, $disciplinaId]);
        $freqD = $stFD->fetch(PDO::FETCH_ASSOC) ?: ['total' => 0, 'presentes' => 0, 'ausencias' => 0];
        $total = (int)($freqD['total'] ?? 0);
        $presentes = (int)($freqD['presentes'] ?? 0);
        $ausencias = (int)($freqD['ausencias'] ?? 0);
        if ($total > 0) {
            $perc = round(($presentes / $total) * 100);
            $frequenciaFonte = 'disciplina';
        }
    }
    if ($perc === null) {
        // Fallback para frequência geral do ano (compatibilidade com bases antigas)
        $sqlFreqG = "SELECT 
                        COUNT(*) AS total,
                        SUM(CASE WHEN Status = 'P' THEN 1 ELSE 0 END) AS presentes,
                        SUM(CASE WHEN Status IN ('A','J') THEN 1 ELSE 0 END) AS ausencias
                    FROM Presencas
                    WHERE ID_Matricula = ? AND ID_Turma = ?";
        $stFG = $pdo->prepare($sqlFreqG);
        $stFG->execute([$idMatricula, $idTurma]);
        $freqG = $stFG->fetch(PDO::FETCH_ASSOC) ?: ['total' => 0, 'presentes' => 0, 'ausencias' => 0];
        $total = (int)($freqG['total'] ?? 0);
        $presentes = (int)($freqG['presentes'] ?? 0);
        $ausencias = (int)($freqG['ausencias'] ?? 0);
        $perc = $total > 0 ? round(($presentes / $total) * 100) : null;
        $frequenciaFonte = 'geral';
    }

    // Plano de ensino (geral por disciplina)
    $sqlPlano = "SELECT Conteudo, Objetivos, Metodologia, Avaliacao
                 FROM Planos_Ensino
                 WHERE ID_Disciplina = ?";
    $stP = $pdo->prepare($sqlPlano);
    $stP->execute([$disciplinaId]);
    $plano = $stP->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'header' => [
            'ano' => $ano,
            'disciplina' => $disc['Nome_Disciplina'],
            'carga_horaria' => isset($disc['Carga_Horaria']) ? (int)$disc['Carga_Horaria'] : null,
            'professor' => $disc['Professor_Nome'] ?? null,
            'turma' => $mat['Nome_Turma'] ?? null,
            'etapa' => $mat['Etapa'] ?? null,
            'frequencia' => [
                'total' => $total,
                'presentes' => $presentes,
                'ausencias' => $ausencias,
                'percentual' => $perc,
                'fonte' => $frequenciaFonte
            ]
        ],
        'plano' => $plano ?: null
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
