<?php
require_once '../../../config/conexao.php';

header('Content-Type: application/json');

$ano = isset($_GET['ano']) && $_GET['ano'] !== '' ? (int) $_GET['ano'] : null;
$professorId = isset($_GET['professor_id']) && $_GET['professor_id'] !== '' ? (int) $_GET['professor_id'] : null;
$disciplina = isset($_GET['disciplina']) && $_GET['disciplina'] !== '' ? trim($_GET['disciplina']) : null;

try {
    // primeiro via Horarios
    $paramsH = [];
    $sqlH = "SELECT h.ID_Professor,
                  GROUP_CONCAT(DISTINCT d.Nome_Disciplina ORDER BY d.Nome_Disciplina SEPARATOR ', ') AS Disciplinas,
                  ROUND(SUM(TIMESTAMPDIFF(MINUTE, h.Hora_Inicio, h.Hora_Fim)) / 60, 2) AS Carga_Total
              FROM Horarios h
              INNER JOIN Disciplinas d ON d.ID_Disciplina = h.ID_Disciplina
              WHERE 1=1";
    if ($ano) {
        $sqlH .= " AND h.Ano_Letivo = ?";
        $paramsH[] = $ano;
    }
    if ($disciplina) {
        $sqlH .= " AND d.Nome_Disciplina = ?";
        $paramsH[] = $disciplina;
        }
    if ($professorId) {
        $sqlH .= " AND h.ID_Professor = ?";
        $paramsH[] = $professorId;
    }
    $sqlH .= " GROUP BY h.ID_Professor";


    $paramsD = [];
    $sqlD = "SELECT ID_Professor,
                  GROUP_CONCAT(DISTINCT Nome_Disciplina ORDER BY Nome_Disciplina SEPARATOR ', ') AS Disciplinas,
                  SUM(Carga_Horaria) AS Carga_Total
              FROM Disciplinas WHERE 1=1";
    if ($ano) {
        $sqlD .= " AND Ano_Letivo = ?";
        $paramsD[] = $ano;
    }
    if ($disciplina) {
        $sqlD .= " AND Nome_Disciplina = ?";
        $paramsD[] = $disciplina;
    }
    if ($professorId) {
        $sqlD .= " AND ID_Professor = ?";
        $paramsD[] = $professorId;
    }
    $sqlD .= " GROUP BY ID_Professor";

    // decide fonte principal
    $useHorarios = false;
    $useFallback = false;
    try {
        $cntH = "SELECT COUNT(*) FROM Horarios h INNER JOIN Disciplinas d ON d.ID_Disciplina=h.ID_Disciplina WHERE 1=1";
        $pH = [];
        if ($ano) {
            $cntH .= " AND h.Ano_Letivo = ?";
            $pH[] = $ano;
        }
        if ($disciplina) {
            $cntH .= " AND d.Nome_Disciplina = ?";
            $pH[] = $disciplina;
        }
        if ($professorId) {
            $cntH .= " AND h.ID_Professor = ?";
            $pH[] = $professorId;
        }
        $cH = $pdo->prepare($cntH);
        $cH->execute($pH);
        $useHorarios = (int) $cH->fetchColumn() > 0;
        if (!$useHorarios) {
            $countSql = "SELECT COUNT(*) FROM Disciplinas WHERE 1=1";
            $countParams = [];
            if ($ano) {
                $countSql .= " AND Ano_Letivo = ?";
                $countParams[] = $ano;
            }
            if ($disciplina) {
                $countSql .= " AND Nome_Disciplina = ?";
                $countParams[] = $disciplina;
            }
            if ($professorId) {
                $countSql .= " AND ID_Professor = ?";
                $countParams[] = $professorId;
            }
            $cstmt = $pdo->prepare($countSql);
            $cstmt->execute($countParams);
            $useFallback = (int) $cstmt->fetchColumn() === 0;
        }
    } catch (Exception $e) {
    }

    // monta a lista usando Notas/Matriculas/Turmas
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
    if ($ano) {
        $sqlDF .= " AND t.Ano_Letivo = ?";
        $paramsDF[] = $ano;
    }
    if ($disciplina) {
        $sqlDF .= " AND d.Nome_Disciplina = ?";
        $paramsDF[] = $disciplina;
    }
    $sqlDF .= " GROUP BY pt.ID_Professor";

    // turmas tenta por Horarios, se não tiver usa Professores_Turmas
    $paramsTH = [];
    $sqlTH = "SELECT h.ID_Professor,
                GROUP_CONCAT(DISTINCT t.Nome_Turma ORDER BY t.Nome_Turma SEPARATOR ', ') AS Turmas
            FROM Horarios h
            INNER JOIN Turmas t ON t.ID_Turma = h.ID_Turma
            WHERE 1=1";
    if ($ano) {
        $sqlTH .= " AND h.Ano_Letivo = ?";
        $paramsTH[] = $ano;
    }
    if ($professorId) {
        $sqlTH .= " AND h.ID_Professor = ?";
        $paramsTH[] = $professorId;
    }
    $sqlTH .= " GROUP BY h.ID_Professor";

    $paramsT = [];
    $sqlT = "SELECT pt.ID_Professor,
                  GROUP_CONCAT(DISTINCT t.Nome_Turma ORDER BY t.Nome_Turma SEPARATOR ', ') AS Turmas
              FROM Professores_Turmas pt
              INNER JOIN Turmas t ON pt.ID_Turma = t.ID_Turma WHERE 1=1";
    if ($ano) {
        $sqlT .= " AND t.Ano_Letivo = ?";
        $paramsT[] = $ano;
    }
    if ($professorId) {
        $sqlT .= " AND pt.ID_Professor = ?";
        $paramsT[] = $professorId;
    }
    $sqlT .= " GROUP BY pt.ID_Professor";

    // query principal juntando tudo
    $params = [];
    // checa se Professores tem a coluna Matricula
    $temMatricula = false;
    try {
        $chk = $pdo->prepare("SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'Professores' AND COLUMN_NAME = 'Matricula'");
        $chk->execute();
        $temMatricula = (bool) $chk->fetchColumn();
    } catch (Exception $e) {
    }

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
            LEFT JOIN (" . ($useHorarios ? $sqlH : ($useFallback ? $sqlDF : $sqlD)) . ") d ON d.ID_Professor = p.ID_Professor
            LEFT JOIN (" . ($useHorarios ? $sqlTH : $sqlT) . ") t ON t.ID_Professor = p.ID_Professor
            WHERE 1=1";

    // filtros finais
    if ($professorId) {
        $sql .= " AND p.ID_Professor = ?";
        $params[] = $professorId;
    }
    if ($disciplina) {
        $sql .= " AND d.ID_Professor IS NOT NULL";
    }

    $sql .= " ORDER BY u.Nome_Completo";

    $stmt = $pdo->prepare($sql);
    $execParams = array_merge(
        $useHorarios ? $paramsH : ($useFallback ? $paramsDF : $paramsD),
        $useHorarios ? $paramsTH : $paramsT,
        $params
    );
    $stmt->execute($execParams);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // status 
    foreach ($rows as &$r) {
        $r['Status'] = ((int) $r['Ativo'] === 1) ? 'Ativo' : 'Inativo';
    }

    echo json_encode(['success' => true, 'data' => $rows]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
