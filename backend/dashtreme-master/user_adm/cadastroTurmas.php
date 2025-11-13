<?php
require_once '../includes/bootstrap.php';

$erro = '';
$sucesso = '';

require_once '../includes/conexao.php';
require_once '../includes/crud/TurmaCRUD.php';

$turmaCRUD = new TurmaCRUD($pdo);

// PROCESSAR FORMULÁRIO
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $dadosTurma = [
            'Nome_Turma' => $_POST['nomeTurma'],
            'Ano_Letivo' => $_POST['anoLetivo'],
            'Turno' => $_POST['turno'],
            'Etapa' => $_POST['etapaSerie'],
            'Capacidade_Alunos' => $_POST['capacidadeAlunos'],
            'Sala' => $_POST['salaLocal'] 
        ];

        // Verificar se é edição ou novo cadastro
        if (isset($_POST['id_turma']) && !empty($_POST['id_turma'])) {
            // Atualização
            $turmaCRUD->atualizar($_POST['id_turma'], $dadosTurma);
            $sucesso = "Turma atualizada com sucesso!";
        } else {
            // Novo cadastro
            $idTurma = $turmaCRUD->criarTurma($dadosTurma);
            $sucesso = "Turma cadastrada com sucesso!";
        }

        // Redirecionar para evitar reenvio do formulário
        header("Location: cadastroTurmas.php?sucesso=" . urlencode($sucesso));
        exit;

    } catch (Exception $e) {
        $erro = "Erro no cadastro: " . $e->getMessage();
        error_log("Erro cadastro turma: " . $e->getMessage());
    }
}

// Exibir mensagens passadas por URL
if (isset($_GET['sucesso'])) {
    $sucesso = $_GET['sucesso'];
}
if (isset($_GET['erro'])) {
    $erro = $_GET['erro'];
}

// Listar turmas para exibição
$turmas = $turmaCRUD->listarTurmasComProfessor();
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="Cadastro de Turmas - SAS (Sistema Academico Santos)" />
    <meta name="author" content="" />
    <title>Cadastro de Turmas - SAS (Sistema Academico Santos)</title>
    <!-- loader-->
    <link href="../assets/css/pace.min.css" rel="stylesheet" />
    <script src="../assets/js/pace.min.js"></script>
    <!--favicon-->
    <link rel="icon" href="../assets/images/favicon.ico" type="image/x-icon">
    <!-- simplebar CSS-->
    <link href="../assets/plugins/simplebar/css/simplebar.css" rel="stylesheet" />
    <!-- Bootstrap core CSS-->
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet" />
    <!-- animate CSS-->
    <link href="../assets/css/animate.css" rel="stylesheet" type="text/css" />
    <!-- Icons CSS-->
    <link href="../assets/css/icons.css" rel="stylesheet" type="text/css" />
    <!-- Sidebar CSS-->
    <link href="../assets/css/sidebar-menu.css" rel="stylesheet" />
    <!-- Custom Style-->
    <link href="../assets/css/app-style.css" rel="stylesheet" />
    <link rel="stylesheet" href="../css/style.css">
</head>

<body class="bg-theme bg-theme1 user_adm_cadastroTurmas">

    <?php require("menu_padrão.php"); ?>

    <div class="clearfix"></div>

    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="row pt-2 pb-2">
                <div class="col-sm-9">
                    <h4 class="page-title">Cadastro de Turmas</h4>
                </div>
            </div>

            <!-- Mensagens -->
            <?php if ($sucesso): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="zmdi zmdi-check-circle mr-2"></i> <?= htmlspecialchars($sucesso) ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <?php if ($erro): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="zmdi zmdi-close-circle mr-2"></i> <?= htmlspecialchars($erro) ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body">
                    <form id="formTurma" method="POST">
                        <input type="hidden" name="id_turma" id="id_turma" value="">

                        <!-- Dados da Turma -->
                        <div class="form-section">
                            <h5>Dados da Turma</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Nome da Turma</label>
                                        <input type="text" class="form-control" id="nomeTurma" name="nomeTurma"
                                            placeholder="Ex: 1º Ano A" required>
                                        <div class="invalid-feedback">Por favor, informe o nome da turma</div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Ano Letivo</label>
                                        <select class="form-control" id="anoLetivo" name="anoLetivo" required>
                                            <option value="">Selecione...</option>
                                            <option>2023</option>
                                            <option>2024</option>
                                            <option>2025</option>
                                            <option>2026</option>
                                        </select>
                                        <div class="invalid-feedback">Por favor, selecione o ano letivo</div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Turno</label>
                                        <select class="form-control" id="turno" name="turno" required>
                                            <option value="">Selecione...</option>
                                            <option>Matutino</option>
                                            <option>Vespertino</option>
                                            <option>Integral</option>
                                        </select>
                                        <div class="invalid-feedback">Por favor, selecione o turno</div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Etapa/Série</label>
                                        <select class="form-control" id="etapaSerie" name="etapaSerie" required>
                                            <option value="">Selecione...</option>
                                            <option>1º Ano</option>
                                            <option>2º Ano</option>
                                            <option>3º Ano</option>
                                            <option>4º Ano</option>
                                            <option>5º Ano</option>
                                            <option>6º Ano</option>
                                            <option>7º Ano</option>
                                            <option>8º Ano</option>
                                            <option>9º Ano</option>
                                        </select>
                                        <div class="invalid-feedback">Por favor, selecione a etapa/série</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Capacidade de Alunos</label>
                                        <input type="number" class="form-control" id="capacidadeAlunos"
                                            name="capacidadeAlunos" placeholder="Ex: 30" min="1" required>
                                        <div class="invalid-feedback" id="capacidadeError">Por favor, informe a
                                            capacidade</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Sala/Local</label>
                                        <input type="text" class="form-control" id="salaLocal" name="salaLocal"
                                            placeholder="Ex: Sala 101" required>
                                        <div class="invalid-feedback">Por favor, informe a sala/local</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botões -->
                        <div class="form-group row">
                            <div class="col-sm-12 text-right">
                                <button type="submit" class="btn btn-Salvar px-5"><i class="zmdi zmdi-save mr-1"></i>
                                    Salvar</button>
                                <button type="button" class="btn btn-cancelar px-5" id="btnCancelar">Cancelar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Listagem de Turmas -->
            <div class="card mt-4">
                <div class="card-body">
                    <h5 class="card-title">Turmas Cadastradas</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Nome da Turma</th>
                                    <th>Ano Letivo</th>
                                    <th>Turno</th>
                                    <th>Etapa</th> <!-- Mudei de Etapa/Série para Etapa -->
                                    <th>Capacidade</th>
                                    <th>Sala</th> <!-- Mudei de Sala para Sala -->
                                    <th>Professor</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($turmas as $turma): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($turma['Nome_Turma']) ?></td>
                                        <td><?= htmlspecialchars($turma['Ano_Letivo']) ?></td>
                                        <td><?= htmlspecialchars($turma['Turno']) ?></td>
                                        <td><?= htmlspecialchars($turma['Etapa']) ?></td> <!-- Mudei aqui -->
                                        <td><?= htmlspecialchars($turma['Capacidade_Alunos']) ?></td>
                                        <td><?= htmlspecialchars($turma['Sala']) ?></td> <!-- Mudei aqui -->
                                        <td><?= htmlspecialchars($turma['Professor_Nome'] ?? 'Não definido') ?></td>
                                        <td>
                                            <button type="button" class="btn btn-editar btn-sm"
                                                onclick="editarTurma(<?= $turma['ID_Turma'] ?>)">
                                                Editar
                                            </button>
                                            <button type="button" class="btn btn-excluir btn-sm"
                                                onclick="excluirTurma(<?= $turma['ID_Turma'] ?>, '<?= htmlspecialchars($turma['Nome_Turma']) ?>')">
                                                Excluir
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="overlay toggle-menu"></div>
            <a href="javaScript:void();" class="back-to-top"><i class="fa fa-angle-double-up"></i> </a>

            <!-- Bootstrap core JavaScript-->
            <script src="../assets/js/jquery.min.js"></script>
            <script src="../assets/js/popper.min.js"></script>
            <script src="../assets/js/bootstrap.min.js"></script>

            <!-- simplebar js -->
            <script src="../assets/plugins/simplebar/js/simplebar.js"></script>
            <!-- sidebar-menu js -->
            <script src="../assets/js/sidebar-menu.js"></script>
            <!-- loader scripts -->
            <script src="../assets/js/jquery.loading-indicator.js"></script>
            <!-- Custom scripts -->
            <script src="../assets/js/app-script.js"></script>

            <script>
                // Função para carregar dados da turma para edição
                function editarTurma(idTurma) {
                    fetch(`../includes/ajax/buscar_turma.php?id=${idTurma}`)
                        .then(response => response.json())
                        .then(turma => {
                            // Preencher formulário com dados da turma
                            document.getElementById('id_turma').value = turma.ID_Turma;
                            document.getElementById('nomeTurma').value = turma.Nome_Turma;
                            document.getElementById('anoLetivo').value = turma.Ano_Letivo;
                            document.getElementById('turno').value = turma.Turno;
                            document.getElementById('etapaSerie').value = turma.Etapa; // Mudei aqui
                            document.getElementById('capacidadeAlunos').value = turma.Capacidade_Alunos;
                            document.getElementById('salaLocal').value = turma.Sala; // Mudei aqui

                            // Rolando para o topo do formulário
                            window.scrollTo(0, 0);
                        })
                        .catch(error => {
                            console.error('Erro ao carregar turma:', error);
                            alert('Erro ao carregar dados da turma');
                        });
                }
                // Função para excluir turma
                function excluirTurma(idTurma, nomeTurma) {
                    if (confirm(`Deseja realmente excluir a turma "${nomeTurma}"?`)) {
                        fetch(`../includes/ajax/excluir_turma.php`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: `id_turma=${idTurma}`
                        })
                            .then(response => response.json())
                            .then(result => {
                                if (result.success) {
                                    alert('Turma excluída com sucesso!');
                                    location.reload();
                                } else {
                                    alert('Erro ao excluir turma: ' + result.message);
                                }
                            })
                            .catch(error => {
                                console.error('Erro:', error);
                                alert('Erro ao excluir turma');
                            });
                    }
                }

                $(document).ready(function () {
                    // Função para validar todos os campos
                    function validarFormulario() {
                        let valido = true;
                        const campos = ['nomeTurma', 'anoLetivo', 'turno', 'etapaSerie', 'capacidadeAlunos', 'salaLocal'];

                        // Resetar validações
                        $('.is-invalid').removeClass('is-invalid');
                        $('.invalid-feedback').hide();

                        // Validar cada campo
                        campos.forEach(function (campo) {
                            const elemento = $('#' + campo);
                            const valor = elemento.val();

                            if (campo === 'capacidadeAlunos') {
                                // Validação especial para capacidade
                                if (!valor || valor < 1) {
                                    elemento.addClass('is-invalid');
                                    $('#capacidadeError').text(
                                        !valor ? 'Por favor, informe a capacidade' :
                                            'A capacidade deve ser maior que zero'
                                    ).show();
                                    valido = false;
                                }
                            } else {
                                // Validação padrão para outros campos
                                if (!valor) {
                                    elemento.addClass('is-invalid');
                                    elemento.next('.invalid-feedback').show();
                                    valido = false;
                                }
                            }
                        });

                        return valido;
                    }

                    // Ao tentar salvar
                    $('#formTurma').on('submit', function (e) {
                        e.preventDefault();

                        // Validar formulário
                        if (validarFormulario()) {
                            // Formulário válido - enviar
                            this.submit();
                        } else {
                            // Rolando para o primeiro erro
                            $('html, body').animate({
                                scrollTop: $('.is-invalid').first().offset().top - 100
                            }, 500);
                        }
                    });

                    // Limpar erros quando o usuário começar a digitar/selecionar
                    $('input, select').on('input change', function () {
                        $(this).removeClass('is-invalid');
                        $(this).next('.invalid-feedback').hide();
                    });

                    // Botão cancelar - limpar formulário
                    $('#btnCancelar').click(function () {
                        if (confirm('Deseja realmente cancelar? Todas as alterações não salvas serão perdidas.')) {
                            document.getElementById('formTurma').reset();
                            document.getElementById('id_turma').value = '';
                        }
                    });
                });
            </script>
</body>

</html>