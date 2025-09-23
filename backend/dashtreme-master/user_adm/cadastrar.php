<?php require_once '../includes/bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Disciplinas - Dashboard Acadêmico</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/app-style.css">
    <link rel="stylesheet" href="../assets/css/icons.css">
    <link rel="stylesheet" href="../assets/css/sidebar-menu.css">
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            background: linear-gradient(to right, #2c3e50, #3498db);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #ecf0f1;
        }

        .form-container {
            background: rgba(255, 255, 255, 0.05);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            max-width: 600px;
            margin: 40px auto;
        }

        .form-group label {
            color: #71affe;
            font-weight: 600;
        }

        .form-control {
            background-color: rgba(255, 255, 255, 0.15);
            border: 1px solid #71affe;
            color: #fff;
            border-radius: 6px;
        }

        .form-control:focus {
            background-color: rgba(255, 255, 255, 0.2);
            color: #fff;
            border-color: #1abc9c;
            box-shadow: 0 0 0 0.2rem rgba(26, 188, 156, 0.25);
        }

        .btn-salvar {
            background-color: #1abc9c;
            color: white;
            border: none;
            border-radius: 6px;
            padding: 10px 20px;
        }

        .btn-salvar:hover {
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

        .btn-voltar {
            background-color: #7f8c8d;
            color: white;
            border: none;
            border-radius: 6px;
            padding: 10px 20px;
        }

        .btn-voltar:hover {
            background-color: #616a6b;
        }

        .section-title {
            color: #71affe;
            border-bottom: 2px solid #71affe;
            margin-bottom: 20px;
            padding-bottom: 10px;
        }

        input::placeholder,
        select,
        textarea {
            color: #ecf0f1;
        }

        option {
            color: #e4dfdf;
        }

        select.form-control option {
            background-color: rgba(45, 65, 91, 0.9);
            color: #ecf0f1;
        }

        .form-section {
            margin-bottom: 20px;
        }

        .bold-title {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .btn-group {
            display: flex;
            gap: 10px;
            margin-top: 20px;
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

    <!-- Conteúdo principal -->
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card" style="background-color: transparent; border: none; box-shadow: none;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h4 class="page-title"><i class="zmdi zmdi-plus-circle mr-2"></i> Cadastrar
                                    Disciplina</h4>
                            </div>

                            <!-- Formulário de cadastro de disciplina -->
                            <div class="form-container">
                                <form id="form-disciplina">
                                    <!-- Seção Dados da Disciplina -->
                                    <div class="form-section">
                                        <h5 class="section-title">DADOS DA DISCIPLINA</h5>
                                        <div class="form-group">
                                            <div class="bold-title">Disciplina</div>
                                            <input type="text" id="nome-disciplina" class="form-control"
                                                placeholder="Nome da disciplina">
                                        </div>
                                        <div class="form-group">
                                            <div class="bold-title">Carga horária</div>
                                            <input type="number" id="carga-horaria" class="form-control"
                                                placeholder="Horas totais">
                                        </div>
                                        <div class="form-group">
                                            <div class="bold-title">Professor</div>
                                            <select id="professor" class="form-control">
                                                <option value="">Selecione o professor</option>
                                                <option value="1">Prof. João Silva</option>
                                                <option value="2">Prof. Maria Souza</option>
                                                <option value="3">Prof. Carlos Oliveira</option>
                                                <option value="4">Prof. Ana Pereira</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <div class="bold-title">Etapa/série</div>
                                            <select id="etapa-serie" class="form-control">
                                                <option value="">Selecione a etapa/série</option>
                                                <option value="1">1º Ano</option>
                                                <option value="2">2º Ano</option>
                                                <option value="3">3º Ano</option>
                                                <option value="4">4º Ano</option>
                                                <option value="5">5º Ano</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Botões -->
                                    <div class="btn-group">
                                        <button type="submit" class="btn-salvar" id="btn-salvar">Salvar</button>
                                        <button type="button" class="btn-cancelar" id="btn-cancelar">Cancelar</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="overlay toggle-menu"></div>
    </div>

    <!-- Scripts -->
    <script src="../assets/js/jquery.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script src="../assets/plugins/simplebar/js/simplebar.js"></script>
    <script src="../assets/js/sidebar-menu.js"></script>
    <script src="../assets/js/app-script.js"></script>

    <script>
        $(document).ready(function () {
            // Validação e envio do formulário
            $('#form-disciplina').submit(function (e) {
                e.preventDefault();

                if (validarFormulario()) {
                    const disciplina = {
                        nome: $('#nome-disciplina').val(),
                        cargaHoraria: $('#carga-horaria').val(),
                        professor: $('#professor option:selected').text(),
                        professorId: $('#professor').val(),
                        etapaSerie: $('#etapa-serie option:selected').text(),
                        etapaSerieId: $('#etapa-serie').val()
                    };

                    console.log('Dados da disciplina:', disciplina);
                    alert('Disciplina cadastrada com sucesso!');
                    limparFormulario();
                }
            });

            // Botão Cancelar
            $('#btn-cancelar').click(function () {
                if (confirm('Deseja realmente cancelar? Todos os dados não salvos serão perdidos.')) {
                    limparFormulario();
                }
            });

            // Validação do formulário
            function validarFormulario() {
                if ($('#nome-disciplina').val() === '') {
                    alert('Por favor, informe o nome da disciplina');
                    return false;
                }
                if ($('#carga-horaria').val() === '' || $('#carga-horaria').val() <= 0) {
                    alert('Por favor, informe uma carga horária válida');
                    return false;
                }
                if ($('#professor').val() === '') {
                    alert('Por favor, selecione o professor');
                    return false;
                }
                if ($('#etapa-serie').val() === '') {
                    alert('Por favor, selecione a etapa/série');
                    return false;
                }
                return true;
            }

            // Limpar formulário
            function limparFormulario() {
                $('#nome-disciplina').val('');
                $('#carga-horaria').val('');
                $('#professor').val('');
                $('#etapa-serie').val('');
            }
        });
    </script>

</body>

</html>