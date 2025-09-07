CREATE TABLE IF NOT EXISTS auditoria_notas_vencimentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nota_id INT NOT NULL,
    data_vencimento DATE NOT NULL,
    FOREIGN KEY (nota_id) REFERENCES auditoria_notas(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; 