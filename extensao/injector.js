console.log('Script de injeção carregado');

function preencherChaveAcesso() {
  // Verificar se estamos na página de resultado ANTES de qualquer coisa
  const currentUrl = window.location.href;
  const isResultPage = currentUrl.includes('consultaResumo.aspx') || 
                      currentUrl.includes('consulta.aspx');
  
  if (isResultPage) {
    console.log('🎯 Página de resultado detectada - INIBINDO preenchimento de chave');
    console.log('🚀 Iniciando monitoramento da página de resultado...');
    setTimeout(() => {
      monitorarPaginaResultado();
    }, 1000);
    return;
  }
  
chrome.runtime.sendMessage({ tipo: "GetChave" }, (response) => {
    const chave = response?.chave;
    if (chave && chave.length === 44) {
      setTimeout(() => {
      const input = document.querySelector("#ctl00_ContentPlaceHolder1_txtChaveAcessoResumo");
      if (input) {
        input.value = chave;
          input.dispatchEvent(new Event('input', { bubbles: true }));
          input.dispatchEvent(new Event('change', { bubbles: true }));
          input.focus();
          
          // Ocultar botões desnecessários após preencher a chave
          if (window.hideUnnecessaryButtons) {
            window.hideUnnecessaryButtons();
          }
          
          // Verificar se é uma ação de baixar XML
          const urlParams = new URLSearchParams(window.location.search);
          const acao = urlParams.get('acao');
          
          if (acao === 'xml') {
            console.log('Ação detectada: Baixar XML');
            // Para XML, iniciar monitoramento do captcha com download automático
            iniciarMonitoramentoCaptchaComDownload();
          } else {
            console.log('Ação detectada: Visualizar nota');
            iniciarMonitoramentoCaptcha();
          }
        }
      }, 1000);
    }
  });
}

function iniciarMonitoramentoCaptcha() {
  // Verificar se estamos na página de resultado
  if (window.AGIL_FISCAL_RESULT_PAGE) {
    console.log('🚫 INIBINDO monitoramento de captcha - página de resultado detectada');
    return;
  }
  
  console.log('Iniciando monitoramento do captcha...');
  setTimeout(() => {
    if (window.CaptchaMonitor) {
      const monitor = new window.CaptchaMonitor();
      monitor.start();
      console.log('Monitor de captcha iniciado');
    } else {
      console.log('Monitor de captcha não disponível, usando método básico');
      monitoramentoBasicoCaptcha();
    }
  }, 2000);
}

function monitoramentoBasicoCaptcha() {
  // Verificar se estamos na página de resultado
  if (window.AGIL_FISCAL_RESULT_PAGE) {
    console.log('🚫 INIBINDO monitoramento básico de captcha - página de resultado detectada');
    return;
  }
  
  console.log('Iniciando monitoramento básico do captcha...');
  
  let attempts = 0;
  const maxAttempts = 120; // 60 segundos
  
  const interval = setInterval(() => {
    attempts++;
    console.log(`Verificação básica ${attempts}/${maxAttempts}`);
    
    // Verificar se o captcha foi resolvido
    const hCaptchaElement = document.querySelector('.h-captcha[data-hcaptcha-response]');
    const iframeCaptcha = document.querySelector('iframe[src*="hcaptcha"]');
    
    if (hCaptchaElement && hCaptchaElement.getAttribute('data-hcaptcha-response') && !iframeCaptcha) {
      console.log('Captcha resolvido detectado (método básico)');
      clearInterval(interval);
      
      setTimeout(() => {
        const botaoContinuar = document.querySelector('#ctl00_ContentPlaceHolder1_btnConsultarHCaptcha');
        if (botaoContinuar) {
          try {
            botaoContinuar.click();
            console.log('Clique executado (método básico)');
          } catch (e) {
            console.log('Erro no clique (método básico):', e);
          }
        }
      }, 1000);
    } else if (attempts >= maxAttempts) {
      console.log('Tempo limite excedido (método básico)');
      clearInterval(interval);
    }
  }, 500);
}

// Função para monitorar captcha com download automático
function iniciarMonitoramentoCaptchaComDownload() {
  // Verificar se estamos na página de resultado
  if (window.AGIL_FISCAL_RESULT_PAGE) {
    console.log('🚫 INIBINDO monitoramento de captcha com download - página de resultado detectada');
    return;
  }
  
  console.log('Iniciando monitoramento do captcha com download automático...');
  setTimeout(() => {
    if (window.CaptchaMonitor) {
      const monitor = new window.CaptchaMonitor();
      monitor.start();
      console.log('Monitor de captcha iniciado com download automático');
      
      // Sobrescrever o método de captcha resolvido para incluir monitoramento de download
      const originalHandleCaptchaResolved = monitor.handleCaptchaResolved;
      monitor.handleCaptchaResolved = function() {
        console.log('Captcha resolvido - preparando para mudança de página...');
        // Salvar informação no sessionStorage antes da mudança de página
        sessionStorage.setItem('agil_fiscal_monitorar_download', 'true');
        if (originalHandleCaptchaResolved) {
          originalHandleCaptchaResolved.call(this);
        }
        // Iniciar monitoramento da página de resultado após clicar no continuar
        setTimeout(() => {
          console.log('Iniciando monitoramento da página de resultado após captcha resolvido...');
          monitorarPaginaResultado();
        }, 3000); // Aumentado para 3 segundos
      };
      
      // Também sobrescrever o método clickContinueButton para garantir
      const originalClickContinueButton = monitor.clickContinueButton;
      monitor.clickContinueButton = function() {
        console.log('Clicando no botão continuar e agendando monitoramento...');
        // Salvar informação no sessionStorage antes da mudança de página
        sessionStorage.setItem('agil_fiscal_monitorar_download', 'true');
        if (originalClickContinueButton) {
          originalClickContinueButton.call(this);
        }
        // Iniciar monitoramento da página de resultado após clicar no continuar
        setTimeout(() => {
          console.log('Iniciando monitoramento da página de resultado após clique no continuar...');
          monitorarPaginaResultado();
        }, 3000);
      };
    } else {
      console.log('Monitor de captcha não disponível, usando método básico com download');
      monitoramentoBasicoCaptchaComDownload();
    }
  }, 2000);
}

// Função para monitorar captcha básico com download
function monitoramentoBasicoCaptchaComDownload() {
  // Verificar se estamos na página de resultado
  if (window.AGIL_FISCAL_RESULT_PAGE) {
    console.log('🚫 INIBINDO monitoramento básico de captcha com download - página de resultado detectada');
    return;
  }
  
  console.log('Iniciando monitoramento básico do captcha com download...');
  
  let attempts = 0;
  const maxAttempts = 120; // 60 segundos
  
  const interval = setInterval(() => {
    attempts++;
    console.log(`Verificação básica com download ${attempts}/${maxAttempts}`);
    
    // Verificar se o captcha foi resolvido
    const hCaptchaElement = document.querySelector('.h-captcha[data-hcaptcha-response]');
    const iframeCaptcha = document.querySelector('iframe[src*="hcaptcha"]');
    
    if (hCaptchaElement && hCaptchaElement.getAttribute('data-hcaptcha-response') && !iframeCaptcha) {
      console.log('Captcha resolvido detectado (método básico com download)');
      clearInterval(interval);
      
      setTimeout(() => {
        const botaoContinuar = document.querySelector('#ctl00_ContentPlaceHolder1_btnConsultarHCaptcha');
        if (botaoContinuar) {
          try {
            botaoContinuar.click();
            console.log('Clique executado (método básico com download)');
            // Iniciar monitoramento da página de resultado
            setTimeout(() => {
              monitorarPaginaResultado();
            }, 2000);
          } catch (e) {
            console.log('Erro no clique (método básico com download):', e);
          }
        }
      }, 1000);
    } else if (attempts >= maxAttempts) {
      console.log('Tempo limite excedido (método básico com download)');
      clearInterval(interval);
    }
  }, 500);
}

// Função para monitorar a página de resultado e clicar no botão Download
function monitorarPaginaResultado() {
  console.log('🚀 Iniciando monitoramento da página de resultado...');
  console.log('URL atual:', window.location.href);
  
  let attempts = 0;
  const maxAttempts = 120; // 60 segundos (aumentado)
  
  const interval = setInterval(() => {
    attempts++;
    console.log(`🔍 Verificando página de resultado ${attempts}/${maxAttempts}`);
    
    // Verificar se estamos na página de resultado (múltiplos indicadores)
    const currentUrl = window.location.href;
    console.log('URL atual:', currentUrl);
    
    // Para a página consultaResumo.aspx, sempre considerar como página de resultado
    const isResultPage = currentUrl.includes('consultaResumo.aspx') || 
                        currentUrl.includes('consulta.aspx') ||
                        document.querySelector('#ctl00_ContentPlaceHolder1_btnDownload') ||
                        document.querySelector('input[value="Download do documento"]') ||
                        document.querySelector('input[value*="Download"]') ||
                        document.querySelector('input[value*="download"]');
    
    console.log('É página de resultado?', isResultPage);
    
    // Se estamos na página consultaResumo.aspx, forçar como página de resultado
    if (currentUrl.includes('consultaResumo.aspx')) {
      console.log('✅ Página consultaResumo.aspx detectada - forçando como página de resultado');
      console.log('🔍 Verificando se a página está completamente carregada...');
      
      // Verificar se o DOM está pronto
      if (document.readyState === 'complete' || document.readyState === 'interactive') {
        console.log('✅ DOM está pronto, continuando monitoramento...');
      } else {
        console.log('⏳ DOM ainda carregando, aguardando...');
        return; // Continuar no próximo intervalo
      }
    }
    
    if (isResultPage) {
      console.log('✅ Página de resultado detectada, procurando botão Download...');
      
      // Múltiplos métodos para encontrar o botão Download
      let botaoDownload = null;
      
      // Método 1: XPath específico
      try {
        const xpath = '//*[@id="ctl00_ContentPlaceHolder1_btnDownload"]';
        const result = document.evaluate(xpath, document, null, XPathResult.FIRST_ORDERED_NODE_TYPE, null);
        botaoDownload = result.singleNodeValue;
        if (botaoDownload) console.log('✅ Botão Download encontrado via XPath');
      } catch (e) {
        console.log('❌ Erro no XPath:', e);
      }
      
      // Método 2: ID direto
      if (!botaoDownload) {
        botaoDownload = document.querySelector('#ctl00_ContentPlaceHolder1_btnDownload');
        if (botaoDownload) console.log('✅ Botão Download encontrado via ID');
      }
      
      // Método 3: Valor do botão
      if (!botaoDownload) {
        botaoDownload = document.querySelector('input[value="Download do documento"]');
        if (botaoDownload) console.log('✅ Botão Download encontrado via valor');
      }
      
      // Método 4: Texto do botão
      if (!botaoDownload) {
        const buttons = document.querySelectorAll('input[type="submit"], button');
        console.log('Botões encontrados:', buttons.length);
        for (let btn of buttons) {
          const buttonText = (btn.value || btn.textContent || '').toLowerCase();
          console.log('Botão:', buttonText);
          if (buttonText.includes('download') || buttonText.includes('baixar') || buttonText.includes('xml')) {
            botaoDownload = btn;
            console.log('✅ Botão Download encontrado via texto:', buttonText);
            break;
          }
        }
      }
      
      // Método 5: Busca por qualquer elemento com "Download" no texto
      if (!botaoDownload) {
        const allElements = document.querySelectorAll('*');
        for (let element of allElements) {
          if (element.textContent && element.textContent.toLowerCase().includes('download do documento')) {
            console.log('✅ Elemento com "Download do documento" encontrado:', element);
            // Procurar o botão mais próximo
            const nearbyButton = element.closest('form')?.querySelector('input[type="submit"], button') ||
                               element.parentElement?.querySelector('input[type="submit"], button');
            if (nearbyButton) {
              botaoDownload = nearbyButton;
              console.log('✅ Botão Download encontrado próximo ao texto');
              break;
            }
          }
        }
      }
      
      if (botaoDownload) {
        console.log('🎯 Botão Download encontrado!', botaoDownload);
        clearInterval(interval);
        
        setTimeout(() => {
          try {
            // Método 1: Clique direto
            console.log('🖱️ Tentando clique direto...');
            botaoDownload.click();
            console.log('✅ Clique no botão Download executado com sucesso!');
          } catch (e) {
            console.log('❌ Erro ao clicar no botão Download:', e);
            try {
              // Método 2: Event dispatch
              console.log('🖱️ Tentando evento de clique...');
              botaoDownload.dispatchEvent(new Event('click', { bubbles: true }));
              console.log('✅ Clique alternativo no botão Download executado!');
            } catch (e2) {
              console.log('❌ Erro no clique alternativo:', e2);
              try {
                // Método 3: MouseEvent
                console.log('🖱️ Tentando MouseEvent...');
                botaoDownload.dispatchEvent(new MouseEvent('click', { bubbles: true }));
                console.log('✅ Clique MouseEvent no botão Download executado!');
              } catch (e3) {
                console.log('❌ Erro no clique MouseEvent:', e3);
              }
            }
          }
        }, 1000);
      } else {
        console.log('❌ Botão Download ainda não encontrado...');
        // Log adicional para debug
        if (attempts % 10 === 0) {
          console.log('📊 URL atual:', currentUrl);
          console.log('📊 Elementos encontrados:', document.querySelectorAll('input[type="submit"], button').length);
          console.log('📊 Todos os botões:', Array.from(document.querySelectorAll('input[type="submit"], button')).map(b => b.value || b.textContent));
        }
      }
    } else if (attempts >= maxAttempts) {
      console.log('⏰ Tempo limite excedido para página de resultado');
      clearInterval(interval);
    }
  }, 500);
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', preencherChaveAcesso);
} else {
  preencherChaveAcesso();
}

// Função removida - agora o download é automático após resolver o captcha

// Listener para mudanças de URL
let lastUrl = location.href;
new MutationObserver(() => {
  const url = location.href;
  if (url !== lastUrl) {
    lastUrl = url;
    console.log('🌐 URL mudou para:', url);
    
    // Se mudou para uma página de resultado, iniciar monitoramento
    if (!url.includes('consultaRecaptcha.aspx')) {
      console.log('🔄 Mudança de URL detectada - iniciando monitoramento da página de resultado');
      setTimeout(() => {
        monitorarPaginaResultado();
      }, 1000);
    }
  }
}).observe(document, { subtree: true, childList: true });

// Listener adicional para mudanças de URL usando popstate
window.addEventListener('popstate', () => {
  console.log('🔄 Popstate detectado - URL mudou para:', window.location.href);
  if (!window.location.href.includes('consultaRecaptcha.aspx')) {
    console.log('🔄 Popstate - iniciando monitoramento da página de resultado');
    setTimeout(() => {
      monitorarPaginaResultado();
    }, 1000);
  }
});

// Listener para mudanças de URL usando pushstate/replacestate
const originalPushState = history.pushState;
const originalReplaceState = history.replaceState;

history.pushState = function(...args) {
  originalPushState.apply(this, args);
  console.log('🔄 PushState detectado - URL mudou para:', window.location.href);
  if (!window.location.href.includes('consultaRecaptcha.aspx')) {
    console.log('🔄 PushState - iniciando monitoramento da página de resultado');
    setTimeout(() => {
      monitorarPaginaResultado();
    }, 1000);
  }
};

history.replaceState = function(...args) {
  originalReplaceState.apply(this, args);
  console.log('🔄 ReplaceState detectado - URL mudou para:', window.location.href);
  if (!window.location.href.includes('consultaRecaptcha.aspx')) {
    console.log('🔄 ReplaceState - iniciando monitoramento da página de resultado');
    setTimeout(() => {
      monitorarPaginaResultado();
    }, 1000);
  }
};

// Listener para mudanças de URL usando beforeunload
window.addEventListener('beforeunload', () => {
  console.log('🔄 Beforeunload detectado - página vai mudar');
  // Salvar informação no sessionStorage para a próxima página
  sessionStorage.setItem('agil_fiscal_monitorar_download', 'true');
});

// Listener para mudanças de URL usando unload
window.addEventListener('unload', () => {
  console.log('🔄 Unload detectado - página mudando');
  // Salvar informação no sessionStorage para a próxima página
  sessionStorage.setItem('agil_fiscal_monitorar_download', 'true');
});

// Listener para quando a página carrega
window.addEventListener('load', () => {
  console.log('🔄 Página carregada - verificando se deve monitorar download');
  console.log('URL atual:', window.location.href);
  
  // Verificar se estamos na página de resultado
  const currentUrl = window.location.href;
  const isResultPage = currentUrl.includes('consultaResumo.aspx') || 
                      currentUrl.includes('consulta.aspx');
  
  console.log('É página de resultado?', isResultPage);
  
  // Se estamos na página de resultado, NÃO iniciar monitoramento de captcha
  if (isResultPage) {
    window.AGIL_FISCAL_RESULT_PAGE = true;
    console.log('🎯 Página de resultado detectada - INIBINDO monitoramento de captcha');
    console.log('🔄 Monitoramento de download detectado - iniciando...');
    sessionStorage.removeItem('agil_fiscal_monitorar_download');
    setTimeout(() => {
      console.log('🚀 Iniciando monitoramento da página de resultado...');
      monitorarPaginaResultado();
    }, 2000);
    return; // IMPORTANTE: Sair aqui para não executar o resto
  }
  
  // Se temos flag de download ou não estamos na página de resultado
  if (sessionStorage.getItem('agil_fiscal_monitorar_download') === 'true') {
    console.log('🔄 Monitoramento de download detectado - iniciando...');
    sessionStorage.removeItem('agil_fiscal_monitorar_download');
    setTimeout(() => {
      console.log('🚀 Iniciando monitoramento da página de resultado...');
      monitorarPaginaResultado();
    }, 2000);
  } else {
    console.log('🔄 Página inicial - iniciando preenchimento da chave');
    setTimeout(preencherChaveAcesso, 500);
  }
});

// Função para verificar se estamos na página consultaResumo.aspx e iniciar monitoramento
function verificarPaginaResultado() {
  if (window.location.href.includes('consultaResumo.aspx')) {
    window.AGIL_FISCAL_RESULT_PAGE = true;
    console.log('🎯 Página consultaResumo.aspx detectada - INIBINDO monitoramento de captcha');
    console.log('🚀 Iniciando monitoramento da página de resultado...');
    setTimeout(() => {
      console.log('🚀 Iniciando monitoramento da página de resultado...');
      monitorarPaginaResultado();
    }, 1000);
  }
}

// Variável global para controlar se estamos na página de resultado
window.AGIL_FISCAL_RESULT_PAGE = false;

// Verificação inicial para evitar monitoramento de captcha na página de resultado
const currentUrl = window.location.href;
const isResultPage = currentUrl.includes('consultaResumo.aspx') || 
                    currentUrl.includes('consulta.aspx');

if (isResultPage) {
  window.AGIL_FISCAL_RESULT_PAGE = true;
  console.log('🎯 PÁGINA DE RESULTADO DETECTADA - INIBINDO TODOS OS MONITORAMENTOS DE CAPTCHA');
  console.log('🚀 Iniciando monitoramento da página de resultado imediatamente...');
  setTimeout(() => {
    monitorarPaginaResultado();
  }, 1000);
} else {
  console.log('🔄 Página inicial detectada - permitindo monitoramento de captcha');
}

// Verificar imediatamente se já estamos na página consultaResumo.aspx
verificarPaginaResultado();

// Listener específico para detectar quando a página consultaResumo.aspx carrega
window.addEventListener('load', verificarPaginaResultado);
window.addEventListener('DOMContentLoaded', verificarPaginaResultado);

// Listener adicional para garantir que o monitoramento seja iniciado
document.addEventListener('DOMContentLoaded', () => {
  console.log('📄 DOM carregado - verificando página de resultado');
  if (window.location.href.includes('consultaResumo.aspx')) {
    console.log('🎯 DOM carregado - página consultaResumo.aspx detectada');
    setTimeout(() => {
      console.log('🚀 Iniciando monitoramento da página de resultado...');
      monitorarPaginaResultado();
    }, 1500);
  }
});

// Listener para readystatechange
document.addEventListener('readystatechange', () => {
  console.log('📄 ReadyState mudou para:', document.readyState);
  if (document.readyState === 'complete' && window.location.href.includes('consultaResumo.aspx')) {
    console.log('🎯 ReadyState complete - página consultaResumo.aspx detectada');
    setTimeout(() => {
      console.log('🚀 Iniciando monitoramento da página de resultado...');
      monitorarPaginaResultado();
    }, 1000);
    }
  });
  