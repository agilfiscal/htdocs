<?php
namespace App\Controllers;

use App\Models\Empresa;
use App\Models\AuditoriaNota;

class LogConsultaController {
    protected $db;
    protected $empresaModel;
    protected $auditoriaNotaModel;

    public function __construct() {
        $this->db = new \PDO(
            'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
            DB_USER,
            DB_PASS,
            [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
            ]
        );
        $this->empresaModel = new Empresa($this->db);
        $this->auditoriaNotaModel = new AuditoriaNota($this->db);
    }

    public function index() {
        $empresas = $this->empresaModel->all();
        $logs = [];
        foreach ($empresas as $empresa) {
            $ultima = $this->auditoriaNotaModel->getUltimaConsulta($empresa['id']);
            $quantidadeLinhas = $this->auditoriaNotaModel->getQuantidadeLinhasInseridas($empresa['id']);
            $logs[] = [
                'empresa' => $empresa,
                'ultima_consulta' => $ultima,
                'quantidade_linhas' => $quantidadeLinhas
            ];
        }
        $content = view('logs_consulta/index', [
            'logs' => $logs
        ], true);
        require APP_ROOT . '/app/Views/layouts/main.php';
    }

    public function forcarConsulta($params) {
        try {
            // Inicia o log
            $logFile = __DIR__ . '/../../public/debug_auditoria.txt';
            file_put_contents($logFile, date('Y-m-d H:i:s') . " - Iniciando forçar consulta\n", FILE_APPEND);
            file_put_contents($logFile, "Parâmetros recebidos: " . print_r($params, true) . "\n", FILE_APPEND);
            file_put_contents($logFile, "REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "\n", FILE_APPEND);

            $empresaId = $params['id'] ?? null;
            if (!$empresaId) {
                // Tenta extrair o ID da URL
                $urlParts = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
                $empresaId = end($urlParts);
                file_put_contents($logFile, "ID extraído da URL: " . $empresaId . "\n", FILE_APPEND);
            }

            if (!$empresaId) {
                throw new \Exception('ID da empresa não fornecido');
            }

            file_put_contents($logFile, "Empresa ID: " . $empresaId . "\n", FILE_APPEND);

            // Busca os dados da empresa
            $empresa = $this->empresaModel->find($empresaId);
            if (!$empresa) {
                throw new \Exception('Empresa não encontrada');
            }

            file_put_contents($logFile, "Dados da empresa encontrados: " . json_encode($empresa) . "\n", FILE_APPEND);

            // Verifica se a empresa tem certificado configurado
            if (empty($empresa['certificado_path']) || empty($empresa['certificado_senha'])) {
                throw new \Exception('Esta empresa não possui certificado digital configurado. Por favor, configure o certificado antes de realizar a consulta.');
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
                throw new \Exception('UF da empresa inválida ou não suportada.');
            }

            // Atualiza o arquivo .env com os dados da empresa
            $envContent = "CERTIFICADO_PFX={$empresa['certificado_path']}\n";
            $envContent .= "CERTIFICADO_SENHA={$empresa['certificado_senha']}\n";
            $envContent .= "CNPJ={$empresa['cnpj']}\n";
            $envContent .= "UF_AUTOR={$codigoUf}\n";
            $envContent .= "AMBIENTE=1\n"; // 1 para produção, 2 para homologação

            $envPath = __DIR__ . '/../../.env';
            file_put_contents($envPath, $envContent);
            file_put_contents($logFile, "Arquivo .env atualizado em: " . $envPath . "\n", FILE_APPEND);

            // Executa o script Node.js
            $rootPath = __DIR__ . '/../../';
            $command = "cd {$rootPath} && npm start";
            file_put_contents($logFile, "Executando comando: " . $command . "\n", FILE_APPEND);

            // Executa o comando e captura a saída
            $output = [];
            $returnVar = 0;
            exec($command . " 2>&1", $output, $returnVar);
            
            file_put_contents($logFile, "Retorno do comando: " . $returnVar . "\n", FILE_APPEND);
            file_put_contents($logFile, "Saída do comando: " . implode("\n", $output) . "\n", FILE_APPEND);

            if ($returnVar !== 0) {
                throw new \Exception('Erro ao executar o comando npm start: ' . implode("\n", $output));
            }

            // Importa o resultado da consulta para o banco de dados
            $importado = $this->importarResultadoMDE($empresaId);
            file_put_contents($logFile, "Importação do resultado: " . ($importado ? 'OK' : 'FALHOU') . "\n", FILE_APPEND);

            // Verifica se houve consumo indevido
            $jsonPath = __DIR__ . '/../../public/resultado_mde.json';
            if (file_exists($jsonPath)) {
                $data = json_decode(file_get_contents($jsonPath), true);
                if (isset($data['data']['cStat']) && $data['data']['cStat'] === '656') {
                    $_SESSION['warning'] = 'Atenção: Consumo indevido detectado. As consultas ao MDE devem ser realizadas com intervalo mínimo de 1 hora entre cada consulta.';
                }
            }

            $_SESSION['success'] = 'Consulta iniciada com sucesso! O resultado estará disponível em breve.';
            file_put_contents($logFile, "Consulta iniciada com sucesso\n", FILE_APPEND);
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            if (
                stripos($msg, 'certificado expirado') !== false ||
                stripos($msg, 'certificado vencido') !== false ||
                stripos($msg, 'forbidden') !== false ||
                stripos($msg, 'acesso negado') !== false ||
                stripos($msg, 'access is denied') !== false
            ) {
                $_SESSION['error'] = 'O certificado digital desta empresa está expirado. Por favor, atualize para o mais recente.';
            } else {
                $_SESSION['error'] = 'Erro ao forçar consulta: ' . $msg;
            }
            file_put_contents($logFile, "ERRO: " . $_SESSION['error'] . "\n", FILE_APPEND);
        }
        
        header('Location: /mde/logs-consulta');
        exit;
    }

    public function configurarCertificado($params) {
        try {
            $empresaId = $params['id'] ?? null;
            if (!$empresaId) {
                throw new \Exception('ID da empresa não fornecido');
            }

            $empresa = $this->empresaModel->find($empresaId);
            if (!$empresa) {
                throw new \Exception('Empresa não encontrada');
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Processa o upload do certificado
                if (!isset($_FILES['certificado']) || $_FILES['certificado']['error'] !== UPLOAD_ERR_OK) {
                    throw new \Exception('Erro ao fazer upload do certificado');
                }

                $certificado = $_FILES['certificado'];
                $senha = $_POST['senha'] ?? '';

                if (empty($senha)) {
                    throw new \Exception('A senha do certificado é obrigatória');
                }

                // Cria o diretório de certificados se não existir
                $certDir = __DIR__ . '/../../certificados';
                if (!file_exists($certDir)) {
                    mkdir($certDir, 0777, true);
                }

                // Gera um nome único para o arquivo
                $ext = pathinfo($certificado['name'], PATHINFO_EXTENSION);
                $novoNome = $empresa['cnpj'] . '_' . time() . '.' . $ext;
                $caminhoCompleto = $certDir . '/' . $novoNome;

                // Move o arquivo
                if (!move_uploaded_file($certificado['tmp_name'], $caminhoCompleto)) {
                    throw new \Exception('Erro ao salvar o certificado');
                }

                // Atualiza os dados da empresa
                $this->empresaModel->update($empresaId, [
                    'certificado_path' => $caminhoCompleto,
                    'certificado_senha' => $senha
                ]);

                $_SESSION['success'] = 'Certificado configurado com sucesso!';
                header('Location: /mde/logs-consulta');
                exit;
            }

            // Exibe o formulário
            $content = view('logs_consulta/configurar_certificado', [
                'empresa' => $empresa
            ], true);
            require APP_ROOT . '/app/Views/layouts/main.php';

        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: /mde/logs-consulta');
            exit;
        }
    }

    public function importarResultadoMDE($empresaId) {
        $jsonPath = __DIR__ . '/../../public/resultado_mde.json';
        if (!file_exists($jsonPath)) {
            return false;
        }
        $data = json_decode(file_get_contents($jsonPath), true);
        if (!$data) {
            return false;
        }

        // Verifica se há documentos na resposta
        if (!isset($data['data']['docZip']) || empty($data['data']['docZip'])) {
            // Mesmo sem documentos, atualiza a data da última consulta
            $this->auditoriaNotaModel->setUltimaConsulta($empresaId);
            return true;
        }

        // Buscar UF da empresa para fallback
        $empresa = $this->empresaModel->find($empresaId);
        $ufEmpresa = $empresa['uf'] ?? '';

        if (!empty($data['data']['docZip'])) {
            foreach ($data['data']['docZip'] as $doc) {
                if (isset($doc['json']['nfeProc']['NFe']['infNFe'])) {
                    $inf = $doc['json']['nfeProc']['NFe']['infNFe'];
                    $statusMap = [
                        '100' => 'Autorizada',
                        '101' => 'Cancelada',
                        '110' => 'Denegada'
                    ];
                    $tipoMap = [
                        '0' => 'Entrada',
                        '1' => 'Saída',
                        '2' => 'Saída'
                    ];
                    $uf = $inf['emit']['enderEmit']['UF'] ?? $ufEmpresa;
                    $numero = isset($inf['ide']['nNF']) ? $inf['ide']['nNF'] : null;
                    $dataEmissao = isset($inf['ide']['dhEmi']) ? $inf['ide']['dhEmi'] : null;
                    $chave = isset($inf['@_Id']) ? preg_replace('/^NFe/', '', $inf['@_Id']) : '';
                    $status = '';
                    if (isset($doc['json']['nfeProc']['protNFe']['infProt']['cStat'])) {
                        $cStat = $doc['json']['nfeProc']['protNFe']['infProt']['cStat'];
                        $status = $statusMap[$cStat] ?? $cStat;
                    }
                    if ($numero && $dataEmissao) {
                        try {
                            $dt = new \DateTime($dataEmissao);
                            $dataEmissao = $dt->format('Y-m-d H:i:s');
                        } catch (\Exception $e) {
                            $dataEmissao = null;
                        }
                        if ($dataEmissao) {
                            $nota = [
                                'empresa_id' => $empresaId,
                                'numero' => $numero,
                                'cnpj' => $inf['emit']['CNPJ'] ?? '',
                                'razao_social' => $inf['emit']['xNome'] ?? '',
                                'data_emissao' => $dataEmissao,
                                'valor' => $inf['total']['ICMSTot']['vNF'] ?? '',
                                'chave' => $chave,
                                'uf' => $uf,
                                'status' => $status,
                                'tipo' => $tipoMap[$inf['ide']['tpNF'] ?? ''] ?? $inf['ide']['tpNF'] ?? ''
                            ];
                            $this->auditoriaNotaModel->inserir($nota);
                        }
                    }
                } elseif (isset($doc['json']['resNFe'])) {
                    $res = $doc['json']['resNFe'];
                    $statusMap = [
                        '1' => 'Autorizada',
                        '2' => 'Cancelada',
                        '3' => 'Denegada'
                    ];
                    $tipoMap = [
                        '0' => 'Entrada',
                        '1' => 'Saída',
                        '2' => 'Saída'
                    ];
                    $uf = $ufEmpresa;
                    if (!empty($res['chNFe']) && strlen($res['chNFe']) >= 2) {
                        $codigoUF = substr($res['chNFe'], 0, 2);
                        $ufs = [
                            '11'=>'RO','12'=>'AC','13'=>'AM','14'=>'RR','15'=>'PA','16'=>'AP','17'=>'TO','21'=>'MA','22'=>'PI','23'=>'CE','24'=>'RN','25'=>'PB','26'=>'PE','27'=>'AL','28'=>'SE','29'=>'BA','31'=>'MG','32'=>'ES','33'=>'RJ','35'=>'SP','41'=>'PR','42'=>'SC','43'=>'RS','50'=>'MS','51'=>'MT','52'=>'GO','53'=>'DF'
                        ];
                        if (isset($ufs[$codigoUF])) {
                            $uf = $ufs[$codigoUF];
                        }
                    }
                    $numero = isset($res['nNF']) ? $res['nNF'] : null;
                    $dataEmissao = isset($res['dhEmi']) ? $res['dhEmi'] : null;
                    if ($numero && $dataEmissao) {
                        try {
                            $dt = new \DateTime($dataEmissao);
                            $dataEmissao = $dt->format('Y-m-d H:i:s');
                        } catch (\Exception $e) {
                            $dataEmissao = null;
                        }
                        if ($dataEmissao) {
                            $nota = [
                                'empresa_id' => $empresaId,
                                'numero' => $numero,
                                'cnpj' => $res['CNPJ'] ?? '',
                                'razao_social' => $res['xNome'] ?? '',
                                'data_emissao' => $dataEmissao,
                                'valor' => $res['vNF'] ?? '',
                                'chave' => $res['chNFe'] ?? '',
                                'uf' => $uf,
                                'status' => $statusMap[$res['cSitNFe'] ?? ''] ?? $res['cSitNFe'] ?? '',
                                'tipo' => $tipoMap[$res['tpNF'] ?? ''] ?? $res['tpNF'] ?? ''
                            ];
                            $this->auditoriaNotaModel->inserir($nota);
                        }
                    }
                }
            }
        }

        // Atualiza a data da última consulta
        $this->auditoriaNotaModel->setUltimaConsulta($empresaId);
        return true;
    }
} 