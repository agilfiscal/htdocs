CREATE TABLE IF NOT EXISTS destino (
    id INT AUTO_INCREMENT PRIMARY KEY,
    destino VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO destino (destino) VALUES
('Venda'),
('Uso e Consumo ME'),
('Uso e Cosnumo SME'),
('Embalagens ME'),
('Embalagens SME'),
('Bonificação ME'),
('Bonificação SME'),
('Troca ME'),
('Troca SME'),
('Comodato ME'),
('Comodato SME'),
('Alteração ME'),
('Alteração SME'),
('Ativo Mobilizado ME'),
('Ativo Mobilizado SME'),
('Materia Prima ME'),
('Materia Prima SME'),
('Insumo ME'),
('Insumo SME'),
('Combustível SME'),
('Amostra Grátis ME'),
('Amostra grátis SME'); 