<style>
        .construtor-placas-body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px 0;
        }
        
        .container-fluid {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .card {
            border: 3px solid #e0e0e0;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
        }
        
        .card-header-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-bottom: 3px solid #5a6fd8;
            border-radius: 12px 12px 0 0;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }
        
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
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-color: #667eea;
        }
        
        .btn-success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border-color: #28a745;
        }
        
        .btn-warning {
            background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
            border-color: #ffc107;
        }
        
        .input-group {
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            border-radius: 8px;
        }
        
        .input-group-text {
            border: 2px solid #dee2e6;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            font-weight: 600;
        }
        
        /* Estilos para a prévia da placa */
        .placa-preview {
            background: white;
            border: 3px solid #dee2e6;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
            overflow: hidden;
            margin: 20px 0;
        }
        
        .placa-container {
            position: relative;
            overflow: hidden;
            margin: 0 auto;
        }
        
        .placa-content {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 20px;
            box-sizing: border-box;
        }
        
        .produto-nome {
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 10px;
            word-wrap: break-word;
        }
        
        .produto-preco {
            font-size: 32px;
            font-weight: bold;
            color: #e74c3c;
            text-align: center;
        }
        

        
        /* Estilos para preço dividido */
        .preco-dividido {
            display: flex;
            align-items: flex-start;
            justify-content: center;
            gap: 2px;
            width: 100%;
            max-width: 100%;
            padding: 0 10px;
            box-sizing: border-box;
        }
        
        .parte-inteira {
            line-height: 1;
        }
        
        .parte-decimal {
            line-height: 1;
            align-self: flex-start;
            margin-top: 0;
        }
        
        .volume-produto {
            line-height: 1;
            align-self: flex-end;
            margin-top: 0;
            margin-left: 2px;
            font-size: 0.6em;
            opacity: 0.8;
        }
        
        .cifrao {
            line-height: 1;
            align-self: flex-start;
            margin-top: 0;
            margin-right: 2px;
            font-size: 0.6em;
            opacity: 0.8;
        }
        

        
        /* Controles de tamanho */
        .size-controls {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border: 2px solid #dee2e6;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .size-option {
            cursor: pointer;
            padding: 10px;
            border: 2px solid #dee2e6;
            border-radius: 8px;
            margin: 5px;
            transition: all 0.3s ease;
            background: white;
        }
        
        .size-option:hover {
            border-color: #667eea;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.25);
        }
        
        .size-option.active {
            border-color: #667eea;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        /* Loading */
        .loading {
            display: none;
        }
        
        .spinner-border {
            width: 3rem;
            height: 3rem;
        }
    </style>
<div class="container-fluid construtor-placas-body">
    <div class="row">
        <!-- Cabeçalho da Página -->
        <div class="col-12 mb-4">
            <div class="d-flex align-items-center">
                <i class="fas fa-tags text-success me-3" style="font-size: 2rem;"></i>
                <div>
                    <h1 class="h3 mb-0">Construtor de Placas de Preço</h1>
                    <p class="text-muted mb-0">Crie placas personalizadas para impressão e redes sociais</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Painel Esquerdo - Controles -->
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header card-header-custom">
                    <h5 class="mb-0">
                        <i class="fas fa-cog me-2"></i>
                        Configurações da Placa
                    </h5>
                </div>
                <div class="card-body">
                    <form id="formPlaca">
                        <!-- Busca por EAN -->
                        <div class="mb-4">
                            <label class="form-label">Buscar Produto por EAN</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="ean_busca" name="ean_busca" placeholder="Digite o EAN do produto">
                                <button type="button" class="btn btn-primary" id="btnBuscarEan">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Dados do Produto -->
                        <div class="mb-4">
                            <label class="form-label">Nome do Produto <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nome_produto" name="nome_produto" placeholder="Digite o nome do produto" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Preço <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="text" class="form-control" id="preco" name="preco" placeholder="0,00" required>
                                <select class="form-select" id="volume_produto" name="volume_produto" style="max-width: 80px;">
                                    <option value="Un" selected>Un</option>
                                    <option value="Kg">Kg</option>
                                    <option value="Dz">Dz</option>
                                    <option value="Cx">Cx</option>
                                    <option value="Fd">Fd</option>
                                    <option value="L">L</option>
                                </select>
                            </div>
                        </div>

                        <!-- Tamanho da Placa -->
                        <div class="mb-4">
                            <label class="form-label">Tamanho da Placa</label>
                            <div class="size-controls">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="size-option active" data-size="a4">
                                            <strong>A4</strong><br>
                                            <small>210×297mm</small>
                                        </div>
                                    </div>
                                                                <div class="col-6">
                                <div class="size-option disabled" data-size="a5" style="opacity: 0.5; cursor: not-allowed;">
                                    <strong>A5</strong><br>
                                    <small>148×210mm</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="size-option disabled" data-size="a6" style="opacity: 0.5; cursor: not-allowed;">
                                    <strong>A6</strong><br>
                                    <small>105×148mm</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="size-option disabled" data-size="100x35" style="opacity: 0.5; cursor: not-allowed;">
                                    <strong>100×35mm</strong><br>
                                    <small>Etiqueta</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="size-option disabled" data-size="80x40" style="opacity: 0.5; cursor: not-allowed;">
                                    <strong>80×40mm</strong><br>
                                    <small>Etiqueta</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="size-option disabled" data-size="50x40" style="opacity: 0.5; cursor: not-allowed;">
                                    <strong>50×40mm</strong><br>
                                    <small>Etiqueta</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="size-option disabled" data-size="100x50" style="opacity: 0.5; cursor: not-allowed;">
                                    <strong>100×50mm</strong><br>
                                    <small>Etiqueta</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="size-option disabled" data-size="110x30" style="opacity: 0.5; cursor: not-allowed;">
                                    <strong>110×30mm</strong><br>
                                    <small>Etiqueta</small>
                                </div>
                            </div>
                                </div>
                            </div>
                        </div>

                        <!-- Cabeçalho da Placa (apenas para A4, A5, A6) -->
                        <div class="mb-4" id="cabecalhoSection" style="display: none;">
                            <label class="form-label">Cabeçalho da Placa</label>
                            <select class="form-select" id="cabecalho" name="cabecalho">
                                <option value="OFERTA">OFERTA</option>
                                <option value="IMPERDÍVEL">IMPERDÍVEL</option>
                                <option value="SÓ HOJE">SÓ HOJE</option>
                                <option value="APROVEITE">APROVEITE</option>
                                <option value="ECONOMIZE">ECONOMIZE</option>
                                <option value="PROMO">PROMO</option>
                                <option value="PROMOÇÃO">PROMOÇÃO</option>
                                <option value="DESCONTO">DESCONTO</option>
                            </select>
                        </div>

                        <!-- Cores do Cabeçalho (apenas para A4, A5, A6) -->
                        <div class="row mb-4" id="coresCabecalhoSection" style="display: none;">
                            <div class="col-6">
                                <label class="form-label">Faixa do Cabeçalho</label>
                                <input type="color" class="form-control form-control-color" id="cor_faixa_cabecalho" name="cor_faixa_cabecalho" value="#ff0000">
                            </div>
                            <div class="col-6">
                                <label class="form-label">Texto do Cabeçalho</label>
                                <input type="color" class="form-control form-control-color" id="cor_texto_cabecalho" name="cor_texto_cabecalho" value="#ffff00">
                            </div>
                        </div>

                        <!-- Controles da Faixa do Cabeçalho (apenas para A4, A5, A6) -->
                        <div class="row mb-4" id="controlesFaixaSection" style="display: none;">
                            <div class="col-6">
                                <label class="form-label">Altura da Faixa (px)</label>
                                <input type="number" class="form-control" id="altura_faixa" name="altura_faixa" value="200" min="10" max="300">
                            </div>
                            <div class="col-6">
                                <label class="form-label">Largura da Faixa (%)</label>
                                <input type="number" class="form-control" id="largura_faixa" name="largura_faixa" value="100" min="50" max="100">
                            </div>
                        </div>
                        
                        <div class="row mb-4" id="controlesFaixaSection2" style="display: none;">
                            <div class="col-6">
                                <label class="form-label">Posição do Topo (px)</label>
                                <input type="number" class="form-control" id="posicao_top_faixa" name="posicao_top_faixa" value="0" min="0" max="100">
                            </div>
                            <div class="col-6">
                                <div class="form-check" style="margin-top: 32px;">
                                    <input class="form-check-input" type="checkbox" id="borda_placa" name="borda_placa" value="borda_vermelha">
                                    <label class="form-check-label" for="borda_placa">
                                        <i class="fas fa-border-style me-2"></i>Borda
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Cores -->
                        <div class="row mb-4">
                            <div class="col-6">
                                <label class="form-label">Cor de Fundo</label>
                                <input type="color" class="form-control form-control-color" id="cor_fundo" name="cor_fundo" value="#ffff00">
                            </div>
                            <div class="col-6">
                                <label class="form-label">Cor do Texto</label>
                                <input type="color" class="form-control form-control-color" id="cor_texto" name="cor_texto" value="#000000">
                            </div>
                        </div>



                        <!-- Configurações Globais -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fas fa-cog me-2"></i>Configurações Globais</h6>
                            </div>
                            <div class="card-body">
                                <!-- Cabeçalho -->
                                <div class="mb-4">
                                    <h6 class="text-primary">Cabeçalho da Placa</h6>
                                    <div class="row">
                                        <div class="col-6">
                                            <label class="form-label">Fonte</label>
                                            <select class="form-select" id="fonte_cabecalho" name="fonte_cabecalho">
                                                <option value="Arial">Arial</option>
                                                <option value="Helvetica">Helvetica</option>
                                                <option value="Times New Roman">Times New Roman</option>
                                                <option value="Georgia">Georgia</option>
                                                <option value="Verdana">Verdana</option>
                                                <option value="Inter">Inter</option>
                                                <option value="Impact">Impact</option>
                                                <option value="Comic Sans MS">Comic Sans MS</option>
                                            </select>
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label">Estilo</label>
                                            <select class="form-select" id="estilo_cabecalho" name="estilo_cabecalho">
                                                <option value="normal">Normal</option>
                                                <option value="bold" selected>Negrito</option>
                                                <option value="italic">Itálico</option>
                                                <option value="bold italic">Negrito + Itálico</option>
                                            </select>
                                        </div>
                                    </div>
                                  
                                </div>

                                <!-- Nome do Produto -->
                                <div class="mb-4">
                                    <h6 class="text-success">Nome do Produto</h6>
                                    <div class="row">
                                        <div class="col-4">
                                            <label class="form-label">Fonte</label>
                                            <select class="form-select" id="fonte_produto" name="fonte_produto">
                                                <option value="Arial">Arial</option>
                                                <option value="Helvetica">Helvetica</option>
                                                <option value="Times New Roman">Times New Roman</option>
                                                <option value="Georgia">Georgia</option>
                                                <option value="Verdana">Verdana</option>
                                                <option value="Inter">Inter</option>
                                                <option value="Impact" selected>Impact</option>
                                                <option value="Comic Sans MS">Comic Sans MS</option>
                                            </select>
                                        </div>
                                        <div class="col-4">
                                            <label class="form-label">Tamanho</label>
                                            <select class="form-select" id="tamanho_fonte_produto" name="tamanho_fonte_produto">
                                                <option value="12px">12px</option>
                                                <option value="14px">14px</option>
                                                <option value="16px">16px</option>
                                                <option value="18px">18px</option>
                                                <option value="20px">20px</option>
                                                <option value="24px">24px</option>
                                                <option value="28px">28px</option>
                                                <option value="32px">32px</option>
                                                <option value="36px">36px</option>
                                                <option value="40px">40px</option>
                                                <option value="48px">48px</option>
                                                <option value="56px">56px</option>
                                                <option value="64px">64px</option>
                                                <option value="72px">72px</option>
                                                <option value="80px" selected>80px</option>
                                                <option value="96px">96px</option>
                                            
                                            </select>
                                        </div>
                                        <div class="col-4">
                                            <label class="form-label">Estilo</label>
                                            <select class="form-select" id="estilo_produto" name="estilo_produto">
                                                <option value="normal" selected>Normal</option>
                                                <option value="bold">Negrito</option>
                                                <option value="italic">Itálico</option>
                                                <option value="bold italic">Negrito + Itálico</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Preço -->
                                <div class="mb-4">
                                    <h6 class="text-warning">Preço</h6>
                                    <div class="row">
                                        <div class="col-4">
                                            <label class="form-label">Fonte</label>
                                            <select class="form-select" id="fonte_preco" name="fonte_preco">
                                                <option value="Arial">Arial</option>
                                                <option value="Helvetica">Helvetica</option>
                                                <option value="Times New Roman">Times New Roman</option>
                                                <option value="Georgia">Georgia</option>
                                                <option value="Verdana">Verdana</option>
                                                <option value="Inter">Inter</option>
                                                <option value="Impact" selected>Impact</option>
                                                <option value="Comic Sans MS">Comic Sans MS</option>
                                            </select>
                                        </div>
                                        <div class="col-4">
                                            <label class="form-label">Tamanho</label>
                                            <select class="form-select" id="tamanho_fonte_preco" name="tamanho_fonte_preco">
                                                <option value="16px">16px</option>
                                                <option value="18px">18px</option>
                                                <option value="20px">20px</option>
                                                <option value="24px">24px</option>
                                                <option value="28px">28px</option>
                                                <option value="32px">32px</option>
                                                <option value="36px">36px</option>
                                                <option value="40px">40px</option>
                                                <option value="48px">48px</option>
                                                <option value="56px">56px</option>
                                                <option value="64px">64px</option>
                                                <option value="72px">72px</option>
                                                <option value="80px">80px</option>
                                                <option value="96px">96px</option>
                                                <option value="120px">120px</option>
                                                <option value="144px">144px</option>
                                                <option value="168px">168px</option>
                                                <option value="200px" selected>200px</option>
                                            </select>
                                        </div>
                                        <div class="col-4">
                                            <label class="form-label">Estilo</label>
                                            <select class="form-select" id="estilo_preco" name="estilo_preco">
                                                <option value="normal">Normal</option>
                                                <option value="bold" selected>Negrito</option>
                                                <option value="italic">Itálico</option>
                                                <option value="bold italic">Negrito + Itálico</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Rodapé -->
                                <div class="mb-4">
                                    <h6 class="text-info">Rodapé</h6>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" id="rodape_menores" name="rodape_menores">
                                                <label class="form-check-label" for="rodape_menores">
                                                    Venda proibida para menores de 18 anos
                                                </label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" id="rodape_validade" name="rodape_validade">
                                                <label class="form-check-label" for="rodape_validade">
                                                    Produto com validade próxima
                                                </label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" id="rodape_estoque" name="rodape_estoque">
                                                <label class="form-check-label" for="rodape_estoque">
                                                    Enquanto durar o estoque
                                                </label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" id="rodape_promocao" name="rodape_promocao">
                                                <label class="form-check-label" for="rodape_promocao">
                                                    Promoção válida por período
                                                </label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" id="rodape_pagamentos" name="rodape_pagamentos">
                                                <label class="form-check-label" for="rodape_pagamentos">
                                                    Aceitamos todas as formas de pagamentos
                                                </label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" id="rodape_parcelamento" name="rodape_parcelamento">
                                                <label class="form-check-label" for="rodape_parcelamento">
                                                    Parcele suas compras em até
                                                </label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" id="rodape_pagamento_especifico" name="rodape_pagamento_especifico">
                                                <label class="form-check-label" for="rodape_pagamento_especifico">
                                                    Promoção válida somente para pagamento em
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Data de validade (aparece quando checkbox está marcado) -->
                                    <div class="row mt-2" id="data_validade_section" style="display: none;">
                                        <div class="col-6">
                                            <label class="form-label">Data de Validade</label>
                                            <input type="date" class="form-control" id="data_validade" name="data_validade">
                                        </div>
                                    </div>
                                    
                                    <!-- Período da promoção (aparece quando checkbox está marcado) -->
                                    <div class="row mt-2" id="periodo_promocao_section" style="display: none;">
                                        <div class="col-6">
                                            <label class="form-label">Data Início</label>
                                            <input type="date" class="form-control" id="data_inicio_promocao" name="data_inicio_promocao">
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label">Data Fim</label>
                                            <input type="date" class="form-control" id="data_fim_promocao" name="data_fim_promocao">
                                        </div>
                                    </div>
                                    
                                    <!-- Parcelamento (aparece quando checkbox está marcado) -->
                                    <div class="row mt-2" id="parcelamento_section" style="display: none;">
                                        <div class="col-6">
                                            <label class="form-label">Número de Parcelas</label>
                                            <select class="form-select" id="numero_parcelas" name="numero_parcelas">
                                                <option value="2">2x</option>
                                                <option value="3">3x</option>
                                                <option value="4">4x</option>
                                                <option value="5">5x</option>
                                                <option value="6">6x</option>
                                                <option value="8">8x</option>
                                                <option value="10">10x</option>
                                                <option value="12">12x</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <!-- Formas de pagamento específicas (aparece quando checkbox está marcado) -->
                                    <div class="row mt-2" id="pagamento_especifico_section" style="display: none;">
                                        <div class="col-12">
                                            <label class="form-label">Formas de Pagamento</label>
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="pagamento_dinheiro" name="pagamento_dinheiro" value="dinheiro">
                                                        <label class="form-check-label" for="pagamento_dinheiro">Dinheiro</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="pagamento_pix" name="pagamento_pix" value="pix">
                                                        <label class="form-check-label" for="pagamento_pix">PIX</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="pagamento_cartao_debito" name="pagamento_cartao_debito" value="cartao_debito">
                                                        <label class="form-check-label" for="pagamento_cartao_debito">Cartão de Débito</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="pagamento_cartao_credito" name="pagamento_cartao_credito" value="cartao_credito">
                                                        <label class="form-check-label" for="pagamento_cartao_credito">Cartão de Crédito</label>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="pagamento_boleto" name="pagamento_boleto" value="boleto">
                                                        <label class="form-check-label" for="pagamento_boleto">Boleto</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="pagamento_transferencia" name="pagamento_transferencia" value="transferencia">
                                                        <label class="form-check-label" for="pagamento_transferencia">Transferência</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="pagamento_vale" name="pagamento_vale" value="vale">
                                                        <label class="form-check-label" for="pagamento_vale">Vale</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="pagamento_cheque" name="pagamento_cheque" value="cheque">
                                                        <label class="form-check-label" for="pagamento_cheque">Cheque</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botões de Ação -->
                        <div class="d-grid gap-2">
                            <div class="row">
                                <div class="col-4">
                                    <button type="button" class="btn btn-primary btn-lg w-100" id="btnImprimir" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-color: #667eea;">
                                        <i class="fas fa-print me-2"></i>
                                        Imprimir
                                    </button>
                                </div>
                                <div class="col-4">
                                    <button type="button" class="btn btn-primary btn-lg w-100" id="btnSalvarJPG" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-color: #667eea;">
                                        <i class="fas fa-image me-2"></i>
                                        JPG
                                    </button>
                                </div>
                                <div class="col-4">
                                    <button type="button" class="btn btn-primary btn-lg w-100" id="btnSalvarPDF" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-color: #667eea;">
                                        <i class="fas fa-file-pdf me-2"></i>
                                        PDF
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Painel Direito - Prévia da Placa -->
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header card-header-custom">
                    <h5 class="mb-0">
                        <i class="fas fa-eye me-2"></i>
                        Prévia da Placa
                    </h5>
                </div>
                <div class="card-body">
                    <div class="placa-preview">
                        <div class="placa-container" id="placaContainer" style="width: 210mm; height: 297mm;">
                            <!-- As placas serão criadas dinamicamente aqui -->
                        </div>
                    </div>

                    <!-- Loading -->
                    <div class="loading text-center mt-4" id="loading">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Gerando placa...</span>
                        </div>
                        <p class="mt-2">Gerando placa...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- IMask -->
<script src="https://unpkg.com/imask"></script>

<!-- html2canvas para conversão de HTML para imagem -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<!-- jsPDF para geração de PDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM carregado!');
    const form = document.getElementById('formPlaca');
    const btnBuscarEan = document.getElementById('btnBuscarEan');
    const btnImprimir = document.getElementById('btnImprimir');
    const btnSalvarJPG = document.getElementById('btnSalvarJPG');
    const btnSalvarPDF = document.getElementById('btnSalvarPDF');
    const loading = document.getElementById('loading');
    
    console.log('Elementos encontrados:');
    console.log('form:', form);
    console.log('btnBuscarEan:', btnBuscarEan);
    console.log('btnImprimir:', btnImprimir);
    console.log('btnSalvarJPG:', btnSalvarJPG);
    console.log('btnSalvarPDF:', btnSalvarPDF);
    console.log('loading:', loading);
    
    // Máscaras
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
        
        ean: IMask.createMask({
            mask: '0000000000000',
            lazy: false,
            prepare: function (str) {
                return str.replace(/\D/g, '');
            }
        })
    };

    // Aplicar máscaras
    masks.currency.mask(document.getElementById('preco'));
    // masks.ean.mask(document.getElementById('ean_busca')); // Comentado temporariamente
    
    // Evento Enter no campo EAN
    document.getElementById('ean_busca').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            console.log('Enter pressionado no campo EAN');
            btnBuscarEan.click();
        }
    });

    // Controle de tamanhos
    const sizeOptions = document.querySelectorAll('.size-option');
    sizeOptions.forEach(option => {
        option.addEventListener('click', function() {
            // Verificar se a opção está desabilitada
            if (this.classList.contains('disabled')) {
                return; // Não fazer nada se estiver desabilitada
            }
            
            sizeOptions.forEach(opt => opt.classList.remove('active'));
            this.classList.add('active');
            atualizarTamanhoPlaca(this.dataset.size);
            controlarCamposCabecalho(this.dataset.size);
        });
    });

    // Função para controlar campos do cabeçalho
    function controlarCamposCabecalho(tamanho) {
        const cabecalhoSection = document.getElementById('cabecalhoSection');
        const coresCabecalhoSection = document.getElementById('coresCabecalhoSection');
        const controlesFaixaSection = document.getElementById('controlesFaixaSection');
        const controlesFaixaSection2 = document.getElementById('controlesFaixaSection2');
        
        // Mostrar cabeçalho apenas para A4, A5, A6
        if (['a4', 'a5', 'a6'].includes(tamanho)) {
            cabecalhoSection.style.display = 'block';
            coresCabecalhoSection.style.display = 'block';
            controlesFaixaSection.style.display = 'block';
            controlesFaixaSection2.style.display = 'block';
            
            // Definir altura padrão baseada no tamanho
            const alturaFaixaInput = document.getElementById('altura_faixa');
            if (tamanho === 'a4') {
                alturaFaixaInput.value = '200';
            } else if (tamanho === 'a6') {
                alturaFaixaInput.value = '130';
            } else {
                alturaFaixaInput.value = '150'; // A5
            }
        } else {
            cabecalhoSection.style.display = 'none';
            coresCabecalhoSection.style.display = 'none';
            controlesFaixaSection.style.display = 'none';
            controlesFaixaSection2.style.display = 'none';
        }
    }
    
    // Função para controlar campo de data de validade
    function controlarDataValidade() {
        const rodapeValidade = document.getElementById('rodape_validade');
        const dataValidadeSection = document.getElementById('data_validade_section');
        
        if (rodapeValidade.checked) {
            dataValidadeSection.style.display = 'block';
        } else {
            dataValidadeSection.style.display = 'none';
        }
    }
    
    // Função para controlar campo de período da promoção
    function controlarPeriodoPromocao() {
        const rodapePromocao = document.getElementById('rodape_promocao');
        const periodoPromocaoSection = document.getElementById('periodo_promocao_section');
        
        if (rodapePromocao.checked) {
            periodoPromocaoSection.style.display = 'block';
        } else {
            periodoPromocaoSection.style.display = 'none';
        }
    }
    
    // Função para validar datas da promoção
    function validarDatasPromocao() {
        const dataInicio = document.getElementById('data_inicio_promocao').value;
        const dataFim = document.getElementById('data_fim_promocao').value;
        const rodapePromocao = document.getElementById('rodape_promocao').checked;
        
        if (rodapePromocao && dataInicio) {
            const dataInicioObj = new Date(dataInicio);
            
            // Validar se a data fim não é menor que a data início
            if (dataFim) {
                const dataFimObj = new Date(dataFim);
                if (dataFimObj < dataInicioObj) {
                    alert('A data final não pode ser menor que a data inicial!');
                    document.getElementById('data_fim_promocao').value = '';
                    return false;
                }
            }
        }
        return true;
    }
    
    // Função para definir data mínima do campo data fim
    function definirDataMinimaFim() {
        const dataInicio = document.getElementById('data_inicio_promocao').value;
        const dataFimInput = document.getElementById('data_fim_promocao');
        
        if (dataInicio) {
            dataFimInput.min = dataInicio;
            
            // Se a data fim atual for menor que a data início, limpar o campo
            if (dataFimInput.value && dataFimInput.value < dataInicio) {
                dataFimInput.value = '';
            }
        }
    }
    
    // Função para controlar campo de parcelamento
    function controlarParcelamento() {
        const rodapeParcelamento = document.getElementById('rodape_parcelamento');
        const parcelamentoSection = document.getElementById('parcelamento_section');
        
        if (rodapeParcelamento.checked) {
            parcelamentoSection.style.display = 'block';
        } else {
            parcelamentoSection.style.display = 'none';
        }
    }
    
    // Função para controlar campo de pagamento específico
    function controlarPagamentoEspecifico() {
        const rodapePagamentoEspecifico = document.getElementById('rodape_pagamento_especifico');
        const pagamentoEspecificoSection = document.getElementById('pagamento_especifico_section');
        
        if (rodapePagamentoEspecifico.checked) {
            pagamentoEspecificoSection.style.display = 'block';
        } else {
            pagamentoEspecificoSection.style.display = 'none';
        }
    }

    // Inicializar campos do cabeçalho
    controlarCamposCabecalho('a4');

    // Buscar produto por EAN
    btnBuscarEan.addEventListener('click', function(e) {
        e.preventDefault();
        console.log('Botão clicado!');
        const eanInput = document.getElementById('ean_busca');
        const ean = eanInput.value.trim();
        console.log('EAN digitado:', ean);
        console.log('EAN input element:', eanInput);
        console.log('EAN input value:', eanInput.value);
        
        if (!ean) {
            alert('Digite o EAN do produto');
            return;
        }

        console.log('Fazendo requisição...');
        loading.style.display = 'block';
        
        fetch('/extras/buscar-produto-ean', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `ean=${ean}`
        })
        .then(response => {
            console.log('Response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Dados recebidos:', data);
            loading.style.display = 'none';
            
            if (data.sucesso) {
                console.log('Preenchendo campos...');
                document.getElementById('nome_produto').value = data.produto.nome;
                document.getElementById('preco').value = data.produto.preco;
                // Manter o volume padrão como "Un" ao buscar produto
                document.getElementById('volume_produto').value = 'Un';
                console.log('Campos preenchidos!');
                atualizarPrevia();
            } else {
                alert('Erro: ' + data.erro);
            }
        })
        .catch(error => {
            console.error('Erro na requisição:', error);
            loading.style.display = 'none';
            alert('Erro ao buscar produto: ' + error.message);
        });
    });

    // Atualizar prévia em tempo real
    document.getElementById('nome_produto').addEventListener('input', atualizarPrevia);
    document.getElementById('preco').addEventListener('input', atualizarPrevia);
    document.getElementById('volume_produto').addEventListener('change', atualizarPrevia);

    document.getElementById('cor_fundo').addEventListener('change', atualizarPrevia);
    document.getElementById('cor_texto').addEventListener('change', atualizarPrevia);
    
    // Configurações do cabeçalho
    document.getElementById('fonte_cabecalho').addEventListener('change', atualizarPrevia);
    document.getElementById('estilo_cabecalho').addEventListener('change', atualizarPrevia);
    
    // Configurações do produto
    document.getElementById('fonte_produto').addEventListener('change', atualizarPrevia);
    document.getElementById('tamanho_fonte_produto').addEventListener('change', atualizarPrevia);
    document.getElementById('estilo_produto').addEventListener('change', atualizarPrevia);
    
    // Configurações do preço
    document.getElementById('fonte_preco').addEventListener('change', atualizarPrevia);
    document.getElementById('tamanho_fonte_preco').addEventListener('change', atualizarPrevia);
    document.getElementById('estilo_preco').addEventListener('change', atualizarPrevia);
    
    // Configurações do cabeçalho da placa
    document.getElementById('cabecalho').addEventListener('change', atualizarPrevia);
    document.getElementById('cor_faixa_cabecalho').addEventListener('change', atualizarPrevia);
    document.getElementById('cor_texto_cabecalho').addEventListener('change', atualizarPrevia);
    document.getElementById('altura_faixa').addEventListener('input', atualizarPrevia);
    document.getElementById('largura_faixa').addEventListener('input', atualizarPrevia);
    document.getElementById('posicao_top_faixa').addEventListener('input', atualizarPrevia);
    
    // Configurações do rodapé
    document.getElementById('rodape_menores').addEventListener('change', atualizarPrevia);
    document.getElementById('rodape_validade').addEventListener('change', controlarDataValidade);
    document.getElementById('rodape_estoque').addEventListener('change', atualizarPrevia);
    document.getElementById('rodape_promocao').addEventListener('change', controlarPeriodoPromocao);
    document.getElementById('rodape_parcelamento').addEventListener('change', controlarParcelamento);
    document.getElementById('rodape_pagamento_especifico').addEventListener('change', controlarPagamentoEspecifico);
    document.getElementById('data_validade').addEventListener('change', atualizarPrevia);
    document.getElementById('data_inicio_promocao').addEventListener('change', function() {
        definirDataMinimaFim();
        if (validarDatasPromocao()) {
            atualizarPrevia();
        }
    });
    document.getElementById('data_fim_promocao').addEventListener('change', function() {
        if (validarDatasPromocao()) {
            atualizarPrevia();
        }
    });
    
    // Event listeners para parcelamento e pagamentos
    document.getElementById('numero_parcelas').addEventListener('change', atualizarPrevia);
    
    // Event listeners para formas de pagamento específicas
    document.getElementById('pagamento_dinheiro').addEventListener('change', atualizarPrevia);
    document.getElementById('pagamento_pix').addEventListener('change', atualizarPrevia);
    document.getElementById('pagamento_cartao_debito').addEventListener('change', atualizarPrevia);
    document.getElementById('pagamento_cartao_credito').addEventListener('change', atualizarPrevia);
    document.getElementById('pagamento_boleto').addEventListener('change', atualizarPrevia);
    document.getElementById('pagamento_transferencia').addEventListener('change', atualizarPrevia);
    document.getElementById('pagamento_vale').addEventListener('change', atualizarPrevia);
    document.getElementById('pagamento_cheque').addEventListener('change', atualizarPrevia);
    
    // Configurações da borda
    document.getElementById('borda_placa').addEventListener('change', atualizarPrevia);



    // Imprimir placa
    btnImprimir.addEventListener('click', function() {
        // Atualizar a prévia antes de imprimir para garantir que está atualizada
        atualizarPrevia();
        
        const placaContainer = document.getElementById('placaContainer');
        const printWindow = window.open('', '_blank');
        
        printWindow.document.write(`
            <html>
                <head>
                    <title>Placa de Preço</title>
                    <style>
                        body { margin: 0; padding: 0; }
                        .placa-container { page-break-inside: avoid; }
                        
                        /* Estilos para preço dividido */
                        .preco-dividido {
                            display: flex;
                            align-items: flex-start;
                            justify-content: center;
                            gap: 2px;
                            width: 100%;
                            max-width: 100%;
                            padding: 0 10px;
                            box-sizing: border-box;
                        }
                        
                        .parte-inteira {
                            line-height: 1;
                        }
                        
                        .parte-decimal {
                            line-height: 1;
                            align-self: flex-start;
                            margin-top: 0;
                        }
                        
                        .volume-produto {
                            line-height: 1;
                            align-self: flex-end;
                            margin-top: 0;
                            margin-left: 2px;
                            font-size: 0.6em;
                            opacity: 0.8;
                        }
                        
                        .cifrao {
                            line-height: 1;
                            align-self: flex-start;
                            margin-top: 0;
                            margin-right: 2px;
                            font-size: 0.6em;
                            opacity: 0.8;
                        }
                    </style>
                </head>
                <body>
                    ${placaContainer.outerHTML}
                </body>
            </html>
        `);
        
        printWindow.document.close();
        printWindow.print();
    });

    // Salvar como JPG
    btnSalvarJPG.addEventListener('click', function() {
        // Atualizar a prévia antes de salvar
        atualizarPrevia();
        
        const placaContainer = document.getElementById('placaContainer');
        
        // Usar html2canvas para converter a placa em imagem
        html2canvas(placaContainer, {
            scale: 2, // Melhor qualidade
            useCORS: true,
            allowTaint: true,
            backgroundColor: null
        }).then(canvas => {
            // Obter nome do produto e preço para o nome do arquivo
            const nomeProduto = document.getElementById('nome_produto').value || 'produto';
            const preco = document.getElementById('preco').value || '0-00';
            
            // Limpar nome do arquivo (remover caracteres especiais)
            const nomeArquivo = `${nomeProduto}-${preco.replace(/[^\w\s-]/g, '')}`.replace(/\s+/g, '-').toLowerCase();
            
            // Criar link para download
            const link = document.createElement('a');
            link.download = `${nomeArquivo}.jpg`;
            link.href = canvas.toDataURL('image/jpeg', 0.9);
            link.click();
        }).catch(error => {
            console.error('Erro ao gerar JPG:', error);
            alert('Erro ao gerar arquivo JPG. Tente novamente.');
        });
    });

    // Salvar como PDF
    btnSalvarPDF.addEventListener('click', function() {
        // Atualizar a prévia antes de salvar
        atualizarPrevia();
        
        const placaContainer = document.getElementById('placaContainer');
        
        // Usar html2canvas para converter a placa em imagem primeiro
        html2canvas(placaContainer, {
            scale: 2, // Melhor qualidade
            useCORS: true,
            allowTaint: true,
            backgroundColor: null
        }).then(canvas => {
            // Usar jsPDF para criar o PDF
            const imgData = canvas.toDataURL('image/jpeg', 0.9);
            const { jsPDF } = window.jspdf;
            const pdf = new jsPDF('p', 'mm', 'a4');
            
            // Calcular dimensões para centralizar a imagem no PDF
            const imgWidth = 210; // Largura A4
            const imgHeight = (canvas.height * imgWidth) / canvas.width;
            
            // Centralizar verticalmente se necessário
            const y = (297 - imgHeight) / 2; // Altura A4
            
            pdf.addImage(imgData, 'JPEG', 0, y, imgWidth, imgHeight);
            
            // Obter nome do produto e preço para o nome do arquivo
            const nomeProduto = document.getElementById('nome_produto').value || 'produto';
            const preco = document.getElementById('preco').value || '0-00';
            
            // Limpar nome do arquivo (remover caracteres especiais)
            const nomeArquivo = `${nomeProduto}-${preco.replace(/[^\w\s-]/g, '')}`.replace(/\s+/g, '-').toLowerCase();
            
            pdf.save(`${nomeArquivo}.pdf`);
        }).catch(error => {
            console.error('Erro ao gerar PDF:', error);
            alert('Erro ao gerar arquivo PDF. Tente novamente.');
        });
    });

    // Funções auxiliares
    function atualizarTamanhoPlaca(tamanho) {
        const dimensoes = {
            'a4': { width: '210mm', height: '297mm', placas: 1 },
            'a5': { width: '210mm', height: '297mm', placas: 2 }, // A4 com 2 placas A5
            'a6': { width: '210mm', height: '297mm', placas: 4 }, // A4 com 4 placas A6
            '100x35': { width: '100mm', height: '35mm', placas: 1 },
            '80x40': { width: '80mm', height: '40mm', placas: 1 },
            '50x40': { width: '50mm', height: '40mm', placas: 1 },
            '100x50': { width: '100mm', height: '50mm', placas: 1 },
            '110x30': { width: '110mm', height: '30mm', placas: 1 },
            'instagram_story': { width: '1080px', height: '1920px', placas: 1 },
            'instagram_feed': { width: '1080px', height: '1080px', placas: 1 },
            'whatsapp_status': { width: '1080px', height: '1920px', placas: 1 }
        };

        const container = document.getElementById('placaContainer');
        const dimensao = dimensoes[tamanho];
        
        // Definir tamanho do container baseado no número de placas
        if (tamanho === 'a5') {
            container.style.width = '210mm';
            container.style.height = '297mm';
            container.style.display = 'grid';
            container.style.gridTemplateColumns = '1fr 1fr';
            container.style.gap = '0';
            container.style.border = '1px solid #ccc';
        } else if (tamanho === 'a6') {
            container.style.width = '210mm';
            container.style.height = '297mm';
            container.style.display = 'grid';
            container.style.gridTemplateColumns = '1fr 1fr';
            container.style.gridTemplateRows = '1fr 1fr';
            container.style.gap = '0';
            container.style.border = '1px solid #ccc';
        } else {
            container.style.width = dimensao.width;
            container.style.height = dimensao.height;
            container.style.display = 'block';
            container.style.gridTemplateColumns = '';
            container.style.gridTemplateRows = '';
            container.style.gap = '';
            container.style.border = '';
        }
        
        // Centralizar a placa na prévia
        container.parentElement.style.display = 'flex';
        container.parentElement.style.justifyContent = 'center';
        container.parentElement.style.alignItems = 'center';
        container.parentElement.style.height = '100%';
        
        atualizarPrevia();
    }

    function atualizarPrevia() {
        const nome = document.getElementById('nome_produto').value || 'Nome do Produto';
        const preco = document.getElementById('preco').value || '0,00';
        const volume = document.getElementById('volume_produto').value || 'Un';

        const corFundo = document.getElementById('cor_fundo').value;
        const corTexto = document.getElementById('cor_texto').value;
        
        // Configurações do cabeçalho
        const fonteCabecalho = document.getElementById('fonte_cabecalho').value;
        const estiloCabecalho = document.getElementById('estilo_cabecalho').value;
        
        // Configurações do produto
        const fonteProduto = document.getElementById('fonte_produto').value;
        const tamanhoFonteProduto = document.getElementById('tamanho_fonte_produto').value;
        const estiloProduto = document.getElementById('estilo_produto').value;
        
        // Configurações do preço
        const fontePreco = document.getElementById('fonte_preco').value;
        const tamanhoFontePreco = document.getElementById('tamanho_fonte_preco').value;
        const estiloPreco = document.getElementById('estilo_preco').value;
        
        // Configurações do cabeçalho da placa
        const cabecalho = document.getElementById('cabecalho').value;
        const corFaixaCabecalho = document.getElementById('cor_faixa_cabecalho').value;
        const corTextoCabecalho = document.getElementById('cor_texto_cabecalho').value;
        const alturaFaixa = document.getElementById('altura_faixa').value;
        const larguraFaixa = document.getElementById('largura_faixa').value;
        const posicaoTopFaixa = document.getElementById('posicao_top_faixa').value;
        
        // Configurações do rodapé
        const rodapeMenores = document.getElementById('rodape_menores').checked;
        const rodapeValidade = document.getElementById('rodape_validade').checked;
        const rodapeEstoque = document.getElementById('rodape_estoque').checked;
        const rodapePromocao = document.getElementById('rodape_promocao').checked;
        const rodapePagamentos = document.getElementById('rodape_pagamentos').checked;
        const rodapeParcelamento = document.getElementById('rodape_parcelamento').checked;
        const rodapePagamentoEspecifico = document.getElementById('rodape_pagamento_especifico').checked;
        const dataValidade = document.getElementById('data_validade').value;
        const dataInicioPromocao = document.getElementById('data_inicio_promocao').value;
        const dataFimPromocao = document.getElementById('data_fim_promocao').value;
        const numeroParcelas = document.getElementById('numero_parcelas').value;
        
        // Capturar formas de pagamento selecionadas
        const formasPagamento = [];
        if (document.getElementById('pagamento_dinheiro').checked) formasPagamento.push('Dinheiro');
        if (document.getElementById('pagamento_pix').checked) formasPagamento.push('PIX');
        if (document.getElementById('pagamento_cartao_debito').checked) formasPagamento.push('Cartão de Débito');
        if (document.getElementById('pagamento_cartao_credito').checked) formasPagamento.push('Cartão de Crédito');
        if (document.getElementById('pagamento_boleto').checked) formasPagamento.push('Boleto');
        if (document.getElementById('pagamento_transferencia').checked) formasPagamento.push('Transferência');
        if (document.getElementById('pagamento_vale').checked) formasPagamento.push('Vale');
        if (document.getElementById('pagamento_cheque').checked) formasPagamento.push('Cheque');
        
        // Configurações da borda
        const bordaPlaca = document.getElementById('borda_placa').checked;
        
        const tamanhoAtivo = document.querySelector('.size-option.active').dataset.size;

        const container = document.getElementById('placaContainer');
        
        // Limpar container
        container.innerHTML = '';
        
        // Determinar número de placas
        let numPlacas = 1;
        if (tamanhoAtivo === 'a5') numPlacas = 2;
        else if (tamanhoAtivo === 'a6') numPlacas = 4;
        
        // Criar placas
        for (let i = 0; i < numPlacas; i++) {
            const placaDiv = document.createElement('div');
            placaDiv.className = 'placa-content';
            placaDiv.style.background = corFundo;
            placaDiv.style.border = '1px solid #ddd';
            placaDiv.style.padding = '15px';
            placaDiv.style.display = 'flex';
            placaDiv.style.flexDirection = 'column';
            placaDiv.style.justifyContent = 'center';
            placaDiv.style.alignItems = 'center';
            placaDiv.style.height = '100%';
            placaDiv.style.width = '100%';
            placaDiv.style.boxSizing = 'border-box';
            
            // Ajustar padding bottom se houver rodapé
            if (rodapeMenores || rodapeValidade || rodapeEstoque || rodapePromocao || rodapePagamentos || rodapeParcelamento || rodapePagamentoEspecifico) {
                placaDiv.style.paddingBottom = '60px';
            }
            

            
            // Aplicar borda
            if (bordaPlaca) {
                placaDiv.style.border = '3px solid #ff0000';
            } else {
                placaDiv.style.border = '1px solid #ddd';
            }
            
            // Rotação horizontal para A5
            if (tamanhoAtivo === 'a5') {
                placaDiv.style.transform = 'rotate(90deg)';
                placaDiv.style.transformOrigin = 'center center';
                placaDiv.style.width = '148mm';
                placaDiv.style.height = '210mm';
                
                // Rotacionar o conteúdo interno para manter a orientação correta
                placaDiv.style.display = 'flex';
                placaDiv.style.flexDirection = 'column';
                placaDiv.style.justifyContent = 'center';
                placaDiv.style.alignItems = 'center';
            }
            
            // Verificar se deve mostrar cabeçalho (A4, A5, A6)
            const mostrarCabecalho = ['a4', 'a5', 'a6'].includes(tamanhoAtivo);
            
            if (mostrarCabecalho) {
                const cabecalhoDiv = document.createElement('div');
                cabecalhoDiv.style.backgroundColor = corFaixaCabecalho;
                cabecalhoDiv.style.color = corTextoCabecalho;
                cabecalhoDiv.style.fontFamily = fonteCabecalho;
                cabecalhoDiv.style.fontWeight = estiloCabecalho.includes('bold') ? 'bold' : 'normal';
                cabecalhoDiv.style.fontStyle = estiloCabecalho.includes('italic') ? 'italic' : 'normal';
                cabecalhoDiv.style.textAlign = 'center';
                cabecalhoDiv.style.display = 'flex';
                cabecalhoDiv.style.alignItems = 'center';
                cabecalhoDiv.style.justifyContent = 'center';
                cabecalhoDiv.style.boxSizing = 'border-box';
                cabecalhoDiv.style.padding = '0 10px';
                cabecalhoDiv.style.height = `${alturaFaixa}px`;
                cabecalhoDiv.style.width = `${larguraFaixa}%`;
                cabecalhoDiv.style.position = 'absolute';
                cabecalhoDiv.style.top = `${posicaoTopFaixa}px`;
                cabecalhoDiv.style.left = '0';
                
                // Ajustar posicionamento para A5 rotacionado
                if (tamanhoAtivo === 'a5') {
                    cabecalhoDiv.style.transform = 'rotate(-90deg)';
                    cabecalhoDiv.style.transformOrigin = 'center center';
                }
                cabecalhoDiv.style.borderRadius = '3px 3px 0 0';
                cabecalhoDiv.style.lineHeight = '1';
                cabecalhoDiv.style.wordWrap = 'break-word';
                
                // Calcular tamanho da fonte dinamicamente
                const larguraPlaca = placaDiv.offsetWidth || 210; // mm
                const alturaFaixaNum = parseInt(alturaFaixa);
                
                // Calcular largura disponível baseada na largura da faixa
                const larguraDisponivel = larguraPlaca * (parseInt(larguraFaixa) / 100);
                const alturaDisponivel = alturaFaixaNum * 0.9; // 90% da altura da faixa
                
                // Calcular tamanho da fonte baseado no texto e espaço disponível
                // Usar um fator muito maior para ocupar 100% da largura
                const tamanhoFonteResponsivo = Math.min(
                    larguraDisponivel / cabecalho.length * 5, // 5.5 para ocupar 100% da largura (aumentado)
                    alturaDisponivel
                );
                
                // Aplicar tamanho calculado com limites mais generosos
                const tamanhoFinal = Math.max(Math.min(tamanhoFonteResponsivo, 250), 20); // Entre 20px e 280px (aumentado)
                cabecalhoDiv.style.fontSize = `${tamanhoFinal}px`;
                
                // Garantir que o texto ocupe toda a largura
                cabecalhoDiv.style.whiteSpace = 'nowrap';
                cabecalhoDiv.style.overflow = 'visible';
                cabecalhoDiv.style.textOverflow = 'clip';
                cabecalhoDiv.style.letterSpacing = '1px'; // Espaçamento entre letras
                
                cabecalhoDiv.textContent = cabecalho;
                
                // Adicionar margem ao conteúdo para compensar a faixa
                placaDiv.style.paddingTop = `${parseInt(alturaFaixa) + parseInt(posicaoTopFaixa) + 10}px`;
                placaDiv.style.position = 'relative';
                
                placaDiv.appendChild(cabecalhoDiv);
                
                
            }
            
            // Nome do produto
            const nomeDiv = document.createElement('div');
            nomeDiv.style.color = corTexto;
            nomeDiv.style.fontFamily = fonteProduto;
            nomeDiv.style.fontSize = tamanhoFonteProduto;
            nomeDiv.style.fontWeight = estiloProduto.includes('bold') ? 'bold' : 'normal';
            nomeDiv.style.fontStyle = estiloProduto.includes('italic') ? 'italic' : 'normal';
            nomeDiv.style.textAlign = 'center';
            nomeDiv.style.marginBottom = '8px';
            nomeDiv.style.width = '100%';
            
            // Ajustar para A5 rotacionado
            if (tamanhoAtivo === 'a5') {
                nomeDiv.style.transform = 'rotate(-90deg)';
                nomeDiv.style.transformOrigin = 'center center';
            }
            

            
            nomeDiv.textContent = nome;
            placaDiv.appendChild(nomeDiv);
            
            // Nome da empresa na lateral direita (para placas sem cabeçalho)
            if (!['a4', 'a5', 'a6'].includes(tamanhoAtivo)) {
                const empresaDiv = document.createElement('div');
                empresaDiv.textContent = 'Ágil Fiscal';
                empresaDiv.style.position = 'absolute';
                empresaDiv.style.right = '25px';
                empresaDiv.style.top = '10px'; // Posição fixa no topo
                empresaDiv.style.fontSize = '12px';
                empresaDiv.style.fontFamily = 'Arial, sans-serif';
                empresaDiv.style.fontWeight = 'normal';
                empresaDiv.style.color = corTexto;
                empresaDiv.style.transform = 'rotate(90deg)';
                empresaDiv.style.transformOrigin = 'bottom right';
                empresaDiv.style.whiteSpace = 'nowrap';
                empresaDiv.style.letterSpacing = '0.5px';
                empresaDiv.style.opacity = '0.7';
                
                placaDiv.appendChild(empresaDiv);
            }
            
            // Preço
            const precoDiv = document.createElement('div');
            precoDiv.className = 'preco-dividido';
            precoDiv.style.color = corTexto;
            precoDiv.style.fontFamily = fontePreco;
            precoDiv.style.fontWeight = estiloPreco.includes('bold') ? 'bold' : 'normal';
            precoDiv.style.fontStyle = estiloPreco.includes('italic') ? 'italic' : 'normal';
            precoDiv.style.textAlign = 'center';
            precoDiv.style.width = '100%';
            precoDiv.style.marginTop = '5px';
            
            // Calcular tamanho da fonte responsivo baseado no preço
            const precoNum = parseFloat(preco.replace(',', '.'));
            const partesPreco = preco.split(',');
            const parteInteira = partesPreco[0];
            const parteDecimal = partesPreco[1] || '00';
            let tamanhoFonteResponsivo = tamanhoFontePreco;
            
            // Ajustar tamanho baseado na parte inteira e espaço disponível
            const larguraContainer = placaDiv.offsetWidth || 200;
            const larguraDisponivel = larguraContainer * 0.9; // 90% da largura disponível
            
            if (parteInteira.length >= 3) {
                // Para valores com 3 ou mais dígitos na parte inteira, reduzir o tamanho
                const tamanhoBase = parseInt(tamanhoFontePreco);
                tamanhoFonteResponsivo = `${Math.max(tamanhoBase * 0.8, 60)}px`; // Aumentado mínimo de 48 para 60
            } else if (parteInteira.length === 2) {
                // Para 2 dígitos, usar tamanho maior
                const tamanhoBase = parseInt(tamanhoFontePreco);
                tamanhoFonteResponsivo = `${Math.max(tamanhoBase * 1.1, 80)}px`; // 10% maior
            } else {
                // Para 1 dígito, usar tamanho ainda maior
                const tamanhoBase = parseInt(tamanhoFontePreco);
                tamanhoFonteResponsivo = `${Math.max(tamanhoBase * 1.3, 100)}px`; // 30% maior
            }
            
            precoDiv.style.fontSize = tamanhoFonteResponsivo;
            
            // Ajustar para A5 rotacionado
            if (tamanhoAtivo === 'a5') {
                precoDiv.style.transform = 'rotate(-90deg)';
                precoDiv.style.transformOrigin = 'center center';
            }
            
            // Criar estrutura HTML para as duas partes do preço com volume e cifrão
            precoDiv.innerHTML = `
                <span class="cifrao">R$</span>
                <span class="parte-inteira">${parteInteira}</span>
                <span class="parte-decimal">,${parteDecimal}</span>
                <span class="volume-produto">${volume}</span>
            `;
            
            // Aplicar estilos específicos para cada parte
            const parteInteiraSpan = precoDiv.querySelector('.parte-inteira');
            const parteDecimalSpan = precoDiv.querySelector('.parte-decimal');
            
            // Parte inteira: 50% maior
            const tamanhoInteira = parseInt(tamanhoFonteResponsivo);
            
            // Cifrão: mesmo tamanho do volume, lado esquerdo superior
            const cifraoSpan = precoDiv.querySelector('.cifrao');
            cifraoSpan.style.fontSize = `${tamanhoInteira * 0.3}px`;
            cifraoSpan.style.fontWeight = 'normal';
            cifraoSpan.style.color = corTexto;
            cifraoSpan.style.alignSelf = 'flex-start';
            cifraoSpan.style.marginTop = '0';
            cifraoSpan.style.marginRight = '2px';
            cifraoSpan.style.lineHeight = '1';
            cifraoSpan.style.opacity = '0.8';
            parteInteiraSpan.style.fontSize = `${tamanhoInteira * 1.5}px`;
            parteInteiraSpan.style.fontWeight = 'bold';
            parteInteiraSpan.style.color = corTexto;
            parteInteiraSpan.style.lineHeight = '1';
            parteInteiraSpan.style.alignSelf = 'flex-end';
            
            // Parte decimal: tamanho normal e alinhada no topo
            parteDecimalSpan.style.fontSize = `${tamanhoInteira}px`;
            parteDecimalSpan.style.fontWeight = 'bold';
            parteDecimalSpan.style.color = corTexto;
            parteDecimalSpan.style.alignSelf = 'flex-start';
            parteDecimalSpan.style.marginTop = '0';
            parteDecimalSpan.style.lineHeight = '1';
            
            // Volume do produto: menor e mais discreto
            const volumeSpan = precoDiv.querySelector('.volume-produto');
            volumeSpan.style.fontSize = `${tamanhoInteira * 0.3}px`;
            volumeSpan.style.fontWeight = 'normal';
            volumeSpan.style.color = corTexto;
            volumeSpan.style.alignSelf = 'flex-end';
            volumeSpan.style.marginTop = '0';
            volumeSpan.style.marginLeft = '2px';
            volumeSpan.style.lineHeight = '1';
            volumeSpan.style.opacity = '0.8';
            placaDiv.appendChild(precoDiv);
            
            // Rodapé
            if (rodapeMenores || rodapeValidade || rodapeEstoque || rodapePromocao || rodapePagamentos || rodapeParcelamento || rodapePagamentoEspecifico) {
                const rodapeDiv = document.createElement('div');
                rodapeDiv.style.color = corTexto;
                rodapeDiv.style.fontFamily = fonteProduto;
                rodapeDiv.style.fontSize = '16px';
                rodapeDiv.style.textAlign = 'center';
                rodapeDiv.style.marginTop = '15px';
                rodapeDiv.style.borderTop = '2px solid #ccc';
                rodapeDiv.style.paddingTop = '8px';
                rodapeDiv.style.paddingBottom = '5px';
                rodapeDiv.style.position = 'absolute';
                rodapeDiv.style.bottom = '5px';
                rodapeDiv.style.left = '5px';
                rodapeDiv.style.right = '5px';
                rodapeDiv.style.backgroundColor = corFundo;
                
                const rodapeTextos = [];
                if (rodapeMenores) rodapeTextos.push('Venda proibida para menores de 18 anos');
                if (rodapeValidade && dataValidade) {
                    const data = new Date(dataValidade);
                    const dataFormatada = data.toLocaleDateString('pt-BR');
                    rodapeTextos.push(`Produto com validade próxima - Vencimento: ${dataFormatada}`);
                }
                if (rodapeEstoque) rodapeTextos.push('Promoção válida enquanto durar o estoque');
                if (rodapePromocao && dataInicioPromocao && dataFimPromocao) {
                    const dataInicio = new Date(dataInicioPromocao);
                    const dataFim = new Date(dataFimPromocao);
                    const dataInicioFormatada = dataInicio.toLocaleDateString('pt-BR');
                    const dataFimFormatada = dataFim.toLocaleDateString('pt-BR');
                    rodapeTextos.push(`Promoção válida de ${dataInicioFormatada} até ${dataFimFormatada} ou enquanto durar o estoque`);
                }
                if (rodapePagamentos) {
                    rodapeTextos.push('Aceitamos todas as formas de pagamentos');
                }
                if (rodapeParcelamento && numeroParcelas) {
                    rodapeTextos.push(`Parcele suas compras em até ${numeroParcelas}x`);
                }
                if (rodapePagamentoEspecifico && formasPagamento.length > 0) {
                    const formasTexto = formasPagamento.join(', ');
                    rodapeTextos.push(`Promoção válida somente para pagamento em ${formasTexto}`);
                }
                
                rodapeDiv.textContent = rodapeTextos.join(' • ');
                placaDiv.appendChild(rodapeDiv);
            }
            
            container.appendChild(placaDiv);
        }
    }

    // Inicializar
    atualizarPrevia();
});
</script> 