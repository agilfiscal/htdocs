<?php
// Script isolado para testar a validação da senha do certificado PFX
// Uso: teste_certificado.php?arquivo=caminho/para/certificado.pfx&senha=senha

if (!extension_loaded('openssl')) {
    die('A extensão OpenSSL não está carregada.');
}

$arquivo = $_GET['arquivo'] ?? '';
$senha = $_GET['senha'] ?? '';

if (empty($arquivo) || empty($senha)) {
    die('Parâmetros arquivo e senha são obrigatórios.');
}

if (!file_exists($arquivo)) {
    die('Arquivo não encontrado: ' . $arquivo);
}

$pkcs12 = file_get_contents($arquivo);
if ($pkcs12 === false) {
    die('Erro ao ler o arquivo.');
}

$certs = [];
$ok = openssl_pkcs12_read($pkcs12, $certs, $senha);

if ($ok) {
    echo 'Certificado validado com sucesso!';
} else {
    echo 'Erro ao validar certificado: ' . openssl_error_string();
} 