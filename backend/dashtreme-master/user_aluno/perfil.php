<?php 
require_once '../includes/bootstrap.php';
$idAluno = $_SESSION['usuario_id'];

try {
    // Buscar dados completos do aluno
    $aluno = $usuarioCRUD->buscarAlunoCompleto($idAluno);

    if (!$aluno) {
        die('Erro: Aluno não encontrado no banco de dados. ID: ' . $idAluno);
    }
} catch (Exception $e) {
    die('Erro ao buscar dados do aluno: ' . $e->getMessage());
}

// Função para gerar iniciais
function gerarIniciaisPerfil($nome) {
    $nome = trim($nome);
    if ($nome === '') return 'US';
    $parts = preg_split('/\s+/u', $nome);
    $first = mb_substr($parts[0], 0, 1, 'UTF-8');
    $last = count($parts) > 1 ? mb_substr(end($parts), 0, 1, 'UTF-8') : (mb_strlen($parts[0], 'UTF-8') > 1 ? mb_substr($parts[0], 1, 1, 'UTF-8') : $first);
    return mb_strtoupper($first . $last, 'UTF-8');
}

$iniciais = gerarIniciaisPerfil($aluno['Nome_Completo'] ?? 'Usuário');

// Buscar turma do aluno
try {
    $sqlTurma = "SELECT t.Nome_Turma, t.Turno, t.Ano_Letivo, t.Serie
                 FROM Alunos_Turmas at
                 INNER JOIN Turmas t ON at.ID_Turma = t.ID_Turma
                 WHERE at.ID_Aluno = ?
                 ORDER BY t.Ano_Letivo DESC
                 LIMIT 1";
    $stmtTurma = $pdo->prepare($sqlTurma);
    $stmtTurma->execute([$idAluno]);
    $turma = $stmtTurma->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $turma = null;
    error_log("Erro ao buscar turma: " . $e->getMessage());
}

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Perfil Acadêmico - Dashboard</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="../assets/css/icons.css" />
    <link rel="stylesheet" href="../assets/css/app-style.css" />
    <link rel="stylesheet" href="../assets/css/sidebar-menu.css" />
    <link rel="stylesheet" href="../css/style.css">
    
</head>

<body class="bg-theme bg-theme1 user_aluno_perfil">
    <?php
    require("menu_padrao.php");
    ?>

        <!-- Conteúdo principal -->
        <div class="main-content">
            <div class="perfil-container">
                <div class="perfil-header">Perfil acadêmico (aluno)</div>
                <div class="dados-topo">
                    <div class="foto">
                        <span class="avatar-initials avatar-xl">
                            <?= htmlspecialchars($iniciais) ?>
                        </span>
                    </div>
                    <div class="dados-pessoais">
                        <p><span>Nome:</span> <?= htmlspecialchars($aluno['Nome_Completo'] ?? 'Não informado') ?></p>
                        <p><span>Matrícula:</span> <?= htmlspecialchars($aluno['Matricula'] ?? 'Não informado') ?></p>
                        <p><span>CPF:</span> <?= htmlspecialchars($aluno['CPF'] ?? 'Não informado') ?></p>
                        <p><span>Telefone:</span> <?= htmlspecialchars($aluno['Telefone'] ?? 'Não informado') ?></p>
                        <p><span>Email:</span> <?= htmlspecialchars($aluno['Email'] ?? 'Não informado') ?></p>
                    </div>
                    <div class="dados-profissionais">
                        <p><strong>Data de Nascimento:</strong> <?= htmlspecialchars($aluno['Data_Nascimento'] ?? 'Não informado') ?></p>
                        <p><strong>Sexo:</strong> <?= htmlspecialchars($aluno['Sexo'] ?? 'Não informado') ?></p>
                        <p><strong>Nome do Responsável:</strong> <?= htmlspecialchars($aluno['Nome_Responsavel'] ?? 'Não informado') ?></p>
                        <p><strong>Telefone Responsável:</strong> <?= htmlspecialchars($aluno['Telefone_Responsavel'] ?? 'Não informado') ?></p>
                        <p><strong>Data de Ingresso:</strong> <?= htmlspecialchars($aluno['Data_Ingresso'] ?? 'Não informado') ?></p>
                    </div>
                </div>
                
                <?php if ($turma): ?>
                <div class="atuacao">
                    <h5 class="text-center mb-3">Turma atual</h5>
                    <div class="table-responsive">
                        <table class="table-mobile-responsive">
                            <thead>
                                <tr>
                                    <th>Turma</th>
                                    <th>Série</th>
                                    <th>Turno</th>
                                    <th>Ano Letivo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td data-label="Turma"><?= htmlspecialchars($turma['Nome_Turma']) ?></td>
                                    <td data-label="Série"><?= htmlspecialchars($turma['Serie']) ?></td>
                                    <td data-label="Turno"><?= htmlspecialchars($turma['Turno']) ?></td>
                                    <td data-label="Ano Letivo"><?= htmlspecialchars($turma['Ano_Letivo']) ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php else: ?>
                <div class="atuacao">
                    <h5 class="text-center mb-3">Turma atual</h5>
                    <p class="text-center">Nenhuma turma vinculada.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="overlay toggle-menu"></div>
    </div>

    <script src="../assets/js/jquery.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script src="../assets/plugins/simplebar/js/simplebar.js"></script>
    <script src="../assets/js/sidebar-menu.js"></script>
    <script src="../assets/js/app-script.js"></script>
</body>

</html>
