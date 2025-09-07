CREATE TABLE IF NOT EXISTS log_envio_notas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    empresa_id INT NOT NULL,
    usuario_id INT DEFAULT NULL,
    data_envio DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    quantidade_notas INT NOT NULL,
    nome_arquivo VARCHAR(255) DEFAULT NULL,
    observacao VARCHAR(255) DEFAULT NULL,
    FOREIGN KEY (empresa_id) REFERENCES empresas(id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; 