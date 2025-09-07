-- Criar tabela de embalagens
CREATE TABLE IF NOT EXISTS `embalagens` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `embalagem` varchar(255) NOT NULL,
    `quantidade` int(11) NOT NULL DEFAULT 1,
    `volume` int(11) NOT NULL DEFAULT 1,
    `custo` decimal(10,2) NOT NULL DEFAULT 0.00,
    `usuario_id` int(11) unsigned NOT NULL,
    `created_at` datetime DEFAULT NULL,
    `updated_at` datetime DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `usuario_id` (`usuario_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci; 