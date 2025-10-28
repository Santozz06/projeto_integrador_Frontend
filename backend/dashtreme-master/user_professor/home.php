<?php require_once '../includes/bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard do Professor - Dashboard Acadêmico</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/app-style.css">
    <link rel="stylesheet" href="../assets/css/icons.css">
    <link rel="stylesheet" href="../assets/css/sidebar-menu.css">
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body {
            background: linear-gradient(to right, #2c3e50, #3498db);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #ecf0f1;
        }

        .content-wrapper {
            padding: 20px;
            padding-top: 20px; /* reduz o espaço entre navbar e conteúdo */
        }

        .dashboard-card {
            background: rgba(255, 255, 255, 0.05);
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            margin-bottom: 30px;
            height: 100%;
            position: relative;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .dashboard-card:hover {
            transform: translateY(-2px);
            z-index: 2;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.35);
        }

        .card-title {
            color: #ffffff;
            border-bottom: 2px solid #71affe;
            padding-bottom: 10px;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .empty-message {
            color: #bdc3c7;
            text-align: center;
            padding: 20px;
            font-style: italic;
        }

        .class-item {
            background: rgba(255, 255, 255, 0.08);
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .class-name {
            color: #ffffff;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .class-time {
            color: #bdc3c7;
            font-size: 0.9em;
        }

        .class-room {
            color: #f1c40f;
            font-size: 0.9em;
        }

        .event-item {
            padding: 10px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .event-date {
            color: #f1c40f;
            font-weight: 600;
        }

        .event-title {
            color: #ecf0f1;
        }

        .welcome-message {
            background: rgba(26, 188, 156, 0.2);
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            border-left: 5px solid #1abc9c;
        }

        .welcome-title {
            color: #1abc9c;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .welcome-text {
            color: #ecf0f1;
        }

        .quick-stats {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .stat-item {
            text-align: center;
            padding: 15px;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.08);
            flex: 1;
            margin: 0 5px;
        }

        .stat-value {
            font-size: 24px;
            font-weight: 600;
            color: #ffffff;
        }

        .stat-label {
            font-size: 0.9em;
            color: #bdc3c7;
        }


        .welcome-message {
            margin-top: 20px;
            position: relative;
            z-index: 1;
        }

        @media (max-width: 992px) {

            .content-wrapper {
                padding-top: 70px;
            }

            .navbar-nav .search-bar {
                display: flex !important;
                width: 100%;
                margin: 10px 0;
                order: 2;
            }

            .quick-stats {
                flex-direction: column;
                gap: 15px;
                margin-top: 15px;
            }

            .stat-item {
                width: 100%;
                margin: 0;
                padding: 12px;
            }

            .dashboard-card {
                padding: 18px;
                margin-bottom: 20px;
            }

            /* Mensagem de boas-vindas */
            .welcome-message {
                padding: 15px;
                margin-bottom: 20px;
            }

            .class-item,
            .event-item {
                padding: 12px;
                margin-bottom: 12px;
            }

            .stat-value {
                font-size: 20px;
            }

            .stat-label {
                font-size: 0.85em;
            }

            .card-title {
                font-size: 1.2em;
                margin-bottom: 15px;
            }

            .welcome-title {
                font-size: 1.2em;
            }

            .welcome-text {
                font-size: 0.95em;
            }

            .navbar-expand .navbar-nav {
                flex-direction: row;
                flex-wrap: wrap;
                align-items: center;
            }

            .navbar .nav-item {
                margin: 2px 0;
            }
        }

        /* Aumenta o espaçamento horizontal entre colunas desta linha específica */
        .row-cards {
            margin-left: -12px;
            margin-right: -12px;
        }
        .row-cards > [class*="col-"] {
            padding-left: 12px;
            padding-right: 12px;
        }

        @media (max-width: 768px) {
            .content-wrapper {
                padding-top: 60px;
                padding-left: 12px;
                padding-right: 12px;
            }

            .dashboard-card {
                padding: 15px;
            }

            .class-item,
            .event-item {
                padding: 10px;
            }

            .row>div[class*="col-"] {
                flex: 0 0 100%;
                max-width: 100%;
            }

            .search-bar {
                width: 100%;
                margin: 8px 0;
            }

            .search-bar .form-control {
                font-size: 14px;
            }
        }

        @media (max-width: 576px) {

            .content-wrapper {
                padding-top: 55px;
                padding-left: 10px;
                padding-right: 10px;
                padding-bottom: 10px;
            }

            .stat-value {
                font-size: 18px;
            }

            .card-title {
                font-size: 1.1em;
            }

            .class-item,
            .event-item {
                padding: 8px 10px;
                margin-bottom: 10px;
            }

            .event-date,
            .class-time,
            .class-room {
                font-size: 0.8em;
            }
        }

        .navbar {
            background-color: rgba(0, 0, 0, 0.2) !important;
            backdrop-filter: blur(10px);
        }

        /* Espaço vertical extra entre linhas de cards para evitar sobreposição no hover */
        .section-gap {
            margin-top: 28px; /* aumenta o respiro entre as linhas */
        }

        /* Efeito para o botão Sair */
        #logout-btn {
            transition: all 0.3s ease;
            border-radius: 4px;
            padding: 8px 12px;
        }

        #logout-btn:hover {
            background-color: #ff4444 !important;
            /* Vermelho suave */
            color: white !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(255, 68, 68, 0.2);
        }

        #logout-btn i {
            transition: all 0.3s ease;
        }

        #logout-btn:hover i {
            transform: rotate(15deg);
        }
    </style>
</head>

<body class="bg-theme bg-theme1">
    <?php
    require("menu_padrao.php");
    ?>
    

        <!-- Conteúdo principal -->
        <div class="content-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="welcome-message">
                            <h4 class="welcome-title">Bem-vindo, Professor!</h4>
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
                    <!-- Tarefas Pendentes -->
                    <div class="col-lg-12">
                        <div class="dashboard-card">
                            <h5 class="card-title"><i class="zmdi zmdi-assignment mr-2"></i> Tarefas Pendentes</h5>

                            <div class="event-item">
                                <div class="event-date">Para 18/03/2024</div>
                                <div class="event-title">Preparar material para aula sobre Revolução Industrial</div>
                            </div>

                            <div class="event-item">
                                <div class="event-date">Para 20/03/2024</div>
                                <div class="event-title">Corrigir provas do 1º bimestre</div>
                            </div>

                            <div class="empty-message">Nenhuma outra tarefa pendente</div>
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