<?php require_once '../includes/bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <link rel="icon" href="../assets/images/favicon.ico" type="image/x-icon">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Frequência por Turma - SAS</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/app-style.css">
    <link rel="stylesheet" href="../assets/css/icons.css">
    <link rel="stylesheet" href="../assets/css/sidebar-menu.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>

<body class="bg-theme bg-theme1 user_adm_frequenciaPorTurma">
    <?php
    require("menu_padrão.php");
    ?>


    <!-- Conteúdo principal -->
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="header-print">
                <img src="../assets/images/logo-icon.png" alt="Logo" style="height: 50px;">
                <h3>Relatório de Frequência por Turma</h3>
                <p id="print-date" class="text-muted"></p>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-3 no-print">
                <h4 class="page-title"><i class="zmdi zmdi-time-countdown mr-2"></i> Frequência por Turma</h4>
                <div>
                    <button id="print-btn" class="btn btn-custom-print">
                        <i class="zmdi zmdi-print mr-2"></i>Imprimir/PDF
                    </button>
                </div>
            </div>

            <div class="filter-section no-print">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="year-select">Ano Letivo</label>
                            <select class="form-control" id="year-select">
                                <option value="">Selecione</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="grade-select">Turma</label>
                            <select class="form-control" id="grade-select" disabled>
                                <option value="">Selecione o ano primeiro</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div id="attendance-container">

                <!-- Lista de alunos e frequência -->
                <div class="card">
                    <div class="card-body">
                        <div id="empty-state" class="empty-state">
                            <i class="zmdi zmdi-search"></i>
                            <h4>Selecione uma turma para visualizar a frequência</h4>
                            <p class="text-muted">Escolha uma turma e filtros acima para exibir os registros de
                                frequência.</p>
                        </div>

                        <div id="attendance-list" style="display: none;">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 id="class-title" class="mb-0"></h5>
                                <div>
                                    <div id="attendance-summary" class="summary-text text-white mt-2"></div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-hover" id="attendance-table">
                                    <thead>
                                        <tr>
                                            <th>Aluno</th>
                                            <th>Matrícula</th>
                                            <th>Presenças</th>
                                            <th>Faltas</th>
                                            <th>Faltas Justificadas</th>
                                            <th>% Frequência</th>
                                        </tr>
                                    </thead>
                                    <tbody id="attendance-data">
                                        <!-- Dados serão carregados aqui -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="overlay toggle-menu"></div>

    <script src="../assets/js/jquery.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script src="../assets/plugins/simplebar/js/simplebar.js"></script>
    <script src="../assets/js/sidebar-menu.js"></script>
    <script src="../assets/js/app-script.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>

    <script>
        $(document).ready(function () {
            // Atualiza a data para impressão
            const now = new Date();
            $('#print-date').text('Emitido em: ' + now.toLocaleDateString() + ' às ' + now.toLocaleTimeString());

            const $ano = $('#year-select');
            const $turma = $('#grade-select');

            function carregarAnos() {
                $.getJSON('../includes/ajax/shared/academico/listar_anos_letivos.php', function (resp) {
                    if (resp.success) {
                        $ano.empty().append('<option value="">Selecione</option>');
                        resp.data.forEach(ano => $ano.append(`<option value="${ano}">${ano}</option>`));
                    }
                });
            }

            function carregarTurmas(ano) {
                $turma.prop('disabled', true).empty().append('<option value="">Carregando...</option>');
                $.getJSON('../includes/ajax/admin/turmas/listar_turmas.php', { ano }, function (resp) {
                    $turma.empty();
                    if (resp.success && resp.data.length) {
                        $turma.append('<option value="">Selecione</option>');
                        resp.data.forEach(t => $turma.append(`<option value="${t.ID_Turma}">${t.Nome_Turma} (${t.Etapa || ''})</option>`));
                        $turma.prop('disabled', false);
                    } else {
                        $turma.append('<option value="">Nenhuma turma encontrada</option>');
                    }
                });
            }

            $ano.on('change', function(){
                const val = $(this).val();
                if (val) carregarTurmas(val);
            });

            $turma.on('change', function () {
                const turmaId = $(this).val();
                if (turmaId) {
                    $('#empty-state').hide();
                    $('#attendance-list').show();
                    const classText = $('#grade-select option:selected').text();
                    $('#class-title').text('Frequência da Turma: ' + classText);
                    loadAttendanceData(turmaId);
                } else {
                    $('#empty-state').show();
                    $('#attendance-list').hide();
                }
            });

            // Função para carregar dados de frequência
            function loadAttendanceData(turmaId) {
                // Destrói a DataTable se já existir
                if ($.fn.DataTable.isDataTable('#attendance-table')) {
                    $('#attendance-table').DataTable().destroy();
                }

                // Limpa a tabela
                $('#attendance-table tbody').empty();
                $.getJSON('../includes/ajax/admin/turmas/listar_frequencia_por_turma.php', { turma_id: turmaId }, function(resp){
                    if (!resp.success) {
                        $('#attendance-data').html('<tr><td colspan="6" class="text-center">Erro ao carregar frequência</td></tr>');
                        return;
                    }
                    const alunos = resp.data;
                    if (!alunos.length) {
                        $('#attendance-data').html('<tr><td colspan="6" class="text-center">Nenhum dado de frequência disponível</td></tr>');
                        return;
                    }

                    let totalPresent = 0;
                    let totalAbsent = 0;
                    let totalJustified = 0; 

                    let tableContent = '';
                        alunos.forEach(a => {
                        const perc = a.Percentual;
                        totalPresent += parseInt(a.Presentes || 0, 10);
                        totalAbsent += parseInt(a.Faltas || 0, 10);
                        totalJustified += parseInt(a.Justificadas || 0, 10);
                        const iniciais = (function(name){
                            if (!name) return '';
                            const parts = name.trim().split(/\s+/);
                            const first = parts[0] ? parts[0][0] : '';
                            const last = parts.length > 1 ? parts[parts.length-1][0] : '';
                            return (first + last).toUpperCase();
                        })(a.Nome_Completo);
                        tableContent += `
                            <tr>
                                <td>
                                        <div class=\"aluno-info\">
                                            <div class=\"aluno-avatar\">${iniciais}</div>
                                            ${a.Nome_Completo}
                                        </div>
                                </td>
                                <td>${a.Matricula || ''}</td>
                                <td>${a.Presentes || 0}</td>
                                <td>${a.Faltas || 0}</td>
                                <td>${a.Justificadas || 0}</td>
                                <td>
                                    <span class="d-block d-print-none">
                                        <div class="progress" style="height: 20px;">
                                        <div class="progress-bar ${getPercentageColor(perc)}"
                                            role="progressbar" style="width: ${perc}%"
                                            aria-valuenow="${perc}" aria-valuemin="0" aria-valuemax="100">
                                            ${perc}%
                                        </div>
                                        </div>
                                    </span>
                                    <span class="d-none d-print-block">${perc}%</span>
                                </td>
                            </tr>
                        `;
                    });
                    $('#attendance-data').html(tableContent);
                    $('#attendance-summary').html(`
                        ${alunos.length} alunos | Presenças: ${totalPresent} | Faltas: ${totalAbsent} | Justificadas: ${totalJustified}
                    `);

                    $('#attendance-table').DataTable({
                        responsive: true,
                        language: { url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/pt-BR.json' },
                        dom: '<"top"f>rt<"bottom"lip><"clear">'
                    });
                });
            }



            // Helper para cor da porcentagem
            function getPercentageColor(percentage) {
                if (percentage >= 90) return 'bg-success';
                if (percentage >= 70) return 'bg-warning';
                return 'bg-danger';
            }

            // Função para imprimir
            $('#print-btn').click(function () {
                if ($('#grade-select').val()) {
                    // Configurações para impressão
                    const originalTitle = document.title;
                    document.title = 'Relatório de Frequência - ' + $('#grade-select option:selected').text();

                    // Esconde elementos não necessários
                    const $hideOnPrint = $('.no-print, .dataTables_info, .dataTables_paginate, .dataTables_length, .dataTables_filter');
                    const $menuElems = $('#sidebar-wrapper, .topbar-nav, .navbar');
                    $hideOnPrint.hide();
                    $menuElems.hide(); // garante ocultar menu/lateral/topo

                    // Força estilos de impressão
                    $('body').addClass('printing');

                    // impressão
                    setTimeout(() => {
                        window.print();
                        $hideOnPrint.show();
                        $menuElems.show();
                        $('body').removeClass('printing');
                        document.title = originalTitle;
                    }, 300);
                } else {
                    alert('Por favor, selecione uma turma antes de gerar o relatório.');
                }
            });
            // Inicialização
            carregarAnos();
        });
    </script>
</body>

</html>