<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Caderno de Chamada - Dashboard Acadêmico</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/app-style.css">
    <link rel="stylesheet" href="../assets/css/icons.css">
    <link rel="stylesheet" href="../assets/css/sidebar-menu.css">
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            background: linear-gradient(to right, #2c3e50, #3498db);
            color: #ecf0f1;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
        }

        .topbar-nav {
            height: 60px;
            z-index: 1000;
        }

        .content-wrapper {
            padding: 40px 20px;
            padding-top: 80px;
            min-height: calc(100vh - 60px);
        }

        .container-presenca {
            max-width: 950px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.05);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #ffffff;
        }

        .filtros {
            display: flex;
            gap: 15px;
            margin-bottom: 25px;
            flex-wrap: wrap;
            justify-content: space-between;
        }

        .filtros select,
        .filtros input[type="date"] {
            flex: 1;
            min-width: 200px;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #71affe;
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        .info-aula {
            background: rgba(255, 255, 255, 0.08);
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 25px;
        }

        .info-aula p {
            margin: 5px 0;
        }

        .table-presenca {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .table-presenca th {
            background-color: rgba(113, 175, 254, 0.2);
            color: #ffffff;
            padding: 12px;
            text-align: left;
        }

        .table-presenca td {
            padding: 12px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .radio-group {
            display: flex;
            gap: 10px;
        }

        .radio-option {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .radio-option input {
            accent-color: #1abc9c;
        }

        .btn-group {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            margin-top: 20px;
        }

        .btn {
            padding: 8px 18px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            border: none;
            font-size: 0.9em;
        }

        .btn-cancelar {
            background-color: #e74c3c;
            color: white;
        }

        .btn-cancelar:hover {
            background-color: #e74c3c;
        }

        .btn-salvar {
            background-color: #1abc9c;
            color: white;
        }

        .btn-salvar:hover {
            background-color: #16a085;
        }

        @media (max-width: 768px) {
            .content-wrapper {
                padding: 20px;
                padding-top: 70px;
            }

            .filtros {
                flex-direction: column;
                gap: 10px;
            }

            .btn-group {
                flex-direction: column;
                align-items: stretch;
            }

            .btn {
                width: 100%;
            }

            .table-presenca {
                overflow-x: auto;
                display: block;
            }
        }

        .navbar {
            background-color: rgba(0, 0, 0, 0.2) !important;
            backdrop-filter: blur(10px);
        }
    </style>
</head>

<body class="bg-theme bg-theme1">
    <?php
    require("menu_padrao.php");
    ?>

        <!-- Conteúdo Principal -->
        <div class="content-wrapper">
            <div class="container-presenca">
                <h2>Caderno de Chamada</h2>

                <div class="filtros">
                    <select id="turmaSelect">
                        <option value="turmaA">Turma A</option>
                        <option value="turmaB">Turma B</option>
                        <option value="turmaC">Turma C</option>
                    </select>
                    <input type="date" id="dataPresenca" />
                </div>

                <div class="info-aula">
                    <p><strong>Data:</strong> <span id="dataInfo"></span></p>
                    <p><strong>Turma:</strong> <span id="turmaInfo"></span></p>
                </div>

                <table class="table-presenca">
                    <thead>
                        <tr>
                            <th>Matrícula</th>
                            <th>Nome</th>
                            <th>Presença</th>
                        </tr>
                    </thead>
                    <tbody id="tabelaAlunos">
                        <!-- JS irá preencher -->
                    </tbody>
                </table>

                <div class="btn-group">
                    <button class="btn btn-cancelar"
                        onclick="window.location.href='caderno_chamada.php'">Cancelar</button>
                    <button class="btn btn-salvar" onclick="salvarPresenca()">Salvar</button>
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
    <script src="botaoSair.js"></script>
    <script>

        $(function () {
            $('.sidebar-menu').sidebarMenu();
        });

        if (typeof alunosFake === 'undefined') {
            var alunosFake = {
                turmaA: [
                    { matricula: "123456", nome: "Aluno A1" },
                    { matricula: "123457", nome: "Aluno A2" }
                ],
                turmaB: [
                    { matricula: "223456", nome: "Aluno B1" },
                    { matricula: "223457", nome: "Aluno B2" }
                ],
                turmaC: [
                    { matricula: "323456", nome: "Aluno C1" },
                    { matricula: "323457", nome: "Aluno C2" }
                ]
            };
        }

        function atualizarTabela() {
            const turma = document.getElementById("turmaSelect").value;
            const data = document.getElementById("dataPresenca").value;
            document.getElementById("turmaInfo").innerText = turma;
            document.getElementById("dataInfo").innerText = data.split('-').reverse().join('/');

            const alunos = alunosFake[turma] || [];
            const tbody = document.getElementById("tabelaAlunos");
            tbody.innerHTML = "";

            alunos.forEach((aluno, index) => {
                tbody.innerHTML += `
          <tr>
            <td>${aluno.matricula}</td>
            <td>${aluno.nome}</td>
            <td>
              <div class="radio-group">
                <label class="radio-option"><input type="radio" name="presenca${index}" value="presente" checked> Presente</label>
                <label class="radio-option"><input type="radio" name="presenca${index}" value="ausente"> Ausente</label>
                <label class="radio-option"><input type="radio" name="presenca${index}" value="justificado"> Justificado</label>
              </div>
            </td>
          </tr>`;
            });
        }

        function salvarPresenca() {
            alert("Presença salva com sucesso!");
        }

        // Inicialização
        document.addEventListener("DOMContentLoaded", () => {
            const hoje = new Date().toISOString().split('T')[0];
            document.getElementById("dataPresenca").value = hoje;
            atualizarTabela();

            document.getElementById("turmaSelect").addEventListener("change", atualizarTabela);
            document.getElementById("dataPresenca").addEventListener("change", atualizarTabela);
        });
    </script>
</body>

</html>