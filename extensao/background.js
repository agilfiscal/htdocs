// Configuração da extensão
const config = {
    urls: [
        'https://www.nfe.fazenda.gov.br/*',
        'https://www.cte.fazenda.gov.br/*'
    ],
    urlFSist: [
        'https://www.fsist.com.br/',
        'https://www.fsist.com.br',
        'https://www.fsist.com.br/Default.aspx'
    ],
    urlsFazendaDownload: [
        'www.nfe.fazenda.gov.br/portal/downloadNFe.aspx',
        'www.cte.fazenda.gov.br/portal/downloadCTe.aspx'
    ],
    urlsTratarElementos: [
        'www.nfe.fazenda.gov.br/portal/consultaRecaptcha.aspx?tipoConsulta=resumo',
        'www.cte.fazenda.gov.br/portal/consultaRecaptcha.aspx?tipoConsulta=resumo'
    ],
    urlsTratarElementosHidden: [
        '#ctl00_ContentPlaceHolder1_txtChaveAcessoResumo',
        '#ctl00_ContentPlaceHolder1_btnConsultar',
        '#ctl00_ContentPlaceHolder1_btnConsultarHCaptcha',
        '#ctl00_ContentPlaceHolder1_btnLimpar',
        '#ctl00_ContentPlaceHolder1_btnLimparHCaptcha'
    ],
    urlsTratarElementosReplace: [
        '#ctl00_ContentPlaceHolder1_lblChaveAcesso'
    ],
    errosParaFechar: [
        'NF-e INEXISTENTE na base nacional',
        'CT-e INEXISTENTE na base nacional',
    ]
};

// Variáveis globais
let chaveGlobal = null;
let tabIdOpen = null;
let windowIdOpen = null;
let checkCertNFe = true;
let checkCertCTe = true;

// Função para verificar URLs
function urlsVerificar(texto, listaUrls) {
    if (texto != null) {
        for (let i = 0; i < listaUrls.length; i++) {
            if (texto.indexOf(listaUrls[i]) > -1) {
                return true;
            }
        }
    }
    return false;
}

// Função para verificar certificado
function checkCert() {
    if (chaveGlobal != null) {
        if (chaveGlobal.substr(20, 2) == '55') {
            return checkCertNFe;
        } else if (chaveGlobal.substr(20, 2) == '57') {
            return checkCertCTe;
        }
    }
    return false;
}

// Função para definir certificado
function checkCertSet(value) {
    if (chaveGlobal != null) {
        if (chaveGlobal.substr(20, 2) == '55') {
            checkCertNFe = value;
        } else if (chaveGlobal.substr(20, 2) == '57') {
            checkCertCTe = value;
        }
    }
}

// Listener para headers de resposta
chrome.webRequest.onHeadersReceived.addListener(function(details) {
    function removerItem(responseHeaders, name) {
        var index = responseHeaders
            .findIndex(function(value) { return value.name.toLowerCase() == name.toLowerCase(); });
        if (index != -1) {
            responseHeaders.splice(index, 1);
        }
    }

    function responseHeadersGetValue(responseHeaders, name) {
        var index = responseHeaders
            .findIndex(function(value) { return value.name.toLowerCase() == name.toLowerCase(); });
        if (index > -1) {
            return responseHeaders[index].value;
        } else {
            return null;
        }
    }

    if (tabIdOpen != null) {
        var Location = responseHeadersGetValue(details.responseHeaders, "Location");
        if (urlsVerificar(Location, config.urlsFazendaDownload)) {
            enviarLink(Location);
            getXML(Location).then(() => {
                Fechar();
            });
            return { cancel: true };
        } else if (details.url != null && urlsVerificar(details.url, config.urlsFazendaDownload)) {
            if (details.statusCode == 403) {
                Fechar();
                enviarXML('Erro 403, verifique o seu certificado digital. ' + JSON.stringify(details));
            }
        }
        removerItem(details.responseHeaders, 'x-frame-options');
        removerItem(details.responseHeaders, 'Content-Disposition');

        return { responseHeaders: details.responseHeaders };
    }
}, { urls: config.urls }, ['responseHeaders', 'extraHeaders']);

// Listener para requisições completadas
chrome.webRequest.onCompleted.addListener(
    function(data) {
        if (tabIdOpen == data.tabId && urlsVerificar(data.url, config.urlsTratarElementos)) {
            var tent = 0;
            function exec() {
                tent++;
                chrome.scripting.executeScript({
                    target: { tabId: data.tabId },
                    func: injectedFunction,
                    args: [JSON.stringify(config)]
                });
            }

            function injectedFunction(configString) {
                sessionStorage.setItem('AGIL_FISCAL_CONFIG', configString);
                if (document.getElementById('AgilFiscalTeste1') != null) {
                    document.getElementById('AgilFiscalTeste1').click();
                }
            }

            try {
                exec();
            } catch (error) {
                console.log(error);
                setTimeout(() => {
                    exec();
                }, 500);
            }
        }
    },
    { urls: config.urls }, ["responseHeaders"]
);

// Listener para mensagens
chrome.runtime.onMessage.addListener(
    function(request, sender, sendResponse) {
        console.log('Mensagem recebida:', request.tipo);

        if (request.tipo === "AbrirConsulta") {
            chaveGlobal = request.chave;
            const acao = request.acao || "visualizar"; // Padrão é visualizar
            console.log('Abrindo consulta para chave:', chaveGlobal, 'Ação:', acao);

            // URL base da SEFAZ
            let url = "https://www.nfe.fazenda.gov.br/portal/consultaRecaptcha.aspx?tipoConsulta=resumo";

            // Se for para baixar XML, adiciona parâmetro
            if (acao === "baixar_xml") {
                url += "&acao=xml";
            }

            chrome.windows.create({
                url: url,
                type: "popup",
                width: 1200,
                height: 900,
                focused: true
            }, (window) => {
                if (chrome.runtime.lastError) {
                    console.error('Erro ao criar janela:', chrome.runtime.lastError);
                    sendResponse({ success: false, error: chrome.runtime.lastError.message });
                    return;
                }

                tabIdOpen = window.tabs[0].id;
                windowIdOpen = window.id;
                console.log('Janela criada com sucesso. Tab ID:', tabIdOpen, 'Window ID:', windowIdOpen, 'Ação:', acao);
                sendResponse({ success: true, tabId: tabIdOpen, acao: acao });
            });
            return true; // Mantém a conexão aberta para resposta assíncrona
        } else if (request.tipo === "GetChave") {
            console.log('Retornando chave:', chaveGlobal);
            sendResponse({ chave: chaveGlobal });
        } else if (request.tipo === "SetHtml") {
            enviarHTML(request.html);
            sendResponse(true);
        } else if (request.tipo === "SetDados") {
            enviarDados(request.dados);
            sendResponse(true);
        } else if (request.tipo === "GetXML") {
            checkCertSet(true);
            Fechar();
            getXML(request.link);
            sendResponse(true);
        } else if (request.tipo === "Fechar") {
            Fechar();
            sendResponse(true);
        } else if (request.tipo === "SetConfig") {
            config = JSON.parse(request.configJson);
            sendResponse(true);
        } else {
            sendResponse(true);
        }
    }
);

// Função para obter XML com retry
async function getXML(url) {
    let attempt = 0;
    let maxAttempts = 30;
    let delay = 1000;

    // Função para aguardar o tempo especificado
    const sleep = ms => new Promise(resolve => setTimeout(resolve, ms));

    while (attempt < maxAttempts) {
        try {
            let response = await fetch(url); // Tenta fazer a requisição
            if (response.ok) {
                let xml = await response.text();
                enviarXML(xml);
                return; // Sucesso, sai da função
            } else {
                throw new Error(`Erro status: ${response.status}\r\nURL: ${url}`);
            }
        } catch (error) {
            // Só incrementa a tentativa e espera se for um erro de fetch
            if (attempt >= maxAttempts - 1) {
                throw new Error(`Erro ao fazer a requisição após ${maxAttempts} tentativas: ${error.message}\r\nURL: ${url}`);
            }
            attempt++;
            console.log(`Tentativa ${attempt} falhou. Tentando novamente em ${delay / 1000} segundos...`);
            await sleep(delay);
        }
    }
}

// Função para converter para base64 UTF-8
function base64utf8(str) {
    return btoa(encodeURIComponent(str).replace(/%([0-9A-F]{2})/g,
        function toSolidBytes(match, p1) {
            return String.fromCharCode('0x' + p1);
        }));
}

// Função para obter ID da aba FSist
function configurlFSistTabGetId(tabs) {
    for (let i = 0; i < tabs.length; i++) {
        const tab = tabs[i];
        if (tab.url != null) {
            for (let v = 0; v < config.urlFSist.length; v++) {
                const tempurlFsist = config.urlFSist[v];
                if (tab.url == tempurlFsist) {
                    return i;
                }
            }
        }
    }
    return -1;
}

// Função para enviar XML
function enviarXML(xml) {
    chrome.tabs.query({}, function(tabs) {
        var tempTabIndex = configurlFSistTabGetId(tabs);
        if (tempTabIndex > -1) {
            chrome.tabs.sendMessage(tabs[tempTabIndex].id, { message: "set_xml", content: base64utf8(xml) }, function(response) {});
        }
    });
}

// Função para enviar link
function enviarLink(link) {
    chrome.tabs.query({}, function(tabs) {
        var tempTabIndex = configurlFSistTabGetId(tabs);
        if (tempTabIndex > -1) {
            chrome.tabs.sendMessage(tabs[tempTabIndex].id, { message: "set_link", content: btoa(link) }, function(response) {});
        }
    });
}

// Função para enviar HTML
function enviarHTML(html) {
    chrome.tabs.query({}, function(tabs) {
        var tempTabIndex = configurlFSistTabGetId(tabs);
        if (tempTabIndex > -1) {
            chrome.tabs.sendMessage(tabs[tempTabIndex].id, { message: "set_html", content: base64utf8(html) }, function(response) {});
        }
    });
}

// Função para enviar dados
function enviarDados(dados) {
    chrome.tabs.query({}, function(tabs) {
        var tempTabIndex = configurlFSistTabGetId(tabs);
        if (tempTabIndex > -1) {
            chrome.tabs.sendMessage(tabs[tempTabIndex].id, { message: "set_dados", content: base64utf8(dados) }, function(response) {});
        }
    });
}

// Função para fechar janela
function Fechar() {
    if (tabIdOpen !== null) {
        chrome.tabs.remove(tabIdOpen);
        tabIdOpen = null;
        windowIdOpen = null;
    }
}

// Listener para quando janela é fechada
chrome.windows.onRemoved.addListener(function(windowsId, removeInfo) {
    if (windowsId == windowIdOpen) {
        windowIdOpen = null;
        tabIdOpen = null;
    }
});

console.log('Background script carregado com sucesso!');