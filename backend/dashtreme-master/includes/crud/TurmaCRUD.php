<?php
require_once 'BaseCRUD.php';

class TurmaCRUD extends BaseCRUD {
    public function __construct($pdo) {
        parent::__construct($pdo, 'Turmas', 'ID_Turma');
    }
    
    // CRIAR TURMA COM VALIDAÇÃO
    public function criarTurma($dados) {
        // Verifica se já existe turma com mesmo nome no mesmo ano
        $existe = $this->buscarPorCampo('Nome_Turma', $dados['Nome_Turma']);
        $existe = array_filter($existe, function($turma) use ($dados) {
            return $turma['Ano_Letivo'] == $dados['Ano_Letivo'];
        });
        
        if (!empty($existe)) {
            throw new Exception("Já existe uma turma com este nome no ano letivo informado.");
        }
        
        return $this->criar($dados);
    }
    
    // LISTAR TURMAS COM PROFESSORES
    public function listarTurmasComProfessor($anoLetivo = null) {
        try {
            $sql = "SELECT t.*, u.Nome_Completo as Professor_Nome 
                    FROM Turmas t 
                    LEFT JOIN Professores_Turmas pt ON t.ID_Turma = pt.ID_Turma 
                    LEFT JOIN Professores p ON pt.ID_Professor = p.ID_Professor 
                    LEFT JOIN Usuarios u ON p.ID_Professor = u.ID_Usuario 
                    WHERE t.Ativo = 1";
            
            $params = [];
            if ($anoLetivo) {
                $sql .= " AND t.Ano_Letivo = ?";
                $params[] = $anoLetivo;
            }
            
            $sql .= " ORDER BY t.Ano_Letivo DESC, t.Nome_Turma";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erro ao listar turmas: " . $e->getMessage());
        }
    }
    
    // BUSCAR TURMA COMPLETA
    public function buscarTurmaCompleta($idTurma) {
        try {
            $sql = "SELECT t.*, u.Nome_Completo as Professor_Nome 
                    FROM Turmas t 
                    LEFT JOIN Professores_Turmas pt ON t.ID_Turma = pt.ID_Turma 
                    LEFT JOIN Professores p ON pt.ID_Professor = p.ID_Professor 
                    LEFT JOIN Usuarios u ON p.ID_Professor = u.ID_Usuario 
                    WHERE t.ID_Turma = ?";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$idTurma]);
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erro ao buscar turma: " . $e->getMessage());
        }
    }
    
    // VINCULAR PROFESSOR À TURMA
    public function vincularProfessor($idTurma, $idProfessor) {
        try {
            $sql = "INSERT INTO Professores_Turmas (ID_Professor, ID_Turma) VALUES (?, ?)";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$idProfessor, $idTurma]);
        } catch (PDOException $e) {
            throw new Exception("Erro ao vincular professor: " . $e->getMessage());
        }
    }
    
    // LISTAR PROFESSORES DA TURMA
    public function listarProfessoresDaTurma($idTurma) {
        try {
            $sql = "SELECT u.ID_Usuario, u.Nome_Completo, u.Email 
                    FROM Professores_Turmas pt 
                    INNER JOIN Professores p ON pt.ID_Professor = p.ID_Professor 
                    INNER JOIN Usuarios u ON p.ID_Professor = u.ID_Usuario 
                    WHERE pt.ID_Turma = ?";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$idTurma]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erro ao listar professores da turma: " . $e->getMessage());
        }
    }
    
    // LISTAR TURMAS POR ANO LETIVO
    public function listarPorAnoLetivo($anoLetivo) {
        return $this->listar('Ano_Letivo = ?', [$anoLetivo], 'Nome_Turma ASC');
    }
    
    // CONTAR ALUNOS NA TURMA
    public function contarAlunos($idTurma) {
        try {
            $sql = "SELECT COUNT(*) as total FROM Matriculas WHERE ID_Turma = ? AND Status = 'Ativa'";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$idTurma]);
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'];
        } catch (PDOException $e) {
            throw new Exception("Erro ao contar alunos: " . $e->getMessage());
        }
    }
}
?>