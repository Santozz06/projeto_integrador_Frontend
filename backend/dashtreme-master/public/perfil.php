<?php 
$__bootstrapLoaded = false;
$__candidates = [
    __DIR__ . '/includes/bootstrap.php',
    __DIR__ . '/../includes/bootstrap.php',
    __DIR__ . '/dashtreme-master/includes/bootstrap.php',
    __DIR__ . '/../dashtreme-master/includes/bootstrap.php',
];
foreach ($__candidates as $__p) {
    if (file_exists($__p)) {
        require_once $__p;
        $__bootstrapLoaded = true;
        break;
    }
}
if (!$__bootstrapLoaded) {
    die('Erro: Não foi possível carregar o bootstrap.');
}

if (session_status() === PHP_SESSION_NONE) { @session_start(); }
$idUsuario = $_SESSION['usuario_id'] ?? null;
$tipoUsuario = $_SESSION['user_type'] ?? null; // 'professor' | 'aluno'

function gerarIniciaisPerfil($nome) {
    $nome = trim((string)$nome);
    if ($nome === '') return 'US';
    $parts = preg_split('/\s+/u', $nome);
    $first = mb_substr($parts[0], 0, 1, 'UTF-8');
    $last = count($parts) > 1 ? mb_substr(end($parts), 0, 1, 'UTF-8') : (mb_strlen($parts[0], 'UTF-8') > 1 ? mb_substr($parts[0], 1, 1, 'UTF-8') : $first);
    return mb_strtoupper($first . $last, 'UTF-8');
}

$usuario = null;
$atuacoes = [];
$turma = null;
$situacaoProfessor = null;

try {
    if ($tipoUsuario === 'professor') {
        $usuario = $usuarioCRUD->buscarProfessorCompleto($idUsuario);
        if (!$usuario) { throw new Exception('Professor não encontrado'); }

        // Debug temporário - remover após verificar
        error_log("Dados do professor: " . print_r($usuario, true));

        // Query para buscar disciplinas, turmas e horários do professor (ajustada ao schema: usa tabela Horarios)
        $sqlDisciplinas = "SELECT 
                            d.Nome_Disciplina,
                            t.Nome_Turma,
                            t.Turno,
                            h.Dia_Semana,
                            h.Hora_Inicio,
                            h.Hora_Fim
                           FROM Professores_Turmas pt
                           INNER JOIN Turmas t ON pt.ID_Turma = t.ID_Turma
                           LEFT JOIN Horarios h ON h.ID_Turma = t.ID_Turma AND h.ID_Professor = pt.ID_Professor
                           LEFT JOIN Disciplinas d ON h.ID_Disciplina = d.ID_Disciplina
                           WHERE pt.ID_Professor = ?
                           ORDER BY t.Nome_Turma, d.Nome_Disciplina, h.Dia_Semana, h.Hora_Inicio";
        $stmtDisc = $pdo->prepare($sqlDisciplinas);
        $stmtDisc->execute([$idUsuario]);
        $atuacoes = $stmtDisc->fetchAll(PDO::FETCH_ASSOC) ?: [];

        // Debug temporário - remover após verificar
        error_log("Atuações encontradas: " . print_r($atuacoes, true));

        // Calcular carga horária total semanal a partir dos horários (Hora_Inicio/Hora_Fim)
        $totalMinutos = 0;
        foreach ($atuacoes as $atuacao) {
            $ini = $atuacao['Hora_Inicio'] ?? null;
            $fim = $atuacao['Hora_Fim'] ?? null;
            if ($ini && $fim) {
                // Calcula diferença em minutos
                [$hi, $mi, $si] = array_pad(explode(':', $ini), 3, 0);
                [$hf, $mf, $sf] = array_pad(explode(':', $fim), 3, 0);
                $start = ((int)$hi) * 3600 + ((int)$mi) * 60 + (int)$si;
                $end = ((int)$hf) * 3600 + ((int)$mf) * 60 + (int)$sf;
                $diff = max(0, $end - $start);
                $totalMinutos += (int) round($diff / 60);
            }
        }
        if ($totalMinutos > 0) {
            $horas = floor($totalMinutos / 60);
            $min = $totalMinutos % 60;
            $usuario['Carga_Horaria'] = $min > 0 ? sprintf('%dh %02dmin/semana', $horas, $min) : sprintf('%dh/semana', $horas);
        }

        // Situação de vínculo (vinculado a alguma turma ou não)
        try {
            require_once __DIR__ . '/includes/crud/VinculoCRUD.php';
            $vinculoCRUD = new VinculoCRUD($pdo);
            $situacaoProfessor = $vinculoCRUD->verificarSituacao('professor', $idUsuario);
        } catch (Exception $e) {
            $situacaoProfessor = null;
            error_log("Erro ao verificar situação: " . $e->getMessage());
        }
    } elseif ($tipoUsuario === 'aluno') {
        $usuario = $usuarioCRUD->buscarAlunoCompleto($idUsuario);
        if (!$usuario) { throw new Exception('Aluno não encontrado'); }

        $sqlTurma = "SELECT t.Nome_Turma, t.Turno, t.Ano_Letivo, t.Etapa, m.Data_Matricula
                     FROM Matriculas m
                     INNER JOIN Turmas t ON m.ID_Turma = t.ID_Turma
                     WHERE m.ID_Aluno = ? AND m.Status = 'Ativa'
                     ORDER BY m.Data_Matricula DESC
                     LIMIT 1";
        $stmtTurma = $pdo->prepare($sqlTurma);
        $stmtTurma->execute([$idUsuario]);
        $turma = $stmtTurma->fetch(PDO::FETCH_ASSOC) ?: null;
    } else {
        throw new Exception('Tipo de usuário não suportado');
    }
} catch (Exception $e) {
    error_log('Erro no perfil: ' . $e->getMessage());
}

$iniciais = gerarIniciaisPerfil($usuario['Nome_Completo'] ?? 'Usuário');
$bodyClass = $tipoUsuario === 'professor' ? 'user_professor_perfil' : 'user_aluno_perfil';
$tipoLabel = $tipoUsuario === 'professor' ? 'professor' : 'aluno';
$menuPath = $tipoUsuario === 'professor' ? 'user_professor/menu_padrao.php' : 'user_aluno/menu_padrao.php';
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <link rel="icon" href="../assets/images/favicon.ico" type="image/x-icon">
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Perfil Acadêmico - Dashboard</title>
    <!-- Caminhos corretos a partir de dashtreme-master -->
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="../assets/css/icons.css" />
    <link rel="stylesheet" href="../assets/css/app-style.css" />
    <link rel="stylesheet" href="../assets/css/sidebar-menu.css" />
    <link rel="stylesheet" href="../css/style.css?v=<?php echo time(); ?>">
    
    <style>
      /* Garante que o avatar de iniciais siga o tamanho do exemplo */
      .perfil-container .avatar-initials { width:150px; height:150px; font-size:48px; line-height:150px; }
    </style>
</head>

<body class="bg-theme bg-theme1 <?= htmlspecialchars($bodyClass) ?>">
    <?php
      // Inclui o menu do perfil conforme o tipo de usuário
      $menuPath = $tipoUsuario === 'professor' ? '../user_professor/menu_padrao.php' : '../user_aluno/menu_padrao.php';
      if (file_exists(__DIR__ . '/' . $menuPath)) {
          require __DIR__ . '/' . $menuPath;
      } elseif (file_exists($menuPath)) {
          require $menuPath;
      } else {
          echo '<!-- Menu não encontrado: ' . htmlspecialchars($menuPath) . ' -->';
      }
    ?>

    <!-- Conteúdo principal -->
    <div class="main-content">
        <div class="perfil-container">
            <div class="perfil-header">Perfil acadêmico (<?= htmlspecialchars($tipoLabel) ?>)</div>
            <div class="dados-topo">
                <div class="foto">
                    <span class="avatar-initials"><?= htmlspecialchars($iniciais) ?></span>
                </div>
                <div class="dados-pessoais">
                    <p><span>Nome:</span> <?= htmlspecialchars($usuario['Nome_Completo'] ?? 'Não informado') ?></p>
                    <p><span>Matrícula:</span> <?= htmlspecialchars($usuario['Matricula'] ?? 'Não informado') ?></p>
                    <p><span>CPF:</span> <?= htmlspecialchars($usuario['CPF'] ?? 'Não informado') ?></p>
                    <p><span>Telefone:</span> <?= htmlspecialchars($usuario['Telefone'] ?? 'Não informado') ?></p>
                    <p><span>Email:</span> <?= htmlspecialchars($usuario['Email'] ?? 'Não informado') ?></p>
                </div>
                <div class="dados-profissionais">
                    <?php if ($tipoUsuario === 'professor'): ?>
                        <p><strong>Titulação:</strong> <?= htmlspecialchars($usuario["Formacao"] ?? 'Não informado') ?></p>
                        <p><strong>Situação:</strong> <?= htmlspecialchars($situacaoProfessor ?? 'Não informado') ?></p>
                        <p><strong>Carga horária:</strong> <?= htmlspecialchars($usuario["Carga_Horaria"] ?? 'Não informado') ?></p>
                        <p><strong>Área de atuação:</strong> <?= htmlspecialchars($usuario["Area_Atuacao"] ?? 'Não informado') ?></p>
                        <p><strong>Ingresso:</strong> <?= htmlspecialchars($usuario["Data_Ingresso"] ?? 'Não informado') ?></p>
                    <?php else: ?>
                        <p><strong>Data de Nascimento:</strong> <?= htmlspecialchars($usuario['Data_Nascimento'] ?? 'Não informado') ?></p>
                        <p><strong>Sexo:</strong> <?= htmlspecialchars($usuario['Sexo'] ?? 'Não informado') ?></p>
                        <p><strong>Nacionalidade:</strong> <?= htmlspecialchars($usuario['Nacionalidade'] ?? 'Não informado') ?></p>
                        <p><strong>Naturalidade:</strong> <?= htmlspecialchars($usuario['Naturalidade'] ?? 'Não informado') ?></p>
                        <?php if (!empty($turma)): ?>
                        <p><strong>Data de Matrícula:</strong> <?= htmlspecialchars($turma['Data_Matricula'] ?? 'Não informado') ?></p>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($tipoUsuario === 'professor'): ?>
            <div class="atuacao">
                <h5 class="text-center mb-3">Atuação atual</h5>
                <div class="table-responsive">
                    <table class="table-mobile-responsive">
                        <thead>
                            <tr>
                                <th>Disciplina</th>
                                <th>Turma</th>
                                <th>Turno</th>
                                <th>Dias/horário</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($atuacoes)): ?>
                            <tr>
                                <td colspan="4" class="text-center">Nenhuma turma/disciplina vinculada.</td>
                            </tr>
                            <?php else: ?>
                                <?php foreach ($atuacoes as $a): ?>
                                <tr>
                                    <td data-label="Disciplina"><?= htmlspecialchars($a['Nome_Disciplina'] ?? 'Não especificada') ?></td>
                                    <td data-label="Turma"><?= htmlspecialchars($a['Nome_Turma'] ?? 'Não especificada') ?></td>
                                    <td data-label="Turno"><?= htmlspecialchars($a['Turno'] ?? '-') ?></td>
                                    <td data-label="Dias/horário">
                                        <?php 
                                        $dias = [1=>'Seg',2=>'Ter',3=>'Qua',4=>'Qui',5=>'Sex',6=>'Sáb',7=>'Dom'];
                                        $dia = $a['Dia_Semana'] ?? null;
                                        $ini = $a['Hora_Inicio'] ?? null;
                                        $fim = $a['Hora_Fim'] ?? null;
                                        if ($dia && $ini && $fim) {
                                            $diaLabel = $dias[$dia] ?? (string)$dia;
                                            echo htmlspecialchars($diaLabel) . ' - ' . htmlspecialchars($ini) . ' às ' . htmlspecialchars($fim);
                                        } else {
                                            echo 'Horário não definido';
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php else: ?>
                <div class="atuacao">
                    <h5 class="text-center mb-3">Turma atual</h5>
                    <?php if ($turma): ?>
                    <div class="table-responsive">
                        <table class="table-mobile-responsive">
                            <thead>
                                <tr>
                                    <th>Turma</th>
                                    <th>Etapa</th>
                                    <th>Turno</th>
                                    <th>Ano Letivo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td data-label="Turma"><?= htmlspecialchars($turma['Nome_Turma']) ?></td>
                                    <td data-label="Etapa"><?= htmlspecialchars($turma['Etapa']) ?></td>
                                    <td data-label="Turno"><?= htmlspecialchars($turma['Turno']) ?></td>
                                    <td data-label="Ano Letivo"><?= htmlspecialchars($turma['Ano_Letivo']) ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                        <p class="text-center">Nenhuma turma vinculada.</p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <div class="overlay toggle-menu"></div>

    <script src="../assets/js/jquery.min.js"></script>
    <script src="../assets/js/popper.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script src="../assets/plugins/simplebar/js/simplebar.js"></script>
    <script src="../assets/js/sidebar-menu.js"></script>
    <script src="../assets/js/app-script.js"></script>
</body>

</html>