<?php

namespace App\Controllers;

class ExtrasController extends Controller {
    
    public function consultarCnpj() {
        $content = view('extras/consultar-cnpj', [
            'title' => 'Consultar CNPJ'
        ], true);
        require APP_ROOT . '/app/Views/layouts/main.php';
    }
    
    public function buscarCnpj() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $cnpj = preg_replace('/[^0-9]/', '', $_POST['cnpj'] ?? '');
            
            if (strlen($cnpj) !== 14) {
                echo json_encode(['erro' => 'CNPJ inválido']);
                return;
            }
            
            // Aqui você deve implementar a chamada para a API do CNPJa
            // Por enquanto, vou simular uma resposta baseada no arquivo JSON existente
            $resultado = $this->consultarCnpjApi($cnpj);
            
            header('Content-Type: application/json');
            echo json_encode($resultado);
        }
    }
    
    private function consultarCnpjApi($cnpj) {
        // Usa a API pública do CNPJa (sem necessidade de chave)
        $apiUrl = 'https://open.cnpja.com/office/' . $cnpj;
        
        // Inicializa o cURL
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $apiUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_USERAGENT => 'AgilFiscal/1.0'
        ]);
        
        // Executa a requisição
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        // Verifica se houve erro na requisição
        if ($error) {
            error_log("Erro na consulta CNPJa: " . $error);
            return [
                'sucesso' => false,
                'erro' => 'Erro de conexão com a API: ' . $error
            ];
        }
        
        // Verifica o código de resposta HTTP
        if ($httpCode !== 200) {
            error_log("Erro HTTP na consulta CNPJa: " . $httpCode . " - " . $response);
            
            // Trata erros específicos
            switch ($httpCode) {
                case 404:
                    return [
                        'sucesso' => false,
                        'erro' => 'CNPJ não encontrado na base de dados'
                    ];
                case 429:
                    return [
                        'sucesso' => false,
                        'erro' => 'Limite de consultas excedido. Tente novamente em alguns minutos'
                    ];
                default:
                    return [
                        'sucesso' => false,
                        'erro' => 'Erro na consulta: HTTP ' . $httpCode
                    ];
            }
        }
        
        // Decodifica a resposta JSON
        $dados = json_decode($response, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("Erro ao decodificar JSON da API CNPJa: " . json_last_error_msg());
            return [
                'sucesso' => false,
                'erro' => 'Erro ao processar resposta da API'
            ];
        }
        
        // Verifica se a resposta contém dados válidos
        if (!isset($dados['taxId']) || !isset($dados['company']['name'])) {
            return [
                'sucesso' => false,
                'erro' => 'Dados inválidos retornados pela API'
            ];
        }
        
        // Retorna os dados formatados
        return [
            'sucesso' => true,
            'dados' => $dados
        ];
    }
    

    
    // Métodos para outras funcionalidades extras
    public function relatorios() {
        $content = view('extras/relatorios', [
            'title' => 'Relatórios'
        ], true);
        require APP_ROOT . '/app/Views/layouts/main.php';
    }
    
    public function importacao() {
        $content = view('extras/importacao', [
            'title' => 'Calculadora de Preço'
        ], true);
        require APP_ROOT . '/app/Views/layouts/main.php';
    }
    
    public function exportacao() {
        $content = view('extras/exportacao', [
            'title' => 'Placas'
        ], true);
        require APP_ROOT . '/app/Views/layouts/main.php';
    }
    
    public function backup() {
        $content = view('extras/backup', [
            'title' => 'Backup'
        ], true);
        require APP_ROOT . '/app/Views/layouts/main.php';
    }
    
    public function logs() {
        $content = view('extras/logs', [
            'title' => 'Logs do Sistema'
        ], true);
        require APP_ROOT . '/app/Views/layouts/main.php';
    }
    
    public function utilitarios() {
        $content = view('extras/utilitarios', [
            'title' => 'Utilitários'
        ], true);
        require APP_ROOT . '/app/Views/layouts/main.php';
    }
    
    public function calculadoraPreco() {
        $content = view('extras/calculadora-preco', [
            'title' => 'Calculadora de Margem e Preço'
        ], true);
        require APP_ROOT . '/app/Views/layouts/main.php';
    }
    
    public function calcularPreco() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $dados = $_POST;
            
            // Validação básica
            if (empty($dados['preco_compra']) || empty($dados['margem_desejada']) || empty($dados['regime_tributacao'])) {
                echo json_encode(['erro' => 'Preço de compra, margem desejada e regime de tributação são obrigatórios']);
                return;
            }
            
            // Validação para Lucro Real
            if ($dados['regime_tributacao'] === 'lucro_real') {
                $camposObrigatorios = [
                    'pis_cofins_venda_pct' => 'PIS/COFINS venda',
                    'icms_venda_pct' => 'ICMS venda',
                    'pis_cofins_compra_pct' => 'Crédito PIS/COFINS',
                    'icms_compra_pct' => 'Crédito ICMS'
                ];
                
                foreach ($camposObrigatorios as $campo => $nome) {
                    if (!isset($dados[$campo]) || $dados[$campo] === '') {
                        echo json_encode(['erro' => "Para o regime Lucro Real, o campo '$nome' é obrigatório"]);
                        return;
                    }
                }
            }
            
            $resultado = $this->calcularPrecoVenda($dados);
            
            header('Content-Type: application/json');
            echo json_encode($resultado);
        }
    }
    
    private function calcularPrecoVenda($dados) {
        try {
            // Converte valores para float
            $precoCompra = floatval(str_replace(['R$', '.', ','], ['', '', '.'], $dados['preco_compra']));
            $margemDesejada = floatval(str_replace(['%', ','], ['', '.'], $dados['margem_desejada']));
            
            // Usar o CMV calculado automaticamente pelo JavaScript
            $cmv = floatval(str_replace(['R$', '.', ','], ['', '', '.'], $dados['cmv'] ?? '0'));
            
            // Valores padrão para impostos se não informados
            $pisCofinsCompra = floatval(str_replace(['%', ','], ['', '.'], $dados['pis_cofins_compra_pct'] ?? '0'));
            $icmsCompra = floatval(str_replace(['%', ','], ['', '.'], $dados['icms_compra_pct'] ?? '0'));
            $reducaoBaseCompra = floatval(str_replace(['%', ','], ['', '.'], $dados['reducao_base_compra'] ?? '0'));
            $icmsSt = floatval(str_replace(['%', ','], ['', '.'], $dados['icms_st_pct'] ?? '0'));
            $ipi = floatval(str_replace(['%', ','], ['', '.'], $dados['ipi_pct'] ?? '0'));
            
            $pisCofinsVenda = floatval(str_replace(['%', ','], ['', '.'], $dados['pis_cofins_venda_pct'] ?? '0'));
            $icmsVenda = floatval(str_replace(['%', ','], ['', '.'], $dados['icms_venda_pct'] ?? '0'));
            $reducaoBaseVenda = floatval(str_replace(['%', ','], ['', '.'], $dados['reducao_base_venda'] ?? '0'));
            $reducaoBaseSt = floatval(str_replace(['%', ','], ['', '.'], $dados['reducao_base_st'] ?? '0'));
            
            // Se o CMV não foi enviado, calcular automaticamente
            if ($cmv == 0) {
                // Cálculo dos Créditos Tributários na Compra
                $creditoPisCofins = ($precoCompra * $pisCofinsCompra) / 100;
                $creditoIcms = ($precoCompra * $icmsCompra) / 100;
                
                // Cálculo do CMV = Preço de Compra - Créditos Tributários
                $cmv = $precoCompra - $creditoPisCofins - $creditoIcms;
                
                // Adicionar ICMS ST e IPI se houver
                if ($icmsSt > 0) {
                    $baseCalculoSt = $precoCompra - $creditoPisCofins - $creditoIcms;
                    $baseCalculoSt = $baseCalculoSt * (1 - $reducaoBaseCompra / 100);
                    $baseCalculoSt = $baseCalculoSt * (1 - $reducaoBaseSt / 100);
                    $icmsStValor = ($baseCalculoSt * $icmsSt) / 100;
                    $cmv += $icmsStValor;
                }
                
                if ($ipi > 0) {
                    $ipiValor = ($precoCompra * $ipi) / 100;
                    $cmv += $ipiValor;
                }
            }
            
            // Cálculo do Preço de Venda baseado no regime
            if ($dados['regime_tributacao'] === 'simples_nacional') {
                // Para Simples Nacional: Preço de Venda = CMV / (1 - Margem Desejada - Simples)
                $simplesPct = floatval(str_replace(['%', ','], ['', '.'], $dados['simples_pagar_pct'] ?? '0'));
                $precoVenda = $cmv / (1 - ($margemDesejada + $simplesPct) / 100);
            } else {
                // Para outros regimes: Preço de Venda = CMV / (1 - Margem Desejada - Σ Alíquotas de Impostos na Venda)
                // Calcular ICMS efetivo considerando a redução da base de cálculo
                $icmsEfetivo = $icmsVenda;
                if ($reducaoBaseVenda > 0) {
                    $icmsEfetivo = $icmsVenda * (1 - $reducaoBaseVenda / 100);
                }
                $precoVenda = $cmv / (1 - ($margemDesejada + $pisCofinsVenda + $icmsEfetivo) / 100);
            }
            
            // Recalcular impostos sobre o preço final
            $icmsPagar = ($precoVenda * $icmsVenda) / 100;
            $pisCofinsPagar = ($precoVenda * $pisCofinsVenda) / 100;
            
            // Cálculos finais
            $icmsVendaValor = ($precoVenda * $icmsVenda) / 100;
            $pisCofinsVendaValor = ($precoVenda * $pisCofinsVenda) / 100;
            
            // Aplicar redução da base de cálculo de saída ao ICMS venda
            if ($reducaoBaseVenda > 0) {
                $reducaoIcmsVenda = $icmsVendaValor * ($reducaoBaseVenda / 100);
                $icmsVendaValor = $icmsVendaValor - $reducaoIcmsVenda;
            }
            
            // Calcular créditos da compra
            $creditoPisCofins = ($precoCompra * $pisCofinsCompra) / 100;
            $creditoIcms = ($precoCompra * $icmsCompra) / 100;
            
            // Impostos a pagar = Impostos da venda - Créditos da compra
            $icmsPagar = $icmsVendaValor - $creditoIcms;
            $pisCofinsPagar = $pisCofinsVendaValor - $creditoPisCofins;
            
            // Calcular ICMS ST e IPI para o fornecedor a pagar
            $icmsStValor = 0;
            $ipiValor = 0;
            
            if ($icmsSt > 0) {
                // Calcular ICMS ST sobre o preço de compra (mesmo cálculo do JavaScript)
                $icmsStValor = ($precoCompra * $icmsSt) / 100;
                
                // Aplicar redução da base de cálculo ST se houver
                if ($reducaoBaseSt > 0) {
                    $icmsStValor = $icmsStValor * (1 - $reducaoBaseSt / 100);
                }
            }
            
            if ($ipi > 0) {
                $ipiValor = ($precoCompra * $ipi) / 100;
            }
            
            // Fornecedor a pagar = Preço de compra + ICMS ST + IPI
            $fornecedorPagar = $precoCompra + $icmsStValor + $ipiValor;
            
            // Cálculo do Lucro Bruto baseado no regime
            if ($dados['regime_tributacao'] === 'simples_nacional') {
                // Para Simples Nacional: Lucro Bruto = Preço de Venda - CMV - Simples a pagar
                $simplesPagarValor = ($precoVenda * $simplesPct) / 100;
                $lucroBruto = $precoVenda - $cmv - $simplesPagarValor;
            } else {
                // Para outros regimes: Lucro Bruto = Preço de Venda - CMV - Impostos brutos da Venda
                // Usar o ICMS venda com redução aplicada
                $lucroBruto = $precoVenda - $cmv - $icmsVendaValor - $pisCofinsVendaValor;
            }
            $margemReal = ($lucroBruto / $precoVenda) * 100;
            $markup = (($precoVenda - $precoCompra) / $precoCompra) * 100;
            
            return [
                'sucesso' => true,
                'dados' => [
                    'preco_compra' => number_format($precoCompra, 2, ',', '.'),
                    'preco_venda' => number_format($precoVenda, 2, ',', '.'),
                    'lucro_bruto' => number_format($lucroBruto, 2, ',', '.'),
                    'icms_pagar' => number_format($icmsPagar, 2, ',', '.'),
                    'pis_cofins_pagar' => number_format($pisCofinsPagar, 2, ',', '.'),
                    'simples_pagar' => number_format($simplesPagarValor ?? 0, 2, ',', '.'),
                    'fornecedor_pagar' => number_format($fornecedorPagar, 2, ',', '.'),
                    'margem' => number_format($margemReal, 2, ',', '.'),
                    'markup' => number_format($markup, 2, ',', '.'),
                    'cmv' => number_format($cmv, 2, ',', '.')
                ]
            ];
            
        } catch (Exception $e) {
            return [
                'sucesso' => false,
                'erro' => 'Erro no cálculo: ' . $e->getMessage()
            ];
        }
    }

    public function construtorPlacas() {
        ob_start();
        $this->view('extras/construtor-placas');
        $content = ob_get_clean();
        require APP_ROOT . '/app/Views/layouts/main.php';
    }
    
    public function classificacaoDesossa() {
        $content = view('extras/classificacao-desossa', [
            'title' => 'Classificação de Desossa'
        ], true);
        require APP_ROOT . '/app/Views/layouts/main.php';
    }
    
    public function calculadoraCusto() {
        // Por enquanto, vamos usar arrays vazios para evitar erros
        // Os dados serão carregados via AJAX quando necessário
        $insumos = [];
        $funcionarios = [];
        
        $content = view('extras/calculadora-custo', [
            'title' => 'Calculadora de Custo de Fabricação',
            'insumos' => $insumos,
            'funcionarios' => $funcionarios
        ], true);
        require APP_ROOT . '/app/Views/layouts/main.php';
    }

    // Métodos para gerenciar insumos
    public function cadastrarInsumo() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $insumoModel = new \App\Models\Insumo();
                
                $dados = [
                    'insumo' => strtoupper($_POST['insumo'] ?? ''), // Salvar em maiúsculas
                    'medida' => strtoupper($_POST['medida'] ?? ''), // Salvar medida em maiúsculas também
                    'custo' => floatval(str_replace(['R$', '.', ','], ['', '', '.'], $_POST['custo'] ?? '0')),
                    'usuario_id' => 1 // Temporário
                ];
                
                // Validar dados
                if (empty($dados['insumo']) || empty($dados['medida']) || $dados['custo'] <= 0) {
                    echo json_encode(['sucesso' => false, 'erro' => 'Todos os campos são obrigatórios e o custo deve ser maior que zero']);
                    return;
                }
                
                $resultado = $insumoModel->insert($dados);
                
                if ($resultado) {
                    echo json_encode(['sucesso' => true, 'mensagem' => 'Insumo cadastrado com sucesso!']);
                } else {
                    echo json_encode(['sucesso' => false, 'erro' => 'Erro ao cadastrar insumo']);
                }
            } catch (Exception $e) {
                error_log("Erro ao cadastrar insumo: " . $e->getMessage());
                echo json_encode(['sucesso' => false, 'erro' => 'Erro interno: ' . $e->getMessage()]);
            }
        }
    }

    public function buscarInsumos() {
        $insumoModel = new \App\Models\Insumo();
        $usuarioId = 1; // Temporário
        
        $insumos = $insumoModel->getInsumosByUsuario($usuarioId);
        
        error_log("Insumos encontrados para usuário $usuarioId: " . json_encode($insumos));
        
        echo json_encode(['sucesso' => true, 'insumos' => $insumos]);
    }

    public function buscarInsumoPorId() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $insumoModel = new \App\Models\Insumo();
            $insumoId = $_POST['insumo_id'] ?? 0;
            $usuarioId = 1; // Temporário
            
            error_log("Buscando insumo ID: $insumoId para usuário: $usuarioId");
            
            $insumo = $insumoModel->getInsumoById($insumoId, $usuarioId);
            
            error_log("Insumo encontrado: " . json_encode($insumo));
            
            if ($insumo) {
                echo json_encode(['sucesso' => true, 'insumo' => $insumo]);
            } else {
                echo json_encode(['sucesso' => false, 'erro' => 'Insumo não encontrado']);
            }
        }
    }

    public function calcularCustoInsumo() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $insumoModel = new \App\Models\Insumo();
            
            $insumoId = $_POST['insumo_id'] ?? 0;
            $quantidade = floatval($_POST['quantidade'] ?? 0);
            $usuarioId = 1; // Temporário
            
            $custoTotal = $insumoModel->calcularCusto($insumoId, $quantidade, $usuarioId);
            
            echo json_encode(['sucesso' => true, 'custo_total' => $custoTotal]);
        }
    }

    // Métodos para gerenciar funcionários
    public function cadastrarFuncionario() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $funcionarioModel = new \App\Models\Funcionario();
                
                $dados = [
                    'profissao' => $_POST['profissao'] ?? '',
                    'salario' => floatval(str_replace(['R$', '.', ','], ['', '', '.'], $_POST['salario'] ?? '0')),
                    'usuario_id' => 1 // Temporário
                ];
                
                // Validar dados
                if (empty($dados['profissao']) || $dados['salario'] <= 0) {
                    echo json_encode(['sucesso' => false, 'erro' => 'Todos os campos são obrigatórios e o salário deve ser maior que zero']);
                    return;
                }
                
                $resultado = $funcionarioModel->insert($dados);
                
                if ($resultado) {
                    echo json_encode(['sucesso' => true, 'mensagem' => 'Funcionário cadastrado com sucesso!']);
                } else {
                    echo json_encode(['sucesso' => false, 'erro' => 'Erro ao cadastrar funcionário']);
                }
            } catch (Exception $e) {
                error_log("Erro ao cadastrar funcionário: " . $e->getMessage());
                echo json_encode(['sucesso' => false, 'erro' => 'Erro interno: ' . $e->getMessage()]);
            }
        }
    }

    public function buscarFuncionarios() {
        $funcionarioModel = new \App\Models\Funcionario();
        $usuarioId = 1; // Temporário
        
        $funcionarios = $funcionarioModel->getFuncionariosByUsuario($usuarioId);
        
        error_log("Funcionários encontrados para usuário $usuarioId: " . json_encode($funcionarios));
        
        echo json_encode(['sucesso' => true, 'funcionarios' => $funcionarios]);
    }

    public function buscarFuncionarioPorId() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $funcionarioModel = new \App\Models\Funcionario();
            $funcionarioId = $_POST['funcionario_id'] ?? 0;
            $usuarioId = 1; // Temporário
            
            error_log("Buscando funcionário ID: $funcionarioId para usuário: $usuarioId");
            
            $funcionario = $funcionarioModel->getFuncionarioById($funcionarioId, $usuarioId);
            
            error_log("Funcionário encontrado: " . json_encode($funcionario));
            
            if ($funcionario) {
                echo json_encode(['sucesso' => true, 'funcionario' => $funcionario]);
            } else {
                echo json_encode(['sucesso' => false, 'erro' => 'Funcionário não encontrado']);
            }
        }
    }

    // Métodos para gerenciar embalagens
    public function cadastrarEmbalagem() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $embalagemModel = new \App\Models\Embalagem();
                
                $dados = [
                    'embalagem' => strtoupper($_POST['embalagem'] ?? ''), // Salvar em maiúsculas
                    'quantidade' => intval($_POST['quantidade'] ?? 1),
                    'volume' => intval($_POST['volume'] ?? 1),
                    'custo' => floatval(str_replace(['R$', '.', ','], ['', '', '.'], $_POST['custo'] ?? '0')),
                    'usuario_id' => 1 // Temporário
                ];
                
                // Validar dados
                if (empty($dados['embalagem']) || $dados['custo'] <= 0 || $dados['volume'] <= 0) {
                    echo json_encode(['sucesso' => false, 'erro' => 'Nome da embalagem, custo e volume são obrigatórios e devem ser maiores que zero']);
                    return;
                }
                
                $resultado = $embalagemModel->insert($dados);
                
                if ($resultado) {
                    echo json_encode(['sucesso' => true, 'mensagem' => 'Embalagem cadastrada com sucesso!']);
                } else {
                    echo json_encode(['sucesso' => false, 'erro' => 'Erro ao cadastrar embalagem']);
                }
            } catch (Exception $e) {
                error_log("Erro ao cadastrar embalagem: " . $e->getMessage());
                echo json_encode(['sucesso' => false, 'erro' => 'Erro interno: ' . $e->getMessage()]);
            }
        }
    }

    public function buscarEmbalagens() {
        $embalagemModel = new \App\Models\Embalagem();
        $usuarioId = 1; // Temporário
        
        $embalagens = $embalagemModel->getEmbalagensByUsuario($usuarioId);
        
        error_log("Embalagens encontradas para usuário $usuarioId: " . json_encode($embalagens));
        
        echo json_encode(['sucesso' => true, 'embalagens' => $embalagens]);
    }

    public function buscarEmbalagemPorId() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $embalagemModel = new \App\Models\Embalagem();
            $embalagemId = $_POST['embalagem_id'] ?? 0;
            $usuarioId = 1; // Temporário
            
            error_log("Buscando embalagem ID: $embalagemId para usuário: $usuarioId");
            
            $embalagem = $embalagemModel->getEmbalagemById($embalagemId, $usuarioId);
            
            error_log("Embalagem encontrada: " . json_encode($embalagem));
            
            if ($embalagem) {
                echo json_encode(['sucesso' => true, 'embalagem' => $embalagem]);
            } else {
                echo json_encode(['sucesso' => false, 'erro' => 'Embalagem não encontrada']);
            }
        }
    }

    public function calcularValorHora() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $funcionarioModel = new \App\Models\Funcionario();
            
            $funcionarioId = $_POST['funcionario_id'] ?? 0;
            $usuarioId = 1; // Temporário
            
            $valorHora = $funcionarioModel->calcularValorHora($funcionarioId, $usuarioId);
            
            echo json_encode(['sucesso' => true, 'valor_hora' => $valorHora]);
        }
    }

    public function buscarProdutoPorEan() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ean = $_POST['ean'] ?? '';
            
            if (empty($ean)) {
                echo json_encode(['sucesso' => false, 'erro' => 'EAN não informado']);
                return;
            }

            try {
                // Buscar produto na tabela produtos
                $pdo = $this->getConnection();
                $stmt = $pdo->prepare("SELECT * FROM produtos WHERE ean = ? LIMIT 1");
                $stmt->execute([$ean]);
                $produto = $stmt->fetch(\PDO::FETCH_ASSOC);



                if ($produto) {
                    $preco = $produto['venda'] ?? 0;
                    $precoFormatado = number_format($preco, 2, ',', '.');
                    
                    echo json_encode([
                        'sucesso' => true,
                        'produto' => [
                            'nome' => $produto['descritivo'] ?? $produto['descricao'] ?? '',
                            'ean' => $produto['ean'],
                            'preco' => $precoFormatado
                        ]
                    ]);
                } else {
                    echo json_encode(['sucesso' => false, 'erro' => 'Produto não encontrado']);
                }
            } catch (Exception $e) {
                echo json_encode(['sucesso' => false, 'erro' => 'Erro ao buscar produto: ' . $e->getMessage()]);
            }
        }
    }

    public function gerarPlaca() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $dados = $_POST;
            
            // Validar dados obrigatórios
            if (empty($dados['nome_produto']) || empty($dados['preco'])) {
                echo json_encode(['sucesso' => false, 'erro' => 'Nome do produto e preço são obrigatórios']);
                return;
            }

            try {
                // Gerar HTML da placa
                $html = $this->gerarHtmlPlaca($dados);
                
                echo json_encode([
                    'sucesso' => true,
                    'html' => $html
                ]);
            } catch (Exception $e) {
                echo json_encode(['sucesso' => false, 'erro' => 'Erro ao gerar placa: ' . $e->getMessage()]);
            }
        }
    }

    private function gerarHtmlPlaca($dados) {
        $nome = $dados['nome_produto'];
        $preco = $dados['preco'];
        $tamanho = $dados['tamanho'] ?? 'a4';
        $layout = $dados['layout'] ?? 'padrao';
        $corFundo = $dados['cor_fundo'] ?? '#ffffff';
        $corTexto = $dados['cor_texto'] ?? '#000000';
        $fonte = $dados['fonte'] ?? 'Arial';
        $tamanhoFonte = $dados['tamanho_fonte'] ?? '16px';

        // Definir dimensões baseadas no tamanho
        $dimensoes = $this->getDimensoesTamanho($tamanho);
        
        // Definir estilos baseados no layout
        $estilos = $this->getEstilosLayout($layout, $dimensoes);

        $html = "
        <div class='placa-container' style='{$estilos['container']}'>
            <div class='placa-content' style='{$estilos['content']}'>
                <div class='produto-nome' style='{$estilos['nome']}'>
                    {$nome}
                </div>
                <div class='produto-preco' style='{$estilos['preco']}'>
                    R$ {$preco}
                </div>
            </div>
        </div>";

        return $html;
    }

    private function getDimensoesTamanho($tamanho) {
        $dimensoes = [
            'a4' => ['width' => '210mm', 'height' => '297mm'],
            'a2' => ['width' => '420mm', 'height' => '594mm'],
            'a1' => ['width' => '594mm', 'height' => '841mm'],
            '100x35' => ['width' => '100mm', 'height' => '35mm'],
            '80x40' => ['width' => '80mm', 'height' => '40mm'],
            '50x40' => ['width' => '50mm', 'height' => '40mm'],
            '100x50' => ['width' => '100mm', 'height' => '50mm'],
            '110x30' => ['width' => '110mm', 'height' => '30mm'],
            'instagram_story' => ['width' => '1080px', 'height' => '1920px'],
            'instagram_feed' => ['width' => '1080px', 'height' => '1080px'],
            'whatsapp_status' => ['width' => '1080px', 'height' => '1920px']
        ];

        return $dimensoes[$tamanho] ?? $dimensoes['a4'];
    }

    private function getConnection() {
        try {
            $pdo = new \PDO(
                'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8',
                DB_USER,
                DB_PASS,
                [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                    \PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
            return $pdo;
        } catch (\PDOException $e) {
            error_log("Erro de conexão: " . $e->getMessage());
            throw new Exception("Erro ao conectar com o banco de dados");
        }
    }

    private function getEstilosLayout($layout, $dimensoes) {
        $baseStyles = [
            'container' => "width: {$dimensoes['width']}; height: {$dimensoes['height']}; position: relative; overflow: hidden;",
            'content' => "width: 100%; height: 100%; display: flex; flex-direction: column; justify-content: center; align-items: center; padding: 20px; box-sizing: border-box;",
            'nome' => "font-size: 24px; font-weight: bold; text-align: center; margin-bottom: 10px;",
            'preco' => "font-size: 32px; font-weight: bold; color: #e74c3c; text-align: center;"
        ];

        switch ($layout) {
            case 'instagram_story':
                $baseStyles['content'] = "width: 100%; height: 100%; display: flex; flex-direction: column; justify-content: center; align-items: center; padding: 40px; box-sizing: border-box; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);";
                $baseStyles['nome'] = "font-size: 48px; font-weight: bold; text-align: center; margin-bottom: 30px; color: white; text-shadow: 2px 2px 4px rgba(0,0,0,0.5);";
                $baseStyles['preco'] = "font-size: 72px; font-weight: bold; color: #f1c40f; text-align: center; text-shadow: 2px 2px 4px rgba(0,0,0,0.5);";
                break;
                
            case 'instagram_feed':
                $baseStyles['content'] = "width: 100%; height: 100%; display: flex; flex-direction: column; justify-content: center; align-items: center; padding: 30px; box-sizing: border-box; background: #f8f9fa;";
                $baseStyles['nome'] = "font-size: 36px; font-weight: bold; text-align: center; margin-bottom: 20px; color: #2c3e50;";
                $baseStyles['preco'] = "font-size: 54px; font-weight: bold; color: #e74c3c; text-align: center; background: white; padding: 15px 30px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);";
                break;
                
            case 'whatsapp_status':
                $baseStyles['content'] = "width: 100%; height: 100%; display: flex; flex-direction: column; justify-content: center; align-items: center; padding: 40px; box-sizing: border-box; background: linear-gradient(135deg, #25d366 0%, #128c7e 100%);";
                $baseStyles['nome'] = "font-size: 42px; font-weight: bold; text-align: center; margin-bottom: 25px; color: white; text-shadow: 1px 1px 3px rgba(0,0,0,0.3);";
                $baseStyles['preco'] = "font-size: 64px; font-weight: bold; color: white; text-align: center; text-shadow: 1px 1px 3px rgba(0,0,0,0.3);";
                break;
                
            default: // padrão
                $baseStyles['content'] = "width: 100%; height: 100%; display: flex; flex-direction: column; justify-content: center; align-items: center; padding: 20px; box-sizing: border-box; background: white; border: 2px solid #ddd;";
                break;
        }

        return $baseStyles;
    }
} 