<?php require_once '../includes/bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <link rel="icon" href="../assets/images/favicon.ico" type="image/x-icon">
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Avaliações - SAS</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/app-style.css">
    <link rel="stylesheet" href="../assets/css/icons.css">
    <link rel="stylesheet" href="../assets/css/sidebar-menu.css">
    <link rel="stylesheet" href="../css/style.css?v=<?php echo time(); ?>">
</head>

<body class="bg-theme bg-theme1 user_professor_avaliacoes">
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


                <div id="avaliacoesContainer" class="avaliacoes-container" style="display:none;">
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
            $.getJSON('../includes/ajax/admin/turmas/listar_turmas.php', { ano, all: 1 })
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
            if (!turmaAtual) {
                document.getElementById('avaliacoesContainer').style.display = 'none';
                return;
            }
            // Mostra o formulário/lista somente após clicar em visualizar
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
            $.getJSON('../includes/ajax/shared/academico/listar_disciplinas_por_turma.php', { turma_id: turmaId })
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
            // Evita bug de timezone ao criar Date com yyyy-mm-dd
            if (!dataString) return '';
            const parts = dataString.split('-');
            if (parts.length !== 3) return dataString;
            return `${parts[2]}/${parts[1]}/${parts[0]}`;
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