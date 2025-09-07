<?php
namespace App\Controllers;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use App\Models\Empresa;
use App\Models\Certificado;
use App\Models\AuditoriaNota;

// Função auxiliar para remover acentos
if (!function_exists('removerAcentos')) {
    function removerAcentos($string) {
        return preg_replace(
            array("/á|à|ã|â|ä/","/Á|À|Ã|Â|Ä/","/é|è|ê|ë/","/É|È|Ê|Ë/","/í|ì|î|ï/","/Í|Ì|Î|Ï/","/ó|ò|õ|ô|ö/","/Ó|Ò|Õ|Ô|Ö/","/ú|ù|û|ü/","/Ú|Ù|Û|Ü/","/ç/","/Ç/"),
            array("a","A","e","E","i","I","o","O","u","U","c","C"),
            $string
        );
    }
}

class AuditoriaController {
    protected $db;
    protected $empresaModel;
    protected $certificadoModel;
    protected $auditoriaNotaModel;
    protected $logFile;

    public function __construct() {
        error_log('=== CONSTRUTOR AuditoriaController ===');
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['usuario_id'])) {
            error_log('Usuário não autenticado, redirecionando para login');
            header('Location: /login');
            exit;
        }
        error_log('Usuário autenticado: ' . $_SESSION['usuario_id']);
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
        $this->certificadoModel = new \App\Models\Certificado($this->db);
        $this->auditoriaNotaModel = new AuditoriaNota($this->db);
        
        // Configurar log personalizado
        $this->logFile = APP_ROOT . '/logs/auditoria.log';
        if (!file_exists(dirname($this->logFile))) {
            mkdir(dirname($this->logFile), 0777, true);
        }
    }

    private function log($message) {
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp] $message\n";
        file_put_contents($this->logFile, $logMessage, FILE_APPEND);
    }

    public function index() {
        $empresas = $this->empresaModel->all();
        
        // Se não houver empresa_id na URL, redireciona para a mesma página com a primeira empresa do usuário
        if (empty($_GET['empresa_id']) && !empty($empresas)) {
            // Buscar empresas que o usuário está atendendo
            $empresasAtendidasIds = [];
            if (!empty($_SESSION['empresas_atendidas_ids']) && is_array($_SESSION['empresas_atendidas_ids'])) {
                $empresasAtendidasIds = $_SESSION['empresas_atendidas_ids'];
            } elseif (!empty($_SESSION['empresa_atendida_id'])) {
                $empresasAtendidasIds = [$_SESSION['empresa_atendida_id']];
            }
            
            // Se não tem empresas atendidas definidas, verificar se é admin/master para usar todas as empresas
            if (empty($empresasAtendidasIds)) {
                $stmt = $this->db->prepare('SELECT tipo FROM usuarios WHERE id = ?');
                $stmt->execute([$_SESSION['usuario_id']]);
                $tipo = $stmt->fetchColumn();
                
                if ($tipo === 'admin' || $tipo === 'master') {
                    $empresasAtendidasIds = array_column($empresas, 'id');
                }
            }
            
            // Se ainda não tem empresas, usar todas as disponíveis
            if (empty($empresasAtendidasIds)) {
                $empresasAtendidasIds = array_column($empresas, 'id');
            }
            
            // Pega o menor ID das empresas que o usuário está atendendo
            sort($empresasAtendidasIds, SORT_NUMERIC);
            $primeiraEmpresaId = $empresasAtendidasIds[0];
            $params = $_GET;
            $params['empresa_id'] = $primeiraEmpresaId;
            header('Location: /auditoria?' . http_build_query($params));
            exit;
        }
        $empresa_id = $_GET['empresa_id'] ?? '';
        $data_inicial = $_GET['data_inicial'] ?? '';
        $data_final = $_GET['data_final'] ?? '';
        $busca = $_GET['busca'] ?? '';
        $filtro_rapido = $_GET['filtro_rapido'] ?? '';
        $status = $_GET['status'] ?? '';
        $pagina = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
        $por_pagina = 15;
        $acao_atualizar = isset($_GET['atualizar']);
        $acao_filtrar = isset($_GET['filtrar']);
        // Filtros rápidos de data
        if ($filtro_rapido === 'ultimos_30') {
            $data_inicial = date('Y-m-d', strtotime('-30 days'));
            $data_final = date('Y-m-d');
        } elseif ($filtro_rapido === 'ultimos_60') {
            $data_inicial = date('Y-m-d', strtotime('-60 days'));
            $data_final = date('Y-m-d');
        } elseif ($filtro_rapido === 'este_mes') {
            $data_inicial = date('Y-m-01');
            $data_final = date('Y-m-t');
        } elseif ($filtro_rapido === 'mes_passado') {
            $data_inicial = date('Y-m-01', strtotime('-1 month'));
            $data_final = date('Y-m-t', strtotime('-1 month'));
        }
        $data_inicial_param = $data_inicial ?: null;
        $data_final_param = $data_final ?: null;

        // Buscar destinos do banco
        $destinos = $this->db->query('SELECT id, destino FROM destino ORDER BY destino')->fetchAll(\PDO::FETCH_ASSOC);

        // Se empresa selecionada, buscar certificado e setar variáveis de ambiente
        if ($empresa_id) {
            $empresa = $this->empresaModel->find($empresa_id);
            $cert = $this->certificadoModel->findByEmpresa($empresa_id);
            if ($empresa && $cert) {
                putenv('CERTIFICADO_PFX=' . APP_ROOT . '/app/Views/certificados/' . $cert['arquivo']);
                putenv('CERTIFICADO_SENHA=' . $cert['senha']);
                putenv('CNPJ=' . $empresa['cnpj']);
                // Mapear sigla para código numérico da UF
                $ufMap = [
                    'RO' => '11', 'AC' => '12', 'AM' => '13', 'RR' => '14', 'PA' => '15', 'AP' => '16', 'TO' => '17',
                    'MA' => '21', 'PI' => '22', 'CE' => '23', 'RN' => '24', 'PB' => '25', 'PE' => '26', 'AL' => '27', 'SE' => '28', 'BA' => '29',
                    'MG' => '31', 'ES' => '32', 'RJ' => '33', 'SP' => '35',
                    'PR' => '41', 'SC' => '42', 'RS' => '43',
                    'MS' => '50', 'MT' => '51', 'GO' => '52', 'DF' => '53'
                ];
                $codigoUf = $ufMap[$empresa['uf']] ?? '';
                putenv('UF_AUTOR=' . $codigoUf);
            }
        }

        // Buscar notas fiscais
        $notas = [];
        if ($empresa_id) {
            $resultado = $this->auditoriaNotaModel->historicoComDestino(
                $empresa_id,
                1875,
                $data_inicial_param,
                $data_final_param,
                $busca,
                1, // buscar tudo, paginar depois
                10000, // buscar até 10 mil notas, ajustar se necessário
                $status // <-- Corrigido: agora passa o status corretamente
            );
            if (!empty($resultado['notas'])) {
                $notas = $resultado['notas'];
            }
        }
        // Adicionar flag de observação
        if (!empty($notas)) {
            $idsNotas = array_column($notas, 'id');
            if (!empty($idsNotas)) {
                $placeholders = implode(',', array_fill(0, count($idsNotas), '?'));
                $sqlObs = "SELECT nota_id FROM observacoes_nota WHERE nota_id IN ($placeholders)";
                $stmtObs = $this->db->prepare($sqlObs);
                $stmtObs->execute($idsNotas);
                $idsComObs = array_column($stmtObs->fetchAll(\PDO::FETCH_ASSOC), 'nota_id');
                foreach ($notas as &$nota) {
                    $nota['tem_observacao'] = in_array($nota['id'], $idsComObs);
                }
                unset($nota);
            }
        }
        // Calcular status_lancamento para cada nota (igual à tela)
        $empresaCnpjLimpo = null;
        if ($empresa_id) {
            $empresaSelecionada = $this->empresaModel->find($empresa_id);
            if ($empresaSelecionada && isset($empresaSelecionada['cnpj'])) {
                $empresaCnpjLimpo = preg_replace('/[^0-9]/', '', $empresaSelecionada['cnpj']);
            }
        }
        foreach ($notas as &$nota) {
            $chave = isset($nota['chave']) ? trim(str_replace("'", '', $nota['chave'])) : '';
            $notaCnpjLimpo = isset($nota['cnpj']) ? preg_replace('/[^0-9]/', '', $nota['cnpj']) : '';
            // Cancelada tem prioridade máxima
            if (isset($nota['situacao']) && mb_strtolower($nota['situacao']) === 'cancelada') {
                $nota['status_lancamento'] = 'Cancelada';
                continue;
            }
            // Se o CNPJ da nota for igual ao da empresa selecionada, status Própria
            if ($empresaCnpjLimpo && $notaCnpjLimpo && $empresaCnpjLimpo === $notaCnpjLimpo) {
                $nota['status_lancamento'] = 'Própria';
                continue;
            }
            $sql = "SELECT 
                CASE 
                    WHEN EXISTS (
                        SELECT 1 
                        FROM romaneio r 
                        WHERE r.empresa_id = ? 
                        AND TRIM(REPLACE(r.chave, '\'', '')) = ?
                    ) THEN 'Romaneio'
                    WHEN EXISTS (
                        SELECT 1 
                        FROM notas n 
                        WHERE n.empresa_id = ? 
                        AND n.hash = ? 
                        AND n.hash IS NOT NULL
                        AND n.hash != ''
                        AND n.hash = TRIM(?)
                    ) THEN 'Lançada'
                    WHEN EXISTS (
                        SELECT 1 
                        FROM desconhecimento d 
                        WHERE d.empresa_id = ? 
                        AND TRIM(REPLACE(d.chave, '\'', '')) = ?
                    ) THEN 'Desconhecimento'
                    WHEN EXISTS (
                        SELECT 1 
                        FROM empresas e 
                        WHERE e.cnpj = ? 
                        AND (
                            e.tipo_empresa = 'filial'
                            
                        )
                    ) THEN 'Transferência'
                    ELSE 'Pendente'
                END as status";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $empresa_id, $chave,
                $empresa_id, $nota['hash'], $nota['hash'],
                $empresa_id, $chave,
                $nota['cnpj']
            ]);
            $resultadoStatus = $stmt->fetch(\PDO::FETCH_ASSOC);
            $nota['status_lancamento'] = $resultadoStatus['status'];
        }
        unset($nota);
        // --- LÓGICA PARA NOTAS ANULADAS ---
        // 1. Separar notas pendentes de saída e notas de entrada
        $notasPendentesSaida = [];
        $notasEntrada = [];
        foreach ($notas as $idx => $nota) {
            if (
                isset($nota['status_lancamento']) && $nota['status_lancamento'] === 'Pendente' &&
                (empty($nota['tipo']) || mb_strtolower($nota['tipo']) !== 'entrada')
            ) {
                $notasPendentesSaida[$idx] = $nota;
            } elseif (
                isset($nota['tipo']) && mb_strtolower($nota['tipo']) === 'entrada' &&
                isset($nota['status_lancamento']) && $nota['status_lancamento'] !== 'Própria'
            ) {
                $notasEntrada[$idx] = $nota;
            }
        }
        // 2. Para cada nota de entrada, procurar a(s) saída(s) correspondente(s)
        $anuladasIdx = [];
        foreach ($notasEntrada as $idxEntrada => $entrada) {
            $fornecedorEntrada = preg_replace('/[^0-9]/', '', $entrada['cnpj'] ?? '');
            $valorEntrada = floatval(str_replace([',', 'R$', ' '], ['.', '', ''], $entrada['valor'] ?? 0));
            $dataEntrada = isset($entrada['data_emissao']) ? strtotime($entrada['data_emissao']) : null;
            // Filtrar notas de saída pendentes do mesmo fornecedor, mesmo valor, data menor ou igual
            $candidatas = [];
            foreach ($notasPendentesSaida as $idxSaida => $saida) {
                $fornecedorSaida = preg_replace('/[^0-9]/', '', $saida['cnpj'] ?? '');
                $valorSaida = floatval(str_replace([',', 'R$', ' '], ['.', '', ''], $saida['valor'] ?? 0));
                $dataSaida = isset($saida['data_emissao']) ? strtotime($saida['data_emissao']) : null;
                if (
                    $fornecedorEntrada === $fornecedorSaida &&
                    abs($valorEntrada - $valorSaida) < 0.01 && // tolerância centavos
                    $dataSaida !== null && $dataEntrada !== null &&
                    $dataSaida <= $dataEntrada
                ) {
                    $candidatas[$idxSaida] = [
                        'diff' => abs($dataEntrada - $dataSaida),
                        'data' => $dataSaida
                    ];
                }
            }
            if (!empty($candidatas)) {
                // Seleciona a saída com data mais próxima da entrada
                uasort($candidatas, function($a, $b) {
                    if ($a['diff'] === $b['diff']) return $b['data'] <=> $a['data']; // mais recente
                    return $a['diff'] <=> $b['diff'];
                });
                $idxAnulada = array_key_first($candidatas);
                $anuladasIdx[] = $idxAnulada;
                // Remove para não anular mais de uma vez
                unset($notasPendentesSaida[$idxAnulada]);
            }
        }
        // 3. Marcar as notas anuladas
        foreach ($anuladasIdx as $idx) {
            $notas[$idx]['status_lancamento'] = 'Anulada';
        }
        // --- LÓGICA PARA NOTAS DE TRANSFERÊNCIA (igual ao dashboard) ---
        // Só faz sentido se a empresa selecionada fizer parte de um grupo (matriz/filial) e houver mais de uma empresa no grupo
        $empresaSelecionadaObj = null;
        if (!empty($empresa_id) && !empty($empresas)) {
            foreach ($empresas as $emp) {
                if ($emp['id'] == $empresa_id) {
                    $empresaSelecionadaObj = $emp;
                    break;
                }
            }
        }
        if ($empresaSelecionadaObj && \App\Models\Empresa::isGrupo($empresaSelecionadaObj) && !empty($empresa_id) && !empty($empresas) && count($empresas) > 1) {
            $empresaSelecionada = null;
            $grupoCnpjs = [];
            foreach ($empresas as $emp) {
                $cnpjLimpo = preg_replace('/[^0-9]/', '', $emp['cnpj']);
                if ($emp['id'] == $empresa_id) {
                    $empresaSelecionada = $cnpjLimpo;
                }
                $grupoCnpjs[] = $cnpjLimpo;
            }
            if ($empresaSelecionada && count($grupoCnpjs) > 1) {
                foreach ($notas as &$nota) {
                    $notaCnpjLimpo = isset($nota['cnpj']) ? preg_replace('/[^0-9]/', '', $nota['cnpj']) : '';
                    if (
                        in_array($notaCnpjLimpo, $grupoCnpjs)
                        && $notaCnpjLimpo !== $empresaSelecionada
                    ) {
                        $nota['status_lancamento'] = 'Transferência';
                    }
                }
                unset($nota);
            }
        }
        // Aplicar filtro por status em PHP (igual à tela)
        if ($status && $status !== 'todos') {
            if ($status === 'Pendente') {
                $notas = array_filter($notas, function($nota) {
                    return $nota['status_lancamento'] !== 'Lançada'
                        && $nota['situacao'] !== 'Cancelada'
                        && $nota['tipo'] !== 'Entrada'
                        && $nota['status_lancamento'] !== 'Transferência'
                        && $nota['status_lancamento'] !== 'Desconhecimento'
                        && $nota['status_lancamento'] !== 'Romaneio'
                        && $nota['status_lancamento'] !== 'Própria'
                        && $nota['status_lancamento'] !== 'Anulada'; // Não incluir Anulada como pendente
                });
            } elseif ($status === 'Cancelada') {
                $notas = array_filter($notas, function($nota) {
                    return $nota['situacao'] === 'Cancelada';
                });
            } elseif ($status === 'Entrada') {
                $notas = array_filter($notas, function($nota) {
                    return (
                        isset($nota['tipo']) && mb_strtolower($nota['tipo']) === 'entrada'
                        && mb_strtolower($nota['status_lancamento']) !== 'cancelada'
                        && mb_strtolower($nota['status_lancamento']) !== 'transferência'
                        && mb_strtolower($nota['status_lancamento']) !== 'desconhecimento'
                        && mb_strtolower($nota['status_lancamento']) !== 'própria'
                    );
                });
            } else {
                $notas = array_filter($notas, function($nota) use ($status) {
                    $statusLanc = mb_strtolower(removerAcentos($nota['status_lancamento']));
                    $statusFiltro = mb_strtolower(removerAcentos($status));
                    if ($statusFiltro === 'transferencia') {
                        return $statusLanc === $statusFiltro && (!isset($nota['situacao']) || mb_strtolower($nota['situacao']) !== 'cancelada');
                    }
                    return $statusLanc === $statusFiltro;
                });
            }
        }
        // Ordenar por data_emissao DESC (mais novo no topo)
        usort($notas, function($a, $b) {
            return strtotime($b['data_emissao'] ?? '1970-01-01') <=> strtotime($a['data_emissao'] ?? '1970-01-01');
        });
        // Não paginar: exportar todas as notas filtradas
        $total_notas = count($notas);

        // Se não houver empresa ou notas, mantém a simulação para não quebrar a view
        if (empty($notas)) {
            $notas = [];
        }
        // Filtra apenas notas válidas (array e com id)
        $notas = array_filter($notas, function($n) {
            return is_array($n) && isset($n['id']);
        });

        $content = view('auditoria/index', [
            'empresas' => $empresas,
            'empresa_id' => $empresa_id,
            'data_inicial' => $data_inicial,
            'data_final' => $data_final,
            'busca' => $busca,
            'filtro_rapido' => $filtro_rapido,
            'notas' => $notas,
            'destinos' => $destinos,
            'pagina_atual' => $pagina,
            'total_notas' => $total_notas ?? 0
        ], true);
        require APP_ROOT . '/app/Views/layouts/main.php';
    }

    private function buscarNotas($empresa_id, $data_inicial, $data_final) {
        // Simulação de notas fiscais
        $notas = [];
        for ($i = 1; $i <= 10; $i++) {
            $notas[] = [
                'numero' => str_pad($i, 9, '0', STR_PAD_LEFT),
                'data_emissao' => date('Y-m-d', strtotime("$data_inicial +$i days")),
                'empresa' => $empresa_id ? "Empresa #$empresa_id" : "Empresa #" . rand(1, 5),
                'valor' => number_format(rand(100, 1000), 2, ',', '.'),
                'status' => (rand(0, 1) ? 'Autorizada' : 'Cancelada')
            ];
        }
        return $notas;
    }

    public function salvarDestinoVencimentos() {
        error_log("Método salvarDestinoVencimentos iniciado");
        try {
            $json = file_get_contents('php://input');
            error_log("JSON recebido: " . $json);
            $data = json_decode($json, true);
            error_log("Dados decodificados: " . print_r($data, true));
            if (!isset($data['nota_id'])) {
                error_log("Erro: nota_id não encontrado");
                throw new \Exception('ID da nota não fornecido');
            }
            $nota_id = $data['nota_id'];
            $destino_id = $data['destino_id'] ?? null;
            $vencimentos = $data['vencimentos'] ?? [];
            
            // Buscar o valor total da nota
            $stmtValor = $this->db->prepare("SELECT valor FROM auditoria_notas WHERE id = ?");
            $stmtValor->execute([$nota_id]);
            $nota = $stmtValor->fetch();

            if (!$nota) {
                throw new \Exception('Nota não encontrada');
            }

            // Converte o valor para um float, tratando formatos como "1.234,56" ou "1234.56"
            $valorString = (string) $nota['valor'];
            $valorLimpo = preg_replace('/[^\d,.]/', '', $valorString); // Remove caracteres não numéricos

            $lastDot = strrpos($valorLimpo, '.');
            $lastComma = strrpos($valorLimpo, ',');

            // Se a última vírgula vem depois do último ponto, o formato é brasileiro (1.234,56)
            if ($lastComma !== false && $lastComma > $lastDot) {
                $valorLimpo = str_replace('.', '', $valorLimpo); // Remove pontos de milhar
                $valorLimpo = str_replace(',', '.', $valorLimpo); // Troca a vírgula decimal por ponto
            } else {
                // Senão, o formato é americano/banco de dados (1,234.56)
                $valorLimpo = str_replace(',', '', $valorLimpo); // Remove vírgulas de milhar
            }
            
            $valorTotal = (float)$valorLimpo;

            $numVencimentos = count($vencimentos);
            $valorParcela = ($numVencimentos > 0) ? round($valorTotal / $numVencimentos, 2) : 0;

            error_log("Processando nota_id: " . $nota_id);
            error_log("Destino_id: " . $destino_id);
            error_log("Vencimentos: " . print_r($vencimentos, true));
            try {
                $db = new \PDO(
                    'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
                    DB_USER,
                    DB_PASS,
                    [
                        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
                    ]
                );
                error_log("Conexão PDO criada com sucesso");
                $db->beginTransaction();
                // Remove destino antigo
                $sql1 = "DELETE FROM auditoria_destino WHERE nota_id = ?";
                error_log("Executando: $sql1 com nota_id=$nota_id");
                $stmt1 = $db->prepare($sql1);
                $stmt1->execute([$nota_id]);
                error_log("Destino antigo removido");
                // Remove vencimentos antigos
                $sql2 = "DELETE FROM auditoria_notas_vencimentos WHERE nota_id = ?";
                error_log("Executando: $sql2 com nota_id=$nota_id");
                $stmt2 = $db->prepare($sql2);
                $stmt2->execute([$nota_id]);
                error_log("Vencimentos antigos removidos");
                // Insere novo destino
                if ($destino_id) {
                    $sql3 = "INSERT INTO auditoria_destino (nota_id, destino_id) VALUES (?, ?)";
                    error_log("Executando: $sql3 com nota_id=$nota_id, destino_id=$destino_id");
                    $stmt3 = $db->prepare($sql3);
                    $stmt3->execute([$nota_id, $destino_id]);
                    error_log("Novo destino inserido");
                }
                // Insere novos vencimentos
                if ($numVencimentos > 0) {
                    $sql4 = "INSERT INTO auditoria_notas_vencimentos (nota_id, data_vencimento, valor, usuario_criador_id) VALUES (?, ?, ?, ?)";
                    $stmt4 = $db->prepare($sql4);

                    // Lidar com a diferença de arredondamento na primeira parcela
                    $valorTotalCalculado = $valorParcela * $numVencimentos;
                    $diferenca = round($valorTotal - $valorTotalCalculado, 2);

                    // Obter o ID do usuário logado
                    $usuario_criador_id = $_SESSION['usuario_id'] ?? null;

                    foreach ($vencimentos as $i => $vencimento) {
                        $valorDaParcelaAtual = $valorParcela;
                        if ($i === 0) {
                            $valorDaParcelaAtual += $diferenca;
                        }

                        error_log("Executando: $sql4 com nota_id=$nota_id, data_vencimento=$vencimento, valor=$valorDaParcelaAtual, usuario_criador_id=$usuario_criador_id");
                        $stmt4->execute([$nota_id, $vencimento, $valorDaParcelaAtual, $usuario_criador_id]);
                        error_log("Vencimento inserido: " . $vencimento . " com valor " . $valorDaParcelaAtual . " pelo usuário " . $usuario_criador_id);
                    }
                }
                $db->commit();
                error_log("Transação commitada com sucesso");
                echo json_encode(['success' => true]);
            } catch (\Exception $e) {
                if (isset($db)) $db->rollBack();
                error_log("Erro na transação: " . $e->getMessage());
                throw $e;
            }
        } catch (\Exception $e) {
            error_log("Erro no método salvarDestinoVencimentos: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function getDestinoEVencimentos() {
        $nota_id = $_GET['nota_id'] ?? null;
        if (!$nota_id) {
            http_response_code(400);
            echo json_encode(['erro' => 'ID da nota não informado']);
            exit;
        }
        $dados = $this->auditoriaNotaModel->getDestinoEVencimentos($nota_id);
        echo json_encode($dados);
        exit;
    }

    public function tributosItens() {
        $nota_id = $_GET['nota_id'] ?? null;
        if (!$nota_id) {
            echo json_encode(['erro' => 'ID da nota não informado']);
            exit;
        }
        $db = $this->db;
        $tributos = $db->prepare('SELECT valor_icms, valor_pis, valor_cofins, valor_ipi, valor_fcp FROM auditoria_tributos WHERE nota_id = ?');
        $tributos->execute([$nota_id]);
        $tributos = $tributos->fetch(PDO::FETCH_ASSOC) ?: [];
        $itens = $db->prepare('SELECT quantidade_itens FROM auditoria_itens WHERE nota_id = ?');
        $itens->execute([$nota_id]);
        $itens = $itens->fetch(PDO::FETCH_ASSOC) ?: [];
        echo json_encode(array_merge($tributos, $itens));
        exit;
    }

    public function getNotaDetalhes() {
        $nota_id = $_GET['nota_id'] ?? null;
        if (!$nota_id) {
            echo json_encode(['erro' => 'ID da nota não informado']);
            exit;
        }
        // Buscar chave, empresa_id e cnpj na auditoria_notas
        $stmt = $this->db->prepare('SELECT chave, empresa_id, cnpj, numero, valor FROM auditoria_notas WHERE id = ?');
        $stmt->execute([$nota_id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$row) {
            echo json_encode(['erro' => 'Nota não encontrada']);
            exit;
        }
        $chave = $row['chave'];
        $empresa_id = $row['empresa_id'];
        $cnpj = $row['cnpj'];
        $numero = $row['numero'];
        $valor = $row['valor'];

        // Log para depuração
        error_log('CNPJ recebido da nota: ' . $cnpj);
        // Limpar o CNPJ para garantir que só tenha números
        $cnpjLimpo = preg_replace('/[^0-9]/', '', $cnpj);
        error_log('CNPJ limpo para busca: ' . $cnpjLimpo);
        // Buscar código interno do fornecedor na tabela de associação
        $sql = "SELECT ef.codigo_interno 
                FROM empresa_fornecedor ef
                JOIN fornecedores f ON ef.fornecedor_id = f.id
                WHERE ef.empresa_id = ? AND f.cnpj = ?";
        $stmtFornecedor = $this->db->prepare($sql);
        $stmtFornecedor->execute([$empresa_id, $cnpjLimpo]);
        $codigoFornecedor = $stmtFornecedor->fetchColumn();
        error_log('Resultado codigo_interno: ' . var_export($codigoFornecedor, true));

        // Buscar dados na tabela notas
        $stmt2 = $this->db->prepare('SELECT escriturador, data_entrada, coleta, codigo_fiscal, codigo_processo FROM notas WHERE chave_nota = ? AND empresa_id = ? LIMIT 1');
        $stmt2->execute([$chave, $empresa_id]);
        $dados = $stmt2->fetch(\PDO::FETCH_ASSOC);
        // Garante que $dados seja um array
        if (!$dados) {
            $dados = [];
        }
        
        // Buscar número financeiro na tabela arius_financeiro
        $numeroFinanceiro = null;
        if ($numero && $valor) {
            // Tentar buscar por número da nota e valor
            $stmtFinanceiro = $this->db->prepare('SELECT numero_finan FROM arius_financeiro WHERE nf = ? AND valor = ? AND empresa_id = ? LIMIT 1');
            $stmtFinanceiro->execute([$numero, $valor, $empresa_id]);
            $numeroFinanceiro = $stmtFinanceiro->fetchColumn();
            
            // Se não encontrou, tentar buscar apenas por número da nota
            if (!$numeroFinanceiro) {
                $stmtFinanceiro2 = $this->db->prepare('SELECT numero_finan FROM arius_financeiro WHERE nf = ? AND empresa_id = ? LIMIT 1');
                $stmtFinanceiro2->execute([$numero, $empresa_id]);
                $numeroFinanceiro = $stmtFinanceiro2->fetchColumn();
            }
        }
        
        // Adicionar o código do fornecedor aos dados
        $dados['codigo_fornecedor'] = $codigoFornecedor ?: 'Fornecedor não cadastrado';
        // Adicionar o número financeiro aos dados
        $dados['numero_financeiro'] = $numeroFinanceiro ?: '---';
        
        // Log para debug
        error_log('Retorno getNotaDetalhes: ' . json_encode($dados));
        echo json_encode($dados);
        exit;
    }

    public function getObservacao($id) {
        error_log('=== INÍCIO getObservacao ===');
        error_log('Entrou no getObservacao com id: ' . print_r($id, true));
        error_log('Tipo do ID: ' . gettype($id));
        error_log('Método HTTP: ' . $_SERVER['REQUEST_METHOD']);
        error_log('URL: ' . $_SERVER['REQUEST_URI']);
        
        // Teste simples primeiro
        try {
            error_log('Iniciando try/catch');
            if (is_array($id)) {
                error_log('ID recebido como array: ' . print_r($id, true));
                $id = isset($id['id']) ? $id['id'] : reset($id);
            }
            $id = (int)$id;
            error_log('Valor final de $id em getObservacao: ' . $id);

            // Verificar se a tabela existe
            $stmt = $this->db->query("SHOW TABLES LIKE 'observacoes_nota'");
            error_log('Verificação da tabela observacoes_nota - rowCount: ' . $stmt->rowCount());
            if ($stmt->rowCount() === 0) {
                error_log('Tabela observacoes_nota não existe!');
                throw new \Exception('Tabela de observações não existe');
            }
            
            // Verificar estrutura da tabela
            $stmt = $this->db->query("DESCRIBE observacoes_nota");
            $colunas = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            error_log('Estrutura da tabela observacoes_nota: ' . print_r($colunas, true));

            // Primeiro, vamos verificar se existem observações para esta nota
            $sqlCount = 'SELECT COUNT(*) as total FROM observacoes_nota WHERE nota_id = ?';
            $stmtCount = $this->db->prepare($sqlCount);
            $stmtCount->execute([$id]);
            $count = $stmtCount->fetch(\PDO::FETCH_ASSOC);
            error_log('Total de observações para nota ' . $id . ': ' . $count['total']);
            
            // Consulta simples sem JOIN primeiro
            $sql = 'SELECT id, observacao, data_hora, tipo_usuario, usuario_id FROM observacoes_nota WHERE nota_id = ? ORDER BY data_hora DESC';
            error_log('Executando SQL: ' . $sql . ' com ID: ' . $id);
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            error_log('Resultado da consulta simples: ' . print_r($rows, true));
            
            // Se encontrou dados, fazer o JOIN com usuários
            if (!empty($rows)) {
                $sql = 'SELECT o.id, o.observacao, o.data_hora, o.tipo_usuario, u.nome as nome_usuario 
                        FROM observacoes_nota o 
                        LEFT JOIN usuarios u ON o.usuario_id = u.id 
                        WHERE o.nota_id = ? 
                        ORDER BY o.data_hora DESC';
                error_log('Executando SQL com JOIN: ' . $sql . ' com ID: ' . $id);
                
                $stmt = $this->db->prepare($sql);
                $stmt->execute([$id]);
                $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                
                error_log('Resultado da consulta com JOIN: ' . print_r($rows, true));
            }
            
            error_log('Resultado final: ' . json_encode(['observacoes' => $rows]));
            echo json_encode(['observacoes' => $rows]);
        } catch (\Exception $e) {
            error_log('Erro em getObservacao: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
            http_response_code(500);
            echo json_encode(['erro' => 'Erro ao buscar observação: ' . $e->getMessage()]);
        }
        error_log('=== FIM getObservacao ===');
        exit;
    }

    // Método de teste para verificar se a rota está funcionando
    public function testeObservacao($id) {
        error_log('=== TESTE OBSERVAÇÃO ===');
        error_log('ID recebido: ' . $id);
        echo json_encode(['teste' => 'ok', 'id' => $id]);
        exit;
    }

    public function salvarObservacao($id) {
        error_log("ENTROU NO MÉTODO salvarObservacao");
        error_log("Método salvarObservacao iniciado");
        try {
            if (is_array($id)) {
                error_log('ID recebido como array: ' . print_r($id, true) . "\n", 3, $this->logFile);
                $id = isset($id['id']) ? $id['id'] : reset($id);
            }
            $id = (int)$id;
            error_log('Valor final de $id em salvarObservacao: ' . $id . "\n", 3, $this->logFile);

            try {
                $json = file_get_contents('php://input');
                error_log('Payload recebido: ' . $json . "\n", 3, $this->logFile);
                $data = json_decode($json, true);

                if (!isset($data['observacao'])) {
                    error_log('Observação não fornecida\n', 3, $this->logFile);
                    throw new \Exception('Observação não fornecida');
                }

                $observacao = trim($data['observacao']);
                if ($observacao === '') {
                    error_log('Observação vazia\n', 3, $this->logFile);
                    throw new \Exception('Observação não pode ser vazia');
                }

                // Verificar se o usuário está logado
                if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['tipo'])) {
                    error_log('Usuário não autenticado. Sessão: ' . print_r($_SESSION, true) . "\n", 3, $this->logFile);
                    throw new \Exception('Usuário não autenticado');
                }

                $usuario_id = $_SESSION['usuario_id'];
                $tipo_usuario = $_SESSION['tipo'];

                error_log("Preparando para inserir: nota_id=$id, usuario_id=$usuario_id, tipo_usuario=$tipo_usuario, observacao=$observacao\n", 3, $this->logFile);

                // Inserir nova observação
                $stmt = $this->db->prepare('INSERT INTO observacoes_nota (nota_id, usuario_id, observacao, tipo_usuario) VALUES (?, ?, ?, ?)');
                $stmt->execute([$id, $usuario_id, $observacao, $tipo_usuario]);
                
                $observacao_id = $this->db->lastInsertId();
                error_log("Observação inserida com sucesso! ID: $observacao_id\n", 3, $this->logFile);

                // Criar notificação de comentário
                $this->criarNotificacaoComentario($observacao_id, $id, $usuario_id, $tipo_usuario);

                echo json_encode(['success' => true]);
            } catch (\Exception $e) {
                error_log('Erro ao salvar observação: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString() . "\n", 3, $this->logFile);
                http_response_code(500);
                echo json_encode([
                    'erro' => 'Erro ao salvar observação: ' . $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        } catch (\Exception $e) {
            error_log("Erro no método salvarObservacao: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
        error_log("==== FIM salvarObservacao ====\n", 3, $this->logFile);
        exit;
    }

    private function criarNotificacaoComentario($observacao_id, $nota_id, $usuario_origem_id, $tipo_usuario) {
        try {
            // Determinar quem deve receber a notificação
            if ($tipo_usuario == 3 || $tipo_usuario === 'operator' || $tipo_usuario === '3') {
                // Se operador inseriu, notificar admin
                // Buscar um admin disponível (diferente do usuário atual)
                $stmt = $this->db->prepare('SELECT id FROM usuarios WHERE (tipo != 3 AND tipo != "operator" AND tipo != "3") AND id != ? LIMIT 1');
                $stmt->execute([$usuario_origem_id]);
                $admin = $stmt->fetch(\PDO::FETCH_ASSOC);
                
                if ($admin) {
                    $usuario_destino_id = $admin['id'];
                } else {
                    error_log("Nenhum admin encontrado para notificar\n", 3, $this->logFile);
                    return;
                }
            } else {
                // Se admin inseriu, notificar o operador que fez a observação original
                // Buscar o operador que fez a primeira observação nesta nota
                $stmt = $this->db->prepare('
                    SELECT DISTINCT on_obs.usuario_id 
                    FROM observacoes_nota on_obs 
                    WHERE on_obs.nota_id = ? 
                        AND on_obs.usuario_id != ? 
                        AND (on_obs.tipo_usuario = 3 OR on_obs.tipo_usuario = "operator" OR on_obs.tipo_usuario = "3")
                    ORDER BY on_obs.data_hora ASC 
                    LIMIT 1
                ');
                $stmt->execute([$nota_id, $usuario_origem_id]);
                $operador = $stmt->fetch(\PDO::FETCH_ASSOC);
                
                if ($operador) {
                    $usuario_destino_id = $operador['usuario_id'];
                } else {
                    error_log("Nenhum operador encontrado para notificar na nota $nota_id\n", 3, $this->logFile);
                    return;
                }
            }
            
            // Inserir notificação
            $stmt = $this->db->prepare('INSERT INTO notificacoes_comentarios (observacao_id, nota_id, usuario_origem_id, usuario_destino_id) VALUES (?, ?, ?, ?)');
            $stmt->execute([$observacao_id, $nota_id, $usuario_origem_id, $usuario_destino_id]);
            
            error_log("Notificação de comentário criada: origem=$usuario_origem_id, destino=$usuario_destino_id\n", 3, $this->logFile);
            
        } catch (\Exception $e) {
            error_log("Erro ao criar notificação de comentário: " . $e->getMessage() . "\n", 3, $this->logFile);
        }
    }

    public function danfe($chave) {
        // Se vier como array, pega o valor da chave
        if (is_array($chave)) {
            $chave = $chave['chave'] ?? reset($chave);
        }
        $chave = preg_replace('/[^0-9]/', '', $chave);
        if (!$chave || strlen($chave) !== 44) {
            http_response_code(400);
            echo 'Chave de acesso inválida.';
            exit;
        }
        // 1. Preparar os dados da nota
        $urlPreparar = "https://ws.meudanfe.com/api/v1/get/nfe/data/MEUDANFE/{$chave}";
        $optsPreparar = [
            'http' => [
                'method' => 'POST',
                'header' => "Content-Type: text/plain\r\n",
                'content' => 'empty',
                'timeout' => 30
            ]
        ];
        $contextPreparar = stream_context_create($optsPreparar);
        $resultPreparar = @file_get_contents($urlPreparar, false, $contextPreparar);
        if ($resultPreparar === false) {
            http_response_code(502);
            echo 'Erro ao preparar a nota na API da DANFE.';
            exit;
        }
        // 2. Baixar o PDF
        $url = "https://ws.meudanfe.com/api/v1/get/nfe/danfepdf/{$chave}";
        $opts = [
            'http' => [
                'method' => 'POST',
                'header' => "Content-Type: text/plain\r\n",
                'content' => 'empty',
                'timeout' => 30
            ]
        ];
        $context = stream_context_create($opts);
        $result = @file_get_contents($url, false, $context);
        if ($result === false) {
            http_response_code(502);
            echo 'Erro ao consultar a API da DANFE.';
            exit;
        }
        $result = trim($result, '"');
        if (strpos($result, 'JVBERi') !== 0) {
            http_response_code(500);
            echo 'A API não retornou um PDF válido.';
            exit;
        }
        $pdf = base64_decode($result);
        if ($pdf === false) {
            http_response_code(500);
            echo 'Erro ao decodificar o PDF.';
            exit;
        }
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="DANFE-' . $chave . '.pdf"');
        header('Content-Length: ' . strlen($pdf));
        echo $pdf;
        exit;
    }

    private function gerarCSV($cabecalho, $dados, $nome_arquivo)
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $nome_arquivo . '"');
        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM para UTF-8
        fputcsv($output, $cabecalho, ';');
        foreach ($dados as $linha) {
            fputcsv($output, $linha, ';');
        }
        fclose($output);
        exit;
    }

    private function gerarXLSX($cabecalho, $dados)
    {
        require_once __DIR__ . '/../../vendor/autoload.php';
        
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Adicionar logo
        if (file_exists(__DIR__ . '/../../public/assets/images/logo.png')) {
            $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
            $drawing->setName('Logo');
            $drawing->setDescription('Logo');
            $drawing->setPath(__DIR__ . '/../../public/assets/images/logo.png');
            $drawing->setHeight(50);
            $drawing->setCoordinates('A1');
            $drawing->setWorksheet($sheet);
        }
        
        // Adicionar título
        $sheet->setCellValue('A2', 'Relatório de Auditoria');
        $sheet->mergeCells('A2:' . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($cabecalho)) . '2');
        
        // Adicionar cabeçalho
        $col = 1;
        foreach ($cabecalho as $valor) {
            $sheet->setCellValueByColumnAndRow($col, 4, $valor);
            $col++;
        }
        
        // Adicionar dados
        $row = 5;
        foreach ($dados as $linha) {
            $col = 1;
            foreach ($linha as $valor) {
                $sheet->setCellValueByColumnAndRow($col, $row, $valor);
                $col++;
            }
            $row++;
        }
        
        // Estilizar
        $sheet->getStyle('A4:' . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($cabecalho)) . '4')
            ->getFont()->setBold(true);
        $sheet->getStyle('A2')
            ->getFont()->setBold(true)
            ->setSize(14);
        
        // Autoajustar largura das colunas
        foreach (range(1, count($cabecalho)) as $col) {
            $sheet->getColumnDimensionByColumn($col)->setAutoSize(true);
        }
        
        // Configurar cabeçalhos
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="relatorio_auditoria_' . date('Y-m-d') . '.xlsx"');
        header('Cache-Control: max-age=0');
        
        // Gerar arquivo
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    private function gerarPDF($cabecalho, $dados, $nome_arquivo)
    {
        require_once __DIR__ . '/../../vendor/autoload.php';
        $pdf = new \TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('MDE Ágil Fiscal');
        $pdf->SetTitle('Relatório de Auditoria');
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->AddPage();

        // Título
        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->Cell(0, 10, 'Relatório de Auditoria', 0, 1, 'C');
        $pdf->Ln(5);

        // Calcular largura das células para caber na página
        $numCols = count($cabecalho);
        $margins = $pdf->getMargins();
        $pageWidth = $pdf->getPageWidth() - $margins['left'] - $margins['right'];
        $cellWidth = $pageWidth / $numCols;

        // Cabeçalho da tabela
        $pdf->SetFont('helvetica', 'B', 9);
        foreach ($cabecalho as $col) {
            $pdf->Cell($cellWidth, 7, $col, 1, 0, 'C');
        }
        $pdf->Ln();

        // Dados
        $pdf->SetFont('helvetica', '', 8);
        foreach ($dados as $linha) {
            foreach ($linha as $col) {
                $pdf->Cell($cellWidth, 6, (string)$col, 1, 0, 'C');
            }
            $pdf->Ln();
        }

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $nome_arquivo . '"');
        $pdf->Output($nome_arquivo, 'D');
        exit;
    }

    public function gerarRelatorio()
    {
        try {
            $formato = $_GET['formato'] ?? 'csv';
            $data_inicial = $_GET['data_inicial'] ?? null;
            $data_final = $_GET['data_final'] ?? null;
            $status = $_GET['status'] ?? 'todos';
            $empresa_id = $_GET['empresa_id'] ?? null;
            $busca = $_GET['busca'] ?? null;

            // Buscar dados da empresa selecionada
            $empresa = $empresa_id ? $this->empresaModel->find($empresa_id) : null;

            // Buscar todas as notas filtradas, ordenadas da mais antiga para a mais nova
            $resultado = $this->auditoriaNotaModel->historicoComDestino(
                $empresa_id,
                1875,
                $data_inicial,
                $data_final,
                $busca,
                1, // página
                10000, // por_pagina (ajuste conforme necessário)
                $status
            );
            $notas = $resultado['notas'] ?? [];
            // Calcular status_lancamento para cada nota (igual à tela)
            $empresaCnpjLimpo = null;
            if ($empresa_id) {
                $empresaSelecionada = $this->empresaModel->find($empresa_id);
                if ($empresaSelecionada && isset($empresaSelecionada['cnpj'])) {
                    $empresaCnpjLimpo = preg_replace('/[^0-9]/', '', $empresaSelecionada['cnpj']);
                }
            }
            foreach ($notas as &$nota) {
                $chave = isset($nota['chave']) ? trim(str_replace("'", '', $nota['chave'])) : '';
                $notaCnpjLimpo = isset($nota['cnpj']) ? preg_replace('/[^0-9]/', '', $nota['cnpj']) : '';
                // Cancelada tem prioridade máxima
                if (isset($nota['situacao']) && mb_strtolower($nota['situacao']) === 'cancelada') {
                    $nota['status_lancamento'] = 'Cancelada';
                    continue;
                }
                // Se o CNPJ da nota for igual ao da empresa selecionada, status Própria
                if ($empresaCnpjLimpo && $notaCnpjLimpo && $empresaCnpjLimpo === $notaCnpjLimpo) {
                    $nota['status_lancamento'] = 'Própria';
                    continue;
                }
                $sql = "SELECT 
                    CASE 
                        WHEN EXISTS (
                            SELECT 1 
                            FROM romaneio r 
                            WHERE r.empresa_id = ? 
                            AND TRIM(REPLACE(r.chave, '\'', '')) = ?
                        ) THEN 'Romaneio'
                        WHEN EXISTS (
                            SELECT 1 
                            FROM notas n 
                            WHERE n.empresa_id = ? 
                            AND n.hash = ? 
                            AND n.hash IS NOT NULL
                            AND n.hash != ''
                            AND n.hash = TRIM(?)
                        ) THEN 'Lançada'
                        WHEN EXISTS (
                            SELECT 1 
                            FROM desconhecimento d 
                            WHERE d.empresa_id = ? 
                            AND TRIM(REPLACE(d.chave, '\'', '')) = ?
                        ) THEN 'Desconhecimento'
                        WHEN EXISTS (
                            SELECT 1 
                            FROM empresas e 
                            WHERE e.cnpj = ? 
                            AND (
                                e.tipo_empresa = 'filial'
                                
                            )
                        ) THEN 'Transferência'
                        ELSE 'Pendente'
                    END as status";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([
                    $empresa_id, $chave,
                    $empresa_id, $nota['hash'], $nota['hash'],
                    $empresa_id, $chave,
                    $nota['cnpj']
                ]);
                $resultadoStatus = $stmt->fetch(\PDO::FETCH_ASSOC);
                $nota['status_lancamento'] = $resultadoStatus['status'];
            }
            unset($nota);
            // --- LÓGICA PARA NOTAS ANULADAS ---
            // 1. Separar notas pendentes de saída e notas de entrada
            $notasPendentesSaida = [];
            $notasEntrada = [];
            foreach ($notas as $idx => $nota) {
                if (
                    isset($nota['status_lancamento']) && $nota['status_lancamento'] === 'Pendente' &&
                    (empty($nota['tipo']) || mb_strtolower($nota['tipo']) !== 'entrada')
                ) {
                    $notasPendentesSaida[$idx] = $nota;
                } elseif (
                    isset($nota['tipo']) && mb_strtolower($nota['tipo']) === 'entrada' &&
                    isset($nota['status_lancamento']) && $nota['status_lancamento'] !== 'Própria'
                ) {
                    $notasEntrada[$idx] = $nota;
                }
            }
            // 2. Para cada nota de entrada, procurar a(s) saída(s) correspondente(s)
            $anuladasIdx = [];
            foreach ($notasEntrada as $idxEntrada => $entrada) {
                $fornecedorEntrada = preg_replace('/[^0-9]/', '', $entrada['cnpj'] ?? '');
                $valorEntrada = floatval(str_replace([',', 'R$', ' '], ['.', '', ''], $entrada['valor'] ?? 0));
                $dataEntrada = isset($entrada['data_emissao']) ? strtotime($entrada['data_emissao']) : null;
                // Filtrar notas de saída pendentes do mesmo fornecedor, mesmo valor, data menor ou igual
                $candidatas = [];
                foreach ($notasPendentesSaida as $idxSaida => $saida) {
                    $fornecedorSaida = preg_replace('/[^0-9]/', '', $saida['cnpj'] ?? '');
                    $valorSaida = floatval(str_replace([',', 'R$', ' '], ['.', '', ''], $saida['valor'] ?? 0));
                    $dataSaida = isset($saida['data_emissao']) ? strtotime($saida['data_emissao']) : null;
                    if (
                        $fornecedorEntrada === $fornecedorSaida &&
                        abs($valorEntrada - $valorSaida) < 0.01 && // tolerância centavos
                        $dataSaida !== null && $dataEntrada !== null &&
                        $dataSaida <= $dataEntrada
                    ) {
                        $candidatas[$idxSaida] = [
                            'diff' => abs($dataEntrada - $dataSaida),
                            'data' => $dataSaida
                        ];
                    }
                }
                if (!empty($candidatas)) {
                    // Seleciona a saída com data mais próxima da entrada
                    uasort($candidatas, function($a, $b) {
                        if ($a['diff'] === $b['diff']) return $b['data'] <=> $a['data']; // mais recente
                        return $a['diff'] <=> $b['diff'];
                    });
                    $idxAnulada = array_key_first($candidatas);
                    $anuladasIdx[] = $idxAnulada;
                    // Remove para não anular mais de uma vez
                    unset($notasPendentesSaida[$idxAnulada]);
                }
            }
            // 3. Marcar as notas anuladas
            foreach ($anuladasIdx as $idx) {
                $notas[$idx]['status_lancamento'] = 'Anulada';
            }
            // --- LÓGICA PARA NOTAS DE TRANSFERÊNCIA (igual ao dashboard) ---
            // Só faz sentido se a empresa selecionada fizer parte de um grupo (matriz/filial) e houver mais de uma empresa no grupo
            $empresaSelecionadaObj = null;
            if (!empty($empresa_id) && !empty($empresas)) {
                foreach ($empresas as $emp) {
                    if ($emp['id'] == $empresa_id) {
                        $empresaSelecionadaObj = $emp;
                        break;
                    }
                }
            }
            if ($empresaSelecionadaObj && \App\Models\Empresa::isGrupo($empresaSelecionadaObj) && !empty($empresa_id) && !empty($empresas) && count($empresas) > 1) {
                $empresaSelecionada = null;
                $grupoCnpjs = [];
                foreach ($empresas as $emp) {
                    $cnpjLimpo = preg_replace('/[^0-9]/', '', $emp['cnpj']);
                    if ($emp['id'] == $empresa_id) {
                        $empresaSelecionada = $cnpjLimpo;
                    }
                    $grupoCnpjs[] = $cnpjLimpo;
                }
                if ($empresaSelecionada && count($grupoCnpjs) > 1) {
                    foreach ($notas as &$nota) {
                        $notaCnpjLimpo = isset($nota['cnpj']) ? preg_replace('/[^0-9]/', '', $nota['cnpj']) : '';
                        if (
                            in_array($notaCnpjLimpo, $grupoCnpjs)
                            && $notaCnpjLimpo !== $empresaSelecionada
                        ) {
                            $nota['status_lancamento'] = 'Transferência';
                        }
                    }
                    unset($nota);
                }
            }
            // Aplicar filtro por status em PHP (igual à tela)
            if ($status && $status !== 'todos') {
                if ($status === 'Pendente') {
                    $notas = array_filter($notas, function($nota) {
                        return $nota['status_lancamento'] !== 'Lançada'
                            && $nota['situacao'] !== 'Cancelada'
                            && $nota['tipo'] !== 'Entrada'
                            && $nota['status_lancamento'] !== 'Transferência'
                            && $nota['status_lancamento'] !== 'Desconhecimento'
                            && $nota['status_lancamento'] !== 'Romaneio'
                            && $nota['status_lancamento'] !== 'Própria'
                            && $nota['status_lancamento'] !== 'Anulada'; // Não incluir Anulada como pendente
                    });
                } elseif ($status === 'Cancelada') {
                    $notas = array_filter($notas, function($nota) {
                        return $nota['situacao'] === 'Cancelada';
                    });
                } elseif ($status === 'Entrada') {
                    $notas = array_filter($notas, function($nota) {
                        return (
                            isset($nota['tipo']) && mb_strtolower($nota['tipo']) === 'entrada'
                            && mb_strtolower($nota['status_lancamento']) !== 'cancelada'
                            && mb_strtolower($nota['status_lancamento']) !== 'transferência'
                            && mb_strtolower($nota['status_lancamento']) !== 'desconhecimento'
                            && mb_strtolower($nota['status_lancamento']) !== 'própria'
                        );
                    });
                } else {
                    $notas = array_filter($notas, function($nota) use ($status) {
                        $statusLanc = mb_strtolower(removerAcentos($nota['status_lancamento']));
                        $statusFiltro = mb_strtolower(removerAcentos($status));
                        if ($statusFiltro === 'transferencia') {
                            return $statusLanc === $statusFiltro && (!isset($nota['situacao']) || mb_strtolower($nota['situacao']) !== 'cancelada');
                        }
                        return $statusLanc === $statusFiltro;
                    });
                }
            }
            // Ordenar por data_emissao DESC (mais novo no topo)
            usort($notas, function($a, $b) {
                return strtotime($b['data_emissao'] ?? '1970-01-01') <=> strtotime($a['data_emissao'] ?? '1970-01-01');
            });
            // Não paginar: exportar todas as notas filtradas
            $total_notas = count($notas);

            // Se não houver empresa ou notas, mantém a simulação para não quebrar a view
            if (empty($notas)) {
                $notas = [];
            }
            // Filtra apenas notas válidas (array e com id)
            $notas = array_filter($notas, function($n) {
                return is_array($n) && isset($n['id']);
            });

            // Cabeçalho fixo da empresa emissora
            $emissora = [
                'nome' => 'Agil Fiscal Soluções Tributárias',
                'cnpj' => '22.754.722/0001-74',
                'endereco' => 'Edifício Hangar Business Park',
                'endereco2' => 'Av. Luís Viana Filho, 13223 - São Cristóvão, Salvador - BA',
                'contato' => '(71) 99290-4366 | contato@agilfiscal.com.br',
            ];

            // Cabeçalho das colunas (exceto Detalhes)
            $cabecalho = [
                'N° NFe', 'CNPJ', 'Razão Social', 'Emissão', 'Valor', 'Chave', 'UF', 'Situação', 'Tipo', 'Destino', 'Vencimento(s)', 'Status'
            ];
            // Buscar destinos
            $destinos = $this->db->query('SELECT id, destino FROM destino')->fetchAll(\PDO::FETCH_ASSOC);
            $destinosMap = [];
            foreach ($destinos as $d) {
                $destinosMap[$d['id']] = $d['destino'];
            }
            // Montar dados
            $dados = [];
            foreach ($notas as $n) {
                // Buscar destino_id se não estiver presente
                if (!isset($n['destino_id'])) {
                    $stmtDest = $this->db->prepare('SELECT destino_id FROM auditoria_destino WHERE nota_id = ? LIMIT 1');
                    $stmtDest->execute([$n['id']]);
                    $n['destino_id'] = $stmtDest->fetchColumn();
                }
                // Buscar destino
                $destino = '';
                if (isset($n['destino_id']) && $n['destino_id'] && isset($destinosMap[$n['destino_id']])) {
                    $destino = $destinosMap[$n['destino_id']];
                }
                // Buscar vencimentos
                $vencimentos = '';
                $stmtVenc = $this->db->prepare('SELECT data_vencimento FROM auditoria_notas_vencimentos WHERE nota_id = ? ORDER BY data_vencimento');
                $stmtVenc->execute([$n['id']]);
                $vencs = $stmtVenc->fetchAll(\PDO::FETCH_COLUMN);
                if ($vencs) {
                    $vencimentos = implode(' | ', array_map(function($d) {
                        return $d ? date('d/m/Y', strtotime($d)) : '';
                    }, $vencs));
                }
                $dados[] = [
                    $n['numero'] ?? '',
                    $n['cnpj'] ?? '',
                    $n['razao_social'] ?? '',
                    isset($n['data_emissao']) ? date('d/m/Y', strtotime($n['data_emissao'])) : '',
                    isset($n['valor']) ? 'R$ ' . number_format($n['valor'], 2, ',', '.') : '',
                    $n['chave'] ?? '',
                    $n['uf'] ?? '',
                    $n['situacao'] ?? '',
                    $n['tipo'] ?? '',
                    $destino,
                    $vencimentos,
                    $n['status_lancamento'] ?? ''
                ];
            }
            // Definir nome do arquivo
            $status_nome = $status && $status !== 'todos' ? $status : 'Todos';
            $datahora_nome = date('d-m-Y H-i-s');
            $nome_arquivo_base = "Relatório - $status_nome - $datahora_nome";
            // Gerar relatório no formato escolhido
            switch ($formato) {
                case 'csv':
                    $this->gerarCSV($cabecalho, $dados, $nome_arquivo_base . '.csv');
                    break;
                case 'xlsx':
                    $this->gerarXLSX($cabecalho, $dados);
                    break;
                case 'pdf':
                    $this->gerarPDF($cabecalho, $dados, $nome_arquivo_base . '.pdf');
                    break;
                case 'txt':
                    $this->gerarTXT($cabecalho, $dados, $nome_arquivo_base . '.txt');
                    break;
                default:
                    throw new \Exception('Formato de relatório inválido');
            }
        } catch (\Exception $e) {
            error_log('Erro ao gerar relatório: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            exit;
        }
    }

    // Adicionar função gerarTXT
    private function gerarTXT($cabecalho, $dados, $nome_arquivo)
    {
        header('Content-Type: text/plain; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $nome_arquivo . '"');
        $linhaCab = implode("\t", $cabecalho) . "\n";
        echo $linhaCab;
        foreach ($dados as $linha) {
            echo implode("\t", $linha) . "\n";
        }
        exit;
    }

    public function baixarXml($chave) {
        // Se vier como array, pega o valor da chave
        if (is_array($chave)) {
            $chave = $chave['chave'] ?? reset($chave);
        }
        $chave = preg_replace('/[^0-9]/', '', $chave);
        if (!$chave || strlen($chave) !== 44) {
            http_response_code(400);
            echo 'Chave de acesso inválida.';
            exit;
        }
        // 1. Preparar os dados da nota
        $urlPreparar = "https://ws.meudanfe.com/api/v1/get/nfe/data/MEUDANFE/{$chave}";
        $optsPreparar = [
            'http' => [
                'method' => 'POST',
                'header' => "Content-Type: text/plain\r\n",
                'content' => 'empty',
                'timeout' => 30
            ]
        ];
        $contextPreparar = stream_context_create($optsPreparar);
        $resultPreparar = @file_get_contents($urlPreparar, false, $contextPreparar);
        if ($resultPreparar === false) {
            http_response_code(502);
            echo 'Erro ao preparar a nota na API da DANFE.';
            exit;
        }
        // 2. Baixar o XML
        $url = "https://ws.meudanfe.com/api/v1/get/nfe/xml/{$chave}";
        $opts = [
            'http' => [
                'method' => 'POST',
                'header' => "Content-Type: text/plain\r\n",
                'content' => 'empty',
                'timeout' => 30
            ]
        ];
        $context = stream_context_create($opts);
        $result = @file_get_contents($url, false, $context);
        if ($result === false) {
            http_response_code(502);
            echo 'Erro ao consultar a API do XML da DANFE.';
            exit;
        }
        // Forçar download do XML
        header('Content-Type: application/xml');
        header('Content-Disposition: attachment; filename="NFE-' . $chave . '.xml"');
        header('Content-Length: ' . strlen($result));
        echo $result;
        exit;
    }
} 