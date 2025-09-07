CREATE TABLE arius_financeiro (
    id INT AUTO_INCREMENT PRIMARY KEY,
    empresa_id INT NOT NULL,
    numero_finan VARCHAR(50),
    nf VARCHAR(50),
    valor DECIMAL(15,2),
    hash VARCHAR(100) GENERATED ALWAYS AS (CONCAT(COALESCE(nf, ''), '-', COALESCE(valor, ''))) STORED,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_empresa_id (empresa_id),
    INDEX idx_hash (hash),
    INDEX idx_nf (nf)
); 