<?php
session_start();
require_once './includes/conexão.php'; 

// Verifica se o formulário foi submetido
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login = $_POST['login'] ?? '';
    $senha = $_POST['senha'] ?? '';
    $userType = $_POST['userType'] ?? '';
    
    // Consulta o usuário no banco de dados
    $stmt = $pdo->prepare("SELECT * FROM Usuarios WHERE Login = ?");
    $stmt->execute([$login]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($usuario) {
        // Verifica a senha (em texto plano para teste, como você pediu)
        if ($senha === $usuario['Senha']) {
            // Verifica o tipo de usuário
            if ($userType === 'admin' && $usuario['IsAdmin']) {
                $_SESSION['usuario_id'] = $usuario['ID_Usuario'];
                $_SESSION['usuario_nome'] = $usuario['Nome_Completo'];
                $_SESSION['usuario_tipo'] = 'admin';
                header("Location: user_adm/home.php");
                exit();
            } 
            elseif ($userType === 'professor') {
                // Verifica se é realmente um professor
                $stmt = $pdo->prepare("SELECT * FROM Professores WHERE ID_Professor = ?");
                $stmt->execute([$usuario['ID_Usuario']]);
                if ($stmt->fetch()) {
                    $_SESSION['usuario_id'] = $usuario['ID_Usuario'];
                    $_SESSION['usuario_nome'] = $usuario['Nome_Completo'];
                    $_SESSION['usuario_tipo'] = 'professor';
                    header("Location: user_professor/home.php");
                    exit();
                } else {
                    $erro = "Este usuário não é um professor.";
                }
            }
            elseif ($userType === 'aluno') {
                // Verifica se é realmente um aluno
                $stmt = $pdo->prepare("SELECT * FROM Alunos WHERE ID_Aluno = ?");
                $stmt->execute([$usuario['ID_Usuario']]);
                if ($stmt->fetch()) {
                    $_SESSION['usuario_id'] = $usuario['ID_Usuario'];
                    $_SESSION['usuario_nome'] = $usuario['Nome_Completo'];
                    $_SESSION['usuario_tipo'] = 'aluno';
                    header("Location: user_aluno/index.php");
                    exit();
                } else {
                    $erro = "Este usuário não é um aluno.";
                }
            }
        } else {
            $erro = "Senha incorreta.";
        }
    } else {
        $erro = "Usuário não encontrado.";
    }
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
          
          <?php if (isset($erro)): ?>
          <div class="alert alert-danger" role="alert">
            <?php echo $erro; ?>
          </div>
          <?php endif; ?>
          
          <form method="POST" action="">
            <div class="form-group">
              <label for="exampleInputUsername" class="sr-only">Usuário</label>
              <div class="position-relative has-icon-right">
                <input type="text" id="exampleInputUsername" name="login" class="form-control input-shadow"
                  placeholder="Digite seu usuário" required>
                <div class="form-control-position">
                  <i class="icon-user"></i>
                </div>
              </div>
            </div>
            <div class="form-group">
              <label for="exampleInputPassword" class="sr-only">Senha</label>
              <div class="position-relative has-icon-right">
                <input type="password" id="exampleInputPassword" name="senha" class="form-control input-shadow"
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
</body>
</html>