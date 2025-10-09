<?php
class LocalidadeCRUD {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function listarEstados() {
        $sql = "SELECT codigo_uf as id, nome FROM estados ORDER BY nome";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function listarMunicipiosPorEstado($estado_id) {
        $sql = "SELECT codigo_ibge as id, nome FROM municipios WHERE codigo_uf = ? ORDER BY nome";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$estado_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function listarPaises() {
    $sql = "SELECT codigo AS id, nome FROM PAIS ORDER BY nome";
    $stmt = $this->pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    
    public function listarOrgaosExpedidores() {
        $sql = "SELECT id, sigla, nome FROM orgaos_expedidores ORDER BY nome";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function buscarMunicipio($municipio_id) {
        $sql = "SELECT * FROM municipios WHERE codigo_ibge = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$municipio_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>