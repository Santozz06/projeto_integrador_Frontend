<?php
require_once 'BaseCRUD.php';

class VinculoCRUD extends BaseCRUD
{
    public function __construct($pdo)
    {
        parent::__construct($pdo, 'Matriculas', 'ID_Matricula');
    }

    // VINCULAR ALUNO À TURMA
    public function vincularAluno($idAluno, $idTurma)
    {
        try {
            // Verificar se o aluno já está vinculado a esta turma
            $sqlCheck = "SELECT * FROM Matriculas WHERE ID_Aluno = ? AND ID_Turma = ? AND Status = 'Ativa'";
            $stmtCheck = $this->pdo->prepare($sqlCheck);
            $stmtCheck->execute([$idAluno, $idTurma]);

            if ($stmtCheck->fetch()) {
                throw new Exception("Este aluno já está vinculado a esta turma.");
            }

            // Verificar capacidade da turma
            $sqlCapacidade = "SELECT Capacidade_Alunos, 
                             (SELECT COUNT(*) FROM Matriculas WHERE ID_Turma = ? AND Status = 'Ativa') as alunos_matriculados
                             FROM Turmas WHERE ID_Turma = ?";
            $stmtCapacidade = $this->pdo->prepare($sqlCapacidade);
            $stmtCapacidade->execute([$idTurma, $idTurma]);
            $turma = $stmtCapacidade->fetch(PDO::FETCH_ASSOC);

            if ($turma['alunos_matriculados'] >= $turma['Capacidade_Alunos']) {
                throw new Exception("A turma atingiu sua capacidade máxima de alunos.");
            }

            // Fazer o vínculo
            $sql = "INSERT INTO Matriculas (ID_Aluno, ID_Turma, Data_Matricula, Status) 
                    VALUES (?, ?, NOW(), 'Ativa')";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$idAluno, $idTurma]);

        } catch (PDOException $e) {
            throw new Exception("Erro ao vincular aluno: " . $e->getMessage());
        }
    }

    // VINCULAR PROFESSOR À TURMA
    public function vincularProfessor($idProfessor, $idTurma)
    {
        try {
            // Verificar se o professor já está vinculado a esta turma
            $sqlCheck = "SELECT * FROM Professores_Turmas WHERE ID_Professor = ? AND ID_Turma = ?";
            $stmtCheck = $this->pdo->prepare($sqlCheck);
            $stmtCheck->execute([$idProfessor, $idTurma]);

            if ($stmtCheck->fetch()) {
                throw new Exception("Este professor já está vinculado a esta turma.");
            }

            // Fazer o vínculo
            $sql = "INSERT INTO Professores_Turmas (ID_Professor, ID_Turma) VALUES (?, ?)";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$idProfessor, $idTurma]);

        } catch (PDOException $e) {
            throw new Exception("Erro ao vincular professor: " . $e->getMessage());
        }
    }

    // LISTAR VÍNCULOS DO ALUNO
    public function listarVinculosAluno($idAluno)
    {
        try {
            $sql = "SELECT m.*, t.Nome_Turma, t.Ano_Letivo, t.Turno, t.Etapa
                    FROM Matriculas m
                    INNER JOIN Turmas t ON m.ID_Turma = t.ID_Turma
                    WHERE m.ID_Aluno = ? AND m.Status = 'Ativa'
                    ORDER BY t.Ano_Letivo DESC, t.Nome_Turma";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$idAluno]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erro ao listar vínculos do aluno: " . $e->getMessage());
        }
    }

    // LISTAR VÍNCULOS DO PROFESSOR
    public function listarVinculosProfessor($idProfessor)
    {
        try {
            $sql = "SELECT pt.*, t.Nome_Turma, t.Ano_Letivo, t.Turno, t.Etapa
                    FROM Professores_Turmas pt
                    INNER JOIN Turmas t ON pt.ID_Turma = t.ID_Turma
                    WHERE pt.ID_Professor = ?
                    ORDER BY t.Ano_Letivo DESC, t.Nome_Turma";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$idProfessor]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erro ao listar vínculos do professor: " . $e->getMessage());
        }
    }

    // REMOVER VÍNCULO DE ALUNO
    public function removerVinculoAluno($idMatricula)
    {
        try {
            $sql = "UPDATE Matriculas SET Status = 'Inativa', Data_Saida = NOW() WHERE ID_Matricula = ?";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$idMatricula]);
        } catch (PDOException $e) {
            throw new Exception("Erro ao remover vínculo do aluno: " . $e->getMessage());
        }
    }

    // REMOVER VÍNCULO DE PROFESSOR
    public function removerVinculoProfessor($idProfessor, $idTurma)
    {
        try {
            $sql = "DELETE FROM Professores_Turmas WHERE ID_Professor = ? AND ID_Turma = ?";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$idProfessor, $idTurma]);
        } catch (PDOException $e) {
            throw new Exception("Erro ao remover vínculo do professor: " . $e->getMessage());
        }
    }

    // VERIFICAR SITUAÇÃO DO USUÁRIO
    public function verificarSituacao($tipo, $idUsuario)
    {
        try {
            if ($tipo === 'aluno') {
                $sql = "SELECT COUNT(*) as total FROM Matriculas WHERE ID_Aluno = ? AND Status = 'Ativa'";
            } else {
                $sql = "SELECT COUNT(*) as total FROM Professores_Turmas WHERE ID_Professor = ?";
            }

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$idUsuario]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return $result['total'] > 0 ? 'Vinculado' : 'Ainda não vinculado';
        } catch (PDOException $e) {
            throw new Exception("Erro ao verificar situação: " . $e->getMessage());
        }
    }
}
?>