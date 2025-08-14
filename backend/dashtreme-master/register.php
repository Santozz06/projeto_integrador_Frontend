<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="utf-8"/>
  <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
  <meta name="description" content=""/>
  <meta name="author" content=""/>
  <title>Dashboard Acadêmico - Cadastro</title>
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
  
</head>

<body class="bg-theme bg-theme1">

<!-- start loader -->
   <div id="pageloader-overlay" class="visible incoming"><div class="loader-wrapper-outer"><div class="loader-wrapper-inner" ><div class="loader"></div></div></div></div>
   <!-- end loader -->

<!-- Start wrapper-->
 <div id="wrapper">

	<div class="card card-authentication1 mx-auto my-4">
		<div class="card-body">
		 <div class="card-content p-2">
		 	<div class="text-center">
		 		<img src="assets/images/logo-icon.png" alt="logo icon">
		 	</div>
		  <div class="card-title text-uppercase text-center py-3">Cadastro</div>
		    <form>
			  <div class="form-group">
			  <label for="exampleInputName" class="sr-only">Nome</label>
			   <div class="position-relative has-icon-right">
				  <input type="text" id="exampleInputName" class="form-control input-shadow" placeholder="Digite seu nome">
				  <div class="form-control-position">
					  <i class="icon-user"></i>
				  </div>
			   </div>
			  </div>
			  <div class="form-group">
			  <label for="exampleInputEmailId" class="sr-only">Email</label>
			   <div class="position-relative has-icon-right">
				  <input type="text" id="exampleInputEmailId" class="form-control input-shadow" placeholder="Digite seu email">
				  <div class="form-control-position">
					  <i class="icon-envelope-open"></i>
				  </div>
			   </div>
			  </div>
			  <div class="form-group">
			   <label for="exampleInputPassword" class="sr-only">Senha</label>
			   <div class="position-relative has-icon-right">
				  <input type="password" id="exampleInputPassword" class="form-control input-shadow" placeholder="Crie uma senha">
				  <div class="form-control-position">
					  <i class="icon-lock"></i>
				  </div>
			   </div>
			  </div>
			  
			  <div class="form-group">
			   <label for="exampleInputConfirmPassword" class="sr-only">Confirmar Senha</label>
			   <div class="position-relative has-icon-right">
				  <input type="password" id="exampleInputConfirmPassword" class="form-control input-shadow" placeholder="Confirme sua senha">
				  <div class="form-control-position">
					  <i class="icon-lock"></i>
				  </div>
			   </div>
			  </div>
			  
			  <div class="form-group">
			     <div class="icheck-material-white">
                   <input type="checkbox" id="user-checkbox" checked="" />
                   <label for="user-checkbox">Aceito os Termos e Condições</label>
			     </div>
			  </div>
			  
			  <button type="button" class="btn btn-light btn-block waves-effect waves-light" id="registerBtn">Cadastrar</button>
			  
			 </form>
		   </div>
		  </div>
		  <div class="card-footer text-center py-3">
		    <p class="text-warning mb-0">Já tem uma conta? <a href="login.html"> Faça login aqui</a></p>
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
    function handleRegister() {
      const name = document.getElementById('exampleInputName').value;
      const email = document.getElementById('exampleInputEmailId').value;
      const password = document.getElementById('exampleInputPassword').value;
      const confirmPassword = document.getElementById('exampleInputConfirmPassword').value;
      const termsChecked = document.getElementById('user-checkbox').checked;

      if (!name || !email || !password || !confirmPassword) {
        alert('Por favor, preencha todos os campos!');
        return;
      }

      if (password !== confirmPassword) {
        alert('As senhas não coincidem!');
        return;
      }

      if (!termsChecked) {
        alert('Você deve aceitar os termos e condições!');
        return;
      }

      setTimeout(() => {
        alert('Cadastro realizado com sucesso!');
        window.location.href = 'login.html';
      }, 300);
    }

    // Configuração inicial
    document.addEventListener('DOMContentLoaded', function () {
      document.getElementById('registerBtn').addEventListener('click', handleRegister);
    });
  </script>
</body>
</html>