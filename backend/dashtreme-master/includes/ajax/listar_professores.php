<?php
require_once '../../includes/bootstrap.php';
require_once '../../includes/conexao.php';

header('Content-Type: application/json');

$departamento = isset($_GET['departamento']) ? trim($_GET['departamento']) : '';
$status = isset($_GET['status']) ? trim($_GET['status']) : '';
// Filtro opcional por ano letivo para agregação de disciplinas
$ano = isset($_GET['ano']) && $_GET['ano'] !== '' ? (int)$_GET['ano'] : null;

try {
    // Não há campo de status no schema atual para professores; usamos 'Ativo' sempre por enquanto.
    // Se quiser suportar status real, adicione coluna em Professores ou use Usuarios.Ativo.
    $params = [];

    // Checar existência da coluna Matricula na tabela Professores
    $temMatricula = false;
    try {
        $chk = $pdo->prepare("SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'Professores' AND COLUMN_NAME = 'Matricula'");
        $chk->execute();
        $temMatricula = (bool)$chk->fetchColumn();
    } catch (Exception $e) { /* ignore */ }

    $selectMatricula = $temMatricula ? ", p.Matricula" : "";

    $sql = "SELECT 
                u.ID_Usuario AS ID_Professor,
                u.Nome_Completo,
                u.Email,
                u.Telefone,
                u.Ativo,
                p.Formacao,
                p.Data_Ingresso,
                p.Area_Atuacao" . $selectMatricula . ",
                GROUP_CONCAT(DISTINCT t.Nome_Turma ORDER BY t.Nome_Turma SEPARATOR ', ') AS Turmas,
                GROUP_CONCAT(DISTINCT d.Nome_Disciplina ORDER BY d.Nome_Disciplina SEPARATOR ', ') AS Disciplinas
            FROM Professores p
            INNER JOIN Usuarios u ON p.ID_Professor = u.ID_Usuario
            LEFT JOIN Professores_Turmas pt ON p.ID_Professor = pt.ID_Professor
            LEFT JOIN Turmas t ON pt.ID_Turma = t.ID_Turma
            LEFT JOIN Disciplinas d ON d.ID_Professor = p.ID_Professor";

    // Aplica filtro de ano letivo diretamente no JOIN das disciplinas (preserva professores sem disciplinas)
    if ($ano) {
        $sql .= " AND d.Ano_Letivo = ?";
        $params[] = $ano;
    }

    $sql .= " WHERE 1=1";

    if ($departamento !== '') {
        $sql .= " AND p.Area_Atuacao = ?";
        $params[] = $departamento;
    }
    // Filtro de status usando Usuarios.Ativo (1=Ativo, 0=Inativo)
    if ($status !== '') {
        if (strcasecmp($status, 'Ativo') === 0) {
            $sql .= " AND u.Ativo = 1";
        } elseif (strcasecmp($status, 'Inativo') === 0) {
            $sql .= " AND u.Ativo = 0";
        }
        // 'Afastado'/'Licença' não existem no schema—ignorar ou implementar quando houver coluna específica
    }

    $sql .= ($temMatricula
        ? " GROUP BY u.ID_Usuario, u.Nome_Completo, u.Email, u.Telefone, u.Ativo, p.Matricula, p.Formacao, p.Data_Ingresso, p.Area_Atuacao"
        : " GROUP BY u.ID_Usuario, u.Nome_Completo, u.Email, u.Telefone, u.Ativo, p.Formacao, p.Data_Ingresso, p.Area_Atuacao")
        . " ORDER BY u.Nome_Completo";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Mapear status amigável
    $rows = array_map(function($r){
        $r['Status'] = ((int)$r['Ativo'] === 1) ? 'Ativo' : 'Inativo';
        return $r;
    }, $rows);

    echo json_encode(['success' => true, 'data' => $rows]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
