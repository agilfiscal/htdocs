CREATE TABLE IF NOT EXISTS equivalencia_cst_icms_csosn (
    csosn VARCHAR(4) NOT NULL,
    cst_icms VARCHAR(4) NOT NULL,
    PRIMARY KEY (csosn, cst_icms)
);

-- 101: 00, 20
INSERT INTO equivalencia_cst_icms_csosn (csosn, cst_icms) VALUES
('101', '00'),
('101', '20');

-- 102: 40, 41, 50, 51
INSERT INTO equivalencia_cst_icms_csosn (csosn, cst_icms) VALUES
('102', '40'),
('102', '41'),
('102', '50'),
('102', '51');

-- 500: 60
INSERT INTO equivalencia_cst_icms_csosn (csosn, cst_icms) VALUES
('500', '60'); 