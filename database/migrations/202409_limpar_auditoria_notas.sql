-- Desativa verificação de chaves estrangeiras
SET FOREIGN_KEY_CHECKS=0;

-- Limpa as tabelas dependentes primeiro
DELETE FROM auditoria_destino;
DELETE FROM auditoria_notas_vencimentos;
DELETE FROM observacoes_nota;

-- Limpa a tabela principal
DELETE FROM auditoria_notas;

-- Reseta os auto_increment
ALTER TABLE auditoria_notas AUTO_INCREMENT = 1;
ALTER TABLE auditoria_destino AUTO_INCREMENT = 1;
ALTER TABLE auditoria_notas_vencimentos AUTO_INCREMENT = 1;
ALTER TABLE observacoes_nota AUTO_INCREMENT = 1;

-- Reativa verificação de chaves estrangeiras
SET FOREIGN_KEY_CHECKS=1; 

truncate table notas;
truncate table desconhecimento;
truncate table romaneio;
truncate table fornecedores;