<?php 
require_once '../includes/bootstrap.php';

// Verificar se os parâmetros foram passados
$tipo = $_GET['tipo'] ?? '';
$id = $_GET['id'] ?? '';

require_once '../includes/conexao.php';
require_once '../includes/crud/UsuarioCRUD.php';
require_once '../includes/crud/TurmaCRUD.php';
require_once '../includes/crud/VinculoCRUD.php';

$usuarioCRUD = new UsuarioCRUD($pdo);
$turmaCRUD = new TurmaCRUD($pdo);
$vinculoCRUD = new VinculoCRUD($pdo);

// Buscar turmas ativas (sempre necessário)
$turmas = $turmaCRUD->listarTodas();

// Buscar lista de alunos e servidores para seleção
$alunos = $usuarioCRUD->listarAlunos(1, 100); // Limite de 100 alunos
$servidores = $usuarioCRUD->listarProfessores(1, 100); // Limite de 100 servidores

// Inicializar variáveis
$usuario = null;
$nome = '';
$matricula = '';
$situacao = '';
$vinculos = [];
$historicoVinculos = [];
$modoSelecao = empty($tipo) || empty($id);

// Se tem parâmetros, buscar dados do usuário específico
if (!$modoSelecao) {
    // Buscar dados do usuário
    if ($tipo === 'aluno') {
        $usuario = $usuarioCRUD->buscarAlunoCompleto($id);
        $nome = $usuario['Nome_Completo'] ?? '';
        $matricula = $usuario['Matricula'] ?? '';
    } else {
    $usuario = $usuarioCRUD->buscarProfessorCompleto($id);
    $nome = $usuario['Nome_Completo'] ?? '';
    // Servidores: usar matrícula de professor quando existir; fallback para Registro/Login
    $matricula = $usuario['Matricula'] ?? ($usuario['Registro'] ?? ($usuario['Login'] ?? ''));
    }

    // Buscar situação atual
    $situacao = $vinculoCRUD->verificarSituacao($tipo, $id);

    // Buscar vínculos atuais
    if ($tipo === 'aluno') {
        $vinculos = $vinculoCRUD->listarVinculosAluno($id);
        // Buscar histórico de vínculos (matrículas inativas)
        if (method_exists($vinculoCRUD, 'listarHistoricoVinculosAluno')) {
            $historicoVinculos = $vinculoCRUD->listarHistoricoVinculosAluno($id);
        }
    } else {
        $vinculos = $vinculoCRUD->listarVinculosProfessor($id);
    }
}

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Vínculos - SAS (Sistema Academico Santos)</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/app-style.css">
    <link rel="stylesheet" href="../assets/css/icons.css">
    <link rel="stylesheet" href="../assets/plugins/simplebar/css/simplebar.css">
    <link rel="stylesheet" href="../assets/css/sidebar-menu.css">
    <link rel="stylesheet" href="../css/style.css">
</head>

<body class="bg-theme bg-theme1 user_adm_gerenciarVinculos">
    <?php require("menu_padrão.php"); ?>

    <div class="clearfix"></div>

    <!-- Conteúdo principal -->
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="row pt-2 pb-2">
                <div class="col-sm-12">
                    <h4 class="page-title">Gerenciar Vínculos</h4>
                    <?php if ($modoSelecao): ?>
                        <p class="text-muted">Selecione um usuário para gerenciar seus vínculos com turmas</p>
                    <?php else: ?>
                        <p class="text-muted">Gerenciando vínculos do <?= $tipo ?>: <strong><?= htmlspecialchars($nome) ?></strong></p>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($modoSelecao): ?>
                <!-- MODO SELEÇÃO: Quando não há usuário específico selecionado -->
                <div class="row">
                    <!-- Seleção de Alunos -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h5 class="card-title mb-0">
                                    <i class="zmdi zmdi-accounts-alt mr-2"></i>Selecionar Aluno
                                </h5>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($alunos)): ?>
                                    <div class="list-group">
                                        <?php foreach ($alunos as $aluno): ?>
                                            <a href="gerenciarVinculos.php?tipo=aluno&id=<?= $aluno['ID_Usuario'] ?>" 
                                               class="list-group-item list-group-item-action usuario-card">
                                                <div class="d-flex w-100 justify-content-between">
                                                    <h6 class="mb-1"><?= htmlspecialchars($aluno['Nome_Completo']) ?></h6>
                                                    <small>ID: <?= $aluno['ID_Usuario'] ?></small>
                                                </div>
                                                <p class="mb-1">
                                                    <small class="text-muted">
                                                        Matrícula: <?= htmlspecialchars($aluno['Matricula'] ?? 'N/A') ?> | 
                                                        Email: <?= htmlspecialchars($aluno['Email']) ?>
                                                    </small>
                                                </p>
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-info">
                                        <i class="zmdi zmdi-info-outline mr-2"></i>
                                        Nenhum aluno cadastrado.
                                        <a href="cadastro.php" class="alert-link">Cadastrar aluno</a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Seleção de Servidores -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-info text-white">
                                <h5 class="card-title mb-0">
                                    <i class="zmdi zmdi-account-box mr-2"></i>Selecionar Servidor
                                </h5>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($servidores)): ?>
                                    <div class="list-group">
                                        <?php foreach ($servidores as $servidor): ?>
                                            <a href="gerenciarVinculos.php?tipo=servidor&id=<?= $servidor['ID_Usuario'] ?>" 
                                               class="list-group-item list-group-item-action usuario-card">
                                                <div class="d-flex w-100 justify-content-between">
                                                    <h6 class="mb-1"><?= htmlspecialchars($servidor['Nome_Completo']) ?></h6>
                                                    <small>ID: <?= $servidor['ID_Usuario'] ?></small>
                                                </div>
                                                <p class="mb-1">
                                                    <small class="text-muted">
                                                        Formação: <?= htmlspecialchars($servidor['Formacao_Academica'] ?? 'N/A') ?> | 
                                                        Email: <?= htmlspecialchars($servidor['Email']) ?>
                                                    </small>
                                                </p>
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-info">
                                        <i class="zmdi zmdi-info-outline mr-2"></i>
                                        Nenhum servidor cadastrado.
                                        <a href="cadastro.php" class="alert-link">Cadastrar servidor</a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ações gerais -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body text-center">
                                <h5>Ações Rápidas</h5>
                                <a href="cadastro.php" class="btn btn-custom-primary mr-3">
                                    <i class="zmdi zmdi-account-add mr-2"></i>Cadastrar Novo Usuário
                                </a>
                                <a href="trocarTurma.php" class="btn btn-custom-secondary">
                                    <i class="zmdi zmdi-plus mr-2"></i>Trocar Turma
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            <?php else: ?>
                <!-- MODO GERENCIAMENTO: Quando há um usuário específico selecionado -->

                <!-- Dados do usuário -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Dados do <?= $tipo ?></h5>
                            <a href="gerenciarVinculos.php" class="btn btn-custom-info btn-sm">
                                <i class="zmdi zmdi-arrow-back mr-1"></i>Voltar para Seleção
                            </a>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <p><span class="info-label">Nome:</span> <span id="nomeUsuario"><?= htmlspecialchars($nome) ?></span></p>
                                <?php if (!empty($matricula)): ?>
                                    <p><span class="info-label">Matrícula:</span> <span id="matriculaUsuario"><?= htmlspecialchars($matricula) ?></span></p>
                                <?php endif; ?>
                                <p><span class="info-label">Situação:</span> <span id="situacaoAtual"><?= htmlspecialchars($situacao) ?></span></p>
                            </div>
                            <div class="col-md-6 text-right">
                                <a href="cadastro.php?editar<?= ucfirst($tipo) ?>=<?= $id ?>" class="btn btn-custom-secondary btn-sm">
                                    <i class="zmdi zmdi-edit mr-1"></i>Editar Cadastro
                                </a>
                            </div>
                        </div>
                        <input type="hidden" id="tipoUsuario" value="<?= $tipo ?>">
                        <input type="hidden" id="idUsuario" value="<?= $id ?>">
                    </div>
                </div>

                <!-- Vínculos atuais -->
                <?php if (!empty($vinculos)): ?>
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Vínculos Atuais</h5>
                        <?php foreach ($vinculos as $vinculo): ?>
                            <div class="vinculo-item">
                                <p class="mb-1">
                                    <strong><?= htmlspecialchars($vinculo['Nome_Turma']) ?></strong> - 
                                    <?= htmlspecialchars($vinculo['Ano_Letivo']) ?> - 
                                    <?= htmlspecialchars($vinculo['Turno']) ?>
                                </p>
                                <?php if ($tipo === 'aluno'): ?>
                                    <button class="btn btn-custom-danger btn-sm" 
                                            onclick="removerVinculo(<?= $vinculo['ID_Matricula'] ?>)">
                                        Remover Vínculo
                                    </button>
                                <?php else: ?>
                                    <button class="btn btn-custom-danger btn-sm" 
                                            onclick="removerVinculoProfessor(<?= $id ?>, <?= $vinculo['ID_Turma'] ?>)">
                                        Remover Vínculo
                                    </button>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ($tipo === 'aluno' && !empty($historicoVinculos)): ?>
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Histórico de Vínculos</h5>
                        <?php foreach ($historicoVinculos as $v): ?>
                            <div class="vinculo-item" style="border-left-color:#6c757d;">
                                <p class="mb-1">
                                    <strong><?= htmlspecialchars($v['Nome_Turma']) ?></strong> - 
                                    <?= htmlspecialchars($v['Ano_Letivo']) ?> - 
                                    <?= htmlspecialchars($v['Turno']) ?>
                                </p>
                                <small class="text-muted">
                                    Ingresso: <?= htmlspecialchars($v['Data_Matricula']) ?>
                                    <?php if (!empty($v['Data_Saida'])): ?>
                                        | Saída: <?= htmlspecialchars($v['Data_Saida']) ?>
                                    <?php endif; ?>
                                </small>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Novo vínculo -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><?= empty($vinculos) ? 'Vincular a turma' : 'Adicionar novo vínculo' ?></h5>
                        <form id="formVinculo">
                            <div class="form-group">
                                <label for="turma" class="text-white">Selecionar turma:</label>
                                <select class="form-control" id="turma" name="turma_id" required>
                                    <option value="">Selecione a turma...</option>
                                    <?php foreach ($turmas as $turma): ?>
                                        <option value="<?= $turma['ID_Turma'] ?>">
                                            <?= htmlspecialchars($turma['Nome_Turma']) ?> - 
                                            <?= htmlspecialchars($turma['Ano_Letivo']) ?> - 
                                            <?= htmlspecialchars($turma['Turno']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group text-right">
                                <button type="button" class="btn btn-custom-secondary" onclick="window.location.href='cadastroTurmas.php'">
                                    <i class="zmdi zmdi-plus mr-1"></i> Nova Turma
                                </button>
                                <button type="submit" class="btn btn-custom-primary">
                                    <i class="zmdi zmdi-link mr-1"></i> <?= empty($vinculos) ? 'Vincular' : 'Adicionar Vínculo' ?>
                                </button>
                            </div>
                        </form>

                        <div class="alert alert-success mt-3 d-none" id="successMessage">
                            Vínculo realizado com sucesso!
                        </div>
                        <div class="alert alert-danger mt-3 d-none" id="errorMessage"></div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="../assets/js/jquery.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script src="../assets/plugins/simplebar/js/simplebar.js"></script>
    <script src="../assets/js/sidebar-menu.js"></script>
    <script src="../assets/js/app-script.js"></script>

    <!-- Modal de confirmação de vínculo (somente aluno) -->
    <div class="modal fade" id="confirmVinculoModal" tabindex="-1" role="dialog" aria-labelledby="confirmVinculoLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmVinculoLabel">Confirmar novo vínculo</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Ao vincular este aluno à turma abaixo, qualquer matrícula ativa anterior será encerrada automaticamente.</p>
                    <div class="p-2" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.15); border-radius: 8px;">
                        <strong>Nova turma:</strong>
                        <div id="turmaResumo" class="mt-1"></div>
                    </div>
                    <small class="text-muted d-block mt-2">A matrícula anterior receberá Status "Inativa" e Data de Saída (quando disponível).</small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="confirmVinculoBtn">Confirmar vínculo</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            function enviarVinculo() {
                const turmaId = $('#turma').val();
                const tipoUsuario = $('#tipoUsuario').val();
                const idUsuario = $('#idUsuario').val();

                $.ajax({
                    url: '../includes/ajax/vincular_usuario_turma.php',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        tipo: tipoUsuario,
                        usuario_id: idUsuario,
                        turma_id: turmaId
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#confirmVinculoModal').modal('hide');
                            $('#successMessage').removeClass('d-none').fadeIn();
                            $('#errorMessage').addClass('d-none');
                            setTimeout(() => { location.reload(); }, 1500);
                        } else {
                            $('#errorMessage').text(response.message).removeClass('d-none');
                            $('#successMessage').addClass('d-none');
                            $('#confirmVinculoModal').modal('hide');
                        }
                    },
                    error: function() {
                        $('#errorMessage').text('Erro ao conectar com o servidor.').removeClass('d-none');
                        $('#successMessage').addClass('d-none');
                        $('#confirmVinculoModal').modal('hide');
                    }
                });
            }

            // Submissão do formulário de vínculo
            $('#formVinculo').on('submit', function (e) {
                e.preventDefault();

                const turmaId = $('#turma').val();
                const tipoUsuario = $('#tipoUsuario').val();

                if (!turmaId) {
                    alert('Por favor, selecione uma turma para vincular.');
                    return;
                }

                if (tipoUsuario === 'aluno') {
                    const turmaTexto = $('#turma option:selected').text();
                    $('#turmaResumo').text(turmaTexto || '—');
                    $('#confirmVinculoModal').modal('show');
                } else {
                    // Professores seguem fluxo direto
                    enviarVinculo();
                }
            });

            $('#confirmVinculoBtn').on('click', function(){
                enviarVinculo();
            });
        });

        // Função para remover vínculo de aluno
        function removerVinculo(idMatricula) {
            if (confirm('Deseja realmente remover este vínculo?')) {
                $.ajax({
                    url: '../includes/ajax/remover_vinculo.php',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        tipo: 'aluno',
                        id_matricula: idMatricula
                    },
                    success: function(response) {
                        if (response.success) {
                            alert('Vínculo removido com sucesso!');
                            location.reload();
                        } else {
                            alert('Erro ao remover vínculo: ' + response.message);
                        }
                    },
                    error: function() {
                        alert('Erro ao conectar com o servidor.');
                    }
                });
            }
        }

        // Função para remover vínculo de professor
        function removerVinculoProfessor(idProfessor, idTurma) {
            if (confirm('Deseja realmente remover este vínculo?')) {
                $.ajax({
                    url: '../includes/ajax/remover_vinculo.php',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        tipo: 'professor',
                        id_professor: idProfessor,
                        id_turma: idTurma
                    },
                    success: function(response) {
                        if (response.success) {
                            alert('Vínculo removido com sucesso!');
                            location.reload();
                        } else {
                            alert('Erro ao remover vínculo: ' + response.message);
                        }
                    },
                    error: function() {
                        alert('Erro ao conectar com o servidor.');
                    }
                });
            }
        }
    </script>

</body>

</html>