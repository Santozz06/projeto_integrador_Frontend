<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <title>Notas - Dashboard Acadêmico</title>
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
      --azul-principal: #2c5f9e;
      --texto-escuro: #333333;
      --cinza-texto: #666666;
      --borda: #e0e0e0;
    }

    .card-componente {
      border-radius: 10px;
      border: 1px solid var(--borda);
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    .card-header-componente {
      background-color: var(--azul-principal);
      color: white;
      border-radius: 10px 10px 0 0 !important;
      padding: 15px 20px;
    }

    .btn-voltar {
      background-color: var(--azul-principal);
      color: white;
      border: none;
      padding: 10px 25px;
      border-radius: 5px;
      margin-top: 20px;
    }

    .btn-voltar:hover {
      background-color: #1e4a7e;
      color: white;
    }

    .table-notas {
      width: 100%;
      border-collapse: collapse;
    }

    .table-notas th {
      background-color: #f8f9fa;
      color: var(--azul-principal);
      padding: 12px 15px;
      text-align: left;
      border-bottom: 2px solid var(--borda);
    }

    .table-notas td {
      padding: 12px 15px;
      border-bottom: 1px solid var(--borda);
    }

    .table-notas tr:hover {
      background-color: transparent;
    }

    .rp-column {
      color: #dc3545;
      font-weight: 500;
    }

    .resultado-aprovado {
      color: #28a745;
      font-weight: 600;
    }

    .resultado-reprovado {
      color: #dc3545;
      font-weight: 600;
    }

    .btn-acoes {
      display: flex;
      justify-content: space-between;
      margin-top: 20px;
    }

    .btn-imprimir {
      background-color: white;
      color: var(--azul-principal);
      border: 1px solid var(--azul-principal);
      padding: 10px 25px;
      border-radius: 5px;
    }

    .btn-imprimir:hover {
      background-color: #f0f4f9;
    }

    /* Estilo para informações do aluno */
    .info-aluno {
      background-color: #f8fafc;
      border-radius: 8px;
      padding: 15px;
      margin-bottom: 20px;
      border-left: 4px solid var(--azul-principal);
    }

    .info-aluno h5 {
      color: var(--azul-principal);
      margin-bottom: 10px;
    }

    .info-aluno p {
      margin-bottom: 5px;
      color: var(--texto-escuro);
    }

    /* Estilo para o nome da matéria */
    .nome-materia {
      font-size: 1.2rem;
      color: var(--borda);
      font-weight: 600;
      margin-bottom: 20px;
      padding-bottom: 10px;
      border-bottom: 1px solid var(--borda);
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
            <div class="card card-componente">
              <div class="card-header card-header-componente">
                <h4 class="mb-0">Consulta de Notas</h4>
              </div>
              <div class="card-body">
                <!-- Informações do Aluno -->
                <div class="info-aluno">
                  <h5>Informações do Aluno</h5>
                  <p><strong>Nome:</strong> João da Silva</p>
                  <p><strong>Matrícula:</strong> 20230001</p>
                  <p><strong>Período:</strong> 2023.1</p>
                </div>

                <!-- Nome da Matéria Fixa -->
                <div class="nome-materia">
                  Matemática
                </div>

                <!-- Tabela de Notas -->
                <div class="table-responsive">
                  <table class="table-notas">
                    <thead>
                      <tr>
                        <th>Notas 1° Trimestre</th>
                        <th>Rp 1° Trimestre</th>
                        <th>Notas 2° Trimestre</th>
                        <th>Rp 2° Trimestre</th>
                        <th>Notas 3° Trimestre</th>
                        <th>Rp 3° Trimestre</th>
                        <th>Média Final</th>
                        <th>Resultado</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td>8,5</td>
                        <td class="rp-column">-</td>
                        <td>7,2</td>
                        <td class="rp-column">-</td>
                        <td>9,0</td>
                        <td class="rp-column">-</td>
                        <td>8,2</td>
                        <td class="resultado-aprovado">Aprovado</td>
                      </tr>
                    </tbody>
                  </table>
                </div>

                <!-- Legenda -->
                <div class="mt-3">
                  <small><strong>Legenda:</strong> Rp = Recuperação Paralela</small>
                </div>

                <!-- Botões de Ação -->
                <div class="btn-acoes">
                  <a href="notas.php" class="btn btn-voltar">
                    <i class="zmdi zmdi-arrow-left mr-2"></i> Voltar
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
         <div class="overlay toggle-menu"></div>
    </div>

    <footer class="footer">
      <div class="container">
        <div class="text-center">
          Copyright © 2023 Dashboard Acadêmico
        </div>
      </div>
    </footer>
  </div>

  <!-- Scripts -->
  <script src="../assets/js/jquery.min.js"></script>
  <script src="../assets/js/popper.min.js"></script>
  <script src="../assets/js/bootstrap.min.js"></script>
  <script src="../assets/plugins/simplebar/js/simplebar.js"></script>
  <script src="../assets/js/sidebar-menu.js"></script>
  <script src="../assets/js/app-script.js"></script>
  <script src="botaoSair.js"></script>
  <script>
    $(document).ready(function () {

    });
  </script>
</body>

</html>