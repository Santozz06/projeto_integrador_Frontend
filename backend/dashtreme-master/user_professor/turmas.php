<?php require_once '../includes/bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <link rel="icon" href="../assets/images/favicon.ico" type="image/x-icon">
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Turmas - SAS</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/app-style.css">
    <link rel="stylesheet" href="../assets/css/icons.css">
    <link rel="stylesheet" href="../assets/css/sidebar-menu.css">
    <link rel="stylesheet" href="../css/style.css?v=<?=time()?>">
</head>

<body class="bg-theme bg-theme1 user_professor_turmas">
    <?php
    require("menu_padrao.php");
    ?>

        <!-- Conteúdo Principal -->
        <div class="content-wrapper">
            <div class="container-select">
                <h2>Selecione a Turma</h2>
                <select id="selectTurma" class="form-select">
                    <option value="" disabled selected>-- Escolha uma turma --</option>
                </select>

                <button class="btn" id="btnVisualizar">Visualizar</button>

                <div id="dadosTurma" class="turma-dados">
                    <div class="card-section">
                        <h4>Informações da Turma</h4>
                        <p><strong>Nome da Turma:</strong> <span id="nomeTurma"></span></p>
                        <p><strong>Professor:</strong> <span id="professorTurma"></span></p>
                        <p><strong>Turno:</strong> <span id="turnoTurma"></span></p>
                    </div>

                    <div class="card-section">
                        <h4>Lista de Alunos</h4>
                        <div id="alunosLista"></div>
                    </div>

                    <div class="card-section">
                        <h4>Conteúdos</h4>
                        <ul id="conteudosLista"></ul>
                    </div>

                    <div class="card-section">
                        <h4>Avisos</h4>
                        <ul id="avisosLista"></ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="overlay toggle-menu"></div>
    </div>

    <!-- JS -->
    <script src="../assets/js/jquery.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script src="../assets/plugins/simplebar/js/simplebar.js"></script>
    <script src="../assets/js/sidebar-menu.js"></script>
    <script src="../assets/js/app-script.js"></script>
   
    <script>
        $(function () {
            // Sidebar menu já é inicializado em app-script.js; evitar dupla inicialização

            // Carrega turmas do professor para o ano de 2025
            carregarTurmasSelect();

            // Botão visualizar
            $('#btnVisualizar').on('click', function (e) {
                e.preventDefault();
                carregarTurmaSelecionada();
            });
        });

        function carregarTurmasSelect() {
            const ano = 2025; // filtro solicitado
            const $sel = $('#selectTurma');
            $sel.prop('disabled', true).empty()
                .append('<option value="" disabled selected>Carregando turmas...</option>');

                // Busca diretamente todas as turmas de 2025 (sem restringir por professor)
                $.getJSON('../includes/ajax/admin/turmas/listar_turmas.php', { ano, all: 1 })
                .done(function (res) {
                    $sel.empty().append('<option value="" disabled selected>-- Escolha uma turma --</option>');
                    if (res && res.success && Array.isArray(res.data) && res.data.length) {
                        res.data.forEach(t => {
                            const label = `${t.Nome_Turma} (${t.Turno || ''} - ${t.Ano_Letivo || ''})`;
                            $sel.append(`<option value="${t.ID_Turma}">${label}</option>`);
                        });
                        $sel.prop('disabled', false);
                        } else {
                            $sel.append('<option value="" disabled>Nenhuma turma encontrada</option>');
                    }
                })
                .fail(function () {
                    $sel.empty().append('<option value="" disabled>Falha ao carregar turmas</option>');
                });
        }

        function carregarTurmaSelecionada() {
            const turmaId = $('#selectTurma').val();
            if (!turmaId) return;

            // Limpa e mostra placeholders
            $('#nomeTurma').text('...');
            $('#professorTurma').text('Carregando...');
            $('#turnoTurma').text('...');
            $('#alunosLista').html('<div class="empty-message">Carregando alunos...</div>');
            $('#conteudosLista').html('<li class="empty-message">Carregando disciplinas...</li>');
            $('#avisosLista').html('<li class="empty-message">Carregando avisos...</li>');
            $('#dadosTurma').show();

            // Carrega info da turma
            $.getJSON('../includes/ajax/admin/turmas/buscar_turma.php', { id: turmaId })
                .done(function (turma) {
                    console.log('Resposta buscar_turma.php:', turma);
                    var t = (turma && turma.data && turma.data[0]) ? turma.data[0] : turma;
                    if (t && !t.error) {
                        $('#nomeTurma').text(t.Nome_Turma || '');
                        $('#turnoTurma').text(t.Turno || '');
                    }
                });

            // Carrega professores da turma
            $.getJSON('../includes/ajax/admin/professores/listar_professores_por_turma.php', { turma_id: turmaId })
                .done(function (res) {
                    if (res && res.success && Array.isArray(res.data) && res.data.length) {
                        const nomes = res.data.map(p => p.Nome_Completo).join(', ');
                        $('#professorTurma').text(nomes);
                    } else {
                        $('#professorTurma').text('Sem professores vinculados');
                    }
                })
                .fail(function () {
                    $('#professorTurma').text('Erro ao carregar professores');
                });

            // Carrega alunos por turma (ativos)
            $.getJSON('../includes/ajax/admin/turmas/listar_alunos_por_turma.php', { turma_id: turmaId })
                .done(function (res) {
                    if (res && res.success && Array.isArray(res.data)) {
                        if (res.data.length === 0) {
                            $('#alunosLista').html('<div class="empty-message">Nenhum aluno ativo nesta turma</div>');
                        } else {
                            const html = res.data.map(a =>
                                `<div class="aluno"><span>${a.Nome_Completo}</span><span class="status">${a.Status}</span></div>`
                            ).join('');
                            $('#alunosLista').html(html);
                        }
                    } else {
                        $('#alunosLista').html('<div class="empty-message">Falha ao carregar alunos</div>');
                    }
                })
                .fail(function () {
                    $('#alunosLista').html('<div class="empty-message">Erro ao carregar alunos</div>');
                });

            // Carrega disciplinas do ano/relacionadas à turma (mesmo ano da turma)
            $.getJSON('../includes/ajax/shared/academico/listar_disciplinas_por_turma.php', { turma_id: turmaId })
                .done(function (res) {
                    if (res && res.success && Array.isArray(res.data)) {
                        if (res.data.length === 0) {
                            $('#conteudosLista').html('<li class="empty-message">Nenhuma disciplina encontrada</li>');
                        } else {
                            const html = res.data.map(d =>
                                `<li>${d.Nome_Disciplina}${d.Professor ? ' — ' + d.Professor : ''}</li>`
                            ).join('');
                            $('#conteudosLista').html(html);
                        }
                    } else {
                        $('#conteudosLista').html('<li class="empty-message">Falha ao carregar disciplinas</li>');
                    }
                })
                .fail(function () {
                    $('#conteudosLista').html('<li class="empty-message">Erro ao carregar disciplinas</li>');
                });

            // Carrega avisos (eventos próximos 30 dias)
            const start = new Date();
            const end = new Date();
            end.setDate(end.getDate() + 30);
            const toISO = d => d.toISOString().slice(0, 10);
            $.getJSON('../includes/ajax/calendario/listar_eventos.php', { start: toISO(start), end: toISO(end) })
                .done(function (res) {
                    if (res && res.success && Array.isArray(res.data)) {
                        if (res.data.length === 0) {
                            $('#avisosLista').html('<li class="empty-message">Sem avisos nos próximos 30 dias</li>');
                        } else {
                            const itens = res.data.slice(0, 6).map(ev => {
                                const dt = (ev.start || '').split('T')[0];
                                const [y, m, d] = dt.split('-');
                                const dataBR = `${d}/${m}/${y}`;
                                return `<li>${dataBR} — ${ev.title || ''}</li>`;
                            }).join('');
                            $('#avisosLista').html(itens);
                        }
                    } else {
                        $('#avisosLista').html('<li class="empty-message">Falha ao carregar avisos</li>');
                    }
                })
                .fail(function () {
                    $('#avisosLista').html('<li class="empty-message">Erro ao carregar avisos</li>');
                });
        }
    </script>
</body>

</html>