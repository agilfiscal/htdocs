CREATE TABLE IF NOT EXISTS auditoria_notas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    empresa_id INT NOT NULL,
    numero VARCHAR(100),
    cnpj VARCHAR(20),
    razao_social VARCHAR(255),
    data_emissao DATETIME,
    valor DECIMAL(15,2),
    chave VARCHAR(60),
    uf VARCHAR(2),
    status VARCHAR(50),
    tipo VARCHAR(20),
    data_consulta DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY (chave, empresa_id)
); 