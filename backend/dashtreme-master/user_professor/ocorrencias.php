<?php require_once '../includes/bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Ocorrências - Dashboard Acadêmico</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="../assets/css/app-style.css" />
    <link rel="stylesheet" href="../assets/css/icons.css" />
    <link rel="stylesheet" href="../assets/css/sidebar-menu.css" />
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            background: linear-gradient(to right, #2c3e50, #3498db);
            color: #ecf0f1;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .container-ocorrencias {
            max-width: 950px;
            margin: 60px auto;
            background: rgba(255, 255, 255, 0.05);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #ffffff;
        }

        label {
            font-weight: bold;
        }

        select,
        input,
        textarea {
            width: 100%;
            padding: 10px;
            margin-top: 6px;
            margin-bottom: 15px;
            border: none;
            border-radius: 8px;
            background-color: rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        .form-inline {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .form-group {
            flex: 1 1 30%;
            min-width: 220px;
        }

        .btn {
            background-color: #1abc9c;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 10px;
            min-width: 120px;
        }

        .btn:hover {
            background-color: #16a085;
        }

        .btn-limpar {
            background-color: #2980b9;
        }

        .btn-limpar:hover {
            background-color: #2471a3;
        }

        .btn-cancelar {
            background-color: #e74c3c;
        }

        .btn-cancelar:hover {
            background-color: #e74c3c;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            padding: 12px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        th {
            background-color: rgba(113, 175, 254, 0.1);
            color: #ffffff;
        }

        @media (max-width: 768px) {
            .container-ocorrencias {
                padding: 20px;
            }

            .form-inline {
                flex-direction: column;
                gap: 15px;
            }

            .form-group {
                width: 100%;
            }

            table {
                font-size: 14px;
                overflow-x: auto;
                display: block;
            }

            th,
            td {
                white-space: nowrap;
            }

            .btn {
                width: 100%;
            }

            .form-inline:last-child {
                flex-direction: column;
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

        <div class="content-wrapper">
            <div class="container-ocorrencias">
                <h2>Ocorrências</h2>

                <div class="form-inline">
                    <div class="form-group">
                        <label for="turma">Turma</label>
                        <select id="turma">
                            <option value="" disabled selected>Selecione a turma</option>
                            <option>Turma A</option>
                            <option>Turma B</option>
                            <option>Turma C</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="aluno">Aluno</label>
                        <input type="text" id="aluno" placeholder="Digite o nome do aluno" />
                    </div>

                    <div class="form-group">
                        <label for="data">Data</label>
                        <input type="date" id="data" />
                    </div>
                </div>

                <div class="form-inline">
                    <div class="form-group">
                        <label for="tipo">Tipo de Ocorrência</label>
                        <input type="text" id="tipo" placeholder="Ex: Indisciplina, Atraso..." />
                    </div>
                    <div class="form-group" style="flex: 1 1 100%">
                        <label for="descricao">Descrição</label>
                        <textarea id="descricao" rows="2" placeholder="Detalhes da ocorrência..."></textarea>
                    </div>
                    <div>
                        <button class="btn" onclick="adicionarOcorrencia()">Adicionar</button>
                    </div>
                </div>

                <div style="overflow-x:auto;">
                    <table id="tabelaOcorrencias">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Turma</th>
                                <th>Aluno</th>
                                <th>Tipo</th>
                                <th>Descrição</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

                <div class="form-inline" style="margin-top: 30px;">
                    <button class="btn" onclick="salvarOcorrencias()">Salvar</button>
                    <button class="btn btn-limpar" onclick="limparOcorrencias()">Limpar</button>
                    <button class="btn btn-cancelar" onclick="window.location.href='ocorrencias.html'">Cancelar</button>
                </div>
            </div>
        </div>
        <div class="overlay toggle-menu"></div>
    </div>

    <script src="../assets/js/jquery.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script src="../assets/plugins/simplebar/js/simplebar.js"></script>
    <script src="../assets/js/sidebar-menu.js"></script>
    <script src="../assets/js/app-script.js"></script>
    <script>
        function adicionarOcorrencia() {
            const turma = document.getElementById('turma').value;
            const aluno = document.getElementById('aluno').value.trim();
            const data = document.getElementById('data').value;
            const tipo = document.getElementById('tipo').value.trim();
            const descricao = document.getElementById('descricao').value.trim();

            if (!turma || !aluno || !data || !tipo || !descricao) {
                alert('Preencha todos os campos antes de adicionar.');
                return;
            }

            const tbody = document.querySelector('#tabelaOcorrencias tbody');
            const linha = document.createElement('tr');
            linha.innerHTML = `
        <td>${data}</td>
        <td>${turma}</td>
        <td>${aluno}</td>
        <td>${tipo}</td>
        <td>${descricao}</td>
      `;
            tbody.appendChild(linha);

            document.getElementById('aluno').value = '';
            document.getElementById('tipo').value = '';
            document.getElementById('descricao').value = '';
        }

        function limparOcorrencias() {
            document.querySelector('#tabelaOcorrencias tbody').innerHTML = '';
        }

        function salvarOcorrencias() {
            alert('Ocorrências salvas com sucesso (simulação)');
        }
    </script>
</body>

</html>