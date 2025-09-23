<?php require_once '../includes/bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resumo por Turma - Dashboard Acadêmico</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/app-style.css">
    <link rel="stylesheet" href="../assets/css/icons.css">
    <link rel="stylesheet" href="../assets/css/sidebar-menu.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .summary-card {
            border-radius: 10px;
            transition: all 0.3s;
            margin-bottom: 20px;
            border-left: 4px solid;
            cursor: pointer;
            background-color: transparent !important;
            /* Alterado para transparente */
        }

        .summary-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .summary-card.students {
            border-left-color: #3498db;
        }

        .summary-card.teachers {
            border-left-color: #2ecc71;
        }

        .summary-card.disciplines {
            border-left-color: #e74c3c;
        }

        .summary-value {
            font-size: 2.5rem;
            font-weight: bold;
        }

        .summary-label {
            font-size: 1rem;
            color: #34495e !important;
            /* Alterado para azul escuro (mais visível) */
            font-weight: 500;
        }

        .filter-section {
            background-color: transparent !important;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .btn-custom-print {
            background-color: #3498db !important;
            color: white !important;
            border: none !important;
        }

        .btn-custom-print:hover {
            background-color: #2980b9 !important;
        }

        .turma-header {
            background-color: transparent !important;
            /* Alterado para transparente */
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            border-bottom: 1px solid #ecf0f1;
        }

        @media print {

            .no-print,
            #sidebar-wrapper,
            .topbar-nav {
                display: none !important;
            }

            body {
                background: white !important;
                padding: 1cm !important;
            }

            .summary-card {
                page-break-inside: avoid;
                border-left: none !important;
                border: 1px solid #ddd !important;
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
    require("menu_padrão.php");
    ?>


    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="card" style="background-color: transparent;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
                        <h4 class="page-title"><i class="zmdi zmdi-group-work mr-2"></i> Resumo por Turma</h4>
                        <button id="print-btn" class="btn btn-custom-print">
                            <i class="zmdi zmdi-print mr-2"></i>Imprimir Relatório
                        </button>
                    </div>

                    <div class="filter-section no-print">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="ano-filter">Ano Letivo</label>
                                    <select class="form-control" id="ano-filter">
                                        <option>2024</option>
                                        <option>2023</option>
                                        <option>2022</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="serie-filter">Série</label>
                                    <select class="form-control" id="serie-filter">
                                        <option value="">Todas</option>
                                        <option>1º Ano</option>
                                        <option>2º Ano</option>
                                        <option>3º Ano</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="turma-filter">Turma</label>
                                    <select class="form-control" id="turma-filter">
                                        <option value="">Todas</option>
                                        <option>Turma A</option>
                                        <option>Turma B</option>
                                        <option>Turma C</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Cards de Resumo -->
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card summary-card students">
                                <div class="card-body text-center">
                                    <div class="summary-value" id="total-alunos">42</div>
                                    <div class="summary-label">Alunos</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card summary-card teachers">
                                <div class="card-body text-center">
                                    <div class="summary-value" id="total-professores">8</div>
                                    <div class="summary-label">Professores</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card summary-card disciplines">
                                <div class="card-body text-center">
                                    <div class="summary-value" id="total-disciplinas">12</div>
                                    <div class="summary-label">Disciplinas</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Detalhes por Turma -->
                    <div class="turma-header">
                        <h5><i class="zmdi zmdi-filter mr-2"></i> Turma: 1º Ano A</h5>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Disciplina</th>
                                    <th>Professor</th>
                                    <th>Alunos</th>
                                    <th>Aulas Semanais</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Matemática</td>
                                    <td>Maria da Silva</td>
                                    <td>42</td>
                                    <td>5</td>
                                </tr>
                                <tr>
                                    <td>Português</td>
                                    <td>João Oliveira</td>
                                    <td>42</td>
                                    <td>5</td>
                                </tr>
                                <tr>
                                    <td>Ciências</td>
                                    <td>Ana Santos</td>
                                    <td>42</td>
                                    <td>3</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
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
        $(document).ready(function () {
            // Dados completos e fixos para todas as turmas
            const dadosCompletos = {
                '2024': {
                    '1º Ano': {
                        'Turma A': {
                            alunos: 42,
                            professores: 8,
                            disciplinas: 12,
                            detalhes: [
                                ['Matemática', 'Maria da Silva', '42', '5'],
                                ['Português', 'João Oliveira', '42', '5'],
                                ['Ciências', 'Ana Santos', '42', '3']
                            ]
                        },
                        'Turma B': {
                            alunos: 40,
                            professores: 7,
                            disciplinas: 11,
                            detalhes: [
                                ['Matemática', 'Carlos Mendes', '40', '5'],
                                ['Português', 'Fernanda Lima', '40', '5']
                            ]
                        }
                    },
                    '2º Ano': {
                        'Turma A': {
                            alunos: 45,
                            professores: 9,
                            disciplinas: 13,
                            detalhes: [
                                ['Matemática', 'Patrícia Gomes', '45', '5'],
                                ['Literatura', 'Ricardo Silva', '45', '4']
                            ]
                        }
                    },
                    '3º Ano': {
                        'Turma A': {
                            alunos: 38,
                            professores: 8,
                            disciplinas: 12,
                            detalhes: [
                                ['Matemática', 'André Luiz', '38', '5'],
                                ['Redação', 'Juliana Martins', '38', '4']
                            ]
                        }
                    }
                }
            };

            // Inicializa com totais gerais
            const totaisGerais = {
                alunos: 181,
                professores: 22,
                disciplinas: 42
            };

            // Filtros dinâmicos
            $('#ano-filter, #serie-filter, #turma-filter').change(function () {
                const ano = $('#ano-filter').val();
                const serie = $('#serie-filter').val();
                const turma = $('#turma-filter').val();

                if (turma && turma !== 'Todas' && serie && serie !== 'Todas') {
                    // Busca dados da turma específica
                    const turmaData = dadosCompletos[ano]?.[serie]?.[turma] || {
                        alunos: 40,
                        professores: 5,
                        disciplinas: 10,
                        detalhes: [
                            ['Disciplina', 'Professor', '40', '5']
                        ]
                    };

                    // Atualiza os cards
                    $('#total-alunos').text(turmaData.alunos);
                    $('#total-professores').text(turmaData.professores);
                    $('#total-disciplinas').text(turmaData.disciplinas);

                    // Atualiza o cabeçalho
                    $('.turma-header h5').text(`Turma: ${serie} ${turma}`);

                    // Atualiza a tabela
                    updateTableData(turmaData.detalhes);
                } else {
                    // Mostra totais gerais quando "Todas" está selecionado
                    $('#total-alunos').text(totaisGerais.alunos);
                    $('#total-professores').text(totaisGerais.professores);
                    $('#total-disciplinas').text(totaisGerais.disciplinas);
                    $('.turma-header h5').text('Resumo Geral');
                    resetTableData();
                }
            });

            // Função para atualizar a tabela
            function updateTableData(dados) {
                const $tableBody = $('table tbody');
                $tableBody.empty();

                dados.forEach(row => {
                    $tableBody.append(`<tr><td>${row[0]}</td><td>${row[1]}</td><td>${row[2]}</td><td>${row[3]}</td></tr>`);
                });
            }

            // Função para resetar a tabela
            function resetTableData() {
                const $tableBody = $('table tbody');
                $tableBody.empty();
                $tableBody.append(`
                <tr><td colspan="4" class="text-center">Selecione uma turma específica para ver os detalhes</td></tr>
            `);
            }

            // Botão de impressão
            $('#print-btn').click(function () {
                window.print();
            });

            // Inicializa a tela com os totais gerais
            $('#total-alunos').text(totaisGerais.alunos);
            $('#total-professores').text(totaisGerais.professores);
            $('#total-disciplinas').text(totaisGerais.disciplinas);
            $('.turma-header h5').text('Resumo Geral');
            resetTableData();
        });
    </script>
</body>

</html>