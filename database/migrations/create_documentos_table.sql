CREATE TABLE IF NOT EXISTS documentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    empresa_id INT NOT NULL,
    nome_arquivo VARCHAR(255) NOT NULL,
    tipo_documento VARCHAR(50) NOT NULL,
    descricao TEXT,
    caminho_arquivo VARCHAR(255) NOT NULL,
    tamanho BIGINT NOT NULL,
    status ENUM('pendente', 'processado', 'erro') DEFAULT 'pendente',
    data_upload DATETIME DEFAULT CURRENT_TIMESTAMP,
    data_processamento DATETIME,
    observacoes TEXT,
    sped_periodo VARCHAR(20),
    FOREIGN KEY (empresa_id) REFERENCES empresas(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci; 