<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/app/Models/Empresa.php';

$controlePath = __DIR__ . '/controle_monitoramento.json';
$controle = file_exists($controlePath) ? json_decode(file_get_contents($controlePath), true) : [];

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
$empresas = $empresaModel->all();

// Debug: mostrar todos os IDs de empresas retornadas
$idsEmpresas = array_column($empresas, 'id');
echo "Empresas retornadas pelo banco: ".implode(', ', $idsEmpresas)."\n";

$arquivosMap = [
    'Auditoria.txt'    => 'notas',
    'Fornecedores.txt' => 'fornecedores',
    'Manifestadas.txt' => 'desconhecimento',
    'Romaneio.txt'     => 'romaneio'
];

$dateNow = date('Y-m-d H:i:s');

// Otimização: Preparar statements fora dos loops
$stmtDeleteRomaneio = $db->prepare("DELETE FROM romaneio WHERE empresa_id = ?");
$stmtInsertNotas = $db->prepare("INSERT IGNORE INTO notas (empresa_id, codigo_processo, codigo_fiscal, valor_nota, numero_nota, chave_nota, hash, coleta, escriturador, data_entrada, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmtInsertFornecedor = $db->prepare("INSERT INTO fornecedores (razao_social, cnpj, created_at, updated_at) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE razao_social = VALUES(razao_social), updated_at = VALUES(updated_at)");
$stmtSelectFornecedor = $db->prepare("SELECT id FROM fornecedores WHERE cnpj = ?");
$stmtInsertEmpresaFornecedor = $db->prepare("INSERT INTO empresa_fornecedor (empresa_id, fornecedor_id, codigo_interno) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE codigo_interno = VALUES(codigo_interno)");
$stmtInsertDesconhecimento = $db->prepare("INSERT INTO desconhecimento (empresa_id, chave, created_at, updated_at) VALUES (?, ?, ?, ?)");
$stmtInsertRomaneio = $db->prepare("INSERT INTO romaneio (empresa_id, chave, created_at, updated_at) VALUES (?, ?, ?, ?)");

foreach ($empresas as $empresa) {
    $empresaId = $empresa['id'];
    $baseDir = __DIR__ . '/public/uploads/' . $empresaId . '/';
    echo "\n--- Empresa $empresaId ---\n";
    foreach ($arquivosMap as $arquivo => $tabela) {
        $caminho = $baseDir . $arquivo;
        echo "Verificando $caminho... ";
        if (!file_exists($caminho)) {
            echo "Arquivo não existe.\n";
            continue;
        }

        $modificado = filemtime($caminho);
        $controleKey = $empresaId . '_' . $arquivo;
        $controleAnterior = $controle[$controleKey] ?? null;
        echo "filemtime: $modificado | controle: ".($controleAnterior ?: 'nenhum')."... ";

        try {
            if (!isset($controle[$controleKey]) || $controle[$controleKey] < $modificado) {
                echo "PROCESSANDO\n";
                $linhas = file($caminho, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

                // Só limpa a tabela romaneio
                if ($tabela === 'romaneio') {
                    $stmtDeleteRomaneio->execute([$empresaId]);
                }

                foreach ($linhas as $linha) {
                    $campos = array_map('trim', explode(';', $linha));
                    switch ($tabela) {
                        case 'notas':
                            if (!empty($campos[0]) && !empty($campos[1]) && !empty($campos[2]) && !empty($campos[3])) {
                                $valorNota = str_replace(',', '.', $campos[2]);
                                $valorNotaFormatado = number_format((float)$valorNota, 2, '.', '');
                                // Padrão igual ao upload web
                                $chaveNota = !empty($campos[4])
                                    ? $campos[4]
                                    : 'SEMCHAVE-' . ($campos[3] ?? 'SEMNUMERO') . '-' . ($campos[1] ?? 'SEM_FISCAL');
                                $hash = $campos[3] . '-' . $valorNotaFormatado;

                                $stmtInsertNotas->execute([
                                    $empresaId,
                                    $campos[0] ?? null,
                                    $campos[1] ?? null,
                                    $valorNotaFormatado,
                                    $campos[3] ?? null,
                                    $chaveNota,
                                    $hash,
                                    $campos[5] ?? null,
                                    $campos[6] ?? null,
                                    isset($campos[7]) ? date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $campos[7]))) : null,
                                    $dateNow,
                                    $dateNow
                                ]);
                            }
                            break;
                        case 'fornecedores':
                            $codigo = $campos[0] ?? null;
                            $razao_social = $campos[1] ?? null;
                            $cnpj = preg_replace('/[^0-9]/', '', ($campos[2] ?? ''));

                            if($codigo && $razao_social && $cnpj) {
                                // 1. Insere ou atualiza o fornecedor
                                $stmtInsertFornecedor->execute([
                                    $razao_social,
                                    $cnpj,
                                    $dateNow,
                                    $dateNow
                                ]);

                                // 2. Pega o ID do fornecedor
                                $stmtSelectFornecedor->execute([$cnpj]);
                                $fornecedor = $stmtSelectFornecedor->fetch();

                                if ($fornecedor) {
                                    // 3. Vincula à empresa com código interno
                                    $stmtInsertEmpresaFornecedor->execute([$empresaId, $fornecedor['id'], $codigo]);
                                }
                            }
                            break;
                        case 'desconhecimento':
                            $stmtInsertDesconhecimento->execute([
                                $empresaId,
                                $campos[0] ?? null,
                                $dateNow,
                                $dateNow
                            ]);
                            break;
                        case 'romaneio':
                            $stmtInsertRomaneio->execute([
                                $empresaId,
                                $campos[0] ?? null,
                                $dateNow,
                                $dateNow
                            ]);
                            break;
                    }
                }
                $controle[$controleKey] = $modificado;
                echo "Arquivo $arquivo da empresa $empresaId processado!\n";
            } else {
                echo "IGNORADO (não houve alteração)\n";
            }
        } catch (Exception $e) {
            echo "\n[ERRO ao processar $arquivo da empresa $empresaId]: " . $e->getMessage() . "\n";
            continue;
        }
    }
}

file_put_contents($controlePath, json_encode($controle));
echo "\nMonitoramento concluído!\n";
echo "Se quiser forçar o processamento de todos os arquivos, apague o arquivo controle_monitoramento.json e rode novamente.\n"; 