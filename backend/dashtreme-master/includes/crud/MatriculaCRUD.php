<?php
require_once 'BaseCRUD.php';

class MatriculaCRUD extends BaseCRUD {
    public function __construct($pdo) {
        parent::__construct($pdo, 'Matriculas', 'ID_Matricula');
    }
    
    // MATRICULAR ALUNO
    public function matricularAluno($idAluno, $idTurma, $tipoMatricula = 'Regular', $anoLetivo = null) {
        try {
            // Verifica se aluno já está matriculado nesta turma/ano
            $sqlVerifica = "SELECT ID_Matricula FROM Matriculas 
                           WHERE ID_Aluno = ? AND ID_Turma = ? AND Ano_Letivo = ?";
            $stmtVerifica = $this->pdo->prepare($sqlVerifica);
            $stmtVerifica->execute([$idAluno, $idTurma, $anoLetivo ?: date('Y')]);
            
            if ($stmtVerifica->fetch()) {
                throw new Exception("Aluno já está matriculado nesta turma para o ano letivo informado.");
            }
            
            $dadosMatricula = [
                'ID_Aluno' => $idAluno,
                'ID_Turma' => $idTurma,
                'Data_Matricula' => date('Y-m-d'),
                'Tipo_Matricula' => $tipoMatricula,
                'Ano_Letivo' => $anoLetivo ?: date('Y'),
                'Status' => 'Ativa'
            ];
            
            return $this->criar($dadosMatricula);
            
        } catch (PDOException $e) {
            throw new Exception("Erro ao matricular aluno: " . $e->getMessage());
        }
    }
    
    // BUSCAR MATRÍCULA COMPLETA
    public function buscarMatriculaCompleta($idMatricula) {
        try {
            $sql = "SELECT m.*, u.Nome_Completo as Aluno_Nome, t.Nome_Turma 
                    FROM Matriculas m 
                    INNER JOIN Usuarios u ON m.ID_Aluno = u.ID_Usuario 
                    INNER JOIN Turmas t ON m.ID_Turma = t.ID_Turma 
                    WHERE m.ID_Matricula = ?";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$idMatricula]);
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erro ao buscar matrícula: " . $e->getMessage());
        }
    }
    
    // LISTAR MATRÍCULAS POR TURMA
    public function listarPorTurma($idTurma, $anoLetivo = null) {
        try {
            $sql = "SELECT m.*, u.Nome_Completo as Aluno_Nome, u.Email, u.Telefone 
                    FROM Matriculas m 
                    INNER JOIN Usuarios u ON m.ID_Aluno = u.ID_Usuario 
                    WHERE m.ID_Turma = ? AND m.Status = 'Ativa'";
            
            $params = [$idTurma];
            if ($anoLetivo) {
                $sql .= " AND m.Ano_Letivo = ?";
                $params[] = $anoLetivo;
            }
            
            $sql .= " ORDER BY u.Nome_Completo";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erro ao listar matrículas da turma: " . $e->getMessage());
        }
    }
    
    // LISTAR MATRÍCULAS POR ALUNO
    public function listarPorAluno($idAluno) {
        try {
            $sql = "SELECT m.*, t.Nome_Turma, t.Etapa, t.Turno 
                    FROM Matriculas m 
                    INNER JOIN Turmas t ON m.ID_Turma = t.ID_Turma 
                    WHERE m.ID_Aluno = ? AND m.Status = 'Ativa' 
                    ORDER BY m.Ano_Letivo DESC, m.Data_Matricula DESC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$idAluno]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erro ao listar matrículas do aluno: " . $e->getMessage());
        }
    }
    
    // CANCELAR MATRÍCULA (TROCAR STATUS)
    public function cancelarMatricula($idMatricula) {
        try {
            return $this->atualizar($idMatricula, ['Status' => 'Cancelada']);
        } catch (PDOException $e) {
            throw new Exception("Erro ao cancelar matrícula: " . $e->getMessage());
        }
    }
    
    // TRANSFERIR ALUNO DE TURMA
    public function transferirAluno($idMatricula, $novaTurmaId) {
        try {
            $this->pdo->beginTransaction();
            
            // Cancela matrícula atual
            $this->atualizar($idMatricula, ['Status' => 'Transferida']);
            
            // Busca dados da matrícula original
            $matricula = $this->buscarPorId($idMatricula);
            
            // Cria nova matrícula
            $novaMatricula = [
                'ID_Aluno' => $matricula['ID_Aluno'],
                'ID_Turma' => $novaTurmaId,
                'Data_Matricula' => date('Y-m-d'),
                'Tipo_Matricula' => 'Transferência',
                'Ano_Letivo' => $matricula['Ano_Letivo'],
                'Status' => 'Ativa'
            ];
            
            $novaId = $this->criar($novaMatricula);
            
            $this->pdo->commit();
            return $novaId;
            
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw new Exception("Erro ao transferir aluno: " . $e->getMessage());
        }
    }
    
    // CONTAR MATRÍCULAS ATIVAS POR TURMA
    public function contarAtivasPorTurma($idTurma) {
        return $this->contar('ID_Turma = ? AND Status = "Ativa"', [$idTurma]);
    }
    
    // VERIFICAR SE ALUNO JÁ ESTÁ MATRICULADO
    public function alunoEstaMatriculado($idAluno, $anoLetivo = null) {
        try {
            $sql = "SELECT ID_Matricula FROM Matriculas 
                    WHERE ID_Aluno = ? AND Status = 'Ativa'";
            
            $params = [$idAluno];
            if ($anoLetivo) {
                $sql .= " AND Ano_Letivo = ?";
                $params[] = $anoLetivo;
            }
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->fetch() !== false;
        } catch (PDOException $e) {
            throw new Exception("Erro ao verificar matrícula: " . $e->getMessage());
        }
    }
}
?>