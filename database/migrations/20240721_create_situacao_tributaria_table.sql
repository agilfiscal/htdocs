CREATE TABLE IF NOT EXISTS situacao_tributaria (
    cst_icms VARCHAR(4) PRIMARY KEY,
    situacao_tributaria VARCHAR(64) NOT NULL
);

INSERT INTO situacao_tributaria (cst_icms, situacao_tributaria) VALUES
('00', 'Tributado'),
('40', 'Isento'),
('60', 'Substituido'),
('41', 'Não Tributado'),
('51', 'Diferimento'),
('50', 'Suspensão'),
('90', 'Outras'),
('20', 'Redução'),
('02', 'Monofásico'),
('10', 'Tributado com substituição'); 