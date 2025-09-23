<?php require_once '../includes/bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transferências - Dashboard Acadêmico</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/app-style.css">
    <link rel="stylesheet" href="../assets/css/icons.css">
    <link rel="stylesheet" href="../assets/css/sidebar-menu.css">
   
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
            max-width: 800px;
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

        .student-card {
            background-color: rgba(255, 255, 255, 0.07);
            border: 1px solid #71affe;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.2s;
        }

        .student-card:hover {
            background-color: rgba(113, 175, 250, 0.2);
            transform: scale(1.02);
        }

        .student-info {
            font-size: 1.2rem;
            font-weight: bold;
            color: #71affe;
        }

        .student-details {
            font-size: 0.95rem;
            color: #bdc3c7;
        }

        #selected-student {
            background-color: rgba(255, 255, 255, 0.05);
            border-left: 4px solid #71affe;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            color: #fff;
        }

        .btn-confirmar {
            background-color: #1abc9c;
            color: white;
            border: none;
            border-radius: 6px;
            padding: 10px 20px;
        }

        .btn-confirmar:hover {
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

        #search-aluno::placeholder {
            color: #999;
        }

        #btn-pesquisar i {
            margin-right: 5px;
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
                                <h4 class="page-title"><i class="zmdi zmdi-account-add mr-2"></i> Transferências
                                </h4>
                            </div>

                            <!-- Container de busca do aluno -->
                            <div class="form-group">
                                <label for="search-aluno" class="text-white font-weight-bold">Buscar aluno</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="search-aluno"
                                        placeholder="Digite nome ou matrícula...">
                                    <div class="input-group-append">
                                        <button class="btn btn-custom-primary" type="button" id="btn-pesquisar">
                                            <i class="zmdi zmdi-search"></i> Pesquisar
                                        </button>
                                    </div>
                                </div>
                            </div>


                            <!-- Resultados da pesquisa -->
                            <div id="search-results" class="search-results"></div>

                            <!-- Aluno selecionado -->
                            <div id="selected-student" class="selected-student" style="display: none;">
                                <div class="student-info" id="selected-student-name"></div>
                                <div class="student-details">
                                    Matrícula: <span id="selected-student-matricula"></span> |
                                    Turma: <span id="selected-student-turma"></span> |
                                    Turno: <span id="selected-student-turno"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Formulário de transferência -->
                        <div class="transferencia-container">
                            <div class="form-section">
                                <div class="bold-title">Selecione a turma</div>
                                <select id="turma-aluno" class="form-control">
                                    <option value="">Selecione a turma</option>
                                    <option value="1A">1º Ano A</option>
                                    <option value="1B">1º Ano B</option>
                                    <option value="2A">2º Ano A</option>
                                    <option value="2B">2º Ano B</option>
                                    <option value="3A">3º Ano A</option>
                                    <option value="3B">3º Ano B</option>
                                </select>
                            </div>

                            <div class="form-section">
                                <div class="bold-title">Selecione o turno</div>
                                <select id="turno-aluno" class="form-control">
                                    <option value="">Selecione o turno</option>
                                    <option value="manha">Manhã</option>
                                    <option value="tarde">Tarde</option>
                                    <option value="noite">Noite</option>
                                </select>
                            </div>

                            <div class="form-section data-field">
                                <div class="bold-title">DATA DA TRANSFERÊNCIA</div>
                                <input type="date" id="data-transferencia" class="form-control" value="2025-07-14">
                            </div>

                            <div class="form-section">
                                <div class="bold-title">NOME DA ESCOLA DE DESTINO</div>
                                <input type="text" id="escola-destino" class="form-control" placeholder="">
                            </div>

                            <div class="form-section">
                                <div class="bold-title">MUNICÍPIO/UF</div>
                                <input type="text" id="municipio-uf" class="form-control" placeholder="">
                            </div>

                            <div class="btn-group">
                                <button class="btn-confirmar" id="btn-confirmar">Confirmar</button>
                                <button class="btn-cancelar" id="btn-cancelar">Cancelar</button>
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
            let alunoSelecionado = null;

            // Configura a data atual como padrão
            const hoje = new Date();
            const dataFormatada = hoje.toISOString().substr(0, 10);
            $('#data-transferencia').val(dataFormatada);

            const turmas = ['1A', '1B', '2A', '2B', '3A', '3B'];
            const turnos = ['manha', 'tarde', 'noite'];

            // Botão Pesquisar
            $('#btn-pesquisar').click(function () {
                const termo = $('#search-aluno').val().trim();

                if (termo.length < 3) {
                    alert('Digite pelo menos 3 caracteres para pesquisar');
                    return;
                }

                const resultsContainer = $('#search-results');
                resultsContainer.empty();

                // Simula um aluno com os dados digitados
                const alunoSimulado = {
                    id: Date.now(),
                    nome: termo.charAt(0).toUpperCase() + termo.slice(1),
                    matricula: '2025' + Math.floor(Math.random() * 9000 + 1000),
                    turma: turmas[Math.floor(Math.random() * turmas.length)],
                    turno: turnos[Math.floor(Math.random() * turnos.length)]
                };

                resultsContainer.append(`
                <div class="student-card" data-id="${alunoSimulado.id}" data-nome="${alunoSimulado.nome}" data-matricula="${alunoSimulado.matricula}" data-turma="${alunoSimulado.turma}" data-turno="${alunoSimulado.turno}">
                    <div class="student-info">${alunoSimulado.nome}</div>
                    <div class="student-details">
                        Matrícula: ${alunoSimulado.matricula} | Turma: ${alunoSimulado.turma} | Turno: ${alunoSimulado.turno}
                    </div>
                </div>
            `);

                resultsContainer.show();
            });

            // Selecionar aluno
            $(document).on('click', '.student-card', function () {
                alunoSelecionado = {
                    id: $(this).data('id'),
                    nome: $(this).data('nome'),
                    matricula: $(this).data('matricula'),
                    turma: $(this).data('turma'),
                    turno: $(this).data('turno')
                };

                $('#selected-student-name').text(alunoSelecionado.nome);
                $('#selected-student-matricula').text(alunoSelecionado.matricula);
                $('#selected-student-turma').text(alunoSelecionado.turma);
                $('#selected-student-turno').text(alunoSelecionado.turno);

                $('#selected-student').show();
                $('#search-results').hide();

                $('#turma-aluno').val(alunoSelecionado.turma);
                $('#turno-aluno').val(alunoSelecionado.turno);
            });

            // Botão Voltar
            $('#btn-voltar').click(function () {
                window.history.back();
            });

            // Botão Cancelar
            $('#btn-cancelar').click(function () {
                if (confirm('Deseja realmente cancelar a operação? Todos os dados não salvos serão perdidos.')) {
                    limparFormulario();
                }
            });

            // Botão Confirmar
            $('#btn-confirmar').click(function () {
                if (!alunoSelecionado) {
                    alert('Por favor, selecione um aluno primeiro');
                    return;
                }

                if (!validarFormulario()) {
                    return;
                }

                const dadosTransferencia = {
                    aluno: alunoSelecionado,
                    dataTransferencia: $('#data-transferencia').val(),
                    escolaDestino: $('#escola-destino').val(),
                    municipioUF: $('#municipio-uf').val()
                };

                console.log('Dados para transferência:', dadosTransferencia);
                alert('Transferência registrada com sucesso!');
                limparFormulario();
            });

            // Validação do formulário
            function validarFormulario() {
                if ($('#turma-aluno').val() === '') {
                    alert('Por favor, selecione a turma do aluno');
                    return false;
                }
                if ($('#turno-aluno').val() === '') {
                    alert('Por favor, selecione o turno do aluno');
                    return false;
                }
                if ($('#escola-destino').val() === '') {
                    alert('Por favor, informe a escola de destino');
                    return false;
                }
                if ($('#municipio-uf').val() === '') {
                    alert('Por favor, informe o município/UF da escola de destino');
                    return false;
                }
                return true;
            }

            // Limpa tudo
            function limparFormulario() {
                $('#search-aluno').val('');
                $('#search-results').empty().hide();
                $('#selected-student').hide();
                alunoSelecionado = null;
                $('#turma-aluno').val('');
                $('#turno-aluno').val('');
                $('#escola-destino').val('');
                $('#municipio-uf').val('');
                $('#data-transferencia').val(dataFormatada);
            }
        });
    </script>

</body>

</html>