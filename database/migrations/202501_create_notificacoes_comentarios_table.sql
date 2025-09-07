-- Tabela para notificações de comentários/observações
CREATE TABLE IF NOT EXISTS notificacoes_comentarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    observacao_id INT NOT NULL,
    nota_id INT NOT NULL,
    usuario_origem_id INT NOT NULL, -- Quem fez o comentário
    usuario_destino_id INT NOT NULL, -- Quem deve receber a notificação
    visualizada TINYINT(1) DEFAULT 0,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_visualizacao TIMESTAMP NULL,
    INDEX idx_usuario_destino (usuario_destino_id),
    INDEX idx_visualizada (visualizada),
    INDEX idx_observacao (observacao_id),
    INDEX idx_nota (nota_id),
    FOREIGN KEY (observacao_id) REFERENCES observacoes_nota(id) ON DELETE CASCADE,
    FOREIGN KEY (nota_id) REFERENCES auditoria_notas(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_origem_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_destino_id) REFERENCES usuarios(id) ON DELETE CASCADE
);
