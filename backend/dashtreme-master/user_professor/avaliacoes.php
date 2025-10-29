<?php require_once '../includes/bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Avaliações - Dashboard Acadêmico</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/app-style.css">
    <link rel="stylesheet" href="../assets/css/icons.css">
    <link rel="stylesheet" href="../assets/css/sidebar-menu.css">
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            background: linear-gradient(to right, #2c3e50, #3498db);
            color: #ecf0f1;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
        }

        /* Header fixo */
        .topbar-nav {
            height: 60px;
            z-index: 1000;
        }

        .content-wrapper {
            padding: 20px;
            padding-top: 80px;
            min-height: calc(100vh - 60px);
        }

        .container-select {
            max-width: 900px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.05);
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #ffffff;
            font-size: 1.5rem;
        }

        .form-select,
        .form-control {
            background-color: rgba(255, 255, 255, 0.1);
            color: #ffffff;
            border: 1px solid #71affe;
            padding: 12px;
            border-radius: 8px;
            width: 100%;
            margin-bottom: 15px;
            font-size: 1rem;
        }

        .form-control::placeholder {
            color: #bdc3c7;
            opacity: 0.7;
        }

        /* Botões */
        .btn {
            color: #fff;
            border: none;
            padding: 6px 14px;
            border-radius: 6px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 0.85rem;
            text-align: center;
            display: inline-block;
            width: auto;
            margin-bottom: 6px;
            line-height: 1.2;
        }

        .btn-primary {
            background-color: #1abc9c;
        }

        .btn-primary:hover {
            background-color: #16a085;
        }

        /* Mantém o botão verde ao clicar/focar (evita roxo padrão do tema) */
        .btn-primary:not(:disabled):not(.disabled):active,
        .btn-primary:not(:disabled):not(.disabled).active,
        .show>.btn-primary.dropdown-toggle {
            background-color: #16a085;
            box-shadow: none;
        }
        .btn-primary:focus {
            background-color: #16a085;
            box-shadow: none;
        }

        .btn-secondary {
            background-color: #7f8c8d;
        }

        .btn-secondary:hover {
            background-color: #616a6b;
        }

        .btn-danger {
            background-color: #e74c3c;
        }

        .btn-danger:hover {
            background-color: #c0392b;
        }

        .avaliacoes-container {
            margin-top: 30px;
            display: none;
        }

        .card-section {
            background: rgba(255, 255, 255, 0.08);
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .card-section h4 {
            border-bottom: 1px solid #71affe;
            padding-bottom: 10px;
            margin-bottom: 15px;
            color: #ffffff;
            font-size: 1.2rem;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #71affe;
            font-size: 0.95rem;
        }

        select.form-select option {
            background-color: #2c3e50;
        }

        select.form-control {
            height: auto;
        }

        /* Tabela */
        .avaliacoes-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 0.9rem;
        }

        .avaliacoes-table th {
            background-color: rgba(113, 175, 254, 0.3);
            color: #ffffff;
            padding: 12px 8px;
            text-align: left;
        }

        .avaliacoes-table td {
            padding: 12px 8px;
            border-bottom: 1px solid rgba(113, 175, 254, 0.2);
            vertical-align: middle;
        }

        .avaliacoes-table tr:hover {
            background-color: rgba(113, 175, 254, 0.1);
        }

        /* Botões de ação */
        .action-btn {
            padding: 5px 10px;
            margin: 2px;
            border-radius: 4px;
            font-size: 0.75rem;
            width: auto;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        /* Grupo de botões */
        .btn-group {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 20px;
        }

        /* Modal */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            display: none;
        }

        .modal-content {
            background: rgba(44, 62, 80, 0.95);
            padding: 20px;
            border-radius: 10px;
            max-width: 90%;
            width: 400px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
        }

        .modal-actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        /* Responsivo */
        @media (min-width: 768px) {
            .content-wrapper {
                padding: 30px;
                padding-top: 90px;
            }

            .container-select {
                padding: 30px;
            }

            .btn-group {
                flex-direction: row;
            }

            .btn {
                width: auto;
                margin-bottom: 0;
            }

            .action-btn {
                width: auto;
                padding: 6px 10px;
                margin: 0 3px;
            }

            .avaliacoes-table {
                font-size: 1rem;
            }
        }

        @media (max-width: 576px) {
            .avaliacoes-table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }

            .action-btn {
                padding: 6px;
                font-size: 0.7rem;
            }

            .card-section {
                padding: 12px;
            }

            h2 {
                font-size: 1.3rem;
            }
        }

        /* Acessibilidade */
        button:focus,
        input:focus,
        select:focus {
            outline: 2px solid #f1c40f;
            outline-offset: 2px;
        }
         .navbar {
            background-color: rgba(0, 0, 0, 0.2) !important;
            backdrop-filter: blur(10px);
        }
    </style>
</head>

<body class="bg-theme bg-theme1">
    <?php
    require("menu_padrao.php");
    ?>

        <!-- Conteúdo principal -->
        <div class="content-wrapper">
            <div class="container-select">
                <h2>Visualizar Avaliações</h2>

                <select id="selectTurma" class="form-select">
                    <option value="" disabled selected>-- Escolha uma turma --</option>
                </select>

                <button class="btn btn-primary" id="btnVisualizar">Visualizar</button>

                <div id="avaliacoesContainer" class="avaliacoes-container">
                    <div class="card-section">
                        <h4 id="formTitle">Nova Avaliação</h4>
                        <div class="form-group">
                            <label for="disciplina">Disciplina</label>
                            <select class="form-control" id="disciplina">
                                <option value="" disabled selected>Carregue uma turma</option>
                            </select>
                            <small id="disciplinaHelp" class="form-text text-muted" style="display:none;">Nenhuma disciplina vinculada a você nesta turma.</small>
                        </div>
                        <div class="form-group">
                            <label for="tipo">Tipo</label>
                            <select class="form-control" id="tipo">
                                <option value="Prova">Prova</option>
                                <option value="Trabalho">Trabalho</option>
                                <option value="Simulado">Simulado</option>
                                <option value="Redação">Redação</option>
                                <option value="Atividade Avaliativa">Atividade Avaliativa</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="data">Data</label>
                            <input type="date" class="form-control" id="data">
                        </div>
                        <div class="btn-group">
                            <button class="btn btn-primary" id="btnSalvar">Adicionar Avaliação</button>
                            <button class="btn btn-secondary" id="btnCancelar">Cancelar</button>
                        </div>
                    </div>

                    <div class="card-section">
                        <h4>Avaliações Registradas</h4>
                        <div class="table-responsive">
                            <table class="avaliacoes-table">
                                <thead>
                                    <tr>
                                        <th>Disciplina</th>
                                        <th>Tipo</th>
                                        <th>Data</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody id="avaliacoesLista">
                                    <!-- Avaliações serão inseridas aqui via JavaScript -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de confirmação -->
        <div class="modal-overlay" id="confirmModal">
            <div class="modal-content">
                <h4 id="modalMessage">Tem certeza que deseja remover esta avaliação?</h4>
                <div class="modal-actions">
                    <button class="btn btn-primary" id="confirmYes">Sim</button>
                    <button class="btn btn-secondary" id="confirmNo">Não</button>
                </div>
            </div>
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
        $(function () {
            carregarTurmasSelect();
            // Visualizar via botão ou ao mudar a turma
            $('#btnVisualizar').on('click', function(e){ e.preventDefault(); carregarAvaliacoes(); });
            $('#selectTurma').on('change', function(){ carregarAvaliacoes(); });
        });

        let turmaAtual = null;
        let editandoId = null;
        let avaliacaoParaRemover = null;
        let avaliacoesCache = [];

        // Elementos DOM
        const btnSalvar = document.getElementById('btnSalvar');
        const btnCancelar = document.getElementById('btnCancelar');
        const formTitle = document.getElementById('formTitle');
        const confirmModal = document.getElementById('confirmModal');
        const confirmYes = document.getElementById('confirmYes');
        const confirmNo = document.getElementById('confirmNo');

        // Event Listeners
        btnSalvar.addEventListener('click', adicionarAvaliacao);
        btnCancelar.addEventListener('click', cancelarAvaliacao);
        confirmYes.addEventListener('click', confirmarRemocao);
        confirmNo.addEventListener('click', fecharModal);

        function carregarTurmasSelect(){
            const ano = 2025;
            const $sel = $('#selectTurma');
            $sel.prop('disabled', true).empty().append('<option value="" disabled selected>Carregando turmas...</option>');
            $.getJSON('../includes/ajax/listar_turmas.php', { ano, all: 1 })
                .done(function(res){
                    $sel.empty().append('<option value="" disabled selected>-- Escolha uma turma --</option>');
                    if (res && res.success && Array.isArray(res.data) && res.data.length){
                        res.data.forEach(t => {
                            const label = `${t.Nome_Turma} (${t.Turno || ''} - ${t.Ano_Letivo || ''})`;
                            $sel.append(`<option value="${t.ID_Turma}">${label}</option>`);
                        });
                        $sel.prop('disabled', false);
                    } else {
                        $sel.append('<option value="" disabled>Nenhuma turma encontrada</option>');
                    }
                })
                .fail(function(){
                    $sel.empty().append('<option value="" disabled>Falha ao carregar turmas</option>');
                });
        }

        function carregarAvaliacoes() {
            turmaAtual = document.getElementById('selectTurma').value;
            if (!turmaAtual) { return; }
            // Mostra o formulário/lista imediatamente
            document.getElementById('avaliacoesContainer').style.display = 'block';
            // Carrega disciplinas vinculadas para a turma selecionada
            carregarDisciplinas(turmaAtual);
            const $lista = $('#avaliacoesLista');
            $lista.html('<tr><td colspan="4" class="empty-message">Carregando avaliações...</td></tr>');
            $.getJSON('../includes/ajax/professor/avaliacoes/listar.php', { turma_id: turmaAtual })
                .done(function(res){
                    $lista.empty();
                    avaliacoesCache = (res && res.success && Array.isArray(res.data)) ? res.data : [];
                    if (avaliacoesCache.length === 0){
                        $lista.html('<tr><td colspan="4" class="empty-message">Nenhuma avaliação registrada</td></tr>');
                    } else {
                        avaliacoesCache.forEach(av => {
                            const dataFormatada = formatarData(av.Data);
                            $lista.append(`
                                <tr data-id="${av.ID_Avaliacao}">
                                    <td>${av.Disciplina}</td>
                                    <td>${av.Tipo}</td>
                                    <td>${dataFormatada}</td>
                                    <td>
                                        <button class="btn btn-primary action-btn" onclick="editarAvaliacao(${av.ID_Avaliacao})">
                                            <i class="zmdi zmdi-edit"></i> <span class="action-text">Editar</span>
                                        </button>
                                        <button class="btn btn-danger action-btn" onclick="solicitarRemocao(${av.ID_Avaliacao})">
                                            <i class="zmdi zmdi-delete"></i> <span class="action-text">Remover</span>
                                        </button>
                                    </td>
                                </tr>`);
                        });
                    }
                })
                .fail(function(){
                    $lista.html('<tr><td colspan="4" class="empty-message">Erro ao carregar avaliações</td></tr>');
                });
        }

        function carregarDisciplinas(turmaId){
            const $disc = $('#disciplina');
            const $help = $('#disciplinaHelp');
            $help.hide();
            $disc.prop('disabled', true).empty().append('<option value="" disabled selected>Carregando disciplinas...</option>');
            $.getJSON('../includes/ajax/listar_disciplinas_por_turma.php', { turma_id: turmaId })
                .done(function(res){
                    $disc.empty();
                    if (res && res.success && Array.isArray(res.data) && res.data.length){
                        $disc.append('<option value="" disabled selected>-- Selecione --</option>');
                        res.data.forEach(function(d){
                            // Usamos o nome como valor pois a tabela de Avaliações armazena texto
                            const nome = d.Nome_Disciplina || '';
                            if (nome){ $disc.append('<option value="'+ nome +'">'+ nome +'</option>'); }
                        });
                        $disc.prop('disabled', false);
                    } else {
                        $disc.append('<option value="" disabled selected>Nenhuma disciplina encontrada</option>');
                        $disc.prop('disabled', true);
                        $help.show();
                    }
                })
                .fail(function(){
                    $disc.empty().append('<option value="" disabled selected>Erro ao carregar</option>');
                    $disc.prop('disabled', true);
                });
        }

        function formatarData(dataString) {
            const options = { day: '2-digit', month: '2-digit', year: 'numeric' };
            return new Date(dataString).toLocaleDateString('pt-BR', options);
        }

        function adicionarAvaliacao() {
            if (!turmaAtual) { alert('Selecione uma turma antes de continuar.'); return; }
            const disciplina = document.getElementById('disciplina').value.trim();
            const tipo = document.getElementById('tipo').value;
            const data = document.getElementById('data').value;
            if (!disciplina || !data) { alert('Preencha todos os campos obrigatórios.'); return; }

            const payload = new FormData();
            payload.append('turma_id', turmaAtual);
            payload.append('disciplina', disciplina);
            payload.append('tipo', tipo);
            payload.append('data', data);
            if (editandoId) { payload.append('id', editandoId); }

            fetch('../includes/ajax/professor/avaliacoes/salvar.php', { method: 'POST', body: payload })
                .then(r => r.json())
                .then(res => {
                    if (res && res.success) {
                        cancelarAvaliacao();
                        carregarAvaliacoes();
                    } else {
                        alert(res && res.message ? res.message : 'Falha ao salvar');
                    }
                })
                .catch(() => alert('Erro ao salvar'));
        }

        function cancelarAvaliacao() {
            // Reseta select para a primeira opção
            const sel = document.getElementById("disciplina");
            if (sel) { sel.selectedIndex = 0; }
            document.getElementById("tipo").value = "Prova";
            document.getElementById("data").value = "";
            editandoId = null;
            btnSalvar.textContent = "Adicionar Avaliação";
            formTitle.textContent = "Nova Avaliação";
            document.getElementById("disciplina").focus();
        }

        function editarAvaliacao(id) {
            const avaliacao = avaliacoesCache.find(a => a.ID_Avaliacao === id);
            if (avaliacao) {
                // Seleciona a disciplina correspondente; se não existir na lista, adiciona temporariamente
                const sel = document.getElementById('disciplina');
                let found = false;
                for (let i = 0; i < sel.options.length; i++) {
                    if (sel.options[i].value === avaliacao.Disciplina) { found = true; break; }
                }
                if (!found && avaliacao.Disciplina) {
                    const opt = document.createElement('option');
                    opt.value = avaliacao.Disciplina;
                    opt.textContent = avaliacao.Disciplina + ' (não listada)';
                    sel.appendChild(opt);
                }
                sel.value = avaliacao.Disciplina;
                document.getElementById('tipo').value = avaliacao.Tipo;
                document.getElementById('data').value = avaliacao.Data;
                editandoId = id;
                btnSalvar.textContent = "Atualizar Avaliação";
                formTitle.textContent = "Editar Avaliação";
                document.getElementById('disciplina').focus();

                // Scroll para o formulário
                document.querySelector('.card-section').scrollIntoView({
                    behavior: 'smooth'
                });
            }
        }

        function solicitarRemocao(id) {
            avaliacaoParaRemover = id;
            document.getElementById('modalMessage').textContent = "Tem certeza que deseja remover esta avaliação?";
            confirmModal.style.display = 'flex';
        }

        function confirmarRemocao() {
            if (!avaliacaoParaRemover) return;
            const payload = new FormData();
            payload.append('id', avaliacaoParaRemover);
            fetch('../includes/ajax/professor/avaliacoes/excluir.php', { method: 'POST', body: payload })
                .then(r => r.json())
                .then(res => {
                    if (res && res.success) {
                        carregarAvaliacoes();
                        fecharModal();
                    } else {
                        alert(res && res.message ? res.message : 'Falha ao excluir');
                    }
                })
                .catch(() => alert('Erro ao excluir'));
        }

        function fecharModal() {
            confirmModal.style.display = 'none';
            avaliacaoParaRemover = null;
        }

        // Fechar modal ao clicar fora
        window.addEventListener('click', (e) => {
            if (e.target === confirmModal) {
                fecharModal();
            }
        });
    </script>
</body>

</html>