<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calculadora de Custo de Fabricação</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #667eea;
            --secondary-color: #764ba2;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --info-color: #17a2b8;
        }

        .card-header-custom {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            border-radius: 12px 12px 0 0;
        }
        
        /* Estilos para as abas - FORÇAR VISIBILIDADE */
        .nav-tabs {
            display: flex !important;
            flex-direction: row !important;
            background: #e9ecef !important;
            border: 2px solid #dee2e6 !important;
            border-radius: 10px !important;
            padding: 15px !important;
            margin: 20px 0 !important;
            list-style: none !important;
            margin-left: 0 !important;
        }
        
        .nav-tabs .nav-item {
            margin-right: 10px !important;
        }
        
        .nav-tabs .nav-link {
            display: block !important;
            color: #495057 !important;
            background: #ffffff !important;
            border: 2px solid #dee2e6 !important;
            font-weight: 600 !important;
            padding: 12px 20px !important;
            border-radius: 8px !important;
            text-decoration: none !important;
            transition: all 0.3s ease !important;
            cursor: pointer !important;
        }
        
        .nav-tabs .nav-link.active {
            color: #ffffff !important;
            background: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
            box-shadow: 0 4px 8px rgba(102, 126, 234, 0.3) !important;
        }
        
        .nav-tabs .nav-link:hover {
            background: var(--secondary-color) !important;
            color: #ffffff !important;
            transform: translateY(-2px) !important;
        }
        
        .tab-pane {
            display: none;
        }
        
        .tab-pane.active {
            display: block !important;
        }
        
        .btn-calculate {
            background: linear-gradient(135deg, var(--success-color) 0%, #20c997 100%);
            border: none;
            color: white;
            font-weight: 600;
            padding: 15px 40px;
            border-radius: 25px;
        }
        
        .result-card {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border: 3px solid #dee2e6;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .result-value {
            font-size: 1.5rem;
            font-weight: bold;
            color: #495057;
        }
        
        .price-suggestion {
            background: linear-gradient(135deg, #e8f5e8 0%, #c8e6c9 100%);
            border: 3px solid var(--success-color);
            border-radius: 15px;
            padding: 25px;
            text-align: center;
        }
        
        .price-value {
            font-size: 2.5rem;
            font-weight: bold;
            color: var(--success-color);
        }
        
        /* Estilos para campos obrigatórios */
        .form-floating label::after {
            content: " *";
            color: #dc3545;
        }
        
        .form-floating label.optional::after {
            content: "";
        }
        
        /* Estilos para campos inválidos */
        .is-invalid {
            border-color: #dc3545 !important;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
        }
        
        .is-invalid + label {
            color: #dc3545 !important;
        }
        
        /* Estilo para texto de ajuda */
        .form-text {
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
        
        .form-text.text-muted {
            color: #6c757d !important;
        }
        
        /* Estilos para campos readonly */
        input[readonly] {
            background-color: #f8f9fa !important;
            border-color: #dee2e6 !important;
            color: #6c757d !important;
            cursor: not-allowed;
        }
        
        input[readonly]:focus {
            box-shadow: none !important;
            border-color: #dee2e6 !important;
        }
        
        /* Estilos para validação */
    </style>
</head>
<body>
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-lg">
                    <div class="card-header card-header-custom">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-industry me-3" style="font-size: 1.5rem;"></i>
                            <div>
                                <h3 class="mb-0">Calculadora de Custo de Fabricação</h3>
                                <p class="mb-0 opacity-75">Calcule o custo detalhado de seus produtos</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <!-- Formulário Principal -->
                        <form id="custoForm">
                                                        <!-- Informações do Produto -->
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="nomeProduto" name="nomeProduto" placeholder="Nome do Produto" required>
                                        <label for="nomeProduto" class="optional">Nome do Produto *</label>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Abas -->
                            <div style="background: #f8f9fa; border: 2px solid #dee2e6; border-radius: 10px; padding: 15px; margin: 20px 0;">
                                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                                    <button class="btn btn-primary" id="insumos-tab" onclick="alternarAba('insumos')" style="font-weight: 600; padding: 12px 20px; border-radius: 8px;">
                                        <i class="fas fa-pizza-slice me-2"></i>Insumos
                                    </button>
                                    <button class="btn btn-outline-primary" id="embalagem-tab" onclick="alternarAba('embalagem')" style="font-weight: 600; padding: 12px 20px; border-radius: 8px;">
                                        <i class="fas fa-box me-2"></i>Embalagem
                                    </button>
                                    <button class="btn btn-outline-primary" id="mao-obra-tab" onclick="alternarAba('mao-obra')" style="font-weight: 600; padding: 12px 20px; border-radius: 8px;">
                                        <i class="fas fa-users me-2"></i>Mão de Obra
                                    </button>
                                    <button class="btn btn-outline-primary" id="despesas-tab" onclick="alternarAba('despesas')" style="font-weight: 600; padding: 12px 20px; border-radius: 8px;">
                                        <i class="fas fa-receipt me-2"></i>Despesas
                                    </button>
                                    <button class="btn btn-outline-primary" id="resumo-tab" onclick="alternarAba('resumo')" style="font-weight: 600; padding: 12px 20px; border-radius: 8px;">
                                        <i class="fas fa-chart-pie me-2"></i>Resumo
                                    </button>
                                </div>
                            </div>
                            
                            <div class="tab-content" id="custoTabContent">
                                <!-- Aba Insumos -->
                                <div class="tab-pane fade show active" id="insumos" role="tabpanel">
                                    <div class="mt-4">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h5 class="mb-0">
                                                <i class="fas fa-pizza-slice me-2"></i>Insumos (Matéria-Prima)
                                            </h5>
                                            <div>
                                                <button type="button" class="btn btn-success me-2" onclick="abrirModalInsumo()">
                                                    <i class="fas fa-plus me-2"></i>Cadastrar Insumo
                                                </button>
                                                <button type="button" class="btn btn-info" onclick="adicionarInsumo()">
                                                    <i class="fas fa-plus me-2"></i>Adicionar Insumo
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <div id="insumosContainer">
                                            <!-- Insumos serão adicionados aqui -->
                                        </div>
                                        
                                        <div class="result-card">
                                            <h6 class="mb-2"><i class="fas fa-calculator me-2"></i>Total dos Insumos</h6>
                                            <div class="result-value" id="totalInsumos">R$ 0,00</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Aba Embalagem -->
                                <div class="tab-pane fade" id="embalagem" role="tabpanel">
                                    <div class="mt-4">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h5 class="mb-0">
                                                <i class="fas fa-box me-2"></i>Embalagens do Produto
                                            </h5>
                                            <div>
                                                <button type="button" class="btn btn-success me-2" onclick="abrirModalEmbalagem()">
                                                    <i class="fas fa-plus me-2"></i>Cadastrar Embalagem
                                                </button>
                                                <button type="button" class="btn btn-info" onclick="adicionarEmbalagem()">
                                                    <i class="fas fa-plus me-2"></i>Adicionar Embalagem
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <div id="embalagemContainer">
                                            <!-- Embalagens serão adicionadas aqui -->
                                        </div>
                                        
                                        <div class="result-card">
                                            <h6 class="mb-2"><i class="fas fa-calculator me-2"></i>Total das Embalagens</h6>
                                            <div class="result-value" id="totalEmbalagem">R$ 0,00</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Aba Mão de Obra -->
                                <div class="tab-pane fade" id="mao-obra" role="tabpanel">
                                    <div class="mt-4">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h5 class="mb-0">
                                                <i class="fas fa-users me-2"></i>Mão de Obra
                                            </h5>
                                            <div>
                                                <button type="button" class="btn btn-success me-2" onclick="abrirModalFuncionario()">
                                                    <i class="fas fa-plus me-2"></i>Cadastrar Funcionário
                                                </button>
                                                <button type="button" class="btn btn-info" onclick="adicionarMaoObra()">
                                                    <i class="fas fa-plus me-2"></i>Adicionar Colaborador
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <div id="maoObraContainer">
                                            <!-- Mão de obra será adicionada aqui -->
                                        </div>
                                        
                                                                                 <div class="result-card">
                                             <h6 class="mb-2"><i class="fas fa-calculator me-2"></i>Total da Mão de Obra</h6>
                                             <div class="result-value" id="totalMaoObra">R$ 0,00</div>
                                             <small class="text-muted">
                                                 <i class="fas fa-clock me-1"></i>
                                                 Total de horas: <span id="totalHoras">0</span>h
                                             </small>
                                         </div>
                                    </div>
                                </div>
                                
                                <!-- Aba Despesas -->
                                <div class="tab-pane fade" id="despesas" role="tabpanel">
                                    <div class="mt-4">
                                        <h5 class="mb-3">
                                            <i class="fas fa-receipt me-2"></i>Despesas Fixas e Variáveis (Mensais)
                                        </h5>
                                        
                                                                                 <div class="alert alert-info">
                                             <i class="fas fa-info-circle me-2"></i>
                                             <strong>Informação:</strong> As despesas são calculadas proporcionalmente ao tempo de produção.<br>
                                             • Energia, água, gás e aluguel: Valor mensal ÷ 30 dias ÷ 24h × horas trabalhadas<br>
                                             • Transporte: Valor diário ÷ 8h × horas trabalhadas<br>
                                             • Impostos e taxas são aplicados como percentual sobre o preço de venda.
                                         </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3">
                                                    <input type="number" class="form-control" id="energia" name="energia" placeholder="Energia Elétrica Mensal" min="0" step="0.01" value="0">
                                                    <label for="energia">Energia Elétrica Mensal (R$)</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3">
                                                    <input type="number" class="form-control" id="gas" name="gas" placeholder="Gás Mensal" min="0" step="0.01" value="0">
                                                    <label for="gas">Gás Mensal (R$)</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3">
                                                    <input type="number" class="form-control" id="agua" name="agua" placeholder="Água Mensal" min="0" step="0.01" value="0">
                                                    <label for="agua">Água Mensal (R$)</label>
                                                </div>
                                            </div>
                                                                                         <div class="col-md-6">
                                                 <div class="form-floating mb-3">
                                                     <input type="number" class="form-control" id="aluguel" name="aluguel" placeholder="Aluguel Mensal" min="0" step="0.01" value="0">
                                                     <label for="aluguel">Aluguel Mensal (R$)</label>
                                                 </div>
                                             </div>
                                             <div class="col-md-6">
                                                 <div class="form-floating mb-3">
                                                     <input type="number" class="form-control" id="transporte" name="transporte" placeholder="Transporte Diário" min="0" step="0.01" value="0">
                                                     <label for="transporte">Transporte Diário (R$)</label>
                                                 </div>
                                             </div>
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3">
                                                    <input type="number" class="form-control" id="embalagem_produto" name="embalagem_produto" placeholder="Embalagem do Produto" min="0" step="0.01" value="0" readonly>
                                                    <label for="embalagem_produto">Embalagem do Produto (R$)</label>
                                                    <small class="form-text text-muted">
                                                        <i class="fas fa-info-circle me-1"></i>Valor total das embalagens - calculado automaticamente
                                                    </small>
                                                </div>
                                            </div>
                                                                                         <div class="col-md-6">
                                                 <div class="form-floating mb-3">
                                                     <input type="number" class="form-control" id="impostos_estaduais" name="impostos_estaduais" placeholder="Impostos Estaduais (%)" min="0" max="50" step="0.1" value="0">
                                                     <label for="impostos_estaduais">Impostos Estaduais (%)</label>
                                                 </div>
                                             </div>
                                             <div class="col-md-6">
                                                 <div class="form-floating mb-3">
                                                     <input type="number" class="form-control" id="impostos_federais" name="impostos_federais" placeholder="Impostos Federais (%)" min="0" max="50" step="0.1" value="0">
                                                     <label for="impostos_federais">Impostos Federais (%)</label>
                                                 </div>
                                             </div>
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3">
                                                    <input type="number" class="form-control" id="taxa_cartao" name="taxa_cartao" placeholder="Taxa de Cartão (%)" min="0" max="10" step="0.1" value="0">
                                                    <label for="taxa_cartao">Taxa de Cartão (%)</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3">
                                                    <input type="number" class="form-control" id="outras" name="outras" placeholder="Outras Despesas" min="0" step="0.01" value="0">
                                                    <label for="outras">Outras Despesas (R$)</label>
                                                </div>
                                            </div>
                                        </div>
                                        
                                                                                 <div class="result-card">
                                             <h6 class="mb-2"><i class="fas fa-calculator me-2"></i>Total das Despesas</h6>
                                             <div class="result-value" id="totalDespesas">R$ 0,00</div>
                                             <small class="text-muted">
                                                 <i class="fas fa-info-circle me-1"></i>
                                                 Rateio proporcional baseado no tempo de produção
                                             </small>
                                         </div>
                                    </div>
                                </div>
                                
                                <!-- Aba Resumo -->
                                <div class="tab-pane fade" id="resumo" role="tabpanel">
                                    <div class="mt-4">
                                        <h5 class="mb-3">
                                            <i class="fas fa-chart-pie me-2"></i>Resumo dos Custos
                                        </h5>
                                        
                                                                                 <div class="row">
                                             <div class="col-md-6">
                                                 <div class="result-card">
                                                     <h6><i class="fas fa-box-open me-2"></i>Total dos Insumos</h6>
                                                     <div class="result-value" id="resumoInsumos">R$ 0,00</div>
                                                 </div>
                                             </div>
                                             <div class="col-md-6">
                                                 <div class="result-card">
                                                     <h6><i class="fas fa-users me-2"></i>Total da Mão de Obra</h6>
                                                     <div class="result-value" id="resumoMaoObra">R$ 0,00</div>
                                                 </div>
                                             </div>
                                             <div class="col-md-6">
                                                 <div class="result-card">
                                                     <h6><i class="fas fa-receipt me-2"></i>Total das Despesas</h6>
                                                     <div class="result-value" id="resumoDespesas">R$ 0,00</div>
                                                     <small class="text-muted">
                                                         <i class="fas fa-info-circle me-1"></i>
                                                         Rateio proporcional
                                                     </small>
                                                 </div>
                                             </div>
                                             <div class="col-md-6">
                                                 <div class="result-card">
                                                     <h6><i class="fas fa-percentage me-2"></i>Total de Impostos e Taxas</h6>
                                                     <div class="result-value" id="resumoImpostosTaxas">R$ 0,00</div>
                                                     <small class="text-muted">
                                                         <i class="fas fa-info-circle me-1"></i>
                                                         Percentual sobre preço de venda
                                                     </small>
                                                 </div>
                                             </div>
                                             <div class="col-md-6">
                                                 <div class="result-card">
                                                     <h6><i class="fas fa-calculator me-2"></i>Custo Total de Produção</h6>
                                                     <div class="result-value" id="custoTotalProducao">R$ 0,00</div>
                                                 </div>
                                             </div>
                                             <div class="col-md-6">
                                                 <div class="result-card">
                                                     <h6><i class="fas fa-money-bill me-2"></i>Custo Unitário</h6>
                                                     <div class="result-value" id="custoUnitario">R$ 0,00</div>
                                                 </div>
                                             </div>
                                                                                           <div class="col-md-6">
                                                  <div class="result-card">
                                                      <h6><i class="fas fa-weight-hanging me-2"></i>Tipo de Venda</h6>
                                                      <div class="form-check form-check-inline">
                                                          <input class="form-check-input" type="radio" name="tipoVenda" id="vendaUnidade" value="unidade" checked>
                                                          <label class="form-check-label" for="vendaUnidade">Por Unidade</label>
                                                      </div>
                                                      <div class="form-check form-check-inline">
                                                          <input class="form-check-input" type="radio" name="tipoVenda" id="vendaKg" value="kg">
                                                          <label class="form-check-label" for="vendaKg">Por Kg</label>
                                                      </div>
                                                      
                                                      <!-- Campo de Rendimento por Unidade -->
                                                      <div id="rendimentoUnidade" class="mt-3">
                                                          <div class="form-floating">
                                                              <input type="number" class="form-control" id="rendimentoPorUnidade" name="rendimentoPorUnidade" placeholder="Rendimento por Unidade" min="1" step="0.01" value="1">
                                                              <label for="rendimentoPorUnidade">Rendimento de quantas Unidades (obrigatório)</label>
                                                          </div>
                                                      </div>
                                                      
                                                      <!-- Campo de Rendimento por Kg -->
                                                      <div id="rendimentoKg" class="mt-3" style="display: none;">
                                                          <div class="form-floating">
                                                              <input type="number" class="form-control" id="rendimentoPorKg" name="rendimentoPorKg" placeholder="Rendimento por Kg" min="0.01" step="0.01" value="1">
                                                              <label for="rendimentoPorKg">Rendimento de quantos Kg's (obrigatório)</label>
                                                          </div>
                                                      </div>
                                                  </div>
                                              </div>
                                         </div>
                                         
                                         <!-- Seção de Formação de Preço -->
                                         <div class="row mt-4">
                                             <div class="col-12">
                                                 <div class="result-card">
                                                     <h6><i class="fas fa-tag me-2"></i>Formação de Preço</h6>
                                                     <div class="row">
                                                         <div class="col-md-4">
                                                             <div class="form-floating mb-3">
                                                                 <input type="number" class="form-control" id="precoFinal" name="precoFinal" placeholder="Preço Final" min="0" step="0.01" value="0">
                                                                 <label for="precoFinal">Preço Final (R$)</label>
                                                             </div>
                                                         </div>
                                                         <div class="col-md-4">
                                                             <div class="form-floating mb-3">
                                                                 <input type="number" class="form-control" id="margemLucro" name="margemLucro" placeholder="Margem de Lucro (%)" min="0" max="100" step="0.01" value="30">
                                                                 <label for="margemLucro">Margem Direta (%)</label>
                                                             </div>
                                                         </div>
                                                         <div class="col-md-4">
                                                             <button type="button" class="btn btn-primary w-100" onclick="calcularPreco()">
                                                                 <i class="fas fa-calculator me-2"></i>Calcular
                                                             </button>
                                                         </div>
                                                     </div>
                                                     <div class="row mt-3">
                                                         <div class="col-md-6">
                                                             <div class="alert alert-info">
                                                                 <h6><i class="fas fa-info-circle me-2"></i>Margem Calculada</h6>
                                                                 <div class="result-value" id="margemCalculada">0%</div>
                                                             </div>
                                                         </div>
                                                         <div class="col-md-6">
                                                             <div class="alert alert-success">
                                                                 <h6><i class="fas fa-lightbulb me-2"></i>Sugestão de Venda</h6>
                                                                 <div class="result-value" id="sugestaoVenda">R$ 0,00</div>
                                                             </div>
                                                         </div>
                                                     </div>
                                                 </div>
                                             </div>
                                         </div>
                                        
                                        <div class="row mt-4">
                                            <div class="col-12 text-center">
                                                <button type="button" class="btn btn-warning" onclick="salvarFichaTecnica()">
                                                    <i class="fas fa-save me-2"></i>Salvar Ficha Técnica
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Variáveis globais
        let contadorInsumos = 0;
        let contadorMaoObra = 0;
        let contadorEmbalagem = 0;
        
                 // Inicialização
         document.addEventListener('DOMContentLoaded', function() {
             // Adicionar primeiro insumo, embalagem e mão de obra
             adicionarInsumo();
             adicionarEmbalagem();
             adicionarMaoObra();
             
             // Garantir que um tipo de venda esteja selecionado por padrão
             const vendaUnidade = document.getElementById('vendaUnidade');
             const vendaKg = document.getElementById('vendaKg');
             
             console.log('Elementos encontrados na inicialização:');
             console.log('Venda Unidade:', vendaUnidade);
             console.log('Venda Kg:', vendaKg);
             console.log('Rendimento Unidade div:', document.getElementById('rendimentoUnidade'));
             console.log('Rendimento Kg div:', document.getElementById('rendimentoKg'));
             
             if (!vendaUnidade.checked && !vendaKg.checked) {
                 vendaUnidade.checked = true; // Selecionar "Por Unidade" por padrão
             }
             
             // Inicializar tipo de venda
             alternarTipoVenda();
             
             // Event listeners para validação em tempo real
             document.getElementById('nomeProduto').addEventListener('input', function() {
                 if (this.value.trim()) {
                     this.classList.remove('is-invalid');
                 } else {
                     this.classList.add('is-invalid');
                 }
             });
             
             // Event listeners para cálculos automáticos
             // (margemLucro event listener será adicionado abaixo)
             
             // Event listeners para tipo de venda
             document.querySelectorAll('input[name="tipoVenda"]').forEach(radio => {
                 radio.addEventListener('change', function() {
                     alternarTipoVenda();
                     calcularCustos();
                 });
             });
             
             // Event listeners para formação de preço
             document.getElementById('precoFinal').addEventListener('input', function() {
                 // Limpar margem direta quando preço final é alterado
                 document.getElementById('margemLucro').value = '';
                 calcularMargem();
             });
             
             document.getElementById('margemLucro').addEventListener('input', function() {
                 // Limpar preço final quando margem direta é alterada
                 document.getElementById('precoFinal').value = '';
                 calcularPreco();
             });
             
             // Event listeners para campos de rendimento
             document.getElementById('rendimentoPorUnidade').addEventListener('input', calcularCustos);
             document.getElementById('rendimentoPorKg').addEventListener('input', calcularCustos);
             
             // Event listeners para despesas
             const despesasInputs = ['energia', 'gas', 'agua', 'aluguel', 'transporte', 'embalagem', 'impostos_estaduais', 'impostos_federais', 'taxa_cartao', 'outras'];
             despesasInputs.forEach(id => {
                 const element = document.getElementById(id);
                 if (element) {
                     element.addEventListener('input', () => {
                         // Pequeno delay para evitar cálculos desnecessários
                         setTimeout(calcularCustos, 100);
                     });
                 }
             });
             
             // Adicionar event listener para preservar dados quando o formulário é submetido
             document.getElementById('custoForm').addEventListener('submit', function(e) {
                 e.preventDefault();
             });
         });
         
                 // Função para alternar tipo de venda
         function alternarTipoVenda() {
            const vendaUnidade = document.getElementById('vendaUnidade');
            const vendaKg = document.getElementById('vendaKg');
            const rendimentoUnidade = document.getElementById('rendimentoUnidade');
            const rendimentoKg = document.getElementById('rendimentoKg');
            
            console.log('Alternando tipo de venda...');
            console.log('Venda Unidade checked:', vendaUnidade.checked);
            console.log('Venda Kg checked:', vendaKg.checked);
            
            if (vendaUnidade && vendaUnidade.checked) {
                console.log('Mostrando rendimento por unidade');
                if (rendimentoUnidade) rendimentoUnidade.style.display = 'block';
                if (rendimentoKg) rendimentoKg.style.display = 'none';
                if (document.getElementById('rendimentoPorUnidade')) {
                    document.getElementById('rendimentoPorUnidade').required = true;
                }
                if (document.getElementById('rendimentoPorKg')) {
                    document.getElementById('rendimentoPorKg').required = false;
                }
            } else if (vendaKg && vendaKg.checked) {
                console.log('Mostrando rendimento por kg');
                if (rendimentoUnidade) rendimentoUnidade.style.display = 'none';
                if (rendimentoKg) rendimentoKg.style.display = 'block';
                if (document.getElementById('rendimentoPorUnidade')) {
                    document.getElementById('rendimentoPorUnidade').required = false;
                }
                if (document.getElementById('rendimentoPorKg')) {
                    document.getElementById('rendimentoPorKg').required = true;
                }
            }
            
            // Recalcular custos após alternar
            calcularCustos();
        }
         
         // Função para alternar abas
         function alternarAba(abaId) {
            // Preservar dados antes de alternar
            const dadosPreservados = {};
            
            // Preservar dados dos insumos (incluindo selects)
            document.querySelectorAll('.insumo-nome, .insumo-quantidade, .insumo-custo-unitario, .insumo-custo-total, .insumo-unidade').forEach(input => {
                if (input.value) {
                    dadosPreservados[input.className + '_' + input.id] = input.value;
                }
            });
            
            // Preservar dados da mão de obra
            document.querySelectorAll('.mao-obra-tipo, .mao-obra-tempo, .mao-obra-valor-hora, .mao-obra-custo-total, .mao-obra-valor-fixo').forEach(input => {
                if (input.value) {
                    dadosPreservados[input.className + '_' + input.id] = input.value;
                }
            });
            
                                     // Preservar dados do produto principal
            const nomeProduto = document.getElementById('nomeProduto');
            const rendimentoPorUnidade = document.getElementById('rendimentoPorUnidade');
            const rendimentoPorKg = document.getElementById('rendimentoPorKg');
            
            if (nomeProduto && nomeProduto.value) {
                dadosPreservados['nomeProduto'] = nomeProduto.value;
            }
            if (rendimentoPorUnidade && rendimentoPorUnidade.value) {
                dadosPreservados['rendimentoPorUnidade'] = rendimentoPorUnidade.value;
            }
            if (rendimentoPorKg && rendimentoPorKg.value) {
                dadosPreservados['rendimentoPorKg'] = rendimentoPorKg.value;
            }
            
            // Esconder todas as abas
            document.querySelectorAll('.tab-pane').forEach(pane => {
                pane.classList.remove('active', 'show');
            });
            
            // Resetar todos os botões para outline
            document.querySelectorAll('[id$="-tab"]').forEach(btn => {
                btn.className = 'btn btn-outline-primary';
                btn.style.fontWeight = '600';
                btn.style.padding = '12px 20px';
                btn.style.borderRadius = '8px';
            });
            
            // Mostrar aba selecionada
            const abaSelecionada = document.getElementById(abaId);
            if (abaSelecionada) {
                abaSelecionada.classList.add('active', 'show');
            }
            
            // Ativar botão correspondente
            const botaoAtivo = document.getElementById(`${abaId}-tab`);
            if (botaoAtivo) {
                botaoAtivo.className = 'btn btn-primary';
                botaoAtivo.style.fontWeight = '600';
                botaoAtivo.style.padding = '12px 20px';
                botaoAtivo.style.borderRadius = '8px';
            }
            
            // Restaurar dados após um pequeno delay
            setTimeout(() => {
                // Restaurar dados dos insumos e mão de obra
                Object.keys(dadosPreservados).forEach(key => {
                    if (key.includes('_')) {
                        const [className, id] = key.split('_');
                        const element = document.querySelector(`.${className}#${id}`);
                        if (element && dadosPreservados[key]) {
                            element.value = dadosPreservados[key];
                        }
                    }
                });
                
                                                 // Restaurar dados do produto principal
                if (dadosPreservados['nomeProduto']) {
                    document.getElementById('nomeProduto').value = dadosPreservados['nomeProduto'];
                }
                if (dadosPreservados['rendimentoPorUnidade']) {
                    document.getElementById('rendimentoPorUnidade').value = dadosPreservados['rendimentoPorUnidade'];
                }
                if (dadosPreservados['rendimentoPorKg']) {
                    document.getElementById('rendimentoPorKg').value = dadosPreservados['rendimentoPorKg'];
                }
            }, 100);
        }
        
        // Função para adicionar insumo
        function adicionarInsumo() {
            const container = document.getElementById('insumosContainer');
            const insumoId = `insumo_${contadorInsumos}`;
            
            const insumoHtml = `
                <div class="row mb-3 insumo-item" id="${insumoId}">
                    <div class="col-md-4">
                        <div class="form-floating">
                            <select class="form-control insumo-select" onchange="selecionarInsumo(this)">
                                <option value="">Selecione um insumo</option>
                            </select>
                            <label class="optional">Insumo</label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-floating">
                            <input type="number" class="form-control insumo-quantidade" placeholder="Quantidade" min="0" step="0.01" value="0" onchange="calcularCustoTotalInsumo(this)" required>
                            <label>Quantidade</label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-floating">
                            <input type="number" class="form-control insumo-custo-unitario" placeholder="Custo Unitário" min="0" step="0.01" value="0" onchange="calcularCustoTotalInsumo(this)">
                            <label class="optional">Custo Unitário (R$)</label>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle me-1"></i>Custo sugerido - Atualize se necessário
                            </small>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-floating">
                            <input type="text" class="form-control insumo-custo-total" placeholder="Custo Total" readonly>
                            <label>Custo Total (R$)</label>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-danger" onclick="removerItem('${insumoId}')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `;
            
            container.insertAdjacentHTML('beforeend', insumoHtml);
            
            // Carregar insumos no select recém-criado
            carregarInsumos();
            
            contadorInsumos++;
        }
        
        // Função para adicionar mão de obra
        function adicionarMaoObra() {
            const container = document.getElementById('maoObraContainer');
            const maoObraId = `mao_obra_${contadorMaoObra}`;
            
            const maoObraHtml = `
                <div class="row mb-3 mao-obra-item" id="${maoObraId}">
                    <div class="col-md-2">
                        <div class="form-floating">
                            <select class="form-control funcionario-select" onchange="selecionarFuncionario(this)">
                                <option value="">Selecione um funcionário</option>
                            </select>
                            <label>Funcionário</label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-floating">
                            <input type="number" class="form-control mao-obra-salario" placeholder="Salário" min="0" step="0.01" value="0" onchange="calcularCustoMaoObra(this.closest('.mao-obra-item'))">
                            <label>Salário (R$)</label>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle me-1"></i>Piso salárial da categoria - pode ser editado
                            </small>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-floating">
                            <input type="number" class="form-control mao-obra-tempo" placeholder="Tempo (horas)" min="0" step="0.1" value="0" onchange="calcularCustoMaoObra(this.closest('.mao-obra-item'))">
                            <label>Tempo (horas)</label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-floating">
                            <input type="text" class="form-control mao-obra-valor-hora" placeholder="Valor/Hora" readonly>
                            <label>Valor/Hora (R$)</label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-floating">
                            <input type="number" class="form-control mao-obra-valor-fixo" placeholder="Valor Fixo" min="0" step="0.01" value="0" onchange="calcularCustoMaoObra(this.closest('.mao-obra-item'))">
                            <label>Valor Fixo (R$)</label>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle me-1"></i>Comissões, bônus, diárias, etc.
                            </small>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="form-floating">
                            <input type="text" class="form-control mao-obra-custo-total" placeholder="Custo Total" readonly>
                            <label>Custo Total (R$)</label>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-danger" onclick="removerItem('${maoObraId}')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `;
            
            container.insertAdjacentHTML('beforeend', maoObraHtml);
            
            // Carregar funcionários no select recém-criado
            carregarFuncionarios();
            
            contadorMaoObra++;
        }
        
        // Função para remover item
        function removerItem(itemId) {
            const item = document.getElementById(itemId);
            if (item) {
                item.remove();
                calcularCustos();
            }
        }
        
        // Função para selecionar insumo e preencher custo unitário
        function selecionarInsumo(selectElement) {
            const insumoId = selectElement.value;
            const insumoItem = selectElement.closest('.insumo-item');
            const custoUnitarioInput = insumoItem.querySelector('.insumo-custo-unitario');
            const quantidadeInput = insumoItem.querySelector('.insumo-quantidade');
            
            console.log('Selecionando insumo:', insumoId);
            
            if (insumoId) {
                // Buscar dados do insumo selecionado
                fetch('/extras/buscar-insumo-por-id', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `insumo_id=${insumoId}`
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('Dados recebidos:', data);
                    if (data.sucesso && data.insumo) {
                        console.log('Insumo encontrado:', data.insumo);
                        // Preencher custo unitário com o valor do banco
                        custoUnitarioInput.value = data.insumo.custo;
                        console.log('Custo unitário definido:', data.insumo.custo);
                        // Calcular custo total se quantidade estiver preenchida
                        if (quantidadeInput.value && parseFloat(quantidadeInput.value) > 0) {
                            calcularCustoTotalInsumo(custoUnitarioInput);
                        } else {
                            // Focar no campo quantidade se estiver vazio
                            quantidadeInput.focus();
                            quantidadeInput.classList.add('is-invalid');
                        }
                    } else {
                        console.error('Erro na resposta:', data);
                    }
                })
                .catch(error => {
                    console.error('Erro ao buscar insumo:', error);
                    // Em caso de erro, manter o campo editável
                    custoUnitarioInput.value = '0';
                });
            } else {
                // Limpar campos se nenhum insumo selecionado
                custoUnitarioInput.value = '0';
                insumoItem.querySelector('.insumo-custo-total').value = 'R$ 0,00';
                quantidadeInput.classList.remove('is-invalid');
            }
        }
        
        // Função para calcular custo total do insumo
        function calcularCustoTotalInsumo(inputElement) {
            const insumoItem = inputElement.closest('.insumo-item');
            const quantidade = parseFloat(insumoItem.querySelector('.insumo-quantidade').value) || 0;
            const custoUnitario = parseFloat(insumoItem.querySelector('.insumo-custo-unitario').value) || 0;
            const custoTotal = quantidade * custoUnitario;
            
            // Validar quantidade
            const quantidadeInput = insumoItem.querySelector('.insumo-quantidade');
            if (quantidade <= 0) {
                quantidadeInput.classList.add('is-invalid');
                insumoItem.querySelector('.insumo-custo-total').value = 'R$ 0,00';
            } else {
                quantidadeInput.classList.remove('is-invalid');
                insumoItem.querySelector('.insumo-custo-total').value = formatarMoeda(custoTotal);
            }
            
            calcularCustos();
        }
        
        // Função para calcular custo de mão de obra
        function calcularCustoMaoObra(maoObraElement) {
            const tempo = parseFloat(maoObraElement.querySelector('.mao-obra-tempo').value) || 0;
            const salario = parseFloat(maoObraElement.querySelector('.mao-obra-salario').value) || 0;
            const valorFixo = parseFloat(maoObraElement.querySelector('.mao-obra-valor-fixo').value) || 0;
            
            console.log('Calculando custo mão de obra:');
            console.log('Tempo:', tempo);
            console.log('Salário:', salario);
            console.log('Valor Fixo:', valorFixo);
            
            // Calcular valor hora: salário mensal / 160 horas (média mensal)
            const valorHora = salario > 0 ? salario / 160 : 0;
            console.log('Valor Hora calculado:', valorHora);
            
            // Calcular custo total: (tempo × valor hora) + valor fixo
            const custoPorTempo = tempo * valorHora;
            const custoTotal = custoPorTempo + valorFixo;
            console.log('Custo por tempo:', custoPorTempo);
            console.log('Custo total:', custoTotal);
            
            // Atualizar campos
            const valorHoraInput = maoObraElement.querySelector('.mao-obra-valor-hora');
            const custoTotalInput = maoObraElement.querySelector('.mao-obra-custo-total');
            
            valorHoraInput.value = formatarMoeda(valorHora);
            custoTotalInput.value = formatarMoeda(custoTotal);
            
            console.log('Valor hora formatado:', valorHoraInput.value);
            console.log('Custo total formatado:', custoTotalInput.value);
            
            // Validar tempo
            const tempoInput = maoObraElement.querySelector('.mao-obra-tempo');
            if (tempo <= 0) {
                tempoInput.classList.add('is-invalid');
            } else {
                tempoInput.classList.remove('is-invalid');
            }
            
            calcularCustos();
        }
        
                 // Função principal para calcular custos
         function calcularCustos() {
             // Validar rendimento se estiver na aba resumo
             const resumoAba = document.getElementById('resumo');
             if (resumoAba && resumoAba.classList.contains('active')) {
                 if (!validarRendimento()) {
                     return;
                 }
             }
             
             // Calcular total dos insumos
             let totalInsumos = 0;
            document.querySelectorAll('.insumo-custo-total').forEach(input => {
                const valor = input.value || input.textContent || 'R$ 0,00';
                totalInsumos += parseFloat(valor.replace('R$', '').replace('.', '').replace(',', '.')) || 0;
            });
            
            // Calcular total da mão de obra
            let totalMaoObra = 0;
            document.querySelectorAll('.mao-obra-custo-total').forEach(input => {
                totalMaoObra += parseFloat(input.value.replace('R$', '').replace('.', '').replace(',', '.')) || 0;
            });
            
            // Calcular total das embalagens
            let totalEmbalagem = 0;
            document.querySelectorAll('.embalagem-custo-total').forEach(input => {
                const valor = input.value.replace('R$', '').replace('.', '').replace(',', '.');
                const valorNumerico = parseFloat(valor) || 0;
                totalEmbalagem += valorNumerico;
                console.log('Valor da embalagem:', valor, 'Valor numérico:', valorNumerico);
            });
            
            console.log('Total das embalagens calculado:', totalEmbalagem);
            
            // Atualizar campo embalagem do produto na aba despesas
            const embalagemProdutoElement = document.getElementById('embalagem_produto');
            console.log('Elemento embalagem encontrado:', embalagemProdutoElement);
            if (embalagemProdutoElement) {
                embalagemProdutoElement.value = totalEmbalagem.toFixed(2);
                console.log('Campo embalagem do produto atualizado:', totalEmbalagem.toFixed(2));
            } else {
                console.error('Elemento embalagem não encontrado!');
            }
            
            // Calcular total das despesas com rateio proporcional
            let totalDespesas = 0;
            
            // Calcular total de horas trabalhadas
            const totalHorasTrabalho = document.querySelectorAll('.mao-obra-tempo').length > 0 ? 
                Array.from(document.querySelectorAll('.mao-obra-tempo')).reduce((total, input) => total + (parseFloat(input.value) || 0), 0) : 1;
            
            // Energia Elétrica: Valor mensal / 30 dias / 24h * horas totais trabalhadas
            const energiaMensal = parseFloat(document.getElementById('energia').value) || 0;
            const energiaRateada = (energiaMensal / 30 / 24) * totalHorasTrabalho;
            totalDespesas += energiaRateada;
            
            // Gás: Valor mensal / 30 dias / 24h * horas totais trabalhadas
            const gasMensal = parseFloat(document.getElementById('gas').value) || 0;
            const gasRateado = (gasMensal / 30 / 24) * totalHorasTrabalho;
            totalDespesas += gasRateado;
            
            // Água: Valor mensal / 30 dias / 24h * horas totais trabalhadas
            const aguaMensal = parseFloat(document.getElementById('agua').value) || 0;
            const aguaRateada = (aguaMensal / 30 / 24) * totalHorasTrabalho;
            totalDespesas += aguaRateada;
            
            // Aluguel: Valor mensal / 30 dias / 24h * horas totais trabalhadas
            const aluguelMensal = parseFloat(document.getElementById('aluguel').value) || 0;
            const aluguelRateado = (aluguelMensal / 30 / 24) * totalHorasTrabalho;
            totalDespesas += aluguelRateado;
            
            // Transporte: Valor diário / 8h * horas totais trabalhadas
            const transporteDiario = parseFloat(document.getElementById('transporte').value) || 0;
            const transporteRateado = (transporteDiario / 8) * totalHorasTrabalho;
            totalDespesas += transporteRateado;
            
            // Despesas diretas do produto
            const despesasDiretas = ['embalagem_produto', 'outras'];
            despesasDiretas.forEach(id => {
                totalDespesas += parseFloat(document.getElementById(id).value) || 0;
            });
            
            // Impostos e taxas (percentuais sobre o preço de venda - serão aplicados depois)
            const impostosEstaduais = parseFloat(document.getElementById('impostos_estaduais').value) || 0;
            const impostosFederais = parseFloat(document.getElementById('impostos_federais').value) || 0;
            const taxaCartao = parseFloat(document.getElementById('taxa_cartao').value) || 0;
            
            // Calcular custo total de produção (sem duplicar embalagem)
            const custoTotalProducao = totalInsumos + totalMaoObra + totalDespesas;
            
            // Calcular custo unitário baseado no tipo de venda e rendimento
            const vendaUnidade = document.getElementById('vendaUnidade').checked;
            let quantidadeParaCalculo = 1;
            
            if (vendaUnidade) {
                quantidadeParaCalculo = parseFloat(document.getElementById('rendimentoPorUnidade').value) || 1;
            } else {
                quantidadeParaCalculo = parseFloat(document.getElementById('rendimentoPorKg').value) || 1;
            }
            
            const custoUnitario = custoTotalProducao / quantidadeParaCalculo;
            
            // Atualizar displays de custos
            document.getElementById('totalInsumos').textContent = formatarMoeda(totalInsumos);
            document.getElementById('totalMaoObra').textContent = formatarMoeda(totalMaoObra);
            document.getElementById('totalEmbalagem').textContent = formatarMoeda(totalEmbalagem);
            document.getElementById('totalDespesas').textContent = formatarMoeda(totalDespesas);
            
            // Atualizar total de horas
            document.getElementById('totalHoras').textContent = totalHorasTrabalho.toFixed(1);
            
            // Calcular percentuais em relação ao custo total de produção
            const percentualInsumos = custoTotalProducao > 0 ? (totalInsumos / custoTotalProducao) * 100 : 0;
            const percentualMaoObra = custoTotalProducao > 0 ? (totalMaoObra / custoTotalProducao) * 100 : 0;
            const percentualDespesas = custoTotalProducao > 0 ? (totalDespesas / custoTotalProducao) * 100 : 0;
            
            document.getElementById('resumoInsumos').textContent = formatarMoeda(totalInsumos) + ' (' + percentualInsumos.toFixed(2) + '%)';
            document.getElementById('resumoMaoObra').textContent = formatarMoeda(totalMaoObra) + ' (' + percentualMaoObra.toFixed(2) + '%)';
            document.getElementById('resumoDespesas').textContent = formatarMoeda(totalDespesas) + ' (' + percentualDespesas.toFixed(2) + '%)';
            
            // Calcular e exibir total de impostos e taxas
            const totalImpostosTaxas = impostosEstaduais + impostosFederais + taxaCartao;
            
            // Calcular o valor monetário dos impostos e taxas baseado no preço de venda sugerido
            let valorMonetarioImpostos = 0;
            const precoVendaSugerido = parseFloat(document.getElementById('sugestaoVenda').textContent.replace('R$', '').replace('.', '').replace(',', '.')) || 0;
            if (precoVendaSugerido > 0) {
                valorMonetarioImpostos = (precoVendaSugerido * totalImpostosTaxas) / 100;
            }
            
            // Exibir valor monetário e percentual
            document.getElementById('resumoImpostosTaxas').textContent = formatarMoeda(valorMonetarioImpostos) + ' (' + totalImpostosTaxas.toFixed(2) + '%)';
            
            document.getElementById('custoTotalProducao').textContent = formatarMoeda(custoTotalProducao);
            document.getElementById('custoUnitario').textContent = formatarMoeda(custoUnitario);
            
            // Verificar se há valores nos campos de preço para decidir se deve calcular automaticamente
            const precoFinal = document.getElementById('precoFinal').value;
            const margemDireta = document.getElementById('margemLucro').value;
            
            // Se não há valores nos campos de preço, calcular preço sugerido padrão
            if (!precoFinal && !margemDireta) {
                const margemLucro = 100; // Margem padrão
                let precoVenda = custoUnitario * (1 + margemLucro / 100);
                
                // Aplicar impostos e taxas como percentuais sobre o preço de venda
                const totalImpostosTaxas = impostosEstaduais + impostosFederais + taxaCartao;
                if (totalImpostosTaxas > 0) {
                    precoVenda = precoVenda / (1 - (totalImpostosTaxas / 100));
                }
                

            }
        }
        
        // Função para calcular total de horas trabalhadas
        function calcularTotalHorasTrabalho() {
            let totalHoras = 0;
            const maoObraItems = document.querySelectorAll('.mao-obra-item');
            
            maoObraItems.forEach(item => {
                const tempo = parseFloat(item.querySelector('.mao-obra-tempo')?.value || 0);
                totalHoras += tempo;
            });
            
            return totalHoras;
        }
        
        // Função para formatar moeda
        function formatarMoeda(valor) {
            // Verificar se o valor é válido
            if (isNaN(valor) || valor === null || valor === undefined) {
                return 'R$ 0,00';
            }
            
            return new Intl.NumberFormat('pt-BR', {
                style: 'currency',
                currency: 'BRL'
            }).format(valor);
        }
        
        // Função para salvar ficha técnica e gerar PDF
        function salvarFichaTecnica() {
            // Validar todos os campos obrigatórios
            const validacao = validarTodosCamposObrigatorios();
            if (!validacao.valido) {
                mostrarModalAviso(validacao.camposFaltantes);
                return;
            }
            
            // Validar rendimento
            if (!validarRendimento()) {
                return;
            }
            
            // Calcular custos para garantir que todos os valores estejam atualizados
            calcularCustos();
            
            // Coletar todos os dados do produto
            const dadosProduto = coletarDadosProduto();
            
            // Debug: mostrar dados coletados no console
            console.log('Dados coletados para PDF:', dadosProduto);
            
            // Gerar PDF com os dados
            gerarPDF(dadosProduto);
        }
        
        // Função para coletar todos os dados do produto
        function coletarDadosProduto() {
            const dados = {
                produto: {
                    nome: document.getElementById('nomeProduto').value || 'Produto não informado',
                    tipoVenda: document.querySelector('input[name="tipoVenda"]:checked')?.value || 'unidade',
                    rendimento: document.getElementById('vendaUnidade').checked ? 
                        document.getElementById('rendimentoPorUnidade').value : 
                        document.getElementById('rendimentoPorKg').value
                },
                insumos: [],
                embalagens: [],
                maoObra: [],
                despesas: {},
                resumo: {}
            };
            
            // Coletar insumos
            const insumos = document.querySelectorAll('.insumo-item');
            console.log('Insumos encontrados:', insumos.length);
            console.log('Elementos insumo-item:', insumos);
            insumos.forEach((insumo, index) => {
                console.log(`Processando insumo ${index}:`, insumo);
                const selectElement = insumo.querySelector('.insumo-select');
                const quantidadeElement = insumo.querySelector('.insumo-quantidade');
                const custoUnitarioElement = insumo.querySelector('.insumo-custo-unitario');
                const custoTotalElement = insumo.querySelector('.insumo-custo-total');
                
                console.log(`Elementos encontrados no insumo ${index}:`, {
                    select: selectElement,
                    quantidade: quantidadeElement,
                    custoUnitario: custoUnitarioElement,
                    custoTotal: custoTotalElement
                });
                
                // Obter nome e medida do insumo selecionado
                let nome = '';
                let medida = '';
                if (selectElement && selectElement.value) {
                    const option = selectElement.querySelector(`option[value="${selectElement.value}"]`);
                    if (option) {
                        const textoCompleto = option.textContent;
                        const partes = textoCompleto.split(' - ');
                        nome = partes[0] || '';
                        medida = partes[1] || '';
                    }
                }
                
                const quantidade = quantidadeElement ? quantidadeElement.value || '' : '';
                const custoUnitario = custoUnitarioElement ? custoUnitarioElement.value || '' : '';
                const custoTotal = custoTotalElement ? custoTotalElement.value || custoTotalElement.textContent || 'R$ 0,00' : 'R$ 0,00';
                
                // Só adicionar se pelo menos o nome estiver preenchido
                if (nome.trim()) {
                    const insumoData = {
                        nome: nome.trim(),
                        unidade: medida || '-',
                        quantidade: quantidade || '0',
                        custoUnitario: custoUnitario ? formatarMoeda(parseFloat(custoUnitario)) : 'R$ 0,00',
                        custoTotal: custoTotal
                    };
                    dados.insumos.push(insumoData);
                    console.log('Insumo adicionado:', insumoData);
                }
            });
            
            // Coletar mão de obra
            const maoObra = document.querySelectorAll('.mao-obra-item');
            console.log('Mão de obra encontrada:', maoObra.length);
            console.log('Elementos mao-obra-item:', maoObra);
            maoObra.forEach((item, index) => {
                console.log(`Processando mão de obra ${index}:`, item);
                const selectElement = item.querySelector('.funcionario-select');
                const salarioElement = item.querySelector('.mao-obra-salario');
                const tempoElement = item.querySelector('.mao-obra-tempo');
                const valorHoraElement = item.querySelector('.mao-obra-valor-hora');
                const valorFixoElement = item.querySelector('.mao-obra-valor-fixo');
                const custoTotalElement = item.querySelector('.mao-obra-custo-total');
                
                console.log(`Elementos encontrados na mão de obra ${index}:`, {
                    select: selectElement,
                    salario: salarioElement,
                    tempo: tempoElement,
                    valorHora: valorHoraElement,
                    valorFixo: valorFixoElement,
                    custoTotal: custoTotalElement
                });
                
                // Obter profissão do funcionário selecionado
                let profissao = '';
                if (selectElement && selectElement.value) {
                    const option = selectElement.querySelector(`option[value="${selectElement.value}"]`);
                    if (option) {
                        profissao = option.textContent;
                    }
                }
                
                const salario = salarioElement ? salarioElement.value || '' : '';
                const tempo = tempoElement ? tempoElement.value || '' : '';
                const valorHora = valorHoraElement ? valorHoraElement.value || '' : '';
                const valorFixo = valorFixoElement ? valorFixoElement.value || '' : '';
                const custoTotal = custoTotalElement ? custoTotalElement.value || custoTotalElement.textContent || 'R$ 0,00' : 'R$ 0,00';
                
                console.log('Valores coletados da mão de obra:');
                console.log('Salário:', salario);
                console.log('Tempo:', tempo);
                console.log('Valor Hora:', valorHora);
                console.log('Valor Fixo:', valorFixo);
                console.log('Custo Total:', custoTotal);
                
                // Só adicionar se pelo menos a profissão estiver preenchida
                if (profissao.trim()) {
                    const maoObraData = {
                        tipo: profissao.trim(),
                        salario: salario ? formatarMoeda(parseFloat(salario) || 0) : 'R$ 0,00',
                        tempo: tempo || '0',
                        valor: valorHora || 'R$ 0,00', // Usar valor direto sem formatar novamente
                        valorFixo: valorFixo ? formatarMoeda(parseFloat(valorFixo) || 0) : 'R$ 0,00',
                        custoTotal: custoTotal
                    };
                    dados.maoObra.push(maoObraData);
                    console.log('Mão de obra adicionada:', maoObraData);
                }
            });
            
            // Coletar despesas calculadas (valores proporcionais à receita)
            const despesasNomes = {
                energia: 'Energia Elétrica',
                gas: 'Gás',
                agua: 'Água',
                aluguel: 'Aluguel',
                transporte: 'Transporte',
                embalagem: 'Embalagem',
                impostos_estaduais: 'Impostos Estaduais (%)',
                impostos_federais: 'Impostos Federais (%)',
                taxa_cartao: 'Taxa de Cartão (%)',
                outras: 'Outras Despesas'
            };
            
            // Calcular valores proporcionais das despesas
            const totalHorasTrabalho = calcularTotalHorasTrabalho();
            const energiaElement = document.getElementById('energia');
            const gasElement = document.getElementById('gas');
            const aguaElement = document.getElementById('agua');
            const aluguelElement = document.getElementById('aluguel');
            const transporteElement = document.getElementById('transporte');
            const embalagemElement = document.getElementById('embalagem_produto');
            const outrasElement = document.getElementById('outras');
            
            const energia = energiaElement ? parseFloat(energiaElement.value || 0) : 0;
            const gas = gasElement ? parseFloat(gasElement.value || 0) : 0;
            const agua = aguaElement ? parseFloat(aguaElement.value || 0) : 0;
            const aluguel = aluguelElement ? parseFloat(aluguelElement.value || 0) : 0;
            const transporte = transporteElement ? parseFloat(transporteElement.value || 0) : 0;
            const embalagem = embalagemElement ? parseFloat(embalagemElement.value || 0) : 0;
            const outras = outrasElement ? parseFloat(outrasElement.value || 0) : 0;
            
            // Calcular valores proporcionais
            const energiaCalculada = energia > 0 ? (energia / 30 / 24) * totalHorasTrabalho : 0;
            const gasCalculado = gas > 0 ? (gas / 30 / 24) * totalHorasTrabalho : 0;
            const aguaCalculada = agua > 0 ? (agua / 30 / 24) * totalHorasTrabalho : 0;
            const aluguelCalculado = aluguel > 0 ? (aluguel / 30 / 24) * totalHorasTrabalho : 0;
            const transporteCalculado = transporte > 0 ? (transporte / 8) * totalHorasTrabalho : 0;
            
            // Adicionar despesas calculadas
            if (energiaCalculada > 0) dados.despesas['energia'] = formatarMoeda(energiaCalculada);
            if (gasCalculado > 0) dados.despesas['gas'] = formatarMoeda(gasCalculado);
            if (aguaCalculada > 0) dados.despesas['agua'] = formatarMoeda(aguaCalculada);
            if (aluguelCalculado > 0) dados.despesas['aluguel'] = formatarMoeda(aluguelCalculado);
            if (transporteCalculado > 0) dados.despesas['transporte'] = formatarMoeda(transporteCalculado);
            if (embalagem > 0) dados.despesas['embalagem'] = formatarMoeda(embalagem);
            if (outras > 0) dados.despesas['outras'] = formatarMoeda(outras);
            
            // Adicionar impostos e taxas (percentuais)
            const impostosEstaduaisElement = document.getElementById('impostos_estaduais');
            const impostosFederaisElement = document.getElementById('impostos_federais');
            const taxaCartaoElement = document.getElementById('taxa_cartao');
            
            const impostosEstaduais = impostosEstaduaisElement ? impostosEstaduaisElement.value || '' : '';
            const impostosFederais = impostosFederaisElement ? impostosFederaisElement.value || '' : '';
            const taxaCartao = taxaCartaoElement ? taxaCartaoElement.value || '' : '';
            
            if (impostosEstaduais) dados.despesas['impostos_estaduais'] = impostosEstaduais + '%';
            if (impostosFederais) dados.despesas['impostos_federais'] = impostosFederais + '%';
            if (taxaCartao) dados.despesas['taxa_cartao'] = taxaCartao + '%';
            
            // Coletar resumo
            const resumoIds = ['resumoInsumos', 'resumoMaoObra', 'resumoDespesas', 'resumoImpostosTaxas', 'custoTotalProducao', 'custoUnitario', 'margemCalculada', 'sugestaoVenda'];
            resumoIds.forEach(id => {
                const elemento = document.getElementById(id);
                const valor = elemento ? (elemento.textContent || elemento.value || '') : '';
                if (valor && valor.trim()) {
                    dados.resumo[id] = valor.trim();
                }
            });
            
            // Coletar embalagens
            const embalagens = document.querySelectorAll('.embalagem-item');
            console.log('Embalagens encontradas:', embalagens.length);
            embalagens.forEach((embalagem, index) => {
                console.log(`Processando embalagem ${index}:`, embalagem);
                const selectElement = embalagem.querySelector('.embalagem-select');
                const quantidadeElement = embalagem.querySelector('.embalagem-quantidade');
                const volumeElement = embalagem.querySelector('.embalagem-volume');
                const custoElement = embalagem.querySelector('.embalagem-custo');
                const custoUnitarioElement = embalagem.querySelector('.embalagem-custo-unitario');
                const custoTotalElement = embalagem.querySelector('.embalagem-custo-total');
                
                // Obter nome da embalagem selecionada
                let nomeEmbalagem = '';
                if (selectElement && selectElement.value) {
                    const option = selectElement.querySelector(`option[value="${selectElement.value}"]`);
                    if (option) {
                        nomeEmbalagem = option.textContent;
                    }
                }
                
                const quantidade = quantidadeElement ? quantidadeElement.value || '' : '';
                const volume = volumeElement ? volumeElement.value || '' : '';
                const custo = custoElement ? custoElement.value || '' : '';
                const custoUnitario = custoUnitarioElement ? custoUnitarioElement.value || '' : '';
                const custoTotal = custoTotalElement ? custoTotalElement.value || custoTotalElement.textContent || 'R$ 0,00' : 'R$ 0,00';
                
                // Só adicionar se pelo menos o nome estiver preenchido
                if (nomeEmbalagem.trim()) {
                    const embalagemData = {
                        nome: nomeEmbalagem.trim(),
                        quantidade: quantidade || '0',
                        volume: volume || '0',
                        custo: custo ? formatarMoeda(parseFloat(custo)) : 'R$ 0,00',
                        custoUnitario: custoUnitario,
                        custoTotal: custoTotal
                    };
                    dados.embalagens.push(embalagemData);
                    console.log('Embalagem adicionada:', embalagemData);
                }
            });
            
            return dados;
        }
        
        // Função para gerar PDF
        function gerarPDF(dados) {
            // Criar conteúdo HTML para o PDF
            const conteudoHTML = criarConteudoPDF(dados);
            
            // Abrir nova janela com o conteúdo
            const novaJanela = window.open('', '_blank', 'width=800,height=600');
            novaJanela.document.write(`
                <!DOCTYPE html>
                <html lang="pt-BR">
                <head>
                    <meta charset="UTF-8">
                    <title>Ficha Técnica - ${dados.produto.nome}</title>
                    <style>
                        @page {
                            size: A4;
                            margin: 1cm;
                        }
                        
                        body { 
                            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
                            margin: 0; 
                            padding: 15px;
                            font-size: 11px;
                            line-height: 1.3;
                            color: #333;
                        }
                        
                        .header { 
                            text-align: center; 
                            border-bottom: 3px solid #2c3e50; 
                            padding-bottom: 15px; 
                            margin-bottom: 20px;
                            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                            color: white;
                            padding: 20px;
                            border-radius: 8px;
                            margin: -15px -15px 20px -15px;
                        }
                        
                        .header-content {
                            display: flex;
                            align-items: center;
                            justify-content: space-between;
                            max-width: 100%;
                        }
                        
                        .logo-section {
                            flex: 0 0 120px;
                            text-align: left;
                        }
                        
                        .logo {
                            width: 200px;
                            height: auto;
                            max-height: 80px;
                            object-fit: contain;
                        }
                        
                        .title-section {
                            flex: 1;
                            text-align: center;
                        }
                        
                        .website {
                            margin: 5px 0 0 0;
                            font-size: 12px;
                            opacity: 0.8;
                            font-style: italic;
                        }
                        
                        .header h1 {
                            margin: 0;
                            font-size: 24px;
                            font-weight: bold;
                            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
                        }
                        
                        .header h2 {
                            margin: 5px 0 0 0;
                            font-size: 18px;
                            font-weight: normal;
                        }
                        
                        .header p {
                            margin: 8px 0 0 0;
                            font-size: 14px;
                            opacity: 0.9;
                        }
                        
                        .section { 
                            margin-bottom: 20px; 
                            page-break-inside: avoid;
                        }
                        
                        .section h2 { 
                            color: #2c3e50; 
                            border-bottom: 2px solid #3498db; 
                            padding-bottom: 8px; 
                            margin-bottom: 15px;
                            font-size: 16px;
                            font-weight: bold;
                            background: #ecf0f1;
                            padding: 10px;
                            border-radius: 5px;
                        }
                        
                        .table { 
                            width: 100%; 
                            border-collapse: collapse; 
                            margin-bottom: 15px;
                            font-size: 10px;
                            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                        }
                        
                        .table th, .table td { 
                            border: 1px solid #bdc3c7; 
                            padding: 6px 8px; 
                            text-align: left; 
                            vertical-align: middle;
                        }
                        
                        .table th { 
                            background: linear-gradient(135deg, #3498db, #2980b9);
                            color: white;
                            font-weight: bold;
                            font-size: 11px;
                        }
                        
                        .table tr:nth-child(even) {
                            background-color: #f8f9fa;
                        }
                        
                        .table tr:hover {
                            background-color: #e8f4fd;
                        }
                        
                        .highlight { 
                            background: linear-gradient(135deg, #f39c12, #e67e22);
                            color: white;
                            padding: 20px; 
                            border-radius: 10px; 
                            margin: 20px 0;
                            text-align: center;
                            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
                        }
                        
                        .highlight h2 {
                            margin: 0 0 10px 0;
                            font-size: 18px;
                            border: none;
                            background: none;
                            padding: 0;
                        }
                        
                        .highlight h1 {
                            margin: 0;
                            font-size: 28px;
                            font-weight: bold;
                            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
                        }
                        
                        .resumo-grid {
                            display: grid;
                            grid-template-columns: 1fr 1fr;
                            gap: 10px;
                            margin-bottom: 15px;
                        }
                        
                        .resumo-item { 
                            margin: 0;
                            padding: 12px; 
                            background: linear-gradient(135deg, #ecf0f1, #bdc3c7);
                            border-radius: 8px;
                            border-left: 4px solid #3498db;
                            font-size: 11px;
                            font-weight: bold;
                        }
                        
                        .resumo-item strong {
                            color: #2c3e50;
                            display: block;
                            margin-bottom: 5px;
                            font-size: 10px;
                            text-transform: uppercase;
                        }
                        
                        .resumo-item span {
                            color: #e74c3c;
                            font-size: 12px;
                        }
                        
                        .compact-table {
                            font-size: 9px;
                        }
                        
                        .compact-table th,
                        .compact-table td {
                            padding: 4px 6px;
                        }
                        
                        .no-data {
                            text-align: center;
                            color: #7f8c8d;
                            font-style: italic;
                            padding: 20px;
                            background: #f8f9fa;
                            border-radius: 5px;
                            border: 1px dashed #bdc3c7;
                        }
                        
                        .footer-note {
                            font-size: 9px;
                            color: #7f8c8d;
                            text-align: center;
                            margin-top: 15px;
                            padding: 10px;
                            background: #f8f9fa;
                            border-radius: 5px;
                            border-top: 1px solid #ecf0f1;
                        }
                        
                        @media print {
                            body { 
                                margin: 0; 
                                padding: 10px;
                            }
                            .no-print { display: none; }
                            .section { page-break-inside: avoid; }
                            .header { 
                                margin: -10px -10px 15px -10px;
                                padding: 15px;
                            }
                        }
                    </style>
                </head>
                <body>
                    ${conteudoHTML}
                    <div class="no-print" style="text-align: center; margin-top: 20px;">
                        <button onclick="window.print()" style="background: #3498db; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; margin-right: 10px;">Imprimir PDF</button>
                        <button onclick="window.close()" style="background: #e74c3c; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer;">Fechar</button>
                    </div>
                </body>
                </html>
            `);
            novaJanela.document.close();
        }
        
        // Função para criar conteúdo HTML do PDF
        function criarConteudoPDF(dados) {
            let html = `
                <div class="header">
                    <div class="header-content">
                        <div class="logo-section">
                            <img src="/public/assets/images/logo.png" alt="Logo Agil Fiscal" class="logo">
                        </div>
                        <div class="title-section">
                            <h1>📋 FICHA TÉCNICA DO PRODUTO</h1>
                            <h2>${dados.produto.nome || 'Produto'}</h2>
                            <p><strong>Tipo de Venda:</strong> ${dados.produto.tipoVenda === 'unidade' ? 'Por Unidade' : 'Por Kg'}</p>
                            <p><strong>Rendimento:</strong> ${dados.produto.rendimento || '0'} ${dados.produto.tipoVenda === 'unidade' ? 'unidades' : 'kg'}</p>
                            <p class="website">www.agilfiscal.com.br</p>
                        </div>
                    </div>
                </div>
            `;
            
            // Seção de Insumos
            if (dados.insumos && dados.insumos.length > 0) {
                html += `
                    <div class="section">
                        <h2>🥘 INSUMOS (MATÉRIA-PRIMA)</h2>
                        <table class="table compact-table">
                            <thead>
                                <tr>
                                    <th>Insumo</th>
                                    <th>Unidade</th>
                                    <th>Quantidade</th>
                                    <th>Custo Unit.</th>
                                    <th>Custo Total</th>
                                </tr>
                            </thead>
                            <tbody>
                `;
                
                dados.insumos.forEach(insumo => {
                    html += `
                        <tr>
                            <td>${insumo.nome || 'N/A'}</td>
                            <td>${insumo.unidade || 'N/A'}</td>
                            <td>${insumo.quantidade || '0'}</td>
                            <td>${insumo.custoUnitario || 'R$ 0,00'}</td>
                            <td>${insumo.custoTotal || 'R$ 0,00'}</td>
                        </tr>
                    `;
                });
                
                html += `
                            </tbody>
                        </table>
                    </div>
                `;
            } else {
                html += `
                    <div class="section">
                        <h2>🥘 INSUMOS (MATÉRIA-PRIMA)</h2>
                        <div class="no-data">Nenhum insumo registrado</div>
                    </div>
                `;
            }
            
            // Seção de Mão de Obra
            if (dados.maoObra && dados.maoObra.length > 0) {
                html += `
                    <div class="section">
                        <h2>👥 MÃO DE OBRA</h2>
                        <table class="table compact-table">
                            <thead>
                                <tr>
                                    <th>Tipo</th>
                                    <th>Tempo (h)</th>
                                    <th>Valor/Hora</th>
                                    <th>Custo Total</th>
                                </tr>
                            </thead>
                            <tbody>
                `;
                
                dados.maoObra.forEach(item => {
                    html += `
                        <tr>
                            <td>${item.tipo || 'N/A'}</td>
                            <td>${item.tempo || '0'}</td>
                            <td>${item.valor || 'R$ 0,00'}</td>
                            <td>${item.custoTotal || 'R$ 0,00'}</td>
                        </tr>
                    `;
                });
                
                html += `
                            </tbody>
                        </table>
                    </div>
                `;
            } else {
                html += `
                    <div class="section">
                        <h2>👥 MÃO DE OBRA</h2>
                        <div class="no-data">Nenhum colaborador registrado</div>
                    </div>
                `;
            }
            
            // Seção de Despesas
            const despesasNomes = {
                energia: '⚡ Energia Elétrica',
                gas: '🔥 Gás',
                agua: '💧 Água',
                aluguel: '🏢 Aluguel',
                transporte: '🚚 Transporte',
                embalagem: '📦 Embalagem',
                impostos_estaduais: '🏛️ Impostos Estaduais',
                impostos_federais: '🏛️ Impostos Federais',
                taxa_cartao: '💳 Taxa de Cartão',
                outras: '📝 Outras Despesas'
            };
            
            if (dados.despesas && Object.keys(dados.despesas).length > 0) {
                html += `
                    <div class="section">
                        <h2>💰 DESPESAS (Valores Calculados para esta Receita)</h2>
                        <table class="table compact-table">
                            <thead>
                                <tr>
                                    <th>Despesa</th>
                                    <th>Valor Calculado</th>
                                </tr>
                            </thead>
                            <tbody>
                `;
                
                Object.entries(dados.despesas).forEach(([key, value]) => {
                    html += `
                        <tr>
                            <td>${despesasNomes[key] || key}</td>
                            <td>${value || 'R$ 0,00'}</td>
                        </tr>
                    `;
                });
                
                html += `
                            </tbody>
                        </table>
                        <div class="footer-note">
                            <em>💡 Valores calculados proporcionalmente ao tempo de produção desta receita</em>
                        </div>
                    </div>
                `;
            } else {
                html += `
                    <div class="section">
                        <h2>💰 DESPESAS (Valores Calculados para esta Receita)</h2>
                        <div class="no-data">Nenhuma despesa registrada</div>
                    </div>
                `;
            }
            
            // Seção de Resumo
            html += `
                <div class="section">
                    <h2>📊 RESUMO DOS CUSTOS</h2>
                    <div class="resumo-grid">
                        <div class="resumo-item">
                            <strong>Total de Insumos</strong>
                            <span>${dados.resumo.resumoInsumos || 'R$ 0,00'}</span>
                        </div>
                        <div class="resumo-item">
                            <strong>Total da Mão de Obra</strong>
                            <span>${dados.resumo.resumoMaoObra || 'R$ 0,00'}</span>
                        </div>
                        <div class="resumo-item">
                            <strong>Total das Despesas</strong>
                            <span>${dados.resumo.resumoDespesas || 'R$ 0,00'}</span>
                        </div>
                        <div class="resumo-item">
                            <strong>Total de Impostos e Taxas</strong>
                            <span>${dados.resumo.resumoImpostosTaxas || 'R$ 0,00'}</span>
                        </div>
                        <div class="resumo-item">
                            <strong>Custo Total de Produção</strong>
                            <span>${dados.resumo.custoTotalProducao || 'R$ 0,00'}</span>
                        </div>
                        <div class="resumo-item">
                            <strong>Custo Unitário</strong>
                            <span>${dados.resumo.custoUnitario || 'R$ 0,00'}</span>
                        </div>
                        <div class="resumo-item">
                            <strong>Margem Calculada</strong>
                            <span>${dados.resumo.margemCalculada || '0%'}</span>
                        </div>
                    </div>
                </div>
            `;
            
            // Destaque da Sugestão de Venda
            if (dados.resumo.sugestaoVenda) {
                html += `
                    <div class="highlight">
                        <h2>🎯 SUGESTÃO DE VENDA</h2>
                        <h1>${dados.resumo.sugestaoVenda}</h1>
                    </div>
                `;
            }
            
            return html;
        }
        
                          // Função para validar campos de rendimento
         function validarRendimento() {
             const vendaUnidade = document.getElementById('vendaUnidade');
             const rendimentoUnidade = document.getElementById('rendimentoPorUnidade');
             const rendimentoKg = document.getElementById('rendimentoPorKg');
             
             if (vendaUnidade.checked) {
                 if (!rendimentoUnidade.value || parseFloat(rendimentoUnidade.value) <= 0) {
                     alert('Por favor, preencha o rendimento por unidade!');
                     rendimentoUnidade.focus();
                     return false;
                 }
             } else {
                 if (!rendimentoKg.value || parseFloat(rendimentoKg.value) <= 0) {
                     alert('Por favor, preencha o rendimento por kg!');
                     rendimentoKg.focus();
                     return false;
                 }
             }
             return true;
         }
         
                 // Função para calcular preço baseado na margem
         function calcularPreco() {
            // Validar todos os campos obrigatórios antes de calcular
            const validacao = validarTodosCamposObrigatorios();
            if (!validacao.valido) {
                mostrarModalAviso(validacao.camposFaltantes);
                return;
            }
            
            // Validar rendimento antes de calcular
            if (!validarRendimento()) {
                return;
            }
            
            const custoUnitario = parseFloat(document.getElementById('custoUnitario').textContent.replace('R$', '').replace('.', '').replace(',', '.')) || 0;
            const margemDireta = parseFloat(document.getElementById('margemLucro').value) || 0;
           
            if (custoUnitario > 0 && margemDireta > 0) {
                // Calcular preço sugerido baseado na margem
                const precoSugerido = custoUnitario * (1 + margemDireta / 100);
                
                // Aplicar impostos e taxas
                const impostosEstaduais = parseFloat(document.getElementById('impostos_estaduais').value) || 0;
                const impostosFederais = parseFloat(document.getElementById('impostos_federais').value) || 0;
                const taxaCartao = parseFloat(document.getElementById('taxa_cartao').value) || 0;
                
                const totalImpostosTaxas = impostosEstaduais + impostosFederais + taxaCartao;
                let precoFinal = precoSugerido;
                
                if (totalImpostosTaxas > 0) {
                    precoFinal = precoSugerido / (1 - (totalImpostosTaxas / 100));
                }
                
                // Atualizar sugestão de venda
                document.getElementById('sugestaoVenda').textContent = formatarMoeda(precoFinal);
                
                // Calcular margem real
                const margemCalculada = ((precoFinal - custoUnitario) / precoFinal) * 100;
                document.getElementById('margemCalculada').textContent = margemCalculada.toFixed(2) + '%';
                
                // Atualizar valor monetário dos impostos e taxas
                const valorMonetarioImpostos = (precoFinal * totalImpostosTaxas) / 100;
                document.getElementById('resumoImpostosTaxas').textContent = formatarMoeda(valorMonetarioImpostos) + ' (' + totalImpostosTaxas.toFixed(2) + '%)';
            }
        }
         
         // Função para calcular margem baseada no preço final
         function calcularMargem() {
            // Validar todos os campos obrigatórios antes de calcular
            const validacao = validarTodosCamposObrigatorios();
            if (!validacao.valido) {
                mostrarModalAviso(validacao.camposFaltantes);
                return;
            }
            
            // Validar rendimento antes de calcular
            if (!validarRendimento()) {
                return;
            }
            
            const custoUnitario = parseFloat(document.getElementById('custoUnitario').textContent.replace('R$', '').replace('.', '').replace(',', '.')) || 0;
            const precoFinal = parseFloat(document.getElementById('precoFinal').value) || 0;
            
            if (custoUnitario > 0 && precoFinal > 0) {
                // Calcular margem percentual
                const margemCalculada = ((precoFinal - custoUnitario) / precoFinal) * 100;
                
                // Atualizar display
                document.getElementById('margemCalculada').textContent = margemCalculada.toFixed(2) + '%';
                
                // Atualizar sugestão de venda
                document.getElementById('sugestaoVenda').textContent = formatarMoeda(precoFinal);
                
                // Calcular e atualizar valor monetário dos impostos e taxas
                const impostosEstaduais = parseFloat(document.getElementById('impostos_estaduais').value) || 0;
                const impostosFederais = parseFloat(document.getElementById('impostos_federais').value) || 0;
                const taxaCartao = parseFloat(document.getElementById('taxa_cartao').value) || 0;
                const totalImpostosTaxas = impostosEstaduais + impostosFederais + taxaCartao;
                const valorMonetarioImpostos = (precoFinal * totalImpostosTaxas) / 100;
                document.getElementById('resumoImpostosTaxas').textContent = formatarMoeda(valorMonetarioImpostos) + ' (' + totalImpostosTaxas.toFixed(2) + '%)';
            }
        }
         
        // Funções para gerenciar insumos e funcionários
        function abrirModalInsumo() {
            document.getElementById('modalInsumo').style.display = 'block';
        }
        
        function fecharModalInsumo() {
            document.getElementById('modalInsumo').style.display = 'none';
            document.getElementById('formInsumo').reset();
        }
        
        function abrirModalFuncionario() {
            document.getElementById('modalFuncionario').style.display = 'block';
        }
        
        function fecharModalFuncionario() {
            document.getElementById('modalFuncionario').style.display = 'none';
            document.getElementById('formFuncionario').reset();
        }
        
        function cadastrarInsumo() {
            const formData = new FormData(document.getElementById('formInsumo'));
            
            fetch('/extras/cadastrar-insumo', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.sucesso) {
                    alert(data.mensagem);
                    fecharModalInsumo();
                    carregarInsumos();
                } else {
                    alert('Erro: ' + data.erro);
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao cadastrar insumo');
            });
        }
        
        function cadastrarFuncionario() {
            const formData = new FormData(document.getElementById('formFuncionario'));
            
            fetch('/extras/cadastrar-funcionario', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.sucesso) {
                    alert(data.mensagem);
                    fecharModalFuncionario();
                    carregarFuncionarios();
                } else {
                    alert('Erro: ' + data.erro);
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao cadastrar funcionário');
            });
        }
        
        function carregarInsumos() {
            fetch('/extras/buscar-insumos')
            .then(response => response.json())
            .then(data => {
                if (data.sucesso) {
                    atualizarSelectInsumos(data.insumos);
                }
            })
            .catch(error => {
                console.error('Erro ao carregar insumos:', error);
            });
        }
        
        function carregarFuncionarios() {
            fetch('/extras/buscar-funcionarios')
            .then(response => response.json())
            .then(data => {
                if (data.sucesso) {
                    atualizarSelectFuncionarios(data.funcionarios);
                }
            })
            .catch(error => {
                console.error('Erro ao carregar funcionários:', error);
            });
        }
        
        function atualizarSelectInsumos(insumos) {
            const selects = document.querySelectorAll('.insumo-select');
            selects.forEach(select => {
                const valorAtual = select.value;
                select.innerHTML = '<option value="">Selecione um insumo</option>';
                
                insumos.forEach(insumo => {
                    const option = document.createElement('option');
                    option.value = insumo.id;
                    option.textContent = `${insumo.insumo} - ${insumo.medida}`;
                    select.appendChild(option);
                });
                
                select.value = valorAtual;
            });
        }
        
        function atualizarSelectFuncionarios(funcionarios) {
            const selects = document.querySelectorAll('.funcionario-select');
            selects.forEach(select => {
                const valorAtual = select.value;
                select.innerHTML = '<option value="">Selecione um funcionário</option>';
                
                funcionarios.forEach(funcionario => {
                    const option = document.createElement('option');
                    option.value = funcionario.id;
                    option.textContent = funcionario.profissao;
                    select.appendChild(option);
                });
                
                select.value = valorAtual;
            });
        }
        
        function calcularCustoInsumo(selectElement) {
            const insumoId = selectElement.value;
            const insumoItem = selectElement.closest('.insumo-item');
            const quantidade = parseFloat(insumoItem.querySelector('.insumo-quantidade').value) || 0;
            const custoUnitario = parseFloat(insumoItem.querySelector('.insumo-custo-unitario').value) || 0;
            
            if (insumoId && quantidade > 0 && custoUnitario > 0) {
                // Calcular custo total localmente
                const custoTotal = quantidade * custoUnitario;
                const custoTotalElement = insumoItem.querySelector('.insumo-custo-total');
                custoTotalElement.value = formatarMoeda(custoTotal);
                calcularCustos();
            }
        }
        
        function calcularValorHoraFuncionario(selectElement) {
            const funcionarioId = selectElement.value;
            
            if (funcionarioId) {
                const formData = new FormData();
                formData.append('funcionario_id', funcionarioId);
                
                fetch('/extras/calcular-valor-hora', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.sucesso) {
                        const valorHoraElement = selectElement.closest('.mao-obra-item').querySelector('.mao-obra-valor-hora');
                        valorHoraElement.value = formatarMoeda(data.valor_hora);
                        calcularCustoMaoObra(selectElement.closest('.mao-obra-item'));
                    }
                })
                .catch(error => {
                    console.error('Erro ao calcular valor hora:', error);
                });
            }
        }
        
        // Função para formatar moeda em inputs
        function formatarMoedaInput(input) {
            let valor = input.value.replace(/\D/g, '');
            valor = (parseFloat(valor) / 100).toFixed(2);
            input.value = 'R$ ' + valor.replace('.', ',');
        }
        
        // Carregar dados ao iniciar a página
        document.addEventListener('DOMContentLoaded', function() {
            carregarInsumos();
            carregarEmbalagens();
            carregarFuncionarios();
        });

        // Função para validar campos obrigatórios
        function validarCamposObrigatorios() {
            let valido = true;
            
            // Validar nome do produto
            const nomeProduto = document.getElementById('nomeProduto');
            if (!nomeProduto.value.trim()) {
                nomeProduto.classList.add('is-invalid');
                valido = false;
            } else {
                nomeProduto.classList.remove('is-invalid');
            }
            
            // Validar insumos
            const insumos = document.querySelectorAll('.insumo-item');
            let temInsumoValido = false;
            
            insumos.forEach(insumo => {
                const select = insumo.querySelector('.insumo-select');
                const quantidade = insumo.querySelector('.insumo-quantidade');
                
                if (select.value && quantidade.value && parseFloat(quantidade.value) > 0) {
                    temInsumoValido = true;
                    quantidade.classList.remove('is-invalid');
                } else if (select.value && (!quantidade.value || parseFloat(quantidade.value) <= 0)) {
                    quantidade.classList.add('is-invalid');
                    valido = false;
                }
            });
            
            if (!temInsumoValido) {
                alert('Por favor, adicione pelo menos um insumo com quantidade válida!');
                valido = false;
            }
            
            return valido;
        }



        // Função para validar todos os campos obrigatórios da calculadora
        function validarTodosCamposObrigatorios() {
            const camposFaltantes = [];
            let valido = true;
            
            // 1. Validar nome do produto
            const nomeProduto = document.getElementById('nomeProduto').value.trim();
            if (!nomeProduto) {
                camposFaltantes.push({
                    aba: 'Geral',
                    campo: 'Nome do Produto',
                    descricao: 'Nome do produto é obrigatório'
                });
                valido = false;
            }
            
            // 2. Validar insumos
            const insumos = document.querySelectorAll('.insumo-item');
            let temInsumoValido = false;
            
            insumos.forEach((insumo, index) => {
                const select = insumo.querySelector('.insumo-select');
                const quantidade = insumo.querySelector('.insumo-quantidade');
                
                if (select.value && quantidade.value && parseFloat(quantidade.value) > 0) {
                    temInsumoValido = true;
                } else if (select.value && (!quantidade.value || parseFloat(quantidade.value) <= 0)) {
                    camposFaltantes.push({
                        aba: 'Insumos',
                        campo: `Insumo ${index + 1}`,
                        descricao: 'Quantidade deve ser maior que zero'
                    });
                    valido = false;
                }
            });
            
            if (!temInsumoValido) {
                camposFaltantes.push({
                    aba: 'Insumos',
                    campo: 'Insumos',
                    descricao: 'Adicione pelo menos um insumo com quantidade válida'
                });
                valido = false;
            }
            
            // 2.5. Validar embalagens (opcional)
            const embalagens = document.querySelectorAll('.embalagem-item');
            embalagens.forEach((embalagem, index) => {
                const select = embalagem.querySelector('.embalagem-select');
                const quantidade = embalagem.querySelector('.embalagem-quantidade');
                const volume = embalagem.querySelector('.embalagem-volume');
                const custo = embalagem.querySelector('.embalagem-custo');
                
                if (select.value && (!quantidade.value || parseFloat(quantidade.value) <= 0)) {
                    camposFaltantes.push({
                        aba: 'Embalagem',
                        campo: `Embalagem ${index + 1} - Quantidade`,
                        descricao: 'Quantidade deve ser maior que zero'
                    });
                    valido = false;
                }
                
                if (select.value && (!volume.value || parseFloat(volume.value) <= 0)) {
                    camposFaltantes.push({
                        aba: 'Embalagem',
                        campo: `Embalagem ${index + 1} - Volume`,
                        descricao: 'Volume deve ser maior que zero'
                    });
                    valido = false;
                }
                
                if (select.value && (!custo.value || parseFloat(custo.value) <= 0)) {
                    camposFaltantes.push({
                        aba: 'Embalagem',
                        campo: `Embalagem ${index + 1} - Custo`,
                        descricao: 'Custo deve ser maior que zero'
                    });
                    valido = false;
                }
            });
            
            // 3. Validar mão de obra
            const maoObra = document.querySelectorAll('.mao-obra-item');
            let temMaoObraValida = false;
            
            maoObra.forEach((item, index) => {
                const select = item.querySelector('.funcionario-select');
                const salario = item.querySelector('.mao-obra-salario');
                const tempo = item.querySelector('.mao-obra-tempo');
                const valorFixo = item.querySelector('.mao-obra-valor-fixo');
                
                const salarioValor = parseFloat(salario.value) || 0;
                const tempoValor = parseFloat(tempo.value) || 0;
                const valorFixoValor = parseFloat(valorFixo.value) || 0;
                
                if (select.value && salarioValor > 0 && (tempoValor > 0 || valorFixoValor > 0)) {
                    temMaoObraValida = true;
                } else if (select.value && (salarioValor <= 0 || (tempoValor <= 0 && valorFixoValor <= 0))) {
                    if (salarioValor <= 0) {
                        camposFaltantes.push({
                            aba: 'Mão de Obra',
                            campo: `Colaborador ${index + 1} - Salário`,
                            descricao: 'Salário deve ser maior que zero'
                        });
                    }
                    if (tempoValor <= 0 && valorFixoValor <= 0) {
                        camposFaltantes.push({
                            aba: 'Mão de Obra',
                            campo: `Colaborador ${index + 1} - Tempo/Valor Fixo`,
                            descricao: 'Tempo ou valor fixo deve ser maior que zero'
                        });
                    }
                    valido = false;
                }
            });
            
            if (!temMaoObraValida) {
                camposFaltantes.push({
                    aba: 'Mão de Obra',
                    campo: 'Mão de Obra',
                    descricao: 'Adicione pelo menos um colaborador com salário e tempo ou valor fixo válidos'
                });
                valido = false;
            }
            
            // 4. Validar rendimento
            const vendaUnidade = document.getElementById('vendaUnidade').checked;
            const rendimentoUnidade = parseFloat(document.getElementById('rendimentoPorUnidade').value) || 0;
            const rendimentoKg = parseFloat(document.getElementById('rendimentoPorKg').value) || 0;
            
            if (vendaUnidade && rendimentoUnidade <= 0) {
                camposFaltantes.push({
                    aba: 'Resumo',
                    campo: 'Rendimento por Unidade',
                    descricao: 'Rendimento por unidade deve ser maior que zero'
                });
                valido = false;
            } else if (!vendaUnidade && rendimentoKg <= 0) {
                camposFaltantes.push({
                    aba: 'Resumo',
                    campo: 'Rendimento por Kg',
                    descricao: 'Rendimento por kg deve ser maior que zero'
                });
                valido = false;
            }
            
            return {
                valido: valido,
                camposFaltantes: camposFaltantes
            };
        }

        // Função para mostrar modal de aviso com campos faltantes
        function mostrarModalAviso(camposFaltantes) {
            // Criar conteúdo do modal
            let conteudo = `
                <div class="modal-header">
                    <h5 class="modal-title text-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Campos Obrigatórios Não Preenchidos
                    </h5>
                    <button type="button" class="btn-close" onclick="fecharModalAviso()"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Atenção:</strong> Os seguintes campos obrigatórios precisam ser preenchidos antes de calcular o preço:
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>Aba</th>
                                    <th>Campo</th>
                                    <th>Descrição</th>
                                </tr>
                            </thead>
                            <tbody>
            `;
            
            // Agrupar campos por aba
            const camposPorAba = {};
            camposFaltantes.forEach(campo => {
                if (!camposPorAba[campo.aba]) {
                    camposPorAba[campo.aba] = [];
                }
                camposPorAba[campo.aba].push(campo);
            });
            
            // Adicionar campos ao conteúdo
            Object.keys(camposPorAba).forEach(aba => {
                const campos = camposPorAba[aba];
                campos.forEach((campo, index) => {
                    const isFirst = index === 0;
                    conteudo += `
                        <tr>
                            <td>${isFirst ? `<strong>${campo.aba}</strong>` : ''}</td>
                            <td><code>${campo.campo}</code></td>
                            <td>${campo.descricao}</td>
                        </tr>
                    `;
                });
            });
            
            conteudo += `
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="alert alert-info mt-3">
                        <i class="fas fa-lightbulb me-2"></i>
                        <strong>Dica:</strong> Navegue pelas abas e preencha todos os campos obrigatórios marcados com <code>*</code>.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="fecharModalAviso()">
                        <i class="fas fa-times me-2"></i>Fechar
                    </button>
                    <button type="button" class="btn btn-primary" onclick="irParaPrimeiraAbaComErro()">
                        <i class="fas fa-arrow-right me-2"></i>Ir para Primeira Aba
                    </button>
                </div>
            `;
            
            // Criar ou atualizar modal
            let modal = document.getElementById('modalAviso');
            if (!modal) {
                modal = document.createElement('div');
                modal.id = 'modalAviso';
                modal.className = 'modal fade';
                modal.setAttribute('tabindex', '-1');
                modal.setAttribute('aria-labelledby', 'modalAvisoLabel');
                modal.setAttribute('aria-hidden', 'true');
                document.body.appendChild(modal);
            }
            
            modal.innerHTML = `
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        ${conteudo}
                    </div>
                </div>
            `;
            
            // Mostrar modal
            const bootstrapModal = new bootstrap.Modal(modal);
            bootstrapModal.show();
            
            // Armazenar campos faltantes para navegação
            window.camposFaltantes = camposFaltantes;
        }
        
        // Função para fechar modal de aviso
        function fecharModalAviso() {
            const modal = document.getElementById('modalAviso');
            if (modal) {
                const bootstrapModal = bootstrap.Modal.getInstance(modal);
                if (bootstrapModal) {
                    bootstrapModal.hide();
                }
            }
        }
        
        // Função para ir para a primeira aba com erro
        function irParaPrimeiraAbaComErro() {
            if (window.camposFaltantes && window.camposFaltantes.length > 0) {
                const primeiroCampo = window.camposFaltantes[0];
                
                // Mapear nome da aba para ID
                const mapaAbas = {
                    'Geral': 'insumos', // Geral fica na aba insumos
                    'Insumos': 'insumos',
                    'Mão de Obra': 'mao-obra',
                    'Despesas': 'despesas',
                    'Resumo': 'resumo'
                };
                
                const abaId = mapaAbas[primeiroCampo.aba];
                if (abaId) {
                    alternarAba(abaId);
                    fecharModalAviso();
                    
                    // Focar no campo específico se possível
                    setTimeout(() => {
                        if (primeiroCampo.campo === 'Nome do Produto') {
                            document.getElementById('nomeProduto').focus();
                        }
                    }, 100);
                }
            }
        }

        // Função para selecionar funcionário e preencher salário
        function selecionarFuncionario(selectElement) {
            const funcionarioId = selectElement.value;
            const maoObraItem = selectElement.closest('.mao-obra-item');
            const salarioInput = maoObraItem.querySelector('.mao-obra-salario');
            const tempoInput = maoObraItem.querySelector('.mao-obra-tempo');
            const valorFixoInput = maoObraItem.querySelector('.mao-obra-valor-fixo');
            
            console.log('Selecionando funcionário:', funcionarioId);
            
            if (funcionarioId) {
                // Buscar dados do funcionário selecionado
                fetch('/extras/buscar-funcionario-por-id', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `funcionario_id=${funcionarioId}`
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('Dados recebidos:', data);
                    if (data.sucesso && data.funcionario) {
                        console.log('Funcionário encontrado:', data.funcionario);
                        // Preencher salário com o valor do banco
                        salarioInput.value = data.funcionario.salario;
                        console.log('Salário definido:', data.funcionario.salario);
                        
                        // Sempre calcular custo quando salário for preenchido
                        calcularCustoMaoObra(maoObraItem);
                        
                        // Validar se tempo está preenchido
                        const tempoValor = parseFloat(tempoInput.value) || 0;
                        if (tempoValor <= 0) {
                            tempoInput.focus();
                            tempoInput.classList.add('is-invalid');
                        } else {
                            tempoInput.classList.remove('is-invalid');
                        }
                    } else {
                        console.error('Erro na resposta:', data);
                    }
                })
                .catch(error => {
                    console.error('Erro ao buscar funcionário:', error);
                    // Em caso de erro, manter o campo editável
                    salarioInput.value = '0';
                });
            } else {
                // Limpar campos se nenhum funcionário selecionado
                salarioInput.value = '0';
                maoObraItem.querySelector('.mao-obra-valor-hora').value = 'R$ 0,00';
                maoObraItem.querySelector('.mao-obra-custo-total').value = 'R$ 0,00';
                tempoInput.classList.remove('is-invalid');
                valorFixoInput.classList.remove('is-invalid');
            }
        }

        // Função para adicionar embalagem
        function adicionarEmbalagem() {
            const container = document.getElementById('embalagemContainer');
            const embalagemId = `embalagem_${contadorEmbalagem}`;
            
            const embalagemHtml = `
                <div class="row mb-3 embalagem-item" id="${embalagemId}">
                    <div class="col-md-2">
                        <div class="form-floating">
                            <select class="form-control embalagem-select" onchange="selecionarEmbalagem(this)">
                                <option value="">Selecione uma embalagem</option>
                            </select>
                            <label>Embalagem</label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-floating">
                            <input type="number" class="form-control embalagem-quantidade" placeholder="Quantidade" min="1" step="1" value="1" onchange="calcularCustoEmbalagem(this)">
                            <label>Quantidade</label>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle me-1"></i>Rendimento por unidade
                            </small>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-floating">
                            <input type="number" class="form-control embalagem-volume" placeholder="Volume" min="1" step="1" value="1" onchange="calcularCustoEmbalagem(this)">
                            <label>Volume</label>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle me-1"></i>Sugerido do banco - editável
                            </small>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-floating">
                            <input type="number" class="form-control embalagem-custo" placeholder="Custo" min="0" step="0.01" value="0" onchange="calcularCustoEmbalagem(this)">
                            <label>Custo (R$)</label>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle me-1"></i>Custo total da embalagem
                            </small>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-floating">
                            <input type="text" class="form-control embalagem-custo-unitario" placeholder="Custo Unitário" readonly>
                            <label>Custo Unitário (R$)</label>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle me-1"></i>Custo ÷ Volume
                            </small>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-floating">
                            <input type="text" class="form-control embalagem-custo-total" placeholder="Custo Total" readonly>
                            <label>Custo Total (R$)</label>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle me-1"></i>Custo Unitário × Quantidade
                            </small>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-danger" onclick="removerItem('${embalagemId}')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `;
            
            container.insertAdjacentHTML('beforeend', embalagemHtml);
            
            // Carregar embalagens no select recém-criado
            carregarEmbalagens();
            
            contadorEmbalagem++;
        }
        
        // Função para selecionar embalagem e preencher custo unitário
        function selecionarEmbalagem(selectElement) {
            const embalagemId = selectElement.value;
            const embalagemItem = selectElement.closest('.embalagem-item');
            const volumeInput = embalagemItem.querySelector('.embalagem-volume');
            const custoInput = embalagemItem.querySelector('.embalagem-custo');
            const quantidadeInput = embalagemItem.querySelector('.embalagem-quantidade');
            
            console.log('Selecionando embalagem:', embalagemId);
            
            if (embalagemId) {
                // Buscar dados da embalagem selecionada
                fetch('/extras/buscar-embalagem-por-id', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `embalagem_id=${embalagemId}`
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('Dados recebidos:', data);
                    if (data.sucesso && data.embalagem) {
                        console.log('Embalagem encontrada:', data.embalagem);
                        
                        // Preencher volume e custo com os valores do banco
                        volumeInput.value = data.embalagem.volume;
                        custoInput.value = data.embalagem.custo;
                        
                        console.log('Volume definido:', data.embalagem.volume);
                        console.log('Custo definido:', data.embalagem.custo);
                        
                        // Calcular custo unitário e total
                        calcularCustoEmbalagem(volumeInput);
                    } else {
                        console.error('Erro na resposta:', data);
                    }
                })
                .catch(error => {
                    console.error('Erro ao buscar embalagem:', error);
                    // Em caso de erro, manter os campos editáveis
                    volumeInput.value = '1';
                    custoInput.value = '0';
                });
            } else {
                // Limpar campos se nenhuma embalagem selecionada
                volumeInput.value = '1';
                custoInput.value = '0';
                embalagemItem.querySelector('.embalagem-custo-unitario').value = 'R$ 0,00';
                embalagemItem.querySelector('.embalagem-custo-total').value = 'R$ 0,00';
                quantidadeInput.classList.remove('is-invalid');
            }
        }
        
        // Função para calcular custo total da embalagem
        function calcularCustoEmbalagem(inputElement) {
            const embalagemItem = inputElement.closest('.embalagem-item');
            const quantidade = parseFloat(embalagemItem.querySelector('.embalagem-quantidade').value) || 0;
            const volume = parseFloat(embalagemItem.querySelector('.embalagem-volume').value) || 0;
            const custo = parseFloat(embalagemItem.querySelector('.embalagem-custo').value) || 0;
            
            // Calcular custo unitário: Custo ÷ Volume
            const custoUnitario = volume > 0 ? custo / volume : 0;
            
            // Calcular custo total: Custo Unitário × Quantidade
            const custoTotal = custoUnitario * quantidade;
            
            // Atualizar campos
            embalagemItem.querySelector('.embalagem-custo-unitario').value = formatarMoeda(custoUnitario);
            embalagemItem.querySelector('.embalagem-custo-total').value = formatarMoeda(custoTotal);
            
            // Validar quantidade
            const quantidadeInput = embalagemItem.querySelector('.embalagem-quantidade');
            if (quantidade <= 0) {
                quantidadeInput.classList.add('is-invalid');
                embalagemItem.querySelector('.embalagem-custo-total').value = 'R$ 0,00';
            } else {
                quantidadeInput.classList.remove('is-invalid');
            }
            
            // Validar volume
            const volumeInput = embalagemItem.querySelector('.embalagem-volume');
            if (volume <= 0) {
                volumeInput.classList.add('is-invalid');
                embalagemItem.querySelector('.embalagem-custo-unitario').value = 'R$ 0,00';
                embalagemItem.querySelector('.embalagem-custo-total').value = 'R$ 0,00';
            } else {
                volumeInput.classList.remove('is-invalid');
            }
            
            // Validar custo
            const custoInput = embalagemItem.querySelector('.embalagem-custo');
            if (custo <= 0) {
                custoInput.classList.add('is-invalid');
                embalagemItem.querySelector('.embalagem-custo-unitario').value = 'R$ 0,00';
                embalagemItem.querySelector('.embalagem-custo-total').value = 'R$ 0,00';
            } else {
                custoInput.classList.remove('is-invalid');
            }
            
            calcularCustos();
        }
        
        // Função para carregar embalagens
        function carregarEmbalagens() {
            fetch('/extras/buscar-embalagens')
            .then(response => response.json())
            .then(data => {
                if (data.sucesso) {
                    atualizarSelectEmbalagens(data.embalagens);
                }
            })
            .catch(error => {
                console.error('Erro ao carregar embalagens:', error);
            });
        }
        
        // Função para atualizar select de embalagens
        function atualizarSelectEmbalagens(embalagens) {
            const selects = document.querySelectorAll('.embalagem-select');
            selects.forEach(select => {
                const valorAtual = select.value;
                select.innerHTML = '<option value="">Selecione uma embalagem</option>';
                
                embalagens.forEach(embalagem => {
                    const option = document.createElement('option');
                    option.value = embalagem.id;
                    option.textContent = embalagem.embalagem;
                    select.appendChild(option);
                });
                
                select.value = valorAtual;
            });
        }

        // Funções para gerenciar embalagens
        function abrirModalEmbalagem() {
            document.getElementById('modalEmbalagem').style.display = 'block';
        }
        
        function fecharModalEmbalagem() {
            document.getElementById('modalEmbalagem').style.display = 'none';
            document.getElementById('formEmbalagem').reset();
        }
        
        function cadastrarEmbalagem() {
            const formData = new FormData(document.getElementById('formEmbalagem'));
            
            fetch('/extras/cadastrar-embalagem', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.sucesso) {
                    alert(data.mensagem);
                    fecharModalEmbalagem();
                    carregarEmbalagens();
                } else {
                    alert('Erro: ' + data.erro);
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao cadastrar embalagem');
            });
        }


        
        // Função para alternar tipo de venda

        
        // Função para alternar tipo de venda
    </script>
    
    <!-- Modal para Cadastrar Insumo -->
    <div id="modalInsumo" class="modal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.4);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cadastrar Novo Insumo</h5>
                    <button type="button" class="btn-close" onclick="fecharModalInsumo()"></button>
                </div>
                <div class="modal-body">
                    <form id="formInsumo">
                        <div class="mb-3">
                            <label for="insumo" class="form-label">Nome do Insumo</label>
                            <input type="text" class="form-control" id="insumo" name="insumo" required>
                        </div>
                        <div class="mb-3">
                            <label for="medida" class="form-label">Medida</label>
                            <select class="form-control" id="medida" name="medida" required>
                                <option value="">Selecione a medida</option>
                                <option value="Kg">Kg</option>
                                <option value="Un">Un</option>
                                <option value="L">L</option>
                                <option value="Ml">Ml</option>
                                <option value="Fd">Fd</option>
                                <option value="Cx">Cx</option>
                                <option value="Pct">Pct</option>
                                <option value="Lata">Lata</option>
                                <option value="Garrafa">Garrafa</option>
                                <option value="Sachê">Sachê</option>
                                <option value="Colher">Colher</option>
                                <option value="Xícara">Xícara</option>
                                <option value="Copo">Copo</option>
                                <option value="Outro">Outro</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="custo" class="form-label">Custo por Medida</label>
                            <input type="text" class="form-control" id="custo" name="custo" placeholder="R$ 0,00" required oninput="formatarMoedaInput(this)">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="fecharModalInsumo()">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="cadastrarInsumo()">Cadastrar</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal para Cadastrar Funcionário -->
    <div id="modalFuncionario" class="modal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.4);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cadastrar Novo Funcionário</h5>
                    <button type="button" class="btn-close" onclick="fecharModalFuncionario()"></button>
                </div>
                <div class="modal-body">
                    <form id="formFuncionario">
                        <div class="mb-3">
                            <label for="profissao" class="form-label">Profissão</label>
                            <input type="text" class="form-control" id="profissao" name="profissao" placeholder="Ex: Padeiro, Confeiteiro" required>
                        </div>
                        <div class="mb-3">
                            <label for="salario" class="form-label">Salário Mensal</label>
                            <input type="text" class="form-control" id="salario" name="salario" placeholder="R$ 0,00" required oninput="formatarMoedaInput(this)">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="fecharModalFuncionario()">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="cadastrarFuncionario()">Cadastrar</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal para Cadastrar Embalagem -->
    <div id="modalEmbalagem" class="modal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.4);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cadastrar Nova Embalagem</h5>
                    <button type="button" class="btn-close" onclick="fecharModalEmbalagem()"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info mb-3">
                        <h6><i class="fas fa-lightbulb me-2"></i>Exemplos de Cadastro:</h6>
                        <ul class="mb-0">
                            <li><strong>Rolo de Etiquetas:</strong> Custo R$ 6,99 | Volume 500 etiquetas | Quantidade 1</li>
                            <li><strong>Fardo de Bandeja B1:</strong> Custo R$ 20,00 | Volume 100 bandejas | Quantidade 1</li>
                            <li><strong>Caixa de Sacos Plásticos:</strong> Custo R$ 15,50 | Volume 1000 sacos | Quantidade 1</li>
                        </ul>
                    </div>
                    <form id="formEmbalagem">
                        <div class="mb-3">
                            <label for="embalagem" class="form-label">Nome da Embalagem</label>
                            <input type="text" class="form-control" id="embalagem" name="embalagem" placeholder="Ex: Rolo de Etiquetas, Fardo de Bandeja B1" required>
                        </div>
                        <div class="mb-3">
                            <label for="volume" class="form-label">Volume (Quantidade de Itens)</label>
                            <input type="number" class="form-control" id="volume" name="volume" placeholder="Ex: 500 (etiquetas por rolo)" min="1" value="1" required>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle me-1"></i>Quantidade de itens que a embalagem contém
                            </small>
                        </div>
                        <div class="mb-3">
                            <label for="quantidade" class="form-label">Quantidade Padrão</label>
                            <input type="number" class="form-control" id="quantidade" name="quantidade" placeholder="Ex: 1 (rolo por vez)" min="1" value="1" required>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle me-1"></i>Quantidade de embalagens utilizadas por padrão
                            </small>
                        </div>
                        <div class="mb-3">
                            <label for="custo" class="form-label">Custo da Embalagem</label>
                            <input type="text" class="form-control" id="custo" name="custo" placeholder="Ex: R$ 6,99 (custo do rolo completo)" required oninput="formatarMoedaInput(this)">
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle me-1"></i>Custo total da embalagem (não por item)
                            </small>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="fecharModalEmbalagem()">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="cadastrarEmbalagem()">Cadastrar</button>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 