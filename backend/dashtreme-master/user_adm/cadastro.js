$(document).ready(function () {
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        // Esconde a mensagem de sucesso ao trocar de aba
        $('#successMessage').hide();

        // Limpeza ao trocar de aba
        if (e.target.hash === '#aluno') {
            limparFormularioServidor();
        }
        else if (e.target.hash === '#servidor') {
            limparFormularioAluno();
        }

        // Mantém a lógica existente para URL e sidebar
        window.location.hash = e.target.hash;
        $('.sidebar-menu').perfectScrollbar('update');
    });
    // Função para limpar o formulário de aluno
    function limparFormularioAluno() {
        $('#formAluno')[0].reset();
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').hide();
    }

    // Função para limpar o formulário de servidor
    function limparFormularioServidor() {
        $('#formServidor')[0].reset();
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').hide();
    }

    // Detecta quando a aba é alterada
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        // Se foi para a aba de aluno, limpa o formulário de servidor
        if (e.target.hash === '#aluno') {
            limparFormularioServidor();
        }
        // Se foi para a aba de servidor, limpa o formulário de aluno
        else if (e.target.hash === '#servidor') {
            limparFormularioAluno();
        }

        //  atualizar o hash da URL
        window.location.hash = e.target.hash;
        $('.sidebar-menu').perfectScrollbar('update');
    });
    // Ativa a aba correta baseada no hash da URL
    function activateTabFromHash() {
        const hash = window.location.hash;

        if (hash === '#aluno') {
            $('.nav-tabs a[href="#aluno"]').tab('show');
        }
        else if (hash === '#servidor') {
            $('.nav-tabs a[href="#servidor"]').tab('show');
        }
    }

    // Executa quando a página carrega
    activateTabFromHash();

    // Atualiza o hash quando as abas são alteradas manualmente
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        window.location.hash = e.target.hash;
        $('.sidebar-menu').perfectScrollbar('update');
    });

    // Trata mudanças no hash (quando usuário clica em links #aluno ou #servidor)
    $(window).on('hashchange', function () {
        activateTabFromHash();
    });

    // Função para validar formulário de aluno
    function validarFormularioAluno() {
        let valido = true;
        const campos = [
            'nomeCompleto', 'dataNascimento', 'estadoCivil', 'nacionalidade',
            'naturalidade', 'filiacao', 'cpf', 'dataExpedicao', 'ufDocumento',
            'orgaoExpedidor', 'cep', 'logradouro', 'numero', 'bairro',
            'municipio', 'ufEndereco', 'celular', 'email'
        ];

        // Resetar validações
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').hide();

        // Validar cada campo
        campos.forEach(function (campo) {
            const elemento = $('#' + campo);
            const valor = elemento.val();

            if (!valor) {
                elemento.addClass('is-invalid');
                elemento.next('.invalid-feedback').show();
                valido = false;
            }
        });

        // Validação especial para e-mail
        const email = $('#email').val();
        if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            $('#email').addClass('is-invalid');
            $('#email').next('.invalid-feedback').text('Por favor, informe um e-mail válido').show();
            valido = false;
        }

        return valido;
    }

    // Função para validar formulário de servidor
    function validarFormularioServidor() {
        let valido = true;
        const campos = [
            'nomeCompletoServidor', 'dataNascimentoServidor', 'sexoServidor', 'racaCorServidor','estadoCivilServidor',
            'nacionalidadeServidor', 'naturalidadeServidor', 'filiacaoServidor','cpfServidor', 'rgServidor', 'orgaoExpedidorServidor', 'ufDocumentoServidor',
            'cepServidor', 'logradouroServidor', 'numeroServidor', 'complementoServidor','bairroServidor',
            'tituloEleitor','municipioServidor', 'ufEnderecoServidor', 'celularServidor', 'emailServidor',
            'cargoFuncao', 'matriculaServidor', 'dataAdmissao', 'formacaoAcademica', 'areaAtuacao'
        ];

        // Resetar validações
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').hide();

        // Validar cada campo
        campos.forEach(function (campo) {
            const elemento = $('#' + campo);
            const valor = elemento.val();

            if (!valor) {
                elemento.addClass('is-invalid');
                elemento.next('.invalid-feedback').show();
                valido = false;
            }
        });

        // Validação especial para e-mail
        const email = $('#emailServidor').val();
        if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            $('#emailServidor').addClass('is-invalid');
            $('#emailServidor').next('.invalid-feedback').text('Por favor, informe um e-mail válido').show();
            valido = false;
        }

        return valido;
    }

    // Ao tentar salvar formulário de aluno
    $('#formAluno').on('submit', function (e) {
        e.preventDefault();

        // Esconder mensagem de sucesso anterior
        $('#successMessage').hide();

        // Validar formulário
        if (validarFormularioAluno()) {
            // Formulário válido - mostrar mensagem de sucesso
            $('#successMessage').fadeIn();



            // Rolando a página para mostrar a mensagem
            $('html, body').animate({
                scrollTop: 0
            }, 500);

        } else {
            // Rolando para o primeiro erro
            $('html, body').animate({
                scrollTop: $('.is-invalid').first().offset().top - 100
            }, 500);
        }
    });

    // Botão "Salvar e vincular turma"
    $('#btnSalvarEVincular').on('click', function () {
        $('#successMessage').hide();

        if (validarFormularioAluno()) {
            const nome = $('#nomeCompleto').val();
            const matricula = $('#cpf').val(); // ou outro campo identificador

            // Armazena os dados temporariamente
            localStorage.setItem('novoAlunoNome', nome);
            localStorage.setItem('novoAlunoMatricula', matricula);

            // Redireciona para a tela de Gerenciar Vínculos
            window.location.href = 'gerenciarVinculos.php';
        } else {
            // Rolando para o primeiro erro
            $('html, body').animate({
                scrollTop: $('.is-invalid').first().offset().top - 100
            }, 500);
        }
    });


    // Ao tentar salvar formulário de servidor
    $('#formServidor').on('submit', function (e) {
        e.preventDefault();
        console.log("Formulário servidor submetido"); // Debug

        $('#successMessage').hide();
        console.log("Mensagem escondida"); // Debug

        if (validarFormularioServidor()) {
            console.log("Formulário válido, mostrando mensagem"); // Debug
            $('#successMessage').fadeIn();

            $('html, body').animate({
                scrollTop: 0
            }, 500);
        } else {
            console.log("Formulário inválido"); // Debug
            $('html, body').animate({
                scrollTop: $('.is-invalid').first().offset().top - 100
            }, 500);
        }
    });

    // Limpar erros quando o usuário começar a digitar/selecionar
    $('input, select').on('input change', function () {
        $(this).removeClass('is-invalid');
        $(this).next('.invalid-feedback').hide();
    });

    // Botão cancelar aluno
    $('#btnCancelarAluno').click(function () {
        if (confirm('Deseja realmente cancelar? Todas as alterações serão perdidas.')) {
            $('#formAluno')[0].reset();
        }
    });

    // Botão cancelar servidor
    $('#btnCancelarServidor').click(function () {
        if (confirm('Deseja realmente cancelar? Todas as alterações serão perdidas.')) {
            $('#formServidor')[0].reset();
        }
    });
});