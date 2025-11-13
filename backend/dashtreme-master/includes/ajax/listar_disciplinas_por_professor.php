<?php
require_once '../../includes/bootstrap.php';
require_once '../../includes/conexao.php';

header('Content-Type: application/json');

$ano = isset($_GET['ano']) && $_GET['ano'] !== '' ? (int)$_GET['ano'] : null;
$professorId = isset($_GET['professor_id']) && $_GET['professor_id'] !== '' ? (int)$_GET['professor_id'] : null;
$disciplina = isset($_GET['disciplina']) && $_GET['disciplina'] !== '' ? trim($_GET['disciplina']) : null;

try {
    // pega disciplinas por professor (com filtros)
    $paramsD = [];
    $sqlD = "SELECT ID_Professor,
                    GROUP_CONCAT(DISTINCT Nome_Disciplina ORDER BY Nome_Disciplina SEPARATOR ', ') AS Disciplinas,
                    SUM(Carga_Horaria) AS Carga_Total
             FROM Disciplinas WHERE 1=1";
    if ($ano) { $sqlD .= " AND Ano_Letivo = ?"; $paramsD[] = $ano; }
    if ($disciplina) { $sqlD .= " AND Nome_Disciplina = ?"; $paramsD[] = $disciplina; }
    if ($professorId) { $sqlD .= " AND ID_Professor = ?"; $paramsD[] = $professorId; }
    $sqlD .= " GROUP BY ID_Professor";

    // se não achar nada em Disciplinas, usa um fallback com outras tabelas
    $useFallback = false;
    try {
        $countSql = "SELECT COUNT(*) FROM Disciplinas WHERE 1=1";
        $countParams = [];
        if ($ano) { $countSql .= " AND Ano_Letivo = ?"; $countParams[] = $ano; }
        if ($disciplina) { $countSql .= " AND Nome_Disciplina = ?"; $countParams[] = $disciplina; }
        if ($professorId) { $countSql .= " AND ID_Professor = ?"; $countParams[] = $professorId; }
        $cstmt = $pdo->prepare($countSql);
        $cstmt->execute($countParams);
        $hasRows = (int)$cstmt->fetchColumn() > 0;
        $useFallback = !$hasRows;
    } catch (Exception $e) {
    // se der erro aqui, segue o plano principal mesmo
        $useFallback = false;
    }

    // fallback: monta a lista usando Notas/Matriculas/Turmas
    $paramsDF = [];
    $sqlDF = "SELECT pt.ID_Professor,
                     GROUP_CONCAT(DISTINCT d.Nome_Disciplina ORDER BY d.Nome_Disciplina SEPARATOR ', ') AS Disciplinas,
                     SUM(d.Carga_Horaria) AS Carga_Total
              FROM Professores_Turmas pt
              INNER JOIN Turmas t ON pt.ID_Turma = t.ID_Turma
              INNER JOIN Matriculas m ON m.ID_Turma = t.ID_Turma
              INNER JOIN Notas n ON n.ID_Matricula = m.ID_Matricula
              INNER JOIN Disciplinas d ON d.ID_Disciplina = n.ID_Disciplina
              WHERE 1=1";
    if ($ano) { $sqlDF .= " AND t.Ano_Letivo = ?"; $paramsDF[] = $ano; }
    if ($disciplina) { $sqlDF .= " AND d.Nome_Disciplina = ?"; $paramsDF[] = $disciplina; }
    // não filtra por professor aqui; a query final já cuida disso
    $sqlDF .= " GROUP BY pt.ID_Professor";

    // turmas por professor (considera ano se passado)
    $paramsT = [];
    $sqlT = "SELECT pt.ID_Professor,
                    GROUP_CONCAT(DISTINCT t.Nome_Turma ORDER BY t.Nome_Turma SEPARATOR ', ') AS Turmas
             FROM Professores_Turmas pt
             INNER JOIN Turmas t ON pt.ID_Turma = t.ID_Turma WHERE 1=1";
    if ($ano) { $sqlT .= " AND t.Ano_Letivo = ?"; $paramsT[] = $ano; }
    $sqlT .= " GROUP BY pt.ID_Professor";

    // query principal juntando tudo
    $params = [];
    // checa se Professores tem a coluna Matricula
    $temMatricula = false;
    try {
        $chk = $pdo->prepare("SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'Professores' AND COLUMN_NAME = 'Matricula'");
        $chk->execute();
        $temMatricula = (bool)$chk->fetchColumn();
    } catch (Exception $e) { }

    $colMat = $temMatricula ? ", p.Matricula" : "";

    $sql = "SELECT u.ID_Usuario AS ID_Professor,
                   u.Nome_Completo,
                   u.Ativo,
                   p.Area_Atuacao" . $colMat . ",
                   d.Disciplinas,
                   d.Carga_Total,
                   t.Turmas
            FROM Professores p
            INNER JOIN Usuarios u ON p.ID_Professor = u.ID_Usuario
            LEFT JOIN (" . ($useFallback ? $sqlDF : $sqlD) . ") d ON d.ID_Professor = p.ID_Professor
            LEFT JOIN (" . $sqlT . ") t ON t.ID_Professor = p.ID_Professor
            WHERE 1=1";

    // filtros finais
    if ($professorId) { $sql .= " AND p.ID_Professor = ?"; $params[] = $professorId; }
    if ($disciplina) { $sql .= " AND d.ID_Professor IS NOT NULL"; }

    $sql .= " ORDER BY u.Nome_Completo";

    $stmt = $pdo->prepare($sql);
    $execParams = array_merge($useFallback ? $paramsDF : $paramsD, $paramsT, $params);
    $stmt->execute($execParams);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // status amigável
    foreach ($rows as &$r) {
        $r['Status'] = ((int)$r['Ativo'] === 1) ? 'Ativo' : 'Inativo';
    }

    echo json_encode(['success' => true, 'data' => $rows]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
