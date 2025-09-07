-- Adicionar campo data_criacao na tabela auditoria_notas_vencimentos
ALTER TABLE auditoria_notas_vencimentos 
ADD COLUMN data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP;
