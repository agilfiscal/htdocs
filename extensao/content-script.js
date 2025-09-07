console.log('Content script da extensão Ágil Fiscal carregado');

// Função para verificar se a extensão está disponível
function verificarExtensao() {
    return new Promise((resolve) => {
        if (typeof chrome !== 'undefined' && chrome.runtime && chrome.runtime.id) {
            chrome.runtime.sendMessage({ tipo: "Ping" }, (response) => {
                if (chrome.runtime.lastError) {
                    console.log('Extensão não disponível:', chrome.runtime.lastError.message);
                    resolve(false);
                } else {
                    console.log('Extensão disponível');
                    resolve(true);
                }
            });
        } else {
            console.log('Chrome runtime não disponível');
            resolve(false);
        }
    });
}

// Função para abrir consulta via extensão
function abrirConsultaExtensao(chave, acao = "visualizar") {
    return new Promise((resolve) => {
        chrome.runtime.sendMessage({
            tipo: "AbrirConsulta",
            chave: chave,
            acao: acao
        }, (response) => {
            if (chrome.runtime.lastError) {
                console.error('Erro ao abrir consulta:', chrome.runtime.lastError);
                resolve({ success: false, error: chrome.runtime.lastError.message });
            } else {
                console.log('Consulta aberta com sucesso:', response);
                resolve({ success: true, response: response });
            }
        });
    });
}

// Função para interceptar cliques no botão de visualizar nota
function interceptarBotaoVisualizar() {
    // Aguarda o DOM estar pronto
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', interceptarBotaoVisualizar);
        return;
    }

    // Procura pelo botão de visualizar nota
    const btnVisualizar = document.getElementById('btn-visualizar-danfe');
    
    if (btnVisualizar) {
        console.log('Botão de visualizar nota encontrado, interceptando...');
        
        // Remove o event listener original se existir
        const novoBtn = btnVisualizar.cloneNode(true);
        btnVisualizar.parentNode.replaceChild(novoBtn, btnVisualizar);
        
        // Adiciona o novo event listener
        novoBtn.addEventListener('click', async function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            console.log('Botão visualizar nota clicado');
            
            // Verifica se há uma nota selecionada
            const notaSelecionada = document.querySelector('input[name="nota_selecionada"]:checked');
            if (!notaSelecionada) {
                alert('Por favor, selecione uma nota fiscal primeiro.');
                return;
            }
            
            // Obtém os dados da nota
            const tr = notaSelecionada.closest('tr');
            const nota = JSON.parse(tr.getAttribute('data-nota'));
            const chave = (nota.chave || '').replace(/[^0-9]/g, '').trim();
            
            if (!chave || chave.length !== 44) {
                alert('Chave de acesso da nota inválida ou não encontrada.');
                return;
            }
            
            console.log('Chave da nota:', chave);
            
            // Verifica se a extensão está disponível
            const extensaoDisponivel = await verificarExtensao();
            
            if (extensaoDisponivel) {
                console.log('Usando extensão para abrir consulta');
                
                // Mostra feedback visual
                const btnOriginal = this.innerHTML;
                this.innerHTML = '<i class="bi bi-hourglass-split"></i> Abrindo...';
                this.disabled = true;
                
                try {
                    const resultado = await abrirConsultaExtensao(chave, "visualizar");
                    
                    if (resultado.success) {
                        console.log('Consulta aberta com sucesso via extensão');
                    } else {
                        console.error('Erro ao abrir consulta via extensão:', resultado.error);
                        alert('Erro ao abrir consulta via extensão. Tentando método alternativo...');
                        // Fallback para o método original
                        window.open(`/auditoria/danfe/${chave}`, '_blank');
                    }
                } catch (error) {
                    console.error('Erro inesperado:', error);
                    alert('Erro inesperado. Tentando método alternativo...');
                    window.open(`/auditoria/danfe/${chave}`, '_blank');
                } finally {
                    // Restaura o botão
                    this.innerHTML = btnOriginal;
                    this.disabled = false;
                }
            } else {
                console.log('Extensão não disponível, usando método original');
                // Fallback para o método original
                window.open(`/auditoria/danfe/${chave}`, '_blank');
            }
        });
        
        console.log('Interceptação do botão concluída');
    } else {
        console.log('Botão de visualizar nota não encontrado, tentando novamente em 1 segundo...');
        setTimeout(interceptarBotaoVisualizar, 1000);
    }
}

// Função para interceptar cliques no botão de baixar XML
function interceptarBotaoBaixarXml() {
    // Aguarda o DOM estar pronto
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', interceptarBotaoBaixarXml);
        return;
    }

    // Procura pelo botão de baixar XML
    const btnBaixarXml = document.getElementById('btn-baixar-xml');
    
    if (btnBaixarXml) {
        console.log('Botão de baixar XML encontrado, interceptando...');
        
        // Remove o event listener original se existir
        const novoBtn = btnBaixarXml.cloneNode(true);
        btnBaixarXml.parentNode.replaceChild(novoBtn, btnBaixarXml);
        
        // Adiciona o novo event listener
        novoBtn.addEventListener('click', async function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            console.log('Botão baixar XML clicado');
            
            // Verifica se há uma nota selecionada
            const notaSelecionada = document.querySelector('input[name="nota_selecionada"]:checked');
            if (!notaSelecionada) {
                alert('Por favor, selecione uma nota fiscal primeiro.');
                return;
            }
            
            // Obtém os dados da nota
            const tr = notaSelecionada.closest('tr');
            const nota = JSON.parse(tr.getAttribute('data-nota'));
            const chave = (nota.chave || '').replace(/[^0-9]/g, '').trim();
            
            if (!chave || chave.length !== 44) {
                alert('Chave de acesso da nota inválida ou não encontrada.');
                return;
            }
            
            console.log('Chave da nota para XML:', chave);
            
            // Verifica se a extensão está disponível
            const extensaoDisponivel = await verificarExtensao();
            
            if (extensaoDisponivel) {
                console.log('Usando extensão para baixar XML');
                
                // Mostra feedback visual
                const btnOriginal = this.innerHTML;
                this.innerHTML = '<i class="bi bi-hourglass-split"></i> Abrindo...';
                this.disabled = true;
                
                try {
                    const resultado = await abrirConsultaExtensao(chave, "baixar_xml");
                    
                    if (resultado.success) {
                        console.log('Consulta XML aberta com sucesso via extensão');
                    } else {
                        console.error('Erro ao abrir consulta XML via extensão:', resultado.error);
                        alert('Erro ao abrir consulta XML via extensão. Tentando método alternativo...');
                        // Fallback para o método original
                        window.open(`/auditoria/baixarXml/${chave}`, '_blank');
                    }
                } catch (error) {
                    console.error('Erro inesperado:', error);
                    alert('Erro inesperado. Tentando método alternativo...');
                    window.open(`/auditoria/baixarXml/${chave}`, '_blank');
                } finally {
                    // Restaura o botão
                    this.innerHTML = btnOriginal;
                    this.disabled = false;
                }
            } else {
                console.log('Extensão não disponível, usando método original para XML');
                // Fallback para o método original
                window.open(`/auditoria/baixarXml/${chave}`, '_blank');
            }
        });
        
        console.log('Interceptação do botão XML concluída');
    } else {
        console.log('Botão de baixar XML não encontrado, tentando novamente em 1 segundo...');
        setTimeout(interceptarBotaoBaixarXml, 1000);
    }
}

// Inicia a interceptação quando o script é carregado
interceptarBotaoVisualizar();
interceptarBotaoBaixarXml();

// Também tenta interceptar quando a página é carregada completamente
window.addEventListener('load', () => {
    console.log('Página carregada, verificando botões novamente...');
    setTimeout(interceptarBotaoVisualizar, 500);
    setTimeout(interceptarBotaoBaixarXml, 500);
});

// Listener para mensagens do background script
chrome.runtime.onMessage.addListener((request, sender, sendResponse) => {
    if (request.tipo === "Ping") {
        sendResponse({ status: "ok" });
        return true;
    }
}); 