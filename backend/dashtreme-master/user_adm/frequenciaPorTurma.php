<?php require_once '../includes/bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Frequência por Turma - Dashboard Acadêmico</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/app-style.css">
    <link rel="stylesheet" href="../assets/css/icons.css">
    <link rel="stylesheet" href="../assets/css/sidebar-menu.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .btn-custom-primary {
            background-color: #1abc9c !important;
            color: white !important;
            border: none !important;
        }

        .btn-custom-primary:hover {
            background-color: #16a085 !important;
        }

        .btn-custom-secondary {
            background-color: #3498db !important;
            color: white !important;
            border: none !important;
        }

        .btn-custom-secondary:hover {
            background-color: #2980b9 !important;
        }

        .filter-section {
            background-color: transparent !important;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .attendance-card {
            border-left: 4px solid #1abc9c;
            transition: all 0.3s;
            margin-bottom: 20px;
        }

        .attendance-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .student-photo {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #e0e0e0;
        }

        .empty-state {
            text-align: center;
            padding: 40px;
            background-color: transparent !important;
            border-radius: 8px;
        }

        .empty-state i {
            font-size: 60px;
            color: #edf0f3;
            margin-bottom: 20px;
        }

        .attendance-badge {
            font-size: 0.8rem;
            padding: 5px 10px;
            border-radius: 20px;
        }

        .present {
            background-color: #2ecc71;
            color: white;
        }

        .absent {
            background-color: #e74c3c;
            color: white;
        }

        .justified {
            background-color: #f39c12;
            color: white;
        }

        .summary-text {
            background-color: #3498db;
            padding: 8px 12px;
            border-radius: 5px;
            font-size: 0.9rem;
            word-break: break-word;
            white-space: normal;
            max-width: 100%;
        }


        @media print {

            .no-print,
            #sidebar-wrapper,
            .topbar-nav,
            .footer,
            .dataTables_info,
            .dataTables_paginate,
            .dataTables_filter,
            .dataTables_length {
                display: none !important;
            }

            body {
                background: white !important;
                color: #000000 !important;
                font-size: 13pt !important;
                line-height: 1.6;
            }

            .content-wrapper {
                margin-left: 0 !important;
            }

            .card {
                border: none !important;
                box-shadow: none !important;
            }

            .header-print {
                display: block !important;
                text-align: center;
                margin-bottom: 20px;
                border-bottom: 2px solid #000;
                padding-bottom: 10px;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 20px;
            }

            th,
            td {
                color: #000000 !important;
                font-weight: 500;
            }

            th {
                background-color: #f2f2f2 !important;
            }

            .progress {
                display: none !important;
            }
        }



        .header-print {
            display: none;
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
            <div class="header-print">
                <img src="../assets/images/logo-icon.png" alt="Logo" style="height: 50px;">
                <h3>Relatório de Frequência por Turma</h3>
                <p id="print-date" class="text-muted"></p>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-3 no-print">
                <h4 class="page-title"><i class="zmdi zmdi-time-countdown mr-2"></i> Frequência por Turma</h4>
                <div>
                    <button id="print-btn" class="btn btn-custom-secondary">
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
                                <option>2023</option>
                                <option selected>2024</option>
                                <option>2025</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="grade-select">Turma</label>
                            <select class="form-control" id="grade-select">
                                <option value="">Selecione uma turma</option>
                                <option value="1A">1º Ano - Turma A</option>
                                <option value="1B">1º Ano - Turma B</option>
                                <option value="2A">2º Ano - Turma A</option>
                                <option value="2B">2º Ano - Turma B</option>
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

    <footer class="footer no-print">
        <div class="container">
            <div class="text-center text-white">
                Copyright © 2023 Dashboard Acadêmico
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

    <script>
        // Dados de exemplo
        const sampleAttendance = {
            "1A": [
                {
                    id: 1,
                    name: "Aluno 1",
                    photo: "../user_adm/imagens/icon_ex1.jpg",
                    registration: "202411001",
                    attendance: {
                        present: 18,
                        absent: 2,
                        justified: 1,
                        percentage: 85.7
                    }
                },
                {
                    id: 2,
                    name: "Aluno 2",
                    photo: "../user_adm/imagens/icon_ex2.jpg",
                    registration: "202411002",
                    attendance: {
                        present: 20,
                        absent: 0,
                        justified: 0,
                        percentage: 100
                    }
                }
            ],
            "2A": [
                {
                    id: 3,
                    name: "Aluno 3",
                    photo: "../user_adm/imagens/icon_ex3.jpg",
                    registration: "202421001",
                    attendance: {
                        present: 15,
                        absent: 5,
                        justified: 1,
                        percentage: 71.4
                    }
                }
            ]
        };

        $(document).ready(function () {
            // Atualiza a data para impressão
            const now = new Date();
            $('#print-date').text('Emitido em: ' + now.toLocaleDateString() + ' às ' + now.toLocaleTimeString());

            // Manipula a seleção de turma
            $('#grade-select').change(function () {
                const selectedClass = $(this).val();

                if (selectedClass) {
                    $('#empty-state').hide();
                    $('#attendance-list').show();

                    // Atualiza o título
                    const classText = $('#grade-select option:selected').text();
                    $('#class-title').text('Frequência da Turma: ' + classText);

                    // Carrega os dados
                    loadAttendanceData(selectedClass);
                } else {
                    $('#empty-state').show();
                    $('#attendance-list').hide();
                }
            });

            // Função para carregar dados de frequência
            function loadAttendanceData(classId) {
                const students = sampleAttendance[classId] || [];
                const totalStudents = students.length;

                // Destrói a DataTable se já existir
                if ($.fn.DataTable.isDataTable('#attendance-table')) {
                    $('#attendance-table').DataTable().destroy();
                }

                // Limpa a tabela
                $('#attendance-table tbody').empty();

                if (totalStudents === 0) {
                    $('#attendance-data').html('<tr><td colspan="6" class="text-center">Nenhum dado de frequência disponível</td></tr>');
                    return;
                }

                // Calcula totais
                let totalPresent = 0;
                let totalAbsent = 0;
                let totalJustified = 0;

                let tableContent = '';
                students.forEach(student => {
                    totalPresent += student.attendance.present;
                    totalAbsent += student.attendance.absent;
                    totalJustified += student.attendance.justified;

                    tableContent += `
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="${student.photo}" alt="${student.name}" 
                                         class="student-photo mr-2" onerror="this.src='../assets/images/default-user.png'">
                                    ${student.name}
                                </div>
                            </td>
                            <td>${student.registration}</td>
                            <td>${student.attendance.present}</td>
                            <td>${student.attendance.absent}</td>
                            <td>${student.attendance.justified}</td>
                            <td>
                                <span class="d-block d-print-none">
                                    <div class="progress" style="height: 20px;">
                                    <div class="progress-bar ${getPercentageColor(student.attendance.percentage)}"
                                        role="progressbar" style="width: ${student.attendance.percentage}%"
                                        aria-valuenow="${student.attendance.percentage}" 
                                        aria-valuemin="0" aria-valuemax="100">
                                        ${student.attendance.percentage}%
                                    </div>
                                    </div>
                                </span>
                                <span class="d-none d-print-block">
                                    ${student.attendance.percentage}%
                                </span>
                            </td>
                        </tr>
                    `;
                });

                $('#attendance-data').html(tableContent);
                $('#attendance-summary').html(`
                    ${totalStudents} alunos | 
                    Presenças: ${totalPresent} | 
                    Faltas: ${totalAbsent} | 
                    Justificadas: ${totalJustified}
                `);

                // Inicializa DataTable
                $('#attendance-table').DataTable({
                    responsive: true,
                    language: {
                        url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/pt-BR.json'
                    },
                    dom: '<"top"f>rt<"bottom"lip><"clear">',
                    initComplete: function () {
                    }
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
                    $('.no-print').hide();
                    $('.dataTables_info, .dataTables_paginate, .dataTables_length, .dataTables_filter').hide();

                    // Força estilos de impressão
                    $('body').addClass('printing');

                    // impressão
                    setTimeout(() => {
                        window.print();
                        $('.no-print').show();
                        $('body').removeClass('printing');
                        document.title = originalTitle;
                    }, 300);
                } else {
                    alert('Por favor, selecione uma turma antes de gerar o relatório.');
                }
            });
        });
    </script>
</body>

</html>