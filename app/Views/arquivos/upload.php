<?php
$title = 'Enviar Arquivo';
$tipo_usuario = $_SESSION['tipo_usuario'] ?? null;
?>

<div class="container-fluid px-4">
    <h2 class="mt-4">Enviar Arquivo</h2>
    
    <?php if (isset($error)): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php echo htmlspecialchars($error); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>
    
    <div class="mb-4 d-flex align-items-center gap-3">
        <div>
            <label for="empresa_id_global" class="form-label fw-bold">Loja</label>
            <select class="form-select w-auto d-inline-block" id="empresa_id_global" name="empresa_id" style="min-width:250px;">
                <option value="">Selecione a loja</option>
                <?php foreach (($empresas ?? []) as $empresa): ?>
                    <option value="<?= $empresa['id'] ?>" <?= ($tipo_usuario == 3 || (isset($_POST['empresa_id']) && $_POST['empresa_id'] == $empresa['id'])) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($empresa['razao_social']) ?><?= isset($empresa['tipo_empresa']) && in_array($empresa['tipo_empresa'], ['matriz','filial']) ? ' - ' . ucfirst($empresa['tipo_empresa']) : '' ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label for="sistema_integrado" class="form-label fw-bold">Sistema Integrado</label>
            <select class="form-select w-auto d-inline-block" id="sistema_integrado" name="sistema_integrado" style="min-width:250px;">
                <option value="">Selecione o sistema</option>
                <option value="arius">Arius</option>
                <option value="vr">VR</option>
                <option value="vr_system">VR System</option>
                <option value="symac">Symac</option>
                <option value="omie">Omie</option>
                <option value="pa">P&A</option>
                <option value="proton">Próton</option>
                <option value="protheus">Protheus</option>
                <option value="consinco">ConSinco</option>
            </select>
        </div>
        <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#modalInstrucoes" id="btn_instrucoes">
            <i class="fas fa-info-circle me-1"></i> Instruções de Layout
        </button>
    </div>
    
    <!-- Modal de Instruções -->
    <div class="modal fade" id="modalInstrucoes" tabindex="-1" aria-labelledby="modalInstrucoesLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalInstrucoesLabel">Instruções de Layout dos Arquivos</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="accordion" id="accordionLayouts">
                        <!-- Notas -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingNotas">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseNotas" aria-expanded="true" aria-controls="collapseNotas">
                                    Layout do Arquivo de Notas
                                </button>
                            </h2>
                            <div id="collapseNotas" class="accordion-collapse collapse show" aria-labelledby="headingNotas" data-bs-parent="#accordionLayouts">
                                <div class="accordion-body">
                                    <p class="mb-2">O arquivo deve ser um CSV/TXT com as seguintes colunas separadas por ponto e vírgula (;):</p>
                                    <code>numero_processo;numero_fiscal;valor;numero_nfe;chave;conferente;escriturador;data_hora_escrituracao</code>
                                    <p class="mt-2 mb-1">Exemplo:</p>
                                    <pre class="bg-light p-2 rounded">12345;67890;1500.00;987654;35220948996461000139550010000123451000123458;JOÃO SILVA;MARIA SANTOS;2024-03-20 14:30:00</pre>
                                    <div class="alert alert-info mt-3">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <strong>Importante:</strong> Caso alguma informação não esteja disponível, deixe o campo em branco usando ponto e vírgula (;;). Por exemplo: <code>12345;;1500.00;;35220948996461000139550010000123451000123458;;;2024-03-20 14:30:00</code>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Fornecedores -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingFornecedores">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFornecedores" aria-expanded="false" aria-controls="collapseFornecedores">
                                    Layout do Arquivo de Fornecedores
                                </button>
                            </h2>
                            <div id="collapseFornecedores" class="accordion-collapse collapse" aria-labelledby="headingFornecedores" data-bs-parent="#accordionLayouts">
                                <div class="accordion-body">
                                    <p class="mb-2">O arquivo deve ser um CSV/TXT com as seguintes colunas separadas por ponto e vírgula (;):</p>
                                    <code>codigo_interno;razao_social;cnpj</code>
                                    <p class="mt-2 mb-1">Exemplo:</p>
                                    <pre class="bg-light p-2 rounded">001;EMPRESA TESTE LTDA;48996461000139</pre>
                                </div>
                            </div>
                        </div>
                        <!-- Desconhecimento -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingDesconhecimento">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDesconhecimento" aria-expanded="false" aria-controls="collapseDesconhecimento">
                                    Layout do Arquivo de Desconhecimento
                                </button>
                            </h2>
                            <div id="collapseDesconhecimento" class="accordion-collapse collapse" aria-labelledby="headingDesconhecimento" data-bs-parent="#accordionLayouts">
                                <div class="accordion-body">
                                    <p class="mb-2">O arquivo deve ser um CSV/TXT contendo apenas a chave da nota fiscal.</p>
                                    <code>chave</code>
                                    <p class="mt-2 mb-1">Exemplo:</p>
                                    <pre class="bg-light p-2 rounded">35220948996461000139550010000123451000123458</pre>
                                                                    </div>
                            </div>
                        </div>
                        <!-- Romaneio -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingRomaneio">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseRomaneio" aria-expanded="false" aria-controls="collapseRomaneio">
                                    Layout do Arquivo de Romaneio
                                </button>
                            </h2>
                            <div id="collapseRomaneio" class="accordion-collapse collapse" aria-labelledby="headingRomaneio" data-bs-parent="#accordionLayouts">
                                <div class="accordion-body">
                                    <p class="mb-2">O arquivo deve ser um CSV/TXT contendo apenas a chave da nota fiscal.</p>
                                    <code>chave</code>
                                    <p class="mt-2 mb-1">Exemplo:</p>
                                    <pre class="bg-light p-2 rounded">35220948996461000139550010000123451000123458</pre>
                                </div>
                            </div>
                        </div>
                        <!-- Financeiro -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingFinanceiro">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFinanceiro" aria-expanded="false" aria-controls="collapseFinanceiro">
                                    Layout do Arquivo Financeiro
                                </button>
                            </h2>
                            <div id="collapseFinanceiro" class="accordion-collapse collapse" aria-labelledby="headingFinanceiro" data-bs-parent="#accordionLayouts">
                                <div class="accordion-body">
                                    <p class="mb-2">O arquivo deve ser um CSV/TXT com as seguintes colunas separadas por ponto e vírgula (;):</p>
                                    <code>numero_finan;nf;valor</code>
                                    <p class="mt-2 mb-1">Exemplo:</p>
                                    <pre class="bg-light p-2 rounded">261;9060;1734,67
262;9060;1734,67
263;9060;1734,65
264;1412;203,76
281;180220250;139,2
282;;341,04
283;267031;720,8</pre>
                                    <div class="alert alert-info mt-3">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <strong>Importante:</strong> 
                                        <ul class="mb-0 mt-2">
                                            <li><strong>numero_finan:</strong> Número financeiro (primeira coluna)</li>
                                            <li><strong>nf:</strong> Número da nota fiscal (segunda coluna - pode ser nulo)</li>
                                            <li><strong>valor:</strong> Valor decimal (terceira coluna)</li>
                                            <li><strong>empresa_id:</strong> Será preenchido automaticamente com a empresa selecionada na página</li>
                                            <li>Use ponto e vírgula (;) como separador</li>
                                            <li>Para valores nulos, deixe o campo em branco</li>
                                            <li>A coluna hash será gerada automaticamente concatenando nf e valor</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Mensagem de Desenvolvimento -->
    <div id="mensagem_desenvolvimento" class="alert alert-info d-none" role="alert">
        <h4 class="alert-heading"><i class="fas fa-tools me-2"></i>Integração em Desenvolvimento</h4>
        <p>A integração com este sistema está em desenvolvimento. Em breve estará disponível para uso.</p>
    </div>

    <div class="card mb-4" id="card_upload">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-upload me-1"></i>
                Enviar Arquivos
            </div>
            <button type="button" class="btn btn-success" id="btn_enviar_todos">
                <i class="fas fa-paper-plane me-1"></i> Enviar Arquivos
            </button>
        </div>
        <div class="card-body">
            <div class="row" style="height: 600px; min-height: 400px;">
                <!-- Coluna da Esquerda -->
                <div class="col-md-6 d-flex flex-column" style="height: 100%;">
                    <div class="flex-fill mb-3 d-flex">
                        <div class="card card-upload w-100 h-100 shadow-sm">
                            <div class="card-header">
                                <i class="fas fa-file-invoice me-1"></i> Notas Sefaz
                            </div>
                            <div class="card-body p-2">
                                <form action="/arquivos/upload" method="post" enctype="multipart/form-data" id="form_sefaz" class="upload-form">
                                    <input type="hidden" name="modelo" value="sefaz">
                                    <input type="hidden" name="empresa_id" id="empresa_id_hidden_sefaz">
                                    <input type="hidden" name="sistema_integrado" id="sistema_integrado_hidden_sefaz">
                                    <div class="mb-3">
                                        <label for="arquivo_sefaz" class="form-label">Arquivo</label>
                                        <input type="file" class="form-control arquivo-input" id="arquivo_sefaz" name="arquivo" accept=".csv">
                                        <div class="form-text">Tipo permitido: CSV (máx. 10MB)</div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="flex-fill d-flex">
                        <div class="card card-upload w-100 h-100 shadow-sm">
                            <div class="card-header">
                                <i class="fas fa-file-alt me-1"></i> Lançamentos
                            </div>
                            <div class="card-body p-2">
                                <form action="/arquivos/upload" method="post" enctype="multipart/form-data" id="form_notas" class="upload-form">
                                    <input type="hidden" name="modelo" value="notas">
                                    <input type="hidden" name="empresa_id" id="empresa_id_hidden_notas">
                                    <input type="hidden" name="sistema_integrado" id="sistema_integrado_hidden_notas">
                                    <div class="mb-3">
                                        <label for="arquivo_notas" class="form-label">Arquivo</label>
                                        <input type="file" class="form-control arquivo-input" id="arquivo_notas" name="arquivo" accept=".txt">
                                        <div class="form-text">Tipo permitido: TXT (máx. 10MB)</div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Coluna da Direita -->
                <div class="col-md-6 d-flex flex-column" style="height: 100%;">
                    <div class="flex-fill d-flex mb-3">
                        <div class="card card-upload w-100 h-100 shadow-sm">
                            <div class="card-header">
                                <i class="fas fa-users me-1"></i> Fornecedores
                            </div>
                            <div class="card-body p-2">
                                <form action="/arquivos/upload" method="post" enctype="multipart/form-data" id="form_forn" class="upload-form">
                                    <input type="hidden" name="modelo" value="fornecedores">
                                    <input type="hidden" name="empresa_id" id="empresa_id_hidden_forn">
                                    <input type="hidden" name="sistema_integrado" id="sistema_integrado_hidden_forn">
                                    <div class="mb-3">
                                       
                                        <input type="file" class="form-control arquivo-input" id="arquivo_forn" name="arquivo" accept=".txt">
                                       
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="flex-fill d-flex mb-3">
                        <div class="card card-upload w-100 h-100 shadow-sm">
                            <div class="card-header">
                                <i class="fas fa-question-circle me-1"></i> Desconhecimento
                            </div>
                            <div class="card-body p-2">
                                <form action="/arquivos/upload" method="post" enctype="multipart/form-data" id="form_desc" class="upload-form">
                                    <input type="hidden" name="modelo" value="desconhecimento">
                                    <input type="hidden" name="empresa_id" id="empresa_id_hidden_desc">
                                    <input type="hidden" name="sistema_integrado" id="sistema_integrado_hidden_desc">
                                    <div class="mb-3">
                                        
                                        <input type="file" class="form-control arquivo-input" id="arquivo_desc" name="arquivo" accept=".txt">
                                        
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="flex-fill d-flex mb-3">
                        <div class="card card-upload w-100 h-100 shadow-sm">
                            <div class="card-header">
                                <i class="fas fa-truck me-1"></i> Romaneio
                            </div>
                            <div class="card-body p-2">
                                <form action="/arquivos/upload" method="post" enctype="multipart/form-data" id="form_rom" class="upload-form">
                                    <input type="hidden" name="modelo" value="romaneio">
                                    <input type="hidden" name="empresa_id" id="empresa_id_hidden_rom">
                                    <input type="hidden" name="sistema_integrado" id="sistema_integrado_hidden_rom">
                                    <div class="mb-3">
                                        
                                        <input type="file" class="form-control arquivo-input" id="arquivo_rom" name="arquivo" accept=".txt">
                                    
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="flex-fill d-flex">
                        <div class="card card-upload w-100 h-100 shadow-sm">
                            <div class="card-header">
                                <i class="fas fa-money-bill-wave me-1"></i> Financeiro
                            </div>
                            <div class="card-body p-2">
                                <form action="/arquivos/upload" method="post" enctype="multipart/form-data" id="form_fin" class="upload-form">
                                    <input type="hidden" name="modelo" value="financeiro">
                                    <input type="hidden" name="empresa_id" id="empresa_id_hidden_fin">
                                    <input type="hidden" name="sistema_integrado" id="sistema_integrado_hidden_fin">
                                    <div class="mb-3">
                                      
                                        <input type="file" class="form-control arquivo-input" id="arquivo_fin" name="arquivo" accept=".txt,.csv">
                                        
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sincronizar seleção global de loja com todos os formulários
    function syncEmpresaId() {
        var empresaId = document.getElementById('empresa_id_global').value;
        var sistemaIntegrado = document.getElementById('sistema_integrado').value;
        document.getElementById('empresa_id_hidden_notas').value = empresaId;
        document.getElementById('empresa_id_hidden_forn').value = empresaId;
        document.getElementById('empresa_id_hidden_desc').value = empresaId;
        document.getElementById('empresa_id_hidden_rom').value = empresaId;
        document.getElementById('empresa_id_hidden_sefaz').value = empresaId;
        document.getElementById('empresa_id_hidden_fin').value = empresaId;
        document.getElementById('sistema_integrado_hidden_notas').value = sistemaIntegrado;
        document.getElementById('sistema_integrado_hidden_forn').value = sistemaIntegrado;
        document.getElementById('sistema_integrado_hidden_desc').value = sistemaIntegrado;
        document.getElementById('sistema_integrado_hidden_rom').value = sistemaIntegrado;
        document.getElementById('sistema_integrado_hidden_sefaz').value = sistemaIntegrado;
        document.getElementById('sistema_integrado_hidden_fin').value = sistemaIntegrado;

        // Controlar visibilidade dos elementos baseado no sistema selecionado
        var cardUpload = document.getElementById('card_upload');
        var mensagemDesenvolvimento = document.getElementById('mensagem_desenvolvimento');
        var btnInstrucoes = document.getElementById('btn_instrucoes');
        
        if (sistemaIntegrado === 'arius') {
            cardUpload.classList.remove('d-none');
            mensagemDesenvolvimento.classList.add('d-none');
            btnInstrucoes.classList.remove('d-none');
        } else if (sistemaIntegrado) {
            cardUpload.classList.add('d-none');
            mensagemDesenvolvimento.classList.remove('d-none');
            btnInstrucoes.classList.add('d-none');
        } else {
            cardUpload.classList.add('d-none');
            mensagemDesenvolvimento.classList.add('d-none');
            btnInstrucoes.classList.add('d-none');
        }
    }

    var selectEmpresa = document.getElementById('empresa_id_global');
    selectEmpresa.addEventListener('change', syncEmpresaId);
    selectEmpresa.addEventListener('input', syncEmpresaId);
    
    var selectSistema = document.getElementById('sistema_integrado');
    selectSistema.addEventListener('change', syncEmpresaId);
    
    syncEmpresaId();

    // Função para enviar um formulário via AJAX
    function enviarFormulario(form) {
        return new Promise((resolve, reject) => {
            var formData = new FormData(form);
            
            fetch(form.action, {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                resolve(data);
            })
            .catch(error => {
                reject(error);
            });
        });
    }

    // Função para enviar todos os arquivos
    document.getElementById('btn_enviar_todos').addEventListener('click', async function() {
        var empresaId = document.getElementById('empresa_id_global').value;
        var sistemaIntegrado = document.getElementById('sistema_integrado').value;
        var btnEnviar = this;

        if (!empresaId) {
            alert('Selecione a loja antes de enviar os arquivos!');
            return;
        }
        if (!sistemaIntegrado) {
            alert('Selecione o sistema integrado antes de enviar os arquivos!');
            return;
        }

        // Verificar se pelo menos um arquivo foi selecionado
        var arquivosSelecionados = document.querySelectorAll('.arquivo-input');
        var temArquivo = false;
        arquivosSelecionados.forEach(function(input) {
            if (input.files.length > 0) {
                temArquivo = true;
            }
        });

        if (!temArquivo) {
            alert('Selecione pelo menos um arquivo para enviar!');
            return;
        }

        // Desabilitar o botão durante o envio
        btnEnviar.disabled = true;
        btnEnviar.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Enviando...';

        try {
            // Coletar todos os formulários com arquivos
            var forms = Array.from(document.querySelectorAll('.upload-form')).filter(form => {
                var input = form.querySelector('.arquivo-input');
                return input.files.length > 0;
            });

            // Enviar cada formulário sequencialmente
            for (let i = 0; i < forms.length; i++) {
                const form = forms[i];
                const tipo = form.querySelector('input[name="modelo"]').value;
                const input = form.querySelector('.arquivo-input');
                
                // Atualizar o texto do botão para mostrar o progresso
                btnEnviar.innerHTML = `<i class="fas fa-spinner fa-spin me-1"></i> Enviando ${tipo} (${i + 1}/${forms.length})...`;
                
                // Enviar o formulário
                await enviarFormulario(form);
            }

            // Sucesso
            btnEnviar.innerHTML = '<i class="fas fa-check me-1"></i> Arquivos Enviados!';
            setTimeout(() => {
                btnEnviar.innerHTML = '<i class="fas fa-paper-plane me-1"></i> Enviar Todos os Arquivos';
                btnEnviar.disabled = false;
            }, 2000);

        } catch (error) {
            // Erro
            console.error('Erro ao enviar arquivos:', error);
            btnEnviar.innerHTML = '<i class="fas fa-exclamation-triangle me-1"></i> Erro ao Enviar!';
            setTimeout(() => {
                btnEnviar.innerHTML = '<i class="fas fa-paper-plane me-1"></i> Enviar Todos os Arquivos';
                btnEnviar.disabled = false;
            }, 2000);
            alert('Ocorreu um erro ao enviar os arquivos. Por favor, tente novamente.');
        }
    });
});
</script>

<style>
.card-upload {
    min-height: 0;
    padding: 0.75rem 0.75rem 0.5rem 0.75rem;
}
.card-upload .card-header {
    background: #2c3e50 !important;
    color: #fff !important;
    padding: 0.5rem 1rem;
    font-size: 1.1rem;
    border-bottom: 1px solid #263445;
}
.btn-upload {
    background: #2c3e50 !important;
    color: #fff !important;
    border: none;
}
.btn-upload:hover, .btn-upload:focus {
    background: #1a232c !important;
    color: #fff !important;
}

/* Estilos para o botão de instruções e modal */
.btn-info {
    background: #3498db !important;
    color: #fff !important;
    border: none;
    padding: 0.5rem 1rem;
    font-size: 0.95rem;
    border-radius: 6px;
    box-shadow: 0 2px 4px rgba(52, 152, 219, 0.1);
    transition: all 0.2s ease;
}
.btn-info:hover, .btn-info:focus {
    background: #2980b9 !important;
    box-shadow: 0 4px 8px rgba(52, 152, 219, 0.2);
}

#modalInstrucoes .modal-content {
    border-radius: 12px;
    border: none;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

#modalInstrucoes .modal-header {
    background: #f8f9fa;
    border-bottom: 1px solid #e9ecef;
    border-radius: 12px 12px 0 0;
    padding: 1.25rem;
}

#modalInstrucoes .modal-title {
    color: #2c3e50;
    font-weight: 600;
}

#modalInstrucoes .modal-body {
    padding: 1.5rem;
}

#modalInstrucoes .accordion-button {
    background: #f8f9fa;
    color: #2c3e50;
    font-weight: 500;
    padding: 1rem 1.25rem;
}

#modalInstrucoes .accordion-button:not(.collapsed) {
    background: #e9ecef;
    color: #2c3e50;
}

#modalInstrucoes code {
    background: #f8f9fa;
    color: #e83e8c;
    padding: 0.2rem 0.4rem;
    border-radius: 4px;
    font-size: 0.9em;
}

#modalInstrucoes pre {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 6px;
    padding: 0.75rem;
    margin-top: 0.5rem;
    font-size: 0.85em;
    color: #2c3e50;
}

#modalInstrucoes .modal-footer {
    border-top: 1px solid #e9ecef;
    padding: 1rem 1.5rem;
    background: #f8f9fa;
    border-radius: 0 0 12px 12px;
}

#modalInstrucoes .btn-secondary {
    background: #6c757d;
    color: #fff;
    border: none;
    padding: 0.5rem 1.5rem;
    font-size: 0.95rem;
    border-radius: 6px;
    transition: all 0.2s ease;
}

#modalInstrucoes .btn-secondary:hover {
    background: #5a6268;
}

.row[style] {
    min-height: 400px;
    height: 600px;
}
.col-md-6.d-flex.flex-column > .flex-fill {
    min-height: 0;
}

.btn-success {
    background: #2ecc71 !important;
    color: #fff !important;
    border: none;
    padding: 0.5rem 1rem;
    font-size: 0.95rem;
    border-radius: 6px;
    box-shadow: 0 2px 4px rgba(46, 204, 113, 0.1);
    transition: all 0.2s ease;
}
.btn-success:hover, .btn-success:focus {
    background: #27ae60 !important;
    box-shadow: 0 4px 8px rgba(46, 204, 113, 0.2);
}

.btn-success:disabled {
    background: #95a5a6 !important;
    cursor: not-allowed;
}
</style> 