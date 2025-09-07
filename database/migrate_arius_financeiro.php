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
    
    // Ler o arquivo SQL da migração
    $sql = file_get_contents(__DIR__ . '/migrations/202501_create_arius_financeiro_table.sql');
    
    // Executar a query
    $pdo->exec($sql);
    
    echo "Tabela arius_financeiro criada com sucesso!\n";
} catch (PDOException $e) {
    die("Erro ao executar migração: " . $e->getMessage() . "\n");
} 