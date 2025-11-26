<nav id="menu_home">

  <body class="bg-theme bg-theme1">

    <div id="wrapper">

      <div id="sidebar-wrapper" data-simplebar="" data-simplebar-auto-hide="true">
        <div class="brand-logo">
          <a href="home.php">
            <img src="../assets/images/logo-icon.png" class="logo-icon" alt="logo icon">
            <h5 class="logo-text">Sistema Acadêmico Santos</h5>
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
            <a href="cadastro.php">
              <i class="zmdi zmdi-accounts"></i> <span>Cadastro</span>
              <i class="zmdi zmdi-caret-down float-right"></i>
            </a>
            <ul class="sidebar-submenu">
              <li><a href="cadastro.php#aluno"><i class="zmdi zmdi-accounts-alt"></i> Alunos</a></li>
              <li><a href="cadastroTurmas.php"><i class="zmdi zmdi-group-work"></i> Turmas</a></li>
              <li><a href="cadastro.php#servidor"><i class="zmdi zmdi-account-box"></i> Servidores</a></li>
              <li><a href="gerenciarVinculos.php"><i class="zmdi zmdi-link"></i> Gerenciar vínculos</a></li>
            </ul>
          </li>
          <li>
            <a href="relatorios.php">
              <i class="zmdi zmdi-chart"></i> <span>Relatórios</span>
            </a>
          </li>
          <li>
            <a href="atestados.php">
              <i class="zmdi zmdi-file-text"></i> <span>Atestados</span>
              <i class="zmdi zmdi-caret-down float-right"></i>
            </a>
            <ul class="sidebar-submenu">
              <li><a href="atestado_matricula.php"><i class="zmdi zmdi-assignment-account"></i> Atestado de
                  matrícula</a></li>
              <li><a href="atestado_frequencia.php"><i class="zmdi zmdi-time-countdown"></i> Frequência</a></li>
              <li><a href="historico.php"><i class="zmdi zmdi-assignment"></i> Histórico</a></li>
            </ul>
          </li>
          <li>
            <a href="matricula.php">
              <i class="zmdi zmdi-assignment-check"></i> <span>Matrícula</span>
              <i class="zmdi zmdi-caret-down float-right"></i>
            </a>
            <ul class="sidebar-submenu">
              <li><a href="transferencias.php"><i class="zmdi zmdi-account-add"></i> Transferências</a></li>
              <li><a href="rematriculas.php"><i class="zmdi zmdi-refresh"></i> Rematrículas</a></li>
            </ul>
          </li>
          <li>
            <a href="disciplinas.php">
              <i class="zmdi zmdi-book"></i> <span>Disciplinas</span>
              <i class="zmdi zmdi-caret-down float-right"></i>
            </a>
            <ul class="sidebar-submenu">
              <li><a href="cadastrar.php"><i class="zmdi zmdi-plus-circle"></i> Cadastrar</a></li>
              <li><a href="notas.php"><i class="zmdi zmdi-check-circle"></i> Notas</a></li>
            </ul>
          </li>
          <li>
            <a href="academico.php">
              <i class="zmdi zmdi-graduation-cap"></i> <span>Acadêmico</span>
              <i class="zmdi zmdi-caret-down float-right"></i>
            </a>
            <ul class="sidebar-submenu">
              <li><a href="calendario.php"><i class="zmdi zmdi-calendar"></i> Calendário</a></li>
              <li><a href="documentos.php"><i class="zmdi zmdi-file"></i> Documentos</a></li>
            </ul>
          </li>
        </ul>
      </div>

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
              <a class="nav-link dropdown-toggle dropdown-toggle-nocaret" href="#" id="navbarDropdownMenuLink"
                role="button" data-toggle="dropdown">
                <span class="alert-count">3</span>
                <i class="icon-bell"></i>
              </a>
              <div class="dropdown-menu dropdown-menu-right dropdown-notifications"
                aria-labelledby="navbarDropdownMenuLink">
                <div class="dropdown-header">
                  <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Notificações</h6>
                    <small class="text-muted">3 novas</small>
                  </div>
                  <a href="javascript:void(0)" id="marcarTodasComoLidas" class="text-primary small">Marcar todas como
                    lidas</a>
                </div>
                <div class="dropdown-list">
                  <a href="#" class="dropdown-item">
                    <div class="media">
                      <div class="notify-icon bg-primary">
                        <i class="zmdi zmdi-account-add"></i>
                      </div>
                      <div class="media-body">
                        <h6 class="notify-title">Novo aluno matriculado</h6>
                        <p class="notify-time">Há 5 minutos</p>
                      </div>
                    </div>
                  </a>
                  <a href="#" class="dropdown-item">
                    <div class="media">
                      <div class="notify-icon bg-success">
                        <i class="zmdi zmdi-calendar-check"></i>
                      </div>
                      <div class="media-body">
                        <h6 class="notify-title">Reunião pedagógica hoje</h6>
                        <p class="notify-time">Há 1 hora</p>
                      </div>
                    </div>
                  </a>
                  <a href="#" class="dropdown-item">
                    <div class="media">
                      <div class="notify-icon bg-warning">
                        <i class="zmdi zmdi-alert-circle"></i>
                      </div>
                      <div class="media-body">
                        <h6 class="notify-title">Documentação pendente</h6>
                        <p class="notify-time">Ontem</p>
                      </div>
                    </div>
                  </a>
                </div>
                <div class="dropdown-footer">
                  <a href="#">Ver todas as notificações</a>
                </div>
              </div>
            </li>

            <li class="nav-item">
              <a class="nav-link dropdown-toggle dropdown-toggle-nocaret" data-toggle="dropdown" href="#">
                <span class="user-profile"><img src="../assets/images/gallery/icon_usuarioBlack.png" class="img-circle"
                    alt="user avatar"></span>
              </a>
              <ul class="dropdown-menu dropdown-menu-right">
                <li class="dropdown-item user-details">
                  <a href="javaScript:void();">
                    <div class="media">
                      <div class="avatar"><img class="align-self-start mr-3" src="https://via.placeholder.com/110x110"
                          alt="user avatar"></div>
                      <div class="media-body">
                        <h6 class="mt-2 user-title" id="nomeAdmin">Nome do Administrador</h6>
                        <p class="user-subtitle" id="nomeInstituicao">Nome da Instituição</p>
                      </div>
                    </div>
                  </a>
                </li>
                <li class="dropdown-divider"></li>
                <li class="dropdown-item">
                  <a href="../auth/logout.php" id="logout-btn" onclick="return confirm('Deseja sair?')">
                    <i class="icon-power mr-2"></i> Sair
                  </a>
                </li>
              </ul>
            </li>
          </ul>
        </nav>

      </header>
</nav>