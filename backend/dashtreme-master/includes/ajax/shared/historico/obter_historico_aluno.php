<?php
require_once '../../../config/conexao.php';

header('Content-Type: application/json');

$alunoId = isset($_GET['aluno_id']) ? (int)$_GET['aluno_id'] : 0;
if ($alunoId <= 0) {
    echo json_encode(['success' => false, 'message' => 'aluno_id inválido']);
    exit;
}

try {
    // Helper para checar colunas dinamicamente
    $hasCol = function($table, $column) use ($pdo) {
        $sql = "SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ? LIMIT 1";
        $st = $pdo->prepare($sql);
        $st->execute([$table, $column]);
        return (bool)$st->fetchColumn();
    };

    // Montagem dinâmica dos campos pessoais (preferindo Usuarios, fallback para Alunos)
    $selectCampos = [
        'u.Nome_Completo AS Nome_Completo',
        'a.Matricula AS Matricula'
    ];

    // Data_Nascimento, Nacionalidade e CPF seguem a estratégia padrão (preferir Usuarios, fallback Alunos)
    foreach (['Data_Nascimento','Nacionalidade','CPF'] as $campo) {
        $partes = [];
        if ($hasCol('Usuarios', $campo)) $partes[] = "u.$campo";
        if ($hasCol('Alunos', $campo)) $partes[] = "a.$campo";
        if ($partes) {
            $selectCampos[] = 'COALESCE(' . implode(', ', $partes) . ") AS $campo";
        } else {
            $selectCampos[] = "NULL AS $campo";
        }
    }

    // Naturalidade: preferir campo textual; fallback por ID (municipios/estados) quando disponível
    $natTextParts = [];
    if ($hasCol('Usuarios', 'Naturalidade')) $natTextParts[] = 'u.Naturalidade';
    if ($hasCol('Alunos', 'Naturalidade')) $natTextParts[] = 'a.Naturalidade';

    // Descobrir se existem colunas de chave para montar naturalidade via JOIN
    $hasUNatId = $hasCol('Usuarios', 'naturalidade_id');
    $hasUNatUF = $hasCol('Usuarios', 'uf_naturalidade');
    $hasANatId = $hasCol('Alunos', 'naturalidade_id');
    $hasANatUF = $hasCol('Alunos', 'uf_naturalidade');

    $joins = [];
    if ($hasUNatId) $joins[] = 'LEFT JOIN municipios mu ON mu.codigo_ibge = u.naturalidade_id';
    if ($hasUNatUF) $joins[] = 'LEFT JOIN estados eu ON eu.codigo_uf = u.uf_naturalidade';
    if ($hasANatId) $joins[] = 'LEFT JOIN municipios ma ON ma.codigo_ibge = a.naturalidade_id';
    if ($hasANatUF) $joins[] = 'LEFT JOIN estados ea ON ea.codigo_uf = a.uf_naturalidade';

    $exprUNat = null;
    if ($hasUNatId && $hasUNatUF) {
        $exprUNat = "CONCAT(mu.nome, '/', eu.uf)";
    } elseif ($hasUNatId) {
        $exprUNat = 'mu.nome';
    } elseif ($hasUNatUF) {
        $exprUNat = 'eu.uf';
    }

    $exprANat = null;
    if ($hasANatId && $hasANatUF) {
        $exprANat = "CONCAT(ma.nome, '/', ea.uf)";
    } elseif ($hasANatId) {
        $exprANat = 'ma.nome';
    } elseif ($hasANatUF) {
        $exprANat = 'ea.uf';
    }

    $natExprs = $natTextParts;
    if ($exprUNat) $natExprs[] = $exprUNat;
    if ($exprANat) $natExprs[] = $exprANat;

    if (!empty($natExprs)) {
        $selectCampos[] = 'NULLIF(TRIM(COALESCE(' . implode(', ', $natExprs) . ")), '') AS Naturalidade";
    } else {
        $selectCampos[] = "NULL AS Naturalidade";
    }

    // Campos acadêmicos adicionais do aluno (opcionais): INEP, NIS
    foreach (['INEP','NIS'] as $campoA) {
        if ($hasCol('Alunos', $campoA)) {
            $selectCampos[] = "a.$campoA AS $campoA";
        } else {
            $selectCampos[] = "NULL AS $campoA";
        }
    }

    // Filiacao pode existir como campo único ou como Nome_Pai/Nome_Mae
    $filiParts = [];
    if ($hasCol('Usuarios', 'Filiacao')) $filiParts[] = 'u.Filiacao';
    if ($hasCol('Alunos', 'Filiacao')) $filiParts[] = 'a.Filiacao';
    $paiMaeExprs = [];
    $temPaiU = $hasCol('Usuarios', 'Nome_Pai');
    $temMaeU = $hasCol('Usuarios', 'Nome_Mae');
    $temPaiA = $hasCol('Alunos', 'Nome_Pai');
    $temMaeA = $hasCol('Alunos', 'Nome_Mae');
    if ($temPaiU || $temMaeU) {
        $left = $temPaiU ? 'u.Nome_Pai' : 'NULL';
        $right = $temMaeU ? 'u.Nome_Mae' : 'NULL';
        $e = "CONCAT_WS(' e ', $left, $right)";
        $paiMaeExprs[] = $e;
    }
    if ($temPaiA || $temMaeA) {
        $left = $temPaiA ? 'a.Nome_Pai' : 'NULL';
        $right = $temMaeA ? 'a.Nome_Mae' : 'NULL';
        $e = "CONCAT_WS(' e ', $left, $right)";
        $paiMaeExprs[] = $e;
    }
    $todasFili = array_merge($filiParts, $paiMaeExprs);
    if ($todasFili) {
        $selectCampos[] = 'NULLIF(TRIM(COALESCE(' . implode(', ', $todasFili) . ")), '') AS Filiacao";
    } else {
        $selectCampos[] = "NULL AS Filiacao";
    }

    $sqlAluno = 'SELECT ' . implode(', ', $selectCampos)
        . ' FROM Alunos a INNER JOIN Usuarios u ON u.ID_Usuario = a.ID_Aluno '
        . (empty($joins) ? '' : (' ' . implode(' ', $joins) . ' '))
        . ' WHERE a.ID_Aluno = ?';
    $stmt = $pdo->prepare($sqlAluno);
    $stmt->execute([$alunoId]);
    $aluno = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$aluno) {
        echo json_encode(['success' => false, 'message' => 'Aluno não encontrado']);
        exit;
    }

    // Normalizações: strings vazias -> null; datas inválidas -> null
    foreach (['Nacionalidade','Naturalidade','Filiacao','INEP','NIS','CPF'] as $k) {
        if (isset($aluno[$k])) {
            $aluno[$k] = (is_string($aluno[$k]) && trim($aluno[$k]) === '') ? null : $aluno[$k];
        }
    }
    if (!empty($aluno['Data_Nascimento'])) {
        $dn = $aluno['Data_Nascimento'];
        if ($dn === '0000-00-00' || $dn === '0000-00-00 00:00:00') {
            $aluno['Data_Nascimento'] = null;
        }
    }

    // Matriculas do aluno por ano letivo, com série (Etapa) da turma quando disponível
    $sqlMat = "SELECT m.ID_Matricula, m.Ano_Letivo, t.Etapa, t.Nome_Turma
               FROM Matriculas m
               LEFT JOIN Turmas t ON t.ID_Turma = m.ID_Turma
               WHERE m.ID_Aluno = ?
               ORDER BY m.Ano_Letivo ASC";
    $stmt = $pdo->prepare($sqlMat);
    $stmt->execute([$alunoId]);
    $matriculas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $anos = [];
    $mapMatAno = [];
    $seriesPorAno = [];
    foreach ($matriculas as $m) {
        $anoLetivo = (int)$m['Ano_Letivo'];
        $mapMatAno[$m['ID_Matricula']] = $anoLetivo;
        if (!in_array($anoLetivo, $anos, true)) {
            $anos[] = $anoLetivo;
        }
        // Série: preferir Etapa; fallback Nome_Turma
        $serie = null;
        if (isset($m['Etapa']) && $m['Etapa'] !== null && trim($m['Etapa']) !== '') {
            $serie = $m['Etapa'];
        } elseif (isset($m['Nome_Turma']) && $m['Nome_Turma'] !== null && trim($m['Nome_Turma']) !== '') {
            $serie = $m['Nome_Turma'];
        }
        if ($serie !== null) {
            $seriesPorAno[(string)$anoLetivo] = $serie;
        }
    }

    sort($anos);

    $disciplinas = [];
    if (!empty($mapMatAno)) {
        // Buscar se existe coluna Trimestre na tabela Notas
        $hasTri = false;
        try {
            $chk = $pdo->query("SHOW COLUMNS FROM Notas LIKE 'Trimestre'");
            $hasTri = $chk && $chk->rowCount() > 0;
        } catch (Throwable $e) { $hasTri = false; }

        // Buscar médias por disciplina para cada matrícula (ano)
        $placeholders = implode(',', array_fill(0, count($mapMatAno), '?'));
        $params = array_keys($mapMatAno);

        if ($hasTri) {
            // 1) Média por trimestre = AVG(Etapas) no trimestre
            // 2) Média anual = AVG(médias de cada trimestre) para a disciplina
            $sqlNotas = "SELECT 
                            x.ID_Matricula,
                            d.Nome_Disciplina,
                            d.Carga_Horaria,
                            ROUND(AVG(x.Media_Trimestre), 2) AS Media
                         FROM (
                            SELECT n.ID_Matricula, n.ID_Disciplina, n.Trimestre, AVG(n.Nota) AS Media_Trimestre
                            FROM Notas n
                            WHERE n.ID_Matricula IN ($placeholders)
                            GROUP BY n.ID_Matricula, n.ID_Disciplina, n.Trimestre
                         ) x
                         INNER JOIN Disciplinas d ON d.ID_Disciplina = x.ID_Disciplina
                         GROUP BY x.ID_Matricula, x.ID_Disciplina, d.Nome_Disciplina, d.Carga_Horaria
                         ORDER BY d.Nome_Disciplina";
        } else {
            // Compatibilidade: média direta das notas cadastradas (sem trimestre)
            $sqlNotas = "SELECT 
                            n.ID_Matricula,
                            d.Nome_Disciplina,
                            d.Carga_Horaria,
                            ROUND(AVG(n.Nota), 2) AS Media
                         FROM Notas n
                         INNER JOIN Disciplinas d ON d.ID_Disciplina = n.ID_Disciplina
                         WHERE n.ID_Matricula IN ($placeholders)
                         GROUP BY n.ID_Matricula, d.ID_Disciplina, d.Nome_Disciplina, d.Carga_Horaria
                         ORDER BY d.Nome_Disciplina";
        }

        $stmt = $pdo->prepare($sqlNotas);
        $stmt->execute($params);

        $linhas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Pivot por disciplina e ano
        $map = [];
        foreach ($linhas as $row) {
            $ano = $mapMatAno[$row['ID_Matricula']] ?? null;
            if ($ano === null) continue;
            $nomeDisc = $row['Nome_Disciplina'];
            if (!isset($map[$nomeDisc])) {
                $map[$nomeDisc] = ['nome' => $nomeDisc, 'porAno' => []];
            }
            $map[$nomeDisc]['porAno'][(string)$ano] = [
                'nota' => $row['Media'] !== null ? (float)$row['Media'] : null,
                'ch' => $row['Carga_Horaria'] !== null ? (int)$row['Carga_Horaria'] : null,
            ];
        }

        // Ordenar disciplinas alfabeticamente
        ksort($map, SORT_NATURAL | SORT_FLAG_CASE);
        $disciplinas = array_values($map);
    }

    echo json_encode([
        'success' => true,
        'aluno' => $aluno,
        'anos' => $anos,
        'series_por_ano' => $seriesPorAno,
        'disciplinas' => $disciplinas,
        'observacoes' => null
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
