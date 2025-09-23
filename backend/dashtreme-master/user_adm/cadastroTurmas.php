<?php require_once '../includes/bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="Cadastro de Turmas - Dashboard Acadêmico" />
    <meta name="author" content="" />
    <title>Cadastro de Turmas - Dashboard Acadêmico</title>
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
    <link rel="stylesheet" href="style.css">

    <style>
        html, body {
            height: 100%;
            min-height: 100%;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
        }
        body {
            flex: 1 0 auto;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .content-wrapper {
            flex: 1 0 auto;
        }
        .footer {
            flex-shrink: 0;
            background: transparent;
            color: #fff;
            border: none;
            text-align: center;
            padding: 15px 0 10px 0;
        }
        .form-section {
            margin-bottom: 30px;
            border-bottom: 1px solid #eee;
            padding-bottom: 20px;
        }

        .form-section h5 {
            color: #71affa;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .is-invalid {
            border-color: #dc3545 !important;
        }

        .invalid-feedback {
            display: none;
            width: 100%;
            margin-top: 0.25rem;
            font-size: 80%;
            color: #dc3545;
        }

        .alert-success {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
            padding: 0.75rem 1.25rem;
            margin-bottom: 1rem;
            border: 1px solid transparent;
            border-radius: 0.25rem;
            display: none;
        }

        .btn-Salvar {
            background-color: #1abc9c;
            color: white;
            border: none;
            border-radius: 6px;
            padding: 10px 20px;
        }

        .btn-Salvar:hover {
            background-color: #16a085;
        }

        .btn-cancelar {
            background-color: #e74c3c;
            color: white;
            border: none;
            border-radius: 6px;
            padding: 10px 20px;
        }

        .btn-cancelar:hover {
            background-color: #c0392b;
        }

        .navbar {
            background-color: rgba(0, 0, 0, 0.2) !important;
            backdrop-filter: blur(10px);
        }
    </style>
</head>

<body class="bg-theme bg-theme1">

    <?php
    require("menu_padrão.php");
    ?>

    <div class="clearfix"></div>

    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="row pt-2 pb-2">
                <div class="col-sm-9">
                    <h4 class="page-title">Cadastro de Turmas</h4>
                </div>
            </div>

            <!-- Mensagem de sucesso -->
            <div class="alert-success" id="successMessage">
                <i class="zmdi zmdi-check-circle mr-2"></i> Turma cadastrada com sucesso!
            </div>

            <div class="card">
                <div class="card-body">
                    <form id="formTurma">
                        <!-- Dados da Turma -->
                        <div class="form-section">
                            <h5>Dados da Turma</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Nome da Turma</label>
                                        <input type="text" class="form-control" id="nomeTurma"
                                            placeholder="Ex: 1º Ano A" required>
                                        <div class="invalid-feedback">Por favor, informe o nome da turma</div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Ano Letivo</label>
                                        <select class="form-control" id="anoLetivo" required>
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
                                        <select class="form-control" id="turno" required>
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
                                        <select class="form-control" id="etapaSerie" required>
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
                                            placeholder="Ex: 30" min="1" required>
                                        <div class="invalid-feedback" id="capacidadeError">Por favor, informe a
                                            capacidade</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Sala/Local</label>
                                        <input type="text" class="form-control" id="salaLocal"
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
        </div>
    </div>

    <div class="overlay toggle-menu"></div>

    <a href="javaScript:void();" class="back-to-top"><i class="fa fa-angle-double-up"></i> </a>


    </div>

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

                // Esconder mensagem de sucesso anterior
                $('#successMessage').hide();

                // Validar formulário
                if (validarFormulario()) {
                    // Formulário válido - mostrar mensagem de sucesso
                    $('#successMessage').fadeIn();

                    // Simular salvamento (apenas frontend)
                    console.log('Dados da turma:', {
                        nome: $('#nomeTurma').val(),
                        ano: $('#anoLetivo').val(),
                        turno: $('#turno').val(),
                        etapa: $('#etapaSerie').val(),
                        capacidade: $('#capacidadeAlunos').val(),
                        sala: $('#salaLocal').val()
                    });

                    // Rolando a página para mostrar a mensagem
                    $('html, body').animate({
                        scrollTop: 0
                    }, 500);

                    // Limpar formulário após 3 segundos (opcional)
                    setTimeout(function () {
                        $('#formTurma')[0].reset();
                        $('#successMessage').fadeOut();
                    }, 3000);
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

            // Botão cancelar
            $('#btnCancelar').click(function () {
                if (confirm('Deseja realmente cancelar? Todas as alterações serão perdidas.')) {
                    window.location.href = 'cadastroTurmas.html';
                }
            });
        });
    </script>
</body>

</html>