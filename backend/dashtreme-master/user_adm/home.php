<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta name="description" content="Dashboard Acadêmico - Administrador" />
  <meta name="author" content="" />
  <title>Dashboard Acadêmico - Admin</title>
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
  <link href="style.css" rel="stylesheet" />
  <style>
    /* ALUNOS - Azul médio */
    .btn-alunos {
      background-color: #377dff;
      color: white;
      border: none;
    }

    .btn-alunos:hover {
      background-color: #2b6edb;
    }

    /* TURMAS - Verde azulado */
    .btn-turmas {
      background-color: #1abc9c;
      color: white;
      border: none;
    }

    .btn-turmas:hover {
      background-color: #16a085;
    }

    /* SERVIDORES - Azul escuro */
    .btn-servidores {
      background-color: #3498db;
      color: white;
      border: none;
    }

    .btn-servidores:hover {
      background-color: #2980b9;
    }

    /* CALENDÁRIO - Roxo claro */
    .btn-calendario {
      background-color: #8e44ad;
      color: white;
      border: none;
    }

    .btn-calendario:hover {
      background-color: #6c3483;
    }

    .alert-warning {
      background-color: #9b59b6;
      color: #fff;
      border: none;
    }

    .navbar {
      background-color: rgba(0, 0, 0, 0.2) !important;
      backdrop-filter: blur(10px);
    }

    /* Estilos para as notificações */
    .dropdown-notifications {
      width: 350px;
      padding: 0;
    }

    .dropdown-notifications .dropdown-header {
      padding: 1rem;
      border-bottom: 1px solid #e9ecef;
    }

    .dropdown-notifications .dropdown-list {
      max-height: 300px;
      overflow-y: auto;
    }

    .dropdown-notifications .dropdown-item {
      padding: 0.75rem 1rem;
      border-bottom: 1px solid #e9ecef;
      transition: all 0.3s;
    }

    .dropdown-notifications .dropdown-item:hover {
      background-color: #f8f9fa;
    }

    .dropdown-notifications .dropdown-item .media {
      align-items: center;
    }

    .dropdown-notifications .dropdown-item .notify-icon {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-right: 15px;
      color: white;
    }

    .dropdown-notifications .dropdown-item .notify-details {
      flex: 1;
    }

    .dropdown-notifications .dropdown-item .notify-title {
      margin-bottom: 0.25rem;
      font-size: 0.875rem;
      font-weight: 500;
    }

    .dropdown-notifications .dropdown-item .notify-time {
      font-size: 0.75rem;
      color: #6c757d;
    }

    .dropdown-notifications .dropdown-footer {
      padding: 0.75rem;
      text-align: center;
      border-top: 1px solid #e9ecef;
    }

    .dropdown-notifications .dropdown-footer a {
      color: #6c757d;
      font-size: 0.875rem;
    }

    .dropdown-notifications .dropdown-footer a:hover {
      color: #007bff;
      text-decoration: none;
    }

    .bg-primary-light {
      background-color: rgba(55, 125, 255, 0.1);
    }

    .bg-success-light {
      background-color: rgba(40, 167, 69, 0.1);
    }

    .bg-warning-light {
      background-color: rgba(255, 193, 7, 0.1);
    }

    /* Estilos para os botões em mobile */
    @media (max-width: 768px) {
      .btn-round {
        width: 100%;
        margin-bottom: 15px;
        padding: 12px;
        font-size: 14px;
      }

      .btn-round i {
        font-size: 24px;
        margin-bottom: 5px;
      }

      .card-body .row.text-center>div {
        padding: 0 8px;
      }
    }
  </style>

</head>

<body class="bg-theme bg-theme1">

  <?php
  require("menu_home.php");
  ?>


  <div class="clearfix"></div>

  <div class="content-wrapper">
    <div class="container-fluid">


      <div class="row">
        <div class="col-12 col-lg-8">
          <div class="card">
            <div class="card-header text-white" style="background-color: var(--azul-cabecalho);">
              <h5 class="text-white">Painel do Administrador</h5>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-6">
                  <div class="card bg-light-primary">
                    <div class="card-body">
                      <h6 class="card-title">Ano Letivo Atual</h6>
                      <h4 class="mb-0">2025</h4>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="card bg-light-success">
                    <div class="card-body">
                      <h6 class="card-title">Total de Matrículas</h6>
                      <h4 class="mb-0">1,245</h4>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-12 col-lg-4">
          <div class="card">
            <div class="card-header">
              <h5>Avisos Importantes</h5>
            </div>
            <div class="card-body">
              <div class="alert alert-warning">
                <strong>Data limite:</strong> A documentação Y deve ser enviada para a direção até 15/03/2025
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row mt-4">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h5>Acesso Rápido</h5>
            </div>
            <div class="card-body">
              <div class="row text-center">
                <div class="col-6 col-md-3">
                  <a href="cadastro.php#aluno" class="btn btn-round btn-alunos">
                    <i class="zmdi zmdi-accounts-alt fa-2x"></i><br> Alunos
                  </a>
                </div>
                <div class="col-6 col-md-3">
                  <a href="cadastroTurmas.php" class="btn btn-round btn-turmas">
                    <i class="zmdi zmdi-group-work fa-2x"></i><br> Turmas
                  </a>
                </div>
                <div class="col-6 col-md-3">
                  <a href="cadastro.php#servidor" class="btn btn-round btn-servidores">
                    <i class="zmdi zmdi-account-box fa-2x"></i><br> Servidores
                  </a>
                </div>
                <div class="col-6 col-md-3">
                  <a href="calendario.php" class="btn btn-round btn-calendario">
                    <i class="zmdi zmdi-calendar fa-2x"></i><br> Calendário
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Bloco de Últimas Movimentações -->
      <div class="row mt-4">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h5>Últimas Movimentações</h5>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table">
                  <thead>
                    <tr>
                      <th>Data</th>
                      <th>Aluno</th>
                      <th>Tipo</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>10/03/2025</td>
                      <td>João Silva</td>
                      <td><span class="badge badge-success">Matrícula</span></td>
                    </tr>
                    <tr>
                      <td>08/03/2025</td>
                      <td>Maria Souza</td>
                      <td><span class="badge badge-info">Transferência</span></td>
                    </tr>
                    <tr>
                      <td>05/03/2025</td>
                      <td>Carlos Oliveira</td>
                      <td><span class="badge badge-primary">Rematrícula</span></td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>


      <!--start overlay-->
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
  <script src="botaoSair.js"></script>

  <script>
    $(document).ready(function () {
      // Simular notificações
      const notificacoes = [
        {
          id: 1,
          tipo: 'primary',
          icone: 'zmdi-account-add',
          titulo: 'Novo aluno matriculado',
          tempo: 'Há 5 minutos',
          lida: false
        },
        {
          id: 2,
          tipo: 'success',
          icone: 'zmdi-calendar-check',
          titulo: 'Reunião pedagógica hoje',
          tempo: 'Há 1 hora',
          lida: false
        },
        {
          id: 3,
          tipo: 'warning',
          icone: 'zmdi-alert-circle',
          titulo: 'Documentação pendente',
          tempo: 'Ontem',
          lida: false
        }
      ];

      // Atualizar contador de notificações
      function atualizarContador() {
        const naoLidas = notificacoes.filter(n => !n.lida).length;
        $('.alert-count').text(naoLidas);
        if (naoLidas === 0) {
          $('.alert-count').hide();
        } else {
          $('.alert-count').show();
        }
      }

      // Marcar todas como lidas
      $('#marcarTodasComoLidas').click(function () {
        notificacoes.forEach(n => n.lida = true);
        atualizarContador();
        $(this).closest('.dropdown-menu').find('.dropdown-item').addClass('lida');
        $(this).text('Todas marcadas como lidas');
      });

      // Marcar notificação individual como lida ao clicar
      $(document).on('click', '.dropdown-item', function () {
        const index = $(this).index() - 1; // -1 porque o primeiro item é o cabeçalho
        if (index >= 0 && index < notificacoes.length) {
          notificacoes[index].lida = true;
          atualizarContador();
          $(this).addClass('lida');
        }
      });

      // Inicializar
      atualizarContador();
    });
  </script>
</body>

</html>