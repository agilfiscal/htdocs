CREATE TABLE IF NOT EXISTS observacoes_nota (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nota_id INT NOT NULL,
    usuario_id INT NOT NULL,
    observacao TEXT(5000),
    data_hora DATETIME DEFAULT CURRENT_TIMESTAMP,
    tipo_usuario VARCHAR(20) NOT NULL,
    FOREIGN KEY (nota_id) REFERENCES auditoria_notas(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; 