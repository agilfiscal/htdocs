CREATE TABLE IF NOT EXISTS auditoria_destino (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nota_id INT NOT NULL,
    destino_id INT NOT NULL,
    FOREIGN KEY (nota_id) REFERENCES auditoria_notas(id) ON DELETE CASCADE,
    FOREIGN KEY (destino_id) REFERENCES destino(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; 