<?php
require_once __DIR__ . '/app/Models/Empresa.php';
require_once __DIR__ . '/app/Models/AuditoriaNota.php';
require_once __DIR__ . '/app/Controllers/LogConsultaController.php';

// Configuração do banco (ajuste conforme seu ambiente)
$db = new PDO(
    'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
    DB_USER,
    DB_PASS,
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]
);

$empresaModel = new \App\Models\Empresa($db);
$auditoriaNotaModel = new \App\Models\AuditoriaNota($db);
$controller = new \App\Controllers\LogConsultaController();

$empresas = $empresaModel->all();

foreach ($empresas as $empresa) {
    // Pula empresas sem certificado
    if (empty($empresa['certificado_path']) || empty($empresa['certificado_senha'])) {
        continue;
    }

    // Conversão de sigla para código numérico da UF
    $ufs = [
        'RO' => '11', 'AC' => '12', 'AM' => '13', 'RR' => '14', 'PA' => '15', 'AP' => '16', 'TO' => '17',
        'MA' => '21', 'PI' => '22', 'CE' => '23', 'RN' => '24', 'PB' => '25', 'PE' => '26', 'AL' => '27', 'SE' => '28', 'BA' => '29',
        'MG' => '31', 'ES' => '32', 'RJ' => '33', 'SP' => '35',
        'PR' => '41', 'SC' => '42', 'RS' => '43',
        'MS' => '50', 'MT' => '51', 'GO' => '52', 'DF' => '53'
    ];
    $codigoUf = $ufs[$empresa['uf']] ?? '';
    if (empty($codigoUf)) {
        continue;
    }

    // Atualiza o .env
    $envContent = "CERTIFICADO_PFX={$empresa['certificado_path']}\n";
    $envContent .= "CERTIFICADO_SENHA={$empresa['certificado_senha']}\n";
    $envContent .= "CNPJ={$empresa['cnpj']}\n";
    $envContent .= "UF_AUTOR={$codigoUf}\n";
    $envContent .= "AMBIENTE=1\n";
    file_put_contents(__DIR__ . '/.env', $envContent);

    // Executa o Node.js
    $command = 'cd ' . __DIR__ . ' && npm start';
    $output = [];
    $returnVar = 0;
    exec($command . " 2>&1", $output, $returnVar);

    // Importa o resultado
    $controller->importarResultadoMDE($empresa['id']);
} 