<?php require_once '../includes/bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Disciplinas por Professor - SAS (Sistema Academico Santos)</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/app-style.css">
    <link rel="stylesheet" href="../assets/css/icons.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="../assets/css/sidebar-menu.css">
    <link rel="stylesheet" href="../css/style.css">
</head>

<body class="bg-theme bg-theme1 user_adm_disciplinasPorProfessor">
    <?php
    require("menu_padrão.php");
    ?>

    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="card" style="background-color: transparent;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
                        <h4 class="page-title"><i class="zmdi zmdi-book mr-2"></i> Disciplinas por Professor</h4>
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
                                        <option value="">Todos</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="professor-filter">Professor</label>
                                    <select class="form-control" id="professor-filter">
                                        <option value="">Todos Professores</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="disciplina-filter">Disciplina</label>
                                    <select class="form-control" id="disciplina-filter">
                                        <option value="">Todas Disciplinas</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <div id="alert-placeholder" class="no-print"></div>
                        <table class="table table-bordered" id="disciplines-table">
                            <thead>
                                <tr>
                                    <th>Professor</th>
                                    <th>Matrícula</th>
                                    <th>Disciplinas</th>
                                    <th>Turmas</th>
                                    <th>Carga Horária</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
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
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
    
    <script>
        $(document).ready(function () {
            const $ano = $('#ano-filter');
            const $prof = $('#professor-filter');
            const $disc = $('#disciplina-filter');

            const table = $('#disciplines-table').DataTable({
                responsive: true,
                language: { url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/pt-BR.json' },
                dom: '<"top"f>rt<"bottom"lip><"clear">'
            });

            function carregarAnos() {
                $.getJSON('../includes/ajax/listar_anos_letivos.php', function (resp) {
                    if (resp.success) {
                        resp.data.forEach(ano => $ano.append(`<option value="${ano}">${ano}</option>`));
                    }
                });
            }

            function carregarProfessores() {
                // Reutiliza endpoint de professores (sem filtros) para popular select
                $.getJSON('../includes/ajax/listar_professores.php', function (resp) {
                    if (resp.success) {
                        $prof.empty().append('<option value="">Todos Professores</option>');
                        resp.data.forEach(p => $prof.append(`<option value="${p.ID_Professor}">${p.Nome_Completo}</option>`));
                    }
                });
            }

            function carregarDisciplinas() {
                const ano = $ano.val();
                $.getJSON('../includes/ajax/listar_disciplinas_distintas.php', { ano }, function (resp) {
                    if (resp.success) {
                        $disc.empty().append('<option value="">Todas Disciplinas</option>');
                        resp.data.forEach(d => $disc.append(`<option value="${d}">${d}</option>`));
                    }
                });
            }

            function showAlert(type, msg) {
                const html = `<div class="alert alert-${type} alert-dismissible fade show" role="alert">
                    ${msg}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>`;
                $('#alert-placeholder').html(html);
                setTimeout(()=> $('.alert').alert('close'), 5000);
            }

            function carregarTabela() {
                const params = {
                    ano: $ano.val(),
                    professor_id: $prof.val(),
                    disciplina: $disc.val()
                };
                $.getJSON('../includes/ajax/listar_disciplinas_por_professor.php', params, function (resp) {
                    table.clear();
                    if (resp.success) {
                        let rowsWithDisc = 0;
                        resp.data.forEach(r => {
                            const profCell = `
                                <img src="../user_adm/imagens/icon_sor.png" class="teacher-photo mr-2" alt="Professor" onerror="this.src='../assets/images/default-user.png'">
                                ${r.Nome_Completo}
                            `;
                            const disciplinas = (r.Disciplinas || '').split(', ').filter(Boolean);
                            const discHtml = disciplinas.map(d => `<span class=\"badge discipline-badge\">${d}</span>`).join(' ');
                            if (disciplinas.length > 0) rowsWithDisc++;
                            const turmas = r.Turmas || '';
                            const carga = r.Carga_Total ? (r.Carga_Total + 'h/semana') : '';
                            table.row.add([
                                profCell,
                                r.Matricula || '',
                                discHtml,
                                turmas,
                                carga
                            ]);
                        });
                        // Se um professor específico foi selecionado e não há disciplinas, mostrar orientação
                        if ($prof.val() && rowsWithDisc === 0) {
                            // Fallback client-side: buscar disciplinas diretamente atribuídas
                            const fparams = { ano: $ano.val(), professor_id: $prof.val() };
                            $.getJSON('../includes/ajax/disciplinas/listar_disciplinas.php', fparams, function(r2){
                                if (r2.success && Array.isArray(r2.data) && r2.data.length > 0 && resp.data.length > 0) {
                                    const profRow = resp.data[0];
                                    const profCell = `
                                        <img src="../user_adm/imagens/icon_sor.png" class="teacher-photo mr-2" alt="Professor" onerror="this.src='../assets/images/default-user.png'">
                                        ${profRow.Nome_Completo}
                                    `;
                                    const nomes = r2.data.map(d => d.Nome_Disciplina).filter(Boolean).sort();
                                    const discHtml = nomes.map(d => `<span class=\"badge discipline-badge\">${d}</span>`).join(' ');
                                    const cargaTotal = r2.data.reduce((acc, d) => acc + (parseInt(d.Carga_Horaria || 0) || 0), 0);
                                    table.clear();
                                    table.row.add([
                                        profCell,
                                        profRow.Matricula || '',
                                        discHtml,
                                        profRow.Turmas || '',
                                        cargaTotal ? (cargaTotal + 'h/semana') : ''
                                    ]).draw();
                                    $('#alert-placeholder').empty();
                                } else {
                                    const anoTxt = $ano.val() ? ` no ano ${$ano.val()}` : '';
                                    showAlert('warning', `Nenhuma disciplina atribuída para o professor selecionado${anoTxt}. Você pode atribuir disciplinas em Disciplinas → <a href=\"atribuirDisciplinas.php\">Atribuir a Professores</a>.`);
                                }
                            }).fail(function(){
                                const anoTxt = $ano.val() ? ` no ano ${$ano.val()}` : '';
                                showAlert('warning', `Nenhuma disciplina atribuída para o professor selecionado${anoTxt}. Você pode atribuir disciplinas em Disciplinas → <a href=\"atribuirDisciplinas.php\">Atribuir a Professores</a>.`);
                            });
                        } else {
                            $('#alert-placeholder').empty();
                        }
                    }
                    table.draw();
                }).fail(function(xhr){
                    const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Erro ao carregar disciplinas por professor.';
                    showAlert('danger', msg);
                });
            }

            $ano.on('change', function(){ carregarDisciplinas(); carregarTabela(); });
            $prof.on('change', carregarTabela);
            $disc.on('change', carregarTabela);

            carregarAnos();
            carregarProfessores();
            carregarDisciplinas();
            carregarTabela();

            $('#print-btn').click(function () {
                const originalTitle = document.title;
                document.title = 'Disciplinas por Professor - SAS (Sistema Academico Santos)';
                $('.no-print').hide();
                $('body').addClass('printing');
                setTimeout(() => {
                    window.print();
                    $('.no-print').show();
                    $('body').removeClass('printing');
                    document.title = originalTitle;
                }, 300);
            });
        });
    </script>
</body>

</html>