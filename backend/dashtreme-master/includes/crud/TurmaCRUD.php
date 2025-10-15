<?php
require_once 'BaseCRUD.php';

class TurmaCRUD extends BaseCRUD
{
    public function __construct($pdo)
    {
        parent::__construct($pdo, 'Turmas', 'ID_Turma');
    }

    // CRIAR TURMA COM VALIDAÇÃO
    public function criarTurma($dados)
    {
        // Verifica se já existe turma com mesmo nome no mesmo ano
        $sql = "SELECT * FROM Turmas WHERE Nome_Turma = ? AND Ano_Letivo = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$dados['Nome_Turma'], $dados['Ano_Letivo']]);
        $existe = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existe) {
            throw new Exception("Já existe uma turma com este nome no ano letivo informado.");
        }

        return $this->criar($dados);
    }

    // LISTAR TURMAS COM PROFESSORES
    public function listarTurmasComProfessor($anoLetivo = null)
    {
        try {
            $sql = "SELECT t.*, u.Nome_Completo as Professor_Nome 
                    FROM Turmas t 
                    LEFT JOIN Professores_Turmas pt ON t.ID_Turma = pt.ID_Turma 
                    LEFT JOIN Professores p ON pt.ID_Professor = p.ID_Professor 
                    LEFT JOIN Usuarios u ON p.ID_Professor = u.ID_Usuario 
                    WHERE 1=1";

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

    // LISTAR TODAS AS TURMAS (SIMPLES)
    public function listarTodas()
    {
        try {
            $sql = "SELECT * FROM Turmas ORDER BY Ano_Letivo DESC, Nome_Turma";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erro ao listar turmas: " . $e->getMessage());
        }
    }

    // BUSCAR TURMA COMPLETA
    public function buscarTurmaCompleta($idTurma)
    {
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
    public function vincularProfessor($idTurma, $idProfessor)
    {
        try {
            // Verificar se já existe o vínculo
            $sqlCheck = "SELECT * FROM Professores_Turmas WHERE ID_Professor = ? AND ID_Turma = ?";
            $stmtCheck = $this->pdo->prepare($sqlCheck);
            $stmtCheck->execute([$idProfessor, $idTurma]);

            if ($stmtCheck->fetch()) {
                throw new Exception("Este professor já está vinculado a esta turma.");
            }

            $sql = "INSERT INTO Professores_Turmas (ID_Professor, ID_Turma) VALUES (?, ?)";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$idProfessor, $idTurma]);
        } catch (PDOException $e) {
            throw new Exception("Erro ao vincular professor: " . $e->getMessage());
        }
    }

    // LISTAR PROFESSORES DA TURMA
    public function listarProfessoresDaTurma($idTurma)
    {
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
    public function listarPorAnoLetivo($anoLetivo)
    {
        try {
            $sql = "SELECT * FROM Turmas WHERE Ano_Letivo = ? ORDER BY Nome_Turma";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$anoLetivo]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erro ao listar turmas por ano letivo: " . $e->getMessage());
        }
    }

    // CONTAR ALUNOS NA TURMA
    public function contarAlunos($idTurma)
    {
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
    // BUSCAR TURMA POR ID
    public function buscar($id)
    {
        try {
            $sql = "SELECT * FROM Turmas WHERE ID_Turma = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erro ao buscar turma: " . $e->getMessage());
        }
    }

    // EXCLUIR TURMA (EXCLUSÃO FÍSICA)
    public function excluir($id)
    {
        try {
            // Verificar se há alunos matriculados
            $totalAlunos = $this->contarAlunos($id);
            if ($totalAlunos > 0) {
                throw new Exception("Não é possível excluir a turma pois existem alunos matriculados.");
            }

            // Remover vínculos com professores primeiro
            $sqlProfessores = "DELETE FROM Professores_Turmas WHERE ID_Turma = ?";
            $stmtProfessores = $this->pdo->prepare($sqlProfessores);
            $stmtProfessores->execute([$id]);

            // Excluir a turma
            $sqlTurma = "DELETE FROM Turmas WHERE ID_Turma = ?";
            $stmtTurma = $this->pdo->prepare($sqlTurma);
            return $stmtTurma->execute([$id]);
        } catch (Exception $e) {
            throw new Exception("Erro ao excluir turma: " . $e->getMessage());
        }
    }
}
?>