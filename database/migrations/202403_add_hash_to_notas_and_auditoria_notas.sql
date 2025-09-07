-- Adiciona coluna hash na tabela notas
ALTER TABLE notas ADD COLUMN hash VARCHAR(100) NULL AFTER chave_nota;

-- Adiciona coluna hash na tabela auditoria_notas
ALTER TABLE auditoria_notas ADD COLUMN hash VARCHAR(100) NULL AFTER chave;

-- Atualiza os dados existentes na tabela notas
UPDATE notas 
SET hash = CONCAT(numero_nota, '-', valor_nota)
WHERE numero_nota IS NOT NULL AND valor_nota IS NOT NULL;

-- Atualiza os dados existentes na tabela auditoria_notas
UPDATE auditoria_notas 
SET hash = CONCAT(numero, '-', valor)
WHERE numero IS NOT NULL AND valor IS NOT NULL;

-- Adiciona índices para melhorar a performance das consultas
ALTER TABLE notas ADD INDEX idx_hash (hash);
ALTER TABLE auditoria_notas ADD INDEX idx_hash (hash); 