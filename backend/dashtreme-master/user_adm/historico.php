<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histórico Escolar - Dashboard Acadêmico</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/app-style.css">
    <link rel="stylesheet" href="../assets/css/icons.css">
    <link rel="stylesheet" href="../assets/css/sidebar-menu.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .filter-section {
            background-color: transparent !important;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid rgba(0, 0, 0, 0.1);
            box-shadow: none !important;
        }

        .student-list {
            max-height: 500px;
            overflow-y: auto;
            background-color: transparent !important;
            border-radius: 8px;
            border: none !important;
            padding: 0 !important;
        }

        .student-card {
            cursor: pointer;
            transition: all 0.2s ease;
            background-color: transparent !important;
            margin-bottom: 8px;
            border-radius: 4px;
            padding: 12px;
            border: none !important;
            box-shadow: none !important;
            border-left: 3px solid transparent;
        }

        .student-card:hover {
            background-color: rgba(0, 0, 0, 0.03) !important;
            border-left: 3px solid rgba(33, 150, 243, 0.3);
        }

        .student-card.selected {
            background-color: rgba(33, 150, 243, 0.08) !important;
            border-left: 3px solid #2196F3;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .student-card h6 {
            margin-bottom: 4px;
            color: #333;
            font-weight: 500;
        }

        .student-card small {
            color: #555;
        }

        .student-card:hover h6 {
            color: #2196F3;
        }

        .student-card.selected h6 {
            color: #1565C0;
        }

        .btn-generate {
            margin-top: 20px;
        }

        .no-print {
            display: block;
        }

        .btn-custom-print {
            background-color: #3498db !important;
            color: white !important;
            border: none !important;
        }

        .btn-custom-print:hover {
            background-color: #2980b9 !important;
        }

        /* Estilos específicos para o histórico */
        .historico-container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            display: none;
            /* Inicialmente oculto */
        }

        .cabecalho-historico {
            text-align: center;
            margin-bottom: 30px;
        }

        .titulo-historico {
            text-align: center;
            font-weight: bold;
            font-size: 18px;
            margin: 20px 0;
            text-decoration: underline;
        }

        .dados-aluno {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .dados-aluno td {
            padding: 5px;
            border: 1px solid #ddd;
        }

        .tabela-disciplinas {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        .tabela-disciplinas th,
        .tabela-disciplinas td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
        }

        .tabela-disciplinas th {
            background-color: #f2f2f2;
        }

        .assinaturas {
            width: 100%;
            margin-top: 50px;
        }

        .assinaturas td {
            padding-top: 50px;
            text-align: center;
            width: 50%;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            .historico-container {
                box-shadow: none;
                padding: 0;
                margin: 0;
            }

            body {
                background-color: white !important;
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

    <!-- Conteúdo principal -->
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body no-print">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="page-title"><i class="zmdi zmdi-assignment mr-2"></i> Histórico Escolar</h4>
                    </div>

                    <!-- Filtros -->
                    <div class="filter-section">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="ano-letivo">Ano Letivo</label>
                                    <select class="form-control" id="ano-letivo">
                                        <option>2025</option>
                                        <option>2024</option>
                                        <option>2023</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="serie">Série</label>
                                    <select class="form-control" id="serie">
                                        <option value="">Todas</option>
                                        <option>1º Ano</option>
                                        <option>2º Ano</option>
                                        <option>3º Ano</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="turma">Turma</label>
                                    <select class="form-control" id="turma">
                                        <option value="">Selecione uma turma</option>
                                        <option>Turma A</option>
                                        <option>Turma B</option>
                                        <option>Turma C</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Lista de Alunos -->
                    <div class="row">
                        <div class="col-md-12">
                            <h5>Alunos Matriculados</h5>
                            <div class="card student-list">
                                <div class="card-body">
                                    <div id="alunos-container">
                                        <p class="text-white">Selecione uma turma para visualizar os alunos.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botão de Visualizar -->
                    <div class="text-center btn-generate">
                        <button id="visualizar-historico" class="btn btn-custom-print" disabled>
                            <i class="zmdi zmdi-eye mr-2"></i> Visualizar Histórico
                        </button>
                    </div>
                </div>

                <!-- Container para o Histórico -->
                <div id="historico-container" class="historico-container">

                </div>

                <!-- Botão de Imprimir -->
                <div class="text-center btn-generate no-print" id="btn-print-container" style="display: none;">
                    <button id="imprimir-historico" class="btn btn-custom-print">
                        <i class="zmdi zmdi-print mr-2"></i> Imprimir Histórico
                    </button>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script src="botaoSair.js"></script>
    <script>
        $(document).ready(function () {
            // Dados de exemplo 
            const alunosPorTurma = {
                "Turma A": [
                    {
                        id: 1,
                        nome: "Ana Silva",
                        matricula: "20250001",
                        nascimento: "10/05/2010",
                        nacionalidade: "Brasileira",
                        naturalidade: "Parobé/RS",
                        filiacao: "José Silva e Maria Silva",
                        nis: "123.45678.90-1",
                        inep: "12345678",
                        disciplinas: [
                            { nome: "Língua Portuguesa", ano1: { nota: "8,5", ch: "160" }, ano2: { nota: "9,0", ch: "160" }, ano3: { nota: "8,7", ch: "160" } },
                            { nome: "Matemática", ano1: { nota: "7,8", ch: "160" }, ano2: { nota: "8,2", ch: "160" }, ano3: { nota: "8,5", ch: "160" } },
                            { nome: "História", ano1: { nota: "9,2", ch: "80" }, ano2: { nota: "8,7", ch: "80" }, ano3: { nota: "9,0", ch: "80" } },
                            { nome: "Geografia", ano1: { nota: "8,9", ch: "80" }, ano2: { nota: "8,5", ch: "80" }, ano3: { nota: "8,8", ch: "80" } },
                            { nome: "Ciências", ano1: { nota: "8,7", ch: "80" }, ano2: { nota: "9,1", ch: "80" }, ano3: { nota: "8,9", ch: "80" } },
                            { nome: "Artes", ano1: { nota: "9,5", ch: "40" }, ano2: { nota: "9,3", ch: "40" }, ano3: { nota: "9,4", ch: "40" } },
                            { nome: "Educação Física", ano1: { nota: "10,0", ch: "40" }, ano2: { nota: "10,0", ch: "40" }, ano3: { nota: "10,0", ch: "40" } }
                        ],
                        observacoes: "Aluno apresentou ótimo desempenho durante todo o período letivo."
                    },
                    {
                        id: 2,
                        nome: "Bruno Oliveira",
                        matricula: "20250002",
                        nascimento: "15/08/2010",
                        nacionalidade: "Brasileira",
                        naturalidade: "Taquara/RS",
                        filiacao: "Carlos Oliveira e Ana Oliveira",
                        nis: "987.65432.10-9",
                        inep: "87654321",
                        disciplinas: [
                            { nome: "Língua Portuguesa", ano1: { nota: "7,5", ch: "160" }, ano2: { nota: "8,0", ch: "160" }, ano3: { nota: "8,2", ch: "160" } },
                            { nome: "Matemática", ano1: { nota: "8,8", ch: "160" }, ano2: { nota: "9,2", ch: "160" }, ano3: { nota: "9,5", ch: "160" } },
                            { nome: "História", ano1: { nota: "8,2", ch: "80" }, ano2: { nota: "8,7", ch: "80" }, ano3: { nota: "9,0", ch: "80" } },
                            { nome: "Geografia", ano1: { nota: "8,0", ch: "80" }, ano2: { nota: "8,5", ch: "80" }, ano3: { nota: "8,3", ch: "80" } },
                            { nome: "Ciências", ano1: { nota: "9,2", ch: "80" }, ano2: { nota: "9,0", ch: "80" }, ano3: { nota: "9,4", ch: "80" } },
                            { nome: "Artes", ano1: { nota: "8,5", ch: "40" }, ano2: { nota: "9,0", ch: "40" }, ano3: { nota: "9,2", ch: "40" } },
                            { nome: "Educação Física", ano1: { nota: "10,0", ch: "40" }, ano2: { nota: "10,0", ch: "40" }, ano3: { nota: "10,0", ch: "40" } }
                        ],
                        observacoes: "Aluno com excelente desempenho em Matemática e Ciências."
                    }
                ]
            };

            // Variável para armazenar aluno selecionado
            let alunoSelecionado = null;

            // Carrega alunos quando uma turma é selecionada
            $('#turma').change(function () {
                const turmaSelecionada = $(this).val();
                const alunosContainer = $('#alunos-container');

                if (turmaSelecionada && alunosPorTurma[turmaSelecionada]) {
                    alunosContainer.empty();

                    alunosPorTurma[turmaSelecionada].forEach(aluno => {
                        alunosContainer.append(`
                            <div class="student-card p-3 mb-2" data-id="${aluno.id}">
                                <h6>${aluno.nome}</h6>
                                <small class="text-white">Matrícula: ${aluno.matricula}</small>
                            </div>
                        `);
                    });

                    // Adiciona evento de clique nos cards de aluno
                    $('.student-card').click(function () {
                        $('.student-card').removeClass('selected');
                        $(this).addClass('selected');

                        const alunoId = $(this).data('id');
                        alunoSelecionado = alunosPorTurma[turmaSelecionada].find(a => a.id == alunoId);

                        $('#visualizar-historico').prop('disabled', false);
                    });
                } else {
                    alunosContainer.html('<p class="text-white">Nenhum aluno encontrado para esta turma.</p>');
                    $('#visualizar-historico').prop('disabled', true);
                }
            });

            // Visualizar o histórico
            $('#visualizar-historico').click(function () {
                if (!alunoSelecionado) return;

                // Redireciona para a página do histórico com os parâmetros do aluno
                window.location.href = `visualizarHistorico.php?nome=${encodeURIComponent(alunoSelecionado.nome)}` +
                    `&matricula=${alunoSelecionado.matricula}` +
                    `&inep=${alunoSelecionado.inep}` +
                    `&nascimento=${encodeURIComponent(alunoSelecionado.nascimento)}` +
                    `&nacionalidade=${encodeURIComponent(alunoSelecionado.nacionalidade)}` +
                    `&naturalidade=${encodeURIComponent(alunoSelecionado.naturalidade)}` +
                    `&filiacao=${encodeURIComponent(alunoSelecionado.filiacao)}` +
                    `&nis=${alunoSelecionado.nis}` +
                    `&observacoes=${encodeURIComponent(alunoSelecionado.observacoes)}`;
            });

            // Imprimir o histórico
            $('#imprimir-historico').click(function () {
                const element = document.getElementById('historico-container');

                html2pdf().set({
                    margin: 10,
                    filename: `historico_${alunoSelecionado.matricula}.pdf`,
                    image: { type: 'jpeg', quality: 0.98 },
                    html2canvas: {
                        scale: 2,
                        logging: false,
                        useCORS: true,
                        backgroundColor: null
                    },
                    jsPDF: {
                        unit: 'mm',
                        format: 'a4',
                        orientation: 'portrait',
                        hotfixes: ["px_scaling"]
                    }
                }).from(element).save().then(() => {
                    document.body.style.fontFamily = window.getComputedStyle(document.body).fontFamily;
                });
            });

            // Função para gerar código de verificação aleatório
            function gerarCodigoVerificacao() {
                const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ0123456789';
                let result = '';
                for (let i = 0; i < 6; i++) {
                    result += chars.charAt(Math.floor(Math.random() * chars.length));
                }
                return result;
            }
        });
    </script>
</body>

</html>