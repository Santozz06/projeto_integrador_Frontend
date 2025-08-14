<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Vínculos - Dashboard Acadêmico</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/app-style.css">
    <link rel="stylesheet" href="../assets/css/icons.css">
    <link rel="stylesheet" href="../assets/plugins/simplebar/css/simplebar.css">
    <link rel="stylesheet" href="../assets/css/sidebar-menu.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .btn-custom-primary {
            background-color: #1abc9c !important;
            color: white !important;
            border: none !important;
        }

        .btn-custom-primary:hover {
            background-color: #16a085 !important;
        }

        .btn-custom-secondary {
            background-color: #2c5f9e !important;
            color: white !important;
            border: none !important;
        }

        .btn-custom-secondary:hover {
            background-color: #1e4a7e !important;
        }

        .info-label {
            font-weight: bold;
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

    <!-- Conteúdo principal -->
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="row pt-2 pb-2">
                <div class="col-sm-12">
                    <h4 class="page-title">Gerenciar Vínculo do Aluno</h4>
                </div>
            </div>

            <!-- Dados do aluno -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Dados do aluno</h5>
                    <p><span class="info-label">Nome:</span> <span id="nomeAluno">João da Silva</span></p>
                    <p><span class="info-label">Matrícula:</span> <span id="matriculaAluno">20251001</span></p>
                    <p><span class="info-label">Situação:</span> Ainda não vinculado</p>
                </div>
            </div>

            <!-- Selecionar turma -->
            <form id="formVinculo">
                <div class="form-group">
                    <label for="turma" class="text-white">Vincular a turma:</label>
                    <select class="form-control" id="turma" required>
                        <option value="">Selecione a turma...</option>
                        <option>1º Ano A - Matutino</option>
                        <option>1º Ano B - Matutino</option>
                        <option>2º Ano A - Vespertino</option>
                        <option>3º Ano A - Integral</option>
                    </select>
                </div>

                <div class="form-group text-right">
                    <button type="button" class="btn btn-custom-secondary" id="btnTrocarTurma">
                        <i class="zmdi zmdi-refresh mr-1"></i> Trocar de turma
                    </button>
                    <button type="submit" class="btn btn-custom-primary">
                        <i class="zmdi zmdi-link mr-1"></i> Vincular
                    </button>
                </div>
            </form>

            <div class="alert alert-success mt-3 d-none" id="successMessage">
                Vínculo realizado com sucesso!
            </div>
        </div>
    </div>
    <!--Overlay-->
    <div class="overlay toggle-menu"></div>

    <footer class="footer">
        <div class="container">
            <div class="text-center text-white">
                Copyright © 2023 Dashboard Acadêmico
            </div>
        </div>
    </footer>
    </div>

    <script src="../assets/js/jquery.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script src="../assets/plugins/simplebar/js/simplebar.js"></script>
    <script src="../assets/js/sidebar-menu.js"></script>
    <script src="../assets/js/app-script.js"></script>
    <script src="botaoSair.js"></script>

    <script>
        $(document).ready(function () {
            // Recupera dados do aluno recém-cadastrado
            const nome = localStorage.getItem("novoAlunoNome");
            const matricula = localStorage.getItem("novoAlunoMatricula");

            if (nome && matricula) {
                // Preenche os campos de visualização
                $('#nomeAluno').text(nome);
                $('#matriculaAluno').text(matricula);

                // Preenche também o campo oculto/oculto no formulário, se necessário
                $('#nomeMatricula').val(`${nome} - ${matricula}`);

                // Limpa os dados salvos
                localStorage.removeItem("novoAlunoNome");
                localStorage.removeItem("novoAlunoMatricula");
            }

            // Submissão do formulário de vínculo
            $('#formVinculo').on('submit', function (e) {
                e.preventDefault();

                const turmaSelecionada = $('#turma').val();
                if (!turmaSelecionada) {
                    alert('Por favor, selecione uma turma para vincular.');
                    return;
                }

                console.log('Aluno vinculado:', {
                    nome: $('#nomeAluno').text(),
                    matricula: $('#matriculaAluno').text(),
                    turma: turmaSelecionada
                });

                // Exibe mensagem de sucesso
                $('#successMessage').removeClass('d-none').fadeIn();
                setTimeout(() => {
                    $('#successMessage').fadeOut();
                }, 3000);
            });

            // Botão "Trocar de turma"
            $('#btnTrocarTurma').click(function () {
                window.location.href = 'trocarTurma.php';
            });
        });
    </script>

</body>

</html>