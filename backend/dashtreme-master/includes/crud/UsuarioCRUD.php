<?php
require_once 'BaseCRUD.php';

class UsuarioCRUD extends BaseCRUD
{
    public function __construct($pdo)
    {
        parent::__construct($pdo, 'Usuarios', 'ID_Usuario');
    }

    // CADASTRO DE ALUNO
    public function cadastrarAluno($dadosUsuario, $matricula)
    {
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

    // LISTAR ALUNOS COM PAGINAÇÃO
    public function listarAlunos($pagina = 1, $limite = 10, $filtro = '')
    {
        try {
            $offset = ($pagina - 1) * $limite;
            $sql = "SELECT u.ID_Usuario, u.Nome_Completo, u.Email, u.Telefone, u.Data_Nascimento, a.Matricula
                FROM Usuarios u
                INNER JOIN Alunos a ON u.ID_Usuario = a.ID_Aluno
                WHERE 1=1";

            $params = [];
            if ($filtro) {
                $sql .= " AND (u.Nome_Completo LIKE ? OR u.Email LIKE ? OR a.Matricula LIKE ?)";
                $likeFilter = "%$filtro%";
                $params = [$likeFilter, $likeFilter, $likeFilter];
            }

            $sql .= " ORDER BY u.Nome_Completo LIMIT :limite OFFSET :offset";

            $stmt = $this->pdo->prepare($sql);

            // Bind dos parâmetros do filtro
            foreach ($params as $key => $value) {
                $stmt->bindValue($key + 1, $value);
            }

            // Bind dos parâmetros de paginação
            $stmt->bindValue(':limite', (int) $limite, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);

            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erro ao listar alunos: " . $e->getMessage());
        }
    }

    // CONTAR TOTAL DE ALUNOS
    public function countAlunos($filtro = '')
    {
        try {
            $sql = "SELECT COUNT(u.ID_Usuario) 
                    FROM Usuarios u 
                    INNER JOIN Alunos a ON u.ID_Usuario = a.ID_Aluno
                    WHERE 1=1";
            
            $params = [];
            if ($filtro) {
                $sql .= " AND (u.Nome_Completo LIKE ? OR u.Email LIKE ? OR a.Matricula LIKE ?)";
                $likeFilter = "%$filtro%";
                $params = [$likeFilter, $likeFilter, $likeFilter];
            }

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);

            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            throw new Exception("Erro ao contar alunos: " . $e->getMessage());
        }
    }

    // CADASTRO DE PROFESSOR
    public function cadastrarProfessor($dadosUsuario, $formacaoAcademica, $dataAdmissao, $areaAtuacao = null)
    {
        try {
            $this->pdo->beginTransaction();

            // Inserir na tabela Usuarios
            $sqlUsuario = "INSERT INTO Usuarios (Login, Nome_Completo, Email, Senha, Data_Nascimento, Sexo, CPF, Raca_Etnia, Estado_Civil, Nacionalidade, Naturalidade, Filiacao, Orgao_Exp, UF_Exp, Telefone, Endereco) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmtUsuario = $this->pdo->prepare($sqlUsuario);
            $stmtUsuario->execute([
                $dadosUsuario['Login'],
                $dadosUsuario['Nome_Completo'],
                $dadosUsuario['Email'],
                password_hash($dadosUsuario['Senha'], PASSWORD_DEFAULT),
                $dadosUsuario['Data_Nascimento'],
                $dadosUsuario['Sexo'],
                $dadosUsuario['CPF'],
                $dadosUsuario['Raca_Etnia'],
                $dadosUsuario['Estado_Civil'],
                $dadosUsuario['Nacionalidade'],
                $dadosUsuario['Naturalidade'],
                $dadosUsuario['Filiacao'],
                $dadosUsuario['Orgao_Exp'],
                $dadosUsuario['UF_Exp'],
                $dadosUsuario['Telefone'],
                $dadosUsuario['Endereco']
            ]);

            $idUsuario = $this->pdo->lastInsertId();

            // Inserir na tabela Professor 
            $sqlProfessor = "INSERT INTO Professores (ID_Professor, Formacao, Data_Ingresso, Area_Atuacao) 
                        VALUES (?, ?, ?, ?)";

            $stmtProfessor = $this->pdo->prepare($sqlProfessor);
            $stmtProfessor->execute([
                $idUsuario,
                $formacaoAcademica,
                $dataAdmissao,
                $areaAtuacao
            ]);

            $this->pdo->commit();
            return $idUsuario;

        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    // ATUALIZAR PROFESSOR
    public function atualizarProfessor($idProfessor, $dadosUsuario, $formacaoAcademica, $dataAdmissao, $areaAtuacao = null)
    {
        try {
            $this->pdo->beginTransaction();

            // Atualizar tabela Usuarios
            $sqlUsuario = "UPDATE Usuarios SET 
                      Login = ?, Nome_Completo = ?, Email = ?, Data_Nascimento = ?, 
                      Sexo = ?, CPF = ?, Raca_Etnia = ?, Estado_Civil = ?, 
                      Nacionalidade = ?, Naturalidade = ?, Filiacao = ?, 
                      Orgao_Exp = ?, UF_Exp = ?, Telefone = ?, Endereco = ?";

            if (!empty($dadosUsuario['Senha'])) {
                $sqlUsuario .= ", Senha = ?";
            }

            $sqlUsuario .= " WHERE ID_Usuario = ?";

            $stmtUsuario = $this->pdo->prepare($sqlUsuario);

            $params = [
                $dadosUsuario['Login'],
                $dadosUsuario['Nome_Completo'],
                $dadosUsuario['Email'],
                $dadosUsuario['Data_Nascimento'],
                $dadosUsuario['Sexo'],
                $dadosUsuario['CPF'],
                $dadosUsuario['Raca_Etnia'],
                $dadosUsuario['Estado_Civil'],
                $dadosUsuario['Nacionalidade'],
                $dadosUsuario['Naturalidade'],
                $dadosUsuario['Filiacao'],
                $dadosUsuario['Orgao_Exp'],
                $dadosUsuario['UF_Exp'],
                $dadosUsuario['Telefone'],
                $dadosUsuario['Endereco']
            ];

            if (!empty($dadosUsuario['Senha'])) {
                $params[] = password_hash($dadosUsuario['Senha'], PASSWORD_DEFAULT);
            }

            $params[] = $idProfessor;
            $stmtUsuario->execute($params);

            // Atualizar tabela Professores
            $sqlProfessor = "UPDATE Professores SET 
                        Formacao = ?, Data_Ingresso = ?, Area_Atuacao = ?
                        WHERE ID_Professor = ?";

            $stmtProfessor = $this->pdo->prepare($sqlProfessor);
            $stmtProfessor->execute([
                $formacaoAcademica,
                $dataAdmissao,
                $areaAtuacao,
                $idProfessor
            ]);

            $this->pdo->commit();

        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    // ATUALIZAR ALUNO
    public function atualizarAluno($idUsuario, $dadosUsuario, $matricula)
    {
        try {
            $this->pdo->beginTransaction();

            // Atualiza a tabela Usuarios (assumindo que o método `atualizar` existe na BaseCRUD)
            $this->atualizar($idUsuario, $dadosUsuario);

            // Atualiza a matrícula na tabela Alunos
            $sql = "UPDATE Alunos SET Matricula = ? WHERE ID_Aluno = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$matricula, $idUsuario]);

            $this->pdo->commit();
            return $idUsuario;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw new Exception("Erro ao atualizar o aluno: " . $e->getMessage());
        }
    }

    // BUSCAR ALUNO COMPLETO
    public function buscarAlunoCompleto($idAluno)
    {
        try {
            $sql = "SELECT u.*, a.Matricula 
                    FROM Usuarios u 
                    INNER JOIN Alunos a ON u.ID_Usuario = a.ID_Aluno 
                    WHERE u.ID_Usuario = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$idAluno]);

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erro ao buscar aluno: " . $e->getMessage());
        }
    }

    // BUSCAR PROFESSOR COMPLETO
    public function buscarProfessorCompleto($idProfessor)
    {
        $sql = "SELECT u.*, p.Formacao, p.Data_Ingresso, p.Area_Atuacao 
            FROM Usuarios u 
            INNER JOIN Professores p ON u.ID_Usuario = p.ID_Professor 
            WHERE u.ID_Usuario = ?";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$idProfessor]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }



    // LISTAR PROFESSORES COM PAGINAÇÃO
    public function listarProfessores($pagina = 1, $limite = 10)
    {
        $offset = ($pagina - 1) * $limite;
        $sql = "SELECT u.*, p.Formacao AS Formacao_Academica, p.Data_Ingresso, p.Area_Atuacao 
            FROM Usuarios u 
            INNER JOIN Professores p ON u.ID_Usuario = p.ID_Professor 
            ORDER BY u.Nome_Completo
            LIMIT :limite OFFSET :offset";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limite', (int) $limite, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // CONTAR TOTAL DE PROFESSORES
    public function countProfessores()
    {
        $sql = "SELECT COUNT(u.ID_Usuario) 
                FROM Usuarios u 
                INNER JOIN Professores p ON u.ID_Usuario = p.ID_Professor";
        
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchColumn();
    }

    // VERIFICAR SE EMAIL JÁ EXISTE
    public function emailExiste($email, $idUsuarioExcluir = null)
    {
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
    public function cpfExiste($cpf, $idUsuarioExcluir = null)
    {
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
    public function buscarPorTipo($tipo, $idUsuario)
    {
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