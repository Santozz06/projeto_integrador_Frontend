<?php require_once '../includes/bootstrap.php'; ?>
!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard do Professor - Dashboard Acadêmico</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/app-style.css">
    <link rel="stylesheet" href="../assets/css/icons.css">
    <link rel="stylesheet" href="../assets/css/sidebar-menu.css">
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            background: linear-gradient(to right, #2c3e50, #3498db);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #ecf0f1;
        }

        .content-wrapper {
            padding: 20px;
            padding-top: 80px;
        }

        .dashboard-card {
            background: rgba(255, 255, 255, 0.05);
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            margin-bottom: 30px;
            height: 100%;
            transition: transform 0.3s;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
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
                                    <div class="stat-value">5</div>
                                    <div class="stat-label">Turmas</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-value">32</div>
                                    <div class="stat-label">Alunos</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-value">4</div>
                                    <div class="stat-label">Disciplinas</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-value">2</div>
                                    <div class="stat-label">Avisos</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Horários e Salas de Aula -->
                    <div class="col-lg-6">
                        <div class="dashboard-card">
                            <h5 class="card-title"><i class="zmdi zmdi-time mr-2"></i> Horários e Salas de Aula</h5>

                            <div class="class-item">
                                <div class="class-name">Matemática - Turma A</div>
                                <div class="class-time">Segunda-feira, 7:30 - 9:40</div>
                                <div class="class-room">Sala 10</div>
                            </div>

                            <div class="class-item">
                                <div class="class-name">Geografia - Turma B</div>
                                <div class="class-time">Terça-feira, 10:00 - 11:30</div>
                                <div class="class-room">Sala 15</div>
                            </div>

                            <div class="class-item">
                                <div class="class-name">História - Turma A</div>
                                <div class="class-time">Quarta-feira, 13:00 - 14:30</div>
                                <div class="class-room">Sala 10</div>
                            </div>
                        </div>
                    </div>

                    <!-- Avisos da Instituição -->
                    <div class="col-lg-6">
                        <div class="dashboard-card">
                            <h5 class="card-title"><i class="zmdi zmdi-notifications-active mr-2"></i> Avisos da
                                Instituição</h5>

                            <div class="event-item">
                                <div class="event-date">15/03/2024</div>
                                <div class="event-title">Reunião pedagógica - Todos os professores</div>
                            </div>

                            <div class="event-item">
                                <div class="event-date">20/03/2024</div>
                                <div class="event-title">Prazo final para lançamento de notas do 1º bimestre</div>
                            </div>

                            <div class="empty-message">Nenhum outro aviso no momento</div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Tarefas Pendentes -->
                    <div class="col-lg-6">
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

                    <!-- Eventos Próximos -->
                    <div class="col-lg-6">
                        <div class="dashboard-card">
                            <h5 class="card-title"><i class="zmdi zmdi-calendar-note mr-2"></i> Eventos Próximos</h5>

                            <div class="event-item">
                                <div class="event-date">12/03/2024</div>
                                <div class="event-title">Conselho de Classe - Turma A</div>
                            </div>

                            <div class="event-item">
                                <div class="event-date">25/03/2024</div>
                                <div class="event-title">Feriado Municipal - Não haverá aula</div>
                            </div>

                            <div class="empty-message">Nenhum outro evento agendado</div>
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
        // Verifica se o usuário está logado e no lugar certo
        const expectedUserType = window.location.pathname.includes('professor') ? 'professor' :
            window.location.pathname.includes('aluno') ? 'aluno' : 'admin';

        if (localStorage.getItem('isLoggedIn') !== 'true' ||
            localStorage.getItem('userType') !== expectedUserType) {
            localStorage.clear();
            window.location.href = '../login.php';
        }
        // Verificação de autenticação
        document.addEventListener('DOMContentLoaded', function () {
            // Verifica se o usuário está logado
            if (localStorage.getItem('isLoggedIn') !== 'true') {
                window.location.href = '../index.php';
                return;
            }

            // Atualiza o nome do usuário no menu
            const username = localStorage.getItem('username') || 'Professor';
            $('.user-title').text(username);
            $('.welcome-title').text(`Bem-vindo, ${username.split(' ')[0]}!`);

            // Configura o botão de logout
            $('.dropdown-item').last().on('click', function (e) {
                e.preventDefault();
                logout();
            });

            // Carrega dados do professor
            loadProfessorData();
        });

        // Função para carregar dados do professor
        function loadProfessorData() {
            // Simula dados do professor
            const professorData = {
                nome: localStorage.getItem('username') || 'Professor Exemplo',
                turmas: 5,
                alunos: 32,
                disciplinas: 4
            };

            // Atualiza a interface
            $('.user-title').text(professorData.nome);
            $('.welcome-title').text('Bem-vindo, ' + professorData.nome.split(' ')[0] + '!');
            $('.stat-value').eq(0).text(professorData.turmas);
            $('.stat-value').eq(1).text(professorData.alunos);
            $('.stat-value').eq(2).text(professorData.disciplinas);

            // Simula carregamento de horários
            const horarios = [
                { disciplina: 'Matemática', turma: 'Turma A', dia: 'Segunda-feira', horario: '7:30 - 9:40', sala: 'Sala 10' },
                { disciplina: 'Geografia', turma: 'Turma B', dia: 'Terça-feira', horario: '10:00 - 11:30', sala: 'Sala 15' },
                { disciplina: 'História', turma: 'Turma A', dia: 'Quarta-feira', horario: '13:00 - 14:30', sala: 'Sala 10' }
            ];

            let html = '';
            horarios.forEach(aula => {
                html += `
    <div class="class-item">
        <div class="class-name">${aula.disciplina} - ${aula.turma}</div>
        <div class="class-time">${aula.dia}, ${aula.horario}</div>
        <div class="class-room">${aula.sala}</div>
    </div>
    `;
            });
            $('.dashboard-card').eq(0).find('.class-item').first().parent().html(html);

            // Simula carregamento de avisos
            const avisos = [
                { data: '15/03/2024', titulo: 'Reunião pedagógica - Todos os professores' },
                { data: '20/03/2024', titulo: 'Prazo final para lançamento de notas do 1º bimestre' }
            ];

            html = '';
            avisos.forEach(aviso => {
                html += `
    <div class="event-item">
        <div class="event-date">${aviso.data}</div>
        <div class="event-title">${aviso.titulo}</div>
    </div>
    `;
            });
            html += '<div class="empty-message">Nenhum outro aviso no momento</div>';
            $('.dashboard-card').eq(1).find('.event-item').first().parent().html(html);
        }

        // Função para logout
        function logout() {
            // Remove os dados de autenticação
            localStorage.removeItem('isLoggedIn');
            localStorage.removeItem('username');

            // Redireciona para a página de login
            window.location.href = '../login.php';
        }
    </script>
</body>

</html>