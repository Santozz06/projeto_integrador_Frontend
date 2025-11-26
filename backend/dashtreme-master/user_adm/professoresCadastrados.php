<?php require_once '../includes/bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <link rel="icon" href="../assets/images/favicon.ico" type="image/x-icon">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório de Professores - SAS</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/app-style.css">
    <link rel="stylesheet" href="../assets/css/icons.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="../assets/css/sidebar-menu.css">
    <link rel="stylesheet" href="../css/style.css">
</head>

<body class="bg-theme bg-theme1 user_adm_professoresCadastrados">
    <?php
    require("menu_padrão.php");
    ?>


    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="card" style="background-color: transparent;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
                        <h4 class="page-title"><i class="zmdi zmdi-account-box mr-2"></i> Relatório de Professores
                        </h4>
                        <button id="print-btn" class="btn btn-custom-print">
                            <i class="zmdi zmdi-print mr-2"></i>Imprimir Relatório
                        </button>
                    </div>

                    <div class="filter-section no-print">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="year-filter">Ano Letivo</label>
                                    <select class="form-control" id="year-filter">
                                        <option value="">Todos</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status-filter">Status</label>
                                    <select class="form-control" id="status-filter">
                                        <option value="">Todos</option>
                                        <option>Ativo</option>
                                        <option>Inativo</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered" id="teachers-table">
                            <thead>
                                <tr>
                                    <th>Foto</th>
                                    <th>Nome</th>
                                    <th>Matrícula</th>
                                    <th>Disciplinas</th>
                                    <th>Status</th>
                                    <th>Turmas Vinculadas</th>
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
            const $dept = $('#department-filter');
            const $status = $('#status-filter');
            const $tbody = $('#teachers-table tbody');
            const $year = $('#year-filter');

            const table = $('#teachers-table').DataTable({
                responsive: true,
                language: { url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/pt-BR.json' },
                dom: '<"top"f>rt<"bottom"lip><"clear">',
                columns: [
                    { data: 0, defaultContent: '' }, // Foto
                    { data: 1, defaultContent: '' }, // Nome
                    { data: 2, defaultContent: '' }, // Matrícula
                    { data: 3, defaultContent: '' }, // Disciplinas
                    { data: 4, defaultContent: '' }, // Status
                    { data: 5, defaultContent: '' }  // Turmas Vinculadas
                ]
            });

            function carregarAnos() {
                $.getJSON('../includes/ajax/shared/academico/listar_anos_letivos.php', function (resp) {
                    if (resp.success && Array.isArray(resp.data)) {
                        resp.data.forEach(ano => $year.append(`<option value="${ano}">${ano}</option>`));
                    }
                });
            }

            function carregarProfessores() {
                const params = { status: $status.val(), ano: $year.val() };
                $.getJSON('../includes/ajax/admin/professores/listar_professores.php', params, function (resp) {
                    table.clear();
                    if (resp.success) {
                        resp.data.forEach(p => {
                            // avatar com iniciais (primeira e última letra do nome)
                            const nome = (p.Nome_Completo || '').trim();
                            let iniciais = 'US';
                            if (nome) {
                                const parts = nome.split(/\s+/);
                                const first = parts[0].charAt(0);
                                const lastPart = parts.length > 1 ? parts[parts.length - 1] : parts[0];
                                const last = lastPart.charAt(lastPart.length - 1);
                                iniciais = (first + last).toUpperCase();
                            }
                            const foto = `<span class="user-profile"><span class="avatar-initials">${iniciais}</span></span>`;
                            const matricula = p.Matricula || '';
                            const turmas = p.Turmas || '<span class="text-muted">—</span>';
                            const disciplinasList = (p.Disciplinas || (p.Area_Atuacao || '')).split(', ').filter(Boolean);
                            const disciplinas = disciplinasList.length
                                ? disciplinasList.map(d => `<span class=\"badge discipline-badge\">${d}</span>`).join(' ')
                                : '<span class="text-muted">—</span>';
                            const status = p.Status || '<span class="text-muted">Ativo</span>';
                            table.row.add([
                                foto,
                                nome,
                                matricula,
                                disciplinas,
                                status,
                                turmas
                            ]);
                        });
                    }
                    table.draw();
                });
            }

            $status.on('change', carregarProfessores);
            $year.on('change', carregarProfessores);

            carregarAnos();
            carregarProfessores();

            $('#print-btn').click(function () {
                const images = document.querySelectorAll('img.teacher-photo');
                let allLoaded = true;
                images.forEach(img => { if (!img.complete || img.naturalWidth === 0) allLoaded = false; });
                if (!allLoaded) { alert('Espere as imagens carregarem completamente antes de imprimir.'); return; }
                const originalTitle = document.title;
                document.title = 'Relatório de Professores - SAS';
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