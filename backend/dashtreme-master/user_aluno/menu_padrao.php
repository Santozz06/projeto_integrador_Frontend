<?php
if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}
$nome = isset($_SESSION['user_name']) ? trim((string) $_SESSION['user_name']) : 'Aluno';
$email = isset($_SESSION['user_email']) ? trim((string) $_SESSION['user_email']) : 'aluno@escola.com';
function gerarIniciaisAluno($nome)
{
    $nome = trim($nome);
    if ($nome === '')
        return 'AL';
    $parts = preg_split('/\s+/u', $nome);
    $first = mb_substr($parts[0], 0, 1, 'UTF-8');
    $last = count($parts) > 1 ? mb_substr(end($parts), 0, 1, 'UTF-8') : (mb_strlen($parts[0], 'UTF-8') > 1 ? mb_substr($parts[0], 1, 1, 'UTF-8') : $first);
    return mb_strtoupper($first . $last, 'UTF-8');
}
$iniciais = gerarIniciaisAluno($nome);
// Ajusta caminhos relativos conforme a página atual 
$script = $_SERVER['SCRIPT_NAME'] ?? '';
$isPerfil = strpos($script, '/public/perfil.php') !== false;
$inAlunoDir = strpos($script, '/user_aluno/') !== false;
$base = $inAlunoDir ? '' : ($isPerfil ? '../user_aluno/' : 'user_aluno/');
$root = ($inAlunoDir || $isPerfil) ? '../' : '';
?>
<nav id="menu_padrao">
    <div id="wrapper">
        <!-- Sidebar -->
        <div id="sidebar-wrapper" data-simplebar>
            <div class="brand-logo">
                <a href="<?= htmlspecialchars($base) ?>index.php">
                    <img src="<?= htmlspecialchars($root) ?>assets/images/logo-icon.png" class="logo-icon"
                        alt="logo icon">
                    <h5 class="logo-text">Sistema Acadêmico Santos</h5>
                </a>
            </div>
            <ul class="sidebar-menu do-nicescrol">
                <li class="sidebar-header">NAVEGAÇÃO PRINCIPAL</li>
                <li><a href="<?= htmlspecialchars($base) ?>index.php"><i class="zmdi zmdi-view-dashboard"></i>
                        <span>Home</span></a></li>
                <li><a href="<?= htmlspecialchars($base) ?>ensino.php"><i class="zmdi zmdi-assignment"></i>
                        <span>Ensino</span></a></li>
                <li><a href="<?= htmlspecialchars($base) ?>componente_curricular.php"><i
                            class="zmdi zmdi-calendar-note"></i> <span>Componente
                            curricular</span></a></li>
                <li><a href="<?= htmlspecialchars($base) ?>notas.php"><i class="zmdi zmdi-file-text"></i>
                        <span>Notas</span></a></li>
                <li><a href="<?= htmlspecialchars($base) ?>calendario.php"><i class="zmdi zmdi-calendar"></i>
                        <span>Calendário
                            Acadêmico</span></a>
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
                            <span class="user-profile"><span
                                    class="avatar-initials"><?php echo htmlspecialchars($iniciais); ?></span></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-right">
                            <li class="dropdown-item user-details">
                                <a href="javaScript:void();">
                                    <div class="media">
                                        <div class="avatar-initials align-self-start mr-3">
                                            <?php echo htmlspecialchars($iniciais); ?></div>
                                        <div class="media-body">
                                            <h6 class="mt-2 user-title" id="nomeAluno">
                                                <?php echo htmlspecialchars($nome); ?></h6>
                                            <p class="user-subtitle" id="nomeInstituicao">
                                                <?php echo htmlspecialchars($email); ?></p>
                                        </div>
                                    </div>
                                </a>
                            </li>
                            <li class="dropdown-divider"></li>
                            <li class="dropdown-item"><a href="<?= htmlspecialchars($root) ?>public/perfil.php"><i
                                        class="icon-user mr-2"></i>
                                    Perfil</a></li>
                            <li class="dropdown-divider"></li>
                            <li class="dropdown-item">
                                <a href="<?= htmlspecialchars($root) ?>auth/logout.php" id="logout-btn"
                                    onclick="return confirm('Deseja sair?')">
                                    <i class="icon-power mr-2"></i> Sair
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </nav>
        </header>
</nav>