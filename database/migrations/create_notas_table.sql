CREATE TABLE IF NOT EXISTS notas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    empresa_id INT NOT NULL,
    codigo_processo VARCHAR(50) NOT NULL,
    codigo_fiscal VARCHAR(50) NOT NULL,
    valor_nota DECIMAL(15,2) NOT NULL,
    numero_nota VARCHAR(50) NOT NULL,
    chave_nota VARCHAR(44) NOT NULL,
    coleta VARCHAR(100),
    escriturador VARCHAR(100),
    data_entrada DATETIME,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (empresa_id) REFERENCES empresas(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci; 