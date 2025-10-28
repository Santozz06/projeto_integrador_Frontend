<?php require_once '../includes/bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Atividades - Dashboard Acadêmico</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/app-style.css">
    <link rel="stylesheet" href="../assets/css/icons.css">
    <link rel="stylesheet" href="../assets/css/sidebar-menu.css">
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body {
            background: linear-gradient(to right, #2c3e50, #3498db);
            color: #ecf0f1;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .container-select {
            max-width: 900px;
            margin: 2px auto; /* reduz o espaço abaixo da navbar */
            background: rgba(255, 255, 255, 0.05);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }


        .atividade-form select,
        .atividade-form input[type="text"],
        .atividade-form input[type="date"] {
            background-color: rgba(255, 255, 255, 0.1);
            color: #ffffff;
            border: 1px solid #71affe;
            border-radius: 8px;
            padding: 10px;
            margin-bottom: 15px;
            width: 100%;
        }

        .btn {
            background-color: #1abc9c;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
        }

        .btn:hover {
            background-color: #16a085;
        }

        /* Mantém verde nos estados de foco/ativo para botões "Editar" (.btn-primary) */
        .btn-primary {
            background-color: #1abc9c;
            border-color: #1abc9c;
            color: #fff;
        }
        .btn-primary:hover,
        .btn-primary:focus,
        .btn-primary:not(:disabled):not(.disabled).active,
        .btn-primary:not(:disabled):not(.disabled):active,
        .show > .btn-primary.dropdown-toggle {
            background-color: #16a085;
            border-color: #16a085;
            box-shadow: none;
            color: #fff;
        }

        .btn-secondary {
            background-color: #7f8c8d;
        }

        .btn-secondary:hover {
            background-color: #616a6b;
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 0.85em;
            margin-right: 5px;
        }

        .btn-danger {
            background-color: #e74c3c;
            color: #fff;
        }

        .btn-danger:hover {
            background-color: #c0392b;
        }

        .atividade-item {
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.08);
        }

        .atividade-titulo {
            color: #ffffff;
            font-weight: 600;
        }

        .atividade-info {
            font-size: 0.9em;
            color: #ffffff;
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

        <!-- Conteúdo Principal -->
        <div class="content-wrapper">
            <div class="container-select">
                <h2>Gerenciar Atividades</h2>
                <div class="atividade-form">
                    <select id="selectTurma">
                        <option value="" disabled selected>-- Escolha uma turma --</option>
                    </select>

                    <input type="text" id="tituloAtividade" placeholder="Título da atividade">
                    <input type="date" id="dataAtividade">
                    <select id="disciplinaAtividade" disabled>
                        <option value="" disabled selected>Carregue uma turma</option>
                    </select>
                    <small id="disciplinaHelp" class="form-text text-muted" style="display:none;">Nenhuma disciplina vinculada a você nesta turma.</small>

                    <div class="btn-group" style="display: flex; gap: 10px; flex-wrap: wrap;">
                        <button class="btn btn-sm" id="btnSalvarAtividade">Adicionar Atividade</button>
                        <button class="btn btn-secondary btn-sm" id="btnCancelarEdicao" style="display: none;">Cancelar</button>
                    </div>
                </div>

                <div id="listaAtividades"></div>
            </div>
            <div class="overlay toggle-menu"></div>
        </div>

        <script src="../assets/js/jquery.min.js"></script>
        <script src="../assets/js/bootstrap.min.js"></script>
        <script src="../assets/plugins/simplebar/js/simplebar.js"></script>
        <script src="../assets/js/sidebar-menu.js"></script>
        <script src="../assets/js/app-script.js"></script>
     

        <script>
            $(function(){
                inicializarAtividades();
            });

            let turmaAtual = null;
            let editandoId = null;
            let atividadesCache = [];

            function inicializarAtividades(){
                $('#btnSalvarAtividade').on('click', function(e){ e.preventDefault(); salvarAtividade(); });
                $('#btnCancelarEdicao').on('click', function(e){ e.preventDefault(); cancelarEdicao(); });
                $('#selectTurma').on('change', function(){ turmaAtual = this.value; carregarDisciplinas(turmaAtual); carregarAtividades(); });
                carregarTurmas();
            }

            function carregarTurmas(){
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
                    .fail(function(){ $sel.empty().append('<option value="" disabled>Falha ao carregar turmas</option>'); });
            }

            function carregarDisciplinas(turmaId){
                const $formSel = $('#disciplinaAtividade');
                const $help = $('#disciplinaHelp');
                $help.hide();
                $formSel.prop('disabled', true).empty().append('<option value="" disabled selected>Carregando disciplinas...</option>');
                if (!turmaId) return;
                $.getJSON('../includes/ajax/listar_disciplinas_por_turma.php', { turma_id: turmaId })
                    .done(function(res){
                        $formSel.empty();
                        if (res && res.success && Array.isArray(res.data) && res.data.length){
                            $formSel.append('<option value="" disabled selected>-- Selecione --</option>');
                            res.data.forEach(function(d){
                                const nome = d.Nome_Disciplina || '';
                                if (nome){
                                    $formSel.append('<option value="'+ nome +'">'+ nome +'</option>');
                                }
                            });
                            $formSel.prop('disabled', false);
                        } else {
                            $formSel.append('<option value="" disabled selected>Nenhuma disciplina encontrada</option>');
                            $formSel.prop('disabled', true);
                            $help.show();
                        }
                    })
                    .fail(function(){
                        $formSel.empty().append('<option value="" disabled selected>Erro ao carregar</option>');
                        $formSel.prop('disabled', true);
                    });
            }

            function carregarAtividades(){
                const $lista = $('#listaAtividades');
                if (!turmaAtual){ $lista.html('<p style="text-align:center; color:#bdc3c7; font-style:italic;">Selecione uma turma</p>'); return; }
                $lista.html('<p style="text-align:center; color:#bdc3c7; font-style:italic;">Carregando atividades...</p>');
                $.getJSON('../includes/ajax/professor/atividades/listar.php', { turma_id: turmaAtual })
                    .done(function(res){
                        atividadesCache = (res && res.success && Array.isArray(res.data)) ? res.data : [];
                        renderizarAtividades();
                    })
                    .fail(function(){ $lista.html('<p style="text-align:center; color:#bdc3c7; font-style:italic;">Erro ao carregar atividades</p>'); });
            }

            function formatarDataBR(dataString){
                if (!dataString) return '';
                const d = new Date(dataString);
                const dia = String(d.getUTCDate()).padStart(2,'0');
                const mes = String(d.getUTCMonth()+1).padStart(2,'0');
                const ano = d.getUTCFullYear();
                return `${dia}/${mes}/${ano}`;
            }

            function renderizarAtividades(){
                const $lista = $('#listaAtividades');
                $lista.empty();
                if (!atividadesCache.length){
                    $lista.html('<p style="text-align:center; color:#bdc3c7; font-style:italic;">Nenhuma atividade encontrada</p>');
                    return;
                }
                atividadesCache.forEach(function(a){
                    const id = a.ID_Atividade;
                    const titulo = a.Titulo;
                    const data = formatarDataBR(a.Data);
                    const disciplina = a.Disciplina;
                    const $div = $('<div>').addClass('atividade-item').attr('data-id', id);
                    $div.append(`<div class="atividade-titulo">${titulo}</div>`);
                    $div.append(`<div class="atividade-info">${data} - ${disciplina}</div>`);
                    const $btns = $('<div style="margin-top:10px;"></div>');
                    $btns.append(`<button class="btn btn-sm btn-primary" onclick="editarAtividade(${id})">Editar</button>`);
                    $btns.append(`<button class="btn btn-sm btn-danger" onclick="removerAtividade(${id})">Remover</button>`);
                    $div.append($btns);
                    $lista.append($div);
                });
            }

            function salvarAtividade(){
                if (!turmaAtual){ alert('Selecione uma turma.'); return; }
                const titulo = $('#tituloAtividade').val().trim();
                const data = $('#dataAtividade').val();
                const disciplina = $('#disciplinaAtividade').val();
                if (!titulo || !data || !disciplina){ alert('Preencha todos os campos.'); return; }
                const payload = new FormData();
                payload.append('turma_id', turmaAtual);
                payload.append('titulo', titulo);
                payload.append('disciplina', disciplina);
                payload.append('data', data);
                if (editandoId){ payload.append('id', editandoId); }
                fetch('../includes/ajax/professor/atividades/salvar.php', { method: 'POST', body: payload })
                    .then(r => r.json())
                    .then(res => { if (res && res.success){ cancelarEdicao(); carregarAtividades(); } else { alert(res && res.message ? res.message : 'Falha ao salvar'); } })
                    .catch(() => alert('Erro ao salvar'));
            }

            function cancelarEdicao(){
                $('#tituloAtividade').val('');
                $('#dataAtividade').val('');
                editandoId = null;
                $('#btnSalvarAtividade').text('Adicionar Atividade');
                $('#btnCancelarEdicao').hide();
                $('#tituloAtividade').focus();
            }

            window.editarAtividade = function(id){
                const a = atividadesCache.find(x => x.ID_Atividade === id);
                if (!a) return;
                // garantir disciplina presente no select
                const sel = document.getElementById('disciplinaAtividade');
                let found = false;
                for (let i=0;i<sel.options.length;i++){ if (sel.options[i].value === a.Disciplina){ found = true; break; } }
                if (!found){ const opt = document.createElement('option'); opt.value = a.Disciplina; opt.textContent = a.Disciplina + ' (não listada)'; sel.appendChild(opt); }
                $('#tituloAtividade').val(a.Titulo);
                $('#dataAtividade').val(a.Data);
                $('#disciplinaAtividade').val(a.Disciplina);
                editandoId = id;
                $('#btnSalvarAtividade').text('Atualizar Atividade');
                $('#btnCancelarEdicao').show();
            }

            window.removerAtividade = function(id){
                if (!confirm('Deseja remover esta atividade?')) return;
                const payload = new FormData();
                payload.append('id', id);
                fetch('../includes/ajax/professor/atividades/excluir.php', { method: 'POST', body: payload })
                    .then(r => r.json())
                    .then(res => { if (res && res.success){ carregarAtividades(); cancelarEdicao(); } else { alert(res && res.message ? res.message : 'Falha ao excluir'); } })
                    .catch(() => alert('Erro ao excluir'));
            }
        </script>
</body>

</html>