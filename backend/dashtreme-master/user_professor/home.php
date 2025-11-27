<?php require_once '../includes/bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <link rel="icon" href="../assets/images/favicon.ico" type="image/x-icon">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard do Professor - SAS</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/app-style.css">
    <link rel="stylesheet" href="../assets/css/icons.css">
    <link rel="stylesheet" href="../assets/css/sidebar-menu.css">
    <link rel="stylesheet" href="../css/style.css">
    
</head>

<body class="bg-theme bg-theme1 user_professor_home">
    <?php
    require("menu_padrao.php");
    ?>
    

        <!-- Conteúdo principal -->
        <div class="content-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="welcome-message">
                            <h4 class="welcome-title">Bem-vindo(a), Professor(a)!</h4>
                            <p class="welcome-text">Aqui você pode gerenciar suas turmas, registrar presenças, lançar
                                notas e acompanhar o desempenho dos alunos.</p>

                            <div class="quick-stats">
                                <div class="stat-item">
                                    <div class="stat-value" id="stat-turmas">-</div>
                                    <div class="stat-label">Turmas</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-value" id="stat-alunos">-</div>
                                    <div class="stat-label">Alunos</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-value" id="stat-disciplinas">-</div>
                                    <div class="stat-label">Disciplinas</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row row-cards">
                    <!-- Horários e Salas de Aula -->
                    <div class="col-lg-6">
                        <div class="dashboard-card" id="panel-horarios">
                            <h5 class="card-title"><i class="zmdi zmdi-time mr-2"></i> Horários e Salas de Aula</h5>
                            <div class="empty-message">Carregando horários...</div>
                        </div>
                    </div>
                    <!-- Eventos Próximos -->
                    <div class="col-lg-6">
                        <div class="dashboard-card" id="panel-eventos-proximos">
                            <h5 class="card-title"><i class="zmdi zmdi-calendar-note mr-2"></i> Eventos Próximos</h5>
                            <div class="empty-message">Carregando eventos...</div>
                        </div>
                    </div>
                </div>

                <div class="row section-gap">
                    <!-- Avaliações -->
                    <div class="col-lg-12">
                        <div class="dashboard-card" id="panel-avaliacoes">
                            <h5 class="card-title"><i class="zmdi zmdi-check-circle mr-2"></i> Avaliações</h5>
                            <div class="empty-message">Carregando avaliações...</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="overlay toggle-menu"></div>

    <!-- Scripts -->
    <script src="../assets/js/jquery.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script src="../assets/plugins/simplebar/js/simplebar.js"></script>
    <script src="../assets/js/sidebar-menu.js"></script>
    <script src="../assets/js/app-script.js"></script>
    <script>
        // Inicialização da página (sem redirecionamentos via localStorage)
        document.addEventListener('DOMContentLoaded', function () {
            const username = (localStorage.getItem('username') || 'Professor');
            $('.user-title').text(username);
            $('.welcome-title').text(`Bem-vindo, ${username.split(' ')[0]}!`);
            loadProfessorData();
        });

        // Função para carregar dados do professor (via backend)
        function loadProfessorData() {
            // Atualiza nome
            const nome = localStorage.getItem('username') || 'Professor';
            $('.user-title').text(nome);
            $('.welcome-title').text('Bem-vindo, ' + nome.split(' ')[0] + '!');

            // Ano letivo fixo solicitado (2025)
            const anoAtual = 2025;

            // Carrega estatísticas do dashboard (filtradas por 2025)
            $.getJSON('../includes/ajax/professor/dashboard_stats.php', { ano: anoAtual })
                .done(function (res) {
                    if (res && res.success && res.data) {
                        $('#stat-turmas').text(res.data.turmas);
                        $('#stat-alunos').text(res.data.alunos);
                        $('#stat-disciplinas').text(res.data.disciplinas);
                    } else {
                        $('#stat-turmas, #stat-alunos, #stat-disciplinas').text('0');
                    }
                })
                .fail(function () {
                    $('#stat-turmas, #stat-alunos, #stat-disciplinas').text('0');
                });

            // Carrega avisos (eventos do calendário) para os próximos 30 dias
            const start = new Date();
            const end = new Date();
            end.setDate(end.getDate() + 30);
            const toISO = d => d.toISOString().slice(0, 10);

            $.getJSON('../includes/ajax/calendario/listar_eventos.php', { start: toISO(start), end: toISO(end) })
                .done(function (res) {
                    let eventos = (res && res.success && Array.isArray(res.data)) ? res.data : [];
                    // Ordena por data
                    eventos.sort((a, b) => (a.start || '').localeCompare(b.start || ''));

                    // Popula painel de Eventos Próximos
                    let htmlEventos = '';
                    if (eventos.length === 0) {
                        htmlEventos = '<div class="empty-message">Nenhum evento agendado nos próximos 30 dias</div>';
                    } else {
                        eventos.slice(0, 6).forEach(ev => {
                            const dt = (ev.start || '').split('T')[0];
                            const [y, m, d] = dt.split('-');
                            const dataBR = `${d}/${m}/${y}`;
                            htmlEventos += `
                                <div class="event-item">
                                    <div class="event-date">${dataBR}</div>
                                    <div class="event-title">${ev.title || ''}</div>
                                </div>`;
                        });
                        if (eventos.length > 6) {
                            htmlEventos += '<div class="empty-message">Mais eventos no Calendário</div>';
                        }
                    }
                    $('#panel-eventos-proximos').find('.card-title').nextAll().remove();
                    $('#panel-eventos-proximos').append(htmlEventos);
                })
                .fail(function () {
                    $('#panel-eventos-proximos').find('.card-title').nextAll().remove();
                    $('#panel-eventos-proximos').append('<div class="empty-message">Não foi possível carregar os eventos</div>');
                });

            // Carrega Avaliações do professor (próximas)
            $.getJSON('../includes/ajax/professor/avaliacoes/listar_professor.php', { futuras: 1, limit: 8 })
                .done(function(res){
                    var avs = (res && res.success && Array.isArray(res.data)) ? res.data : [];
                    var html = '';
                    if (avs.length === 0) {
                        html = '<div class="empty-message">Nenhuma avaliação agendada</div>';
                    } else {
                        for (var i = 0; i < avs.length; i++){
                            var av = avs[i];
                            var dt = (av.Data || '').split('T')[0] || av.Data; // Data é DATE
                            var parts = (dt || '').split('-');
                            var dataBR = (parts.length === 3) ? (parts[2] + '/' + parts[1] + '/' + parts[0]) : dt;
                            var turma = (av.Nome_Turma || ('Turma ' + av.ID_Turma));
                            var disc = av.Disciplina || '';
                            html += ''+
                                '<div class="event-item">' +
                                '  <div class="event-date">' + dataBR + '</div>' +
                                '  <div class="event-title">' + turma + '</div>' +
                                (disc ? ('  <div class="class-time">Disciplina: ' + disc + '</div>') : '') +
                                '</div>';
                        }
                        html += '<div class="empty-message"><a href="avaliacoes.php">Ver todas as avaliações</a></div>';
                    }
                    $('#panel-avaliacoes').find('.event-item, .empty-message').remove();
                    $('#panel-avaliacoes').append(html);
                })
                .fail(function(){
                    $('#panel-avaliacoes').find('.event-item, .empty-message').remove();
                    $('#panel-avaliacoes').append('<div class="empty-message">Não foi possível carregar as avaliações</div>');
                });

            // Carrega horários do professor somente do ano de 2025 (fixo)
            $.getJSON('../includes/ajax/professor/horarios.php', { ano: anoAtual })
                .done(function(res){
                    const dados = (res && res.success && Array.isArray(res.data)) ? res.data : [];
                    const NOMES = {1:'Segunda-feira',2:'Terça-feira',3:'Quarta-feira',4:'Quinta-feira',5:'Sexta-feira',6:'Sábado',7:'Domingo'};
                    let html = '';
                    if (dados.length === 0){
                        html = '<div class="empty-message">Nenhum horário cadastrado</div>';
                    } else {
                        dados.slice(0,6).forEach(aula => {
                            const hi = (aula.Hora_Inicio||'').substring(0,5);
                            const hf = (aula.Hora_Fim||'').substring(0,5);
                            html += `
                                <div class="class-item">
                                    <div class="class-name">${aula.Nome_Disciplina} - ${aula.Nome_Turma}</div>
                                    <div class="class-time">${NOMES[aula.Dia_Semana]||aula.Dia_Semana}, ${hi} - ${hf}</div>
                                    <div class="class-room">${aula.Sala ? ('Sala ' + aula.Sala) : ''}</div>
                                </div>`;
                        });
                        if (dados.length > 6){
                            html += '<div class="empty-message">Mais aulas disponíveis em Horários</div>';
                        }
                    }
                    $('#panel-horarios').find('.class-item, .empty-message').remove();
                    $('#panel-horarios').append(html);
                })
                .fail(function(){
                    $('#panel-horarios').find('.class-item, .empty-message').remove();
                    $('#panel-horarios').append('<div class="empty-message">Não foi possível carregar os horários</div>');
                });

            // Estatísticas já carregadas acima com ano fixo

            // Logout é tratado via link ../logout.php no menu
        }

    
    </script>
    </body>
    </html>