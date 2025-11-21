<?php require_once '../includes/bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atestado de Matrícula - SAS</title>
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
                        <h4 class="page-title"><i class="zmdi zmdi-assignment-account mr-2"></i> Atestado de
                            Matrícula</h4>
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
            // Estado
            let alunoSelecionado = null;
            let periodoLetivo = null; // { Data_Inicio, Data_Fim }

            const $ano = $('#ano-letivo');
            const $turno = $('#turno');
            const $turma = $('#turma');
            const $alunosContainer = $('#alunos-container');

            // Carregar anos letivos
            $.getJSON('../includes/ajax/listar_anos_letivos.php', function (resp) {
                if (resp.success) {
                    $ano.empty().append('<option value="">Selecione</option>');
                    resp.data.forEach(ano => $ano.append(`<option value="${ano}">${ano}</option>`));
                }
            });

            // Quando mudar o ano, carrega turmas e período
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

                // período letivo
                $.getJSON('../includes/ajax/listar_periodo_letivo.php', { ano: anoVal }, function(r){
                    periodoLetivo = r && r.success ? r.data : null;
                });

                // carrega turmas
                carregarTurmas(anoVal, $turno.val());
            });

            // Quando mudar o turno, refiltra turmas (se ano selecionado)
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

            // Carrega alunos ao selecionar turma
            $turma.on('change', function(){
                const turmaId = $(this).val();
                alunoSelecionado = null;
                $('#gerar-atestado').prop('disabled', true);
                if (!turmaId) {
                    $alunosContainer.html('<p class="text-white">Selecione uma turma para visualizar os alunos.</p>');
                    return;
                }

                $alunosContainer.html('<p class="text-white">Carregando alunos...</p>');
                $.getJSON('../includes/ajax/listar_alunos_por_turma.php', { turma_id: turmaId }, function(resp){
                    if (!resp.success) {
                        $alunosContainer.html('<p class="text-muted">Erro ao carregar alunos.</p>');
                        return;
                    }
                    const alunos = resp.data || [];
                    if (!alunos.length) {
                        $alunosContainer.html('<p class="text-muted">Nenhum aluno encontrado para esta turma.</p>');
                        return;
                    }

                    const html = alunos.map(a => `
                        <div class="student-card p-3 mb-2 border rounded" data-id="${a.ID_Aluno}" data-matricula="${a.Matricula || ''}" data-nome="${a.Nome_Completo}">
                            <h6>${a.Nome_Completo}</h6>
                            <small class="text-white">Matrícula: ${a.Matricula || '—'}</small>
                        </div>
                    `).join('');
                    $alunosContainer.html(html);

                    // bind click
                    $('.student-card').click(function(){
                        $('.student-card').removeClass('selected');
                        $(this).addClass('selected');
                        alunoSelecionado = {
                            id: $(this).data('id'),
                            nome: $(this).data('nome'),
                            matricula: $(this).data('matricula')
                        };
                        $('#gerar-atestado').prop('disabled', false);
                    });
                });
            });

            // Geração do PDF com dados reais
            $('#gerar-atestado').click(function(){
                if (!alunoSelecionado) return;
                const dataAtual = new Date();
                const dia = String(dataAtual.getDate()).padStart(2, '0');
                const mes = String(dataAtual.getMonth() + 1).padStart(2, '0');
                const anoCorrente = dataAtual.getFullYear();
                const dataFormatada = `${dia}/${mes}/${anoCorrente}`;

                const anoLetivo = $ano.val();
                let periodo = '';
                if (periodoLetivo && periodoLetivo.Data_Inicio && periodoLetivo.Data_Fim) {
                    const di = new Date(periodoLetivo.Data_Inicio);
                    const df = new Date(periodoLetivo.Data_Fim);
                    const fmt = d => `${String(d.getDate()).padStart(2,'0')}/${String(d.getMonth()+1).padStart(2,'0')}/${d.getFullYear()}`;
                    periodo = `${fmt(di)} a ${fmt(df)}`;
                } else {
                    // fallback genérico por ano
                    periodo = `01/02/${anoLetivo} a 15/12/${anoLetivo}`;
                }

                const turmaTexto = $('#turma option:selected').text();
                const turnoTexto = $turno.val() || '—';
                // Se houver Etapa/Curso no nome da turma, já está em turmaTexto

                const pdfContent = `
                    <!DOCTYPE html>
                    <html lang="pt-br">
                    <head>
                        <meta charset="UTF-8">
                        <title>Atestado de Matrícula</title>
                        <style>
                            body { font-family: sans-serif; margin: 40px 60px; font-size: 16px; color: #000; line-height: 1.5; }
                            .cabecalho { text-align: center; font-size: 14px; margin-bottom: 30px; line-height: 1.4; }
                            .titulo { text-align: center; font-weight: bold; font-size: 20px; margin: 30px 0; text-transform: uppercase; }
                            .texto { text-align: justify; line-height: 1.8; margin-bottom: 20px; }
                            .dados { margin: 20px 0; line-height: 1.8; }
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

                            <div class="titulo">ATESTADO DE MATRÍCULA</div>

                            <div class="texto">
                                Atestamos, para os fins que se fizerem necessários, que o(a) estudante <strong>${alunoSelecionado.nome}</strong> possui vínculo regular de matrícula nesta Instituição de Ensino na turma <strong>${turmaTexto}</strong>, turno <strong>${turnoTexto}</strong>, conforme registro acadêmico atualizado.
                            </div>

                            <div class="dados">
                                Matrícula nº: <strong>${alunoSelecionado.matricula || '—'}</strong><br>
                                Período Letivo: <strong>${anoLetivo}</strong><br>
                                Duração: <strong>${periodo}</strong>
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
                    filename: `atestado_matricula_${alunoSelecionado.matricula || 'aluno'}.pdf`,
                    image: { type: 'jpeg', quality: 0.98 },
                    html2canvas: { scale: 2 },
                    jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
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
        });
    </script>
</body>

</html>