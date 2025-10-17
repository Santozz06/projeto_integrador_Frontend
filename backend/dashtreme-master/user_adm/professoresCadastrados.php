<?php require_once '../includes/bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório de Professores - Dashboard Acadêmico</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/app-style.css">
    <link rel="stylesheet" href="../assets/css/icons.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="../assets/css/sidebar-menu.css">
    <link rel="stylesheet" href="style.css">


    <style>
        .teacher-photo {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #e0e0e0;
        }

        .filter-section {
            background-color: transparent !important;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
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

            .no-print,
            #sidebar-wrapper,
            .topbar-nav,
            .dataTables_filter,
            .dataTables_paginate,
            .dataTables_info,
            .dataTables_length {
                display: none !important;
            }

            body {
                background: white !important;
                color: black !important;
                font-size: 12pt !important;
                margin: 0;
                padding: 1cm;
            }

            .content-wrapper {
                margin: 0 !important;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 20px;
            }

            th,
            td {
                border: 1px solid #000 !important;
                padding: 6px 10px !important;
                font-size: 11pt;
                color: #000 !important;
            }

            .teacher-photo {
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
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="year-filter">Ano Letivo</label>
                                    <select class="form-control" id="year-filter">
                                        <option value="">Todos</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="department-filter">Departamento</label>
                                    <select class="form-control" id="department-filter">
                                        <option value="">Todos</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
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
                                    <th>Departamento</th>
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
                dom: '<"top"f>rt<"bottom"lip><"clear">'
            });

            function carregarDepartamentos() {
                $.getJSON('../includes/ajax/listar_departamentos.php', function (resp) {
                    if (resp.success) {
                        resp.data.forEach(dep => $dept.append(`<option value="${dep}">${dep}</option>`));
                    }
                });
            }

            function carregarAnos() {
                $.getJSON('../includes/ajax/listar_anos_letivos.php', function (resp) {
                    if (resp.success && Array.isArray(resp.data)) {
                        resp.data.forEach(ano => $year.append(`<option value="${ano}">${ano}</option>`));
                    }
                });
            }

            function carregarProfessores() {
                const params = { departamento: $dept.val(), status: $status.val(), ano: $year.val() };
                $.getJSON('../includes/ajax/listar_professores.php', params, function (resp) {
                    table.clear();
                    if (resp.success) {
                        resp.data.forEach(p => {
                            const foto = '<img src="../user_adm/imagens/icon_sor.png" class="teacher-photo" alt="Professor" onerror="this.src=\'../assets/images/default-user.png\'">';
                            // Exibir matrícula do professor quando disponível; fallback vazio
                            const matricula = p.Matricula || '';
                            const turmas = p.Turmas || '';
                            const disciplinas = p.Disciplinas || '';
                            table.row.add([
                                foto,
                                p.Nome_Completo,
                                matricula,
                                p.Area_Atuacao || '',
                                disciplinas,
                                p.Status,
                                turmas
                            ]);
                        });
                    }
                    table.draw();
                });
            }

            $dept.on('change', carregarProfessores);
            $status.on('change', carregarProfessores);
            $year.on('change', carregarProfessores);

            carregarDepartamentos();
            carregarAnos();
            carregarProfessores();

            $('#print-btn').click(function () {
                const images = document.querySelectorAll('img.teacher-photo');
                let allLoaded = true;
                images.forEach(img => { if (!img.complete || img.naturalWidth === 0) allLoaded = false; });
                if (!allLoaded) { alert('Espere as imagens carregarem completamente antes de imprimir.'); return; }
                const originalTitle = document.title;
                document.title = 'Relatório de Professores - Dashboard Acadêmico';
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