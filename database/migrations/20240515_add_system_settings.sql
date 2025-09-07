-- Configurações de Segurança
INSERT INTO settings (`key`, value) VALUES
('password_min_length', '8'),
('password_expiry', '90'),
('password_require_special', '1'),
('password_require_numbers', '1'),
('session_timeout', '30'),
('enable_2fa', '0'),
('allowed_ips', '[]');

-- Configurações de Notificação
INSERT INTO settings (`key`, value) VALUES
('notification_channels', '["email"]'),
('alert_types', '["system","security","business"]');

-- Configurações de Agenda
INSERT INTO settings (`key`, value) VALUES
('business_hours', '{"start":"09:00","end":"18:00"}'),
('holidays', '[]');

-- Configurações Financeiras
INSERT INTO settings (`key`, value) VALUES
('billing_cycle', 'monthly'),
('payment_methods', '["credit","boleto","pix"]'),
('invoice_series', ''),
('invoice_number', '1');

-- Configurações de Integração
INSERT INTO settings (`key`, value) VALUES
('api_keys', '{"main":"","secret":""}'),
('webhooks', '{"url":"","secret":""}');

-- Configurações do Sistema
INSERT INTO settings (`key`, value) VALUES
('theme', 'light'),
('date_format', 'd/m/Y'),
('time_format', 'H:i'),
('max_file_size', '10'),
('max_users', '10'),
('backup_frequency', 'daily'); 