<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('log_errors', 0); // Não tenta logar, só exibe
error_reporting(E_ALL);

require_once __DIR__ . '/../config/config.php';

echo "<h2>Debug PHP</h2>";

try {
    // Teste conexão com o banco
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
    echo "<p>Conexão com o banco: <b>OK</b></p>";
} catch (Exception $e) {
    echo "<p style='color:red'>Erro ao conectar no banco: " . $e->getMessage() . "</p>";
}

// Teste leitura de arquivo
$cssPath = __DIR__ . '/assets/css/main.css';
if (file_exists($cssPath)) {
    echo "<p>Arquivo CSS encontrado: <b>OK</b></p>";
} else {
    echo "<p style='color:red'>Arquivo CSS NÃO encontrado em $cssPath</p>";
}

// Teste escrita de arquivo
$testFile = __DIR__ . '/debug_test.txt';
if (file_put_contents($testFile, 'debug test: ' . date('Y-m-d H:i:s')) !== false) {
    echo "<p>Permissão de escrita: <b>OK</b></p>";
    unlink($testFile);
} else {
    echo "<p style='color:red'>Sem permissão de escrita na pasta raiz!</p>";
}

// Teste sessão
session_start();
$_SESSION['debug'] = 'ok';
if (isset($_SESSION['debug'])) {
    echo "<p>Sessão PHP: <b>OK</b></p>";
} else {
    echo "<p style='color:red'>Problema com sessão PHP!</p>";
}

echo "<hr><b>Se você quiser testar um trecho de código específico, me envie que eu coloco aqui!</b>";
?> 