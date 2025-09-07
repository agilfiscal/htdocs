-- Adiciona campos para controle de matriz/filial
ALTER TABLE empresas
ADD COLUMN tipo_empresa ENUM('matriz', 'filial', 'independente') DEFAULT 'independente' AFTER representante_legal,
ADD COLUMN empresa_matriz_id INT NULL AFTER tipo_empresa,
ADD FOREIGN KEY (empresa_matriz_id) REFERENCES empresas(id) ON DELETE SET NULL; 