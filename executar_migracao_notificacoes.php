<?php
/**
 * Script para executar a migração de notificações
 * Execute este script uma vez para aplicar as mudanças no banco de dados
 */

// Configurações do banco de dados
require_once 'config/config.php';

try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );

    echo "Iniciando migração de notificações...\n";

    // Ler o arquivo de migração
    $migrationFile = 'database/migrations/202501_add_usuario_criador_vencimentos.sql';
    
    if (!file_exists($migrationFile)) {
        throw new Exception("Arquivo de migração não encontrado: $migrationFile");
    }

    $sql = file_get_contents($migrationFile);
    
    // Dividir o SQL em comandos individuais
    $commands = array_filter(array_map('trim', explode(';', $sql)));
    
    foreach ($commands as $command) {
        if (!empty($command)) {
            echo "Executando: " . substr($command, 0, 50) . "...\n";
            $pdo->exec($command);
        }
    }

    echo "Migração executada com sucesso!\n";
    echo "As seguintes mudanças foram aplicadas:\n";
    echo "- Adicionado campo 'usuario_criador_id' na tabela 'auditoria_notas_vencimentos'\n";
    echo "- Criada tabela 'notificacoes_resolucao_operador' para notificações de resolução\n";
    echo "\nAgora o sistema está pronto para notificar operadores quando admins resolverem suas solicitações!\n";

} catch (Exception $e) {
    echo "Erro na migração: " . $e->getMessage() . "\n";
    exit(1);
}
?>
