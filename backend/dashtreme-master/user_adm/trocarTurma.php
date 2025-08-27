<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Trocar de Turma - Dashboard Acadêmico</title>
  <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
  <link rel="stylesheet" href="../assets/css/app-style.css">
  <link rel="stylesheet" href="../assets/css/icons.css">
  <link rel="stylesheet" href="../assets/plugins/simplebar/css/simplebar.css">
  <link rel="stylesheet" href="../assets/css/sidebar-menu.css">
  <link rel="stylesheet" href="style.css">
  <style>
    html, body {
      height: 100%;
      min-height: 100%;
      margin: 0;
      padding: 0;
      display: flex;
      flex-direction: column;
    }
    body {
      flex: 1 0 auto;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }
    .content-wrapper {
      flex: 1 0 auto;
    }
    .footer {
      flex-shrink: 0;
      background: transparent;
      color: #fff;
      border: none;
      text-align: center;
      padding: 15px 0 10px 0;
    }
    .btn-custom-primary {
      background-color: #1abc9c !important;
      color: white !important;
      border: none !important;
    }

    .btn-custom-primary:hover {
      background-color: #16a085 !important;
    }

    .btn-custom-secondary {
      background-color: #2c5f9e !important;
      color: white !important;
      border: none !important;
    }

    .btn-custom-secondary:hover {
      background-color: #1e4a7e !important;
    }

    label {
      font-weight: bold;
      color: #fff;
    }

    .d-none {
      display: none;
    }

    .search-box {
      margin-bottom: 20px;
    }

    .navbar {
      background-color: rgba(0, 0, 0, 0.2) !important;
      backdrop-filter: blur(10px);
    }
  </style>
</head>

<body class="bg-theme bg-theme1">
  <?php
  require("menu_padrão.php");
  ?>

  <div class="content-wrapper">
    <div class="container-fluid">
      <div class="row pt-2 pb-2">
        <div class="col-sm-12">
          <h4 class="page-title">Trocar de Turma</h4>
        </div>
      </div>

      <form id="formBuscaAluno" class="search-box">
        <div class="form-group">
          <label for="buscaAluno">Pesquisar por nome ou matrícula:</label>
          <input type="text" class="form-control" id="buscaAluno" placeholder="Digite o nome ou matrícula...">
        </div>
        <div class="form-group text-right">
          <button type="submit" class="btn btn-custom-secondary">Buscar</button>
        </div>
      </form>

      <div id="resultadoBusca" class="d-none">
        <div class="card bg-transparent border-0">
          <div class="card-body">
            <h5 class="card-title text-white">Dados do aluno</h5>
            <p class="text-white"><strong>Nome:</strong> <span id="resultadoNome"></span></p>
            <p class="text-white"><strong>Matrícula:</strong> <span id="resultadoMatricula">20251001</span></p>
            <p class="text-white"><strong>Turma atual:</strong> <span id="resultadoTurma">1º Ano A</span></p>

            <div class="form-group mt-3">
              <label for="novaTurma">Nova turma:</label>
              <select id="novaTurma" class="form-control">
                <option value="">Selecione a nova turma...</option>
                <option>1º Ano B</option>
                <option>2º Ano A</option>
                <option>3º Ano A</option>
              </select>
            </div>

            <div class="form-group text-right">
              <button class="btn btn-custom-primary" id="btnConfirmarTroca">
                <i class="zmdi zmdi-refresh mr-1"></i> Confirmar troca
              </button>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
  <!--Overlay-->
  <div class="overlay toggle-menu"></div>


  </div>

  <script src="../assets/js/jquery.min.js"></script>
  <script src="../assets/js/bootstrap.min.js"></script>
  <script src="../assets/plugins/simplebar/js/simplebar.js"></script>
  <script src="../assets/js/sidebar-menu.js"></script>
  <script src="../assets/js/app-script.js"></script>
  <script src="botaoSair.js"></script>
  <script>
    $(document).ready(function () {
      $('#formBuscaAluno').on('submit', function (e) {
        e.preventDefault();
        const termo = $('#buscaAluno').val().trim();

        if (termo !== '') {
          $('#resultadoNome').text(termo);
          $('#resultadoMatricula').text('20251001');
          $('#resultadoTurma').text('1º Ano A');
          $('#resultadoBusca').removeClass('d-none');
          $('html, body').animate({ scrollTop: $('#resultadoBusca').offset().top - 100 }, 500);
        }
      });

      $('#btnConfirmarTroca').on('click', function () {
        const novaTurma = $('#novaTurma').val();
        if (!novaTurma) {
          alert('Por favor, selecione uma nova turma.');
        } else {
          const nome = $('#resultadoNome').text();
          alert('Aluno ' + nome + ' transferido para: ' + novaTurma);
        }
      });
    });
  </script>
</body>

</html>