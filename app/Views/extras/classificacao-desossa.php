<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Classificação de Desossa' ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- IMask -->
    <script src="https://unpkg.com/imask"></script>
    <!-- jsPDF para PDF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <!-- jsPDF-AutoTable para tabelas no PDF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.29/jspdf.plugin.autotable.min.js"></script>
    
    <style>
        .card-header-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-bottom: 3px solid #5a6fd8;
            border-radius: 12px 12px 0 0;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }
        
        .btn-toggle-section {
            transition: all 0.3s ease;
            border-radius: 50%;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }
        
        .btn-toggle-section:hover {
            transform: scale(1.1);
            box-shadow: 0 4px 12px rgba(255, 255, 255, 0.3);
            border-color: rgba(255, 255, 255, 0.6);
        }
        
        .card-body {
            transition: all 0.3s ease;
            overflow: hidden;
        }
        
        .card-body.collapsed {
            max-height: 0;
            padding: 0;
            margin: 0;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: 3px solid #667eea;
            color: white;
            font-weight: 600;
            padding: 12px 30px;
            border-radius: 25px;
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4c93 100%);
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.5);
            color: white;
            border-color: #5a6fd8;
        }
        
        .btn-success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: 3px solid #28a745;
            color: white;
            font-weight: 600;
            padding: 12px 30px;
            border-radius: 25px;
            box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
            transition: all 0.3s ease;
        }
        
        .btn-success:hover {
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
        
        .card.shadow-sm {
            border: 3px solid #e0e0e0;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
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
        
        .table {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .table tbody tr {
            border-bottom: 2px solid #f8f9fa;
        }
        
                 .container-fluid {
             background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
             padding: 30px;
             border-radius: 20px;
             box-shadow: 0 10px 30px rgba(0,0,0,0.1);
             margin-top: 20px;
         }
         
         body {
             background: linear-gradient(135deg,rgb(228, 228, 228) 0%,rgb(194, 194, 194) 100%);
             min-height: 100vh;
             padding: 20px 0;
         }
         
         .h3 {
             color: #495057;
             font-weight: 600;
         }
        
        .row {
            margin-bottom: 30px;
        }
        
        .upload-area {
            border: 2px dashed #dee2e6;
            border-radius: 10px;
            padding: 40px;
            text-align: center;
            background: #f8f9fa;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .upload-area:hover {
            border-color: #667eea;
            background: #e3f2fd;
        }
        
        .upload-area.dragover {
            border-color: #667eea;
            background: #e3f2fd;
            transform: scale(1.02);
        }
        
        /* Estilos para os novos campos do resumo */
        .text-primary {
            color: #667eea !important;
        }
        
        .text-success {
            color: #28a745 !important;
        }
        
        .text-warning {
            color: #ffc107 !important;
        }
        
        .text-danger {
            color: #dc3545 !important;
        }
        
        .text-info {
            color: #17a2b8 !important;
        }
        
        .text-secondary {
            color: #6c757d !important;
        }
        
        .fw-bold {
            font-weight: 700 !important;
        }
        
        .text-muted {
            color: #6c757d !important;
        }
        
        .d-block {
            display: block !important;
        }
        
        .corte-item {
            background: white;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
        }
        
        .corte-item:hover {
            border-color: #667eea;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.2);
        }
        
        .corte-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .corte-nome {
            font-weight: 600;
            color: #495057;
        }
        
        .corte-valor {
            font-weight: bold;
            color: #28a745;
        }
        
        .progress {
            height: 8px;
            border-radius: 4px;
            margin-top: 5px;
        }
        
        .progress-bar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .cursor-pointer {
            cursor: pointer;
        }
        
        .produto-card {
            transition: all 0.3s ease;
            border: 2px solid #e9ecef;
        }
        
        .produto-card:hover {
            border-color: #667eea;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.2);
            transform: translateY(-2px);
        }
        
        .produto-card .card-title {
            color: #667eea;
            font-weight: 600;
        }
        
        .produto-card .card-body {
            padding: 1rem;
        }
        
        .produto-card small {
            font-size: 0.75rem;
        }
        
                 .produto-card p {
             margin-bottom: 0.25rem;
         }
         
                   .grupo-corte-card {
              transition: all 0.3s ease;
              border: 2px solid #e9ecef;
              cursor: pointer;
              height: 100%;
              min-height: 200px;
          }
          
          .grupo-corte-card:hover {
              border-color: #667eea;
              box-shadow: 0 4px 15px rgba(102, 126, 234, 0.2);
              transform: translateY(-2px);
          }
          
          .grupo-corte-card.selected {
              border-color: #28a745;
              background-color: #f8fff9;
              box-shadow: 0 4px 15px rgba(40, 167, 69, 0.2);
          }
          
          .grupo-corte-card .card-header {
              background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
              border-bottom: 2px solid #dee2e6;
              font-weight: 600;
              color: #495057;
              padding: 0.75rem;
          }
          
          .grupo-corte-card.selected .card-header {
              background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
              border-bottom-color: #28a745;
              color: #155724;
          }
          
          .grupo-corte-card .card-body {
              padding: 0.75rem;
              flex-grow: 1;
              display: flex;
              flex-direction: column;
          }
          
          .grupo-corte-card .cortes-lista {
              font-size: 0.85rem;
              color: #6c757d;
              margin: 0;
              flex-grow: 1;
              list-style: none;
              padding-left: 0;
          }
          
          .grupo-corte-card .cortes-lista li {
              margin-bottom: 0.25rem;
              padding: 0.125rem 0;
              border-bottom: 1px solid #f8f9fa;
          }
          
          .grupo-corte-card .cortes-lista li:last-child {
              border-bottom: none;
          }
          
          .grupo-corte-card .check-icon {
              position: absolute;
              top: 10px;
              right: 10px;
              color: #28a745;
              font-size: 1.2rem;
              opacity: 0;
              transition: opacity 0.3s ease;
          }
          
          .grupo-corte-card.selected .check-icon {
              opacity: 1;
          }
          
          /* Responsividade para telas menores */
          @media (max-width: 768px) {
              .grupo-corte-card {
                  min-height: 180px;
              }
              
              .grupo-corte-card .card-header h6 {
                  font-size: 0.9rem;
              }
              
              .grupo-corte-card .cortes-lista {
                  font-size: 0.8rem;
              }
          }
          
          @media (max-width: 576px) {
              .grupo-corte-card {
                  min-height: 160px;
              }
              
              .grupo-corte-card .card-header h6 {
                  font-size: 0.85rem;
              }
              
              .grupo-corte-card .cortes-lista {
                  font-size: 0.75rem;
              }
          }
         
         .modo-container {
             display: none;
         }
     </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <!-- Cabeçalho da Página -->
        <div class="col-12 mb-4">
            <div class="d-flex align-items-center">
                <i class="fas fa-cut text-primary me-3" style="font-size: 2rem;"></i>
                <div>
                    <h1 class="h3 mb-0">Classificação de Desossa</h1>
                    <p class="text-muted mb-0">Análise detalhada de rendimento e classificação das carnes.</p>
                </div>
                <div class="ms-auto">
                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#helpModal">
                        <i class="fas fa-question-circle"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

        <!-- Etapa 1: Dados Brutos -->
    <div class="row" id="etapaDadosBrutos">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header card-header-custom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-building me-2"></i>
                        Dados Brutos
                    </h5>
                    <button type="button" class="btn btn-sm btn-outline-light btn-toggle-section" onclick="toggleSection('dadosBoi')" id="btnToggleDadosBoi" title="Recolher seção">
                        <i class="fas fa-chevron-up" id="iconDadosBoi"></i>
                    </button>
                </div>
                <div class="card-body" id="bodyDadosBoi">
                    <form id="formBoi">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Identificação</label>
                                <input type="text" class="form-control" id="identificacao_boi" name="identificacao_boi" placeholder="Ex: BOI001" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Data de Abate</label>
                                <input type="date" class="form-control" id="data_abate" name="data_abate" required>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Peso Total (kg)</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="peso_total" name="peso_total" placeholder="0,00" required>
                                    <span class="input-group-text">kg</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Preço por kg</label>
                                <div class="input-group">
                                    <span class="input-group-text">R$</span>
                                    <input type="text" class="form-control" id="preco_kg" name="preco_kg" placeholder="0,00" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Custo Total</label>
                                <div class="input-group">
                                    <span class="input-group-text">R$</span>
                                    <input type="text" class="form-control" id="custo_total" name="custo_total" placeholder="0,00" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Fornecedor</label>
                                <input type="text" class="form-control" id="fornecedor" name="fornecedor" placeholder="Nome do fornecedor">
                            </div>
                        </div>
                        
                        <!-- Importação XML -->
                        <div class="section-title">
                            <i class="fas fa-file-code me-2"></i>
                            Importar Dados XML
                        </div>
                        
                        <div class="upload-area" id="uploadXml">
                            <i class="fas fa-file-upload fa-3x text-muted mb-3"></i>
                            <p class="mb-2">Arraste e solte um arquivo XML ou clique para selecionar</p>
                            <small class="text-muted">Suporte para arquivos XML de nota fiscal</small>
                            <input type="file" id="xmlFile" accept=".xml" style="display: none;">
                        </div>
                        
                        <!-- Status do upload XML -->
                        <div id="xmlStatus" class="mt-2" style="display: none;">
                            <div class="alert alert-success alert-sm">
                                <i class="fas fa-check-circle me-2"></i>
                                <span id="xmlStatusText">XML carregado com sucesso!</span>
                            </div>
                        </div>
                        
                        <!-- Botões de ação -->
                        <div class="row mt-4" id="botoesAcaoDadosBrutos" style="display: none;">
                            <div class="col-12 text-center">
                                <button type="button" class="btn btn-success btn-lg me-3" onclick="salvarDadosBrutos()" id="botaoSalvarDadosBrutos">
                                    <i class="fas fa-save me-2"></i>
                                    Salvar e Continuar
                                </button>
                                <button type="button" class="btn btn-outline-secondary" onclick="limparCamposDadosBrutos()">
                                    <i class="fas fa-eraser me-2"></i>
                                    Limpar Campos
                                </button>
                                <small class="d-block text-muted mt-3">
                                    Clique em "Salvar e Continuar" para validar os dados e avançar para a próxima etapa
                                </small>
                            </div>
                        </div>
                        
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Etapa 2: Seleção de Grupos de Cortes -->
    <div class="row" id="etapaGruposCortes" style="display: none;">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header card-header-custom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-layer-group me-2"></i>
                        Seleção de Grupos de Cortes
                    </h5>
                    <button type="button" class="btn btn-sm btn-outline-light btn-toggle-section" onclick="toggleSection('gruposCortes')" id="btnToggleGruposCortes" title="Recolher seção">
                        <i class="fas fa-chevron-up" id="iconGruposCortes"></i>
                    </button>
                </div>
                <div class="card-body" id="bodyGruposCortes">
                    <div class="alert alert-info mb-3">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Instrução:</strong> Selecione os grupos de cortes que deseja classificar. Você pode selecionar múltiplos grupos.
                    </div>
                    
                    <div class="row mb-4" id="gruposCortesContainer">
                        <!-- Grupos serão inseridos dinamicamente -->
                    </div>
                    
                    <div class="text-center mb-4">
                        <button type="button" class="btn btn-primary" id="btnConfirmarGrupos" disabled>
                            <i class="fas fa-check me-2"></i>
                            Confirmar Grupos Selecionados
                        </button>
                    </div>
                    
                               </div>
            </div>
        </div>
    </div>

    <!-- Etapa 3: Classificação das Carnes -->
    <div class="row" id="etapaClassificacao" style="display: none;">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header card-header-custom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-drumstick-bite me-2"></i>
                        Classificação das Carnes
                    </h5>
                    <button type="button" class="btn btn-sm btn-outline-light btn-toggle-section" onclick="toggleSection('classificacaoCarnes')" id="btnToggleClassificacaoCarnes" title="Recolher seção">
                        <i class="fas fa-chevron-up" id="iconClassificacaoCarnes"></i>
                    </button>
                </div>
                <div class="card-body" id="bodyClassificacaoCarnes">
                    <form id="formCarnes">
                        <!-- Seleção do Modo de Inserção -->
                        <div class="section-title">
                            <i class="fas fa-cogs me-2"></i>
                            Modo de Inserção de Dados
                        </div>
                         
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="modoInsercao" id="modoTxt" value="txt">
                                    <label class="form-check-label" for="modoTxt">
                                        <i class="fas fa-file-alt me-1"></i> Arquivo TXT
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="modoInsercao" id="modoManual" value="manual">
                                    <label class="form-check-label" for="modoManual">
                                        <i class="fas fa-edit me-1"></i> Inserção Manual
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="modoInsercao" id="modoEtiqueta" value="etiqueta">
                                    <label class="form-check-label" for="modoEtiqueta">
                                        <i class="fas fa-barcode me-1"></i> Etiqueta (Código de Barras)
                                    </label>
                                </div>
                            </div>
                        </div>
                         
                        <!-- Modo TXT -->
                        <div id="modoTxtContainer" class="modo-container" style="display: none;">
                            <div class="section-title">
                                <i class="fas fa-file-alt me-2"></i>
                                Importar Classificação TXT
                            </div>
                            
                            <div class="upload-area" id="uploadTxt">
                                <i class="fas fa-file-upload fa-3x text-muted mb-3"></i>
                                <p class="mb-2">Arraste e solte um arquivo TXT ou clique para selecionar</p>
                                <small class="text-muted">Formato: CÓDIGO;CORTE;PESO;PREÇO_VENDA (um por linha)</small><br>
                                <small class="text-muted">O nome do corte deve ser exatamente como está no grupo de cortes</small>
                                <input type="file" id="txtFile" accept=".txt" style="display: none;">
                            </div>
                        </div>
                         
                        <!-- Modo Manual -->
                        <div id="modoManualContainer" class="modo-container" style="display: none;">
                            <div class="section-title">
                                <i class="fas fa-edit me-2"></i>
                                Inserção Manual
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label">Corte Atual</label>
                                    <div class="input-group">
                                        <span class="input-group-text">#<span id="numeroCorte">1</span></span>
                                        <input type="text" class="form-control" id="corte_atual" name="corte_atual" placeholder="Nome do corte" readonly>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Peso (kg)</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="peso_corte_manual" name="peso_corte_manual" placeholder="0,000" autofocus>
                                        <span class="input-group-text">kg</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Preço/kg</label>
                                    <div class="input-group">
                                        <span class="input-group-text">R$</span>
                                        <input type="text" class="form-control" id="preco_corte_manual" name="preco_corte_manual" placeholder="0,00">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Instrução:</strong> Digite o peso e pressione ENTER, depois digite o preço e pressione ENTER novamente. Digite 0 no peso para pular um corte.
                            </div>
                        </div>
                         
                        <!-- Modo Etiqueta -->
                        <div id="modoEtiquetaContainer" class="modo-container" style="display: none;">
                            <div class="section-title">
                                <i class="fas fa-barcode me-2"></i>
                                Leitura de Etiqueta
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label">Corte Atual</label>
                                    <div class="input-group">
                                        <span class="input-group-text">#<span id="numeroCorteEtiqueta">1</span></span>
                                        <input type="text" class="form-control" id="corte_atual_etiqueta" name="corte_atual_etiqueta" placeholder="Nome do corte" readonly>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Código de Barras</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="codigo_barras" name="codigo_barras" placeholder="2069600017009" autofocus>
                                        <button type="button" class="btn btn-outline-primary" id="btnScan">
                                            <i class="fas fa-barcode"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Preço/kg</label>
                                    <div class="input-group">
                                        <span class="input-group-text">R$</span>
                                        <input type="text" class="form-control" id="preco_corte_etiqueta" name="preco_corte_etiqueta" placeholder="0,00">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Instrução:</strong> Escaneie o código de barras e pressione ENTER, depois digite o preço e pressione ENTER novamente para avançar automaticamente.
                            </div>
                        </div>
                         
                        <!-- Lista de Cortes Classificados -->
                        <div id="listaCortesContainer" class="mt-4" style="display: none;">
                            <div class="section-title">
                                <i class="fas fa-list me-2"></i>
                                Cortes Classificados
                            </div>
                            
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead class="table-primary">
                                        <tr>
                                            <th>Ações</th>
                                            <th>Corte</th>
                                            <th>Peso (kg)</th>
                                            <th>% do Corte</th>
                                            <th>Custo</th>
                                            <th>Preço de Venda (R$/kg)</th>
                                            <th>Total Venda</th>
                                            <th>Margem</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tabela_cortes_classificados">
                                        <!-- Dados serão inseridos dinamicamente -->
                                    </tbody>
                                    <tfoot class="table-info">
                                        <tr class="fw-bold">
                                            <td><strong>TOTAIS:</strong></td>
                                            <td id="total_cortes_count">0</td>
                                            <td id="total_peso">0,000 kg</td>
                                            <td id="total_percentual">100,00%</td>
                                            <td id="total_custo">R$ 0,00</td>
                                            <td>-</td>
                                            <td id="total_venda">R$ 0,00</td>
                                            <td id="total_margem">0,00%</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                          
                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-success" id="btnCalcularRendimento" style="display: none;">
                                <i class="fas fa-calculator me-2"></i>
                                Calcular Rendimento
                            </button>
                        </div>
                        
                  
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Resultados -->
    <div class="row" id="resultados" style="display: none;">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header card-header-custom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-pie me-2"></i>
                        Análise de Rendimento
                    </h5>
                    <button type="button" class="btn btn-sm btn-outline-light btn-toggle-section" onclick="toggleSection('resultados')" id="btnToggleResultados" title="Recolher seção">
                        <i class="fas fa-chevron-up" id="iconResultados"></i>
                    </button>
                </div>
                <div class="card-body" id="bodyResultados">
                    <div class="row">
                        <!-- Resumo Geral -->
                        <div class="col-md-6">
                            <div class="result-card">
                                <h6 class="section-title">Resumo Geral</h6>
                                
                                <!-- Peso -->
                                <div class="mb-3">
                                    <h6 class="text-secondary mb-2">Peso (kg)</h6>
                                    <div class="row">
                                        <div class="col-4">
                                            <div class="text-center">
                                                <small class="text-muted d-block">Total</small>
                                                <div class="fw-bold text-dark" id="resumo_peso_total">0,000</div>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="text-center">
                                                <small class="text-muted d-block">Classificado</small>
                                                <div class="fw-bold text-dark" id="resumo_peso_classificado">0,000</div>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="text-center">
                                                <small class="text-muted d-block">Não Classificado</small>
                                                <div class="fw-bold text-dark" id="resumo_peso_nao_classificado">0,000</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Custo -->
                                <div class="mb-3">
                                    <h6 class="text-secondary mb-2">Custo (R$)</h6>
                                    <div class="row">
                                        <div class="col-4">
                                            <div class="text-center">
                                                <small class="text-muted d-block">Total</small>
                                                <div class="fw-bold text-dark" id="resumo_custo_total">R$ 0,00</div>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="text-center">
                                                <small class="text-muted d-block">Classificado</small>
                                                <div class="fw-bold text-dark" id="resumo_custo_classificado">R$ 0,00</div>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="text-center">
                                                <small class="text-muted d-block">Não Classificado</small>
                                                <div class="fw-bold text-dark" id="resumo_custo_nao_classificado">R$ 0,00</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Percentuais -->
                                <div class="mb-2">
                                    <h6 class="text-secondary mb-2">Percentuais (%)</h6>
                                    <div class="row">
                                        <div class="col-4">
                                            <div class="text-center">
                                                <small class="text-muted d-block">Classificado</small>
                                                <div class="fw-bold text-dark" id="resumo_percentual_classificado">0,00%</div>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="text-center">
                                                <small class="text-muted d-block">Não Classificado</small>
                                                <div class="fw-bold text-dark" id="resumo_percentual_nao_classificado">0,00%</div>
                                            </div>
                                        </div>
                                      
                                     
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Resumo Financeiro -->
                        <div class="col-md-6">
                            <div class="result-card">
                                <h6 class="section-title">Resumo Financeiro</h6>
                                
                                <!-- Valores -->
                                <div class="mb-3">
                                    <h6 class="text-secondary mb-2">Valores</h6>
                                    <div class="row">
                                        <div class="col-4">
                                            <div class="text-center">
                                                <small class="text-muted d-block">Valor Venda Carnes</small>
                                                <div class="fw-bold text-dark" id="resumo_valor_venda_carnes">R$ 0,00</div>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="text-center">
                                                <small class="text-muted d-block">Valor Lucro</small>
                                                <div class="fw-bold text-dark" id="resumo_valor_lucro">R$ 0,00</div>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="text-center">
                                                <small class="text-muted d-block">% Lucro</small>
                                                <div class="fw-bold text-dark" id="resumo_percentual_lucro">0,00%</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Desperdício -->
                                <div class="mb-3">
                                    <h6 class="text-secondary mb-2">Desperdício</h6>
                                    <div class="row">
                                        <div class="col-4">
                                            <div class="text-center">
                                                <small class="text-muted d-block">Peso Desperdício</small>
                                                <div class="fw-bold text-dark" id="resumo_peso_desperdicio">0,000 kg</div>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="text-center">
                                                <small class="text-muted d-block">Valor Desperdício</small>
                                                <div class="fw-bold text-dark" id="resumo_valor_desperdicio">R$ 0,00</div>
                                            </div>
                                        </div> 
                                        <div class="col-4">
                                            <div class="text-center">
                                                <small class="text-muted d-block">% Desperdício</small>
                                                <div class="fw-bold text-dark" id="resumo_percentual_desperdicio">0,00%</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Indicadores -->
                                <div class="mb-2">
                                    <h6 class="text-secondary mb-2">Indicadores</h6>
                                    <div class="row">
                                        <div class="col-4">
                                            <div class="text-center">
                                                <small class="text-muted d-block">Custo Final</small>
                                                <div class="fw-bold text-dark" id="resumo_custo_final">R$ 0,00</div>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="text-center">
                                                <small class="text-muted d-block">Venda Médio</small>
                                                <div class="fw-bold text-dark" id="resumo_venda_medio">R$ 0,00</div>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="text-center">
                                                <small class="text-muted d-block">Cortes Classificados (- desperdício)</small>
                                                <div class="fw-bold text-dark" id="resumo_cortes_classificados_sem_desperdicio">0</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Botão de Relatório HTML -->
                    <div class="row mt-4">
                        <div class="col-12 text-center">
                            <button type="button" class="btn btn-primary btn-lg" onclick="gerarRelatorioHTML()">
                                <i class="fas fa-file-alt me-2"></i>
                                Gerar Relatório HTML
                            </button>
                        </div>
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
                    <i class="fas fa-question-circle me-2"></i>
                    Ajuda - Classificação de Desossa
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <h6>Como usar:</h6>
                <ol>
                    <li><strong>Dados do Boi:</strong> Preencha as informações básicas do boi (identificação, peso, preço)</li>
                    <li><strong>Importação XML:</strong> Você pode importar dados de nota fiscal XML</li>
                    <li><strong>Classificação:</strong> Adicione manualmente cada corte ou importe via arquivo TXT</li>
                    <li><strong>Formato TXT:</strong> Use o formato: CORTE|PESO|PRECO_VENDA (um por linha)</li>
                    <li><strong>Cálculo:</strong> O sistema calculará automaticamente rendimentos e margens</li>
                </ol>
                
                <h6>Tipos de Corte:</h6>
                <ul>
                    <li><strong>Carnes Nobres:</strong> Picanha, Alcatra, Maminha, Coxão Mole</li>
                    <li><strong>Carnes Secundárias:</strong> Patinho, Contra Filé, Filé Mignon</li>
                    <li><strong>Desperdícios:</strong> Osso, Sebo, Pelanca</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Seleção de Produto -->
<div class="modal fade" id="produtoModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-list me-2"></i>
                    Selecionar Produto para Classificação
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-3">Foram encontrados múltiplos produtos no XML. Selecione qual produto deseja classificar:</p>
                
                <div id="listaProdutos" class="row">
                    <!-- Produtos serão inseridos dinamicamente -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Edição de Corte -->
<div class="modal fade" id="editarCorteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-edit text-primary me-2"></i>
                    Editar Corte
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formEditarCorte">
                    <input type="hidden" id="edit_corte_id">
                    <div class="mb-3">
                        <label class="form-label">Nome do Corte</label>
                        <input type="text" class="form-control" id="edit_corte_nome" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Peso (kg)</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="edit_corte_peso" placeholder="0,000" required>
                            <span class="input-group-text">kg</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Preço por kg</label>
                        <div class="input-group">
                            <span class="input-group-text">R$</span>
                            <input type="text" class="form-control" id="edit_corte_preco" placeholder="0,00" required>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnSalvarEdicao">
                    <i class="fas fa-save me-2"></i>
                    Salvar Alterações
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Inserção de Descarte -->
<div class="modal fade" id="modalDescarte" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-trash text-warning me-2"></i>
                    Inserção de Descarte
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Instrução:</strong> Insira o peso dos itens que serão descartados (osso, sebo e pelanca). Estes itens aparecem em todos os grupos, exceto "Miúdos e Subprodutos".
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Peso do Osso (kg)</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="peso_osso" name="peso_osso" placeholder="0,000" required>
                            <span class="input-group-text">kg</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Peso do Sebo (kg)</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="peso_sebo" name="peso_sebo" placeholder="0,000" required>
                            <span class="input-group-text">kg</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Peso da Pelanca (kg)</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="peso_pelanca" name="peso_pelanca" placeholder="0,000" required>
                            <span class="input-group-text">kg</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnConfirmarDescarte">
                    <i class="fas fa-check me-2"></i>
                    Confirmar Descarte
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Aviso Etiqueta -->
<div class="modal fade" id="avisoEtiquetaModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                    Aviso - Modo Etiqueta
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Importante:</strong> Este modo aceita apenas etiquetas por peso, não por preço.
                </div>
                
                <h6>Formato do Código de Barras:</h6>
                <div class="bg-light p-3 rounded">
                    <code>2069600017009</code>
                    <ul class="mt-2 mb-0">
                        <li><strong>Dígito 1:</strong> Código de balança (sempre começa com 2)</li>
                        <li><strong>Dígitos 2-7:</strong> Código do produto</li>
                        <li><strong>Dígitos 8-12:</strong> Peso do produto em gramas</li>
                        <li><strong>Dígito 13:</strong> Dígito verificador</li>
                    </ul>
                    <p class="mt-2 mb-0"><small><strong>Exemplo:</strong> 17009 = 170,09 gramas = 0,170 kg</small></p>
                </div>
                
                <p class="mt-3 mb-0">
                    Após confirmar, você poderá escanear as etiquetas e o sistema avançará automaticamente para o próximo corte.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnConfirmarEtiqueta">
                    <i class="fas fa-check me-2"></i>
                    Entendi, Continuar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM carregado, iniciando configuração...');
    
    // Inicializar máscaras
    if (typeof IMask !== 'undefined') {
        console.log('Configurando máscaras IMask...');
        
        IMask(document.getElementById('peso_total'), {
            mask: Number,
            scale: 2,
            thousandsSeparator: '.',
            radix: ',',
            mapToRadix: ['.']
        });
        
        IMask(document.getElementById('preco_kg'), {
            mask: Number,
            scale: 2,
            thousandsSeparator: '.',
            radix: ',',
            mapToRadix: ['.']
        });
        
        IMask(document.getElementById('peso_corte_manual'), {
            mask: /^\d*$/,
            lazy: false,
            placeholderChar: '0'
        });
        
        // Adicionar evento para formatação automática do peso
        document.getElementById('peso_corte_manual').addEventListener('blur', function() {
            formatarPeso(this);
        });
        
        // Adicionar evento para formatação ao pressionar Enter
        document.getElementById('peso_corte_manual').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                formatarPeso(this);
            }
        });
        
        // Função para formatar peso
        function formatarPeso(elemento) {
            const value = elemento.value.trim();
            if (value && /^\d+$/.test(value)) {
                const numValue = parseInt(value);
                const formattedValue = (numValue / 1000).toFixed(3).replace('.', ',');
                elemento.value = formattedValue;
            }
        }
        
        IMask(document.getElementById('edit_corte_peso'), {
            mask: /^\d*$/,
            lazy: false,
            placeholderChar: '0'
        });
        
        // Adicionar evento para formatação automática do peso na edição
        document.getElementById('edit_corte_peso').addEventListener('blur', function() {
            formatarPeso(this);
        });
        
        // Adicionar evento para formatação ao pressionar Enter
        document.getElementById('edit_corte_peso').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                formatarPeso(this);
            }
        });
        
        IMask(document.getElementById('edit_corte_preco'), {
            mask: /^\d*$/,
            lazy: false,
            placeholderChar: '0'
        });
        
        // Adicionar evento para formatação automática do preço na edição
        document.getElementById('edit_corte_preco').addEventListener('blur', function() {
            formatarPreco(this);
        });
        
        // Adicionar evento para formatação ao pressionar Enter
        document.getElementById('edit_corte_preco').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                formatarPreco(this);
            }
        });
        
        IMask(document.getElementById('preco_corte_manual'), {
            mask: /^\d*$/,
            lazy: false,
            placeholderChar: '0'
        });
        
        // Adicionar evento para formatação automática do preço
        document.getElementById('preco_corte_manual').addEventListener('blur', function() {
            formatarPreco(this);
        });
        
        // Adicionar evento para formatação ao pressionar Enter
        document.getElementById('preco_corte_manual').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                formatarPreco(this);
            }
        });
        
        // Função para formatar preço
        function formatarPreco(elemento) {
            const value = elemento.value.trim();
            if (value && /^\d+$/.test(value)) {
                const numValue = parseInt(value);
                const formattedValue = (numValue / 100).toFixed(2).replace('.', ',');
                elemento.value = formattedValue;
            }
        }
        
        IMask(document.getElementById('preco_corte_etiqueta'), {
            mask: /^\d*$/,
            lazy: false,
            placeholderChar: '0'
        });
        
        // Adicionar evento para formatação automática do preço da etiqueta
        document.getElementById('preco_corte_etiqueta').addEventListener('blur', function() {
            formatarPreco(this);
        });
        
        // Adicionar evento para formatação ao pressionar Enter
        document.getElementById('preco_corte_etiqueta').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                formatarPreco(this);
            }
        });
        
        // Máscaras para campos de descarte
        IMask(document.getElementById('peso_osso'), {
            mask: /^\d*$/,
            lazy: false,
            placeholderChar: '0'
        });
        
        // Adicionar evento para formatação automática do peso do osso
        document.getElementById('peso_osso').addEventListener('blur', function() {
            formatarPeso(this);
        });
        
        // Adicionar evento para formatação ao pressionar Enter
        document.getElementById('peso_osso').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                formatarPeso(this);
            }
        });
        
        IMask(document.getElementById('peso_sebo'), {
            mask: /^\d*$/,
            lazy: false,
            placeholderChar: '0'
        });
        
        // Adicionar evento para formatação automática do peso do sebo
        document.getElementById('peso_sebo').addEventListener('blur', function() {
            formatarPeso(this);
        });
        
        // Adicionar evento para formatação ao pressionar Enter
        document.getElementById('peso_sebo').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                formatarPeso(this);
            }
        });
        
        IMask(document.getElementById('peso_pelanca'), {
            mask: /^\d*$/,
            lazy: false,
            placeholderChar: '0'
        });
        
        // Adicionar evento para formatação automática do peso da pelanca
        document.getElementById('peso_pelanca').addEventListener('blur', function() {
            formatarPeso(this);
        });
        
        // Adicionar evento para formatação ao pressionar Enter
        document.getElementById('peso_pelanca').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                formatarPeso(this);
            }
        });
        
        console.log('Máscaras configuradas com sucesso');
    } else {
        console.warn('IMask não está disponível');
    }
    
    // Calcular custo total automaticamente
    console.log('Configurando listeners para cálculo automático...');
    document.getElementById('peso_total').addEventListener('input', () => {
        marcarDigitacao();
        calcularCustoTotal();
    });
    document.getElementById('preco_kg').addEventListener('input', () => {
        marcarDigitacao();
        calcularCustoTotal();
    });
    
    // Upload de arquivos
    console.log('Configurando upload de arquivos...');
    setupFileUpload('uploadXml', 'xmlFile');
    setupFileUpload('uploadTxt', 'txtFile');
    
    // Modos de inserção
    console.log('Configurando modos de inserção...');
    setupModosInsercao();
    
    // Formulários
    console.log('Configurando formulários...');
    document.getElementById('formBoi').addEventListener('submit', salvarDadosBoi);
    document.getElementById('formCarnes').addEventListener('submit', calcularRendimento);
    
    // Inicializar dados
    console.log('Chamando inicialização de dados...');
    inicializarDados();
    
    console.log('Configuração concluída');
});

// Variável para controlar o timeout de validação
let timeoutValidacao = null;
let usuarioDigitando = false;
let preenchimentoManual = false;

function calcularCustoTotal() {
    // Verificar se os campos existem antes de acessá-los
    const campoPeso = document.getElementById('peso_total');
    const campoPreco = document.getElementById('preco_kg');
    const campoCusto = document.getElementById('custo_total');
    
    if (!campoPeso || !campoPreco || !campoCusto) {
        console.error('❌ Campos necessários não encontrados para cálculo do custo');
        return;
    }
    
    const pesoFormatado = campoPeso.value;
    const precoFormatado = campoPreco.value;
    
    console.log('Valores para cálculo:', { pesoFormatado, precoFormatado });
    
    const peso = parseFloat(pesoFormatado.replace(/\./g, '').replace(',', '.')) || 0;
    const preco = parseFloat(precoFormatado.replace(/\./g, '').replace(',', '.'));
    const custo = peso * preco;
    
    campoCusto.value = custo.toFixed(2).replace('.', ',');
    
    console.log('Custo calculado:', custo, 'Preenchimento manual:', preenchimentoManual);
    
    // Se for preenchimento manual, mostrar botão de salvar
    if (preenchimentoManual) {
        console.log('Preenchimento manual detectado - mostrando botões de ação');
        mostrarBotaoSalvar();
    }
    
    // Se não for preenchimento manual (XML), não mostrar botões
    if (!preenchimentoManual) {
        console.log('Preenchimento via XML - ocultando botões de ação');
        const botoesAcao = document.getElementById('botoesAcaoDadosBrutos');
        if (botoesAcao) {
            botoesAcao.style.display = 'none';
        }
    }
    
    // IMPORTANTE: Em preenchimento manual, NÃO executar validação automática
    // A validação só deve executar quando o usuário clicar em "Salvar e Continuar"
    console.log('Validação automática BLOQUEADA - aguardando ação do usuário');
}

// Função para marcar que o usuário está digitando
function marcarDigitacao() {
    usuarioDigitando = true;
    preenchimentoManual = true;
    
    // Limpar timeout anterior se existir
    if (timeoutValidacao) {
        clearTimeout(timeoutValidacao);
    }
    
    // Marcar que parou de digitar após 1.5 segundos
    // IMPORTANTE: NÃO executar validação automática
    timeoutValidacao = setTimeout(() => {
        usuarioDigitando = false;
        // NÃO chamar verificarDadosBrutos() aqui
        console.log('Usuário parou de digitar - aguardando ação manual');
    }, 1500);
}

// Função para mostrar os botões de ação
function mostrarBotaoSalvar() {
    const botoesAcao = document.getElementById('botoesAcaoDadosBrutos');
    if (botoesAcao) {
        botoesAcao.style.display = 'block';
        console.log('Botões de ação exibidos - aguardando usuário clicar em "Salvar e Continuar"');
    } else {
        console.error('Container de botões não encontrado');
    }
}

// Função para salvar dados brutos e continuar
function salvarDadosBrutos() {
    console.log('=== INÍCIO DA FUNÇÃO SALVAR DADOS BRUTOS ===');
    console.log('Função salvarDadosBrutos() executada pelo usuário');
    console.log('Estado atual - preenchimentoManual:', preenchimentoManual);
    
    // Verificar cada campo individualmente para debug
    const campos = {
        identificacao: document.getElementById('identificacao_boi')?.value?.trim(),
        dataAbate: document.getElementById('data_abate')?.value?.trim(),
        pesoTotal: document.getElementById('peso_total')?.value?.trim(),
        precoKg: document.getElementById('preco_kg')?.value?.trim()
    };
    
    console.log('Valores dos campos antes da validação:', campos);
    
    if (verificarDadosBrutos()) {
        console.log('✅ Validação bem-sucedida - avançando para próxima etapa');
        
        // Ocultar botões de ação
        const botoesAcao = document.getElementById('botoesAcaoDadosBrutos');
        if (botoesAcao) {
            botoesAcao.style.display = 'none';
        }
        
        // Mostrar próxima etapa
        document.getElementById('etapaGruposCortes').style.display = 'block';
        console.log('Dados brutos salvos e próxima etapa exibida');
        
        // Recolher automaticamente a seção de Dados Brutos
        recolherSecaoDadosBrutos();
    } else {
        console.log('❌ Validação falhou - campos incompletos ou inválidos');
        
        // Mostrar detalhes dos campos que falharam
        let mensagemErro = 'Campos que precisam ser corrigidos:\n\n';
        
        if (!campos.identificacao) mensagemErro += '• Identificação\n';
        if (!campos.dataAbate) mensagemErro += '• Data de Abate\n';
        if (!campos.pesoTotal) mensagemErro += '• Peso Total\n';
        if (!campos.precoKg) mensagemErro += '• Preço por kg\n';
        
        // Verificar valores numéricos
        if (campos.pesoTotal) {
            const peso = parseFloat(campos.pesoTotal.replace(/\./g, '').replace(',', '.'));
            if (isNaN(peso) || peso <= 0) {
                mensagemErro += '• Peso Total deve ser um número maior que zero\n';
            }
        }
        
        if (campos.precoKg) {
            const preco = parseFloat(campos.precoKg.replace(/\./g, '').replace(',', '.'));
            if (isNaN(preco) || preco <= 0) {
                mensagemErro += '• Preço por kg deve ser um número maior que zero\n';
            }
        }
        
        console.log('Mensagem de erro detalhada:', mensagemErro);
        alert(mensagemErro);
    }
    
    console.log('=== FIM DA FUNÇÃO SALVAR DADOS BRUTOS ===');
}

// Função de teste removida - não é mais necessária após correção da validação

// Função para recolher automaticamente a seção de Dados Brutos
function recolherSecaoDadosBrutos() {
    console.log('Recolhendo seção de Dados Brutos automaticamente...');
    
    // Recolher o corpo da seção
    const bodyDadosBoi = document.getElementById('bodyDadosBoi');
    if (bodyDadosBoi) {
        bodyDadosBoi.style.display = 'none';
    }
    
    // Atualizar o ícone do botão toggle
    const iconDadosBoi = document.getElementById('iconDadosBoi');
    if (iconDadosBoi) {
        iconDadosBoi.className = 'fas fa-chevron-down';
    }
    
    // Atualizar o estado do botão toggle
    const btnToggleDadosBoi = document.getElementById('btnToggleDadosBoi');
    if (btnToggleDadosBoi) {
        btnToggleDadosBoi.setAttribute('data-expanded', 'false');
        btnToggleDadosBoi.title = 'Expandir seção';
    }
    
    console.log('Seção de Dados Brutos recolhida automaticamente');
}

// Função para limpar campos e resetar estado
function limparCamposDadosBrutos() {
    preenchimentoManual = false;
    
    // Ocultar botões de ação
    const botoesAcao = document.getElementById('botoesAcaoDadosBrutos');
    if (botoesAcao) {
        botoesAcao.style.display = 'none';
    }
    
    // Ocultar próximas etapas
    document.getElementById('etapaGruposCortes').style.display = 'none';
    document.getElementById('etapaClassificacao').style.display = 'none';
    
    // Limpar campos do formulário
    document.getElementById('formBoi').reset();
    
    console.log('Campos limpos e estado resetado');
}

function setupFileUpload(uploadAreaId, fileInputId) {
    const uploadArea = document.getElementById(uploadAreaId);
    const fileInput = document.getElementById(fileInputId);
    
    uploadArea.addEventListener('click', () => fileInput.click());
    
    uploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadArea.classList.add('dragover');
    });
    
    uploadArea.addEventListener('dragleave', () => {
        uploadArea.classList.remove('dragover');
    });
    
    uploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadArea.classList.remove('dragover');
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            fileInput.files = files;
            processarArquivo(files[0], fileInputId);
        }
    });
    
    fileInput.addEventListener('change', (e) => {
        if (e.target.files.length > 0) {
            processarArquivo(e.target.files[0], fileInputId);
        }
    });
}

function processarArquivo(file, tipo) {
    if (tipo === 'xmlFile') {
        processarXML(file);
    } else if (tipo === 'txtFile') {
        processarTXT(file);
    }
}

function processarXML(file) {
    const reader = new FileReader();
    reader.onload = function(e) {
        try {
            const xmlContent = e.target.result;
            const parser = new DOMParser();
            const xmlDoc = parser.parseFromString(xmlContent, "text/xml");
            
            // Resetar estado de preenchimento manual
            preenchimentoManual = false;
            
            // Ocultar botões de ação se estiverem visíveis
            const botoesAcao = document.getElementById('botoesAcaoDadosBrutos');
            if (botoesAcao) {
                botoesAcao.style.display = 'none';
            }
            
            // Extrair dados da nota fiscal
            const dados = extrairDadosXML(xmlDoc);
            
            if (dados) {
                // Preencher os campos automaticamente
                preencherCamposBoi(dados);
                mostrarStatusXML('Dados XML carregados com sucesso!', 'success');
            } else {
                mostrarStatusXML('Erro ao processar XML: Estrutura não reconhecida', 'danger');
            }
        } catch (error) {
            console.error('Erro ao processar XML:', error);
            mostrarStatusXML('Erro ao processar arquivo XML: ' + error.message, 'danger');
        }
    };
    reader.readAsText(file);
}

function extrairDadosXML(xmlDoc) {
    try {
        // Buscar todos os produtos no XML
        const produtos = xmlDoc.querySelectorAll('det');
        
        if (produtos.length === 0) {
            console.warn('Nenhum produto encontrado no XML');
            return null;
        }
        
        // Se houver apenas um produto, processar diretamente
        if (produtos.length === 1) {
            return extrairProdutoUnico(produtos[0], xmlDoc);
        }
        
        // Se houver múltiplos produtos, extrair todos e mostrar modal
        const listaProdutos = [];
        produtos.forEach((produto, index) => {
            const dados = extrairProdutoUnico(produto, xmlDoc);
            if (dados) {
                dados.index = index;
                listaProdutos.push(dados);
            }
        });
        
        if (listaProdutos.length > 0) {
            mostrarModalSelecaoProduto(listaProdutos);
            return null; // Retorna null para não preencher automaticamente
        }
        
        return null;
    } catch (error) {
        console.error('Erro ao extrair dados XML:', error);
        return null;
    }
}

function extrairProdutoUnico(produto, xmlDoc) {
    try {
        // Extrair dados do produto específico
        const xProd = produto.querySelector('xProd');
        const qCom = produto.querySelector('qCom');
        const vUnTrib = produto.querySelector('vUnTrib');
        const vProd = produto.querySelector('vProd');
        
        // Extrair dados de emissão - tentar diferentes seletores
        const dhEmi = xmlDoc.querySelector('dhEmi') || xmlDoc.querySelector('ide dhEmi');
        
        // Extrair dados do emitente (fornecedor) - tentar diferentes seletores
        const xNome = xmlDoc.querySelector('xNome') || xmlDoc.querySelector('emit xNome');
        
        // Verificar se encontrou pelo menos os campos essenciais
        if (!xProd || !qCom || !vUnTrib || !vProd) {
            console.warn('Campos essenciais não encontrados no produto');
            return null;
        }
        
        return {
            identificacao: xProd.textContent.trim(),
            pesoTotal: parseFloat(qCom.textContent) || 0,
            precoKg: parseFloat(vUnTrib.textContent) || 0,
            custoTotal: parseFloat(vProd.textContent) || 0,
            dataEmissao: dhEmi ? dhEmi.textContent.trim() : '',
            fornecedor: xNome ? xNome.textContent.trim() : ''
        };
    } catch (error) {
        console.error('Erro ao extrair produto único:', error);
        return null;
    }
}

function preencherCamposBoi(dados) {
    console.log('Preenchendo campos com dados:', dados);
    
    // Preencher identificação do boi
    if (dados.identificacao) {
        document.getElementById('identificacao_boi').value = dados.identificacao;
        console.log('Identificação preenchida:', dados.identificacao);
    }
    
    // Preencher data de abate (extrair apenas a data da string ISO)
    if (dados.dataEmissao) {
        try {
            const dataISO = dados.dataEmissao;
            const data = new Date(dataISO);
            if (!isNaN(data.getTime())) {
                const dataFormatada = data.toISOString().split('T')[0];
                document.getElementById('data_abate').value = dataFormatada;
                console.log('Data preenchida:', dataFormatada);
            } else {
                console.warn('Data inválida:', dados.dataEmissao);
            }
        } catch (error) {
            console.error('Erro ao processar data:', error);
        }
    }
    
    // Preencher peso total
    if (dados.pesoTotal > 0) {
        const pesoFormatado = dados.pesoTotal.toFixed(2).replace('.', ',');
        document.getElementById('peso_total').value = pesoFormatado;
        console.log('Peso preenchido:', pesoFormatado);
    }
    
    // Preencher preço por kg
    if (dados.precoKg > 0) {
        const precoFormatado = dados.precoKg.toFixed(2).replace('.', ',');
        document.getElementById('preco_kg').value = precoFormatado;
        console.log('Preço preenchido:', precoFormatado);
    }
    
    // Preencher custo total
    if (dados.custoTotal > 0) {
        const custoFormatado = dados.custoTotal.toFixed(2).replace('.', ',');
        document.getElementById('custo_total').value = custoFormatado;
        console.log('Custo preenchido:', custoFormatado);
    }
    
    // Preencher fornecedor
    if (dados.fornecedor) {
        document.getElementById('fornecedor').value = dados.fornecedor;
        console.log('Fornecedor preenchido:', dados.fornecedor);
    }
    
    // Recalcular custo total se necessário
    if (dados.pesoTotal > 0 && dados.precoKg > 0) {
        console.log('Recalculando custo total...');
        calcularCustoTotal();
    }
    
    // Verificar se todos os dados brutos estão preenchidos para avançar ao próximo fluxo
    setTimeout(() => {
        // Se for XML, avançar automaticamente (não é preenchimento manual)
        if (verificarDadosBrutos()) {
            document.getElementById('etapaGruposCortes').style.display = 'block';
            console.log('XML processado - avançando automaticamente para próxima etapa');
            
            // Recolher automaticamente a seção de Dados Brutos
            recolherSecaoDadosBrutos();
        }
    }, 100);
    
    console.log('Campos preenchidos com sucesso!');
}

function mostrarStatusXML(mensagem, tipo) {
    const statusDiv = document.getElementById('xmlStatus');
    const statusText = document.getElementById('xmlStatusText');
    const alertDiv = statusDiv.querySelector('.alert');
    
    // Atualizar mensagem
    statusText.textContent = mensagem;
    
    // Atualizar classe de cor
    alertDiv.className = `alert alert-${tipo} alert-sm`;
    
    // Atualizar ícone
    const icon = alertDiv.querySelector('i');
    if (tipo === 'success') {
        icon.className = 'fas fa-check-circle me-2';
    } else {
        icon.className = 'fas fa-exclamation-triangle me-2';
    }
    
    // Mostrar status
    statusDiv.style.display = 'block';
    
    // Ocultar após 5 segundos
    setTimeout(() => {
        statusDiv.style.display = 'none';
    }, 5000);
}

function mostrarModalSelecaoProduto(produtos) {
    const listaProdutos = document.getElementById('listaProdutos');
    listaProdutos.innerHTML = '';
    
    produtos.forEach((produto, index) => {
        const produtoCard = document.createElement('div');
        produtoCard.className = 'col-md-6 mb-3';
        produtoCard.innerHTML = `
            <div class="card h-100 cursor-pointer produto-card" data-index="${index}">
                <div class="card-body">
                    <h6 class="card-title text-primary">
                        <i class="fas fa-cow me-2"></i>
                        ${produto.identificacao}
                    </h6>
                    <div class="row">
                        <div class="col-6">
                            <small class="text-muted">Peso:</small>
                            <p class="mb-1"><strong>${produto.pesoTotal.toFixed(2)} kg</strong></p>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Preço/kg:</small>
                            <p class="mb-1"><strong>R$ ${produto.precoKg.toFixed(2)}</strong></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <small class="text-muted">Custo Total:</small>
                            <p class="mb-1"><strong>R$ ${produto.custoTotal.toFixed(2)}</strong></p>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Fornecedor:</small>
                            <p class="mb-1"><strong>${produto.fornecedor || 'N/A'}</strong></p>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Adicionar evento de clique
        produtoCard.querySelector('.produto-card').addEventListener('click', () => {
            selecionarProduto(produto);
        });
        
        listaProdutos.appendChild(produtoCard);
    });
    
    // Mostrar modal
    const modal = new bootstrap.Modal(document.getElementById('produtoModal'));
    modal.show();
}

function selecionarProduto(produto) {
    // Preencher os campos com o produto selecionado
    preencherCamposBoi(produto);
    
    // Mostrar status de sucesso
    mostrarStatusXML(`Produto "${produto.identificacao}" selecionado com sucesso!`, 'success');
    
    // Fechar modal
    const modal = bootstrap.Modal.getInstance(document.getElementById('produtoModal'));
    modal.hide();
}

// Estrutura de grupos de cortes
const gruposCortes = {
    'regiaoDianteira': {
        nome: 'Região Dianteira (Carne de segunda)',
        cortes: [
            'Acém Com Osso', 'Acém Sem Osso', 'Capa do Acém', 'Costela', 'Cruz Machado Com Osso', 'Cruz Machado Sem Osso',
            'Cupim', 'Miolo da Paleta', 'Músculo Dianteiro', 'Osso de Perú', 'Paleta',
            'Peito Com Osso', 'Peito Sem Osso', 'Pescoço', 'Ponta de Agulha', 'Ponta Dianteira'
        ]
    },
    'regiaoTraseira': {
        nome: 'Região Traseira (Carne de primeira)',
        cortes: [
            'Alcatra', 'Baby Beef', 'Bombom da Alcatra', 'Capote','Contrafilé',
            'Coxão Duro', 'Coxão Mole', 'Chã de Dentro', 'Chã de Fora', 'Filé Mignon', 'Filé Especial', 'Fraldinha',
            'Lagarto', 'Paulista', 'Maminha', 'Miolo da Alcatra', 'Músculo Traseiro', 'Osso Patinho', 'Patinho',
            'Picanha', 'Ponta de Alcatra', 'Ponta Traseira'
        ]
    },
    'regiaoCostela': {
        nome: 'Região Costela e Entorno',
        cortes: [
            'Bananinha', 'Chupa-molho', 'Costela Minga', 'Costela Ripa',
            'Costelão Gaúcho', 'Peito Entremeado'
        ]
    },
    'regiaoAbaFlanco': {
        nome: 'Região da Aba e do Flanco',
        cortes: [
            'Entranha', 'Flanco', 'Matambre', 'Paella Beef', 'Vazio'
        ]
    },
    'cortesEspeciais': {
        nome: 'Cortes Especiais para Churrasco',
        cortes: [
            'Ancho', 'Chorizo', 'Costela Fogo de Chão', 'Cupim Grill', 'Denver Steak', 'Ossobuco', 'Prime Rib',
            'Short Ribs', 'Tomahawk'
        ]
    },
    'miudosSubprodutos': {
        nome: 'Miúdos e Subprodutos',
        cortes: [
            'Bochecha', 'Bucho', 'Baço', 'Bofe', 'Coração', 'Fato Livro', 'Fígado',
            'Língua', 'Mocotó', 'Osso Para Sopa', 'Rabo', 'Rim', 'Testiculos', 'Tripa'
        ]
    }
};

// Variáveis globais para classificação
let cortesDisponiveis = [];
let cortesClassificados = [];
let corteAtualIndex = 0;
let modoAtivo = null;

function setupModosInsercao() {
    // Event listeners para os modos de inserção
    document.querySelectorAll('input[name="modoInsercao"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const modo = this.value;
            mostrarModo(modo);
        });
    });
    
    // Event listeners para campos específicos
    document.getElementById('peso_corte_manual').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            processarPesoManual();
        }
    });
    
    document.getElementById('preco_corte_manual').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            finalizarInserçãoManual();
        }
    });
    
    document.getElementById('codigo_barras').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            processarCodigoBarras();
        }
    });
    
    document.getElementById('preco_corte_etiqueta').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            finalizarInserçãoEtiqueta();
        }
    });
    
    // Botão de confirmação do modal de etiqueta
    document.getElementById('btnConfirmarEtiqueta').addEventListener('click', function() {
        const modal = bootstrap.Modal.getInstance(document.getElementById('avisoEtiquetaModal'));
        modal.hide();
        ativarModoEtiqueta();
    });
    
    // Botão de salvar edição do corte
    document.getElementById('btnSalvarEdicao').addEventListener('click', salvarEdicaoCorte);
}

function mostrarModo(modo) {
    // Verificar se os grupos foram selecionados
    if (cortesDisponiveis.length === 0) {
        alert('Por favor, selecione os grupos de cortes primeiro!');
        return;
    }
    
    // Ocultar todos os containers
    document.querySelectorAll('.modo-container').forEach(container => {
        container.style.display = 'none';
    });
    
    // Mostrar o container selecionado
    if (modo === 'txt') {
        document.getElementById('modoTxtContainer').style.display = 'block';
        modoAtivo = 'txt';
    } else if (modo === 'manual') {
        document.getElementById('modoManualContainer').style.display = 'block';
        modoAtivo = 'manual';
        ativarModoManual();
    } else if (modo === 'etiqueta') {
        document.getElementById('modoEtiquetaContainer').style.display = 'block';
        modoAtivo = 'etiqueta';
        mostrarModalAvisoEtiqueta();
    }
    
    // Mostrar lista de cortes se houver dados
    if (cortesClassificados.length > 0) {
        document.getElementById('listaCortesContainer').style.display = 'block';
        document.getElementById('btnCalcularRendimento').style.display = 'inline-block';
    }
}

function mostrarModalAvisoEtiqueta() {
    const modal = new bootstrap.Modal(document.getElementById('avisoEtiquetaModal'));
    modal.show();
}

function ativarModoManual() {
    if (corteAtualIndex < cortesDisponiveis.length) {
        document.getElementById('corte_atual').value = cortesDisponiveis[corteAtualIndex];
        document.getElementById('numeroCorte').textContent = corteAtualIndex + 1;
        document.getElementById('peso_corte_manual').focus();
        // Limpar campos anteriores
        document.getElementById('peso_corte_manual').value = '';
        document.getElementById('preco_corte_manual').value = '';
        // Limpar peso temporário se existir
        if (window.pesoTempManual) {
            window.pesoTempManual = 0;
        }
    } else {
        alert('Todos os cortes foram classificados!');
    }
}

function ativarModoEtiqueta() {
    if (corteAtualIndex < cortesDisponiveis.length) {
        document.getElementById('corte_atual_etiqueta').value = cortesDisponiveis[corteAtualIndex];
        document.getElementById('numeroCorteEtiqueta').textContent = corteAtualIndex + 1;
        document.getElementById('codigo_barras').focus();
        // Limpar campos anteriores
        document.getElementById('codigo_barras').value = '';
        document.getElementById('preco_corte_etiqueta').value = '';
        // Limpar peso temporário se existir
        if (window.pesoTempEtiqueta) {
            window.pesoTempEtiqueta = 0;
        }
    } else {
        alert('Todos os cortes foram classificados!');
    }
}

function processarPesoManual() {
    // Obter o valor formatado do campo
    const valorFormatado = document.getElementById('peso_corte_manual').value;
    
    // Converter corretamente: remover separadores de milhares e converter vírgula para ponto
    const peso = parseFloat(valorFormatado.replace(/\./g, '').replace(',', '.')) || 0;
    
    if (peso >= 0) {
        // Focar no campo de preço para continuar o fluxo
        document.getElementById('preco_corte_manual').focus();
    } else {
        alert('Por favor, insira um peso válido (0 ou maior).');
    }
}

function finalizarInserçãoManual() {
    // Obter valores formatados dos campos
    const pesoFormatado = document.getElementById('peso_corte_manual').value;
    const precoFormatado = document.getElementById('preco_corte_manual').value;
    
    // Converter corretamente: remover separadores de milhares e converter vírgula para ponto
    const peso = parseFloat(pesoFormatado.replace(/\./g, '').replace(',', '.')) || 0;
    const preco = parseFloat(precoFormatado.replace(/\./g, '').replace(',', '.')) || 0;
    
    if (peso > 0) {
        adicionarCorteClassificado(cortesDisponiveis[corteAtualIndex], peso, preco);
        // Limpar campos e avançar para próximo corte
        document.getElementById('peso_corte_manual').value = '';
        document.getElementById('preco_corte_manual').value = '';
        avancarParaProximoCorte();
    } else if (peso === 0) {
        // Pular corte (peso zero)
        document.getElementById('peso_corte_manual').value = '';
        document.getElementById('preco_corte_manual').value = '';
        avancarParaProximoCorte();
    } else {
        alert('Por favor, insira um peso válido (0 ou maior).');
    }
}

function finalizarInserçãoEtiqueta() {
    const peso = window.pesoTempEtiqueta || 0;
    
    // Obter valor formatado do campo de preço
    const precoFormatado = document.getElementById('preco_corte_etiqueta').value;
    
    // Converter corretamente: remover separadores de milhares e converter vírgula para ponto
    const preco = parseFloat(precoFormatado.replace(/\./g, '').replace(',', '.')) || 0;
    
    if (peso > 0) {
        adicionarCorteClassificado(cortesDisponiveis[corteAtualIndex], peso, preco);
        // Limpar campos e avançar para próximo corte
        document.getElementById('preco_corte_etiqueta').value = '';
        window.pesoTempEtiqueta = 0;
        avancarParaProximoCorte();
    } else if (peso === 0) {
        // Pular corte (peso zero)
        document.getElementById('preco_corte_etiqueta').value = '';
        window.pesoTempEtiqueta = 0;
        avancarParaProximoCorte();
    } else {
        alert('Erro: Peso não foi lido corretamente. Escaneie novamente o código de barras.');
    }
}

function processarCodigoBarras() {
    const codigo = document.getElementById('codigo_barras').value.trim();
    
    if (codigo.length === 13 && codigo.startsWith('2')) {
        const peso = extrairPesoDoCodigo(codigo);
        if (peso >= 0) {
            // Armazenar o peso temporariamente e focar no campo de preço
            window.pesoTempEtiqueta = peso;
            document.getElementById('preco_corte_etiqueta').focus();
        } else {
            alert('Erro ao extrair peso do código de barras. Verifique o formato.');
        }
    } else {
        alert('Código de barras inválido. Deve ter 13 dígitos e começar com 2.');
    }
    
    // Limpar campo de código para próximo scan
    document.getElementById('codigo_barras').value = '';
}

function extrairPesoDoCodigo(codigo) {
    try {
        // Dígitos 8-12 (posições 7-11 no array)
        const pesoStr = codigo.substring(7, 12);
        const peso = parseFloat(pesoStr) / 1000; // Converter para kg (está em gramas, não centavos)
        return peso;
    } catch (error) {
        console.error('Erro ao extrair peso:', error);
        return 0;
    }
}

function adicionarCorteClassificado(corte, peso, preco) {
    // Verificar se o corte está na lista de cortes disponíveis (exceto itens de descarte)
    const itensDescarte = ['Osso', 'Sebo', 'Pelanca'];
    if (!cortesDisponiveis.includes(corte) && !itensDescarte.includes(corte)) {
        console.warn(`Corte "${corte}" não está na lista de cortes selecionados`);
        return;
    }
    
    // Calcular custo baseado no preço por kg dos dados brutos
    const precoKgBrutoFormatado = document.getElementById('preco_kg').value;
    const precoKgBruto = parseFloat(precoKgBrutoFormatado.replace(/\./g, '').replace(',', '.')) || 0;
    const custo = peso * precoKgBruto;
    
    const corteObj = {
        id: cortesClassificados.length + 1,
        nome: corte,
        peso: peso,
        preco: parseFloat(preco.toFixed(2)), // Forçar 2 casas decimais
        custo: custo,
        totalVenda: peso * preco,
        margem: peso * preco > 0 ? ((peso * preco - custo) / (peso * preco)) * 100 : 0
    };
    
    cortesClassificados.push(corteObj);
    atualizarTabelaCortes();
    
    // Mostrar lista e botão de cálculo
    document.getElementById('listaCortesContainer').style.display = 'block';
    document.getElementById('btnCalcularRendimento').style.display = 'inline-block';
}

function avancarParaProximoCorte() {
    corteAtualIndex++;
    
    if (corteAtualIndex < cortesDisponiveis.length) {
        if (modoAtivo === 'manual') {
            ativarModoManual();
        } else if (modoAtivo === 'etiqueta') {
            ativarModoEtiqueta();
        }
    } else {
        // Todos os cortes foram classificados
        if (modoAtivo === 'manual') {
            document.getElementById('peso_corte_manual').value = '';
            document.getElementById('peso_corte_manual').disabled = true;
            document.getElementById('preco_corte_manual').value = '';
            document.getElementById('preco_corte_manual').disabled = true;
            alert('Todos os cortes foram classificados!');
        } else if (modoAtivo === 'etiqueta') {
            document.getElementById('codigo_barras').disabled = true;
            document.getElementById('preco_corte_etiqueta').disabled = true;
            alert('Todos os cortes foram classificados!');
        }
    }
}

function atualizarTabelaCortes() {
    const tbody = document.getElementById('tabela_cortes_classificados');
    tbody.innerHTML = '';
    
    const pesoTotalFormatado = document.getElementById('peso_total').value;
    const pesoTotal = parseFloat(pesoTotalFormatado.replace(/\./g, '').replace(',', '.')) || 0;
    
    cortesClassificados.forEach(corte => {
        const percentual = pesoTotal > 0 ? (corte.peso / pesoTotal) * 100 : 0;
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>
                <button type="button" class="btn btn-primary btn-sm me-1" onclick="editarCorte(${corte.id})">
                    <i class="fas fa-edit"></i>
                </button>
                <button type="button" class="btn btn-danger btn-sm" onclick="removerCorte(${corte.id})">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
            <td>${corte.nome}</td>
            <td>${corte.peso.toFixed(3)} kg</td>
            <td>${percentual.toFixed(2)}%</td>
            <td>R$ ${corte.custo.toFixed(2)}</td>
            <td>R$ ${corte.preco.toFixed(2)}</td>
            <td>R$ ${corte.totalVenda.toFixed(2)}</td>
            <td>${corte.margem.toFixed(2)}%</td>
        `;
        tbody.appendChild(row);
    });
    
    // Atualizar totais
    atualizarTotaisTabela();
}

function atualizarTotaisTabela() {
    if (cortesClassificados.length === 0) {
        // Limpar totais se não houver cortes
        document.getElementById('total_cortes_count').textContent = '0';
        document.getElementById('total_peso').textContent = '0,000 kg';
        document.getElementById('total_percentual').textContent = '0,00%';
        document.getElementById('total_custo').textContent = 'R$ 0,00';
        document.getElementById('total_venda').textContent = 'R$ 0,00';
        document.getElementById('total_margem').textContent = '0,00%';
        return;
    }
    
    // Calcular totais
    const totalPeso = cortesClassificados.reduce((sum, corte) => sum + corte.peso, 0);
    const totalCusto = cortesClassificados.reduce((sum, corte) => sum + corte.custo, 0);
    const totalVenda = cortesClassificados.reduce((sum, corte) => sum + corte.totalVenda, 0);
    
    // Calcular margem total
    const margemTotal = totalVenda > 0 ? ((totalVenda - totalCusto) / totalVenda) * 100 : 0;
    
    // Calcular percentual total (soma das porcentagens individuais)
    const pesoTotal = parseFloat(document.getElementById('peso_total').value.replace('.', '').replace(',', '.')) || 0;
    const percentualTotal = pesoTotal > 0 ? (totalPeso / pesoTotal) * 100 : 0;
    
    // Atualizar campos de totais
    document.getElementById('total_cortes_count').textContent = cortesClassificados.length;
    document.getElementById('total_peso').textContent = totalPeso.toFixed(3) + ' kg';
    document.getElementById('total_percentual').textContent = percentualTotal.toFixed(2) + '%';
    document.getElementById('total_custo').textContent = 'R$ ' + totalCusto.toFixed(2);
    document.getElementById('total_venda').textContent = 'R$ ' + totalVenda.toFixed(2);
    document.getElementById('total_margem').textContent = margemTotal.toFixed(2) + '%';
}



function editarCorte(id) {
    const corte = cortesClassificados.find(c => c.id === id);
    if (corte) {
        // Preencher o modal com os dados do corte
        document.getElementById('edit_corte_id').value = corte.id;
        document.getElementById('edit_corte_nome').value = corte.nome;
        document.getElementById('edit_corte_peso').value = corte.peso.toFixed(3).replace('.', ',');
        document.getElementById('edit_corte_preco').value = corte.preco.toFixed(2).replace('.', ',');
        
        // Mostrar o modal
        const modal = new bootstrap.Modal(document.getElementById('editarCorteModal'));
        modal.show();
    }
}

function salvarEdicaoCorte() {
    const id = parseInt(document.getElementById('edit_corte_id').value);
    
    const pesoFormatado = document.getElementById('edit_corte_peso').value;
    const precoFormatado = document.getElementById('edit_corte_preco').value;
    
    const peso = parseFloat(pesoFormatado.replace(/\./g, '').replace(',', '.')) || 0;
    const preco = parseFloat(precoFormatado.replace(/\./g, '').replace(',', '.')) || 0;
    
    if (peso < 0 || preco < 0) {
        alert('Por favor, insira valores válidos (maiores ou iguais a zero).');
        return;
    }
    
    const index = cortesClassificados.findIndex(corte => corte.id === id);
    if (index > -1) {
        // Recalcular custo baseado no preço por kg dos dados brutos
        const precoKgBrutoFormatado = document.getElementById('preco_kg').value;
        const precoKgBruto = parseFloat(precoKgBrutoFormatado.replace(/\./g, '').replace(',', '.')) || 0;
        const custo = peso * precoKgBruto;
        
        cortesClassificados[index].peso = peso;
        cortesClassificados[index].preco = parseFloat(preco.toFixed(2)); // Forçar 2 casas decimais
        cortesClassificados[index].custo = custo;
        cortesClassificados[index].totalVenda = peso * preco;
        cortesClassificados[index].margem = peso * preco > 0 ? ((peso * preco - custo) / (peso * preco)) * 100 : 0;
        
        // Atualizar a tabela
        atualizarTabelaCortes();
        
        // Fechar o modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('editarCorteModal'));
        modal.hide();
        
        // Mostrar mensagem de sucesso
        alert('Corte atualizado com sucesso!');
    }
}

function removerCorte(id) {
    const index = cortesClassificados.findIndex(corte => corte.id === id);
    if (index > -1) {
        cortesClassificados.splice(index, 1);
        // Reajustar IDs
        cortesClassificados.forEach((corte, i) => {
            corte.id = i + 1;
        });
        atualizarTabelaCortes();
        
        // Se não houver mais cortes, ocultar lista
        if (cortesClassificados.length === 0) {
            document.getElementById('listaCortesContainer').style.display = 'none';
            document.getElementById('btnCalcularRendimento').style.display = 'none';
        }
    }
}

function processarTXT(file) {
    // Verificar se os grupos foram selecionados
    if (cortesDisponiveis.length === 0) {
        alert('Por favor, selecione os grupos de cortes primeiro!');
        return;
    }
    
    const reader = new FileReader();
    reader.onload = function(e) {
        try {
            const linhas = e.target.result.split('\n');
            let linhasProcessadas = 0;
            
            linhas.forEach((linha, index) => {
                if (linha.trim()) {
                    const partes = linha.split(';');
                    if (partes.length >= 4) {
                        const codigo = partes[0].trim();
                        const corte = partes[1].trim();
                        const peso = parseFloat(partes[2].replace(',', '.')) || 0;
                        const preco = parseFloat(partes[3].replace(',', '.')) || 0;
                        
                        // Verificar se o corte está na lista de cortes disponíveis
                        if (peso > 0 && cortesDisponiveis.includes(corte)) {
                            adicionarCorteClassificado(corte, peso, parseFloat(preco.toFixed(2))); // Forçar 2 casas decimais
                            linhasProcessadas++;
                        } else if (peso > 0) {
                            console.warn(`Corte "${corte}" não está na lista de cortes selecionados`);
                        }
                    } else {
                        console.warn(`Linha ${index + 1} com formato inválido: ${linha}`);
                    }
                }
            });
            
            if (linhasProcessadas > 0) {
                alert(`Arquivo TXT processado com sucesso! ${linhasProcessadas} cortes importados.`);
                // Mostrar lista e botão de cálculo
                document.getElementById('listaCortesContainer').style.display = 'block';
                document.getElementById('btnCalcularRendimento').style.display = 'inline-block';
            } else {
                alert('Nenhum corte válido encontrado no arquivo TXT ou todos os cortes estão fora dos grupos selecionados.');
            }
        } catch (error) {
            console.error('Erro ao processar arquivo TXT:', error);
            alert('Erro ao processar arquivo TXT: ' + error.message);
        }
    };
    reader.readAsText(file);
}

function calcularRendimento(e) {
    e.preventDefault();
    
    // Verificar se os grupos foram selecionados
    if (cortesDisponiveis.length === 0) {
        alert('Por favor, selecione os grupos de cortes primeiro!');
        return;
    }
    
    if (cortesClassificados.length === 0) {
        alert('Nenhum corte foi classificado ainda. Por favor, classifique pelo menos um corte.');
        return;
    }
    
    console.log('Debug - Cortes classificados completos:', cortesClassificados);
    console.log('Debug - Estrutura do primeiro corte:', cortesClassificados[0]);
    
    // Calcular totais
    const pesoTotal = parseFloat(document.getElementById('peso_total').value.replace(/\./g, '').replace(',', '.')) || 0;
    const custoTotal = parseFloat(document.getElementById('custo_total').value.replace(/\./g, '').replace(',', '.')) || 0;
    
    let pesoCarnes = 0;
    let valorCarnes = 0;
    let custoCarnes = 0;
    
    // Separar itens de descarte dos cortes de carne
    const itensDescarte = ['Osso', 'Sebo', 'Pelanca'];
    
    // Verificar se há problemas com espaços ou caracteres especiais
    const carnes = cortesClassificados.filter(corte => {
        const nomeLimpo = corte.nome.trim();
        const isDescarte = itensDescarte.some(item => item.trim() === nomeLimpo);
        console.log(`Verificando corte: "${corte.nome}" (limpo: "${nomeLimpo}") - É descarte? ${isDescarte}`);
        return !isDescarte;
    });
    
    const descarte = cortesClassificados.filter(corte => {
        const nomeLimpo = corte.nome.trim();
        return itensDescarte.some(item => item.trim() === nomeLimpo);
    });
    
    // Verificar se a filtragem está funcionando
    if (carnes.length === 0) {
        console.warn('ATENÇÃO: Nenhuma carne foi encontrada após filtragem!');
        console.warn('Todos os cortes foram classificados como descarte:', cortesClassificados.map(c => c.nome));
        
        // Forçar classificação manual baseada nos nomes conhecidos
        console.log('Tentando classificação manual...');
        const carnesConhecidas = ['Alcatra', 'Baby Beef', 'Bombom da Alcatra', 'Picanha', 'Maminha', 'Contrafilé', 'Filé mignon', 'Coxão mole', 'Coxão duro', 'Lagarto', 'Paulista', 'Patinho', 'Fraldinha', 'Ponta de alcatra', 'Músculo Traseiro', 'Costela ripa', 'Costela minga', 'Costelão gaúcho', 'Chupa-molho', 'Short ribs', 'Prime rib', 'Denver steak', 'Peito entremeado', 'Bananinha', 'Fraldinha', 'Matambre', 'Vazio', 'Entranha', 'Flanco', 'Paella beef', 'Ancho', 'Chorizo', 'Tomahawk', 'Costela fogo de chão', 'Fígado', 'Rim', 'Língua', 'Coração', 'Fato', 'Livro', 'Bochecha', 'Mocotó', 'Rabo', 'Bucho'];
        
        const carnesManual = cortesClassificados.filter(corte => {
            const nomeLimpo = corte.nome.trim();
            return carnesConhecidas.some(carne => carne.toLowerCase() === nomeLimpo.toLowerCase());
        });
        
        if (carnesManual.length > 0) {
            console.log('Classificação manual bem-sucedida:', carnesManual.map(c => c.nome));
            // Usar a classificação manual
            carnes.length = 0; // Limpar array
            carnesManual.forEach(c => carnes.push(c)); // Adicionar carnes encontradas
        }
    }
    

    
    console.log('Debug - Itens de descarte:', itensDescarte);
    console.log('Debug - Cortes classificados:', cortesClassificados.map(c => c.nome));
    console.log('Debug - Carnes filtradas:', carnes.map(c => c.nome));
    console.log('Debug - Descarte filtrado:', descarte.map(c => c.nome));
    
    // Calcular totais das carnes (excluindo descarte)
    carnes.forEach(corte => {
        pesoCarnes += corte.peso;
        valorCarnes += corte.totalVenda;
        custoCarnes += corte.custo;
        console.log(`Debug - Carne: ${corte.nome}, Peso: ${corte.peso}, Venda: ${corte.totalVenda}, Custo: ${corte.custo}`);
    });
    
    // Calcular totais do descarte
    let pesoDescarte = 0;
    let custoDescarte = 0;
    descarte.forEach(item => {
        pesoDescarte += item.peso;
        custoDescarte += item.custo;
        console.log(`Debug - Descarte: ${item.nome}, Peso: ${item.peso}, Custo: ${item.custo}`);
    });
    
    console.log('Debug - Totais calculados:');
    console.log('Peso Carnes:', pesoCarnes);
    console.log('Valor Carnes:', valorCarnes);
    console.log('Custo Carnes:', custoCarnes);
    console.log('Peso Descarte:', pesoDescarte);
    console.log('Custo Descarte:', custoDescarte);
    
    // Calcular peso não classificado (diferença entre total e classificado)
    const pesoClassificado = pesoCarnes + pesoDescarte;
    const pesoNaoClassificado = pesoTotal - pesoClassificado;
    const custoNaoClassificado = pesoNaoClassificado * (custoTotal / pesoTotal);
    
    // Calcular percentuais
    const rendimento = pesoTotal > 0 ? (pesoCarnes / pesoTotal) * 100 : 0;
    const percentualClassificado = pesoTotal > 0 ? (pesoClassificado / pesoTotal) * 100 : 0;
    const percentualNaoClassificado = pesoTotal > 0 ? (pesoNaoClassificado / pesoTotal) * 100 : 0;
    
    // Calcular indicadores financeiros
    // Valor Lucro: Valor Venda Carnes - Custo Total
    const valorLucro = valorCarnes - custoTotal;
    // % Lucro: (Valor Venda Carnes - Custo Total) / Valor Venda Carnes
    const margemPercentual = valorCarnes > 0 ? (valorLucro / valorCarnes) * 100 : 0;
    const precoMedio = pesoCarnes > 0 ? valorCarnes / pesoCarnes : 0;
    
    // Mostrar resultados
    const secaoResultados = document.getElementById('resultados');
    if (secaoResultados) {
        secaoResultados.style.display = 'block';
        console.log('Seção de resultados exibida com sucesso');
    } else {
        console.error('Seção de resultados não encontrada!');
        return; // Parar execução se a seção não existir
    }
    
    // Aguardar um momento para garantir que o DOM foi atualizado
    setTimeout(() => {
        console.log('DOM atualizado, prosseguindo com atualizações...');
        atualizarResumoGeral();
        atualizarResumoFinanceiro();
    }, 100);
    
    // Função para atualizar resumo geral
    function atualizarResumoGeral() {
        console.log('Atualizando resumo geral...');
        
        // Atualizar campos de peso com formatação condicional
        const elementoPesoTotal = document.getElementById('resumo_peso_total');
        const elementoPesoClassificado = document.getElementById('resumo_peso_classificado');
        const elementoPesoNaoClassificado = document.getElementById('resumo_peso_nao_classificado');
        
        if (elementoPesoTotal) {
            elementoPesoTotal.textContent = pesoTotal.toFixed(3);
            elementoPesoTotal.className = 'fw-bold text-dark';
            console.log('Campo resumo_peso_total atualizado: ' + pesoTotal.toFixed(3));
        }
        if (elementoPesoClassificado) {
            elementoPesoClassificado.textContent = pesoClassificado.toFixed(3);
            elementoPesoClassificado.className = pesoClassificado > 0 ? 'fw-bold text-dark' : 'fw-bold text-danger';
            console.log('Campo resumo_peso_classificado atualizado: ' + pesoClassificado.toFixed(3));
        }
        if (elementoPesoNaoClassificado) {
            elementoPesoNaoClassificado.textContent = pesoNaoClassificado.toFixed(3);
            elementoPesoNaoClassificado.className = pesoNaoClassificado > 0 ? 'fw-bold text-danger' : 'fw-bold text-dark';
            console.log('Campo resumo_peso_nao_classificado atualizado: ' + pesoNaoClassificado.toFixed(3));
        }
        
        // Atualizar campos de custo com formatação condicional
        const elementoCustoTotal = document.getElementById('resumo_custo_total');
        const elementoCustoClassificado = document.getElementById('resumo_custo_classificado');
        const elementoCustoNaoClassificado = document.getElementById('resumo_custo_nao_classificado');
        
        if (elementoCustoTotal) {
            elementoCustoTotal.textContent = 'R$ ' + custoTotal.toFixed(2);
            elementoCustoTotal.className = 'fw-bold text-dark';
            console.log('Campo resumo_custo_total atualizado: R$ ' + custoTotal.toFixed(2));
        }
        if (elementoCustoClassificado) {
            const custoClassificado = custoCarnes + custoDescarte;
            elementoCustoClassificado.textContent = 'R$ ' + custoClassificado.toFixed(2);
            elementoCustoClassificado.className = custoClassificado > 0 ? 'fw-bold text-dark' : 'fw-bold text-danger';
            console.log('Campo resumo_custo_classificado atualizado: R$ ' + custoClassificado.toFixed(2));
        }
        if (elementoCustoNaoClassificado) {
            elementoCustoNaoClassificado.textContent = 'R$ ' + custoNaoClassificado.toFixed(2);
            elementoCustoNaoClassificado.className = custoNaoClassificado > 0 ? 'fw-bold text-danger' : 'fw-bold text-dark';
            console.log('Campo resumo_custo_nao_classificado atualizado: R$ ' + custoNaoClassificado.toFixed(2));
        }
        
        // Atualizar campos de percentual com formatação condicional
        const elementoPercentualClassificado = document.getElementById('resumo_percentual_classificado');
        const elementoPercentualNaoClassificado = document.getElementById('resumo_percentual_nao_classificado');
        const elementoRendimento = document.getElementById('resumo_rendimento');
        
        if (elementoPercentualClassificado) {
            elementoPercentualClassificado.textContent = percentualClassificado.toFixed(2) + '%';
            elementoPercentualClassificado.className = percentualClassificado > 0 ? 'fw-bold text-dark' : 'fw-bold text-danger';
            console.log('Campo resumo_percentual_classificado atualizado: ' + percentualClassificado.toFixed(2) + '%');
        }
        if (elementoPercentualNaoClassificado) {
            elementoPercentualNaoClassificado.textContent = percentualNaoClassificado.toFixed(2) + '%';
            elementoPercentualNaoClassificado.className = percentualNaoClassificado > 0 ? 'fw-bold text-danger' : 'fw-bold text-dark';
            console.log('Campo resumo_percentual_nao_classificado atualizado: ' + percentualNaoClassificado.toFixed(2) + '%');
        }

    }
    
    // Função para atualizar resumo financeiro
    function atualizarResumoFinanceiro() {
        console.log('Atualizando resumo financeiro...');
        console.log('Debug - Atualizando resumo financeiro:');
        console.log('Valor Venda Carnes:', valorCarnes);
        console.log('Valor Lucro:', valorLucro);
        console.log('% Lucro:', margemPercentual);
        
        // Verificar se os elementos existem antes de atualizar
        const elementoValorVenda = document.getElementById('resumo_valor_venda_carnes');
        const elementoValorLucro = document.getElementById('resumo_valor_lucro');
        const elementoPercentualLucro = document.getElementById('resumo_percentual_lucro');
        
        if (elementoValorVenda) {
            elementoValorVenda.textContent = 'R$ ' + valorCarnes.toFixed(2);
            elementoValorVenda.className = valorCarnes > 0 ? 'fw-bold text-dark' : 'fw-bold text-danger';
            console.log('Campo resumo_valor_venda_carnes atualizado: R$ ' + valorCarnes.toFixed(2));
        } else {
            console.error('Elemento resumo_valor_venda_carnes não encontrado!');
        }
        
        if (elementoValorLucro) {
            elementoValorLucro.textContent = 'R$ ' + valorLucro.toFixed(2);
            elementoValorLucro.className = valorLucro > 0 ? 'fw-bold text-dark' : 'fw-bold text-danger';
            console.log('Campo resumo_valor_lucro atualizado: R$ ' + valorLucro.toFixed(2));
        } else {
            console.error('Elemento resumo_valor_lucro não encontrado!');
        }
        
        if (elementoPercentualLucro) {
            elementoPercentualLucro.textContent = margemPercentual.toFixed(2) + '%';
            elementoPercentualLucro.className = margemPercentual > 0 ? 'fw-bold text-dark' : 'fw-bold text-danger';
            console.log('Campo resumo_percentual_lucro atualizado: ' + margemPercentual.toFixed(2) + '%');
        } else {
            console.error('Elemento resumo_percentual_lucro não encontrado!');
        }
        
        // Desperdício
        console.log('Debug - Desperdício:');
        console.log('Peso Desperdício:', pesoDescarte);
        console.log('Valor Desperdício:', (pesoDescarte * (custoTotal / pesoTotal)));
        console.log('% Desperdício:', (pesoDescarte / pesoTotal * 100));
        
        const elementoPesoDesperdicio = document.getElementById('resumo_peso_desperdicio');
        const elementoValorDesperdicio = document.getElementById('resumo_valor_desperdicio');
        const elementoPercentualDesperdicio = document.getElementById('resumo_percentual_desperdicio');
        
        if (elementoPesoDesperdicio) {
            elementoPesoDesperdicio.textContent = pesoDescarte.toFixed(3) + ' kg';
            elementoPesoDesperdicio.className = pesoDescarte > 0 ? 'fw-bold text-dark' : 'fw-bold text-danger';
            console.log('Campo resumo_peso_desperdicio atualizado: ' + pesoDescarte.toFixed(3) + ' kg');
        } else {
            console.error('Elemento resumo_peso_desperdicio não encontrado!');
        }
        
        if (elementoValorDesperdicio) {
            elementoValorDesperdicio.textContent = 'R$ ' + (pesoDescarte * (custoTotal / pesoTotal)).toFixed(2);
            elementoValorDesperdicio.className = pesoDescarte > 0 ? 'fw-bold text-dark' : 'fw-bold text-danger';
            console.log('Campo resumo_valor_desperdicio atualizado: R$ ' + (pesoDescarte * (custoTotal / pesoTotal)).toFixed(2));
        } else {
            console.error('Elemento resumo_valor_desperdicio não encontrado!');
        }
        
        if (elementoPercentualDesperdicio) {
            elementoPercentualDesperdicio.textContent = pesoTotal > 0 ? (pesoDescarte / pesoTotal * 100).toFixed(2) + '%' : '0,00%';
            elementoPercentualDesperdicio.className = pesoTotal > 0 ? 'fw-bold text-dark' : 'fw-bold text-danger';
            console.log('Campo resumo_percentual_desperdicio atualizado: ' + (pesoTotal > 0 ? (pesoDescarte / pesoTotal * 100).toFixed(2) + '%' : '0,00%'));
        } else {
            console.error('Elemento resumo_percentual_desperdicio não encontrado!');
        }
        
        // Indicadores
        const pesoCarnesSemDesperdicio = pesoCarnes;
        
        // Cálculo do Custo Final: Custo Total / (Peso Classificado - Peso Desperdício)
        const pesoClassificadoTotal = pesoClassificado; // Peso total classificado (carnes + descarte)
        const pesoDesperdicioTotal = pesoDescarte; // Peso total de desperdício
        const custoFinal = (pesoClassificadoTotal - pesoDesperdicioTotal) > 0 ? 
            custoTotal / (pesoClassificadoTotal - pesoDesperdicioTotal) : 0;
        
        // Venda Média: Soma de todos Preços de Venda / Cortes Classificados (- desperdício)
        const somaPrecosVenda = carnes.reduce((sum, corte) => sum + corte.preco, 0);
        const vendaMedio = carnes.length > 0 ? somaPrecosVenda / carnes.length : 0;
        
        const cortesClassificadosSemDesperdicio = carnes.length;
        
        console.log('Debug - Indicadores:');
        console.log('Peso Classificado Total:', pesoClassificadoTotal);
        console.log('Peso Desperdício Total:', pesoDesperdicioTotal);
        console.log('Custo Final:', custoFinal);
        console.log('Soma Preços de Venda:', somaPrecosVenda);
        console.log('Venda Médio:', vendaMedio);
        console.log('Cortes Classificados (- desperdício):', cortesClassificadosSemDesperdicio);
        
        const elementoCustoFinal = document.getElementById('resumo_custo_final');
        const elementoVendaMedio = document.getElementById('resumo_venda_medio');
        const elementoCortesClassificados = document.getElementById('resumo_cortes_classificados_sem_desperdicio');
        
        if (elementoCustoFinal) {
            elementoCustoFinal.textContent = 'R$ ' + custoFinal.toFixed(2);
            elementoCustoFinal.className = custoFinal > 0 ? 'fw-bold text-dark' : 'fw-bold text-danger';
            console.log('Campo resumo_custo_final atualizado: R$ ' + custoFinal.toFixed(2));
        } else {
            console.error('Elemento resumo_custo_final não encontrado!');
        }
        
        if (elementoVendaMedio) {
            elementoVendaMedio.textContent = 'R$ ' + vendaMedio.toFixed(2);
            elementoVendaMedio.className = vendaMedio > 0 ? 'fw-bold text-dark' : 'fw-bold text-danger';
            console.log('Campo resumo_venda_medio atualizado: R$ ' + vendaMedio.toFixed(2));
        } else {
            console.error('Elemento resumo_venda_medio não encontrado!');
        }
        
        if (elementoCortesClassificados) {
            elementoCortesClassificados.textContent = cortesClassificadosSemDesperdicio;
            elementoCortesClassificados.className = cortesClassificadosSemDesperdicio > 0 ? 'fw-bold text-dark' : 'fw-bold text-danger';
            console.log('Campo resumo_cortes_classificados_sem_desperdicio atualizado: ' + cortesClassificadosSemDesperdicio);
        } else {
            console.error('Elemento resumo_cortes_classificados_sem_desperdicio não encontrado!');
        }
    }
    
    // Atualizar tabela de cortes com dados reais
    atualizarTabelaCortesComPrecos();
    
    // Recolher automaticamente a etapa anterior
    recolherEtapaAnterior('resultados');
}

function salvarDadosBoi(e) {
    e.preventDefault();
    
    // Validar dados
    const dados = {
        identificacao: document.getElementById('identificacao_boi').value,
        dataAbate: document.getElementById('data_abate').value,
        pesoTotal: document.getElementById('peso_total').value,
        precoKg: document.getElementById('preco_kg').value,
        custoTotal: document.getElementById('custo_total').value,
        fornecedor: document.getElementById('fornecedor').value
    };
    
    if (!dados.identificacao || !dados.dataAbate || !dados.pesoTotal || !dados.precoKg) {
        alert('Preencha todos os campos obrigatórios');
        return;
    }
    
    // Salvar dados (implementar conforme necessário)
    console.log('Dados do boi salvos:', dados);
    alert('Dados do boi salvos com sucesso!');
    
    // NÃO executar validação automática - aguardar ação do usuário
    console.log('Dados salvos - aguardando usuário clicar em "Salvar e Continuar"');
}

function atualizarTabelaCortesComPrecos() {
    const tbody = document.getElementById('tabela_cortes_classificados');
    tbody.innerHTML = '';
    
    const pesoTotalFormatado = document.getElementById('peso_total').value;
    const pesoTotal = parseFloat(pesoTotalFormatado.replace(/\./g, '').replace(',', '.')) || 0;
    
    cortesClassificados.forEach(corte => {
        const percentual = pesoTotal > 0 ? (corte.peso / pesoTotal) * 100 : 0;
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>
                <button type="button" class="btn btn-primary btn-sm me-1" onclick="editarCorte(${corte.id})">
                    <i class="fas fa-edit"></i>
                </button>
                <button type="button" class="btn btn-danger btn-sm" onclick="removerCorte(${corte.id})">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
            <td>${corte.nome}</td>
            <td>${corte.peso.toFixed(3)} kg</td>
            <td>${percentual.toFixed(2)}%</td>
            <td>R$ ${corte.custo.toFixed(2)}</td>
            <td>R$ ${corte.preco.toFixed(2)}</td>
            <td>R$ ${corte.totalVenda.toFixed(2)}</td>
            <td>${corte.margem.toFixed(2)}%</td>
        `;
        tbody.appendChild(row);
    });
    
    // Atualizar totais
    atualizarTotaisTabela();
}

function inicializarDados() {
    console.log('Inicializando dados...');
    
    // Definir data atual como padrão
    const hoje = new Date().toISOString().split('T')[0];
    document.getElementById('data_abate').value = hoje;
    console.log('Data definida:', hoje);
    
    // Adicionar event listeners para validar dados brutos (sem validação automática)
console.log('Configurando validação sem execução automática...');
    setupValidacaoDadosBrutos();
    
    // Inicializar grupos de cortes
    inicializarGruposCortes();
    
    console.log('Inicialização concluída');
}

function inicializarGruposCortes() {
    const container = document.getElementById('gruposCortesContainer');
    container.innerHTML = '';
    
    // Criar duas linhas para organizar os grupos
    const row1 = document.createElement('div');
    row1.className = 'row mb-3';
    
    const row2 = document.createElement('div');
    row2.className = 'row mb-3';
    
    Object.keys(gruposCortes).forEach((grupoKey, index) => {
        const grupo = gruposCortes[grupoKey];
        const col = document.createElement('div');
        
        // Primeiros 3 grupos vão para a primeira linha, últimos 3 para a segunda
        if (index < 3) {
            col.className = 'col-md-4 mb-3';
            row1.appendChild(col);
        } else {
            col.className = 'col-md-4 mb-3';
            row2.appendChild(col);
        }
        
        col.innerHTML = `
            <div class="card grupo-corte-card" data-grupo="${grupoKey}">
                <i class="fas fa-check check-icon"></i>
                <div class="card-header">
                    <h6 class="mb-0">${grupo.nome}</h6>
                </div>
                <div class="card-body">
                    <ul class="cortes-lista">
                        ${grupo.cortes.map(corte => `<li>${corte}</li>`).join('')}
                    </ul>
                </div>
            </div>
        `;
        
        // Adicionar evento de clique
        const card = col.querySelector('.grupo-corte-card');
        card.addEventListener('click', () => toggleGrupoSelecionado(grupoKey));
    });
    
    // Adicionar as linhas ao container
    container.appendChild(row1);
    container.appendChild(row2);
    
    // Adicionar evento ao botão de confirmar grupos
    document.getElementById('btnConfirmarGrupos').addEventListener('click', confirmarGruposSelecionados);
    
    // Adicionar evento ao botão de confirmar descarte
    document.getElementById('btnConfirmarDescarte').addEventListener('click', confirmarDescarte);
}

function confirmarDescarte() {
    const pesoOssoFormatado = document.getElementById('peso_osso').value;
    const pesoSeboFormatado = document.getElementById('peso_sebo').value;
    const pesoPelancaFormatado = document.getElementById('peso_pelanca').value;
    
    const pesoOsso = parseFloat(pesoOssoFormatado.replace(/\./g, '').replace(',', '.')) || 0;
    const pesoSebo = parseFloat(pesoSeboFormatado.replace(/\./g, '').replace(',', '.')) || 0;
    const pesoPelanca = parseFloat(pesoPelancaFormatado.replace(/\./g, '').replace(',', '.')) || 0;
    
    if (pesoOsso < 0 || pesoSebo < 0 || pesoPelanca < 0) {
        alert('Por favor, insira valores válidos (maiores ou iguais a zero) para todos os campos de descarte.');
        return;
    }
    
    // Adicionar itens de descarte à lista de cortes classificados
    if (pesoOsso > 0) {
        adicionarCorteClassificado('Osso', pesoOsso, 0);
    }
    if (pesoSebo > 0) {
        adicionarCorteClassificado('Sebo', pesoSebo, 0);
    }
    if (pesoPelanca > 0) {
        adicionarCorteClassificado('Pelanca', pesoPelanca, 0);
    }
    
    // Mostrar mensagem de sucesso
    alert('Descarte confirmado! Agora você pode classificar as carnes.');
    
    // Fechar modal e mostrar etapa de classificação
    const modal = bootstrap.Modal.getInstance(document.getElementById('modalDescarte'));
    modal.hide();
    
    document.getElementById('etapaClassificacao').style.display = 'block';
    
    // Recolher automaticamente a etapa anterior
    recolherEtapaAnterior('etapaClassificacao');
}

function voltarEtapa(etapa) {
    // Ocultar todas as etapas
    document.getElementById('etapaDadosBrutos').style.display = 'none';
    document.getElementById('etapaGruposCortes').style.display = 'none';
    document.getElementById('etapaClassificacao').style.display = 'none';
    
    // Mostrar a etapa solicitada
    document.getElementById(etapa).style.display = 'block';
}

function setupValidacaoDadosBrutos() {
    console.log('Configurando validação de dados brutos...');
    
    // Campos obrigatórios para validação
    const camposObrigatorios = [
        'identificacao_boi',
        'data_abate',
        'peso_total',
        'preco_kg'
    ];
    
    // Adicionar event listeners para cada campo
    camposObrigatorios.forEach(campoId => {
        const campo = document.getElementById(campoId);
        if (campo) {
            console.log(`Adicionando listener para campo: ${campoId}`);
            // NÃO executar validação automática - apenas marcar como preenchimento manual
            campo.addEventListener('input', () => {
                marcarDigitacao();
                // Não chamar verificarDadosBrutos() aqui
            });
            campo.addEventListener('change', () => {
                marcarDigitacao();
                // Não chamar verificarDadosBrutos() aqui
            });
        } else {
            console.error(`Campo não encontrado: ${campoId}`);
        }
    });
    
    // NÃO executar verificação inicial automaticamente
    console.log('Validação automática desabilitada - aguardando ação do usuário');
}

function verificarDadosBrutos() {
    console.log('=== INÍCIO DA VALIDAÇÃO DE DADOS BRUTOS ===');
    console.log('Função verificarDadosBrutos() executada');
    
    const camposObrigatorios = [
        'identificacao_boi',
        'data_abate',
        'peso_total',
        'preco_kg'
    ];
    
    let todosPreenchidos = true;
    const resultados = {};
    
    // Verificar cada campo obrigatório
    camposObrigatorios.forEach(campoId => {
        const campo = document.getElementById(campoId);
        if (campo) {
            const valor = campo.value.trim();
            console.log(`Campo ${campoId}: valor="${valor}", tipo="${typeof valor}"`);
            
            if (!valor) {
                todosPreenchidos = false;
                resultados[campoId] = { preenchido: false, motivo: 'Campo vazio' };
                console.log(`❌ Campo ${campoId} não preenchido: "${valor}"`);
            } else {
                // Validação específica para preço por kg
                if (campoId === 'preco_kg') {
                    const preco = parseFloat(valor.replace(/\./g, '').replace(',', '.'));
                    console.log(`Preço por kg: valor original="${valor}", convertido=${preco}`);
                    if (isNaN(preco) || preco <= 0) {
                        todosPreenchidos = false;
                        resultados[campoId] = { preenchido: false, motivo: `Preço inválido: ${valor}` };
                        console.log(`❌ Preço por kg inválido: "${valor}" -> ${preco}`);
                    } else {
                        resultados[campoId] = { preenchido: true, valor: preco };
                        console.log(`✅ Preço por kg válido: ${preco}`);
                    }
                }
                
                // Validação específica para peso total
                else if (campoId === 'peso_total') {
                    const peso = parseFloat(valor.replace(/\./g, '').replace(',', '.'));
                    console.log(`Peso total: valor original="${valor}", convertido=${peso}`);
                    if (isNaN(peso) || peso <= 0) {
                        todosPreenchidos = false;
                        resultados[campoId] = { preenchido: false, motivo: `Peso inválido: ${valor}` };
                        console.log(`❌ Peso total inválido: "${valor}" -> ${peso}`);
                    } else {
                        resultados[campoId] = { preenchido: true, valor: peso };
                        console.log(`✅ Peso total válido: ${peso}`);
                    }
                }
                
                // Campos de texto simples
                else {
                    resultados[campoId] = { preenchido: true, valor: valor };
                    console.log(`✅ Campo ${campoId} preenchido: "${valor}"`);
                }
            }
    } else {
            todosPreenchidos = false;
            resultados[campoId] = { preenchido: false, motivo: 'Campo não encontrado no DOM' };
            console.error(`❌ Campo não encontrado no DOM: ${campoId}`);
        }
    });
    
    console.log('=== RESUMO DA VALIDAÇÃO ===');
    console.log('Resultados por campo:', resultados);
    console.log('Todos os campos preenchidos:', todosPreenchidos);
    console.log('=== FIM DA VALIDAÇÃO ===');
    
    // Retornar resultado da validação (não executar automaticamente)
    return todosPreenchidos;
}

function toggleGrupoSelecionado(grupoKey) {
    const card = document.querySelector(`[data-grupo="${grupoKey}"]`);
    card.classList.toggle('selected');
    
    // Verificar se há grupos selecionados
    const gruposSelecionados = document.querySelectorAll('.grupo-corte-card.selected');
    const btnConfirmar = document.getElementById('btnConfirmarGrupos');
    
    btnConfirmar.disabled = gruposSelecionados.length === 0;
}

function confirmarGruposSelecionados() {
    const gruposSelecionados = document.querySelectorAll('.grupo-corte-card.selected');
    
    // Limpar array de cortes disponíveis
    cortesDisponiveis = [];
    
    // Adicionar cortes dos grupos selecionados
    gruposSelecionados.forEach(card => {
        const grupoKey = card.dataset.grupo;
        const grupo = gruposCortes[grupoKey];
        cortesDisponiveis = cortesDisponiveis.concat(grupo.cortes);
    });
    
    // Remover duplicatas (caso um corte apareça em múltiplos grupos)
    cortesDisponiveis = [...new Set(cortesDisponiveis)];
    
    // Resetar índices
    corteAtualIndex = 0;
    cortesClassificados = [];
    
    // Mostrar mensagem de sucesso
    alert(`Grupos confirmados! ${cortesDisponiveis.length} cortes disponíveis para classificação.`);
    
    // Mostrar modal de descarte
    const modal = new bootstrap.Modal(document.getElementById('modalDescarte'));
    modal.show();
    
    // Recolher automaticamente a etapa anterior
    recolherEtapaAnterior('etapaClassificacao');
}

// Função para expandir/recolher seções
function toggleSection(sectionName, expandir = null) {
    console.log('toggleSection chamada com:', sectionName, 'expandir:', expandir);
    
    // Normalizar capitalização: primeira letra maiúscula
    const normalizedName = sectionName.charAt(0).toUpperCase() + sectionName.slice(1);
    
    const bodyId = `body${normalizedName}`;
    const btnId = `btnToggle${normalizedName}`;
    const iconId = `icon${normalizedName}`;
    
    console.log('Procurando elementos:', { bodyId, btnId, iconId });
    
    const body = document.getElementById(bodyId);
    const btn = document.getElementById(btnId);
    const icon = document.getElementById(iconId);
    
    if (!body || !btn || !icon) {
        console.error('Elementos não encontrados para:', sectionName);
        console.error('Body:', body, 'Btn:', btn, 'Icon:', icon);
        return;
    }
    
    // Se expandir não foi especificado, alternar estado atual
    if (expandir === null) {
        // Se está oculto (none), queremos expandir (true)
        // Se está visível (block), queremos recolher (false)
        expandir = (body.style.display === 'none');
    }
    
    console.log('Estado atual da seção:', sectionName, 'expandir:', expandir);
    
    if (expandir === true) {
        // Expandir
        console.log('Expandindo seção:', sectionName);
        body.style.display = 'block';
        body.classList.remove('collapsed');
        icon.className = 'fas fa-chevron-up';
        btn.title = 'Recolher seção';
    } else {
        // Recolher
        console.log('Recolhendo seção:', sectionName);
        body.style.display = 'none';
        body.classList.add('collapsed');
        icon.className = 'fas fa-chevron-down';
        btn.title = 'Expandir seção';
    }
}

// Função para recolher automaticamente a etapa anterior
function recolherEtapaAnterior(etapaAtual) {
    console.log('Recolhendo etapa anterior para:', etapaAtual);
    
    const etapas = {
        'etapaGruposCortes': 'DadosBoi',
        'etapaClassificacao': 'GruposCortes',
        'resultados': 'ClassificacaoCarnes'
    };
    
    const etapaAnterior = etapas[etapaAtual];
    if (etapaAnterior) {
        console.log('Recolhendo etapa:', etapaAnterior);
        toggleSection(etapaAnterior, false);
    }
}

// FUNÇÃO DUPLICADA REMOVIDA - A versão correta está na linha 2961
// Esta função estava causando conflito e execução automática indesejada

function confirmarGruposSelecionados() {
    const gruposSelecionados = document.querySelectorAll('.grupo-corte-card.selected');
    
    // Limpar array de cortes disponíveis
    cortesDisponiveis = [];
    
    // Adicionar cortes dos grupos selecionados
    gruposSelecionados.forEach(card => {
        const grupoKey = card.dataset.grupo;
        const grupo = gruposCortes[grupoKey];
        cortesDisponiveis = cortesDisponiveis.concat(grupo.cortes);
    });
    
    // Remover duplicatas (caso um corte apareça em múltiplos grupos)
    cortesDisponiveis = [...new Set(cortesDisponiveis)];
    
    // Resetar índices
    corteAtualIndex = 0;
    cortesClassificados = [];
    
    // Mostrar mensagem de sucesso
    alert(`Grupos confirmados! ${cortesDisponiveis.length} cortes disponíveis para classificação.`);
    
    // Mostrar modal de descarte
    const modal = new bootstrap.Modal(document.getElementById('modalDescarte'));
    modal.show();
    
    // Recolher automaticamente a etapa anterior
    recolherEtapaAnterior('etapaClassificacao');
}

// ========================================
// FUNÇÃO DE GERAÇÃO DE RELATÓRIO HTML
// ========================================

function gerarRelatorioHTML() {
    try {
        // Verificar se há dados para gerar o relatório
        const pesoTotal = parseFloat(document.getElementById('peso_total').value.replace(/\./g, '').replace(',', '.')) || 0;
        if (pesoTotal <= 0) {
            alert('Por favor, preencha os dados do boi antes de gerar o relatório.');
            return;
        }
        
        // Função auxiliar para aplicar classes de cor baseada no valor
        function aplicarClasseCor(valor, tipo = 'texto') {
            if (tipo === 'texto') {
                // Para valores de texto, verificar se contém números negativos
                const numero = parseFloat(valor.replace(/[^\d.,-]/g, '').replace(',', '.'));
                if (numero < 0) return 'negative';
                return '';
            } else if (tipo === 'percentual') {
                // Para percentuais, verificar se é negativo
                const numero = parseFloat(valor.replace(/[^\d.,-]/g, '').replace(',', '.'));
                if (numero < 0) return 'negative';
                return '';
            }
            return '';
        }
        
        // Obter dados básicos
        const identificacaoBoi = document.getElementById('identificacao_boi').value || 'N/A';
        const dataAbate = document.getElementById('data_abate').value || '';
        const dataFormatada = dataAbate ? dataAbate.split('-').reverse().join('-') : '';
        
        // Função para obter o usuário logado
        function obterUsuarioLogado() {
            // Tentar múltiplas formas de obter o usuário
            const usuarioElement = document.querySelector('[data-usuario]');
            if (usuarioElement && usuarioElement.dataset.usuario) {
                return usuarioElement.dataset.usuario;
            }
            
            // Tentar buscar por elementos com informações do usuário
            const nomeUsuario = document.querySelector('.user-name, .usuario-nome, .nome-usuario');
            if (nomeUsuario) {
                return limparTextoUsuario(nomeUsuario.textContent.trim());
            }
            
            // Tentar buscar por elementos de login/logout
            const loginElement = document.querySelector('.login-info, .user-info, .usuario-info');
            if (loginElement) {
                return limparTextoUsuario(loginElement.textContent.trim());
            }
            
            // Fallback para o nome da empresa ou sistema
            return 'AgilFiscal';
        }
        
        // Função para limpar o texto do usuário, removendo informações extras
        function limparTextoUsuario(texto) {
            if (!texto) return 'AgilFiscal';
            
            // Remover informações de notificações, empresa, notas fiscais, etc.
            let textoLimpo = texto;
            
            // Remover padrões comuns de informações extras
            textoLimpo = textoLimpo.replace(/\d+\s*Notificações?/gi, ''); // Remove "1 Notificações"
            textoLimpo = textoLimpo.replace(/ORGANI\s+[^-]+/gi, ''); // Remove "ORGANI VILAS DISTRIBUIDORA DE ALIMENTOS LTDA"
            textoLimpo = textoLimpo.replace(/MatrizNota:\s*\d+/gi, ''); // Remove "MatrizNota: 227864"
            textoLimpo = textoLimpo.replace(/Fechar/gi, ''); // Remove "Fechar"
            textoLimpo = textoLimpo.replace(/Usuário:\s*/gi, ''); // Remove "Usuário: " duplicado
            textoLimpo = textoLimpo.replace(/LTDA\s*-\s*Matriz/gi, ''); // Remove "LTDA - Matriz"
            textoLimpo = textoLimpo.replace(/Nota:\s*\d+/gi, ''); // Remove "Nota: 227864"
            textoLimpo = textoLimpo.replace(/\s+/g, ' '); // Remove espaços múltiplos
            textoLimpo = textoLimpo.replace(/^\s+|\s+$/g, ''); // Remove espaços no início e fim
            
            // Se o texto ficou muito curto ou vazio, usar fallback
            if (textoLimpo.length < 2) {
                return 'AgilFiscal';
            }
            
            // Se ainda contém muitas informações, tentar extrair apenas o nome
            if (textoLimpo.length > 50) {
                // Tentar extrair apenas a primeira parte que parece ser um nome
                const partes = textoLimpo.split(/\s+/);
                if (partes.length > 0 && partes[0].length > 2) {
                    return partes[0];
                }
            }
            
            return textoLimpo;
        }
        
        // Obter caminho base para as imagens
        const baseUrl = window.location.origin + window.location.pathname.replace(/\/[^\/]*$/, '');
        
        // Verificar se estamos em um subdiretório específico
        const currentPath = window.location.pathname;
        let logoPath;
        
        if (currentPath.includes('/extras/')) {
            // Se estamos na página de extras, subir um nível para acessar public
            logoPath = window.location.origin + '/public/assets/images/logo.png';
        } else {
            // Caminho padrão
            logoPath = baseUrl + '/public/assets/images/logo.png';
        }
        
        // Fallback para a logo - tentar múltiplos caminhos na ordem correta
        const logoPaths = [
            logoPath,
            '/public/assets/images/logo.png',
            'public/assets/images/logo.png',
            '../public/assets/images/logo.png',
            '../../public/assets/images/logo.png'
        ];
        
        // Função para criar tag de imagem com fallback
        function criarImagemLogo(className) {
            console.log('Tentando carregar logo com caminhos:', logoPaths);
            console.log('Caminho base:', baseUrl);
            console.log('Caminho completo da logo:', logoPath);
            
            // Verificar se estamos no diretório correto
            const currentPath = window.location.pathname;
            console.log('Caminho atual:', currentPath);
            
            return `<img src="${logoPath}" alt="AgilFiscal Logo" class="${className}" onerror="this.onerror=null; this.src='${logoPaths[1]}'; this.onerror=null; this.src='${logoPaths[2]}'; this.onerror=null; this.src='${logoPaths[3]}'; this.onerror=null; this.src='${logoPaths[4]}'; this.style.display='none'; this.parentNode.innerHTML+='<div style=\'position:absolute; top:15px; left:20px; background:#fff; padding:5px; border-radius:5px; font-size:12px; color:#333;\'>AGILFISCAL</div>';" style="max-width: 100%; height: auto;">`;
        }
        
        // Criar HTML profissional do relatório
        const relatorioHTML = `
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Classificação de Desossa - ${identificacaoBoi}</title>
    <style>
        @media print {
            body { 
                margin: 0; 
                font-size: 11px;
            }
            .no-print { display: none !important; }
            .page-break { page-break-before: always; }
            .container { 
                padding: 8px; 
                box-shadow: none;
            }
            .section { 
                margin-bottom: 8px; 
                padding: 8px;
            }
            .header { 
                padding: 12px; 
                margin-bottom: 12px;
            }
            .footer { 
                padding: 8px; 
                margin-top: 12px;
            }
            .three-columns {
                gap: 8px;
                margin-bottom: 8px;
            }
            .info-row {
                padding: 2px 0;
            }
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background: #f8f9fa;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 15px;
            background: white;
            box-shadow: 0 0 15px rgba(0,0,0,0.08);
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 10px 10px 0 0;
            margin-bottom: 20px;
            position: relative;
        }
        
        .logo {
            position: absolute;
            top: 15px;
            left: 20px;
            width: 80px;
            height: auto;
        }
        
        .header-content {
            margin-left: 100px;
        }
        
        .header h1 {
            font-size: 2rem;
            margin-bottom: 8px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        
        .header p {
            font-size: 1rem;
            opacity: 0.9;
        }
        
        .section {
            background: white;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 12px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.05);
        }
        
        .section h2 {
            color: #667eea;
            margin-bottom: 8px;
            font-size: 1.2rem;
            border-bottom: 1px solid #667eea;
            padding-bottom: 4px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        
        .section h2::before {
            font-size: 1rem;
        }
        
        .three-columns {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-bottom: 12px;
        }
        
        .three-columns .section {
            margin-bottom: 0;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 3px 0;
            border-bottom: 1px solid #f1f3f4;
        }
        
        .info-row:last-child {
            border-bottom: none;
        }
        
        .label {
            font-weight: 600;
            color: #495057;
            font-size: 0.85rem;
        }
        
        .value {
            font-weight: bold;
            color: #000000;
            font-size: 0.9rem;
        }
        
        .value.negative {
            color: #dc3545;
        }
        
        .value.desperdicio {
            color: #dc3545;
        }
        
        .value.success {
            color: #28a745;
        }
        
        .cortes-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            font-size: 0.85rem;
        }
        
        .cortes-table th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 8px 10px;
            text-align: left;
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        .cortes-table td {
            padding: 6px 10px;
            border-bottom: 1px solid #dee2e6;
            color: #000000;
        }
        
        .cortes-table tr:nth-child(even) {
            background: #f8f9fa;
        }
        
        .cortes-table .total-row {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%) !important;
            color: white;
            font-weight: bold;
        }
        
        .footer {
            background: linear-gradient(135deg, #495057 0%, #6c757d 100%);
            color: white;
            text-align: center;
            padding: 15px;
            border-radius: 0 0 10px 10px;
            margin-top: 20px;
            position: relative;
        }
        
        .footer-logo {
            position: absolute;
            top: 15px;
            left: 20px;
            width: 50px;
            height: auto;
            opacity: 0.8;
        }
        
        .footer-content {
            margin-left: 70px;
        }
        
        .footer p {
            margin: 5px 0;
            font-size: 0.9rem;
        }
        
        .actions {
            text-align: center;
            margin: 20px 0;
        }
        
        .btn {
            display: inline-block;
            padding: 10px 25px;
            margin: 0 10px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 20px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            font-size: 1rem;
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
        }
        
        .page-break {
            page-break-before: always;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        
        .stat-card {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border: 2px solid #dee2e6;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            border-color: #667eea;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.15);
        }
        
        .stat-value {
            font-size: 2rem;
            font-weight: bold;
            color: #000000;
            margin: 10px 0;
        }
        
        .stat-value.negative {
            color: #dc3545;
        }
        
        .stat-value.desperdicio {
            color: #dc3545;
        }
        
        .stat-label {
            font-size: 0.9rem;
            color: #6c757d;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        @media (max-width: 768px) {
            .container { padding: 15px; }
            .header h1 { font-size: 2rem; }
            .logo { width: 70px; }
            .header-content { margin-left: 85px; }
            .footer-logo { width: 50px; }
            .footer-content { margin-left: 60px; }
            .stats-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Cabeçalho -->
        <div class="header">
            ${criarImagemLogo('logo')}
            <div class="header-content">
                <h1>📊 Relatório de Classificação de Desossa</h1>
                <p>Identificação: ${identificacaoBoi}</p>
            </div>
        </div>

                   <div class="section">
          <h2>🐄 Dados Brutos</h2>
          <div class="info-row">
              <span class="label">Identificação:</span>
              <span class="value">${identificacaoBoi}</span>
          </div>
          <div class="info-row">
              <span class="label">Data de Abate:</span>
              <span class="value">${dataFormatada}</span>
          </div>
          <div class="info-row">
              <span class="label">Peso Total:</span>
              <span class="value">${document.getElementById('peso_total').value || '0,000'} kg</span>
          </div>
          <div class="info-row">
              <span class="label">Preço por kg:</span>
              <span class="value">R$ ${document.getElementById('preco_kg').value || '0,00'}</span>
          </div>
          <div class="info-row">
              <span class="label">Custo Total:</span>
              <span class="value">R$ ${document.getElementById('custo_total').value || '0,00'}</span>
          </div>
          <div class="info-row">
              <span class="label">Fornecedor:</span>
              <span class="value">${document.getElementById('fornecedor').value || 'N/A'}</span>
          </div>
      </div>

      <div class="section">
          <h2>📊 Resumo Geral</h2>
          <div class="info-row">
              <span class="label">Peso Total:</span>
              <span class="value">${document.getElementById('resumo_peso_total').textContent || '0,000'} kg</span>
          </div>
          <div class="info-row">
              <span class="label">Peso Classificado:</span>
              <span class="value">${document.getElementById('resumo_peso_classificado').textContent || '0,000'} kg</span>
          </div>
          <div class="info-row">
              <span class="label">Peso Não Classificado:</span>
              <span class="value desperdicio">${document.getElementById('resumo_peso_nao_classificado').textContent || '0,000'} kg</span>
          </div>
          <div class="info-row">
              <span class="label">Custo Total:</span>
              <span class="value">${document.getElementById('resumo_custo_total').textContent || 'R$ 0,00'}</span>
          </div>
          <div class="info-row">
              <span class="label">Custo Classificado:</span>
              <span class="value">${document.getElementById('resumo_custo_classificado').textContent || 'R$ 0,00'}</span>
          </div>
          <div class="info-row">
              <span class="label">Custo Não Classificado:</span>
              <span class="value desperdicio">${document.getElementById('resumo_custo_nao_classificado').textContent || 'R$ 0,00'}</span>
          </div>
          <div class="info-row">
              <span class="label">Percentuais Classificado:</span>
              <span class="value">${document.getElementById('resumo_percentual_classificado').textContent || '0,00%'}</span>
          </div>
          <div class="info-row">
              <span class="label">Percentuais Não Classificado:</span>
              <span class="value desperdicio">${document.getElementById('resumo_percentual_nao_classificado').textContent || '0,00%'}</span>
          </div>
      </div>

      <div class="three-columns">
          <div class="section">
              <h2>💰 Resumo Financeiro</h2>
              <div class="info-row">
                  <span class="label">Valor Venda Carnes:</span>
                  <span class="value">${document.getElementById('resumo_valor_venda_carnes').textContent || 'R$ 0,00'}</span>
              </div>
              <div class="info-row">
                  <span class="label">Valor Lucro:</span>
                  <span class="value ${aplicarClasseCor(document.getElementById('resumo_valor_lucro').textContent || 'R$ 0,00')}">${document.getElementById('resumo_valor_lucro').textContent || 'R$ 0,00'}</span>
              </div>
              <div class="info-row">
                  <span class="label">% Lucro:</span>
                  <span class="value ${aplicarClasseCor(document.getElementById('resumo_percentual_lucro').textContent || '0,00%', 'percentual')}">${document.getElementById('resumo_percentual_lucro').textContent || '0,00%'}</span>
              </div>
          </div>

          <div class="section">
              <h2>⚠️ Desperdício</h2>
              <div class="info-row">
                  <span class="label">Peso Desperdício:</span>
                  <span class="value desperdicio">${document.getElementById('resumo_peso_desperdicio').textContent || '0,000 kg'}</span>
              </div>
              <div class="info-row">
                  <span class="label">Valor Desperdício:</span>
                  <span class="value desperdicio">${document.getElementById('resumo_valor_desperdicio').textContent || 'R$ 0,00'}</span>
              </div>
              <div class="info-row">
                  <span class="label">% Desperdício:</span>
                  <span class="value desperdicio">${document.getElementById('resumo_percentual_desperdicio').textContent || '0,00%'}</span>
              </div>
          </div>

          <div class="section">
              <h2>📈 Indicadores</h2>
              <div class="info-row">
                  <span class="label">Custo Final:</span>
                  <span class="value ${aplicarClasseCor(document.getElementById('resumo_custo_final').textContent || 'R$ 0,00')}">${document.getElementById('resumo_custo_final').textContent || 'R$ 0,00'}</span>
              </div>
              <div class="info-row">
                  <span class="label">Venda Médio:</span>
                  <span class="value ${aplicarClasseCor(document.getElementById('resumo_venda_medio').textContent || 'R$ 0,00')}">${document.getElementById('resumo_venda_medio').textContent || 'R$ 0,00'}</span>
              </div>
              <div class="info-row">
                  <span class="label">Cortes Classificados (- desperdício):</span>
                  <span class="value ${aplicarClasseCor(document.getElementById('resumo_cortes_classificados_sem_desperdicio').textContent || '0')}">${document.getElementById('resumo_cortes_classificados_sem_desperdicio').textContent || '0'}</span>
              </div>
          </div>
      </div>

         ${cortesClassificados && cortesClassificados.length > 0 ? `
     <div class="page-break"></div>
     <div class="section">
         <h2>🥩 Cortes Classificados</h2>
         <table class="cortes-table">
             <thead>
                 <tr>
                     <th>Corte</th>
                     <th>Peso (kg)</th>
                     <th>% do Total</th>
                     <th>Custo</th>
                     <th>Preço/kg</th>
                     <th>Total Venda</th>
                     <th>Margem</th>
                 </tr>
             </thead>
             <tbody>
                 ${cortesClassificados.map(corte => {
                     const pesoTotal = parseFloat(document.getElementById('peso_total').value.replace(/\./g, '').replace(',', '.')) || 0;
                     const percentual = pesoTotal > 0 ? (corte.peso / pesoTotal) * 100 : 0;
                     const totalVenda = corte.peso * corte.preco;
                     const margem = totalVenda > 0 ? ((totalVenda - corte.custo) / totalVenda) * 100 : 0;
                     
                     return `
                     <tr>
                         <td><strong>${corte.nome}</strong></td>
                         <td>${corte.peso.toFixed(3)} kg</td>
                         <td>${percentual.toFixed(2)}%</td>
                         <td>R$ ${corte.custo.toFixed(2)}</td>
                         <td>R$ ${corte.preco.toFixed(2)}</td>
                         <td>R$ ${totalVenda.toFixed(2)}</td>
                         <td class="${margem < 0 ? 'negative' : ''}">${margem.toFixed(2)}%</td>
                     </tr>
                     `;
                 }).join('')}
                 <tr class="total-row">
                     <td><strong>TOTAIS:</strong></td>
                     <td><strong>${cortesClassificados.reduce((sum, corte) => sum + corte.peso, 0).toFixed(3)} kg</strong></td>
                     <td><strong>${(cortesClassificados.reduce((sum, corte) => sum + corte.peso, 0) / pesoTotal * 100).toFixed(2)}%</strong></td>
                     <td><strong>R$ ${cortesClassificados.reduce((sum, corte) => sum + corte.custo, 0).toFixed(2)}</strong></td>
                     <td><strong>-</strong></td>
                     <td><strong>R$ ${cortesClassificados.reduce((sum, corte) => sum + (corte.peso * corte.preco), 0).toFixed(2)}</strong></td>
                     <td><strong>${cortesClassificados.reduce((sum, corte) => {
                         const totalVenda = sum + (corte.peso * corte.preco);
                         const totalCusto = sum + corte.custo;
                         return totalVenda > 0 ? ((totalVenda - totalCusto) / totalVenda) * 100 : 0;
                     }, 0).toFixed(2)}%</strong></td>
                 </tr>
             </tbody>
         </table>
     </div>
     ` : ''}

     <!-- Ações -->
     <div class="actions no-print">
         <button class="btn" onclick="window.print()">
             🖨️ Imprimir Relatório
         </button>
            </div>

     <!-- Rodapé -->
     <div class="footer">
         ${criarImagemLogo('footer-logo')}
         <div class="footer-content">
             <p><strong>AgilFiscal.com.br</strong> - Sistema de Classificação de Desossa</p>
             <p>Relatório gerado em: ${new Date().toLocaleDateString('pt-BR')} às ${new Date().toLocaleTimeString('pt-BR')}</p>
             <p>Usuário: ${obterUsuarioLogado()}</p>
         </div>
     </div>
</body>
</html>`;

        // Abrir o relatório em uma nova janela
        const novaJanela = window.open('', '_blank');
        novaJanela.document.write(relatorioHTML);
        novaJanela.document.close();
        
                // Aguardar o carregamento e focar na janela
        novaJanela.onload = function() {
            novaJanela.focus();
        };

    } catch (error) {
        console.error('Erro ao gerar relatório HTML:', error);
        alert('Erro ao gerar relatório HTML: ' + error.message);
    }
}

// Função para exportar como PDF (usa a função de impressão do navegador)
function exportarPDF() {
    window.print();
}


</script>

</body>
</html>
