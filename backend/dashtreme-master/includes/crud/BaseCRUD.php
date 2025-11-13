<?php
class BaseCRUD {
    protected $pdo;
    protected $tableName;
    protected $primaryKey;
    
    public function __construct($pdo, $tableName, $primaryKey = null) {
        $this->pdo = $pdo;
        $this->tableName = $tableName;
        $this->primaryKey = $primaryKey ?: 'ID_' . $tableName;
    }
    
    // criar um registro novo
    public function criar($dados) {
        try {
            $campos = implode(', ', array_keys($dados));
            $placeholders = ':' . implode(', :', array_keys($dados));
            
            $sql = "INSERT INTO {$this->tableName} ($campos) VALUES ($placeholders)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($dados);
            
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            throw new Exception("Erro ao criar registro em {$this->tableName}: " . $e->getMessage());
        }
    }
    
    // pegar 1 registro pelo id
    public function buscarPorId($id) {
        try {
            $sql = "SELECT * FROM {$this->tableName} WHERE {$this->primaryKey} = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erro ao buscar registro em {$this->tableName}: " . $e->getMessage());
        }
    }
    
    // listar todos (pode filtrar/ordenar se passar params)
    public function listar($condicao = '', $params = [], $ordenacao = '') {
        try {
            $sql = "SELECT * FROM {$this->tableName}";
            
            if ($condicao) {
                $sql .= " WHERE $condicao";
            }
            
            if ($ordenacao) {
                $sql .= " ORDER BY $ordenacao";
            }
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erro ao listar registros de {$this->tableName}: " . $e->getMessage());
        }
    }
    
    // buscar tudo que bate com um campo
    public function buscarPorCampo($campo, $valor) {
        try {
            $sql = "SELECT * FROM {$this->tableName} WHERE $campo = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$valor]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erro ao buscar por $campo em {$this->tableName}: " . $e->getMessage());
        }
    }
    
    // atualizar campos de um registro
    public function atualizar($id, $dados) {
        try {
            $setClause = [];
            foreach ($dados as $campo => $valor) {
                $setClause[] = "$campo = :$campo";
            }
            $setClause = implode(', ', $setClause);
            
            $sql = "UPDATE {$this->tableName} SET $setClause WHERE {$this->primaryKey} = :id";
            $dados['id'] = $id;
            
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($dados);
        } catch (PDOException $e) {
            throw new Exception("Erro ao atualizar registro em {$this->tableName}: " . $e->getMessage());
        }
    }
    
    // excluir (se tiver campo Ativo faz remoção lógica)
    public function excluir($id) {
        try {
            $stmt = $this->pdo->prepare("SHOW COLUMNS FROM {$this->tableName} LIKE 'Ativo'");
            $stmt->execute();
            $temCampoAtivo = $stmt->fetch();
            
            if ($temCampoAtivo) {
                $sql = "UPDATE {$this->tableName} SET Ativo = 0 WHERE {$this->primaryKey} = ?";
            } else {
                $sql = "DELETE FROM {$this->tableName} WHERE {$this->primaryKey} = ?";
            }
            
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            throw new Exception("Erro ao excluir registro de {$this->tableName}: " . $e->getMessage());
        }
    }
    
    // contar quantos registros tem
    public function contar($condicao = '', $params = []) {
        try {
            $sql = "SELECT COUNT(*) as total FROM {$this->tableName}";
            
            if ($condicao) {
                $sql .= " WHERE $condicao";
            }
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'];
        } catch (PDOException $e) {
            throw new Exception("Erro ao contar registros de {$this->tableName}: " . $e->getMessage());
        }
    }
    
    // checa se existe pelo id
    public function existe($id) {
        try {
            $sql = "SELECT 1 FROM {$this->tableName} WHERE {$this->primaryKey} = ? LIMIT 1";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);
            
            return $stmt->fetch() !== false;
        } catch (PDOException $e) {
            throw new Exception("Erro ao verificar existência em {$this->tableName}: " . $e->getMessage());
        }
    }
}
?>