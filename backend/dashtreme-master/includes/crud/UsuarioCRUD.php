<?php
require_once 'BaseCRUD.php';

class UsuarioCRUD extends BaseCRUD {
    public function __construct($pdo) {
        parent::__construct($pdo, 'Usuarios', 'ID_Usuario');
    }
    
    // CADASTRO DE ALUNO
    public function cadastrarAluno($dadosUsuario, $matricula = '') {
        try {
            $this->pdo->beginTransaction();
            
            // 1. Cadastra na tabela Usuarios
            $idUsuario = $this->criar($dadosUsuario);
            
            // 2. Cadastra na tabela Alunos
            $sqlAluno = "INSERT INTO Alunos (ID_Aluno, Matricula) VALUES (?, ?)";
            $stmtAluno = $this->pdo->prepare($sqlAluno);
            $stmtAluno->execute([$idUsuario, $matricula]);
            
            $this->pdo->commit();
            return $idUsuario;
            
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw new Exception("Erro no cadastro do aluno: " . $e->getMessage());
        }
    }
    
    // CADASTRO DE PROFESSOR
    public function cadastrarProfessor($dadosUsuario, $formacao = '', $dataIngresso = null) {
        try {
            $this->pdo->beginTransaction();
            
            // 1. Cadastra na tabela Usuarios
            $idUsuario = $this->criar($dadosUsuario);
            
            // 2. Cadastra na tabela Professores
            $sqlProfessor = "INSERT INTO Professores (ID_Professor, Formacao, Data_Ingresso) VALUES (?, ?, ?)";
            $stmtProfessor = $this->pdo->prepare($sqlProfessor);
            $stmtProfessor->execute([$idUsuario, $formacao, $dataIngresso ?: date('Y-m-d')]);
            
            $this->pdo->commit();
            return $idUsuario;
            
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw new Exception("Erro no cadastro do professor: " . $e->getMessage());
        }
    }
    
    // BUSCAR ALUNO COMPLETO
    public function buscarAlunoCompleto($idAluno) {
        try {
            $sql = "SELECT u.*, a.Matricula 
                    FROM Usuarios u 
                    INNER JOIN Alunos a ON u.ID_Usuario = a.ID_Aluno 
                    WHERE u.ID_Usuario = ? AND u.Ativo = 1";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$idAluno]);
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erro ao buscar aluno: " . $e->getMessage());
        }
    }
    
    // BUSCAR PROFESSOR COMPLETO
    public function buscarProfessorCompleto($idProfessor) {
        try {
            $sql = "SELECT u.*, p.Formacao, p.Data_Ingresso 
                    FROM Usuarios u 
                    INNER JOIN Professores p ON u.ID_Usuario = p.ID_Professor 
                    WHERE u.ID_Usuario = ? AND u.Ativo = 1";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$idProfessor]);
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erro ao buscar professor: " . $e->getMessage());
        }
    }
    
    // LISTAR ALUNOS
    public function listarAlunos($filtro = '') {
        try {
            $sql = "SELECT u.ID_Usuario, u.Nome_Completo, u.Email, u.Telefone, u.Data_Nascimento, a.Matricula 
                    FROM Usuarios u 
                    INNER JOIN Alunos a ON u.ID_Usuario = a.ID_Aluno 
                    WHERE u.Ativo = 1";
            
            $params = [];
            if ($filtro) {
                $sql .= " AND (u.Nome_Completo LIKE ? OR u.Email LIKE ? OR a.Matricula LIKE ?)";
                $likeFilter = "%$filtro%";
                $params = [$likeFilter, $likeFilter, $likeFilter];
            }
            
            $sql .= " ORDER BY u.Nome_Completo";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erro ao listar alunos: " . $e->getMessage());
        }
    }
    
    // LISTAR PROFESSORES
    public function listarProfessores($filtro = '') {
        try {
            $sql = "SELECT u.ID_Usuario, u.Nome_Completo, u.Email, u.Telefone, p.Formacao, p.Data_Ingresso 
                    FROM Usuarios u 
                    INNER JOIN Professores p ON u.ID_Usuario = p.ID_Professor 
                    WHERE u.Ativo = 1";
            
            $params = [];
            if ($filtro) {
                $sql .= " AND (u.Nome_Completo LIKE ? OR u.Email LIKE ? OR p.Formacao LIKE ?)";
                $likeFilter = "%$filtro%";
                $params = [$likeFilter, $likeFilter, $likeFilter];
            }
            
            $sql .= " ORDER BY u.Nome_Completo";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erro ao listar professores: " . $e->getMessage());
        }
    }
    
    // VERIFICAR SE EMAIL JÁ EXISTE
    public function emailExiste($email, $idUsuarioExcluir = null) {
        try {
            $sql = "SELECT ID_Usuario FROM Usuarios WHERE Email = ? AND Ativo = 1";
            $params = [$email];
            
            if ($idUsuarioExcluir) {
                $sql .= " AND ID_Usuario != ?";
                $params[] = $idUsuarioExcluir;
            }
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->fetch() !== false;
        } catch (PDOException $e) {
            throw new Exception("Erro ao verificar email: " . $e->getMessage());
        }
    }
    
    // VERIFICAR SE CPF JÁ EXISTE
    public function cpfExiste($cpf, $idUsuarioExcluir = null) {
        try {
            $sql = "SELECT ID_Usuario FROM Usuarios WHERE CPF = ? AND Ativo = 1";
            $params = [$cpf];
            
            if ($idUsuarioExcluir) {
                $sql .= " AND ID_Usuario != ?";
                $params[] = $idUsuarioExcluir;
            }
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->fetch() !== false;
        } catch (PDOException $e) {
            throw new Exception("Erro ao verificar CPF: " . $e->getMessage());
        }
    }
    
    // BUSCAR POR TIPO (ALUNO/PROFESSOR/ADMIN)
    public function buscarPorTipo($tipo, $idUsuario) {
        try {
            if ($tipo === 'aluno') {
                $sql = "SELECT u.* FROM Usuarios u INNER JOIN Alunos a ON u.ID_Usuario = a.ID_Aluno WHERE u.ID_Usuario = ?";
            } elseif ($tipo === 'professor') {
                $sql = "SELECT u.* FROM Usuarios u INNER JOIN Professores p ON u.ID_Usuario = p.ID_Professor WHERE u.ID_Usuario = ?";
            } else {
                $sql = "SELECT u.* FROM Usuarios u WHERE u.ID_Usuario = ? AND u.IsAdmin = 1";
            }
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$idUsuario]);
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erro ao buscar usuário por tipo: " . $e->getMessage());
        }
    }
}
?>