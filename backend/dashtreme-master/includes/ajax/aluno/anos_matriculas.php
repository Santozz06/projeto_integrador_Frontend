<?php
require_once '../../bootstrap.php';

header('Content-Type: application/json');

try {
    if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'aluno' || !isset($_SESSION['usuario_id'])) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Acesso negado']);
        exit;
        }

    $alunoId = (int)$_SESSION['usuario_id'];

    // Busca todas as matrículas do aluno ordenadas por ano
    $sql = "SELECT m.ID_Matricula, COALESCE(m.Ano_Letivo, t.Ano_Letivo) AS Ano_Letivo, m.Status, t.Etapa, t.Nome_Turma
            FROM Matriculas m
            LEFT JOIN Turmas t ON t.ID_Turma = m.ID_Turma
            WHERE m.ID_Aluno = ?
        ORDER BY COALESCE(m.Ano_Letivo, t.Ano_Letivo) DESC";
    $st = $pdo->prepare($sql);
    $st->execute([$alunoId]);
    $rows = $st->fetchAll(PDO::FETCH_ASSOC);

    //evita anos repetidos quando há múltiplas matrículas no mesmo ano)
    $map = [];
    foreach ($rows as $r) {
        $ano = (int)$r['Ano_Letivo'];
        if (!isset($map[$ano])) {
            $map[$ano] = [
                'ano' => $ano,
                'serie' => null,
                'temAtiva' => false
            ];
        }
        // Preferir Etapa como série; se vazio, usar Nome_Turma
        if (!$map[$ano]['serie']) {
            if (!empty($r['Etapa'])) {
                $map[$ano]['serie'] = $r['Etapa'];
            } elseif (!empty($r['Nome_Turma'])) {
                $map[$ano]['serie'] = $r['Nome_Turma'];
            }
        }
        if (isset($r['Status']) && strtolower($r['Status']) === 'ativa') {
            $map[$ano]['temAtiva'] = true;
        }
    }

    // Ordenar anos desc e montar saída única por ano
    krsort($map, SORT_NUMERIC);
    $out = [];
    foreach ($map as $ano => $info) {
        $out[] = [
            'ano' => $info['ano'],
            'serie' => $info['serie'],
            'status' => $info['temAtiva'] ? 'matriculado' : 'aprovado'
        ];
    }

    echo json_encode(['success' => true, 'anos' => $out]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
