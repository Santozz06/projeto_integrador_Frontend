<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Configurações - Dashboard Acadêmico</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="../assets/css/app-style.css" />
    <link rel="stylesheet" href="../assets/css/icons.css" />
    <link rel="stylesheet" href="../assets/css/sidebar-menu.css" />
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            background: linear-gradient(to right, #2c3e50, #34495e);
            color: #ecf0f1;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
        }

        .topbar-nav {
            height: 60px;
            z-index: 1000;
        }

        .content-wrapper {
            padding: 40px;
            padding-top: 100px;
            min-height: calc(100vh - 60px);
        }

        .container-config {
            max-width: 800px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.05);
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.4);
        }

        .config-section {
            margin-bottom: 40px;
            padding: 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            background: rgba(0, 0, 0, 0.2);
        }

        .config-section h5 {
            margin-bottom: 20px;
            font-weight: bold;
            color: #71affe;
            border-bottom: 1px solid rgba(113, 175, 254, 0.3);
            padding-bottom: 10px;
        }

        .form-group label {
            font-weight: 600;
            color: #ecf0f1;
            margin-bottom: 8px;
            display: block;
        }

        .form-control {
            background-color: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: #ffffff;
            padding: 10px 15px;
            border-radius: 8px;
            width: 100%;
            margin-bottom: 15px;
            transition: all 0.3s;
        }

        .form-control:focus {
            background-color: rgba(255, 255, 255, 0.2);
            border-color: #71affe;
            box-shadow: 0 0 0 0.2rem rgba(113, 175, 254, 0.25);
            outline: none;
        }

        .form-control::placeholder {
            color: #bdc3c7;
            opacity: 0.7;
        }

        .btn-salvar {
            background-color: #1abc9c;
            color: #ffffff;
            border: none;
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .btn-salvar:hover {
            background-color: #16a085;
        }

        .form-check {
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }

        .form-check-input {
            width: 18px;
            height: 18px;
            margin-right: 10px;
            background-color: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .form-check-input:checked {
            background-color: #1abc9c;
            border-color: #1abc9c;
        }

        .form-check-label {
            color: #ecf0f1;
        }

        select.form-control {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%2371affe' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 15px center;
            background-size: 16px 12px;
        }

        select.form-control option {
            background-color: #2c3e50;
            color: #ecf0f1;
        }

        select.form-control {
            height: auto;
        }

        /* Toast de notificação */
        .toast {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #2ecc71;
            color: white;
            padding: 15px 25px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            display: flex;
            align-items: center;
            gap: 10px;
            z-index: 1000;
            transform: translateY(100px);
            opacity: 0;
            transition: all 0.3s ease;
        }

        .toast.show {
            transform: translateY(0);
            opacity: 1;
        }

        .toast.error {
            background-color: #e74c3c;
        }

        @media (max-width: 768px) {
            .content-wrapper {
                padding: 20px;
                padding-top: 80px;
            }

            .container-config {
                padding: 20px;
            }

            .config-section {
                padding: 15px;
            }
        }
    </style>
</head>

<body class="bg-theme bg-theme1">
    <?php
    require("menu_padrão.php");
    ?>

    <!-- Conteúdo principal -->
    <div class="content-wrapper">
        <div class="container-config">
            <h4 class="text-center font-weight-bold mb-4">Configurações</h4>

            <div class="config-section">
                <h5>Alterar senha</h5>
                <div class="form-group">
                    <label for="senha-atual">Senha atual</label>
                    <input type="password" class="form-control" id="senha-atual" placeholder="Digite a senha atual">
                </div>
                <div class="form-group">
                    <label for="nova-senha">Nova senha</label>
                    <input type="password" class="form-control" id="nova-senha" placeholder="Nova senha">
                </div>
                <div class="form-group">
                    <label for="confirmar-senha">Confirmar senha</label>
                    <input type="password" class="form-control" id="confirmar-senha"
                        placeholder="Confirme a nova senha">
                </div>
                <button class="btn-salvar" id="btn-salvar-senha">Salvar</button>
            </div>

            <div class="config-section">
                <h5>Atualizar contato</h5>
                <div class="form-group">
                    <label for="telefone">Telefone</label>
                    <input type="text" class="form-control" id="telefone" placeholder="(00) 00000-0000">
                </div>
                <div class="form-group">
                    <label for="email-alternativo">E-mail alternativo</label>
                    <input type="email" class="form-control" id="email-alternativo" placeholder="exemplo@email.com">
                </div>
                <button class="btn-salvar" id="btn-salvar-contato">Salvar</button>
            </div>

            <div class="config-section">
                <h5>Preferências</h5>
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="notificacao">
                    <label class="form-check-label" for="notificacao">Receber notificação de tarefas ou
                        eventos</label>
                </div>
                <button class="btn-salvar" id="btn-salvar-preferencias">Salvar</button>
            </div>
        </div>
    </div>

    <!-- Toast de notificação -->
    <div class="toast" id="toast-message">
        <i class="zmdi zmdi-check"></i>
        <span id="toast-text">Alterações salvas com sucesso!</span>
    </div>
    <div class="overlay toggle-menu"></div>
    </div>

    <!-- Scripts -->
    <script src="../assets/js/jquery.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script src="../assets/plugins/simplebar/js/simplebar.js"></script>
    <script src="../assets/js/sidebar-menu.js"></script>
    <script src="../assets/js/app-script.js"></script>
    <script src="botaoSair.js"></script>

    <script>
        // Função para mostrar toast de notificação
        function showToast(message, isError = false) {
            const toast = document.getElementById('toast-message');
            const toastText = document.getElementById('toast-text');

            // Atualiza ícone e cor conforme o tipo
            const icon = toast.querySelector('i');
            icon.className = isError ? 'zmdi zmdi-close' : 'zmdi zmdi-check';

            toastText.textContent = message;
            toast.classList.toggle('error', isError);

            // Mostra o toast
            toast.classList.add('show');

            // Esconde após 3 segundos
            setTimeout(() => {
                toast.classList.remove('show');
            }, 3000);
        }

        // Seção: Alterar Senha
        document.getElementById('btn-salvar-senha').addEventListener('click', function () {
            const atual = document.getElementById('senha-atual');
            const nova = document.getElementById('nova-senha');
            const confirmar = document.getElementById('confirmar-senha');

            if (!atual.value.trim()) {
                showToast("Por favor, preencha o campo: Senha atual", true);
                atual.focus();
                return;
            }
            if (!nova.value.trim()) {
                showToast("Por favor, preencha o campo: Nova senha", true);
                nova.focus();
                return;
            }
            if (!confirmar.value.trim()) {
                showToast("Por favor, preencha o campo: Confirmar senha", true);
                confirmar.focus();
                return;
            }
            if (nova.value !== confirmar.value) {
                showToast("A nova senha e a confirmação não coincidem.", true);
                return;
            }

            // Simula o salvamento 
            setTimeout(() => {
                showToast("Senha atualizada com sucesso!");

                // Limpa os campos
                atual.value = '';
                nova.value = '';
                confirmar.value = '';
            }, 800);
        });

        // Seção: Atualizar Contato
        document.getElementById('btn-salvar-contato').addEventListener('click', function () {
            const telefone = document.getElementById('telefone');
            const email = document.getElementById('email-alternativo');

            if (!telefone.value.trim()) {
                showToast("Por favor, preencha o campo: Telefone", true);
                telefone.focus();
                return;
            }
            if (!email.value.trim()) {
                showToast("Por favor, preencha o campo: E-mail alternativo", true);
                email.focus();
                return;
            }

            // Simula o salvamento 
            setTimeout(() => {
                showToast("Contato atualizado com sucesso!");
            }, 800);
        });

        // Seção: Preferências
        document.getElementById('btn-salvar-preferencias').addEventListener('click', function () {
            const notificacoes = document.getElementById('notificacao').checked;

            // Simula o salvamento
            setTimeout(() => {
                showToast("Preferências de notificação salvas!");
            }, 800);
        });

        // Inicialização do menu sidebar
        $(function () {
            $('.sidebar-menu').sidebarMenu();
        });
    </script>
</body>

</html>