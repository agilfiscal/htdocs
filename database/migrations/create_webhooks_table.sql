CREATE TABLE IF NOT EXISTS webhooks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    url VARCHAR(255) NOT NULL,
    eventos TEXT NOT NULL,
    secret VARCHAR(255),
    status ENUM('ativo', 'inativo') NOT NULL DEFAULT 'ativo',
    ultimo_envio DATETIME,
    created_at DATETIME NOT NULL,
    updated_at DATETIME
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci; 