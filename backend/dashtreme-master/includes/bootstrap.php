<?php
// Inicia sessão somente se ainda não iniciada e cabeçalhos não enviados
if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    session_start();
}

// inclui conexão e CRUDs
require_once 'config/conexao.php';
require_once 'crud/BaseCRUD.php';
require_once 'crud/UsuarioCRUD.php';
require_once 'crud/TurmaCRUD.php';
require_once 'crud/DisciplinaCRUD.php';
require_once 'crud/MatriculaCRUD.php';


$usuarioCRUD = new UsuarioCRUD($pdo);
$turmaCRUD = new TurmaCRUD($pdo);
$disciplinaCRUD = new DisciplinaCRUD($pdo);
$matriculaCRUD = new MatriculaCRUD($pdo);

// checa auth
function verificarAuth($tipoRequerido = null) {
    // Se cabeçalhos já foram enviados, não tenta redirecionar para evitar warnings
    $canRedirect = !headers_sent();
    if (!isset($_SESSION['usuario_id'])) {
        if ($canRedirect) {
            header("Location: ../public/index.php");
            exit();
        }
        return false;
    }
    if ($tipoRequerido && (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== $tipoRequerido)) {
        if ($canRedirect) {
            header("Location: ../acesso_negado.php");
            exit();
        }
        return false;
    }
    return true;
}

// checa tipo pelo caminho
$url = $_SERVER['REQUEST_URI'];

if (strpos($url, '/user_adm/') !== false) {
    verificarAuth('admin');
} elseif (strpos($url, '/user_professor/') !== false) {
    verificarAuth('professor');
} elseif (strpos($url, '/user_aluno/') !== false) {
    verificarAuth('aluno');
}
?>