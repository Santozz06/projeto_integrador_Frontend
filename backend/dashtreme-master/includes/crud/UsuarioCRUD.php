<?php
require_once 'BaseCRUD.php';

class UsuarioCRUD extends BaseCRUD
{
    public function __construct($pdo)
    {
        parent::__construct($pdo, 'Usuarios', 'ID_Usuario');
    }

    /**
     * Normaliza campos do usuário removendo máscaras e mantendo apenas dígitos
     * para CPF, CEP, Telefone, Celular e Telefone_Fixo.
     * Limita tamanhos máximos para evitar estouro na coluna do banco.
     *
     * @param array $dados
     * @return array
     */
    private function sanitizeUsuarioFields(array $dados): array
    {
        $numericOnly = ['CPF', 'CEP', 'Telefone', 'Celular', 'Telefone_Fixo'];
        foreach ($numericOnly as $k) {
            if (isset($dados[$k]) && $dados[$k] !== null) {
                $val = preg_replace('/\D+/', '', (string)$dados[$k]);
                // limites razoáveis por campo
                if ($k === 'CPF') {
                    $val = substr($val, 0, 11);
                } elseif ($k === 'CEP') {
                    $val = substr($val, 0, 8);
                } else { // telefones
                    $val = substr($val, 0, 11); // DDD + número
                }
                $dados[$k] = $val;
            }
        }
        return $dados;
    }

    private function hasColumn($table, $column)
    {
        try {
            $sql = "SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$table, $column]);
            return (bool)$stmt->fetchColumn();
        } catch (Exception $e) {
            return false;
        }
    }

    private function filterCamposValidos(array $dados): array
    {
        if (empty($dados)) return $dados;
        $cols = array_keys($dados);
        $placeholders = rtrim(str_repeat('?,', count($cols)), ',');
        try {
            $sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'Usuarios' AND COLUMN_NAME IN ($placeholders)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($cols);
            $validas = array_map(fn($r) => $r['COLUMN_NAME'], $stmt->fetchAll(PDO::FETCH_ASSOC));
            return array_intersect_key($dados, array_flip($validas));
        } catch (Exception $e) {
            // Fallback para whitelist básica
            $whitelist = [
                'Login','Senha','Nome_Completo','Data_Nascimento','Sexo','CPF','Orgao_Exp','UF_Exp','Raca_Etnia','Endereco','Telefone','Email','Possui_Necessidades_Especiais','IsAdmin','Ativo','Estado_Civil','Nacionalidade','Naturalidade','Filiacao',
                'RG','Data_Expedicao','CEP','Numero','Complemento','Bairro','UF_Endereco','Municipio_Endereco','Celular','Logradouro','Telefone_Fixo'
            ];
            return array_intersect_key($dados, array_flip($whitelist));
        }
    }

    private function inserirUsuario(array $dadosUsuario): int
    {
        if (empty($dadosUsuario['Login']) && !empty($dadosUsuario['Email'])) {
            $dadosUsuario['Login'] = $dadosUsuario['Email'];
        }
        // Normaliza documentos e telefones antes de persistir
        $dadosUsuario = $this->sanitizeUsuarioFields($dadosUsuario);
        if (!empty($dadosUsuario['Senha']) && strlen($dadosUsuario['Senha']) < 60) {
            $dadosUsuario['Senha'] = password_hash($dadosUsuario['Senha'], PASSWORD_DEFAULT);
        }
        $dadosFiltrados = $this->filterCamposValidos($dadosUsuario);
        return $this->criar($dadosFiltrados);
    }

    // CADASTRO DE ALUNO
    /**
     * @param array $dadosUsuario
     * @param string $matricula
     * @return int ID do usuário criado
     */
    public function cadastrarAluno($dadosUsuario, $matricula)
    {
        try {
            $this->pdo->beginTransaction();
            // 1. Cadastra na tabela Usuarios
            if (empty($dadosUsuario['Senha'])) {
                // Gera senha temporária segura (12 chars) se não veio do formulário
                $dadosUsuario['Senha'] = bin2hex(random_bytes(6));
            }
            $idUsuario = $this->inserirUsuario($dadosUsuario);
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
    /**
     * @return array<int, array<string,mixed>>
     */
    public function listarAlunos($pagina = 1, $limite = 10, $filtro = '')
    {
        try {
            $offset = ($pagina - 1) * $limite;
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
    /**
     * @return int
     */
    public function countAlunos($filtro = '')
    {
        try {
            $sql = "SELECT COUNT(u.ID_Usuario) 
                    FROM Usuarios u 
                    INNER JOIN Alunos a ON u.ID_Usuario = a.ID_Aluno
                    WHERE u.Ativo = 1";
            
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
    /**
     * @param array $dadosUsuario
     * @return int ID do professor criado
     */
    public function cadastrarProfessor($dadosUsuario, $formacaoAcademica, $dataAdmissao, $areaAtuacao = null, $matriculaProfessor = null)
    {
        try {
            $this->pdo->beginTransaction();
            // Inserir na tabela Usuarios (dinâmico)
            if (empty($dadosUsuario['Login']) && !empty($dadosUsuario['Email'])) {
                $dadosUsuario['Login'] = $dadosUsuario['Email'];
            }
            $idUsuario = $this->inserirUsuario($dadosUsuario);

            // Inserir na tabela Professor (inclui Matricula quando fornecida)
            $sqlProfessor = "INSERT INTO Professores (ID_Professor, Formacao, Data_Ingresso, Area_Atuacao, Matricula) 
                        VALUES (?, ?, ?, ?, ?)";

            $stmtProfessor = $this->pdo->prepare($sqlProfessor);
            $stmtProfessor->execute([
                $idUsuario,
                $formacaoAcademica,
                $dataAdmissao,
                $areaAtuacao,
                $matriculaProfessor
            ]);

            $this->pdo->commit();
            return $idUsuario;

        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    // ATUALIZAR PROFESSOR
    /**
     * @return void
     */
    public function atualizarProfessor($idProfessor, $dadosUsuario, $formacaoAcademica, $dataAdmissao, $areaAtuacao = null, $matriculaProfessor = null)
    {
        try {
            $this->pdo->beginTransaction();
            // Atualizar tabela Usuarios via BaseCRUD
            if (empty($dadosUsuario['Login']) && !empty($dadosUsuario['Email'])) {
                $dadosUsuario['Login'] = $dadosUsuario['Email'];
            }
            // Normaliza documentos e telefones
            $dadosUsuario = $this->sanitizeUsuarioFields($dadosUsuario);
            if (!empty($dadosUsuario['Senha']) && strlen($dadosUsuario['Senha']) < 60) {
                $dadosUsuario['Senha'] = password_hash($dadosUsuario['Senha'], PASSWORD_DEFAULT);
            }
            $dadosFiltrados = $this->filterCamposValidos($dadosUsuario);
            if (!empty($dadosFiltrados)) {
                $this->atualizar($idProfessor, $dadosFiltrados);
            }

            // Atualizar tabela Professores
            $sqlProfessor = "UPDATE Professores SET 
                        Formacao = ?, Data_Ingresso = ?, Area_Atuacao = ?, Matricula = ?
                        WHERE ID_Professor = ?";

            $stmtProfessor = $this->pdo->prepare($sqlProfessor);
            $stmtProfessor->execute([
                $formacaoAcademica,
                $dataAdmissao,
                $areaAtuacao,
                $matriculaProfessor,
                $idProfessor
            ]);

            $this->pdo->commit();

        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    // ATUALIZAR ALUNO
    /**
     * @return int ID do usuário atualizado
     */
    public function atualizarAluno($idUsuario, $dadosUsuario, $matricula)
    {
        try {
            $this->pdo->beginTransaction();

            // Atualiza a tabela Usuarios (assumindo que o método `atualizar` existe na BaseCRUD)
            // Normaliza documentos e telefones
            $dadosUsuario = $this->sanitizeUsuarioFields($dadosUsuario);
            if (isset($dadosUsuario['Senha']) && !empty($dadosUsuario['Senha']) && strlen($dadosUsuario['Senha']) < 60) {
                $dadosUsuario['Senha'] = password_hash($dadosUsuario['Senha'], PASSWORD_DEFAULT);
            }
            $dadosFiltrados = $this->filterCamposValidos($dadosUsuario);
            if (!empty($dadosFiltrados)) {
                $this->atualizar($idUsuario, $dadosFiltrados);
            }

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
    /**
     * @return array|false
     */
    public function buscarAlunoCompleto($idAluno)
    {
        try {
            // Extrai cidade e UF de u.Naturalidade no formato "Cidade/UF" se existir
            $sql = "
                SELECT 
                    u.*, 
                    a.Matricula,
                    m.codigo_ibge AS naturalidade_id,
                    e.codigo_uf AS uf_naturalidade
                FROM Usuarios u
                INNER JOIN Alunos a ON u.ID_Usuario = a.ID_Aluno
                LEFT JOIN (
                    SELECT codigo_ibge, nome, codigo_uf FROM municipios
                ) m ON m.nome = SUBSTRING_INDEX(u.Naturalidade, '/', 1)
                LEFT JOIN (
                    SELECT codigo_uf, uf FROM estados
                ) e ON e.uf = NULLIF(SUBSTRING_INDEX(u.Naturalidade, '/', -1), u.Naturalidade)
                    AND (m.codigo_uf IS NULL OR m.codigo_uf = e.codigo_uf)
                WHERE u.ID_Usuario = ?
            ";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$idAluno]);
            $aluno = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($aluno) {
                // Carregar NEE mais recente e mapear para campos esperados pelo formulário
                try {
                    $neeStmt = $this->pdo->prepare("SELECT Descricao, Acompanhamento_Especializado FROM Acompanhamento_NEE WHERE ID_Aluno = ? AND Tipo = 'NEE' ORDER BY ID_Acompanhamento DESC LIMIT 1");
                    $neeStmt->execute([$idAluno]);
                    if ($row = $neeStmt->fetch(PDO::FETCH_ASSOC)) {
                        $payload = json_decode((string)$row['Acompanhamento_Especializado'], true) ?: [];
                        $aluno['AEE'] = !empty($payload['aee']) ? 1 : 0;
                        $aluno['Sala_AEE'] = !empty($payload['salaAee']) ? 1 : 0;
                        $aluno['Monitor'] = !empty($payload['monitor']) ? 1 : 0;
                        $aluno['Interprete_Libras'] = !empty($payload['interprete']) ? 1 : 0;
                        $aluno['Material_Adaptado'] = !empty($payload['materialAdaptado']) ? 1 : 0;
                        $aluno['Tecnologia_Assistiva'] = !empty($payload['tecnologiaAssistiva']) ? 1 : 0;
                        $aluno['Outras_Necessidades'] = isset($row['Descricao']) ? (string)$row['Descricao'] : '';
                    }
                } catch (Exception $e) {
                    // silencioso, segue sem NEE
                }
            }

            return $aluno;
        } catch (PDOException $e) {
            throw new Exception("Erro ao buscar aluno: " . $e->getMessage());
        }
    }

    // BUSCAR PROFESSOR COMPLETO
    /**
     * @return array|false
     */
    public function buscarProfessorCompleto($idProfessor)
    {
        $temMatricula = $this->hasColumn('Professores', 'Matricula');
        $select = $temMatricula
            ? "SELECT u.*, p.Formacao, p.Data_Ingresso, p.Area_Atuacao, p.Matricula"
            : "SELECT u.*, p.Formacao, p.Data_Ingresso, p.Area_Atuacao";

        // Deriva naturalidade_id e uf_naturalidade a partir do texto em u.Naturalidade
        $sql = $select . ", m.codigo_ibge AS naturalidade_id, e.codigo_uf AS uf_naturalidade
            FROM Usuarios u 
            INNER JOIN Professores p ON u.ID_Usuario = p.ID_Professor 
            LEFT JOIN (
                SELECT codigo_ibge, nome, codigo_uf FROM municipios
            ) m ON m.nome = SUBSTRING_INDEX(u.Naturalidade, '/', 1)
            LEFT JOIN (
                SELECT codigo_uf, uf FROM estados
            ) e ON e.uf = NULLIF(SUBSTRING_INDEX(u.Naturalidade, '/', -1), u.Naturalidade)
                AND (m.codigo_uf IS NULL OR m.codigo_uf = e.codigo_uf)
            WHERE u.ID_Usuario = ?";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$idProfessor]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Salvar Necessidades Especiais do Aluno (Acompanhamento_NEE)
    public function salvarNeeAluno(int $idAluno, array $nee): void
    {
        if (!$idAluno) return;
        $this->pdo->beginTransaction();
        try {
            $del = $this->pdo->prepare("DELETE FROM Acompanhamento_NEE WHERE ID_Aluno = ?");
            $del->execute([$idAluno]);

            if (!empty($nee['possui'])) {
                $descricao = isset($nee['outras']) ? trim((string)$nee['outras']) : null;
                $payload = [
                    'aee' => !empty($nee['aee']) ? 1 : 0,
                    'salaAee' => !empty($nee['salaAee']) ? 1 : 0,
                    'monitor' => !empty($nee['monitor']) ? 1 : 0,
                    'interprete' => !empty($nee['interprete']) ? 1 : 0,
                    'materialAdaptado' => !empty($nee['materialAdaptado']) ? 1 : 0,
                    'tecnologiaAssistiva' => !empty($nee['tecnologiaAssistiva']) ? 1 : 0,
                ];
                $json = json_encode($payload, JSON_UNESCAPED_UNICODE);
                $ins = $this->pdo->prepare("INSERT INTO Acompanhamento_NEE (ID_Acompanhamento, ID_Aluno, Tipo, Descricao, Acompanhamento_Especializado) VALUES (NULL, ?, 'NEE', ?, ?)");
                $ins->execute([$idAluno, $descricao, $json]);
            }
            $this->pdo->commit();
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log('Erro salvar NEE: ' . $e->getMessage());
        }
    }



    // LISTAR PROFESSORES COM PAGINAÇÃO
    /**
     * @return array<int, array<string,mixed>>
     */
    public function listarProfessores($pagina = 1, $limite = 10)
    {
        $offset = ($pagina - 1) * $limite;
        $temMatricula = $this->hasColumn('Professores', 'Matricula');
        $select = $temMatricula
            ? "SELECT u.*, p.Formacao AS Formacao_Academica, p.Data_Ingresso, p.Area_Atuacao, p.Matricula"
            : "SELECT u.*, p.Formacao AS Formacao_Academica, p.Data_Ingresso, p.Area_Atuacao";

        $sql = $select . "
            FROM Usuarios u 
            INNER JOIN Professores p ON u.ID_Usuario = p.ID_Professor 
            WHERE u.Ativo = 1
            ORDER BY u.Nome_Completo
            LIMIT :limite OFFSET :offset";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limite', (int) $limite, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // CONTAR TOTAL DE PROFESSORES
    /**
     * @return int
     */
    public function countProfessores()
    {
    $sql = "SELECT COUNT(u.ID_Usuario) 
        FROM Usuarios u 
        INNER JOIN Professores p ON u.ID_Usuario = p.ID_Professor
        WHERE u.Ativo = 1";
        
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchColumn();
    }

    // VERIFICAR SE EMAIL JÁ EXISTE
    /**
     * @return bool
     */
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
    /**
     * @return bool
     */
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
    /**
     * @return array|false
     */
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