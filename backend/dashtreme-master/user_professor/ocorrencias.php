<?php require_once '../includes/bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Ocorrências - SAS (Sistema Academico Santos)</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="../assets/css/app-style.css" />
    <link rel="stylesheet" href="../assets/css/icons.css" />
    <link rel="stylesheet" href="../assets/css/sidebar-menu.css" />
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            background: linear-gradient(to right, #2c3e50, #3498db);
            color: #ecf0f1;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .container-ocorrencias {
            max-width: 950px;
            margin: 60px auto;
            background: rgba(255, 255, 255, 0.05);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #ffffff;
        }

        label {
            font-weight: bold;
        }

        select,
        input,
        textarea {
            width: 100%;
            padding: 10px;
            margin-top: 6px;
            margin-bottom: 15px;
            border: none;
            border-radius: 8px;
            background-color: rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        .form-inline {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .form-group {
            flex: 1 1 30%;
            min-width: 220px;
        }

        .btn {
            background-color: #1abc9c;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 10px;
            min-width: 120px;
        }

        .btn:hover {
            background-color: #16a085;
        }

        .btn-limpar {
            background-color: #2980b9;
        }

        .btn-limpar:hover {
            background-color: #2471a3;
        }

        .btn-cancelar {
            background-color: #e74c3c;
        }

        .btn-cancelar:hover {
            background-color: #e74c3c;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            padding: 12px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        th {
            background-color: rgba(113, 175, 254, 0.1);
            color: #ffffff;
        }

        @media (max-width: 768px) {
            .container-ocorrencias {
                padding: 20px;
            }

            .form-inline {
                flex-direction: column;
                gap: 15px;
            }

            .form-group {
                width: 100%;
            }

            table {
                font-size: 14px;
                overflow-x: auto;
                display: block;
            }

            th,
            td {
                white-space: nowrap;
            }

            .btn {
                width: 100%;
            }

            .form-inline:last-child {
                flex-direction: column;
            }
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

        <div class="content-wrapper">
            <div class="container-ocorrencias">
                <h2>Ocorrências</h2>

                <div class="form-inline">
                    <div class="form-group">
                        <label for="turmaSelect">Turma</label>
                        <select id="turmaSelect">
                            <option value="" disabled selected>Carregando turmas...</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="alunoSelect">Aluno</label>
                        <select id="alunoSelect" disabled>
                            <option value="" disabled selected>Selecione uma turma primeiro</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="data">Data</label>
                        <input type="date" id="data" />
                    </div>
                </div>

                <div class="form-inline">
                    <div class="form-group">
                        <label for="tipo">Tipo de Ocorrência</label>
                        <input type="text" id="tipo" placeholder="Ex: Indisciplina, Atraso..." />
                    </div>
                    <div class="form-group" style="flex: 1 1 100%">
                        <label for="descricao">Descrição</label>
                        <textarea id="descricao" rows="2" placeholder="Detalhes da ocorrência..."></textarea>
                    </div>
                    <div>
                        <button class="btn" onclick="adicionarOcorrencia()">Adicionar</button>
                    </div>
                </div>

                <div style="overflow-x:auto;">
                    <table id="tabelaOcorrencias">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Turma</th>
                                <th>Aluno</th>
                                <th>Tipo</th>
                                <th>Descrição</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

                <div class="form-inline" style="margin-top: 30px;">
                    <button class="btn" onclick="salvarOcorrencias()">Salvar</button>
                    <button class="btn btn-limpar" onclick="limparOcorrencias()">Limpar</button>
                    <button class="btn btn-cancelar" onclick="window.location.href='ocorrencias.php'">Cancelar</button>
                </div>

                <hr style="border-color: rgba(255,255,255,0.2); margin: 30px 0;" />

                <h4 style="margin: 0 0 10px 0;">Ocorrências salvas</h4>
                <div class="form-inline" style="gap: 10px; align-items: flex-end;">
                    <div class="form-group">
                        <label for="inicioFiltro">Início</label>
                        <input type="date" id="inicioFiltro" />
                    </div>
                    <div class="form-group">
                        <label for="fimFiltro">Fim</label>
                        <input type="date" id="fimFiltro" />
                    </div>
                    <div class="form-group">
                        <label for="alunoFiltro">Aluno</label>
                        <select id="alunoFiltro" disabled>
                            <option value="" selected>Todos</option>
                        </select>
                    </div>
                    <div>
                        <button class="btn" onclick="carregarOcorrencias()">Buscar</button>
                    </div>
                </div>

                <div style="overflow-x:auto; margin-top: 10px;">
                    <table id="tabelaOcorrenciasSalvas">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Aluno</th>
                                <th>Tipo</th>
                                <th>Descrição</th>
                                <th style="width:120px">Ações</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="overlay toggle-menu"></div>
    </div>

    <script src="../assets/js/jquery.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script src="../assets/plugins/simplebar/js/simplebar.js"></script>
    <script src="../assets/js/sidebar-menu.js"></script>
    <script src="../assets/js/app-script.js"></script>
    <script>
        let turmaAtual = '';
        let alunosTurma = [];

        $(document).ready(function(){
            carregarTurmas();
            // Data padrão hoje
            const hoje = new Date().toISOString().slice(0,10);
            $('#data').val(hoje);
            $('#inicioFiltro').val(new Date(Date.now() - 29*24*3600*1000).toISOString().slice(0,10));
            $('#fimFiltro').val(hoje);

            $('#turmaSelect').on('change', function(){
                turmaAtual = this.value;
                carregarAlunos(turmaAtual);
                // Ajusta filtro de aluno para a mesma lista
                preencherAlunoFiltro();
                carregarOcorrencias();
            });
        });

        function carregarTurmas(){
            const $sel = $('#turmaSelect');
            $sel.prop('disabled', true).html('<option>Carregando...</option>');
            $.getJSON('../includes/ajax/listar_turmas.php')
                .done(function(resp){
                    if (!resp || !resp.success){ throw new Error('Falha ao listar turmas'); }
                    const turmas = resp.data || [];
                    if (turmas.length === 0){
                        $sel.html('<option value="" disabled selected>Sem turmas vinculadas</option>');
                        return;
                    }
                    $sel.empty();
                    $sel.append('<option value="" selected disabled>Selecione a turma</option>');
                    turmas.forEach(function(t){
                        var nome = t.Nome_Turma + (t.Turno ? ' (' + t.Turno + ')' : '');
                        $sel.append('<option value="' + t.ID_Turma + '">' + nome + '</option>');
                    });
                })
                .fail(function(){
                    $sel.html('<option value="" disabled selected>Erro ao carregar turmas</option>');
                })
                .always(function(){ $sel.prop('disabled', false); });
        }

        function carregarAlunos(idTurma){
            const $sel = $('#alunoSelect');
            $sel.prop('disabled', true).html('<option>Carregando alunos...</option>');
            alunosTurma = [];
            if (!idTurma){
                $sel.html('<option value="" disabled selected>Selecione uma turma primeiro</option>');
                return;
            }
            $.getJSON('../includes/ajax/listar_alunos_por_turma.php', { turma_id: idTurma })
                .done(function(resp){
                    if (!resp || !resp.success){ throw new Error('Falha ao listar alunos'); }
                    alunosTurma = resp.data || [];
                    if (alunosTurma.length === 0){
                        $sel.html('<option value="" disabled selected>Sem alunos na turma</option>');
                        return;
                    }
                    $sel.empty();
                    $sel.append('<option value="" selected disabled>Selecione o aluno</option>');
                    alunosTurma.forEach(function(a){
                        var label = a.Nome_Completo + ' - Matrícula: ' + a.Matricula;
                        $sel.append('<option value="' + a.ID_Matricula + '">' + label + '</option>');
                    });
                    $sel.prop('disabled', false);
                    preencherAlunoFiltro();
                })
                .fail(function(){
                    $sel.html('<option value="" disabled selected>Erro ao carregar alunos</option>');
                });
        }

        function preencherAlunoFiltro(){
            const $f = $('#alunoFiltro');
            $f.empty();
            $f.append('<option value="" selected>Todos</option>');
            if (alunosTurma.length === 0){
                $f.prop('disabled', true);
                return;
            }
            alunosTurma.forEach(function(a){
                $f.append('<option value="' + a.ID_Matricula + '">' + a.Nome_Completo + ' - ' + a.Matricula + '</option>');
            });
            $f.prop('disabled', false);
        }

        function adicionarOcorrencia() {
            const turmaSel = document.getElementById('turmaSelect');
            const alunoSel = document.getElementById('alunoSelect');
            const turmaId = turmaSel.value;
            const turmaTxt = turmaSel.options[turmaSel.selectedIndex] ? turmaSel.options[turmaSel.selectedIndex].text : '';
            const idMatricula = alunoSel.value;
            const alunoTxt = alunoSel.options[alunoSel.selectedIndex] ? alunoSel.options[alunoSel.selectedIndex].text : '';
            const data = document.getElementById('data').value;
            const tipo = document.getElementById('tipo').value.trim();
            const descricao = document.getElementById('descricao').value.trim();

            if (!turmaId || !idMatricula || !data || !tipo || !descricao) {
                alert('Preencha todos os campos antes de adicionar.');
                return;
            }

            const tbody = document.querySelector('#tabelaOcorrencias tbody');
            const linha = document.createElement('tr');
            linha.setAttribute('data-idmatricula', idMatricula);
            linha.setAttribute('data-turmaid', turmaId);
            linha.innerHTML =
                '<td>' + data + '</td>' +
                '<td>' + turmaTxt + '</td>' +
                '<td>' + alunoTxt + '</td>' +
                '<td>' + tipo + '</td>' +
                '<td>' + descricao + '</td>';
            tbody.appendChild(linha);

            // Limpa campos específicos
            document.getElementById('alunoSelect').value = '';
            document.getElementById('tipo').value = '';
            document.getElementById('descricao').value = '';
        }

        function limparOcorrencias() {
            document.querySelector('#tabelaOcorrencias tbody').innerHTML = '';
        }

        function salvarOcorrencias() {
            const turmaSel = document.getElementById('turmaSelect');
            const turmaId = turmaSel.value;
            if (!turmaId){ alert('Selecione a turma.'); return; }

            const itens = [];
            document.querySelectorAll('#tabelaOcorrencias tbody tr').forEach(function(tr){
                const idMat = parseInt(tr.getAttribute('data-idmatricula'), 10);
                const tds = tr.querySelectorAll('td');
                const data = tds[0] ? tds[0].textContent.trim() : '';
                const tipo = tds[3] ? tds[3].textContent.trim() : '';
                const descricao = tds[4] ? tds[4].textContent.trim() : '';
                if (idMat && data && tipo && descricao){
                    itens.push({ id_matricula: idMat, data: data, tipo: tipo, descricao: descricao });
                }
            });

            if (itens.length === 0){ alert('Adicione ao menos uma ocorrência na tabela.'); return; }

            $.ajax({
                url: '../includes/ajax/professor/ocorrencias/salvar.php',
                method: 'POST',
                contentType: 'application/json; charset=utf-8',
                dataType: 'json',
                data: JSON.stringify({ turma_id: parseInt(turmaId,10), ocorrencias: itens })
            }).done(function(res){
                if (res && res.success){
                    alert(`Ocorrências salvas. Inseridas: ${res.inserted||0}`);
                    limparOcorrencias();
                    carregarOcorrencias();
                } else {
                    alert(res && res.message ? res.message : 'Falha ao salvar ocorrências');
                }
            }).fail(function(xhr){
                let msg = 'Erro ao salvar ocorrências';
                try { const r = JSON.parse(xhr.responseText); if (r.message) msg = r.message; } catch{}
                alert(msg);
            });
        }

        function carregarOcorrencias(){
            const turmaId = $('#turmaSelect').val();
            if (!turmaId){ return; }
            const inicio = $('#inicioFiltro').val();
            const fim = $('#fimFiltro').val();
            const idMatricula = $('#alunoFiltro').val();

            const params = { turma_id: turmaId, inicio, fim };
            if (idMatricula) params.id_matricula = idMatricula;

            $.getJSON('../includes/ajax/professor/ocorrencias/listar.php', params)
                .done(function(resp){
                    if (!resp || !resp.success){ alert('Falha ao carregar ocorrências'); return; }
                    const tbody = document.querySelector('#tabelaOcorrenciasSalvas tbody');
                    tbody.innerHTML = '';
                    (resp.data || []).forEach(function(o){
                        const tr = document.createElement('tr');
                        tr.setAttribute('data-id', o.ID_Ocorrencia);
                        tr.innerHTML =
                            '<td>' + o.Data + '</td>' +
                            '<td>' + escapeHtml(o.Nome_Aluno) + ' - ' + escapeHtml(o.Matricula) + '</td>' +
                            '<td class="td-tipo">' + escapeHtml(o.Tipo) + '</td>' +
                            '<td class="td-desc">' + escapeHtml(o.Descricao) + '</td>' +
                            '<td>' +
                                '<button class="btn btn-editar" style="padding:6px 10px" data-id="' + o.ID_Ocorrencia + '">Editar</button> ' +
                                '<button class="btn btn-cancelar btn-excluir" style="padding:6px 10px" data-id="' + o.ID_Ocorrencia + '">Excluir</button>' +
                            '</td>';
                        tbody.appendChild(tr);
                    });
                })
                .fail(function(){ alert('Erro ao carregar ocorrências'); });
        }

        function entrarEdicao(id){
            const tr = document.querySelector(`#tabelaOcorrenciasSalvas tr[data-id="${id}"]`);
            if (!tr) return;
            const tdTipo = tr.querySelector('.td-tipo');
            const tdDesc = tr.querySelector('.td-desc');
            const antigoTipo = tdTipo.textContent;
            const antigoDesc = tdDesc.textContent;
            tdTipo.innerHTML = '<input type="text" class="form-control" id="editTipo_' + id + '" value="' + escapeAttr(antigoTipo) + '">';
            tdDesc.innerHTML = '<textarea class="form-control" id="editDesc_' + id + '" rows="2">' + escapeHtml(antigoDesc) + '</textarea>';
            const tdAcoes = tr.querySelector('td:last-child');
            tdAcoes.innerHTML = '' +
                '<button class="btn btn-salvar-ed" style="padding:6px 10px" data-id="' + id + '">Salvar</button> ' +
                '<button class="btn btn-cancelar btn-cancelar-ed" style="padding:6px 10px" data-id="' + id + '">Cancelar</button>';
        }

        function salvarEdicao(id){
            const tipo = document.getElementById(`editTipo_${id}`).value.trim();
            const descricao = document.getElementById(`editDesc_${id}`).value.trim();
            if (!tipo || !descricao){ alert('Tipo e descrição são obrigatórios'); return; }
            $.ajax({
                url: '../includes/ajax/professor/ocorrencias/editar.php',
                method: 'POST',
                contentType: 'application/json; charset=utf-8',
                dataType: 'json',
                data: JSON.stringify({ id_ocorrencia: id, tipo, descricao })
            }).done(function(res){
                if (res && res.success){ carregarOcorrencias(); }
                else { alert(res && res.message ? res.message : 'Falha ao editar ocorrência'); }
            }).fail(function(){ alert('Erro ao editar ocorrência'); });
        }

        function cancelarEdicao(id){ carregarOcorrencias(); }

        function excluirOcorrencia(id){
            if (!confirm('Confirma excluir esta ocorrência?')) return;
            $.ajax({
                url: '../includes/ajax/professor/ocorrencias/remover.php',
                method: 'POST',
                contentType: 'application/json; charset=utf-8',
                dataType: 'json',
                data: JSON.stringify({ id_ocorrencia: id })
            }).done(function(res){
                if (res && res.success){ carregarOcorrencias(); }
                else { alert(res && res.message ? res.message : 'Falha ao excluir ocorrência'); }
            }).fail(function(){ alert('Erro ao excluir ocorrência'); });
        }

        function escapeHtml(s){
            return (s||'').replace(/[&<>"]/g, function(c){ return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c]; });
        }
        function escapeAttr(s){
            return (s||'').replace(/["']/g, function(c){ return c === '"' ? '&quot;' : '&#39;'; });
        }
        function escapeJs(s){
            s = s == null ? '' : String(s);
            s = s.replace(/\\/g, '\\\\');
            s = s.replace(/\n/g, '\\n');
            s = s.replace(/\r/g, '\\r');
            s = s.replace(/'/g, "\\'");
            return s;
        }

        // Delegação de eventos para Ações na tabela de salvas
        $('#tabelaOcorrenciasSalvas').on('click', '.btn-editar', function(){
            const id = $(this).data('id');
            entrarEdicao(id);
        });
        $('#tabelaOcorrenciasSalvas').on('click', '.btn-excluir', function(){
            const id = $(this).data('id');
            excluirOcorrencia(id);
        });
        $('#tabelaOcorrenciasSalvas').on('click', '.btn-salvar-ed', function(){
            const id = $(this).data('id');
            salvarEdicao(id);
        });
        $('#tabelaOcorrenciasSalvas').on('click', '.btn-cancelar-ed', function(){
            const id = $(this).data('id');
            cancelarEdicao(id);
        });
    </script>
</body>

</html>