<?php
session_start();
require_once './includes/conexao.php';

// Redirecionamento simplificado se já estiver logado
if (isset($_SESSION['usuario_id']) && isset($_SESSION['user_type'])) {
  $redirecionamentos = [
    'admin' => 'user_adm/home.php',
    'professor' => 'user_professor/home.php',
    'aluno' => 'user_aluno/index.php'
  ];

  $paginaDestino = $redirecionamentos[$_SESSION['user_type']] ?? 'user_aluno/index.php';
  header("Location: $paginaDestino");
  exit();
}
?>

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
<style>
  .texto-professores {
    color: #ffffffff !important;
  }

  .texto-professores:hover {
    color: #ffffffff !important;
  }
</style>

<body class="bg-theme bg-theme1">

  <!-- conteúdo do seu HTML atual -->
  <div class="card card-authentication1 mx-auto my-5">
    <div class="card-body">
      <div class="card-content p-2">
        <div class="text-center">
          <img src="assets/images/logo-icon.png" alt="logo icon">
        </div>
        <div class="card-title text-uppercase text-center py-3">Login</div>

        <!-- Mensagem de erro -->
        <!-- Adicione estas mensagens de erro -->
        <?php if (isset($_GET['status']) && $_GET['status'] == 'erro_tipo'): ?>
          <div class="alert alert-warning alert-dismissible fade show" role="alert">
            Este usuário não tem acesso como <?php echo $_POST['userType'] ?? 'este tipo'; ?>!
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
        <?php endif; ?>

        <?php if (isset($_GET['status']) && $_GET['status'] == 'erro'): ?>
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            Usuário ou senha inválidos!
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
        <?php endif; ?>

        <?php if (isset($_GET['status']) && $_GET['status'] == 'logout'): ?>
          <div class="alert alert-success alert-dismissible fade show" role="alert">
            Logout realizado com sucesso!
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
        <?php endif; ?>

        <form action="autenticar.php" method="POST" id="loginForm">
          <div class="form-group">
            <label for="login" class="sr-only">Usuário ou Email</label>
            <div class="position-relative has-icon-right">
              <input type="text" id="login" name="login" class="form-control input-shadow"
                placeholder="Digite seu usuário ou email" required
                value="<?php echo isset($_GET['login']) ? htmlspecialchars($_GET['login']) : ''; ?>">
              <div class="form-control-position">
                <i class="icon-user"></i>
              </div>
            </div>
          </div>
          <div class="form-group">
            <label for="senha" class="sr-only">Senha</label>
            <div class="position-relative has-icon-right">
              <input type="password" id="senha" name="senha" class="form-control input-shadow"
                placeholder="Digite sua senha" required>
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
                <input type="checkbox" id="user-checkbox" name="lembrar" />
                <label for="user-checkbox">Lembrar-me</label>
              </div>
            </div>
            <div class="form-group col-6 text-right">
              <a href="reset-password.php">Esqueci minha senha</a>
            </div>
          </div>
          <button type="submit" class="btn btn-light btn-block">Entrar</button>
        </form>
      </div>
    </div>
    <div class="card-footer text-center py-3">
      <p class="text-warning mb-0">É aluno? <a href="register.php"> Cadastre-se aqui</a></p>
      <p class="texto-professores mb-0 mt-2"><small>Professores: entre em contato com a secretaria para cadastro</small></p>
    </div>
  </div>

  <!-- scripts -->
  <script src="assets/js/jquery.min.js"></script>
  <script src="assets/js/popper.min.js"></script>
  <script src="assets/js/bootstrap.min.js"></script>
  <script src="assets/js/app-script.js"></script>
</body>

</html>