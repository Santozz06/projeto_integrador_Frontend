<?php require_once '../includes/bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alunos por Turma - SAS (Sistema Academico Santos)</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/app-style.css">
    <link rel="stylesheet" href="../assets/css/icons.css">
    <link rel="stylesheet" href="../assets/css/sidebar-menu.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>

<body class="bg-theme bg-theme1 user_adm_alunosPorTurma">

<?php
    require("menu_padrão.php");
?>

        <!-- Conteúdo principal -->
        <div class="content-wrapper">
            <div class="container-fluid">
                <div class="header-print">
                    <img src="../assets/images/logo-icon.png" alt="Logo" style="height: 50px;">
                    <h3>Lista de Alunos por Turma</h3>
                    <p id="print-date" class="text-muted"></p>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3 no-print">
                    <h4 class="page-title"><i class="zmdi zmdi-accounts mr-2"></i> Alunos por Turma</h4>
                    <div>
                        <button id="print-btn" class="btn btn-custom-secondary">
                            <i class="zmdi zmdi-print mr-2"></i>Imprimir/PDF
                        </button>
                    </div>
                </div>

                <div class="filter-section no-print">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="year-select">Ano Letivo</label>
                                <select class="form-control" id="year-select">
                                    <option value="">Selecione o ano</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="grade-select">Série/Turma</label>
                                <select class="form-control" id="grade-select" disabled>
                                    <option value="">Selecione o ano primeiro</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="shift-select">Turno</label>
                                <select class="form-control" id="shift-select">
                                    <option value="">Todos</option>
                                    <option>Matutino</option>
                                    <option>Vespertino</option>
                                    <option>Integral</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="students-container">
                    <!-- Estado inicial - antes de selecionar turma -->
                    <div class="empty-state" id="empty-state">
                        <i class="zmdi zmdi-search"></i>
                        <h4>Selecione uma turma para visualizar os alunos</h4>
                        <p class="text-muted">Escolha uma série/turma no filtro acima para exibir a lista de alunos
                            matriculados.</p>
                    </div>

                    <!-- Lista de alunos -->
                    <div id="students-list" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 id="class-title" class="mb-0"></h5>
                            <span id="students-count" class="badge badge-primary"></span>
                        </div>

                        <div class="row" id="students-grid">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="overlay toggle-menu"></div>

        <footer class="footer no-print">
            <div class="container">
                <div class="text-center text-white">
                    Copyright © 2023 SAS (Sistema Academico Santos)
                </div>
            </div>
        </footer>
    </div>


    <script src="../assets/js/jquery.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script src="../assets/plugins/simplebar/js/simplebar.js"></script>
    <script src="../assets/js/sidebar-menu.js"></script>
    <script src="../assets/js/app-script.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    

    <script>
        $(document).ready(function () {
            // Atualiza a data para impressão
            const now = new Date();
            $('#print-date').text('Emitido em: ' + now.toLocaleDateString() + ' às ' + now.toLocaleTimeString());

            // Carregar anos dinamicamente
            $.getJSON('../includes/ajax/listar_anos_letivos.php', function (resp) {
                if (resp.success) {
                    const $year = $('#year-select');
                    resp.data.forEach(ano => {
                        $year.append(`<option value="${ano}">${ano}</option>`);
                    });
                }
            });

            // Ao mudar ano ou turno, carregar turmas
            function carregarTurmas() {
                const ano = $('#year-select').val();
                const turno = $('#shift-select').val();
                const $turma = $('#grade-select');

                $turma.prop('disabled', true).empty();
                if (!ano) {
                    $turma.append('<option value="">Selecione o ano primeiro</option>');
                    return;
                }

                $turma.append('<option value="">Carregando...</option>');
                $.getJSON('../includes/ajax/listar_turmas.php', { ano, turno }, function (resp) {
                    $turma.empty();
                    if (resp.success && resp.data.length) {
                        $turma.append('<option value="">Selecione uma turma</option>');
                        resp.data.forEach(t => {
                            const label = `${t.Nome_Turma} - ${t.Ano_Letivo}${t.Turno ? ' - ' + t.Turno : ''}`;
                            $turma.append(`<option value="${t.ID_Turma}">${label}</option>`);
                        });
                        $turma.prop('disabled', false);
                    } else {
                        $turma.append('<option value="">Nenhuma turma encontrada</option>');
                    }
                });
            }

            $('#year-select').on('change', function(){
                $('#empty-state').show();
                $('#students-list').hide();
                carregarTurmas();
            });
            $('#shift-select').on('change', carregarTurmas);

            // Ao selecionar turma, carregar alunos
            $('#grade-select').on('change', function(){
                const turmaId = $(this).val();
                if (turmaId) {
                    $('#empty-state').hide();
                    $('#students-list').show();
                    const classText = $('#grade-select option:selected').text();
                    $('#class-title').text('Alunos da Turma: ' + classText);
                    carregarAlunos(turmaId);
                } else {
                    $('#empty-state').show();
                    $('#students-list').hide();
                }
            });

            function carregarAlunos(turmaId) {
                const $grid = $('#students-grid');
                $grid.empty().append('<div class="col-12 text-center text-muted">Carregando alunos...</div>');
                $.getJSON('../includes/ajax/listar_alunos_por_turma.php', { turma_id: turmaId }, function (resp) {
                    $grid.empty();
                    if (resp.success) {
                        const students = resp.data;
                        $('#students-count').text(students.length + ' alunos');
                        if (!students.length) {
                            $grid.html(`
                                <div class="col-12">
                                    <div class="empty-state">
                                        <i class="zmdi zmdi-info-outline"></i>
                                        <h5>Nenhum aluno matriculado nesta turma</h5>
                                        <p class="text-muted">Não há alunos cadastrados para a turma selecionada.</p>
                                    </div>
                                </div>
                            `);
                            return;
                        }

                        students.forEach(student => {
                            const nome = student.Nome_Completo;
                            const matricula = student.Matricula || '—';
                            const status = student.Status === 'Ativa' ? 'Ativo' : 'Inativo';
                            $grid.append(`
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="card student-card">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <img src="../user_adm/imagens/icon_ex1.jpg" alt="${nome}" class="student-photo mr-3">
                                                <div>
                                                    <h6 class="mb-1">${nome}</h6>
                                                    <p class="mb-1 text-white small">Matrícula: ${matricula}</p>
                                                    <span class="badge ${status === 'Ativo' ? 'badge-success' : 'badge-warning'}">${status}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `);
                        });
                    } else {
                        $('#students-count').text('0 alunos');
                        $grid.html('<div class="col-12 text-danger">Falha ao carregar alunos: ' + (resp.message || 'Erro desconhecido') + '</div>');
                    }
                }).fail(function(){
                    $grid.html('<div class="col-12 text-danger">Erro ao consultar o servidor.</div>');
                });
            }

            // Imprimir/PDF
            $('#print-btn').click(function () {
                if ($('#grade-select').val()) {
                    const originalTitle = document.title;
                    document.title = 'Lista de Alunos - ' + $('#grade-select option:selected').text();
                    window.print();
                    document.title = originalTitle;
                } else {
                    alert('Por favor, selecione uma turma antes de gerar o relatório.');
                }
            });
        });
    </script>
</body>

</html>