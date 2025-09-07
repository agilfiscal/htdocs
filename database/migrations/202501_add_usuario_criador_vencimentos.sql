-- Adicionar campo para rastrear quem criou o vencimento
ALTER TABLE auditoria_notas_vencimentos 
ADD COLUMN usuario_criador_id INT DEFAULT NULL,
ADD FOREIGN KEY (usuario_criador_id) REFERENCES usuarios(id) ON DELETE SET NULL;

-- Criar tabela para notificações de resolução para operadores
CREATE TABLE IF NOT EXISTS notificacoes_resolucao_operador (
    id INT AUTO_INCREMENT PRIMARY KEY,
    operador_id INT NOT NULL,
    admin_id INT NOT NULL,
    nota_id INT NOT NULL,
    vencimento_id INT NOT NULL,
    mensagem TEXT DEFAULT 'Sua solicitação de escrituração foi feita pelo usuário.',
    data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP,
    data_visualizacao DATETIME NULL,
    visualizada TINYINT(1) DEFAULT 0,
    FOREIGN KEY (operador_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (admin_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (nota_id) REFERENCES auditoria_notas(id) ON DELETE CASCADE,
    FOREIGN KEY (vencimento_id) REFERENCES auditoria_notas_vencimentos(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
