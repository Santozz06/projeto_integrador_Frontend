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
    :root {
      --azul-cabecalho: #2c5f9e;
      /* Azul mais suave e profissional */
      --texto-preto: #333333;
      /* Preto mais suave que #000 */
      --cinza-texto: #666666;
    }

    .document-option {
      transition: all 0.3s ease;
      background-color: white;
    }

    .document-option:hover {
      transform: translateY(-3px);
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
      border-color: var(--azul-cabecalho) !important;
    }

    .badge-warning {
      background-color: #f6c23e;
      color: #fff;
    }

    .badge-success {
      background-color: #1cc88a;
      color: #fff;
    }

    .card-header {
      border-radius: 0.35rem 0.35rem 0 0 !important;
      background-color: var(--azul-cabecalho) !important;
    }

    .document-option h5 {
      color: var(--texto-preto) !important;
      font-weight: 600;
    }

    .document-option p {
      color: var(--cinza-texto) !important;
    }

    .card-header h4 {
      color: white !important;
      font-weight: 600;
    }

    .document-option {
      padding: 20px;
      margin-bottom: 15px;
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
                <a href="boletim.php" class="text-decoration-none">
                  <div class="document-option mb-4 border rounded">
                    <h5>EMITIR BOLETIM</h5>
                    <p class="mb-2">Acesse seu boletim acadêmico atual</p>
                  </div>
                </a>

                <!-- ATESTADO DE MATRÍCULA -->
                <a href="atestado_matricula.php" target="_blank" class="text-decoration-none">
                  <div class="document-option mb-4 border rounded">
                    <h5>ATESTADO DE MATRÍCULA</h5>
                    <p class="mb-2">Gere seu comprovante de matrícula</p>
                  </div>
                </a>
                <!-- EMITIR HISTÓRICO -->
                <a href="historico.php" class="text-decoration-none">
                  <div class="document-option mb-4 border rounded">
                    <h5>EMITIR HISTÓRICO</h5>
                    <p class="mb-2">Obtenha seu histórico escolar</p>
                  </div>
                </a>

                <!-- ATESTADO DE FREQUÊNCIA -->
                <a href="atestado_frequencia.php" class="text-decoration-none">
                  <div class="document-option mb-4 border rounded">
                    <h5>ATESTADO DE FREQUÊNCIA</h5>
                    <p class="mb-2">Comprove sua frequência nas aulas</p>
                  </div>
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