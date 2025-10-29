<?php require_once '../includes/bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>IFRS - Boletim Escolar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --azul-principal: #2c5f9e;
            --azul-escuro: #1d4b7d;
            --branco: #ffffff;
            --cinza-claro: #f8f9fa;
            --cinza-texto: #6c757d;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            color: #000;
            background-color: var(--branco);
        }

        .header-ifrs {
            background-color: var(--azul-principal);
            color: var(--branco);
            padding: 1rem 0;
            border-bottom: 4px solid #f6c23e;
        }

        .titulo-boletim {
            font-weight: 700;
            margin: 1.5rem 0;
            text-align: center;
            color: var(--azul-principal);
        }

        .dados-aluno {
            background-color: var(--cinza-claro);
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1.5rem;
            border-left: 4px solid var(--azul-principal);
        }

        .table-boletim {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1.5rem;
        }

        .table-boletim th,
        .table-boletim td {
            border: 1px solid #dee2e6;
            padding: 0.5rem;
            text-align: center;
        }

        .table-boletim th {
            background-color: var(--azul-principal);
            color: var(--branco);
            font-weight: 600;
        }

        .table-boletim tr:nth-child(even) {
            background-color: var(--cinza-claro);
        }

        .table-boletim tr:hover {
            background-color: rgba(44, 95, 158, 0.1);
        }

        .table-legend {
            width: 100%;
            margin: 1.5rem 0;
        }

        .table-legend th {
            background-color: var(--azul-principal);
            color: var(--branco);
        }

        .footer-boletim {
            margin-top: 2rem;
            padding-top: 1rem;
            border-top: 1px solid #dee2e6;
            text-align: center;
            font-size: 0.9rem;
            color: var(--cinza-texto);
        }

        .btn-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 1.5rem;
        }

        .aprovado {
            color: #28a745;
            font-weight: bold;
        }

        .recuperacao {
            color: #ffc107;
            font-weight: bold;
        }

        .reprovado {
            color: #dc3545;
            font-weight: bold;
        }

        .data-emissao {
            color: var(--azul-escuro);
            font-weight: bold;
        }

        .situacao-aluno {
            font-weight: bold;
            color: var(--azul-principal);
        }

        @media print {
            @page {
                size: A4;
                margin: 5mm;
            }

            body {
                font-size: 8.5pt;
                line-height: 1.1;
                width: auto;
                margin: 0 auto;
                background: white !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .container,
            .container-fluid {
                max-width: 100% !important;
                width: 100% !important;
                padding: 0 !important;
            }

            .table-responsive {
                overflow: visible !important;
                -webkit-overflow-scrolling: auto !important;
            }

            .header-ifrs {
                font-size: 10pt;
                margin-bottom: 4mm;
                padding: 2mm;
                background-color: var(--azul-principal) !important;
                color: white !important;
            }

            .titulo-boletim {
                font-size: 11pt;
                margin: 2mm 0;
                color: var(--azul-principal);
            }

            .dados-aluno,
            .footer-boletim,
            .table-responsive {
                padding: 2mm !important;
                margin-bottom: 2mm !important;
            }

            .dados-aluno {
                border-left: none !important;
                background-color: var(--cinza-claro) !important;
            }

            .table-boletim {
                font-size: 8pt;
                width: 100%;
                border-collapse: collapse;
            }

            .table-boletim th,
            .table-boletim td {
                padding: 2pt;
                border: 1px solid #000;
            }

            .table-boletim th {
                background-color: var(--azul-principal) !important;
                color: white !important;
            }

            .table-legend,
            .table-legend table,
            .table-legend tbody {
                page-break-inside: auto !important;
            }

            .table-legend th,
            .table-legend td {
                font-size: 7pt !important;
                padding: 1pt 2pt !important;
            }

            .footer-boletim {
                margin-top: 3mm;
                font-size: 7pt;
                text-align: center;
                color: var(--cinza-texto);
            }

            .no-print,
            .btn-actions {
                display: none !important;
            }
        }
        .navbar {
            background-color: rgba(0, 0, 0, 0.2) !important;
            backdrop-filter: blur(10px);
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="print-area">
            <!-- Cabeçalho IFRS -->
            <div class="header-ifrs text-center">
                <h5>República Federativa do Brasil</h5>
                <h6>Ministério da Educação</h6>
                <p class="mb-0">Secretaria de Educação</p>
                <p class="mb-0">Escola do Rio Grande do Sul</p>
            </div>

            <!-- Informações do Campus -->
            <div class="text-center mt-2">
                <p>Rodovia RS-239, Km 68, Rolante - RS, 95690-000</p>
            </div>

            <!-- Título do Boletim -->
            <h2 id="titulo-boletim" class="titulo-boletim">Boletim Escolar</h2>

            <p id="data-emissao" class="text-end data-emissao">Emitido em 03/07/2025, 20:09</p>

            <!-- Dados do Aluno -->
            <div class="dados-aluno">
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Nome:</strong> <span id="nome-aluno">—</span></p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Matrícula:</strong> <span id="matricula">—</span></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Turma:</strong> <span id="turma">161</span></p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Ano - Série:</strong> <span id="ano-serie">2023 - 6°</span></p>
                    </div>
                </div>
                <p class="mb-0"><strong>Situação:</strong> <span id="situacao-aluno" class="situacao-aluno">—</span></p>
            </div>

            <!-- Tabela de Notas -->
            <h5 class="mt-4 mb-3">Dados dos Componentes Curriculares</h5>
            <div class="table-responsive">
                <table class="table-boletim">
                    <thead>
                        <tr>
                            <th>COMPONENTE CURRICULAR</th>
                            <th>Nota do 1º Trim</th>
                            <th>Rec. Par. NT1</th>
                            <th>Nota do 2º Trim</th>
                            <th>Rec. Par. NT2</th>
                            <th>Nota do 3º Trim</th>
                            <th>Rec. Par. NT3</th>
                            <th>Prova Final</th>
                            <th>Faltas</th>
                            <th>FINAL</th>
                            <th>Situação</th>
                        </tr>
                    </thead>
                    <tbody id="tabela-notas">
                    </tbody>
                </table>
            </div>

            <!-- Totais -->
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Total de Faltas:</strong></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Percentual de faltas sobre as aulas ministradas:</strong></p>
                </div>
            </div>

            <!-- Legenda -->
            <h5 class="mt-4 mb-3">Legenda</h5>
            <div class="table-responsive">
                <table class="table-legend table table-bordered">
                    <thead>
                        <tr>
                            <th>Sigla</th>
                            <th>Situação</th>
                            <th>Significado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>REP</td>
                            <td>Reprovado por Faltas</td>
                            <td>Reprovado por não atender os critérios de assiduidade.</td>
                        </tr>
                        <tr>
                            <td>REP</td>
                            <td>Reprovado por Média</td>
                            <td>Reprovado pois a média está inferior ao mínimo que dá direito à recuperação.</td>
                        </tr>
                        <tr>
                            <td>REPN</td>
                            <td>Reprovado por Nota</td>
                            <td>Reprovado pois a média (após recuperação) não atingiu o valor mínimo para satisfazer o
                                critério de aprovação.</td>
                        </tr>
                        <tr>
                            <td>REPP</td>
                            <td>Reprovado em Todo Período Letivo</td>
                            <td>Reprovado por não atender aos critérios de número máximo de reprovações permitidas por
                                ano.</td>
                        </tr>
                        <tr>
                            <td>REC</td>
                            <td>Em Recuperação</td>
                            <td>Aluno que fará a recuperação.</td>
                        </tr>
                        <tr>
                            <td>APR</td>
                            <td>Aprovado por média</td>
                            <td>Aprovado pois a média atingiu o valor mínimo que satisfaz o critério de aprovação.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div> 

        <!-- Rodapé com botões (FORA da área de impressão) -->
        <div class="footer-boletim no-print">
            <div class="btn-actions">
                <button id="btn-voltar" class="btn btn-outline-secondary">Voltar</button>
                <button id="btn-imprimir" class="btn btn-primary">Imprimir</button>
            </div>
            <p class="mt-3">Diretoria | Copyright ©</p>
        </div>
    </div>

    <!-- Scripts -->
    <script src="../assets/js/jquery.min.js"></script>
    <script src="../assets/js/popper.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script src="../assets/plugins/simplebar/js/simplebar.js"></script>
    <script src="../assets/js/sidebar-menu.js"></script>
    <script src="../assets/js/app-script.js"></script>
    <script src="boletim.js"></script>
</body>

</html>