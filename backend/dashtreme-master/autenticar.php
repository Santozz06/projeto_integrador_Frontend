<?php
session_start();
require_once 'includes/conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login']);
    $senha = $_POST['senha'];
    $tipoUsuario = $_POST['userType'];
    
    try {
        // Consulta o usuário
    $sql = "SELECT u.ID_Usuario, u.Login, u.Senha, u.Nome_Completo, u.Email, u.IsAdmin 
        FROM Usuarios u 
        WHERE (u.Login = ? OR u.Email = ?)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$login, $login]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $senhaValida = false;
        if ($usuario) {
            $hash = $usuario['Senha'];
            // Suporta hash (bcrypt/argon) e texto puro legado
            if (is_string($hash) && strlen($hash) > 0 && (strpos($hash, '$2y$') === 0 || strpos($hash, '$argon2') === 0)) {
                $senhaValida = password_verify($senha, $hash);
            } else {
                $senhaValida = ($senha === $hash);
            }
        }

        if ($usuario && $senhaValida) {
            
            // VERIFICAÇÃO ESPECÍFICA PARA CADA TIPO
            if (validarTipoUsuario($pdo, $usuario, $tipoUsuario)) {
                
                // Cria a sessão
                $_SESSION['usuario_id'] = $usuario['ID_Usuario'];
                $_SESSION['user_name'] = $usuario['Nome_Completo'];
                $_SESSION['user_email'] = $usuario['Email'];
                $_SESSION['user_type'] = $tipoUsuario;
                
                redirecionarUsuario($tipoUsuario);
                exit();
            } else {
                header('Location: login.php?status=erro_tipo');
                exit();
            }
        }

        header('Location: login.php?status=erro');
        exit();
        
    } catch (PDOException $e) {
        error_log("Erro de autenticação: " . $e->getMessage());
    header('Location: login.php?status=erro');
        exit();
    }
}

function validarTipoUsuario($pdo, $usuario, $tipoSelecionado) {
    $idUsuario = $usuario['ID_Usuario'];
    
    switch ($tipoSelecionado) {
        case 'admin':
            return $usuario['IsAdmin'] == 1;
            
        case 'professor':
            // Verifica se existe na tabela Professores
            $stmt = $pdo->prepare("SELECT 1 FROM Professores WHERE ID_Professor = ?");
            $stmt->execute([$idUsuario]);
            return $stmt->fetch() !== false;
            
        case 'aluno':
            // Verifica se existe na tabela Alunos
            $stmt = $pdo->prepare("SELECT 1 FROM Alunos WHERE ID_Aluno = ?");
            $stmt->execute([$idUsuario]);
            return $stmt->fetch() !== false;
            
        default:
            return false;
    }
}

function redirecionarUsuario($tipo) {
    $redirecionamentos = [
        'admin' => 'user_adm/home.php',
        'professor' => 'user_professor/home.php', 
        'aluno' => 'user_aluno/index.php'
    ];
    
    header('Location: ' . ($redirecionamentos[$tipo] ?? 'index.php'));
    exit();
}
?>