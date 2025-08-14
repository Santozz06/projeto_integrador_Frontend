<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta name="description" content="" />
  <meta name="author" content="" />
  <title>Dashboard Acadêmico - Login</title>
  <!-- loader-->
  <link href="assets/css/pace.min.css" rel="stylesheet" />
  <script src="assets/js/pace.min.js"></script>
  <!--favicon-->
  <link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">
  <!-- Bootstrap core CSS-->
  <link href="assets/css/bootstrap.min.css" rel="stylesheet" />
  <!-- animate CSS-->
  <link href="assets/css/animate.css" rel="stylesheet" type="text/css" />
  <!-- Icons CSS-->
  <link href="assets/css/icons.css" rel="stylesheet" type="text/css" />
  <!-- Custom Style-->
  <link href="assets/css/app-style.css" rel="stylesheet" />

</head>

<body class="bg-theme bg-theme1">

  <!-- start loader -->
  <div id="pageloader-overlay" class="visible incoming">
    <div class="loader-wrapper-outer">
      <div class="loader-wrapper-inner">
        <div class="loader"></div>
      </div>
    </div>
  </div>
  <!-- end loader -->

  <!-- Start wrapper-->
  <div id="wrapper">

    <div class="loader-wrapper">
      <div class="lds-ring">
        <div></div>
        <div></div>
        <div></div>
        <div></div>
      </div>
    </div>
    <div class="card card-authentication1 mx-auto my-5">
      <div class="card-body">
        <div class="card-content p-2">
          <div class="text-center">
            <img src="assets/images/logo-icon.png" alt="logo icon">
          </div>
          <div class="card-title text-uppercase text-center py-3">Entrar</div>
          <form>
            <div class="form-group">
              <label for="exampleInputUsername" class="sr-only">Usuário</label>
              <div class="position-relative has-icon-right">
                <input type="text" id="exampleInputUsername" class="form-control input-shadow"
                  placeholder="Digite seu usuário">
                <div class="form-control-position">
                  <i class="icon-user"></i>
                </div>
              </div>
            </div>
            <div class="form-group">
              <label for="exampleInputPassword" class="sr-only">Senha</label>
              <div class="position-relative has-icon-right">
                <input type="password" id="exampleInputPassword" class="form-control input-shadow"
                  placeholder="Digite sua senha">
                <div class="form-control-position">
                  <i class="icon-lock"></i>
                </div>
              </div>
            </div>
            <div class="form-group">
              <div class="icheck-material-white">
                <input type="radio" id="type-professor" name="userType" value="professor" checked>
                <label for="type-professor">Professor</label>

                <input type="radio" id="type-aluno" name="userType" value="aluno" class="ml-3">
                <label for="type-aluno">Aluno</label>

                <input type="radio" id="type-admin" name="userType" value="admin" class="ml-3">
                <label for="type-admin">Admin</label>
              </div>
            </div>
            <div class="form-row">
              <div class="form-group col-6">
                <div class="icheck-material-white">
                  <input type="checkbox" id="user-checkbox" checked="" />
                  <label for="user-checkbox">Lembrar-me</label>
                </div>
              </div>
              <div class="form-group col-6 text-right">
                <a href="reset-password.php">Esqueci minha senha</a>
              </div>
            </div>
            <button type="button" class="btn btn-light btn-block" id="loginBtn">Entrar</button>
          </form>
        </div>
      </div>
      <div class="card-footer text-center py-3">
        <p class="text-warning mb-0">Não tem uma conta? <a href="register.php"> Cadastre-se aqui</a></p>
      </div>
    </div>

    <!--Start Back To Top Button-->
    <a href="javaScript:void();" class="back-to-top"><i class="fa fa-angle-double-up"></i> </a>
    <!--End Back To Top Button-->

  </div><!--wrapper-->

  <!-- Bootstrap core JavaScript-->
  <script src="assets/js/jquery.min.js"></script>
  <script src="assets/js/popper.min.js"></script>
  <script src="assets/js/bootstrap.min.js"></script>

  <!-- sidebar-menu js -->
  <script src="assets/js/sidebar-menu.js"></script>

  <!-- Custom scripts -->
  <script src="assets/js/app-script.js"></script>
  <script>
    function handleLogin() {
      const username = document.getElementById('exampleInputUsername').value;
      const password = document.getElementById('exampleInputPassword').value;
      const userType = document.querySelector('input[name="userType"]:checked')?.value;

      if (!username || !password || !userType) {
        alert('Preencha todos os campos!');
        return;
      }

      // Simula um delay para processamento
      setTimeout(() => {
        localStorage.setItem('isLoggedIn', 'true');
        localStorage.setItem('userType', userType);

        // Redirecionamentos absolutos 
        const redirectPages = {
          'professor': 'user_professor/home.php',
          'aluno': 'user_aluno/index.php',
          'admin': 'user_adm/home.php'
        };

        window.location.href = redirectPages[userType];
      }, 300); 
    }

    // Configuração inicial
    document.addEventListener('DOMContentLoaded', function () {
      document.getElementById('loginBtn').addEventListener('click', handleLogin);
    });
  </script>
</body>

</html>