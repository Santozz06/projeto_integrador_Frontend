<?php
session_start();
require_once 'includes/conexao.php';

// Se já estiver logado, redireciona
if (isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit();
}

$erro = '';
$sucesso = '';

// Processa o formulário se for submetido
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];
    $confirmar_senha = $_POST['confirmar_senha'];
    $aceitou_termos = isset($_POST['termos']) ? true : false;
    
    // TIPO FIXO - apenas alunos podem se cadastrar
    $tipo_usuario = 'aluno';

    // Validações
    if (empty($nome) || empty($email) || empty($senha) || empty($confirmar_senha)) {
        $erro = 'Por favor, preencha todos os campos!';
    } elseif ($senha !== $confirmar_senha) {
        $erro = 'As senhas não coincidem!';
    } elseif (strlen($senha) < 6) {
        $erro = 'A senha deve ter pelo menos 6 caracteres!';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = 'Email inválido!';
    } elseif (!$aceitou_termos) {
        $erro = 'Você deve aceitar os termos e condições!';
    } else {
        try {
            // Verifica se email já existe
            $stmt = $pdo->prepare("SELECT ID_Usuario FROM Usuarios WHERE Email = ?");
            $stmt->execute([$email]);
            
            if ($stmt->fetch()) {
                $erro = 'Este email já está cadastrado!';
            } else {
                // Cria login a partir do email
                $login = explode('@', $email)[0];
                
                // Verifica se login já existe e adapta se necessário
                $login_original = $login;
                $contador = 1;
                while (true) {
                    $stmt = $pdo->prepare("SELECT ID_Usuario FROM Usuarios WHERE Login = ?");
                    $stmt->execute([$login]);
                    if (!$stmt->fetch()) break;
                    $login = $login_original . $contador;
                    $contador++;
                }

                // Insere o usuário
                $sql = "INSERT INTO Usuarios (Login, Senha, Nome_Completo, Email, Data_Cadastro) 
                        VALUES (?, ?, ?, ?, NOW())";
                
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$login, $senha, $nome, $email]);
                
                // Obtém o ID do usuário inserido
                $id_usuario = $pdo->lastInsertId();
                
                // Insere na tabela Alunos (SEMPRE aluno)
                $sql_aluno = "INSERT INTO Alunos (ID_Aluno) VALUES (?)";
                $stmt_aluno = $pdo->prepare($sql_aluno);
                $stmt_aluno->execute([$id_usuario]);
                
                $sucesso = 'Cadastro realizado com sucesso! Você será redirecionado para o login.';
                
                // Redireciona após 3 segundos
                header('Refresh: 3; URL=index.php?status=cadastro_sucesso');
            }
            
        } catch (PDOException $e) {
            $erro = 'Erro no cadastro: ' . $e->getMessage();
            error_log("Erro cadastro: " . $e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="utf-8"/>
  <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
  <meta name="description" content="Cadastro de Alunos - Sistema Acadêmico"/>
  <meta name="author" content=""/>
  <title>Cadastro de Alunos - Sistema Acadêmico</title>
  <!-- loader-->
  <link href="assets/css/pace.min.css" rel="stylesheet"/>
  <script src="assets/js/pace.min.js"></script>
  <!--favicon-->
  <link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">
  <!-- Bootstrap core CSS-->
  <link href="assets/css/bootstrap.min.css" rel="stylesheet"/>
  <!-- animate CSS-->
  <link href="assets/css/animate.css" rel="stylesheet" type="text/css"/>
  <!-- Icons CSS-->
  <link href="assets/css/icons.css" rel="stylesheet" type="text/css"/>
  <!-- Custom Style-->
  <link href="assets/css/app-style.css" rel="stylesheet"/>
  
 <style>
    .alert { margin-bottom: 20px; }
    .info-box { 
        background-color: rgba(255, 255, 255, 0.1); 
        color: #ffffff; 
        border-left: 4px solid #ffd700; 
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 4px;
        backdrop-filter: blur(10px); 
    }
    
    .info-box small {
        color: #ffffff !important; 
    }
</style>
</head>

<body class="bg-theme bg-theme1">

<div id="wrapper">
    <div class="card card-authentication1 mx-auto my-4">
        <div class="card-body">
            <div class="card-content p-2">
                <div class="text-center">
                    <img src="assets/images/logo-icon.png" alt="logo icon">
                </div>
                <div class="card-title text-uppercase text-center py-3">Cadastro de Aluno</div>
                
                <!-- Mensagem informativa -->
                <div class="info-box">
                    <small>
                        <i class="icon-info"></i> 
                        <strong>Cadastro disponível apenas para alunos.</strong><br>
                        Professores devem ser cadastrados pela secretaria da escola.
                    </small>
                </div>
                
                <!-- Mensagens de erro/sucesso -->
                <?php if ($erro): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo $erro; ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>
                
                <?php if ($sucesso): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo $sucesso; ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="form-group">
                        <label for="nome" class="sr-only">Nome Completo</label>
                        <div class="position-relative has-icon-right">
                            <input type="text" id="nome" name="nome" class="form-control input-shadow" 
                                   placeholder="Digite seu nome completo" required
                                   value="<?php echo isset($_POST['nome']) ? htmlspecialchars($_POST['nome']) : ''; ?>">
                            <div class="form-control-position">
                                <i class="icon-user"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="email" class="sr-only">Email</label>
                        <div class="position-relative has-icon-right">
                            <input type="email" id="email" name="email" class="form-control input-shadow" 
                                   placeholder="Digite seu email" required
                                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                            <div class="form-control-position">
                                <i class="icon-envelope-open"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="senha" class="sr-only">Senha</label>
                        <div class="position-relative has-icon-right">
                            <input type="password" id="senha" name="senha" class="form-control input-shadow" 
                                   placeholder="Crie uma senha (mínimo 6 caracteres)" required minlength="6">
                            <div class="form-control-position">
                                <i class="icon-lock"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirmar_senha" class="sr-only">Confirmar Senha</label>
                        <div class="position-relative has-icon-right">
                            <input type="password" id="confirmar_senha" name="confirmar_senha" class="form-control input-shadow" 
                                   placeholder="Confirme sua senha" required>
                            <div class="form-control-position">
                                <i class="icon-lock"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="icheck-material-white">
                            <input type="checkbox" id="termos" name="termos" value="1" 
                                   <?php echo (isset($_POST['termos']) ? 'checked' : ''); ?> required>
                            <label for="termos">Aceito os Termos e Condições</label>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-light btn-block waves-effect waves-light">
                        <i class="icon-user-follow"></i> Cadastrar como Aluno
                    </button>
                </form>
            </div>
        </div>
        <div class="card-footer text-center py-3">
            <p class="text-warning mb-0">Já tem uma conta? <a href="index.php"> Faça login aqui</a></p>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="assets/js/jquery.min.js"></script>
<script src="assets/js/popper.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script src="assets/js/app-script.js"></script>

<script>
// Validação em tempo real
document.addEventListener('DOMContentLoaded', function() {
    const senha = document.getElementById('senha');
    const confirmarSenha = document.getElementById('confirmar_senha');
    
    function validarSenhas() {
        if (senha.value !== confirmarSenha.value && confirmarSenha.value !== '') {
            confirmarSenha.style.borderColor = 'red';
        } else {
            confirmarSenha.style.borderColor = '';
        }
    }
    
    senha.addEventListener('input', validarSenhas);
    confirmarSenha.addEventListener('input', validarSenhas);
});
</script>

</body>
</html>