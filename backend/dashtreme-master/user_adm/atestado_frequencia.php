<?php require_once '../includes/bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atestado de Frequência - Dashboard Acadêmico</title>
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

        .year-option {
            cursor: pointer;
            transition: all 0.3s ease;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 10px;
            border: 1px solid rgba(0, 0, 0, 0.1);
            background-color: rgba(255, 255, 255, 0.05);
            display: block;
            text-decoration: none !important;
        }

        .year-option:hover {
            background-color: rgba(0, 0, 0, 0.03);
            border-color: #2196F3;
        }

        .year-title {
            font-weight: 600;
            color: #333;
            display: inline-block;
        }

        .year-status {
            float: right;
        }

        .badge-info {
            background-color: #36b9cc;
            color: #fff;
        }

        .badge-success {
            background-color: #1cc88a;
            color: #fff;
        }

        @media print {
            .no-print {
                display: none !important;
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
                        <h4 class="page-title"><i class="zmdi zmdi-time-countdown mr-2"></i> Atestado de Frequência
                        </h4>
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
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="data-inicio">Data início (opcional)</label>
                                    <input type="date" class="form-control" id="data-inicio" />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="data-fim">Data fim (opcional)</label>
                                    <input type="date" class="form-control" id="data-fim" />
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

                    <!-- Botão de Gerar -->
                    <div class="text-center btn-generate">
                        <button id="gerar-atestado" class="btn btn-custom-print" disabled>
                            <i class="zmdi zmdi-file-text mr-2"></i> Gerar Atestado
                        </button>
                    </div>
                </div>

                <!-- Container para o PDF (inicialmente oculto) -->
                <div id="pdf-container" style="display:none;"></div>
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
            let mapaFrequencia = []; // cache dos dados da turma

            const $ano = $('#ano-letivo');
            const $turno = $('#turno');
            const $turma = $('#turma');
            const $inicio = $('#data-inicio');
            const $fim = $('#data-fim');
            const $alunosContainer = $('#alunos-container');

            // Carrega anos letivos
            $.getJSON('../includes/ajax/listar_anos_letivos.php', function (resp) {
                if (resp.success) {
                    $ano.empty().append('<option value="">Selecione</option>');
                    resp.data.forEach(ano => $ano.append(`<option value="${ano}">${ano}</option>`));
                }
            });

            // Ao mudar Ano
            $ano.on('change', function(){
                const anoVal = $(this).val();
                $turma.prop('disabled', true).empty().append('<option value="">Carregando...</option>');
                alunoSelecionado = null;
                $('#gerar-atestado').prop('disabled', true);
                $alunosContainer.html('<p class="text-white">Selecione uma turma para visualizar os alunos.</p>');
                if (!anoVal) {
                    $turma.prop('disabled', true).empty().append('<option value="">Selecione o ano</option>');
                    return;
                }
                carregarTurmas(anoVal, $turno.val());
            });

            // Ao mudar Turno
            $turno.on('change', function(){
                const anoVal = $ano.val();
                if (anoVal) carregarTurmas(anoVal, $(this).val());
            });

            function carregarTurmas(ano, turno){
                $turma.prop('disabled', true).empty().append('<option value="">Carregando...</option>');
                $.getJSON('../includes/ajax/listar_turmas.php', { ano, turno }, function (resp) {
                    $turma.empty();
                    if (resp.success && resp.data.length) {
                        $turma.append('<option value="">Selecione</option>');
                        resp.data.forEach(t => $turma.append(`<option value="${t.ID_Turma}">${t.Nome_Turma} ${t.Etapa ? '('+t.Etapa+')' : ''}</option>`));
                        $turma.prop('disabled', false);
                    } else {
                        $turma.append('<option value="">Nenhuma turma encontrada</option>');
                    }
                });
            }

            // Recarregar alunos quando turma/datas mudarem
            $turma.on('change', carregarAlunos);
            $inicio.on('change', carregarAlunos);
            $fim.on('change', carregarAlunos);

            function carregarAlunos(){
                const turmaId = $turma.val();
                alunoSelecionado = null;
                $('#gerar-atestado').prop('disabled', true);
                if (!turmaId) {
                    $alunosContainer.html('<p class="text-white">Selecione uma turma para visualizar os alunos.</p>');
                    return;
                }
                $alunosContainer.html('<p class="text-white">Carregando alunos...</p>');

                const params = { turma_id: turmaId };
                if ($inicio.val()) params.data_inicio = $inicio.val();
                if ($fim.val()) params.data_fim = $fim.val();

                $.getJSON('../includes/ajax/listar_frequencia_por_turma.php', params, function(resp){
                    if (!resp.success) {
                        $alunosContainer.html('<p class="text-muted">Erro ao carregar frequências.</p>');
                        return;
                    }
                    const alunos = resp.data || [];
                    mapaFrequencia = alunos;
                    if (!alunos.length) {
                        $alunosContainer.html('<p class="text-muted">Nenhum registro de frequência encontrado.</p>');
                        return;
                    }
                    const html = alunos.map(a => `
                        <div class="student-card p-3 mb-2" data-id="${a.Matricula}">
                            <h6>${a.Nome_Completo}</h6>
                            <small class="text-white">Matrícula: ${a.Matricula || '—'} | Frequência: ${a.Percentual}% (P: ${a.Presentes || 0} • F: ${a.Faltas || 0} • Total: ${a.Total_Registros || 0})</small>
                        </div>
                    `).join('');
                    $alunosContainer.html(html);

                    $('.student-card').click(function(){
                        $('.student-card').removeClass('selected');
                        $(this).addClass('selected');
                        const id = $(this).data('id');
                        const found = mapaFrequencia.find(x => String(x.Matricula) === String(id));
                        if (found) {
                            alunoSelecionado = found;
                            $('#gerar-atestado').prop('disabled', false);
                        }
                    });
                });
            }

            // Gera o atestado em PDF com dados reais
            $('#gerar-atestado').click(function(){
                if (!alunoSelecionado) return;

                const dataAtual = new Date();
                const dia = String(dataAtual.getDate()).padStart(2, '0');
                const mes = String(dataAtual.getMonth() + 1).padStart(2, '0');
                const anoCorrente = dataAtual.getFullYear();
                const dataFormatada = `${dia}/${mes}/${anoCorrente}`;

                const periodoTexto = ($inicio.val() && $fim.val())
                    ? `no período de ${formatarDataBR($inicio.val())} a ${formatarDataBR($fim.val())}`
                    : `no ano letivo de ${$ano.val()}`;

                const turmaTexto = $('#turma option:selected').text();

                const total = parseInt(alunoSelecionado.Total_Registros || 0, 10);
                const presencas = parseInt(alunoSelecionado.Presentes || 0, 10);
                const faltas = parseInt(alunoSelecionado.Faltas || 0, 10);
                const perc = alunoSelecionado.Percentual != null ? alunoSelecionado.Percentual : (total > 0 ? Math.round((presencas/total)*100) : 0);

                const pdfContent = `
                    <!DOCTYPE html>
                    <html lang="pt-br">
                    <head>
                        <meta charset="UTF-8">
                        <title>Atestado de Frequência</title>
                        <style>
                            body { font-family: Arial, sans-serif; margin: 40px 60px; font-size: 16px; color: #000; line-height: 1.5; }
                            .cabecalho { text-align: center; font-size: 14px; margin-bottom: 30px; line-height: 1.4; }
                            .titulo { text-align: center; font-weight: bold; font-size: 20px; margin: 30px 0; text-transform: uppercase; }
                            .texto { text-align: justify; line-height: 1.8; margin-bottom: 20px; }
                            .dados { margin: 20px 0; line-height: 1.8; }
                            .tabela-frequencia { width: 100%; border-collapse: collapse; margin: 20px 0; }
                            .tabela-frequencia th, .tabela-frequencia td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                            .tabela-frequencia th { background-color: #f2f2f2; }
                            .rodape { margin-top: 40px; text-align: right; }
                            .autenticidade { margin-top: 30px; font-size: 14px; text-align: center; }
                            .codigo { font-weight: bold; margin-top: 10px; text-align: center; }
                            strong { font-weight: bold; }
                        </style>
                    </head>
                    <body>
                        <div id="doc">
                            <div class="cabecalho">
                                República Federativa do Brasil<br>
                                Ministério da Educação<br>
                                Secretaria de Educação Profissional e Tecnológica<br>
                                Instituto Federal de Educação, Ciência e Tecnologia<br>
                                Campus Parobé
                            </div>

                            <div class="titulo">ATESTADO DE FREQUÊNCIA</div>

                            <div class="texto">
                                Atestamos, para os fins que se fizerem necessários, que o(a) estudante <strong>${alunoSelecionado.Nome_Completo}</strong>,
                                matrícula nº <strong>${alunoSelecionado.Matricula || '—'}</strong>, matriculado(a) na turma <strong>${turmaTexto}</strong>, apresentou a seguinte frequência ${periodoTexto}:
                            </div>

                            <table class="tabela-frequencia">
                                <thead>
                                    <tr>
                                        <th>Total de Aulas</th>
                                        <th>Presenças</th>
                                        <th>Faltas</th>
                                        <th>Frequência</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>${total}</td>
                                        <td>${presencas}</td>
                                        <td>${faltas}</td>
                                        <td>${perc}%</td>
                                    </tr>
                                </tbody>
                            </table>

                            <div class="texto">
                                Este documento atesta a assiduidade do(a) estudante no período informado, conforme registros acadêmicos.
                            </div>

                            <div class="rodape">
                                <strong>Parobé - RS, ${dataFormatada}</strong>
                            </div>

                            <div class="autenticidade">
                                Para verificar a autenticidade deste documento, acesse:<br>
                                <a href="#" target="_blank">http://meusite.com/autenticacao</a>
                            </div>

                            <div class="codigo">
                                Código de verificação: <span>${gerarCodigoVerificacao()}</span>
                            </div>
                        </div>
                    </body>
                    </html>
                `;

                const pdfContainer = $('#pdf-container');
                pdfContainer.html(pdfContent);
                const element = pdfContainer.find('#doc')[0];

                html2pdf().set({
                    margin: 10,
                    filename: `atestado_frequencia_${alunoSelecionado.Matricula || 'aluno'}.pdf`,
                    image: { type: 'jpeg', quality: 0.98 },
                    html2canvas: { scale: 2, logging: false, useCORS: true, backgroundColor: null },
                    jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait', hotfixes: ["px_scaling"] }
                }).from(element).save();
            });

            function gerarCodigoVerificacao() {
                const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ0123456789';
                let result = '';
                for (let i = 0; i < 12; i++) {
                    if (i > 0 && i % 4 === 0) result += '-';
                    result += chars.charAt(Math.floor(Math.random() * chars.length));
                }
                return result;
            }

            function formatarDataBR(iso){
                const d = new Date(iso);
                const dd = String(d.getDate()).padStart(2,'0');
                const mm = String(d.getMonth()+1).padStart(2,'0');
                const yy = d.getFullYear();
                return `${dd}/${mm}/${yy}`;
            }
        });
    </script>
</body>

</html>