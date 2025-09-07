CREATE TABLE IF NOT EXISTS `empresa_fornecedor` (
  `empresa_id` int(11) NOT NULL,
  `fornecedor_id` int(11) NOT NULL,
  `codigo_interno` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`empresa_id`,`fornecedor_id`),
  UNIQUE KEY `empresa_id_codigo_interno_unique` (`empresa_id`, `codigo_interno`),
  KEY `fornecedor_id` (`fornecedor_id`),
  CONSTRAINT `empresa_fornecedor_ibfk_1` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `empresa_fornecedor_ibfk_2` FOREIGN KEY (`fornecedor_id`) REFERENCES `fornecedores` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci; 