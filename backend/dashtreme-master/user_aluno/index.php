<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta name="description" content="Dashboard Acadêmico" />
  <meta name="author" content="" />
  <title>Dashboard Acadêmico</title>
  <!-- loader-->
  <link href="../assets/css/pace.min.css" rel="stylesheet" />
  <script src="../assets/js/pace.min.js"></script>
  <!--favicon-->
  <link rel="icon" href="../assets/images/favicon.ico" type="image/x-icon">
  <!-- simplebar CSS-->
  <link href="../assets/plugins/simplebar/css/simplebar.css" rel="stylesheet" />
  <!-- Bootstrap core CSS-->
  <link href="../assets/css/bootstrap.min.css" rel="stylesheet" />
  <!-- animate CSS-->
  <link href="../assets/css/animate.css" rel="stylesheet" type="text/css" />
  <!-- Icons CSS-->
  <link href="../assets/css/icons.css" rel="stylesheet" type="text/css" />
  <!-- Sidebar CSS-->
  <link href="../assets/css/sidebar-menu.css" rel="stylesheet" />
  <!-- Custom Style-->
  <link href="../assets/css/app-style.css" rel="stylesheet" />

  <style>
    .navbar {
      background-color: rgba(0, 0, 0, 0.2) !important;
      backdrop-filter: blur(10px);
    }

    /* Efeito para o botão Sair */
    #logout-btn {
      transition: all 0.3s ease;
      border-radius: 4px;
      padding: 8px 12px;
    }

    #logout-btn:hover {
      background-color: #ff4444 !important;
      /* Vermelho suave */
      color: white !important;
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(255, 68, 68, 0.2);
    }

    #logout-btn i {
      transition: all 0.3s ease;
    }

    #logout-btn:hover i {
      transform: rotate(15deg);
    }
  </style>
</head>

<body class="bg-theme bg-theme1">

  <!-- Start wrapper-->
  <div id="wrapper">
    <?php
    require("menu_padrao.php");
    ?>

    <div class="clearfix"></div>

    <div class="content-wrapper">
      <div class="container-fluid">

        <!-- Bloco de Frequência/Informações Acadêmicas -->
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h5>Frequência</h5>
              </div>
              <div class="card-body">
                <div class="alert alert-light">Não há frequências registradas</div>

                <div class="row mt-4">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Ano atual</label>
                      <input type="text" class="form-control" readonly>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Matrícula</label>
                      <input type="text" class="form-control" readonly>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Bloco de Eventos Próximos -->
        <div class="row mt-4">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h5>Eventos próximos</h5>
              </div>
              <div class="card-body">
                <div class="alert alert-light">No momento não tem eventos</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Bloco de Aulas -->
        <div class="row mt-4">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h5>Aulas</h5>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table">
                    <thead>
                      <tr>
                        <th>Segunda</th>
                        <th>Terça</th>
                        <th>Quarta</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td>Matemática<br>7:30 - 9:40</td>
                        <td></td>
                        <td></td>
                      </tr>
                      <tr>
                        <td></td>
                        <td>Geografia<br>10:00 - 11:30</td>
                        <td></td>
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

    </div>

    <!--Start footer-->
    <footer class="footer">
      <div class="container">
        <div class="text-center">
          Copyright © 2023 Dashboard Acadêmico
        </div>
      </div>
    </footer>
    <!--End footer-->

  </div>

  <!-- Bootstrap core JavaScript-->
  <script src="../assets/js/jquery.min.js"></script>
  <script src="../assets/js/popper.min.js"></script>
  <script src="../assets/js/bootstrap.min.js"></script>

  <!-- simplebar js -->
  <script src="../assets/plugins/simplebar/js/simplebar.js"></script>
  <!-- sidebar-menu js -->
  <script src="../assets/js/sidebar-menu.js"></script>
  <!-- loader scripts -->
  <script src="../assets/js/jquery.loading-indicator.js"></script>
  <!-- Custom scripts -->
  <script src="../assets/js/app-script.js"></script>

  <script>
    // Verifica se o usuário está logado e no lugar certo
    const expectedUserType = window.location.pathname.includes('professor') ? 'professor' :
      window.location.pathname.includes('aluno') ? 'aluno' : 'admin';

    if (localStorage.getItem('isLoggedIn') !== 'true' ||
      localStorage.getItem('userType') !== expectedUserType) {
      localStorage.clear();
      window.location.href = '../login.php';
    }

    function logout() {
      // Remove os dados de autenticação
      localStorage.removeItem('isLoggedIn');
      localStorage.removeItem('userType');
      localStorage.removeItem('username');

      // Adiciona o alerta antes do redirecionamento
      alert('Você saiu do sistema!');
      window.location.href = '../login.php';
    }

    // Vincula ao botão "Sair"
    document.addEventListener('DOMContentLoaded', function () {
      const logoutBtn = document.getElementById('logout-btn');
      if (logoutBtn) {
        logoutBtn.addEventListener('click', function (e) {
          e.preventDefault();
          logout();
        });
      }
    });
  </script>
</body>

</html>