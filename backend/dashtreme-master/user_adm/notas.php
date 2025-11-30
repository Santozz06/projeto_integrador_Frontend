<?php require_once '../includes/bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <link rel="icon" href="../assets/images/favicon.ico" type="image/x-icon">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notas - SAS</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/app-style.css">
    <link rel="stylesheet" href="../assets/css/icons.css">
    <link rel="stylesheet" href="../assets/css/sidebar-menu.css">
    <link rel="stylesheet" href="../css/style.css">
</head>

<body class="bg-theme bg-theme1 user_adm_notas">
    <?php
    require("menu_padrão.php");
    ?>

    <!-- Conteúdo principal -->
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h4 class="page-title"><i class="zmdi zmdi-check-circle mr-2"></i> Notas</h4>
                            </div>

                            <!-- Formulário de filtros -->
                            <div class="form-container">
                                <div class="filtros-container">
                                    <div class="filtro-item">
                                        <div class="bold-title">Ano Letivo</div>
                                        <select id="ano-letivo" class="form-control">
                                            <option value="">Carregando...</option>
                                        </select>
                                    </div>
                                    <div class="filtro-item">
                                        <div class="bold-title">Turma</div>
                                        <select id="turma" class="form-control">
                                            <option value="">Selecione o ano letivo</option>
                                        </select>
                                    </div>
                                    <div class="filtro-item">
                                        <div class="bold-title">Disciplina</div>
                                        <select id="disciplina" class="form-control">
                                            <option value="">Selecione a turma</option>
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

                                <!-- Tabela de notas -->
                                <div class="table-container">
                                    <table id="tabela-notas" class="table tabela-notas-unificada">
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
                                        <tbody>
                                            <!-- Dados serão preenchidos via JavaScript -->
                                        </tbody>
                                    </table>
                                    <div id="no-results" class="no-results">
                                        Nenhum aluno encontrado com os filtros selecionados.
                                    </div>
                                </div>

                                <!-- Botões -->
                                <div class="btn-group">
                                    <button class="btn-salvar" id="btn-salvar-todos">Salvar Todas as
                                        Alterações</button>
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
    </div>

    <!-- Scripts -->
    <script src="../assets/js/jquery.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script src="../assets/plugins/simplebar/js/simplebar.js"></script>
    <script src="../assets/js/sidebar-menu.js"></script>
    <script src="../assets/js/app-script.js"></script>


    <script>
        $(document).ready(function () {
            // Estado global simples
            let turmas = [];
            let disciplinas = [];
            let dadosNotas = [];
            let turmaSelecionada = null;
            let disciplinaSelecionada = null;

            // Inicialização: carregar anos e setar padrão
            carregarAnos().then(() => {
                const anoAtual = new Date().getFullYear();
                if ($(`#ano-letivo option[value="${anoAtual}"]`).length) {
                    $('#ano-letivo').val(anoAtual);
                } else {
                    // se não houver ano atual, usa o primeiro
                    const first = $('#ano-letivo option:first').val();
                    if (first) $('#ano-letivo').val(first);
                }
                atualizarTurmas();
            });

            // Eventos de filtros
            $('#ano-letivo').on('change', function () {
                atualizarTurmas();
            });

            $('#turma').on('change', function () {
                turmaSelecionada = $(this).val() ? parseInt($(this).val(), 10) : null;
                atualizarDisciplinas();
            });

            $('#disciplina').on('change', function () {
                disciplinaSelecionada = $(this).val() ? parseInt($(this).val(), 10) : null;
                carregarNotas();
            });

            // Trimestre: recarrega dados do trimestre selecionado
            $('#trimestre').on('change', function () {
                carregarNotas();
            });

            // Botão Cancelar: recarrega notas do backend
            $('#btn-cancelar').click(function () {
                if (confirm('Deseja realmente cancelar? Todas as alterações não salvas serão perdidas.')) {
                    carregarNotas();
                }
            });

            // Cálculo de média e status
            function calcularMedia(notasArray) {
                const nums = notasArray.filter(n => n !== null && n !== '' && !isNaN(parseFloat(n))).map(Number);
                if (nums.length === 0) return '0.0';
                const soma = nums.reduce((acc, v) => acc + v, 0);
                return (soma / nums.length).toFixed(1);
            }

            function determinarStatus(media) {
                const m = parseFloat(media);
                if (m >= 7) return { texto: 'Aprovado', classe: 'status-aprovado' };
                if (m >= 5) return { texto: 'Recuperação', classe: 'status-recuperacao' };
                return { texto: 'Reprovado', classe: 'status-reprovado' };
            }

            function avatarFromNome(nome) {
                const parts = (nome || '').trim().split(/\s+/);
                const ini = (parts[0] ? parts[0][0] : '') + (parts[parts.length - 1] ? parts[parts.length - 1][0] : '');
                return ini.toUpperCase();
            }

            function renderTabela(data) {
                const tbody = $('#tabela-notas tbody');
                tbody.empty();

                if (!data || data.length === 0) {
                    $('#no-results').show();
                    return;
                } else {
                    $('#no-results').hide();
                }

                data.forEach(item => {
                    const notas = [item.Notas['1'], item.Notas['2'], item.Notas['3'], item.Notas['4']];
                    const media = calcularMedia(notas);
                    const status = determinarStatus(media);
                    const avatar = avatarFromNome(item.Nome);

                    const row = `
                        <tr data-id-matricula="${item.ID_Matricula}">
                            <td>
                                <div class="aluno-info">
                                    <div class="aluno-avatar">${avatar}</div>
                                    ${item.Nome}
                                </div>
                            </td>
                            <td>${item.Matricula || ''}</td>
                            <td><span class="badge-turma">${item.Turma || ''}</span></td>
                            <td><input type="number" class="input-nota" data-etapa="1" value="${notas[0] ?? ''}" min="0" max="10" step="0.1"></td>
                            <td><input type="number" class="input-nota" data-etapa="2" value="${notas[1] ?? ''}" min="0" max="10" step="0.1"></td>
                            <td><input type="number" class="input-nota" data-etapa="3" value="${notas[2] ?? ''}" min="0" max="10" step="0.1"></td>
                            <td><input type="number" class="input-nota" data-etapa="4" value="${notas[3] ?? ''}" min="0" max="10" step="0.1"></td>
                            <td class="media">${media}</td>
                            <td class="status-cell ${status.classe}">${status.texto}</td>
                            <td>
                                <button class="btn btn-sm btn-salvar">Salvar</button>
                            </td>
                        </tr>
                    `;
                    tbody.append(row);
                });

            }

            // Atualização da média ao editar
            $(document).on('input change', '.input-nota', function () {
                const row = $(this).closest('tr');
                const valores = row.find('.input-nota').map(function () {
                    const v = $(this).val();
                    return v === '' ? null : parseFloat(v);
                }).get();
                const media = calcularMedia(valores);
                row.find('.media').text(media);
                const status = determinarStatus(media);
                row.find('.status-cell').removeClass('status-aprovado status-reprovado status-recuperacao')
                    .addClass(status.classe)
                    .text(status.texto);
            });

            // Salvar linha
            $(document).on('click', '.btn-salvar', async function () {
                if (!disciplinaSelecionada) { alert('Selecione uma disciplina.'); return; }
                const row = $(this).closest('tr');
                const idMatricula = parseInt(row.data('id-matricula'), 10);
                const updates = row.find('.input-nota').map(function () {
                    const etapa = $(this).data('etapa').toString();
                    const val = $(this).val();
                    return { id_matricula: idMatricula, etapa: etapa, nota: val === '' ? null : parseFloat(val) };
                }).get();
                if (updates.length === 0) { alert('Nenhuma nota para salvar.'); return; }
                try {
                    console.log('salvarNotas payload', { turma_id: turmaSelecionada, disciplina_id: disciplinaSelecionada, trimestre: $('#trimestre').val(), updates });
                    await salvarNotas(disciplinaSelecionada, updates);
                    alert('Notas salvas com sucesso.');
                } catch (e) {
                    alert('Erro ao salvar notas: ' + (e?.message || e));
                }
            });

            // Salvar todas
            $('#btn-salvar-todos').on('click', async function () {
                if (!disciplinaSelecionada) { alert('Selecione uma disciplina.'); return; }
                const updates = [];
                $('#tabela-notas tbody tr').each(function () {
                    const row = $(this);
                    const idMatricula = parseInt(row.data('id-matricula'), 10);
                    row.find('.input-nota').each(function () {
                        const etapa = $(this).data('etapa').toString();
                        const val = $(this).val();
                        updates.push({ id_matricula: idMatricula, etapa: etapa, nota: val === '' ? null : parseFloat(val) });
                    });
                });
                if (updates.length === 0) { alert('Nada para salvar.'); return; }
                try {
                    await salvarNotas(disciplinaSelecionada, updates);
                    alert('Todas as notas foram salvas com sucesso!');
                } catch (e) {
                    alert('Erro ao salvar notas: ' + (e?.message || e));
                }
            });

            // Carregadores de filtros
            async function carregarAnos() {
                try {
                    const res = await $.getJSON('../includes/ajax/shared/academico/listar_anos_letivos.php');
                    const $sel = $('#ano-letivo');
                    $sel.empty();
                    if (res.success && Array.isArray(res.data)) {
                        res.data.forEach(ano => $sel.append(`<option value="${ano}">${ano}</option>`));
                    }
                } catch (e) {
                }
            }

            async function atualizarTurmas() {
                const ano = $('#ano-letivo').val();
                try {
                    const res = await $.getJSON('../includes/ajax/admin/turmas/listar_turmas.php', { ano });
                    turmas = (res.success && Array.isArray(res.data)) ? res.data : [];
                } catch { turmas = []; }

                const $sel = $('#turma');
                $sel.empty();
                $sel.append('<option value="">Todas turmas</option>');
                turmas.forEach(t => $sel.append(`<option value="${t.ID_Turma}">${t.Nome_Turma}</option>`));
                turmaSelecionada = null;
                $('#disciplina').empty().append('<option value="">Todas disciplinas</option>');
                disciplinaSelecionada = null;
                renderTabela([]);
            }

            async function atualizarDisciplinas() {
                const $sel = $('#disciplina');
                $sel.empty();
                $sel.append('<option value="">Todas disciplinas</option>');
                if (!turmaSelecionada) { disciplinas = []; renderTabela([]); return; }
                try {
                    const res = await $.getJSON('../includes/ajax/shared/academico/listar_disciplinas_por_turma.php', { turma_id: turmaSelecionada });
                    disciplinas = (res.success && Array.isArray(res.data)) ? res.data : [];
                } catch { disciplinas = []; }
                disciplinas.forEach(d => $sel.append(`<option value="${d.ID_Disciplina}">${d.Nome_Disciplina}</option>`));
                disciplinaSelecionada = null;
                renderTabela([]);
            }

            async function carregarNotas() {
                if (!turmaSelecionada || !disciplinaSelecionada) { renderTabela([]); return; }
                try {
                    const res = await $.getJSON('../includes/ajax/notas/listar_notas.php', {
                        turma_id: turmaSelecionada,
                        disciplina_id: disciplinaSelecionada,
                        trimestre: $('#trimestre').val()
                    });
                    dadosNotas = (res.success && Array.isArray(res.data)) ? res.data : [];
                    renderTabela(dadosNotas);
                } catch (e) {
                    dadosNotas = [];
                    renderTabela([]);
                }
            }

            function salvarNotas(disciplinaId, updates) {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        url: '../includes/ajax/notas/salvar_notas.php',
                        method: 'POST',
                        contentType: 'application/json; charset=utf-8',
                        data: JSON.stringify({ turma_id: turmaSelecionada, disciplina_id: disciplinaId, trimestre: parseInt($('#trimestre').val(), 10), updates }),
                        dataType: 'json'
                    }).done(function (res) {
                        if (res && res.success) { resolve(res); }
                        else { reject(new Error(res && res.message ? res.message : 'Falha ao salvar')); }
                    }).fail(function (xhr) {
                        try {
                            const r = JSON.parse(xhr.responseText);
                            reject(new Error(r.message || 'Erro no servidor'));
                        } catch {
                            reject(new Error('Erro no servidor'));
                        }
                    });
                });
            }


        });
    </script>
</body>

</html>