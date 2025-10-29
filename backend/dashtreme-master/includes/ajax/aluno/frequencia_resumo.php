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
    $ano = isset($_GET['ano']) ? (int)$_GET['ano'] : null; // opcional

    // Pega a matrícula ativa (do ano quando informado; senão mais recente)
    $params = [$alunoId];
    $sqlMat = "SELECT ID_Matricula, ID_Turma, Ano_Letivo
               FROM Matriculas
               WHERE ID_Aluno = ? AND Status = 'Ativa'";
    if ($ano) { $sqlMat .= " AND Ano_Letivo = ?"; $params[] = $ano; }
    $sqlMat .= " ORDER BY Ano_Letivo DESC LIMIT 1";

    $stm = $pdo->prepare($sqlMat);
    $stm->execute($params);
    $mat = $stm->fetch(PDO::FETCH_ASSOC);

    if (!$mat) {
        echo json_encode(['success' => true, 'data' => [
            'ano' => $ano ?: null,
            'matricula' => null,
            'turma' => null,
            'presencas' => 0,
            'total' => 0,
            'percentual' => null
        ]]);
        exit;
    }

    $matId = (int)$mat['ID_Matricula'];
    $anoLetivo = (int)$mat['Ano_Letivo'];

    // Turma nome
    $turmaNome = null;
    try {
        $stT = $pdo->prepare("SELECT Nome_Turma FROM Turmas WHERE ID_Turma = ?");
        $stT->execute([(int)$mat['ID_Turma']]);
        $rT = $stT->fetch(PDO::FETCH_ASSOC);
        $turmaNome = $rT ? $rT['Nome_Turma'] : null;
    } catch (Throwable $e) { /* ignore */ }

    // Garante tabela Frequencias
    $pdo->exec("CREATE TABLE IF NOT EXISTS Frequencias (
        ID_Frequencia INT AUTO_INCREMENT PRIMARY KEY,
        ID_Matricula INT NOT NULL,
        Data DATE NOT NULL,
        Presenca TINYINT(1) NOT NULL,
        Observacao VARCHAR(255) NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // Resumo simples
    $st1 = $pdo->prepare("SELECT COUNT(*) FROM Frequencias WHERE ID_Matricula = ?");
    $st1->execute([$matId]);
    $total = (int)$st1->fetchColumn();

    $st2 = $pdo->prepare("SELECT COUNT(*) FROM Frequencias WHERE ID_Matricula = ? AND Presenca = 1");
    $st2->execute([$matId]);
    $pres = (int)$st2->fetchColumn();

    $perc = ($total > 0) ? round(($pres / $total) * 100, 1) : null;

    echo json_encode(['success' => true, 'data' => [
        'ano' => $anoLetivo,
        'matricula' => $matId,
        'turma' => $turmaNome,
        'presencas' => $pres,
        'total' => $total,
        'percentual' => $perc
    ]]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
