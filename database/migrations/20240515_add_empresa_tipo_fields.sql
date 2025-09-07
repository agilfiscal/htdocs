-- Adiciona campos para controle de matriz/filial
ALTER TABLE empresas
ADD COLUMN tipo_empresa VARCHAR(20) DEFAULT 'independente' AFTER representante_legal,
ADD COLUMN empresa_matriz_id INT NULL AFTER tipo_empresa;

-- Atualiza o tipo da coluna para ENUM
ALTER TABLE empresas
MODIFY COLUMN tipo_empresa ENUM('matriz', 'filial', 'independente') DEFAULT 'independente';

-- Adiciona a foreign key
ALTER TABLE empresas
ADD CONSTRAINT fk_empresa_matriz 
FOREIGN KEY (empresa_matriz_id) 
REFERENCES empresas(id) 
ON DELETE SET NULL; 