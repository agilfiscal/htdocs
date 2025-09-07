-- Renomear tabela
RENAME TABLE log_envio_notas TO log_envio_arquivos;

-- Adicionar colunas extras
ALTER TABLE log_envio_arquivos
    ADD COLUMN nome_original VARCHAR(255) DEFAULT NULL AFTER nome_arquivo,
    ADD COLUMN tipo_arquivo VARCHAR(50) DEFAULT NULL AFTER nome_original,
    ADD COLUMN status VARCHAR(20) NOT NULL DEFAULT 'ativo' AFTER tipo_arquivo,
    ADD COLUMN data_exclusao DATETIME DEFAULT NULL AFTER status,
    ADD COLUMN usuario_exclusao_id INT DEFAULT NULL AFTER data_exclusao,
    ADD COLUMN motivo_exclusao VARCHAR(255) DEFAULT NULL AFTER usuario_exclusao_id,
    ADD FOREIGN KEY (usuario_exclusao_id) REFERENCES usuarios(id); 