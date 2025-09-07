<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Calculadora de Margem e Preço' ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- IMask -->
    <script src="https://unpkg.com/imask"></script>
    
    <style>
        .card-header-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-bottom: 3px solid #5a6fd8;
            border-radius: 12px 12px 0 0;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .btn-calculate {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: 3px solid #28a745;
            color: white;
            font-weight: 600;
            padding: 15px 40px;
            border-radius: 25px;
            box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
            transition: all 0.3s ease;
            font-size: 1.1rem;
        }
        
        .btn-calculate:hover {
            background: linear-gradient(135deg, #218838 0%, #1ea085 100%);
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(40, 167, 69, 0.5);
            color: white;
            border-color: #218838;
        }
        
        .result-card {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border: 3px solid #dee2e6;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            transition: all 0.3s ease;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12);
        }
        
        .result-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.18);
        }
        
        .result-value {
            font-size: 1.5rem;
            font-weight: bold;
            color: #495057;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        /* Estilos para destacar valores importantes */
        .result-value:not(:empty):not([id*="resumo"]) {
            background: linear-gradient(135deg, #e3f2fd 0%,rgb(216, 216, 216) 100%);
            padding: 8px 12px;
            border-radius: 8px;
            border: 2px solidrgb(194, 222, 245);
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .section-title {
            color: #495057;
            font-weight: 600;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #e9ecef;
        }
        
        .form-label {
            font-weight: 600;
            color: #495057;
        }
        
        .help-icon {
            color: #667eea;
            cursor: help;
            margin-left: 5px;
        }
        
        .loading {
            display: none;
        }
        
        .alert-info {
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            border: 2px solid #90caf9;
            color: #1565c0;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        
        /* Estilos para os cards principais */
        .card.shadow-sm {
            border: 3px solid #e0e0e0;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
        }
        
        /* Estilos para campos de formulário */
        .form-control, .form-select {
            border: 2px solid #dee2e6;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.25);
        }
        
        /* Estilos para campos readonly */
        .form-control[readonly] {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border: 2px solid #adb5bd;
            color: #495057;
            font-weight: 600;
        }
        
        /* Estilos para botões */
        .btn {
            border: 2px solid;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            transition: all 0.3s ease;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.2);
        }
        
        /* Estilos para input groups */
        .input-group {
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            border-radius: 8px;
        }
        
        .input-group-text {
            border: 2px solid #dee2e6;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            font-weight: 600;
        }
        
        /* Estilos para tabelas */
        .table {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .table tbody tr {
            border-bottom: 2px solid #f8f9fa;
        }
        
        /* Estilos para o cabeçalho da página */
        .container-fluid {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        /* Estilos para o body da página */
        body {
            background: linear-gradient(135deg,rgb(228, 228, 228) 0%,rgb(194, 194, 194) 100%);
            min-height: 100vh;
            padding: 20px 0;
        }
        
        /* Estilos para destacar seções */
        .row {
            margin-bottom: 30px;
        }
        
        /* Estilos para o modal */
        .modal-content {
            border: 3px solid #dee2e6;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        
        .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-bottom: 3px solid #5a6fd8;
            border-radius: 12px 12px 0 0;
        }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <!-- Cabeçalho da Página -->
        <div class="col-12 mb-4">
            <div class="d-flex align-items-center">
                <i class="fas fa-calculator text-success me-3" style="font-size: 2rem;"></i>
                <div>
                    <h1 class="h3 mb-0">Calculadora de Margem e Preço</h1>
                    <p class="text-muted mb-0">Calcule preços de venda com base em custos e margens</p>
                </div>
                <div class="ms-auto">
                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#helpModal">
                        <i class="fas fa-question-circle"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Painel Esquerdo - Entrada de Dados -->
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header card-header-custom">
                    <h5 class="mb-0">
                        <i class="fas fa-cog me-2"></i>
                        Configurações e Dados de Entrada
                    </h5>
                </div>
                <div class="card-body">
                    <form id="formCalculadora">
                        <!-- Configurações Iniciais -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Regime de tributação <span class="text-danger">*</span></label>
                                <select class="form-select" id="regime_tributacao" name="regime_tributacao" required>
                                    <option value="">***SELECIONE***</option>
                                    <option value="lucro_real">Lucro Real</option>
                                    <option value="lucro_presumido">Lucro Presumido</option>
                                    <option value="simples_nacional">Simples Nacional</option>
                                </select>
                            </div>
                        </div>

                        <!-- Seção Preço de Compra -->
                        <div class="section-title">
                            <i class="fas fa-shopping-cart me-2"></i>
                            Preço de compra
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Preço de compra</label>
                                <div class="input-group">
                                    <span class="input-group-text">R$</span>
                                    <input type="text" class="form-control" id="preco_compra" name="preco_compra" placeholder="0,00" required>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">(-) Crédito PIS/COFINS</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="pis_cofins_compra_pct" name="pis_cofins_compra_pct" placeholder="0,00" style="max-width: 80px;">
                                    <span class="input-group-text">%</span>
                                    <input type="text" class="form-control" id="pis_cofins_compra_valor" name="pis_cofins_compra_valor" placeholder="0,00" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">(-) Crédito ICMS</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="icms_compra_pct" name="icms_compra_pct" placeholder="0,00" style="max-width: 80px;">
                                    <span class="input-group-text">%</span>
                                    <input type="text" class="form-control" id="icms_compra_valor" name="icms_compra_valor" placeholder="0,00" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Redução da base de cálculo</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="reducao_base_compra" name="reducao_base_compra" placeholder="0,00">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">(+) ICMS ST</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="icms_st_pct" name="icms_st_pct" placeholder="0,00" style="max-width: 80px;">
                                    <span class="input-group-text">%</span>
                                    <input type="text" class="form-control" id="icms_st_valor" name="icms_st_valor" placeholder="0,00" readonly>
                                    
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Redução da base de cálculo ST</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="reducao_base_st" name="reducao_base_st" placeholder="0,00">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">(+) IPI</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="ipi_pct" name="ipi_pct" placeholder="0,00" style="max-width: 80px;">
                                    <span class="input-group-text">%</span>
                                    <input type="text" class="form-control" id="ipi_valor" name="ipi_valor" placeholder="0,00" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label">(=) CMV</label>
                                <div class="input-group">
                                    <span class="input-group-text">R$</span>
                                    <input type="text" class="form-control" id="cmv" name="cmv" placeholder="0,00" readonly>
                                    
                                </div>
                            </div>
                        </div>

                        <!-- Seção Margem Desejada -->
                        <div class="section-title">
                            <i class="fas fa-chart-line me-2"></i>
                            Margem desejada
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="form-label">Margem desejada</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="margem_desejada" name="margem_desejada" placeholder="0,00" required>
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">PIS/COFINS venda</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="pis_cofins_venda_pct" name="pis_cofins_venda_pct" placeholder="0,00" style="max-width: 80px;">
                                    <span class="input-group-text">%</span>
                                    <input type="text" class="form-control" id="pis_cofins_venda_valor" name="pis_cofins_venda_valor" placeholder="0,00" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">ICMS venda</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="icms_venda_pct" name="icms_venda_pct" placeholder="0,00" style="max-width: 80px;">
                                    <span class="input-group-text">%</span>
                                    <input type="text" class="form-control" id="icms_venda_valor" name="icms_venda_valor" placeholder="0,00" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Redução da base de cálculo Saída</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="reducao_base_venda" name="reducao_base_venda" placeholder="0,00">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                        </div>

                        <!-- Campo Simples a pagar (visível apenas para Simples Nacional) -->
                        <div class="row mb-4" id="container_simples" style="display: none;">
                            <div class="col-md-6">
                                <label class="form-label">Simples a pagar</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="simples_pagar_pct" name="simples_pagar_pct" placeholder="0,00" style="max-width: 80px;">
                                    <span class="input-group-text">%</span>
                                    <input type="text" class="form-control" id="simples_pagar_valor" name="simples_pagar_valor" placeholder="0,00" readonly>
                                </div>
                            </div>
                        </div>

                        <!-- Botão Calcular -->
                        <div class="text-center">
                            <button type="submit" class="btn btn-calculate btn-lg">
                                <i class="fas fa-check me-2"></i>
                                Calcular
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Painel Direito - Resultados -->
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header card-header-custom">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar me-2"></i>
                        Resultados
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Cards de Resumo -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="result-card text-center">
                                <i class="fas fa-shopping-cart text-warning mb-2" style="font-size: 2rem;"></i>
                                <h6 class="text-muted">Preço de Compra</h6>
                                <div class="result-value" id="resumo_preco_compra">-</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="result-card text-center">
                                <i class="fas fa-tag text-success mb-2" style="font-size: 2rem;"></i>
                                <h6 class="text-muted">Preço de Venda</h6>
                                <div class="result-value" id="resumo_preco_venda">-</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="result-card text-center">
                                <i class="fas fa-chart-bar text-primary mb-2" style="font-size: 2rem;"></i>
                                <h6 class="text-muted">Lucro Bruto</h6>
                                <div class="result-value" id="resumo_lucro_bruto">-</div>
                            </div>
                        </div>
                    </div>

                    <!-- Seção Resultado Detalhado -->
                    <div class="section-title">
                        <i class="fas fa-list-alt me-2"></i>
                        Resultado
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <table class="table table-borderless">
                                <tbody>
                                    <tr>
                                        <td><strong>Preço de venda:</strong></td>
                                        <td class="text-end"><span class="result-value" id="resultado_preco_venda">R$ -</span></td>
                                    </tr>
                                    <tr id="tr_icms_pagar">
                                        <td><strong>(-) ICMS a Pagar:</strong></td>
                                        <td class="text-end"><span class="result-value" id="resultado_icms_pagar">R$ -</span></td>
                                    </tr>
                                    <tr id="tr_pis_cofins_pagar">
                                        <td><strong>(-) PIS/COFINS a Pagar:</strong></td>
                                        <td class="text-end"><span class="result-value" id="resultado_pis_cofins_pagar">R$ -</span></td>
                                    </tr>
                                    <tr id="tr_simples_pagar" style="display: none;">
                                        <td><strong>(-) Simples a pagar:</strong></td>
                                        <td class="text-end"><span class="result-value" id="resultado_simples_pagar">R$ -</span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>(-) Fornecedor a Pagar:</strong></td>
                                        <td class="text-end"><span class="result-value" id="resultado_fornecedor_pagar">R$ -</span></td>
                                    </tr>
                                    <tr class="table-active">
                                        <td><strong>(=) Lucro Bruto:</strong></td>
                                        <td class="text-end"><span class="result-value" id="resultado_lucro_bruto">R$ -</span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>(=) Margem:</strong></td>
                                        <td class="text-end"><span class="result-value" id="resultado_margem">-%</span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>(=) Mark UP:</strong></td>
                                        <td class="text-end">
                                            <span class="result-value" id="resultado_markup">-%</span>
                                            
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Loading -->
                    <div class="loading text-center mt-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Calculando...</span>
                        </div>
                        <p class="mt-2">Calculando...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Ajuda -->
<div class="modal fade" id="helpModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-question-circle text-primary me-2"></i>
                    Ajuda - Calculadora de Margem e Preço
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Como usar:</strong> Preencha os campos obrigatórios (Preço de compra e Margem desejada) e clique em "Calcular" para ver os resultados.
                </div>
                
                <h6><i class="fas fa-calculator me-2"></i>Termos e Conceitos:</h6>
                <ul>
                    <li><strong>CMV:</strong> Custo da Mercadoria Vendida - representa o custo total para adquirir o produto</li>
                    <li><strong>ICMS ST:</strong> ICMS Substituição Tributária - imposto cobrado quando há substituição tributária</li>
                    <li><strong>Markup:</strong> Percentual de acréscimo sobre o custo para formar o preço de venda</li>
                    <li><strong>Margem:</strong> Percentual de lucro sobre o preço de venda</li>
                </ul>
                
                <h6><i class="fas fa-lightbulb me-2"></i>Dicas:</h6>
                <ul>
                    <li>Os campos de impostos são opcionais e podem ser deixados em zero</li>
                    <li>Informe a margem desejada em percentual e a calculadora mostrará o preço de venda necessário</li>
                    <li>Os resultados são calculados automaticamente conforme você preenche os campos</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formCalculadora');
    
    // Máscaras para os campos
    const masks = {
        currency: IMask.createMask({
            mask: Number,
            scale: 2,
            thousandsSeparator: '.',
            radix: ',',
            mapToRadix: ['.'],
            normalizeZeros: true,
            padFractionalZeros: true,
            min: 0,
            max: 999999999.99,
            parser: function (str) {
                return str.replace(/\D/g, '') / 100;
            },
            formatter: function (value) {
                return value.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            }
        }),
        
        percentage: IMask.createMask({
            mask: Number,
            scale: 2,
            thousandsSeparator: '.',
            radix: ',',
            mapToRadix: ['.'],
            normalizeZeros: true,
            padFractionalZeros: true,
            min: 0,
            max: 100,
            parser: function (str) {
                return str.replace(/\D/g, '') / 100;
            },
            formatter: function (value) {
                return value.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            }
        })
    };

    // Aplicar máscaras
    const currencyFields = document.querySelectorAll('input[id*="preco"], input[id*="valor"]');
    const percentageFields = document.querySelectorAll('input[id*="pct"], input[id*="margem"], input[id*="reducao"]');
    
    currencyFields.forEach(field => {
        if (!field.readOnly) {
            masks.currency.mask(field);
        }
    });
    
    percentageFields.forEach(field => {
        masks.percentage.mask(field);
    });
    
    // Executar controle inicial dos campos
    controlarCamposPorRegime();

    // Cálculos automáticos
    function calcularCamposAutomaticos() {
        const precoCompra = parseFloat(document.getElementById('preco_compra').value.replace(/\./g, '').replace(',', '.')) || 0;
        const pisCofinsPct = parseFloat(document.getElementById('pis_cofins_compra_pct').value.replace(/\./g, '').replace(',', '.')) || 0;
        const icmsPct = parseFloat(document.getElementById('icms_compra_pct').value.replace(/\./g, '').replace(',', '.')) || 0;
        const reducaoBaseCompra = parseFloat(document.getElementById('reducao_base_compra').value.replace(/\./g, '').replace(',', '.')) || 0;
        const icmsStPct = parseFloat(document.getElementById('icms_st_pct').value.replace(/\./g, '').replace(',', '.')) || 0;
        const reducaoBaseSt = parseFloat(document.getElementById('reducao_base_st').value.replace(/\./g, '').replace(',', '.')) || 0;
        const ipiPct = parseFloat(document.getElementById('ipi_pct').value.replace(/\./g, '').replace(',', '.')) || 0;
        
        // Calcular valores de compra
        const pisCofinsValor = (precoCompra * pisCofinsPct) / 100;
        let icmsValor = 0;
        
        // Só calcular crédito ICMS se não houver ICMS ST
        if (icmsStPct === 0) {
            icmsValor = (precoCompra * icmsPct) / 100;
            
            // Aplicar redução da base de cálculo ao crédito ICMS
            if (reducaoBaseCompra > 0) {
                icmsValor = icmsValor * (1 - reducaoBaseCompra / 100);
            }
        }
        
        // Calcular ICMS ST sobre o preço de compra
        let icmsStValor = (precoCompra * icmsStPct) / 100;
        
        // Aplicar redução da base de cálculo ST ao ICMS ST
        if (reducaoBaseSt > 0) {
            icmsStValor = icmsStValor * (1 - reducaoBaseSt / 100);
        }
        const ipiValor = (precoCompra * ipiPct) / 100;
        
        // Calcular CMV
        const cmv = precoCompra - pisCofinsValor - icmsValor + icmsStValor + ipiValor;
        
        // Atualizar campos de compra
        document.getElementById('pis_cofins_compra_valor').value = pisCofinsValor.toFixed(2).replace('.', ',');
        document.getElementById('icms_compra_valor').value = icmsValor.toFixed(2).replace('.', ',');
        document.getElementById('icms_st_valor').value = icmsStValor.toFixed(2).replace('.', ',');
        document.getElementById('ipi_valor').value = ipiValor.toFixed(2).replace('.', ',');
        document.getElementById('cmv').value = cmv.toFixed(2).replace('.', ',');
        
        // Calcular Simples a pagar se for Simples Nacional
        const regime = document.getElementById('regime_tributacao').value;
        if (regime === 'simples_nacional') {
            calcularSimplesPagar();
        }
    }

    // Cálculos automáticos para campos de venda
    function calcularCamposVenda() {
        const pisCofinsVendaPct = parseFloat(document.getElementById('pis_cofins_venda_pct').value.replace(/\./g, '').replace(',', '.')) || 0;
        const icmsVendaPct = parseFloat(document.getElementById('icms_venda_pct').value.replace(/\./g, '').replace(',', '.')) || 0;
        const reducaoBaseVenda = parseFloat(document.getElementById('reducao_base_venda').value.replace(/\./g, '').replace(',', '.')) || 0;
        
        // Obter dados para cálculo do preço de venda
        const precoCompra = parseFloat(document.getElementById('preco_compra').value.replace(/\./g, '').replace(',', '.')) || 0;
        const margemDesejada = parseFloat(document.getElementById('margem_desejada').value.replace(/\./g, '').replace(',', '.')) || 0;
        
        // Calcular preço de venda para atingir a margem desejada
        let precoVenda = 0;
        if (margemDesejada > 0 && precoCompra > 0) {
            // Obter CMV calculado
            const cmv = parseFloat(document.getElementById('cmv').value.replace(/\./g, '').replace(',', '.')) || 0;
            
            // Calcular ICMS efetivo considerando a redução da base de cálculo
            let icmsEfetivo = icmsVendaPct;
            if (reducaoBaseVenda > 0) {
                icmsEfetivo = icmsVendaPct * (1 - reducaoBaseVenda / 100);
            }
            
            // Fórmula: Preço de Venda = CMV / (1 - Margem Desejada - Σ Alíquotas de Impostos na Venda)
            precoVenda = cmv / (1 - (margemDesejada + pisCofinsVendaPct + icmsEfetivo) / 100);
        }
        
        // Calcular valores de venda sobre o preço de venda final
        const pisCofinsVendaValor = (precoVenda * pisCofinsVendaPct) / 100;
        let icmsVendaValor = (precoVenda * icmsVendaPct) / 100;
        
        // Aplicar redução da base de cálculo de saída ao ICMS venda
        if (reducaoBaseVenda > 0) {
            const reducaoIcmsVenda = icmsVendaValor * (reducaoBaseVenda / 100);
            icmsVendaValor = icmsVendaValor - reducaoIcmsVenda;
        }
        
        // Atualizar campos de venda
        document.getElementById('pis_cofins_venda_valor').value = pisCofinsVendaValor.toFixed(2).replace('.', ',');
        document.getElementById('icms_venda_valor').value = icmsVendaValor.toFixed(2).replace('.', ',');
    }
    

    
    // Função para calcular Simples a pagar
    function calcularSimplesPagar() {
        const precoCompra = parseFloat(document.getElementById('preco_compra').value.replace(/\./g, '').replace(',', '.')) || 0;
        const margemDesejada = parseFloat(document.getElementById('margem_desejada').value.replace(/\./g, '').replace(',', '.')) || 0;
        const simplesPct = parseFloat(document.getElementById('simples_pagar_pct').value.replace(/\./g, '').replace(',', '.')) || 0;
        
        if (precoCompra > 0 && margemDesejada > 0 && simplesPct > 0) {
            // Calcular preço de venda estimado
            const precoVendaEstimado = precoCompra * (1 + margemDesejada / 100);
            const simplesPagarValor = (precoVendaEstimado * simplesPct) / 100;
            
            document.getElementById('simples_pagar_valor').value = simplesPagarValor.toFixed(2).replace('.', ',');
        }
    }

    // Event listeners para cálculos automáticos
    document.getElementById('preco_compra').addEventListener('input', function() {
        calcularCamposAutomaticos();
        calcularCamposVenda();
        
        // Atualizar card de resumo do preço de compra
        const precoCompra = parseFloat(this.value.replace(/\./g, '').replace(',', '.')) || 0;
        document.getElementById('resumo_preco_compra').textContent = `R$ ${precoCompra.toFixed(2).replace('.', ',')}`;
    });
    document.getElementById('pis_cofins_compra_pct').addEventListener('input', function() {
        calcularCamposAutomaticos();
        calcularCamposVenda();
    });
    document.getElementById('icms_compra_pct').addEventListener('input', function() {
        calcularCamposAutomaticos();
        calcularCamposVenda();
    });
    document.getElementById('reducao_base_compra').addEventListener('input', function() {
        calcularCamposAutomaticos();
        calcularCamposVenda();
    });
    document.getElementById('icms_st_pct').addEventListener('input', function() {
        const icmsStPct = parseFloat(this.value.replace(/\./g, '').replace(',', '.')) || 0;
        
        // Se ICMS ST for preenchido, zerar redução da base de cálculo e crédito ICMS
        if (icmsStPct > 0) {
            document.getElementById('reducao_base_compra').value = '';
            document.getElementById('icms_compra_pct').value = '';
            document.getElementById('icms_compra_valor').value = '';
        }
        
        calcularCamposAutomaticos();
        calcularCamposVenda();
    });
    document.getElementById('reducao_base_st').addEventListener('input', function() {
        calcularCamposAutomaticos();
        calcularCamposVenda();
    });
    document.getElementById('ipi_pct').addEventListener('input', function() {
        calcularCamposAutomaticos();
        calcularCamposVenda();
    });
    
    // Event listeners para campos de venda
    document.getElementById('pis_cofins_venda_pct').addEventListener('input', calcularCamposVenda);
    document.getElementById('icms_venda_pct').addEventListener('input', calcularCamposVenda);
    document.getElementById('reducao_base_venda').addEventListener('input', calcularCamposVenda);
    document.getElementById('margem_desejada').addEventListener('input', function() {
        calcularCamposVenda();
        // Se for Simples Nacional, calcular também o Simples a pagar
        if (document.getElementById('regime_tributacao').value === 'simples_nacional') {
            calcularSimplesPagar();
        }
    });
    
    // Event listener para Simples a pagar (porcentagem)
    document.getElementById('simples_pagar_pct').addEventListener('input', calcularSimplesPagar);
    document.getElementById('reducao_base_venda').addEventListener('input', calcularCamposVenda);
    
    // Event listener para regime de tributação
    document.getElementById('regime_tributacao').addEventListener('change', controlarCamposPorRegime);
    
    // Função para controlar campos baseado no regime selecionado
    function controlarCamposPorRegime() {
        const regime = document.getElementById('regime_tributacao').value;
        
        // Campos que serão controlados
        const camposImpostos = [
            'pis_cofins_compra_pct',
            'pis_cofins_compra_valor',
            'icms_compra_pct', 
            'icms_compra_valor',
            'reducao_base_compra',
            'pis_cofins_venda_pct',
            'pis_cofins_venda_valor',
            'icms_venda_pct',
            'icms_venda_valor',
            'reducao_base_venda'
        ];
        
        // Campos que NÃO devem ser ocultados para Simples Nacional
        const camposSimplesNacional = [
            'icms_st_pct',
            'icms_st_valor',
            'reducao_base_st',
            'ipi_pct',
            'ipi_valor'
        ];
        
        // Container do campo Simples a pagar
        const containerSimples = document.getElementById('container_simples');
        
        if (regime === 'lucro_presumido') {
            // Inabilitar apenas Crédito PIS/COFINS para Lucro Presumido
            document.getElementById('pis_cofins_compra_pct').disabled = true;
            document.getElementById('pis_cofins_compra_pct').value = '';
            document.getElementById('pis_cofins_compra_valor').value = '';
            
            // Mostrar outros campos de impostos
            camposImpostos.forEach(campo => {
                const elemento = document.getElementById(campo);
                if (elemento) {
                    elemento.closest('.row').style.display = 'flex';
                }
            });
            
            // Esconder campo Simples
            if (containerSimples) {
                containerSimples.style.display = 'none';
            }
            
            calcularCamposAutomaticos();
            
        } else if (regime === 'simples_nacional') {
            // Esconder campos de impostos específicos para Simples Nacional
            camposImpostos.forEach(campo => {
                const elemento = document.getElementById(campo);
                if (elemento) {
                    elemento.closest('.row').style.display = 'none';
                }
            });
            
            // Manter visíveis os campos que NÃO devem ser ocultados para Simples Nacional
            camposSimplesNacional.forEach(campo => {
                const elemento = document.getElementById(campo);
                if (elemento) {
                    elemento.closest('.row').style.display = 'flex';
                }
            });
            
            // Mostrar campo Simples
            if (containerSimples) {
                containerSimples.style.display = 'flex';
            }
            
            // Controlar visibilidade na tabela de resultados
            document.getElementById('tr_icms_pagar').style.display = 'none';
            document.getElementById('tr_pis_cofins_pagar').style.display = 'none';
            document.getElementById('tr_simples_pagar').style.display = 'table-row';
            
            calcularCamposAutomaticos();
            
        } else {
            // Habilitar todos os campos para outros regimes
            camposImpostos.forEach(campo => {
                const elemento = document.getElementById(campo);
                if (elemento) {
                    elemento.closest('.row').style.display = 'flex';
                    if (elemento.disabled) {
                        elemento.disabled = false;
                    }
                }
            });
            
            // Esconder campo Simples
            if (containerSimples) {
                containerSimples.style.display = 'none';
            }
            
            // Controlar visibilidade na tabela de resultados
            document.getElementById('tr_icms_pagar').style.display = 'table-row';
            document.getElementById('tr_pis_cofins_pagar').style.display = 'table-row';
            document.getElementById('tr_simples_pagar').style.display = 'none';
        }
    }

    // Submissão do formulário
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Validação do regime de tributação (obrigatório)
        const regimeTributacao = document.getElementById('regime_tributacao').value;
        if (!regimeTributacao) {
            alert('Por favor, selecione o regime de tributação');
            return;
        }
        
        // Validação para Lucro Real
        if (regimeTributacao === 'lucro_real') {
            const camposObrigatorios = [
                { id: 'pis_cofins_venda_pct', nome: 'PIS/COFINS venda' },
                { id: 'icms_venda_pct', nome: 'ICMS venda' },
                { id: 'pis_cofins_compra_pct', nome: 'Crédito PIS/COFINS' },
                { id: 'icms_compra_pct', nome: 'Crédito ICMS' }
            ];
            
            for (const campo of camposObrigatorios) {
                const valor = document.getElementById(campo.id).value;
                if (!valor) {
                    alert(`Para o regime Lucro Real, o campo '${campo.nome}' é obrigatório`);
                    return;
                }
            }
        }
        
        const formData = new FormData(form);
        
        // Mostrar loading
        document.querySelector('.loading').style.display = 'block';
        
        fetch('/extras/calcular-preco', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            document.querySelector('.loading').style.display = 'none';
            
            if (data.erro) {
                alert('Erro: ' + data.erro);
            } else if (data.sucesso) {
                exibirResultados(data.dados);
            }
        })
        .catch(error => {
            document.querySelector('.loading').style.display = 'none';
            console.error('Erro:', error);
            alert('Erro na comunicação com o servidor.');
        });
    });

    function exibirResultados(dados) {
        // Atualizar sempre o preço de compra
        document.getElementById('resumo_preco_compra').textContent = `R$ ${dados.preco_compra}`;
        
        // Atualizar cards de resumo
        document.getElementById('resumo_preco_venda').textContent = `R$ ${dados.preco_venda}`;
        document.getElementById('resumo_lucro_bruto').textContent = `R$ ${dados.lucro_bruto}`;
        
        // Atualizar resultados detalhados
        document.getElementById('resultado_preco_venda').textContent = `R$ ${dados.preco_venda}`;
        document.getElementById('resultado_icms_pagar').textContent = `R$ ${dados.icms_pagar}`;
        document.getElementById('resultado_pis_cofins_pagar').textContent = `R$ ${dados.pis_cofins_pagar}`;
        document.getElementById('resultado_simples_pagar').textContent = `R$ ${dados.simples_pagar || '0,00'}`;
        
        // Atualizar campo de valor do Simples se for Simples Nacional
        if (document.getElementById('regime_tributacao').value === 'simples_nacional') {
            document.getElementById('simples_pagar_valor').value = dados.simples_pagar || '0,00';
        }
        document.getElementById('resultado_fornecedor_pagar').textContent = `R$ ${dados.fornecedor_pagar}`;
        document.getElementById('resultado_lucro_bruto').textContent = `R$ ${dados.lucro_bruto}`;
        document.getElementById('resultado_margem').textContent = `${dados.margem}%`;
        document.getElementById('resultado_markup').textContent = `${dados.markup}%`;
        
        // NÃO atualizar o CMV - manter o valor calculado automaticamente
        // document.getElementById('cmv').value = dados.cmv.replace('.', ',');
    }
});
</script>

</body>
</html> 