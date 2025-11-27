<?php require_once '../includes/bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <link rel="icon" href="../assets/images/favicon.ico" type="image/x-icon">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atestado de Frequência - SAS</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/app-style.css">
    <link rel="stylesheet" href="../assets/css/icons.css">
    <link rel="stylesheet" href="../assets/css/sidebar-menu.css">
    <link rel="stylesheet" href="../css/style.css">
</head>

<body class="bg-theme bg-theme1 user_adm_atestadoFrequencia">
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
            let mapaFrequencia = [];

            const $ano = $('#ano-letivo');
            const $turno = $('#turno');
            const $turma = $('#turma');
            const $inicio = $('#data-inicio');
            const $fim = $('#data-fim');
            const $alunosContainer = $('#alunos-container');

            // Carrega anos letivos
            $.getJSON('../includes/ajax/shared/academico/listar_anos_letivos.php', function (resp) {
                if (resp.success) {
                    $ano.empty().append('<option value="">Selecione</option>');
                    resp.data.forEach(ano => $ano.append(`<option value="${ano}">${ano}</option>`));
                }
            });

            // Ao mudar Ano
            $ano.on('change', function () {
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
            $turno.on('change', function () {
                const anoVal = $ano.val();
                if (anoVal) carregarTurmas(anoVal, $(this).val());
            });

            function carregarTurmas(ano, turno) {
                $turma.prop('disabled', true).empty().append('<option value="">Carregando...</option>');
                $.getJSON('../includes/ajax/admin/turmas/listar_turmas.php', { ano, turno }, function (resp) {
                    $turma.empty();
                    if (resp.success && resp.data.length) {
                        $turma.append('<option value="">Selecione</option>');
                        resp.data.forEach(t => $turma.append(`<option value="${t.ID_Turma}">${t.Nome_Turma} ${t.Etapa ? '(' + t.Etapa + ')' : ''}</option>`));
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

            function carregarAlunos() {
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

                $.getJSON('../includes/ajax/admin/turmas/listar_frequencia_por_turma.php', params, function (resp) {
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

                    $('.student-card').click(function () {
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
            $('#gerar-atestado').click(function () {
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
                const perc = alunoSelecionado.Percentual != null ? alunoSelecionado.Percentual : (total > 0 ? Math.round((presencas / total) * 100) : 0);

                // Usar o container que já existe
                const pdfContainer = $('#pdf-container');

                // Conteúdo do PDF com CSS inline para garantir renderização
                const pdfContent = `
        <div id="doc" style="
            width: 210mm; 
            min-height: 297mm; 
            padding: 25mm; 
            font-family: 'Times New Roman', serif; 
            font-size: 16px; 
            line-height: 1.6; 
            color: #000000;
            margin: 0 auto;
            box-sizing: border-box;
        ">
            <!-- Cabeçalho -->
            <div style="
                text-align: center; 
                margin-bottom: 40px; 
                line-height: 1.4;
                font-size: 15px;
            ">
                <div>República Federativa do Brasil</div>
                <div>Ministério da Educação</div>
                <div style="font-weight: bold; margin-top: 5px;">Escola</div>
            </div>

            <!-- Título -->
            <div style="
                text-align: center; 
                font-weight: bold; 
                font-size: 22px; 
                margin: 60px 0; 
                text-transform: uppercase;
                letter-spacing: 1.5px;
            ">
                ATESTADO DE FREQUÊNCIA
            </div>

            <!-- Texto principal -->
            <div style="
                text-align: justify; 
                margin-bottom: 40px; 
                line-height: 1.8;
                font-size: 16px;
            ">
                Atestamos, para os fins que se fizerem necessários, que o(a) estudante 
                <strong style="font-weight: bold;">${alunoSelecionado.Nome_Completo}</strong>,
                matrícula nº <strong style="font-weight: bold;">${alunoSelecionado.Matricula || '—'}</strong>, 
                matriculado(a) na turma <strong style="font-weight: bold;">${turmaTexto}</strong>, 
                apresentou a seguinte frequência ${periodoTexto}:
            </div>

            <!-- Tabela de frequência -->
            <div style="margin: 40px 0;">
                <table style="
                    width: 100%; 
                    border-collapse: collapse; 
                    margin: 20px 0;
                    font-size: 15px;
                ">
                    <thead>
                        <tr>
                            <th style="border: 1px solid #000; padding: 12px; text-align: center; background-color: #f8f9fa; font-weight: bold;">Total de Aulas</th>
                            <th style="border: 1px solid #000; padding: 12px; text-align: center; background-color: #f8f9fa; font-weight: bold;">Presenças</th>
                            <th style="border: 1px solid #000; padding: 12px; text-align: center; background-color: #f8f9fa; font-weight: bold;">Faltas</th>
                            <th style="border: 1px solid #000; padding: 12px; text-align: center; background-color: #f8f9fa; font-weight: bold;">Frequência</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="border: 1px solid #000; padding: 12px; text-align: center;">${total}</td>
                            <td style="border: 1px solid #000; padding: 12px; text-align: center;">${presencas}</td>
                            <td style="border: 1px solid #000; padding: 12px; text-align: center;">${faltas}</td>
                            <td style="border: 1px solid #000; padding: 12px; text-align: center; font-weight: bold;">${perc}%</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Texto final -->
            <div style="
                text-align: justify; 
                margin-bottom: 40px; 
                line-height: 1.8;
                font-size: 16px;
            ">
                Este documento atesta a assiduidade do(a) estudante no período informado, conforme registros acadêmicos.
            </div>

            <!-- Espaço para assinatura -->
            <div style="margin-top: 80px; text-align: center;">
                <div style="margin-bottom: 60px; border-bottom: 1px solid #000; width: 300px; margin-left: auto; margin-right: auto; padding-bottom: 5px;">
                    <!-- Linha para assinatura -->
                </div>
                <div style="font-size: 14px;">
                    Coordenador(a) de Registros Acadêmicos
                </div>
            </div>

            <!-- Data e local -->
            <div style="
                margin-top: 40px; 
                text-align: right;
                font-size: 15px;
            ">
                <strong style="font-weight: bold;">Parobé - RS, ${dataFormatada}</strong>
            </div>

            <!-- Rodapé com autenticação -->
            <div style="
                margin-top: 100px; 
                font-size: 12px; 
                text-align: center; 
                color: #555555;
                line-height: 1.4;
            ">
                <div>Para verificar a autenticidade deste documento, acesse:</div>
                <div style="margin: 5px 0;">http://meusite.com/autenticacao</div>
                <div style="margin-top: 10px; font-weight: bold;">
                    Código de verificação: ${gerarCodigoVerificacao()}
                </div>
            </div>
        </div>
    `;

                pdfContainer.show().html(pdfContent);

                setTimeout(() => {
                    const element = document.getElementById('doc');

                    if (element) {
                        const options = {
                            margin: 0,
                            filename: `atestado_frequencia_${alunoSelecionado.Matricula || 'aluno'}.pdf`,
                            image: {
                                type: 'jpeg',
                                quality: 1
                            },
                            html2canvas: {
                                scale: 2,
                                useCORS: true,
                                logging: false,
                                letterRendering: true
                            },
                            jsPDF: {
                                unit: 'mm',
                                format: 'a4',
                                orientation: 'portrait'
                            }
                        };

                        html2pdf().set(options).from(element).save().then(() => {
                            pdfContainer.hide();
                        });
                    }
                }, 100);
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

            function formatarDataBR(iso) {
                const d = new Date(iso);
                const dd = String(d.getDate()).padStart(2, '0');
                const mm = String(d.getMonth() + 1).padStart(2, '0');
                const yy = d.getFullYear();
                return `${dd}/${mm}/${yy}`;
            }
        });
    </script>
</body>

</html>