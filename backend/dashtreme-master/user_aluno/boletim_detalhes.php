<?php require_once '../includes/bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <link rel="icon" href="../assets/images/favicon.ico" type="image/x-icon">
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>IFRS - Boletim Escolar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/app-style.css?v=<?php echo time(); ?>" rel="stylesheet" />
    <link href="../css/style.css?v=<?php echo time(); ?>" rel="stylesheet" />
    <style>
        body {
            background: #ffffff !important;
            background-color: #ffffff !important;
            background-image: none !important;
        }
        body::before,
        body::after {
            display: none !important;
        }
    </style>
    <script>
        // Força fundo branco antes de qualquer outro script
        document.addEventListener('DOMContentLoaded', function() {
            document.body.style.cssText = 'background: #ffffff !important; background-color: #ffffff !important; background-image: none !important;';
            document.body.classList.remove('bg-theme', 'bg-theme1', 'bg-theme2', 'bg-theme3', 'bg-theme4', 'bg-theme5');
        });
    </script>
</head>

<body class="user_aluno_boletim_detalhes">
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
    <script>
        document.body.classList.remove('bg-theme', 'bg-theme1', 'bg-theme2', 'bg-theme3', 'bg-theme4', 'bg-theme5');
        document.body.style.background = '#ffffff';
        document.body.style.backgroundColor = '#ffffff';
        document.body.style.backgroundImage = 'none';
    </script>
</body>

</html>