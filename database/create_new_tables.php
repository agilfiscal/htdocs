<?php
require_once __DIR__ . '/../config/config.php';

try {
    // Conectar ao MySQL
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    // Ler o arquivo SQL
    $sql = file_get_contents(__DIR__ . '/migrations/create_insumos_funcionarios_tables.sql');
    
    // Executar as queries
    $pdo->exec($sql);
    
    echo "Tabelas de insumos e funcionários criadas com sucesso!\n";
} catch (PDOException $e) {
    die("Erro ao executar migrações: " . $e->getMessage() . "\n");
} 