<nav id="menu_padrao">
    <body class="bg-theme bg-theme1">

    <div id="wrapper">
        <!-- Sidebar -->
        <div id="sidebar-wrapper" data-simplebar>
            <div class="brand-logo">
                <a href="index.php">
                    <img src="../assets/images/logo-icon.png" class="logo-icon" alt="logo icon">
                    <h5 class="logo-text">Dashboard Acadêmico</h5>
                </a>
            </div>
            <ul class="sidebar-menu do-nicescrol">
                <li class="sidebar-header">NAVEGAÇÃO PRINCIPAL</li>
                <li><a href="index.php"><i class="zmdi zmdi-view-dashboard"></i> <span>Home</span></a></li>
                <li><a href="ensino.php"><i class="zmdi zmdi-assignment"></i> <span>Ensino</span></a></li>
                <li><a href="componente_curricular.php"><i class="zmdi zmdi-calendar-note"></i> <span>Componente
                            curricular</span></a></li>
                <li><a href="notas.php"><i class="zmdi zmdi-file-text"></i> <span>Notas</span></a></li>
                <li><a href="calendario.php"><i class="zmdi zmdi-calendar"></i> <span>Calendário Acadêmico</span></a>
                </li>
            </ul>
        </div>

        <!-- Topbar -->
        <header class="topbar-nav">
            <nav class="navbar navbar-expand fixed-top">
                <ul class="navbar-nav mr-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link toggle-menu" href="javascript:void();">
                            <i class="icon-menu menu-icon"></i>
                        </a>
                    </li>
                </ul>

                <ul class="navbar-nav align-items-center right-nav-link">
                    <li class="nav-item">
                        <a class="nav-link dropdown-toggle dropdown-toggle-nocaret" data-toggle="dropdown" href="#">
                            <span class="user-profile"><img src="../assets/images/gallery/icon_usuarioBlack.png"
                                    class="img-circle" alt="user avatar"></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-right">
                            <li class="dropdown-item user-details">
                                <a href="javaScript:void();">
                                    <div class="media">
                                        <div class="avatar"><img class="align-self-start mr-3"
                                                src="https://via.placeholder.com/110x110" alt="user avatar"></div>
                                        <div class="media-body">
                                            <h6 class="mt-2 user-title" id="nomeAluno">Nome do Aluno</h6>
                                            <p class="user-subtitle" id="nomeInstituicao">Nome da Instituição</p>
                                        </div>
                                    </div>
                                </a>
                            </li>
                            <li class="dropdown-divider"></li>
                           <li class="dropdown-item"><a href="configuracoes.php"><i class="icon-settings mr-2"></i>
                                    Configurações</a></li>
                            <li class="dropdown-divider"></li>
                            <li class="dropdown-item" id="logout-btn"><i class="icon-power mr-2"></i> Sair</li>
                        </ul>
                    </li>
                </ul>
            </nav>
        </header>
</nav>    