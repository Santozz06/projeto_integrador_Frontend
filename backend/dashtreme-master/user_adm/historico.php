<?php require_once '../includes/bootstrap.php'; ?>
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
                                        <option value="">Selecione</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="turno">Turno</label>
                                    <select class="form-control" id="turno">
                                        <option value="">Todos</option>
                                        <option value="MATUTINO">Matutino</option>
                                        <option value="VESPERTINO">Vespertino</option>
                                        <option value="NOTURNO">Noturno</option>
                                        <option value="INTEGRAL">Integral</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="turma">Turma</label>
                                    <select class="form-control" id="turma" disabled>
                                        <option value="">Selecione o ano</option>
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
    <script>
        $(document).ready(function () {
            let alunoSelecionado = null;

            const $ano = $('#ano-letivo');
            const $turno = $('#turno');
            const $turma = $('#turma');
            const $alunos = $('#alunos-container');

            // Carregar anos
            $.getJSON('../includes/ajax/listar_anos_letivos.php', function (resp) {
                if (resp.success) {
                    $ano.empty().append('<option value="">Selecione</option>');
                    resp.data.forEach(ano => $ano.append(`<option value="${ano}">${ano}</option>`));
                } else {
                    console.error('Falha ao carregar anos:', resp.message);
                }
            }).fail(function(jq){
                console.error('Erro ao consultar anos:', jq.statusText || jq.status);
            });

            $ano.on('change', function(){
                const anoVal = $(this).val();
                $turma.prop('disabled', true).empty().append('<option value="">Carregando...</option>');
                $alunos.html('<p class="text-white">Selecione uma turma para visualizar os alunos.</p>');
                $('#visualizar-historico').prop('disabled', true);
                if (!anoVal){
                    $turma.prop('disabled', true).empty().append('<option value="">Selecione o ano</option>');
                    return;
                }
                carregarTurmas(anoVal, $turno.val());
            });

            $turno.on('change', function(){
                const anoVal = $ano.val();
                if (anoVal) carregarTurmas(anoVal, $(this).val());
            });

            function carregarTurmas(ano, turno){
                $turma.prop('disabled', true).empty().append('<option value="">Carregando...</option>');
                $.getJSON('../includes/ajax/listar_turmas.php', { ano, turno }, function(resp){
                    $turma.empty();
                    if (resp.success && resp.data.length){
                        $turma.append('<option value="">Selecione</option>');
                        resp.data.forEach(t => $turma.append(`<option value="${t.ID_Turma}">${t.Nome_Turma} ${t.Etapa ? '('+t.Etapa+')' : ''}</option>`));
                        $turma.prop('disabled', false);
                    } else {
                        $turma.append('<option value="">Nenhuma turma encontrada</option>');
                    }
                }).fail(function(jq){
                    $turma.empty().append('<option value="">Erro ao carregar turmas</option>');
                    console.error('Erro ao consultar turmas:', jq.statusText || jq.status);
                });
            }

            $turma.on('change', function(){
                const turmaId = $(this).val();
                alunoSelecionado = null;
                $('#visualizar-historico').prop('disabled', true);
                if (!turmaId){
                    $alunos.html('<p class="text-white">Selecione uma turma para visualizar os alunos.</p>');
                    return;
                }
                $alunos.html('<p class="text-white">Carregando alunos...</p>');
                $.getJSON('../includes/ajax/listar_alunos_por_turma.php', { turma_id: turmaId }, function(resp){
                    if (!resp.success){
                        $alunos.html('<p class="text-muted">Erro ao carregar alunos.</p>');
                        return;
                    }
                    const lista = resp.data || [];
                    if (!lista.length){
                        $alunos.html('<p class="text-muted">Nenhum aluno encontrado.</p>');
                        return;
                    }
                    const html = lista.map(a => `
                        <div class="student-card p-3 mb-2" data-id="${a.ID_Aluno}" data-mat="${a.Matricula || ''}">
                            <h6>${a.Nome_Completo}</h6>
                            <small class="text-white">Matrícula: ${a.Matricula || '—'}</small>
                        </div>
                    `).join('');
                    $alunos.html(html);
                    $('.student-card').click(function(){
                        $('.student-card').removeClass('selected');
                        $(this).addClass('selected');
                        alunoSelecionado = {
                            ID_Aluno: $(this).data('id'),
                            Matricula: $(this).data('mat')
                        };
                        $('#visualizar-historico').prop('disabled', false);
                    });
                }).fail(function(jq){
                    $alunos.html('<p class="text-muted">Erro de comunicação ao carregar alunos.</p>');
                    console.error('Erro ao consultar alunos:', jq.statusText || jq.status);
                });
            });

            // Visualizar o histórico
            $('#visualizar-historico').click(function () {
                if (!alunoSelecionado) return;
                const id = alunoSelecionado.ID_Aluno;
                window.location.href = `visualizarHistorico.php?aluno_id=${encodeURIComponent(id)}`;
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