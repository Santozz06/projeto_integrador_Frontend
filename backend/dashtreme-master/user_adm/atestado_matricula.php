<?php require_once '../includes/bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <link rel="icon" href="../assets/images/favicon.ico" type="image/x-icon">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atestado de Matrícula - SAS</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/app-style.css">
    <link rel="stylesheet" href="../assets/css/icons.css">
    <link rel="stylesheet" href="../assets/css/sidebar-menu.css">
    <link rel="stylesheet" href="../css/style.css">
</head>

<body class="bg-theme bg-theme1 user_adm_atestadoMatricula">
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
            let periodoLetivo = null;

            const $ano = $('#ano-letivo');
            const $turno = $('#turno');
            const $turma = $('#turma');
            const $alunosContainer = $('#alunos-container');

            // Carregar anos letivos
            $.getJSON('../includes/ajax/shared/academico/listar_anos_letivos.php', function (resp) {
                if (resp.success) {
                    $ano.empty().append('<option value="">Selecione</option>');
                    resp.data.forEach(ano => $ano.append(`<option value="${ano}">${ano}</option>`));
                } else {
                    console.error('Erro ao carregar anos letivos:', resp);
                }
            });

            // Quando mudar o ano, carrega turmas e período
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

                // período letivo
                $.getJSON('../includes/ajax/shared/academico/listar_periodo_letivo.php', { ano: anoVal }, function (r) {
                    if (r && r.success) {
                        periodoLetivo = r.data;
                    } else {
                        periodoLetivo = null;
                        console.error('Erro ao carregar período letivo:', r);
                    }
                });

                // carrega turmas
                carregarTurmas(anoVal, $turno.val());
            });

            // Quando mudar o turno, refiltra turmas (se ano selecionado)
            $turno.on('change', function () {
                const anoVal = $ano.val();
                if (anoVal) carregarTurmas(anoVal, $(this).val());
            });

            function carregarTurmas(ano, turno) {
                $turma.prop('disabled', true).empty().append('<option value="">Carregando...</option>');
                $.getJSON('../includes/ajax/admin/turmas/listar_turmas.php', { ano, turno }, function (resp) {
                    if (resp.success && resp.data.length) {
                        $turma.empty().append('<option value="">Selecione</option>');
                        resp.data.forEach(t => $turma.append(`<option value="${t.ID_Turma}">${t.Nome_Turma} ${t.Etapa ? '(' + t.Etapa + ')' : ''}</option>`));
                        $turma.prop('disabled', false);
                    } else {
                        $turma.empty().append('<option value="">Nenhuma turma encontrada</option>');
                        console.error('Erro ao carregar turmas:', resp);
                    }
                }).fail(function (jqXHR, textStatus, errorThrown) {
                    console.error('Erro na requisição de turmas:', textStatus, errorThrown);
                    $turma.empty().append('<option value="">Erro ao carregar turmas</option>');
                });
            }

            // Carrega alunos ao selecionar turma
            $turma.on('change', function () {
                const turmaId = $(this).val();
                alunoSelecionado = null;
                $('#gerar-atestado').prop('disabled', true);
                if (!turmaId) {
                    $alunosContainer.html('<p class="text-white">Selecione uma turma para visualizar os alunos.</p>');
                    return;
                }

                $alunosContainer.html('<p class="text-white">Carregando alunos...</p>');
                $.getJSON('../includes/ajax/admin/turmas/listar_alunos_por_turma.php', { turma_id: turmaId }, function (resp) {
                    if (!resp.success) {
                        $alunosContainer.html('<p class="text-muted">Erro ao carregar alunos.</p>');
                        console.error('Erro ao carregar alunos:', resp);
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
                    $('.student-card').click(function () {
                        $('.student-card').removeClass('selected');
                        $(this).addClass('selected');
                        alunoSelecionado = {
                            id: $(this).data('id'),
                            nome: $(this).data('nome'),
                            matricula: $(this).data('matricula')
                        };
                        $('#gerar-atestado').prop('disabled', false);
                    });
                }).fail(function (jqXHR, textStatus, errorThrown) {
                    console.error('Erro na requisição de alunos:', textStatus, errorThrown);
                    $alunosContainer.html('<p class="text-muted">Erro ao carregar alunos.</p>');
                });
            });

            // Geração do PDF com dados reais
            $('#gerar-atestado').click(function () {
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
                    const fmt = d => `${String(d.getDate()).padStart(2, '0')}/${String(d.getMonth() + 1).padStart(2, '0')}/${d.getFullYear()}`;
                    periodo = `${fmt(di)} a ${fmt(df)}`;
                } else {
                    periodo = `01/02/${anoLetivo} a 15/12/${anoLetivo}`;
                }

                const turmaTexto = $('#turma option:selected').text();
                const turnoTexto = $turno.val() || '—';
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
                    ATESTADO DE MATRÍCULA
                </div>

                <!-- Texto principal -->
                <div style="
                    text-align: justify; 
                    margin-bottom: 40px; 
                    line-height: 1.8;
                    font-size: 16px;
                ">
                    Atestamos, para os fins que se fizerem necessários, que o(a) estudante 
                    <strong style="font-weight: bold;">${alunoSelecionado.nome}</strong> 
                    possui vínculo regular de matrícula nesta Instituição de Ensino na turma 
                    <strong style="font-weight: bold;">${turmaTexto}</strong>, 
                    turno <strong style="font-weight: bold;">${turnoTexto}</strong>, 
                    conforme registro acadêmico atualizado.
                </div>

                <!-- Dados do aluno -->
                <div style="
                    margin: 40px 0; 
                    line-height: 2.0;
                    font-size: 16px;
                    padding-left: 20px;
                ">
                    <div><strong style="font-weight: bold;">Matrícula nº:</strong> ${alunoSelecionado.matricula || '—'}</div>
                    <div><strong style="font-weight: bold;">Período Letivo:</strong> ${anoLetivo}</div>
                    <div><strong style="font-weight: bold;">Duração:</strong> ${periodo}</div>
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
                            filename: `atestado_matricula_${alunoSelecionado.matricula || 'aluno'}.pdf`,
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
        });
    </script>
</body>

</html>