<?php require_once '../includes/bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <title>Ensino - Dashboard Acadêmico</title>
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
    .document-option {
      transition: box-shadow 0.3s, transform 0.2s;
      background: rgba(255,255,255,0.04);
      border: 1px solid rgba(20,182,255,0.13);
      border-radius: 0.5rem;
      box-shadow: 0 2px 8px rgba(20,182,255,0.07);
      padding: 22px 24px 18px 24px;
      margin-bottom: 18px;
      color: #fff;
    }
    .document-option:hover {
      background: rgba(20,182,255,0.10);
      box-shadow: 0 6px 18px rgba(20,182,255,0.13);
      border-color: #14b6ff !important;
      transform: translateY(-2px) scale(1.01);
      text-decoration: none;
    }
    .document-option h5 {
      color: #14b6ff !important;
      font-weight: 600;
      letter-spacing: 0.5px;
      margin-bottom: 6px;
    }
    .document-option p {
      color: #b8c7ce !important;
      font-size: 1em;
      margin-bottom: 0;
    }
    .card-header {
      border-radius: 0.5rem 0.5rem 0 0 !important;
      background: linear-gradient(90deg, #2c5f9e 60%, #14b6ff 100%) !important;
      border: none;
    }
    .card-header h4 {
      color: #fff !important;
      font-weight: 600;
      margin-bottom: 0;
      letter-spacing: 0.5px;
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
                <h4 class="mb-0"><i class="zmdi zmdi-assignment mr-2"></i> Gerencie seus documentos acadêmicos</h4>
              </div>
              <div class="card-body">
                <!-- EMITIR BOLETIM -->
                <a href="boletim.php" class="document-option d-block mb-4">
                  <h5><i class="zmdi zmdi-file-text mr-2"></i> Emitir Boletim</h5>
                  <p>Acesse seu boletim acadêmico atual</p>
                </a>

                <!-- ATESTADO DE MATRÍCULA -->
                <a href="atestado_matricula.php" target="_blank" class="document-option d-block mb-4">
                  <h5><i class="zmdi zmdi-assignment mr-2"></i> Atestado de Matrícula</h5>
                  <p>Gere seu comprovante de matrícula</p>
                </a>
                <!-- EMITIR HISTÓRICO -->
                <a href="historico.php" class="document-option d-block mb-4">
                  <h5><i class="zmdi zmdi-collection-text mr-2"></i> Emitir Histórico</h5>
                  <p>Obtenha seu histórico escolar</p>
                </a>

                <!-- ATESTADO DE FREQUÊNCIA -->
                <a href="atestado_frequencia.php" class="document-option d-block mb-4">
                  <h5><i class="zmdi zmdi-check-square mr-2"></i> Atestado de Frequência</h5>
                  <p>Comprove sua frequência nas aulas</p>
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
</body>

</html>