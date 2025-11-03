<nav id="menu_padrao">
    <link rel="stylesheet" href="css/style.css">
        <div id="wrapper">
            <!-- Sidebar -->
            <div id="sidebar-wrapper" data-simplebar="" data-simplebar-auto-hide="true">
                <div class="brand-logo">
                    <a href="home.php">
                        <img src="../assets/images/logo-icon.png" class="logo-icon" alt="logo icon">
                        <h5 class="logo-text">Dashboard Acadêmico</h5>
                    </a>
                </div>
                <ul class="sidebar-menu do-nicescrol">
                    <li class="sidebar-header">NAVEGAÇÃO PRINCIPAL</li>
                    <li>
                        <a href="home.php" class="active">
                            <i class="zmdi zmdi-view-dashboard"></i> <span>Home</span>
                        </a>
                    </li>
                    <li>
                        <a href="turmas.php">
                            <i class="zmdi zmdi-group-work"></i> <span>Turmas</span>
                        </a>
                    </li>
                    <li>
                        <a href="notas.php">
                            <i class="zmdi zmdi-check-circle"></i> <span>Notas</span>
                        </a>
                    </li>
                    <li>
                        <a href="plano_ensino.php">
                            <i class="zmdi zmdi-assignment"></i> <span>Plano de Ensino</span>
                            <i class="zmdi zmdi-caret-down float-right"></i>
                        </a>
                        <ul class="sidebar-submenu">
                            <li><a href="avaliacoes.php"><i class="zmdi zmdi-check-circle"></i> Avaliações</a></li>
                            <li><a href="atividades.php"><i class="zmdi zmdi-assignment-check"></i> Atividades</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="presenca.php">
                            <i class="zmdi zmdi-time-countdown"></i> <span>Presença</span>
                            <i class="zmdi zmdi-caret-down float-right"></i>
                        </a>
                        <ul class="sidebar-submenu">
                            <li><a href="caderno_chamada.php"><i class="zmdi zmdi-accounts-list"></i> Caderno de
                                    Chamada</a></li>
                            <li><a href="ocorrencias.php"><i class="zmdi zmdi-alert-circle"></i> Ocorrências</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="orientacoes.php">
                            <i class="zmdi zmdi-bookmark"></i> <span>Orientações Acadêmicas</span>
                            <i class="zmdi zmdi-caret-down float-right"></i>
                        </a>
                        <ul class="sidebar-submenu">
                            <li><a href="normas.php"><i class="zmdi zmdi-assignment"></i> Normas</a></li>
                            <li><a href="calendario.php"><i class="zmdi zmdi-calendar"></i> Calendário</a></li>
                        </ul>
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
                        <li class="nav-item dropdown">
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
                                                <h6 class="mt-2 user-title">Professor</h6>
                                                <p class="user-subtitle">professor@escola.com</p>
                                            </div>
                                        </div>
                                    </a>
                                </li>   
                                <li class="dropdown-divider"></li>
                                <li class="dropdown-divider"></li>
                                <li class="dropdown-item">
                                    <a href="perfil.php" style="display: flex; align-items: center; width: 100%;">
                                        <i class="zmdi zmdi-account mr-2" style="min-width: 22px; text-align: center;"></i> Perfil Acadêmico
                                    </a>
                                </li>
                                <li class="dropdown-divider"></li>
                                <li class="dropdown-item">
                                    <a href="../logout.php" id="logout-btn" onclick="return confirm('Deseja sair?')" style="display: flex; align-items: center; width: 100%;">
                                        <i class="icon-power mr-2" style="min-width: 22px; text-align: center;"></i> Sair
                                    </a>
                                </li>
                        </li>
                    </ul>
                </nav>
            </header>
</nav>
<style>
.dropdown-item a {
    padding-left: 0 !important;
}
</style>