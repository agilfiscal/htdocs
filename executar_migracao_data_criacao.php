<?php
/**
 * Script para executar a migração da data_criacao na tabela auditoria_notas_vencimentos
 * Execute este script uma vez para adicionar o campo data_criacao
 */

// Configurações do banco de dados
require_once 'config/database.php';

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

    echo "Iniciando migração da data_criacao na tabela auditoria_notas_vencimentos...\n";

    // Verificar se a coluna já existe
    $stmt = $pdo->query("SHOW COLUMNS FROM auditoria_notas_vencimentos LIKE 'data_criacao'");
    $colunaExiste = $stmt->fetch();
    
    if (!$colunaExiste) {
        echo "Adicionando campo data_criacao...\n";
        $pdo->exec("ALTER TABLE auditoria_notas_vencimentos ADD COLUMN data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP");
        echo "Campo data_criacao adicionado com sucesso!\n";
        
        // Atualizar registros existentes com a data atual (já que não temos a data real de criação)
        echo "Atualizando registros existentes...\n";
        $pdo->exec("UPDATE auditoria_notas_vencimentos SET data_criacao = NOW() WHERE data_criacao IS NULL");
        echo "Registros existentes atualizados!\n";
    } else {
        echo "Campo data_criacao já existe na tabela.\n";
    }

    echo "Migração concluída com sucesso!\n";

} catch (Exception $e) {
    echo "Erro durante a migração: " . $e->getMessage() . "\n";
    exit(1);
}
