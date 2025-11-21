<?php require_once '../includes/bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notas - Professor</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/app-style.css">
    <link rel="stylesheet" href="../assets/css/icons.css">
    <link rel="stylesheet" href="../assets/css/sidebar-menu.css">
    <link rel="stylesheet" href="../css/style.css?v=<?php echo time(); ?>">
</head>

<body class="bg-theme bg-theme1 user_professor_notas">
    <?php require("menu_padrao.php"); ?>

    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card" style="background-color: transparent; border: none; box-shadow: none;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h4 class="page-title">Lançamento de Notas</h4>
                            </div>

                            <div class="form-container">
                                <div class="filtros-container">
                                    <div class="filtro-item">
                                        <div class="bold-title">Ano Letivo</div>
                                        <select id="ano-letivo" class="form-control"></select>
                                    </div>
                                    <div class="filtro-item">
                                        <div class="bold-title">Turma</div>
                                        <select id="turma" class="form-control">
                                            <option value="">Selecione um ano</option>
                                        </select>
                                    </div>
                                    <div class="filtro-item">
                                        <div class="bold-title">Disciplina</div>
                                        <select id="disciplina" class="form-control">
                                            <option value="">Selecione uma turma</option>
                                        </select>
                                    </div>
                                    <div class="filtro-item">
                                        <div class="bold-title">Trimestre</div>
                                        <select id="trimestre" class="form-control">
                                            <option value="1">1º Trimestre</option>
                                            <option value="2">2º Trimestre</option>
                                            <option value="3">3º Trimestre</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="table-container">
                                    <table id="tabela-notas" class="table">
                                        <thead>
                                            <tr>
                                                <th>Aluno</th>
                                                <th>Matrícula</th>
                                                <th>Turma</th>
                                                <th>Nota 1</th>
                                                <th>Nota 2</th>
                                                <th>Nota 3</th>
                                                <th>Nota 4</th>
                                                <th>Média</th>
                                                <th>Status</th>
                                                <th>Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                    <div id="no-results" class="no-results">Nenhum aluno encontrado com os filtros selecionados.</div>
                                </div>

                                <div class="btn-group">
                                    <button class="btn-salvar" id="btn-salvar-todos">Salvar Todas as Alterações</button>
                                    <button class="btn-cancelar" id="btn-cancelar">Cancelar</button>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="overlay toggle-menu"></div>

    <script src="../assets/js/jquery.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script src="../assets/plugins/simplebar/js/simplebar.js"></script>
    <script src="../assets/js/sidebar-menu.js"></script>
    <script src="../assets/js/app-script.js"></script>

    <script>
        $(function(){
            let turmas = [];
            let disciplinas = [];
            let dadosNotas = [];
            let turmaSelecionada = null;
            let disciplinaSelecionada = null;

            // init
            carregarAnos().then(() => {
                const anoAtual = new Date().getFullYear();
                if ($(`#ano-letivo option[value="${anoAtual}"]`).length) {
                    $('#ano-letivo').val(anoAtual);
                } else {
                    const first = $('#ano-letivo option:first').val();
                    if (first) $('#ano-letivo').val(first);
                }
                atualizarTurmas();
            });

            $('#ano-letivo').on('change', atualizarTurmas);
            $('#turma').on('change', function(){
                turmaSelecionada = $(this).val() ? parseInt($(this).val(), 10) : null;
                atualizarDisciplinas();
            });
            $('#disciplina').on('change', function(){
                disciplinaSelecionada = $(this).val() ? parseInt($(this).val(), 10) : null;
                carregarNotas();
            });
            // Trimestre muda o conjunto de 4 notas (não desabilita inputs)
            $('#trimestre').on('change', function(){
                carregarNotas();
            });

            $('#btn-cancelar').click(function(){
                if (confirm('Deseja realmente cancelar? Todas as alterações não salvas serão perdidas.')) {
                    carregarNotas();
                }
            });

            function calcularMedia(notasArray){
                const nums = notasArray.filter(n => n !== null && n !== '' && !isNaN(parseFloat(n))).map(Number);
                if (nums.length === 0) return '0.0';
                const soma = nums.reduce((a,v)=>a+v,0);
                return (soma/nums.length).toFixed(1);
            }
            function determinarStatus(media){
                const m = parseFloat(media);
                if (m >= 7) return {texto:'Aprovado', classe:'status-aprovado'};
                if (m >= 5) return {texto:'Recuperação', classe:'status-recuperacao'};
                return {texto:'Reprovado', classe:'status-reprovado'};
            }
            function avatarFromNome(nome){
                const parts = (nome||'').trim().split(/\s+/);
                const ini = (parts[0]?parts[0][0]:'') + (parts[parts.length-1]?parts[parts.length-1][0]:'');
                return ini.toUpperCase();
            }
            function renderTabela(data){
                const tbody = $('#tabela-notas tbody');
                tbody.empty();
                if (!data || data.length===0){ $('#no-results').show(); return; } else { $('#no-results').hide(); }
                data.forEach(item => {
                    const notas = [item.Notas['1'], item.Notas['2'], item.Notas['3'], item.Notas['4']];
                    const media = calcularMedia(notas);
                    const status = determinarStatus(media);
                    const avatar = avatarFromNome(item.Nome);
                    const row = `
                        <tr data-id-matricula="${item.ID_Matricula}">
                            <td><div class="aluno-info"><div class="aluno-avatar">${avatar}</div>${item.Nome}</div></td>
                            <td>${item.Matricula || ''}</td>
                            <td><span class="badge-turma">${item.Turma || ''}</span></td>
                            <td><input type="number" class="input-nota" data-etapa="1" value="${notas[0] ?? ''}" min="0" max="10" step="0.1"></td>
                            <td><input type="number" class="input-nota" data-etapa="2" value="${notas[1] ?? ''}" min="0" max="10" step="0.1"></td>
                            <td><input type="number" class="input-nota" data-etapa="3" value="${notas[2] ?? ''}" min="0" max="10" step="0.1"></td>
                            <td><input type="number" class="input-nota" data-etapa="4" value="${notas[3] ?? ''}" min="0" max="10" step="0.1"></td>
                            <td class="media">${media}</td>
                            <td class="status-cell ${status.classe}">${status.texto}</td>
                            <td><button class="btn btn-sm btn-salvar">Salvar</button></td>
                        </tr>`;
                    tbody.append(row);
                });
                aplicarFiltroTrimestre();
            }

            $(document).on('input change', '.input-nota', function(){
                const row = $(this).closest('tr');
                const valores = row.find('.input-nota').map(function(){ const v=$(this).val(); return v===''?null:parseFloat(v); }).get();
                const media = calcularMedia(valores);
                row.find('.media').text(media);
                const status = determinarStatus(media);
                row.find('.status-cell').removeClass('status-aprovado status-reprovado status-recuperacao').addClass(status.classe).text(status.texto);
            });

            $(document).on('click', '.btn-salvar', async function(){
                if (!disciplinaSelecionada){ alert('Selecione uma disciplina.'); return; }
                const row = $(this).closest('tr');
                const idMatricula = parseInt(row.data('id-matricula'), 10);
                const updates = row.find('.input-nota').map(function(){
                    const etapa = $(this).data('etapa').toString();
                    const val = $(this).val();
                    return { id_matricula: idMatricula, etapa: etapa, nota: val === '' ? null : parseFloat(val) };
                }).get();
                try { await salvarNotas(disciplinaSelecionada, updates); alert('Notas salvas com sucesso.'); }
                catch(e){ alert('Erro ao salvar notas: ' + (e?.message || e)); }
            });

            $('#btn-salvar-todos').on('click', async function(){
                if (!disciplinaSelecionada){ alert('Selecione uma disciplina.'); return; }
                const updates = [];
                $('#tabela-notas tbody tr').each(function(){
                    const row = $(this);
                    const idMatricula = parseInt(row.data('id-matricula'), 10);
                    row.find('.input-nota').each(function(){
                        const etapa = $(this).data('etapa').toString();
                        const val = $(this).val();
                        updates.push({ id_matricula: idMatricula, etapa: etapa, nota: val === '' ? null : parseFloat(val) });
                    });
                });
                if (updates.length === 0){ alert('Nada para salvar.'); return; }
                try { await salvarNotas(disciplinaSelecionada, updates); alert('Todas as notas foram salvas com sucesso!'); }
                catch(e){ alert('Erro ao salvar notas: ' + (e?.message || e)); }
            });

            async function carregarAnos(){
                try {
                    const res = await $.getJSON('../includes/ajax/listar_anos_letivos.php');
                    const $sel = $('#ano-letivo');
                    $sel.empty();
                    if (res.success && Array.isArray(res.data)) {
                        res.data.forEach(ano => $sel.append(`<option value="${ano}">${ano}</option>`));
                    }
                } catch(e) { }
            }
            async function atualizarTurmas(){
                const ano = $('#ano-letivo').val();
                try {
                    const res = await $.getJSON('../includes/ajax/listar_turmas.php', { ano });
                    turmas = (res.success && Array.isArray(res.data)) ? res.data : [];
                } catch { turmas = []; }
                const $sel = $('#turma');
                $sel.empty();
                if (turmas.length === 0) {
                    $sel.append('<option value="" disabled>(Sem turmas vinculadas neste ano)</option>');
                } else {
                    $sel.append('<option value="">Todas turmas</option>');
                    turmas.forEach(t => $sel.append(`<option value="${t.ID_Turma}">${t.Nome_Turma}</option>`));
                }
                turmaSelecionada = null;
                $('#disciplina').empty().append('<option value="">Todas disciplinas</option>');
                disciplinaSelecionada = null;
                renderTabela([]);
                aplicarFiltroTrimestre();
            }
            async function atualizarDisciplinas(){
                const $sel = $('#disciplina');
                $sel.empty();
                $sel.append('<option value="">Todas disciplinas</option>');
                if (!turmaSelecionada){ disciplinas = []; renderTabela([]); return; }
                try {
                    const res = await $.getJSON('../includes/ajax/listar_disciplinas_por_turma.php', { turma_id: turmaSelecionada });
                    disciplinas = (res.success && Array.isArray(res.data)) ? res.data : [];
                } catch { disciplinas = []; }
                disciplinas.forEach(d => $sel.append(`<option value="${d.ID_Disciplina}">${d.Nome_Disciplina}</option>`));
                disciplinaSelecionada = null;
                renderTabela([]);
                aplicarFiltroTrimestre();
            }
            async function carregarNotas(){
                if (!turmaSelecionada || !disciplinaSelecionada){ renderTabela([]); return; }
                return new Promise((resolve) => {
                    $.ajax({
                        url: '../includes/ajax/notas/listar_notas.php',
                        method: 'GET',
                        dataType: 'json',
                        data: { turma_id: turmaSelecionada, disciplina_id: disciplinaSelecionada, trimestre: $('#trimestre').val() }
                    }).done(function(res){
                        dadosNotas = (res.success && Array.isArray(res.data)) ? res.data : [];
                        renderTabela(dadosNotas);
                        resolve();
                    }).fail(function(xhr){
                        let msg = 'Erro ao listar alunos/notas';
                        try { const r = JSON.parse(xhr.responseText); if (r.message) msg = r.message; } catch {}
                        alert(msg);
                        dadosNotas = []; renderTabela([]);
                        resolve();
                    });
                });
            }
            function salvarNotas(disciplinaId, updates){
                return new Promise((resolve, reject) => {
                    $.ajax({
                        url: '../includes/ajax/notas/salvar_notas.php',
                        method: 'POST',
                        contentType: 'application/json; charset=utf-8',
                        data: JSON.stringify({ turma_id: turmaSelecionada, disciplina_id: disciplinaId, trimestre: parseInt($('#trimestre').val(), 10), updates }),
                        dataType: 'json'
                    }).done(function(res){ if (res && res.success) resolve(res); else reject(new Error(res && res.message ? res.message : 'Falha ao salvar')); })
                    .fail(function(xhr){ try { const r = JSON.parse(xhr.responseText); reject(new Error(r.message || 'Erro no servidor')); } catch { reject(new Error('Erro no servidor')); } });
                });
            }
            // Sem restrição por trimestre: as 4 notas sempre editáveis, variam por trimestre selecionado
        });
    </script>
</body>

</html>