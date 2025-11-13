<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="utf-8"/>
  <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
  <meta name="description" content=""/>
  <meta name="author" content=""/>
  <title>SAS (Sistema Academico Santos) - Redefinir Senha</title>
  <!-- loader-->
  <link href="assets/css/pace.min.css" rel="stylesheet"/>
  <script src="assets/js/pace.min.js"></script>
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
    .invalid-feedback {
      display: none;
      color: #ff5252;
      font-size: 0.875em;
    }
    .is-invalid {
      border-color: #ff5252 !important;
    }
  </style>
</head>

<body class="bg-theme bg-theme1">

<!-- Start wrapper-->
 <div id="wrapper">

 <div class="height-100v d-flex align-items-center justify-content-center">
	<div class="card card-authentication1 mb-0">
		<div class="card-body">
		 <div class="card-content p-2">
		  <div class="card-title text-uppercase pb-2">Redefinir Senha</div>
		    <p class="pb-2">Digite seu endereço de e-mail. Você receberá um link para criar uma nova senha.</p>
		    <form id="resetForm">
			  <div class="form-group">
			  <label for="exampleInputEmailAddress" class="">E-mail</label>
			   <div class="position-relative has-icon-right">
				  <input type="email" id="exampleInputEmailAddress" class="form-control input-shadow" 
				         placeholder="Digite seu e-mail" required>
				  <div class="form-control-position">
					  <i class="icon-envelope-open"></i>
				  </div>
				  <div class="invalid-feedback" id="emailError">
                    Por favor, insira um e-mail válido (exemplo@dominio.com)
                  </div>
			   </div>
			  </div>
			 
			  <button type="submit" class="btn btn-light btn-block mt-3">Redefinir Senha</button>
			 </form>
		   </div>
		  </div>
		   <div class="card-footer text-center py-3">
		    <p class="text-warning mb-0">Voltar para <a href="login.php">Login</a></p>
		  </div>
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
    document.addEventListener('DOMContentLoaded', function() {
      const form = document.getElementById('resetForm');
      const emailInput = document.getElementById('exampleInputEmailAddress');
      const emailError = document.getElementById('emailError');

      function isValidEmail(email) {
        // Expressão regular para validar e-mail
        const re = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        return re.test(String(email).toLowerCase());
      }

      form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Resetar estados de erro
        emailInput.classList.remove('is-invalid');
        emailError.style.display = 'none';
        
        const email = emailInput.value.trim();
        
        // Validações
        if (!email) {
          emailError.textContent = 'Por favor, digite seu endereço de e-mail';
          emailInput.classList.add('is-invalid');
          emailError.style.display = 'block';
          emailInput.focus();
          return;
        }
        
        if (!isValidEmail(email)) {
          emailError.textContent = 'Por favor, insira um e-mail válido (exemplo@dominio.com)';
          emailInput.classList.add('is-invalid');
          emailError.style.display = 'block';
          emailInput.focus();
          return;
        }
        
        // Simula o envio do e-mail 
        setTimeout(() => {
          alert(`Um link para redefinição de senha foi enviado para ${email}`);
          window.location.href = 'login.php';
        }, 500);
      });
      
      // Validação em tempo real enquanto digita
      emailInput.addEventListener('input', function() {
        if (isValidEmail(this.value.trim())) {
          this.classList.remove('is-invalid');
          emailError.style.display = 'none';
        }
      });
    });
  </script>
</body>
</html>