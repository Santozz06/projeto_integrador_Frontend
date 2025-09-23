<?php require_once '../includes/bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Ensino - Emitir Boletim</title>
    <link href="../assets/css/pace.min.css" rel="stylesheet" />
    <script src="../assets/js/pace.min.js"></script>
    <link rel="icon" href="../assets/images/favicon.ico" type="image/x-icon">
    <link href="../assets/plugins/simplebar/css/simplebar.css" rel="stylesheet" />
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet" />
    <link href="../assets/css/animate.css" rel="stylesheet" />
    <link href="../assets/css/icons.css" rel="stylesheet" />
    <link href="../assets/css/sidebar-menu.css" rel="stylesheet" />
    <link href="../assets/css/app-style.css" rel="stylesheet" />
     <link href="style.css" rel="stylesheet" />
    <style>
        :root {
            --azul-cabecalho: #2c5f9e;
            --texto-preto: #333333;
            --cinza-texto: #666666;
        }

        /* Cores dos selos de status */
        .badge-warning {
            background-color: #f6c23e;
            color: #fff;
        }

        .badge-success {
            background-color: #1cc88a;
            color: #fff;
        }

        .badge-info {
            background-color: #36b9cc;
            color: #fff;
        }

        /* Cabeçalho do card */
        .card-header {
            border-radius: 0.35rem 0.35rem 0 0 !important;
            background-color: var(--azul-cabecalho) !important;
        }

        .card-header h4 {
            color: white !important;
            font-weight: 600;
        }

        /* Card geral  */
        .card {
            background-color: rgba(0, 0, 0, 0.3);
            color: #ffffff;
        }

        /* Responsividade da tabela */
        .table-responsive {
            margin-top: 20px;
        }

        .table th {
            background-color: #f8f9fa;
            color: var(--texto-preto);
        }

        /* Selo de status */
        .status-badge {
            font-size: 0.85rem;
            padding: 0.35em 0.65em;
        }

        .btn-voltar-custom:active,
        .btn-voltar-custom:focus,
        .btn-voltar-custom:focus-visible {
            background-color: #2c5f9e !important;
            color: #fff !important;
            box-shadow: none !important;
            outline: none !important;
        }

        .btn-voltar-custom {
            background-color: #2c5f9e;
            color: #fff;
            border: none;
            text-transform: uppercase;
            font-weight: 600;
            padding: 8px 18px;
            border-radius: 6px;
            transition: background-color 0.3s ease;
            font-size: 0.85rem;
        }

        .btn-voltar-custom:hover {
            background-color: #224a7d;
            color: #fff;
        }

        .btn-voltar-custom i {
            color: #fff;
        }

        /* Botão de voltar */
        .back-button {
            margin-bottom: 35px;
        }

        /* Bloco de ano  */
        .year-option {
            cursor: pointer;
            transition: all 0.3s ease;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 10px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            background-color: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(4px);
            display: block;
            text-decoration: none !important;
            color: inherit;
        }

        .year-option:hover {
            background-color: rgba(255, 255, 255, 0.1);
            border-color: var(--azul-cabecalho);
            text-decoration: none;
        }

        /* Título do ano */
        .year-title {
            font-weight: 600;
            color: #ffffff;
            display: inline-block;
        }

        /* Status do ano*/
        .year-status {
            float: right;
        }

        /* Texto inferior*/
        .card-body p.text-muted {
            color: #e0e0e0 !important;
        }

        .navbar {
            background-color: rgba(0, 0, 0, 0.2) !important;
            backdrop-filter: blur(10px);
        }
    </style>
</head>

<body class="bg-theme bg-theme1">
    <?php
    require("menu_padrao.php");
    ?>

        <div class="clearfix"></div>

        <!-- Conteúdo da Página -->
        <div class="content-wrapper">
            <div class="container-fluid">
                <div class="row justify-content-center mt-4">
                    <div class="col-lg-10">
                        <div class="card shadow-lg rounded-lg">
                            <div class="card-header">
                                <h4 class="mb-0"><i class="zmdi zmdi-assignment mr-2"></i> Boletim</h4>
                            </div>
                            <div class="card-body">
                                <a href="ensino.php" class="btn btn-primary btn-voltar-custom">
                                    <i class="zmdi zmdi-arrow-left mr-1"></i> VOLTAR
                                </a>

                                <h5 class="mb-4">Selecione um ano para emitir o boletim</h5>

                                <!-- 2025 - Matriculado -->
                                <a href="boletim_detalhes.php?ano=2025" class="year-option">
                                    <span class="year-title">2025 - 3°ano</span>
                                    <span class="badge badge-info status-badge year-status">matriculado</span>
                                </a>

                                <!-- 2024 - Aprovado -->
                                <a href="boletim_detalhes.php?ano=2024" class="year-option">
                                    <span class="year-title">2024 - 2°ano</span>
                                    <span class="badge badge-success status-badge year-status">aprovado</span>
                                </a>

                                <!-- 2023 - Aprovado -->
                                <a href="boletim_detalhes.php?ano=2023" class="year-option">
                                    <span class="year-title">2023 - 1°ano</span>
                                    <span class="badge badge-success status-badge year-status">aprovado</span>
                                </a>

                                <div class="mt-4 pt-3 border-top">
                                    <p style="color: #e0e0e0;">Selecione um ano escolar acima para visualizar ou
                                        imprimir seu boletim acadêmico.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

       
    </div>

    <!-- Scripts -->
    <script src="../assets/js/jquery.min.js"></script>
    <script src="../assets/js/popper.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script src="../assets/plugins/simplebar/js/simplebar.js"></script>
    <script src="../assets/js/sidebar-menu.js"></script>
    <script src="../assets/js/app-script.js"></script>
    
</body>

</html>