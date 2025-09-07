-- Adiciona campos de certificado na tabela empresas
ALTER TABLE empresas
ADD COLUMN certificado_path VARCHAR(255) NULL AFTER representante_legal,
ADD COLUMN certificado_senha VARCHAR(255) NULL AFTER certificado_path; 