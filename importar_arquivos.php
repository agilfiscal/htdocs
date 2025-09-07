<?php
// Uso: php importar_arquivos.php arquivo modelo empresa_id
// Exemplo: php importar_arquivos.php uploads/fornecedores.csv fornecedores 4

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/config.php';

if ($argc < 4) {
    echo "Uso: php importar_arquivos.php <arquivo> <modelo> <empresa_id>\n";
    exit(1);
}

$arquivo = $argv[1];
$modelo = strtolower($argv[2]);
$empresa_id = (int)$argv[3];

if (!file_exists($arquivo)) {
    echo "Arquivo não encontrado: $arquivo\n";
    exit(1);
}

// Conexão PDO
$db = new PDO(
    'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
    DB_USER,
    DB_PASS,
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]
);

function dataAtual() {
    return date('Y-m-d H:i:s');
}

function lerCSV($arquivo) {
    $dados = [];
    if (($handle = fopen($arquivo, 'r')) !== false) {
        while (($row = fgetcsv($handle, 0, ";")) !== false) {
            $dados[] = $row;
        }
        fclose($handle);
    }
    return $dados;
}

function lerTXT($arquivo) {
    $linhas = file($arquivo, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    return array_map(function($l) { return explode(';', $l); }, $linhas);
}

function lerXLS($arquivo) {
    $ext = strtolower(pathinfo($arquivo, PATHINFO_EXTENSION));
    if ($ext === 'csv') return lerCSV($arquivo);
    if ($ext === 'txt') return lerTXT($arquivo);
    // XLS/XLSX via PhpSpreadsheet
    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($arquivo);
    $sheet = $spreadsheet->getActiveSheet();
    return $sheet->toArray();
}

$ext = strtolower(pathinfo($arquivo, PATHINFO_EXTENSION));
if (in_array($ext, ['csv', 'txt'])) {
    $linhas = $ext === 'csv' ? lerCSV($arquivo) : lerTXT($arquivo);
} elseif (in_array($ext, ['xls', 'xlsx'])) {
    $linhas = lerXLS($arquivo);
} else {
    echo "Tipo de arquivo não suportado para importação em lote.\n";
    exit(1);
}

// Remove cabeçalho se existir
if (in_array($modelo, ['fornecedores', 'notas'])) {
    $cabecalho = array_map('strtolower', $linhas[0]);
    if (
        ($modelo === 'fornecedores' && strpos(implode(',', $cabecalho), 'razao') !== false) ||
        ($modelo === 'notas' && strpos(implode(',', $cabecalho), 'nota') !== false)
    ) {
        array_shift($linhas);
    }
}

$now = dataAtual();

switch ($modelo) {
    case 'romaneio':
        $sql = 'INSERT INTO romaneio (empresa_id, chave, created_at, updated_at) VALUES (?, ?, ?, ?)';
        $stmt = $db->prepare($sql);
        foreach ($linhas as $linha) {
            $chave = trim($linha[0] ?? '');
            if (strlen($chave) === 44) {
                $stmt->execute([$empresa_id, $chave, $now, $now]);
            }
        }
        echo "Romaneio importado!\n";
        break;
    case 'desconhecimento':
        $sql = 'INSERT INTO desconhecimento (empresa_id, chave, created_at, updated_at) VALUES (?, ?, ?, ?)';
        $stmt = $db->prepare($sql);
        foreach ($linhas as $linha) {
            $chave = trim($linha[0] ?? '');
            if (strlen($chave) === 44) {
                $stmt->execute([$empresa_id, $chave, $now, $now]);
            }
        }
        echo "Desconhecimento importado!\n";
        break;
    case 'fornecedores':
        $sqlFornecedor = 'INSERT INTO fornecedores (razao_social, cnpj, created_at, updated_at) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE razao_social = VALUES(razao_social), updated_at = VALUES(updated_at)';
        $stmtFornecedor = $db->prepare($sqlFornecedor);

        $sqlSelectFornecedor = 'SELECT id FROM fornecedores WHERE cnpj = ?';
        $stmtSelectFornecedor = $db->prepare($sqlSelectFornecedor);

        $sqlEmpresaFornecedor = 'INSERT INTO empresa_fornecedor (empresa_id, fornecedor_id, codigo_interno) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE codigo_interno = VALUES(codigo_interno)';
        $stmtEmpresaFornecedor = $db->prepare($sqlEmpresaFornecedor);

        foreach ($linhas as $linha) {
            $codigo = trim($linha[0] ?? '');
            $razao = trim($linha[1] ?? '');
            $cnpj = preg_replace('/[^0-9]/', '', trim($linha[2] ?? ''));

            if ($codigo && $razao && $cnpj) {
                // 1. Inserir ou atualizar fornecedor
                $stmtFornecedor->execute([$razao, $cnpj, $now, $now]);
                
                // 2. Obter o ID do fornecedor
                $stmtSelectFornecedor->execute([$cnpj]);
                $fornecedor = $stmtSelectFornecedor->fetch();
                
                if ($fornecedor) {
                    $fornecedor_id = $fornecedor['id'];
                    // 3. Vincular à empresa com código interno
                    $stmtEmpresaFornecedor->execute([$empresa_id, $fornecedor_id, $codigo]);
                }
            }
        }
        echo "Fornecedores importados!\n";
        break;
    case 'notas':
        $sql = 'INSERT INTO notas (empresa_id, codigo_processo, codigo_fiscal, valor_nota, numero_nota, chave_nota, coleta, escriturador, data_entrada, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
        $stmt = $db->prepare($sql);
        foreach ($linhas as $linha) {
            $codigo_processo = trim($linha[0] ?? '');
            $codigo_fiscal = trim($linha[1] ?? '');
            $valor_nota = str_replace([',', 'R$',' '], ['.', '', ''], trim($linha[2] ?? ''));
            $numero_nota = trim($linha[3] ?? '');
            // Debug: mostrar o array da linha e o valor da chave antes da limpeza
            print_r($linha);
            if (isset($linha[4])) {
                var_dump($linha[4]);
                // Mostrar o código ASCII de cada caractere
                for ($i = 0; $i < strlen($linha[4]); $i++) {
                    echo ord($linha[4][$i]) . ' ';
                }
                echo PHP_EOL;
            }
            // Limpeza agressiva: mantém apenas números
            $chave_nota = preg_replace('/\D/', '', $linha[4] ?? '');
            echo "Chave original: [{$linha[4]}] | Chave limpa: [{$chave_nota}]\n";
            $coleta = trim($linha[5] ?? '');
            $escriturador = trim($linha[6] ?? '');
            $data_entrada = isset($linha[7]) ? date('Y-m-d H:i:s', strtotime($linha[7])) : null;
            if ($codigo_processo && $codigo_fiscal && $valor_nota && $numero_nota && strlen($chave_nota) === 44) {
                $stmt->execute([$empresa_id, $codigo_processo, $codigo_fiscal, $valor_nota, $numero_nota, $chave_nota, $coleta, $escriturador, $data_entrada, $now, $now]);
            }
        }
        echo "Notas importadas!\n";
        break;
    default:
        echo "Modelo não suportado.\n";
        exit(1);
} 