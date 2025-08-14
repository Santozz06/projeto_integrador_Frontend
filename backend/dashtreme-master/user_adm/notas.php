<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notas - Dashboard Acadêmico</title>
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
            max-width: 1000px;
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
            color: #e7e8e9;
            border-bottom: 2px solid #ffffff;
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

        .table-container {
            margin-top: 30px;
            overflow-x: auto;
        }

        .table {
            background-color: rgba(255, 255, 255, 0.05);
            color: #ecf0f1;
            border-collapse: separate;
            border-spacing: 0;
            border-radius: 8px;
            overflow: hidden;
        }

        .table th {
            background-color: rgba(113, 175, 254, 0.3);
            color: #ffffff;
            border: none;
        }

        .table td,
        .table th {
            padding: 12px 15px;
            vertical-align: middle;
            border: none;
            border-bottom: 1px solid rgba(113, 175, 254, 0.1);
        }

        .table tr:hover td {
            background-color: rgba(113, 175, 254, 0.1);
        }

        .input-nota {
            width: 70px;
            text-align: center;
            background-color: rgba(255, 255, 255, 0.1);
            border: 1px solid #ffffff;
            color: #fff;
            border-radius: 4px;
            padding: 5px;
        }

        .input-nota:focus {
            outline: none;
            border-color: #1abc9c;
        }

        .filtros-container {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .filtro-item {
            flex: 1;
            min-width: 200px;
        }

        .aluno-info {
            display: flex;
            align-items: center;
        }

        .aluno-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
            background-color: #71affe;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        .media {
            display: flex;
            align-items: center;
            font-weight: bold;
            color: #ffffff;
        }

        .status-aprovado {
            color: #3efa9c;
        }

        .status-reprovado {
            color: #eb1902;
        }

        .status-recuperacao {
            color: #ffcb3d;
        }

        .badge-turma {
            background-color: rgba(113, 175, 254, 0.2);
            color: #eff0f1;
            padding: 3px 8px;
            border-radius: 10px;
            font-size: 0.8rem;
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
                                <h4 class="page-title"><i class="zmdi zmdi-check-circle mr-2"></i> Notas</h4>
                            </div>

                            <!-- Formulário de filtros -->
                            <div class="form-container">
                                <div class="filtros-container">
                                    <div class="filtro-item">
                                        <div class="bold-title">Ano Letivo</div>
                                        <select id="ano-letivo" class="form-control">
                                            <option value="2025">2025</option>
                                            <option value="2024">2024</option>
                                            <option value="2023">2023</option>
                                        </select>
                                    </div>
                                    <div class="filtro-item">
                                        <div class="bold-title">Disciplina</div>
                                        <select id="disciplina" class="form-control">
                                            <option value="">Todas disciplinas</option>
                                            <option value="1">Matemática</option>
                                            <option value="2">Português</option>
                                            <option value="3">História</option>
                                            <option value="4">Geografia</option>
                                            <option value="5">Ciências</option>
                                        </select>
                                    </div>
                                    <div class="filtro-item">
                                        <div class="bold-title">Turma</div>
                                        <select id="turma" class="form-control">
                                            <option value="">Todas turmas</option>
                                            <option value="1A">1º Ano A</option>
                                            <option value="1B">1º Ano B</option>
                                            <option value="2A">2º Ano A</option>
                                            <option value="2B">2º Ano B</option>
                                            <option value="3A">3º Ano A</option>
                                            <option value="3B">3º Ano B</option>
                                        </select>
                                    </div>
                                    <div class="filtro-item">
                                        <div class="bold-title">Trimestre</div>
                                        <select id="trimestre" class="form-control">
                                            <option value="1">1º Trimestre</option>
                                            <option value="2">2º Trimestre</option>
                                            <option value="3">3º Trimestre</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Tabela de notas -->
                                <div class="table-container">
                                    <table id="tabela-notas" class="table">
                                        <thead>
                                            <tr>
                                                <th>Aluno</th>
                                                <th>Matrícula</th>
                                                <th>Turma</th>
                                                <th>Nota 1</th>
                                                <th>Nota 2</th>
                                                <th>Nota 3</th>
                                                <th>Nota 4</th>
                                                <th>Média</th>
                                                <th>Status</th>
                                                <th>Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Dados serão preenchidos via JavaScript -->
                                        </tbody>
                                    </table>
                                    <div id="no-results" class="no-results">
                                        Nenhum aluno encontrado com os filtros selecionados.
                                    </div>
                                </div>

                                <!-- Botões -->
                                <div class="btn-group">
                                    <button class="btn-salvar" id="btn-salvar-todos">Salvar Todas as
                                        Alterações</button>
                                    <button class="btn-cancelar" id="btn-cancelar">Cancelar</button>
                                </div>
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
    <script src="botaoSair.js"></script>

    <script>
        $(document).ready(function () {
            // Dados de exemplo corrigidos
            const dadosAlunos = [
                {
                    id: 1,
                    nome: "João Silva",
                    matricula: "2025001",
                    turma: "2A",
                    disciplina: "Matemática",
                    notas: [8.5, 7.0, 9.2, 8.8],
                    avatar: "JS"
                },
                {
                    id: 2,
                    nome: "Maria Andrade",
                    matricula: "2025002",
                    turma: "2A",
                    disciplina: "Matemática",
                    notas: [6.5, 5.0, 7.2, 6.8],
                    avatar: "MA"
                },
                {
                    id: 3,
                    nome: "Carlos Pereira",
                    matricula: "2025003",
                    turma: "2A",
                    disciplina: "Português",
                    notas: [4.5, 3.0, 5.2, 4.8],
                    avatar: "CP"
                },
                {
                    id: 4,
                    nome: "Ana Souza",
                    matricula: "2025004",
                    turma: "2B",
                    disciplina: "Português",
                    notas: [9.5, 8.0, 9.2, 9.8],
                    avatar: "AS"
                },
                {
                    id: 5,
                    nome: "Pedro Oliveira",
                    matricula: "2025005",
                    turma: "3A",
                    disciplina: "História",
                    notas: [7.5, 8.0, 6.5, 7.0],
                    avatar: "PO"
                }
            ];

            // Definir ano atual como padrão
            const anoAtual = new Date().getFullYear();
            $('#ano-letivo').val(anoAtual);

            // Botão Cancelar
            $('#btn-cancelar').click(function () {
                if (confirm('Deseja realmente cancelar? Todas as alterações não salvas serão perdidas.')) {
                    carregarTabela();
                }
            });

            // Função para calcular média
            function calcularMedia(notas) {
                const soma = notas.reduce((total, nota) => total + parseFloat(nota), 0);
                return (soma / notas.length).toFixed(1);
            }

            // Função para determinar status
            function determinarStatus(media) {
                if (media >= 7) return { texto: 'Aprovado', classe: 'status-aprovado' };
                if (media >= 5) return { texto: 'Recuperação', classe: 'status-recuperacao' };
                return { texto: 'Reprovado', classe: 'status-reprovado' };
            }

            // Função para carregar tabela corrigida
            function carregarTabela(dados = dadosAlunos) {
                const tbody = $('#tabela-notas tbody');
                tbody.empty();

                if (dados.length === 0) {
                    $('#no-results').show();
                    return;
                } else {
                    $('#no-results').hide();
                }

                dados.forEach(aluno => {
                    const media = calcularMedia(aluno.notas);
                    const status = determinarStatus(media);

                    const row = `
                        <tr data-id="${aluno.id}" data-turma="${aluno.turma}" data-disciplina="${aluno.disciplina}">
                            <td>
                                <div class="aluno-info">
                                    <div class="aluno-avatar">${aluno.avatar}</div>
                                    ${aluno.nome}
                                </div>
                            </td>
                            <td>${aluno.matricula}</td>
                            <td><span class="badge-turma">${formatarTurma(aluno.turma)}</span></td>
                            <td><input type="number" class="input-nota" value="${aluno.notas[0]}" min="0" max="10" step="0.1"></td>
                            <td><input type="number" class="input-nota" value="${aluno.notas[1]}" min="0" max="10" step="0.1"></td>
                            <td><input type="number" class="input-nota" value="${aluno.notas[2]}" min="0" max="10" step="0.1"></td>
                            <td><input type="number" class="input-nota" value="${aluno.notas[3]}" min="0" max="10" step="0.1"></td>
                            <td class="media">${media}</td>
                            <td class="status-cell ${status.classe}">${status.texto}</td>
                            <td>
                                <button class="btn btn-sm btn-salvar">Salvar</button>
                            </td>
                        </tr>
                    `;

                    tbody.append(row);
                });
            }

            // Função auxiliar para formatar turma
            function formatarTurma(turma) {
                return turma.replace(/(\d)([AB])/, '$1º Ano $2');
            }

            // Carregar tabela inicialmente
            carregarTabela();

            // Atualizar médias quando as notas são alteradas
            $(document).on('change', '.input-nota', function () {
                const row = $(this).closest('tr');
                const notas = row.find('.input-nota');

                let soma = 0;
                let count = 0;

                notas.each(function () {
                    const valor = parseFloat($(this).val());
                    if (!isNaN(valor)) {
                        soma += valor;
                        count++;
                    }
                });

                const media = count > 0 ? (soma / count).toFixed(1) : 0;
                row.find('.media').text(media);

                // Atualizar status
                const statusCell = row.find('.status-cell');
                const status = determinarStatus(media);
                statusCell.removeClass('status-aprovado status-reprovado status-recuperacao')
                    .addClass(status.classe)
                    .text(status.texto);
            });

            // Salvar notas individuais
            $(document).on('click', '.btn-salvar', function () {
                const row = $(this).closest('tr');
                const id = row.data('id');
                const notas = [];

                row.find('.input-nota').each(function () {
                    notas.push($(this).val());
                });

                // Atualizar nos dados originais
                const alunoIndex = dadosAlunos.findIndex(a => a.id == id);
                if (alunoIndex !== -1) {
                    dadosAlunos[alunoIndex].notas = notas.map(parseFloat);
                }

                alert('Notas salvas com sucesso para o aluno ' + row.find('.aluno-info').text().trim());
            });

            // Salvar todas as notas
            $('#btn-salvar-todos').on('click', function () {
                $('#tabela-notas tbody tr').each(function () {
                    const row = $(this);
                    const id = row.data('id');
                    const notas = [];

                    row.find('.input-nota').each(function () {
                        notas.push($(this).val());
                    });

                    // Atualizar nos dados originais
                    const alunoIndex = dadosAlunos.findIndex(a => a.id == id);
                    if (alunoIndex !== -1) {
                        dadosAlunos[alunoIndex].notas = notas.map(parseFloat);
                    }
                });

                alert('Todas as notas foram salvas com sucesso!');
            });

            // Filtros
            $('#disciplina, #turma, #trimestre').on('change', function () {
                const disciplina = $('#disciplina').val();
                const turma = $('#turma').val();
                const trimestre = $('#trimestre').val();

                let dadosFiltrados = dadosAlunos;

                // Aplicar filtros
                if (disciplina) {
                    dadosFiltrados = dadosFiltrados.filter(aluno =>
                        aluno.disciplina.toLowerCase() === $('#disciplina option:selected').text().toLowerCase()
                    );
                }

                if (turma) {
                    dadosFiltrados = dadosFiltrados.filter(aluno => aluno.turma === turma);
                }

                carregarTabela(dadosFiltrados);
            });
        });
    </script>
</body>

</html>