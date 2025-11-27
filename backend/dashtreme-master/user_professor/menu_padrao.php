<?php
if (session_status() === PHP_SESSION_NONE) { @session_start(); }
$nome = isset($_SESSION['user_name']) ? trim((string)$_SESSION['user_name']) : 'Professor';
$email = isset($_SESSION['user_email']) ? trim((string)$_SESSION['user_email']) : 'professor@escola.com';
function gerarIniciais($nome){
    $nome = trim($nome);
    if($nome==='') return 'PF';
    $parts = preg_split('/\s+/u',$nome);
    $first = mb_substr($parts[0],0,1,'UTF-8');
    $last = count($parts)>1 ? mb_substr(end($parts),0,1,'UTF-8') : (mb_strlen($parts[0],'UTF-8')>1 ? mb_substr($parts[0],1,1,'UTF-8') : $first);
    return mb_strtoupper($first.$last,'UTF-8');
}
$iniciais = gerarIniciais($nome);
// Ajusta caminhos relativos conforme a página atual (se está em /user_professor/ ou fora)
$script = $_SERVER['SCRIPT_NAME'] ?? '';
$isPerfil = strpos($script, '/public/perfil.php') !== false;
$inProfessorDir = strpos($script, '/user_professor/') !== false;
$base = $inProfessorDir ? '' : ($isPerfil ? '../user_professor/' : 'user_professor/');
$root = ($inProfessorDir || $isPerfil) ? '../' : '';
?>
<nav id="menu_padrao">
    <!-- Corrige caminho da folha de estilos central -->
    <link rel="stylesheet" href="<?= htmlspecialchars($root) ?>css/style.css?v=<?= time() ?>">
        <div id="wrapper">
            <!-- Sidebar -->
            <div id="sidebar-wrapper" data-simplebar="" data-simplebar-auto-hide="true">
                <div class="brand-logo">
                    <a href="<?= htmlspecialchars($base) ?>home.php">
                        <img src="<?= htmlspecialchars($root) ?>assets/images/logo-icon.png" class="logo-icon" alt="logo icon">
                        <h5 class="logo-text">Sistema Acadêmico Santos</h5>
                    </a>
                </div>
                <ul class="sidebar-menu do-nicescrol">
                    <li class="sidebar-header">NAVEGAÇÃO PRINCIPAL</li>
                    <li>
                        <a href="<?= htmlspecialchars($base) ?>home.php" class="active">
                            <i class="zmdi zmdi-view-dashboard"></i> <span>Home</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= htmlspecialchars($base) ?>turmas.php">
                            <i class="zmdi zmdi-group-work"></i> <span>Turmas</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= htmlspecialchars($base) ?>notas.php">
                            <i class="zmdi zmdi-check-circle"></i> <span>Notas</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= htmlspecialchars($base) ?>plano_ensino.php">
                            <i class="zmdi zmdi-assignment"></i> <span>Plano de Ensino</span>
                            <i class="zmdi zmdi-caret-down float-right"></i>
                        </a>
                        <ul class="sidebar-submenu">
                            <li><a href="<?= htmlspecialchars($base) ?>avaliacoes.php"><i class="zmdi zmdi-check-circle"></i> Avaliações</a></li>
                            <li><a href="<?= htmlspecialchars($base) ?>atividades.php"><i class="zmdi zmdi-assignment-check"></i> Atividades</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="<?= htmlspecialchars($base) ?>presenca.php">
                            <i class="zmdi zmdi-time-countdown"></i> <span>Presença</span>
                            <i class="zmdi zmdi-caret-down float-right"></i>
                        </a>
                        <ul class="sidebar-submenu">
                            <li><a href="<?= htmlspecialchars($base) ?>caderno_chamada.php"><i class="zmdi zmdi-accounts-list"></i> Caderno de
                                    Chamada</a></li>
                            <li><a href="<?= htmlspecialchars($base) ?>ocorrencias.php"><i class="zmdi zmdi-alert-circle"></i> Ocorrências</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="<?= htmlspecialchars($base) ?>orientacoes.php">
                            <i class="zmdi zmdi-bookmark"></i> <span>Orientações Acadêmicas</span>
                            <i class="zmdi zmdi-caret-down float-right"></i>
                        </a>
                        <ul class="sidebar-submenu">
                            <li><a href="<?= htmlspecialchars($base) ?>normas.php"><i class="zmdi zmdi-assignment"></i> Normas</a></li>
                            <li><a href="<?= htmlspecialchars($base) ?>calendario.php"><i class="zmdi zmdi-calendar"></i> Calendário</a></li>
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
                                <span class="user-profile"><span class="avatar-initials"><?php echo htmlspecialchars($iniciais); ?></span></span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li class="dropdown-item user-details">
                                    <a href="javaScript:void();">
                                        <div class="media">
                                            <div class="avatar-initials align-self-start mr-3"><?php echo htmlspecialchars($iniciais); ?></div>
                                            <div class="media-body">
                                                <h6 class="mt-2 user-title"><?php echo htmlspecialchars($nome); ?></h6>
                                                <p class="user-subtitle"><?php echo htmlspecialchars($email); ?></p>
                                            </div>
                                        </div>
                                    </a>
                                </li>   
                                <li class="dropdown-divider"></li>
                                <li class="dropdown-divider"></li>
                                <li class="dropdown-item">
                                    <a href="../public/perfil.php" style="display: flex; align-items: center; width: 100%;">
                                        <i class="zmdi zmdi-account mr-2" style="min-width: 22px; text-align: center;"></i> Perfil Acadêmico
                                    </a>
                                </li>
                                <li class="dropdown-divider"></li>
                                <li class="dropdown-item">
                                    <a href="<?= htmlspecialchars($root) ?>auth/logout.php" id="logout-btn" onclick="return confirm('Deseja sair?')" style="display: flex; align-items: center; width: 100%;">
                                        <i class="icon-power mr-2" style="min-width: 22px; text-align: center;"></i> Sair
                                    </a>
                                </li>
                        </li>
                    </ul>
                </nav>
            </header>
</nav>