<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <title>Componentes Curriculares - Dashboard Acadêmico</title>
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
      background-color: rgba(44, 95, 158, 0.1);
      color: #000;
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

    <!-- Conteúdo -->
    <div class="content-wrapper">
      <div class="container-fluid">
        <div class="row justify-content-center mt-4">
          <div class="col-lg-10">
            <div class="card card-componente">
              <div class="card-header card-header-componente">
                <h4 class="mb-0">Notas individuais por componente curricular - 2025</h4>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table-notas">
                    <thead>
                      <tr>
                        <th>Componente Curricular</th>
                        <th>Local</th>
                        <th>Horário</th>
                      </tr>
                    </thead>
                    <tbody>
                    <tbody>
                      <tr onclick="window.location.href='notas_pt.php'" style="cursor: pointer;">
                        <td>Lingua Portuguesa</td>
                        <td>SALA 12 - Turma 160 - 6º ANO</td>
                        <td>4M45 *</td>
                      </tr>
                      <tr onclick="window.location.href='notas_matematica.php'" style="cursor: pointer;">
                        <td>Matemática</td>
                        <td>SALA 12 - Turma 160 - 6º ANO</td>
                        <td>5M45 *</td>
                      </tr>
                      <tr onclick="window.location.href='notas_historia.php'" style="cursor: pointer;">
                        <td>História</td>
                        <td>SALA 12 - Turma 160 - 6º ANO</td>
                        <td>6M45 *</td>
                      </tr>
                      <tr onclick="window.location.href='notas_geografia.php'" style="cursor: pointer;">
                        <td>Geografia</td>
                        <td>SALA 12 - Turma 160 - 6º ANO</td>
                        <td>9M45 *</td>
                      </tr>
                      <tr onclick="window.location.href='notas_ciencias.php'" style="cursor: pointer;">
                        <td>Ciências</td>
                        <td>SALA 12 - Turma 160 - 6º ANO</td>
                        <td>4M12 *</td>
                      </tr>
                      <tr onclick="window.location.href='notas_arte.php'" style="cursor: pointer;">
                        <td>Arte</td>
                        <td>SALA 12 - Turma 160 - 6º ANO</td>
                        <td>5M12 *</td>
                      </tr>
                      <tr onclick="window.location.href='notas_ed_fisica.php'" style="cursor: pointer;">
                        <td>Educação Física</td>
                        <td>SALA 12 - Turma 160 - 6º ANO</td>
                        <td>3M45 *</td>
                      </tr>
                      <tr onclick="window.location.href='notas_ensino_religioso.php'" style="cursor: pointer;">
                        <td>Ensino Religioso</td>
                        <td>SALA 12 - Turma 160 - 6º ANO</td>
                        <td>2M45 5M3 *</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
                <div class="mt-2">
                  <small>* A turma possui horário flexível ou o horário exibido é da semana atual.</small>
                </div>
                <a href="index.php" class="btn btn-voltar mt-3">
                  <i class="zmdi zmdi-arrow-left mr-2"></i> Voltar
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
         <div class="overlay toggle-menu"></div>
    </div>

  </div>

  <!-- Scripts -->
  <script src="../assets/js/jquery.min.js"></script>
  <script src="../assets/js/popper.min.js"></script>
  <script src="../assets/js/bootstrap.min.js"></script>
  <script src="../assets/plugins/simplebar/js/simplebar.js"></script>
  <script src="../assets/js/sidebar-menu.js"></script>
  <script src="../assets/js/app-script.js"></script>
  <script src="botaoSair.js"></script>
</body>

</html>