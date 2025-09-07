-- Criar tabela de insumos
CREATE TABLE IF NOT EXISTS `insumos` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `insumo` varchar(255) NOT NULL,
    `medida` varchar(50) NOT NULL,
    `custo` decimal(10,2) NOT NULL DEFAULT 0.00,
    `usuario_id` int(11) unsigned NOT NULL,
    `created_at` datetime DEFAULT NULL,
    `updated_at` datetime DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `usuario_id` (`usuario_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Criar tabela de funcionários
CREATE TABLE IF NOT EXISTS `funcionarios` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `profissao` varchar(255) NOT NULL,
    `salario` decimal(10,2) NOT NULL DEFAULT 0.00,
    `usuario_id` int(11) unsigned NOT NULL,
    `created_at` datetime DEFAULT NULL,
    `updated_at` datetime DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `usuario_id` (`usuario_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci; 