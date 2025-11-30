<?php
require_once 'BaseCRUD.php';

class DisciplinaCRUD extends BaseCRUD {
    public function __construct($pdo) {
        parent::__construct($pdo, 'Disciplinas', 'ID_Disciplina');
    }
    
    // CRIAR DISCIPLINA COM PROFESSOR
    public function criarDisciplina($dados) {
        try {
            $this->pdo->beginTransaction();
            
            // Cria a disciplina
            $idDisciplina = $this->criar($dados);
            
            $this->pdo->commit();
            return $idDisciplina;
            
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw new Exception("Erro ao criar disciplina: " . $e->getMessage());
        }
    }
    
    // LISTAR DISCIPLINAS COM PROFESSOR
    public function listarDisciplinasComProfessor($anoLetivo = null) {
        try {
            $sql = "SELECT d.*, u.Nome_Completo as Professor_Nome 
                    FROM Disciplinas d 
                    LEFT JOIN Usuarios u ON d.ID_Professor = u.ID_Usuario 
                    WHERE d.Ativo = 1";
            
            $params = [];
            if ($anoLetivo) {
                $sql .= " AND d.Ano_Letivo = ?";
                $params[] = $anoLetivo;
            }
            
            $sql .= " ORDER BY d.Nome_Disciplina";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erro ao listar disciplinas: " . $e->getMessage());
        }
    }
    
    // LISTAR DISCIPLINAS POR PROFESSOR
    public function listarPorProfessor($idProfessor, $anoLetivo = null) {
        try {
            $sql = "SELECT d.* FROM Disciplinas d WHERE d.ID_Professor = ? AND d.Ativo = 1";
            $params = [$idProfessor];
            
            if ($anoLetivo) {
                $sql .= " AND d.Ano_Letivo = ?";
                $params[] = $anoLetivo;
            }
            
            $sql .= " ORDER BY d.Nome_Disciplina";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erro ao listar disciplinas do professor: " . $e->getMessage());
        }
    }
    
    // LISTAR DISCIPLINAS POR TURMA
    public function listarPorTurma($idTurma) {
        try {
            $sql = "SELECT DISTINCT d.*, u.Nome_Completo as Professor_Nome 
                    FROM Disciplinas d 
                    LEFT JOIN Usuarios u ON d.ID_Professor = u.ID_Usuario 
                    INNER JOIN Matriculas m ON d.ID_Disciplina = m.ID_Disciplina 
                    WHERE m.ID_Turma = ? AND d.Ativo = 1 
                    ORDER BY d.Nome_Disciplina";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$idTurma]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erro ao listar disciplinas da turma: " . $e->getMessage());
        }
    }
    
    // VINCULAR DISCIPLINA À TURMA
    public function vincularATurma($idDisciplina, $idTurma) {
        try {
            throw new Exception("Funcionalidade não implementada. Use MatriculaCRUD para vincular disciplinas.");
        } catch (PDOException $e) {
            throw new Exception("Erro ao vincular disciplina: " . $e->getMessage());
        }
    }
}
?>