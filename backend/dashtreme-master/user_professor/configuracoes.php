<?php require_once '../includes/bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Configurações - SAS</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="../assets/css/app-style.css" />
    <link rel="stylesheet" href="../assets/css/icons.css" />
    <link rel="stylesheet" href="../assets/css/sidebar-menu.css" />
    <link rel="stylesheet" href="../css/style.css?v=<?php echo time(); ?>">
</head>

<body class="bg-theme bg-theme1 user_professor_configuracoes">
    <?php
    require("menu_padrao.php");
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