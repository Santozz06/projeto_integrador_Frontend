<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Disciplinas por Professor - Dashboard Acadêmico</title>
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

        .discipline-badge {
            background-color: #e3f2fd;
            color: #1976d2;
            margin-right: 5px;
            margin-bottom: 5px;
            display: inline-block;
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

            .discipline-badge {
                border: 1px solid #ccc !important;
                padding: 2px 5px !important;
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
                        <h4 class="page-title"><i class="zmdi zmdi-book mr-2"></i> Disciplinas por Professor</h4>
                        <button id="print-btn" class="btn btn-custom-print">
                            <i class="zmdi zmdi-print mr-2"></i>Imprimir Relatório
                        </button>
                    </div>

                    <div class="filter-section no-print">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="periodo-filter">Período Letivo</label>
                                    <select class="form-control" id="periodo-filter">
                                        <option value="">2024 - 1º Semestre</option>
                                        <option>2023 - 2º Semestre</option>
                                        <option>2023 - 1º Semestre</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="professor-filter">Professor</label>
                                    <select class="form-control" id="professor-filter">
                                        <option value="">Todos Professores</option>
                                        <option>Maria da Silva</option>
                                        <option>João Oliveira</option>
                                        <option>Ana Santos</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="disciplina-filter">Disciplina</label>
                                    <select class="form-control" id="disciplina-filter">
                                        <option value="">Todas Disciplinas</option>
                                        <option>Matemática</option>
                                        <option>Português</option>
                                        <option>Ciências</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
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
                            <tbody>
                                <tr>
                                    <td>
                                        <img src="../user_adm/imagens/icon_sor.png" class="teacher-photo mr-2"
                                            alt="Professor" onerror="this.src='../assets/images/default-user.png'">
                                        Maria da Silva
                                    </td>
                                    <td>PROF2023001</td>
                                    <td>
                                        <span class="badge discipline-badge">Matemática</span>
                                        <span class="badge discipline-badge">Geometria</span>
                                    </td>
                                    <td>1ºA, 2ºB, 3ºC</td>
                                    <td>20h/semana</td>
                                </tr>
                                <tr>
                                    <td>
                                        <img src="../user_adm/imagens/icon_sor.png" class="teacher-photo mr-2"
                                            alt="Professor" onerror="this.src='../assets/images/default-user.png'">
                                        João Oliveira
                                    </td>
                                    <td>PROF2023002</td>
                                    <td>
                                        <span class="badge discipline-badge">Ciências</span>
                                        <span class="badge discipline-badge">Biologia</span>
                                    </td>
                                    <td>1ºB, 2ºC</td>
                                    <td>15h/semana</td>
                                </tr>
                                <tr>
                                    <td>
                                        <img src="../user_adm/imagens/icon_sor.png" class="teacher-photo mr-2"
                                            alt="Professor" onerror="this.src='../assets/images/default-user.png'">
                                        Ana Santos
                                    </td>
                                    <td>PROF2023003</td>
                                    <td>
                                        <span class="badge discipline-badge">Português</span>
                                        <span class="badge discipline-badge">Literatura</span>
                                    </td>
                                    <td>1ºC, 2ºA, 3ºB</td>
                                    <td>18h/semana</td>
                                </tr>
                            </tbody>
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
    <script src="botaoSair.js"></script>
    <script>
        $(document).ready(function () {
            const table = $('#disciplines-table').DataTable({
                responsive: true,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/pt-BR.json'
                },
                dom: '<"top"f>rt<"bottom"lip><"clear">'
            });

            // Filtros
            $('#professor-filter, #disciplina-filter').change(function () {
                table.draw();
            });

            // Filtro personalizado
            $.fn.dataTable.ext.search.push(
                function (settings, data) {
                    const professor = $('#professor-filter').val();
                    const disciplina = $('#disciplina-filter').val();
                    const rowProfessor = data[0];
                    const rowDisciplinas = data[2];

                    return (!professor || rowProfessor.includes(professor)) &&
                        (!disciplina || rowDisciplinas.includes(disciplina));
                }
            );

            $('#print-btn').click(function () {
                const originalTitle = document.title;
                document.title = 'Disciplinas por Professor - Dashboard Acadêmico';

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