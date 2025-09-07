CREATE TABLE IF NOT EXISTS cst_icms (
    cst VARCHAR(4) PRIMARY KEY,
    descricao VARCHAR(255) NOT NULL
);

INSERT INTO cst_icms (cst, descricao) VALUES
('00', 'Tributada integralmente'),
('02', 'Tributação monofásica própria sobre combustíveis'),
('10', 'Tributada e com cobrança do ICMS por substituição tributária'),
('12', 'Tributada com ICMS devido por substituição tributária relativo às operações e prestações antecedentes'),
('13', 'Tributada com ICMS devido por substituição tributária relativo às operações e prestações concomitantes'),
('15', 'Tributação monofásica própria e com responsabilidade pela retenção sobre combustíveis'),
('20', 'Com redução de base de cálculo'),
('30', 'Isenta ou não tributada e com cobrança do ICMS por substituição tributária'),
('40', 'Isenta'),
('41', 'Não tributada'),
('50', 'Suspensão'),
('51', 'Diferimento'),
('52', 'Diferimento com ICMS devido por substituição tributária relativo às operações e prestações subsequentes'),
('53', 'Tributação monofásica sobre combustíveis com recolhimento diferido'),
('60', 'ICMS cobrado anteriormente por substituição tributária'),
('61', 'Tributação monofásica sobre combustíveis cobrada anteriormente'),
('70', 'Com redução de base de cálculo e cobrança do ICMS por substituição tributária'),
('72', 'Tributada com redução de base de cálculo e com ICMS devido por substituição tributária relativo às operações e prestações antecedentes'),
('74', 'Tributada com redução de base de cálculo e com ICMS devido por substituição tributária relativo às operações e prestações concomitantes'),
('90', 'Outras'); 