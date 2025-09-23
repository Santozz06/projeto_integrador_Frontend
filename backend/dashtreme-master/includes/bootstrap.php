<?php
// Inicia sessão se não estiver iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Inclui conexão
require_once 'conexao.php';

// Função para verificar autenticação
function verificarAuth($tipoRequerido = null) {
    if (!isset($_SESSION['usuario_id'])) {
        header("Location: ../index.php");
        exit();
    }
    
    if ($tipoRequerido && $_SESSION['user_type'] !== $tipoRequerido) {
        header("Location: ../acesso_negado.php");
        exit();
    }
    
    return true;
}

// Verificação automática baseada na URL
$url = $_SERVER['REQUEST_URI'];
if (strpos($url, '/user_adm/') !== false) {
    verificarAuth('admin');
} elseif (strpos($url, '/user_professor/') !== false) {
    verificarAuth('professor');
} elseif (strpos($url, '/user_aluno/') !== false) {
    verificarAuth('aluno');
}
// Páginas fora dessas pastas não são verificadas automaticamente
?>