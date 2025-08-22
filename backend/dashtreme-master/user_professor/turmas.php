<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Turmas - Dashboard Acadêmico</title>
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
        }

        .container-select {
            max-width: 900px;
            margin: 40px auto;
            background: rgba(255, 255, 255, 0.05);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }

        .form-select {
            background-color: rgba(255, 255, 255, 0.1);
            color: #ffffff;
            border: 1px solid #71affe;
            padding: 10px;
            border-radius: 8px;
            width: 100%;
            margin-bottom: 20px;
        }

        .btn {
            background-color: #1abc9c;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
        }

        .btn:hover {
            background-color: #16a085;
        }

        .turma-dados {
            margin-top: 30px;
            display: none;
        }

        .card-section {
            background: rgba(255, 255, 255, 0.08);
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .card-section h4 {
            border-bottom: 1px solid #71affe;
            padding-bottom: 10px;
            margin-bottom: 15px;
            color: #71affe;
        }

        .aluno {
            display: flex;
            justify-content: space-between;
            padding: 6px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .status {
            font-size: 0.9em;
            color: #ffffff;
        }
    </style>
</head>

<body class="bg-theme bg-theme1">
    <?php
    require("menu_padrao.php");
    ?>

        <!-- Conteúdo Principal -->
        <div class="content-wrapper">
            <div class="container-select">
                <h2>Selecione a Turma</h2>
                <select id="selectTurma" class="form-select">
                    <option value="" disabled selected>-- Escolha uma turma --</option>
                    <option value="turmaA">Turma A</option>
                    <option value="turmaB">Turma B</option>
                    <option value="turmaC">Turma C</option>
                </select>

                <button class="btn" onclick="carregarTurma()">Visualizar</button>

                <div id="dadosTurma" class="turma-dados">
                    <div class="card-section">
                        <h4>Informações da Turma</h4>
                        <p><strong>Nome da Turma:</strong> <span id="nomeTurma"></span></p>
                        <p><strong>Professor:</strong> <span id="professorTurma"></span></p>
                        <p><strong>Turno:</strong> <span id="turnoTurma"></span></p>
                    </div>

                    <div class="card-section">
                        <h4>Lista de Alunos</h4>
                        <div id="alunosLista"></div>
                    </div>

                    <div class="card-section">
                        <h4>Conteúdos</h4>
                        <ul id="conteudosLista"></ul>
                    </div>

                    <div class="card-section">
                        <h4>Avisos</h4>
                        <ul id="avisosLista"></ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="overlay toggle-menu"></div>
    </div>

    <!-- JS -->
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
    </script>

    <script>
        const turmasFake = {
            turmaA: {
                nome: "Turma A",
                professor: "Profa. Ana Souza",
                turno: "Manhã",
                alunos: [
                    { nome: "João Silva", status: "Ativo" },
                    { nome: "Maria Oliveira", status: "Ativo" },
                    { nome: "Carlos Lima", status: "Transferido" },
                ],
                conteudos: [
                    "Aula sobre relevo - 17/11/25",
                    "Atividade prática sobre mapas - 20/11/25"
                ],
                avisos: [
                    "Atividade avaliativa no dia 21/11/25"
                ]
            },
            turmaB: {
                nome: "Turma B",
                professor: "Prof. Marcos Lima",
                turno: "Tarde",
                alunos: [
                    { nome: "Fernanda Costa", status: "Ativo" },
                    { nome: "Bruno Castro", status: "Ativo" },
                    { nome: "Larissa Teixeira", status: "Ativo" }
                ],
                conteudos: [
                    "Revisão de geografia urbana - 15/11/25"
                ],
                avisos: [
                    "Entrega do trabalho até 22/11/25"
                ]
            },
            turmaC: {
                nome: "Turma C",
                professor: "Profa. Beatriz Nunes",
                turno: "Noite",
                alunos: [
                    { nome: "Eduardo Silva", status: "Ativo" },
                    { nome: "Juliana Alves", status: "Transferido" }
                ],
                conteudos: [
                    "Introdução ao clima - 14/11/25"
                ],
                avisos: [
                    "Prova dia 23/11/25"
                ]
            }
        };

        function carregarTurma() {
            const turmaSelecionada = document.getElementById("selectTurma").value;
            if (!turmaSelecionada || !turmasFake[turmaSelecionada]) return;

            const turma = turmasFake[turmaSelecionada];

            document.getElementById("nomeTurma").innerText = turma.nome;
            document.getElementById("professorTurma").innerText = turma.professor;
            document.getElementById("turnoTurma").innerText = turma.turno;

            const alunosDiv = document.getElementById("alunosLista");
            alunosDiv.innerHTML = "";
            turma.alunos.forEach(aluno => {
                alunosDiv.innerHTML += `<div class="aluno"><span>${aluno.nome}</span><span class="status">${aluno.status}</span></div>`;
            });

            const conteudosUl = document.getElementById("conteudosLista");
            conteudosUl.innerHTML = "";
            turma.conteudos.forEach(c => {
                conteudosUl.innerHTML += `<li>${c}</li>`;
            });

            const avisosUl = document.getElementById("avisosLista");
            avisosUl.innerHTML = "";
            turma.avisos.forEach(a => {
                avisosUl.innerHTML += `<li>${a}</li>`;
            });

            document.getElementById("dadosTurma").style.display = "block";
        }
    </script>
</body>

</html>