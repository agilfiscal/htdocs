<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="fas fa-search me-2"></i>
                        Consulta de CNPJ
                    </h4>
                    <p class="card-subtitle text-muted">
                        Consulte informações detalhadas de empresas através do CNPJ usando a API pública do CNPJa
                    </p>
                </div>
                <div class="card-body">
                    <!-- Formulário de Consulta -->
                    <form id="formConsultaCnpj" class="mb-4">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="cnpj" class="form-label">
                                        <i class="fas fa-building me-1"></i>
                                        CNPJ
                                    </label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="cnpj" 
                                           name="cnpj" 
                                           placeholder="00.000.000/0000-00"
                                           maxlength="18"
                                           required>
                                    <div class="form-text">
                                        Digite o CNPJ com ou sem formatação
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search me-1"></i>
                                    Consultar
                                </button>
                                <button type="button" class="btn btn-secondary ms-2" id="btnLimpar">
                                    <i class="fas fa-eraser me-1"></i>
                                    Limpar
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Loading -->
                    <div id="loading" class="text-center" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Carregando...</span>
                        </div>
                        <p class="mt-2">Consultando CNPJ...</p>
                    </div>

                    <!-- Resultado da Consulta -->
                    <div id="resultado" style="display: none;">
                        <div class="card">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-check-circle me-2"></i>
                                    Dados da Empresa
                                </h5>
                            </div>
                            <div class="card-body">
                                <!-- Botões de Ação -->
                                <div class="row mb-3" id="botoesAcao" style="display: none;">
                                    <div class="col-12">
                                        <div class="d-flex gap-2">
                                            <button type="button" id="btnCompartilharWhatsApp" class="btn btn-success btn-sm">
                                                <i class="fab fa-whatsapp me-1"></i>
                                                Compartilhar WhatsApp
                                            </button>
                                            <button type="button" id="btnCopiarClipboard" class="btn btn-primary btn-sm">
                                                <i class="fas fa-copy me-1"></i>
                                                Copiar Dados
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <!-- Informações Básicas -->
                                    <div class="col-md-6">
                                        <h6 class="text-primary">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Informações Básicas
                                        </h6>
                                        <table class="table table-sm">
                                            <tr>
                                                <td><strong>CNPJ:</strong></td>
                                                <td id="cnpj-resultado"></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Razão Social:</strong></td>
                                                <td id="razao-social"></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Nome Fantasia:</strong></td>
                                                <td id="nome-fantasia"></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Data de Abertura:</strong></td>
                                                <td id="data-abertura"></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Status:</strong></td>
                                                <td id="status-empresa"></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Natureza Jurídica:</strong></td>
                                                <td id="natureza-juridica"></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Porte:</strong></td>
                                                <td id="porte-empresa"></td>
                                            </tr>
                                        </table>
                                    </div>

                                    <!-- Endereço -->
                                    <div class="col-md-6">
                                        <h6 class="text-primary">
                                            <i class="fas fa-map-marker-alt me-1"></i>
                                            Endereço
                                        </h6>
                                        <table class="table table-sm">
                                            <tr>
                                                <td><strong>Logradouro:</strong></td>
                                                <td id="logradouro"></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Número:</strong></td>
                                                <td id="numero"></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Bairro:</strong></td>
                                                <td id="bairro"></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Cidade:</strong></td>
                                                <td id="cidade"></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Estado:</strong></td>
                                                <td id="estado"></td>
                                            </tr>
                                            <tr>
                                                <td><strong>CEP:</strong></td>
                                                <td id="cep"></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <!-- Contato -->
                                    <div class="col-md-6">
                                        <h6 class="text-primary">
                                            <i class="fas fa-phone me-1"></i>
                                            Contato
                                        </h6>
                                        <table class="table table-sm">
                                            <tr>
                                                <td><strong>Telefone:</strong></td>
                                                <td id="telefone"></td>
                                            </tr>
                                            <tr>
                                                <td><strong>E-mail:</strong></td>
                                                <td id="email"></td>
                                            </tr>
                                        </table>
                                    </div>

                                    <!-- Regime Tributário -->
                                    <div class="col-md-6">
                                        <h6 class="text-primary">
                                            <i class="fas fa-chart-pie me-1"></i>
                                            Regime Tributário
                                        </h6>
                                        <table class="table table-sm">
                                            <tr>
                                                <td><strong>Regime:</strong></td>
                                                <td id="regime-tributario"></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Simples Nacional:</strong></td>
                                                <td id="simples-status"></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <!-- CNAE -->
                                    <div class="col-md-6">
                                        <h6 class="text-primary">
                                            <i class="fas fa-industry me-1"></i>
                                            CNAE
                                        </h6>
                                        <table class="table table-sm">
                                            <tr>
                                                <td><strong>CNAE Principal:</strong></td>
                                                <td id="cnae-principal"></td>
                                            </tr>
                                            <tr>
                                                <td><strong>CNAE Secundários:</strong></td>
                                                <td id="cnae-secundarios"></td>
                                            </tr>
                                        </table>
                                    </div>

                                    <!-- Sócios e Administradores -->
                                    <div class="col-md-6">
                                        <h6 class="text-primary">
                                            <i class="fas fa-users me-1"></i>
                                            Sócios e Administradores
                                        </h6>
                                        <div id="socios-administradores">
                                            <!-- Será preenchido via JavaScript -->
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <!-- Inscrições Estaduais -->
                                    <div class="col-12">
                                        <h6 class="text-primary">
                                            <i class="fas fa-file-alt me-1"></i>
                                            Inscrições Estaduais
                                        </h6>
                                        <div id="inscricoes-estaduais">
                                            <!-- Será preenchido via JavaScript -->
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <!-- Capital Social -->
                                    <div class="col-md-6">
                                        <h6 class="text-primary">
                                            <i class="fas fa-dollar-sign me-1"></i>
                                            Capital Social
                                        </h6>
                                        <table class="table table-sm">
                                            <tr>
                                                <td><strong>Valor:</strong></td>
                                                <td id="capital-social"></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Erro -->
                    <div id="erro" class="alert alert-danger" style="display: none;">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <span id="mensagem-erro"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formConsultaCnpj');
    const cnpjInput = document.getElementById('cnpj');
    const loading = document.getElementById('loading');
    const resultado = document.getElementById('resultado');
    const erro = document.getElementById('erro');
    const btnLimpar = document.getElementById('btnLimpar');

    // Máscara para CNPJ
    if (typeof IMask !== 'undefined') {
        IMask(cnpjInput, {
            mask: '00.000.000/0000-00'
        });
    }

    // Submissão do formulário
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const cnpj = cnpjInput.value.replace(/\D/g, '');
        
        if (cnpj.length !== 14) {
            mostrarErro('CNPJ inválido. Digite um CNPJ com 14 dígitos.');
            return;
        }

        consultarCnpj(cnpj);
    });

    // Botão limpar
    btnLimpar.addEventListener('click', function() {
        cnpjInput.value = '';
        ocultarResultados();
    });

    function consultarCnpj(cnpj) {
        mostrarLoading();
        ocultarResultados();

        const formData = new FormData();
        formData.append('cnpj', cnpj);

        fetch('/extras/buscar-cnpj', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            ocultarLoading();
            
            if (data.erro) {
                mostrarErro(data.erro);
            } else if (data.sucesso) {
                exibirResultado(data.dados);
            } else {
                mostrarErro('Erro ao consultar CNPJ. Tente novamente.');
            }
        })
        .catch(error => {
            ocultarLoading();
            mostrarErro('Erro na comunicação com o servidor.');
            console.error('Erro:', error);
        });
    }

    function exibirResultado(dados) {
        // Armazena os dados na variável global para compartilhamento
        dadosConsulta = dados;
        
        // Informações básicas
        document.getElementById('cnpj-resultado').textContent = formatarCnpj(dados.taxId);
        document.getElementById('razao-social').textContent = dados.company.name || 'Não informado';
        document.getElementById('nome-fantasia').textContent = dados.alias || 'Não informado';
        document.getElementById('data-abertura').textContent = formatarData(dados.founded);
        document.getElementById('status-empresa').textContent = dados.status.text;
        document.getElementById('natureza-juridica').textContent = dados.company.nature.text;
        document.getElementById('porte-empresa').textContent = dados.company.size.text;

        // Endereço
        document.getElementById('logradouro').textContent = dados.address.street || 'Não informado';
        document.getElementById('numero').textContent = dados.address.number || 'Não informado';
        document.getElementById('bairro').textContent = dados.address.district || 'Não informado';
        document.getElementById('cidade').textContent = dados.address.city || 'Não informado';
        document.getElementById('estado').textContent = dados.address.state || 'Não informado';
        document.getElementById('cep').textContent = formatarCep(dados.address.zip);

        // Contato
        const telefone = dados.phones && dados.phones.length > 0 ? 
            `(${dados.phones[0].area}) ${dados.phones[0].number}` : 'Não informado';
        document.getElementById('telefone').textContent = telefone;

        const email = dados.emails && dados.emails.length > 0 ? 
            dados.emails[0].address : 'Não informado';
        document.getElementById('email').textContent = email;

        // CNAE
        document.getElementById('cnae-principal').textContent = formatarCnae(dados.mainActivity);
        
        const cnaeSecundarios = dados.sideActivities ? 
            dados.sideActivities.map(a => formatarCnae(a)).join(', ') : 'Não informado';
        document.getElementById('cnae-secundarios').textContent = cnaeSecundarios;

        // Regime Tributário
        let regimeTributario = 'Não informado';
        if (dados.company.simples && dados.company.simples.optant) {
            regimeTributario = 'Simples Nacional';
        } else if (dados.company.simei && dados.company.simei.optant) {
            regimeTributario = 'MEI (Microempreendedor Individual)';
        } else {
            // Determina o regime baseado na natureza jurídica e porte
            if (dados.company.nature && dados.company.nature.id) {
                const natureId = dados.company.nature.id;
                const sizeId = dados.company.size ? dados.company.size.id : null;
                
                // Empresas de grande porte geralmente são Lucro Real
                if (sizeId === 5) { // Demais (grande porte)
                    regimeTributario = 'Lucro Real';
                } else if (natureId === 2046) { // Sociedade Anônima Aberta
                    regimeTributario = 'Lucro Real';
                } else if (natureId === 2047) { // Sociedade Anônima Fechada
                    regimeTributario = 'Lucro Presumido ou Real';
                } else if (natureId === 2060) { // Sociedade Empresária Limitada
                    regimeTributario = 'Lucro Presumido ou Real';
                } else {
                    regimeTributario = 'A definir conforme faturamento';
                }
            }
        }
        document.getElementById('regime-tributario').textContent = regimeTributario;

        // Status do Simples Nacional
        let simplesStatus = 'Não optante';
        if (dados.company.simples && dados.company.simples.optant) {
            simplesStatus = dados.company.simples.since ? 
                `Optante desde ${formatarData(dados.company.simples.since)}` : 
                'Optante';
        } else if (dados.company.simei && dados.company.simei.optant) {
            simplesStatus = dados.company.simei.since ? 
                `MEI desde ${formatarData(dados.company.simei.since)}` : 
                'MEI';
        }
        document.getElementById('simples-status').textContent = simplesStatus;

        // Sócios e Administradores
        const sociosDiv = document.getElementById('socios-administradores');
        if (dados.company.members && dados.company.members.length > 0) {
            let html = '<table class="table table-sm table-striped">';
            html += '<thead><tr><th>Nome</th><th>Cargo</th><th>Desde</th><th>CPF</th></tr></thead><tbody>';
            
            dados.company.members.forEach(member => {
                let cpf = 'Não informado';
                if (member.person.taxId && member.person.taxId.length === 11) {
                    cpf = member.person.taxId.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
                } else if (member.person.taxId) {
                    cpf = member.person.taxId; // Se não tiver 11 dígitos, mostra como está
                }
                
                html += `<tr>
                    <td>${member.person.name}</td>
                    <td>${member.role.text}</td>
                    <td>${formatarData(member.since)}</td>
                    <td>${cpf}</td>
                </tr>`;
            });
            
            html += '</tbody></table>';
            sociosDiv.innerHTML = html;
        } else {
            sociosDiv.innerHTML = '<p class="text-muted">Nenhum sócio ou administrador encontrado.</p>';
        }

        // Inscrições estaduais
        const inscricoesDiv = document.getElementById('inscricoes-estaduais');
        if (dados.registrations && dados.registrations.length > 0) {
            let html = '<table class="table table-sm table-striped">';
            html += '<thead><tr><th>Estado</th><th>Número</th><th>Status</th><th>Tipo</th></tr></thead><tbody>';
            
            dados.registrations.forEach(reg => {
                html += `<tr>
                    <td>${reg.state}</td>
                    <td>${reg.number}</td>
                    <td><span class="badge ${reg.status.id === 1 ? 'bg-success' : 'bg-warning'}">${reg.status.text}</span></td>
                    <td>${reg.type.text}</td>
                </tr>`;
            });
            
            html += '</tbody></table>';
            inscricoesDiv.innerHTML = html;
        } else {
            inscricoesDiv.innerHTML = '<p class="text-muted">Nenhuma inscrição estadual encontrada.</p>';
        }



        // Capital Social
        const capital = dados.company.equity ? 
            `R$ ${parseFloat(dados.company.equity).toLocaleString('pt-BR', {minimumFractionDigits: 2})}` : 
            'Não informado';
        document.getElementById('capital-social').textContent = capital;

        // Mostra os botões de ação
        document.getElementById('botoesAcao').style.display = 'block';

        resultado.style.display = 'block';
    }

    function mostrarLoading() {
        loading.style.display = 'block';
    }

    function ocultarLoading() {
        loading.style.display = 'none';
    }

    function mostrarErro(mensagem) {
        document.getElementById('mensagem-erro').textContent = mensagem;
        erro.style.display = 'block';
    }

    function ocultarResultados() {
        resultado.style.display = 'none';
        erro.style.display = 'none';
        document.getElementById('botoesAcao').style.display = 'none';
        dadosConsulta = null;
    }

    function formatarCnpj(cnpj) {
        return cnpj.replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/, '$1.$2.$3/$4-$5');
    }

    function formatarCep(cep) {
        if (!cep) return 'Não informado';
        return cep.replace(/(\d{5})(\d{3})/, '$1-$2');
    }

    function formatarData(data) {
        if (!data) return 'Não informado';
        return new Date(data).toLocaleDateString('pt-BR');
    }

    function formatarCnae(cnae) {
        if (!cnae) return 'Não informado';
        if (typeof cnae === 'string') {
            return cnae;
        }
        return `${cnae.id} - ${cnae.text}`;
    }

    // Variável global para armazenar os dados da consulta
    let dadosConsulta = null;

    // Event listeners para os botões
    document.getElementById('btnCompartilharWhatsApp').addEventListener('click', compartilharWhatsApp);
    document.getElementById('btnCopiarClipboard').addEventListener('click', copiarParaClipboard);

    function compartilharWhatsApp() {
        if (!dadosConsulta) {
            alert('Nenhum dado disponível para compartilhar.');
            return;
        }

        const texto = formatarDadosParaCompartilhamento();
        const textoCodificado = encodeURIComponent(texto);
        
        // Detecta se é mobile ou desktop
        const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
        
        // URLs do WhatsApp
        const urlWhatsAppDesktop = `whatsapp://send?text=${textoCodificado}`;
        const urlWhatsAppWeb = `https://web.whatsapp.com/send?text=${textoCodificado}`;
        const urlWhatsAppMobile = `whatsapp://send?text=${textoCodificado}`;
        const urlWhatsAppFallback = `https://wa.me/?text=${textoCodificado}`;
        
        try {
            if (isMobile) {
                // Para dispositivos móveis
                const link = document.createElement('a');
                link.href = urlWhatsAppMobile;
                link.target = '_blank';
                link.rel = 'noopener noreferrer';
                link.click();
                
                // Fallback após 1 segundo se não conseguir abrir o app
                setTimeout(() => {
                    window.open(urlWhatsAppWeb, '_blank');
                }, 1000);
            } else {
                // Para desktop - tenta WhatsApp Desktop primeiro
                const link = document.createElement('a');
                link.href = urlWhatsAppDesktop;
                link.target = '_blank';
                link.rel = 'noopener noreferrer';
                link.click();
                
                // Fallback para WhatsApp Web após 1 segundo
                setTimeout(() => {
                    window.open(urlWhatsAppWeb, '_blank');
                }, 1000);
            }
            
            mostrarNotificacao('Abrindo WhatsApp...', 'success');
        } catch (error) {
            console.error('Erro ao abrir WhatsApp:', error);
            // Fallback final
            window.open(urlWhatsAppFallback, '_blank');
            mostrarNotificacao('Abrindo WhatsApp (modo alternativo)...', 'success');
        }
    }

    function copiarParaClipboard() {
        if (!dadosConsulta) {
            alert('Nenhum dado disponível para copiar.');
            return;
        }

        const texto = formatarDadosParaCompartilhamento();
        
        // Usa a API moderna do clipboard se disponível
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(texto).then(() => {
                mostrarNotificacao('Dados copiados para a área de transferência!', 'success');
            }).catch(err => {
                console.error('Erro ao copiar:', err);
                copiarViaFallback(texto);
            });
        } else {
            copiarViaFallback(texto);
        }
    }

    function copiarViaFallback(texto) {
        // Método fallback para navegadores mais antigos
        const textarea = document.createElement('textarea');
        textarea.value = texto;
        textarea.style.position = 'fixed';
        textarea.style.left = '-999999px';
        textarea.style.top = '-999999px';
        document.body.appendChild(textarea);
        textarea.focus();
        textarea.select();
        
        try {
            document.execCommand('copy');
            mostrarNotificacao('Dados copiados para a área de transferência!', 'success');
        } catch (err) {
            console.error('Erro ao copiar:', err);
            mostrarNotificacao('Erro ao copiar dados. Tente selecionar e copiar manualmente.', 'error');
        }
        
        document.body.removeChild(textarea);
    }

    function formatarDadosParaCompartilhamento() {
        const dados = dadosConsulta;
        let texto = `🏢 *DADOS DA EMPRESA*\n\n`;
        
        // Informações Básicas
        texto += `📋 *INFORMAÇÕES BÁSICAS*\n`;
        texto += `CNPJ: ${formatarCnpj(dados.taxId)}\n`;
        texto += `Razão Social: ${dados.company.name}\n`;
        texto += `Nome Fantasia: ${dados.alias || 'Não informado'}\n`;
        texto += `Data de Abertura: ${formatarData(dados.founded)}\n`;
        texto += `Status: ${dados.status.text}\n`;
        texto += `Natureza Jurídica: ${dados.company.nature.text}\n`;
        texto += `Porte: ${dados.company.size.text}\n\n`;
        
        // Endereço
        texto += `📍 *ENDEREÇO*\n`;
        texto += `${dados.address.street}, ${dados.address.number}\n`;
        texto += `Bairro: ${dados.address.district}\n`;
        texto += `${dados.address.city} - ${dados.address.state}\n`;
        texto += `CEP: ${formatarCep(dados.address.zip)}\n\n`;
        
        // CNAE
        texto += `🏭 *CNAE*\n`;
        texto += `Principal: ${formatarCnae(dados.mainActivity)}\n`;
        if (dados.sideActivities && dados.sideActivities.length > 0) {
            texto += `Secundários: ${dados.sideActivities.map(a => formatarCnae(a)).join(', ')}\n`;
        }
        texto += `\n`;
        
        // Regime Tributário
        texto += `📊 *REGIME TRIBUTÁRIO*\n`;
        const regimeElement = document.getElementById('regime-tributario');
        const simplesElement = document.getElementById('simples-status');
        texto += `Regime: ${regimeElement ? regimeElement.textContent : 'Não informado'}\n`;
        texto += `Simples: ${simplesElement ? simplesElement.textContent : 'Não informado'}\n\n`;
        
        // Contato
        if (dados.phones && dados.phones.length > 0) {
            texto += `📞 *CONTATO*\n`;
            texto += `Telefone: (${dados.phones[0].area}) ${dados.phones[0].number}\n`;
        }
        if (dados.emails && dados.emails.length > 0) {
            texto += `E-mail: ${dados.emails[0].address}\n`;
        }
        texto += `\n`;
        
        // Capital Social
        if (dados.company.equity) {
            texto += `💰 *CAPITAL SOCIAL*\n`;
            texto += `R$ ${parseFloat(dados.company.equity).toLocaleString('pt-BR', {minimumFractionDigits: 2})}\n\n`;
        }
        
        // Sócios
        if (dados.company.members && dados.company.members.length > 0) {
            texto += `👥 *SÓCIOS E ADMINISTRADORES*\n`;
            dados.company.members.forEach((member, index) => {
                texto += `${index + 1}. ${member.person.name}\n`;
                texto += `   Cargo: ${member.role.text}\n`;
                texto += `   Desde: ${formatarData(member.since)}\n`;
                if (member.person.taxId) {
                    const cpf = member.person.taxId.length === 11 ? 
                        member.person.taxId.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4') : 
                        member.person.taxId;
                    texto += `   CPF: ${cpf}\n`;
                }
                texto += `\n`;
            });
        }
        
        // Inscrições Estaduais
        if (dados.registrations && dados.registrations.length > 0) {
            texto += `📄 *INSCRIÇÕES ESTADUAIS*\n`;
            dados.registrations.forEach(reg => {
                const statusEmoji = reg.status.id === 1 ? '✅' : '⚠️';
                texto += `${statusEmoji} ${reg.state}: ${reg.number}\n`;
                texto += `   Status: ${reg.status.text}\n`;
                texto += `   Tipo: ${reg.type.text}\n\n`;
            });
        }
        
        texto += `\n📱 *Consulta realizada via AgilFiscal*`;
        texto += `\n🔗 *www.agilfiscal.com.br*`;
        
        return texto;
    }

    function mostrarNotificacao(mensagem, tipo) {
        // Cria uma notificação temporária
        const notificacao = document.createElement('div');
        notificacao.className = `alert alert-${tipo === 'success' ? 'success' : 'danger'} alert-dismissible fade show position-fixed`;
        notificacao.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        notificacao.innerHTML = `
            ${mensagem}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(notificacao);
        
        // Remove a notificação após 3 segundos
        setTimeout(() => {
            if (notificacao.parentNode) {
                notificacao.parentNode.removeChild(notificacao);
            }
        }, 3000);
    }
});
</script> 