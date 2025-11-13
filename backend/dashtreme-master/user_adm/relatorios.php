<?php require_once '../includes/bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatórios - SAS (Sistema Academico Santos)</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/app-style.css">
    <link rel="stylesheet" href="../assets/css/icons.css">
    <link rel="stylesheet" href="../assets/css/sidebar-menu.css">
    <link rel="stylesheet" href="../css/style.css">
</head>

<body class="bg-theme bg-theme1 user_adm_relatorios">
    <?php
    require("menu_padrão.php");
    ?>

    <!-- Conteúdo principal -->
    <div class="content-wrapper">
        <div class="container-fluid">
            <h4 class="page-title">Relatórios</h4>

            <!-- Alunos -->
            <div id="relatorio-alunos" class="report-section">
                <h4 class="section-title"><i class="zmdi zmdi-accounts-alt"></i> Alunos</h4>
                <div class="row">
                    <div class="col-md-6">
                        <div class="card report-card h-100">
                            <div class="card-body">
                                <div class="report-icon icon-blue"><i class="zmdi zmdi-accounts"></i></div>
                                <h5>Lista de Alunos por Turma</h5>
                                <p>Visualize os alunos matriculados em cada turma, com filtros por período letivo,
                                    série e turno.</p>
                                <div class="mt-3">
                                    <a href="alunosPorTurma.php" class="btn btn-custom-primary">Ver Lista
                                        Completa</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card report-card h-100">
                            <div class="card-body">
                                <div class="report-icon icon-green"><i class="zmdi zmdi-time-countdown"></i></div>
                                <h5>Frequência Geral</h5>
                                <p>Controle de presenças e faltas por turma, com indicadores visuais.</p>
                                <div class="mt-3">
                                    <a href="frequenciaPorTurma.php" class="btn btn-custom-primary">Gerar
                                        Relatório</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Servidores -->
            <div id="relatorio-servidores" class="report-section">
                <h4 class="section-title"><i class="zmdi zmdi-account-box"></i> Servidores</h4>
                <div class="row">
                    <div class="col-md-6">
                        <div class="card report-card h-100">
                            <div class="card-body">
                                <div class="report-icon icon-orange"><i class="zmdi zmdi-accounts-list"></i></div>
                                <h5>Professores Cadastrados</h5>
                                <p>Relação completa de docentes com filtros por departamento, formação e status.</p>
                                <div class="mt-3">
                                    <a href="professoresCadastrados.php" class="btn btn-custom-primary">Ver
                                        Professores</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card report-card h-100">
                            <div class="card-body">
                                <div class="report-icon icon-red"><i class="zmdi zmdi-book"></i></div>
                                <h5>Disciplinas por Professor</h5>
                                <p>Distribuição de carga horária e alocação de disciplinas para planejamento
                                    acadêmico.</p>
                                <div class="mt-3">
                                    <a href="disciplinasPorProfessor.php" class="btn btn-custom-primary">Ver
                                        Disciplinas</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Turmas -->
            <div id="relatorio-turmas" class="report-section">
                <h4 class="section-title"><i class="zmdi zmdi-group-work"></i> Turmas</h4>
                <div class="row">
                    <div class="col-md-6">
                        <div class="card report-card h-100">
                            <div class="card-body">
                                <div class="report-icon icon-purple"><i class="zmdi zmdi-accounts-list-alt"></i>
                                </div>
                                <h5>Resumo por Turma</h5>
                                <p>Quantidade de alunos, disciplinas e professores por turma.</p>
                                <div class="mt-3">
                                    <a href="resumoTurma.php" class="btn btn-custom-primary">Ver Resumo</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Overlay-->
                <div class="overlay toggle-menu"></div>
            </div>



        </div>

        <script src="../assets/js/jquery.min.js"></script>
        <script src="../assets/js/bootstrap.min.js"></script>
        <script src="../assets/plugins/simplebar/js/simplebar.js"></script>
        <script src="../assets/js/sidebar-menu.js"></script>
    <script src="../assets/js/app-script.js"></script>

</body>

</html>