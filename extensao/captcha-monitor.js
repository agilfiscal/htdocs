// Monitor de Captcha para Extensão Ágil Fiscal
console.log('Monitor de captcha carregado');

class CaptchaMonitor {
  constructor() {
    this.isResolved = false;
    this.observers = [];
    this.checkInterval = null;
    this.maxAttempts = 120; // 60 segundos
    this.attempts = 0;
    this.lastCheckTime = 0;
  }

  // Inicia o monitoramento
  start() {
    console.log('Iniciando monitoramento de captcha...');
    this.setupObservers();
    this.startPeriodicCheck();
    
    // Verificação inicial
    setTimeout(() => {
      this.checkCaptchaStatus();
    }, 1000);
  }

  // Configura os observers para detectar mudanças
  setupObservers() {
    // Observer para mudanças no elemento h-captcha
    const hCaptchaObserver = new MutationObserver((mutations) => {
      mutations.forEach((mutation) => {
        console.log('Mudança detectada no h-captcha:', mutation);
        if (mutation.type === 'attributes') {
          this.checkCaptchaStatus();
        }
      });
    });

    // Observer para mudanças no DOM (detectar iframes)
    const domObserver = new MutationObserver((mutations) => {
      mutations.forEach((mutation) => {
        if (mutation.type === 'childList') {
          mutation.addedNodes.forEach((node) => {
            if (node.nodeType === Node.ELEMENT_NODE) {
              this.checkForCaptchaElements(node);
            }
          });
          mutation.removedNodes.forEach((node) => {
            if (node.nodeType === Node.ELEMENT_NODE) {
              console.log('Elemento removido:', node);
              // Se um iframe do captcha foi removido, pode indicar resolução
              if (node.tagName === 'IFRAME' && node.src && node.src.includes('hcaptcha')) {
                console.log('Iframe do captcha removido, verificando resolução...');
                setTimeout(() => this.checkCaptchaStatus(), 500);
              }
            }
          });
        }
      });
    });

    // Configura os observers
    const hCaptchaElement = document.querySelector('.h-captcha');
    if (hCaptchaElement) {
      hCaptchaObserver.observe(hCaptchaElement, {
        attributes: true,
        attributeFilter: ['data-hcaptcha-response', 'class', 'style']
      });
      this.observers.push(hCaptchaObserver);
      console.log('Observer configurado para h-captcha');
    } else {
      console.log('Elemento h-captcha não encontrado inicialmente');
    }

    domObserver.observe(document.body, {
      childList: true,
      subtree: true
    });
    this.observers.push(domObserver);
    console.log('Observer configurado para mudanças no DOM');
  }

  // Verifica elementos de captcha adicionados
  checkForCaptchaElements(element) {
    // Verifica se o elemento é um iframe do captcha
    if (element.tagName === 'IFRAME' && element.src && element.src.includes('hcaptcha')) {
      console.log('Iframe do hCaptcha detectado:', element);
      setTimeout(() => this.checkCaptchaStatus(), 1000);
    }

    // Verifica se é um elemento h-captcha
    if (element.classList && element.classList.contains('h-captcha')) {
      console.log('Elemento h-captcha detectado:', element);
      setTimeout(() => this.checkCaptchaStatus(), 500);
    }
  }

  // Verifica o status do captcha
  checkCaptchaStatus() {
    if (this.isResolved) return;
    
    // Evita verificações muito frequentes
    const now = Date.now();
    if (now - this.lastCheckTime < 200) return;
    this.lastCheckTime = now;

    console.log('=== Verificando status do captcha ===');

    // Método 1: Verificar atributo data-hcaptcha-response
    const hCaptchaElement = document.querySelector('.h-captcha[data-hcaptcha-response]');
    if (hCaptchaElement) {
      const response = hCaptchaElement.getAttribute('data-hcaptcha-response');
      console.log('Atributo data-hcaptcha-response encontrado:', response);
      if (response && response.length > 0) {
        console.log('Captcha resolvido detectado via data-hcaptcha-response');
        this.handleCaptchaResolved();
        return;
      }
    }

    // Método 2: Verificar se o iframe do captcha foi removido
    const iframeCaptcha = document.querySelector('iframe[src*="hcaptcha"]');
    console.log('Iframe do captcha encontrado:', iframeCaptcha);
    if (!iframeCaptcha) {
      console.log('Iframe do captcha não encontrado, pode indicar resolução');
      // Verifica se há algum indicador de que foi resolvido
      const responseElement = document.querySelector('.h-captcha[data-hcaptcha-response]');
      if (responseElement && responseElement.getAttribute('data-hcaptcha-response')) {
        console.log('Captcha resolvido detectado via remoção do iframe + response');
        this.handleCaptchaResolved();
        return;
      }
    }

    // Método 3: Verificar mudanças visuais no captcha
    const captchaContainer = document.querySelector('.h-captcha');
    if (captchaContainer) {
      const style = window.getComputedStyle(captchaContainer);
      console.log('Estilo do captcha:', {
        display: style.display,
        visibility: style.visibility,
        opacity: style.opacity
      });
      if (style.display === 'none' || style.visibility === 'hidden' || style.opacity === '0') {
        console.log('Captcha resolvido detectado via mudança visual');
        this.handleCaptchaResolved();
        return;
      }
    }

    // Método 4: Verificar se há mensagens de sucesso
    const successMessages = document.querySelectorAll('*');
    for (let element of successMessages) {
      if (element.textContent && (
          element.textContent.includes('verificado') || 
          element.textContent.includes('sucesso') || 
          element.textContent.includes('resolvido') ||
          element.textContent.includes('Verificado') ||
          element.textContent.includes('Sucesso')
      )) {
        console.log('Captcha resolvido detectado via mensagem de sucesso:', element.textContent);
        this.handleCaptchaResolved();
        return;
      }
    }

    // Método 5: Verificar se o botão está habilitado (indicador indireto)
    const botaoContinuar = document.querySelector('#ctl00_ContentPlaceHolder1_btnConsultarHCaptcha');
    if (botaoContinuar) {
      console.log('Botão continuar encontrado:', {
        disabled: botaoContinuar.disabled,
        style: botaoContinuar.style.display,
        onclick: !!botaoContinuar.onclick
      });
      
      // Se o botão não está desabilitado e não há iframe do captcha, pode estar resolvido
      if (!botaoContinuar.disabled && !iframeCaptcha) {
        console.log('Captcha resolvido detectado via botão habilitado + sem iframe');
        this.handleCaptchaResolved();
        return;
      }
    }

    console.log('Captcha ainda não resolvido');
  }

  // Manipula quando o captcha é resolvido
  handleCaptchaResolved() {
    if (this.isResolved) return;
    
    this.isResolved = true;
    console.log('🎉 Captcha resolvido! Clicando no botão Continuar...');
    
    // Limpa todos os observers
    this.cleanup();
    
    // Aguarda um pouco e clica no botão
    setTimeout(() => {
      this.clickContinueButton();
    }, 1000);
  }

  // Clica no botão Continuar
  clickContinueButton() {
    const botaoContinuar = document.querySelector('#ctl00_ContentPlaceHolder1_btnConsultarHCaptcha');
    
    if (botaoContinuar) {
      console.log('Botão Continuar encontrado, clicando...');
      console.log('Estado do botão:', {
        disabled: botaoContinuar.disabled,
        style: botaoContinuar.style.display,
        onclick: !!botaoContinuar.onclick
      });
      
      // Tenta diferentes métodos de clique
      try {
        // Método 1: Clique direto
        console.log('Tentando clique direto...');
        botaoContinuar.click();
        console.log('✅ Clique direto executado');
      } catch (e) {
        console.log('❌ Erro no clique direto:', e);
        
        try {
          // Método 2: Disparar evento de clique
          console.log('Tentando evento de clique...');
          const clickEvent = new MouseEvent('click', {
            view: window,
            bubbles: true,
            cancelable: true
          });
          botaoContinuar.dispatchEvent(clickEvent);
          console.log('✅ Evento de clique disparado');
        } catch (e2) {
          console.log('❌ Erro no evento de clique:', e2);
          
          try {
            // Método 3: Executar onclick diretamente
            console.log('Tentando executar onclick...');
            if (botaoContinuar.onclick) {
              botaoContinuar.onclick();
              console.log('✅ Onclick executado');
            } else {
              console.log('❌ Onclick não encontrado');
            }
          } catch (e3) {
            console.log('❌ Erro no onclick:', e3);
          }
        }
      }
    } else {
      console.log('❌ Botão Continuar não encontrado');
    }
  }

  // Verificação periódica como fallback
  startPeriodicCheck() {
    this.checkInterval = setInterval(() => {
      this.attempts++;
      console.log(`🔄 Verificação periódica ${this.attempts}/${this.maxAttempts}`);
      
      this.checkCaptchaStatus();
      
      if (this.attempts >= this.maxAttempts) {
        console.log('⏰ Tempo limite excedido para resolução do captcha');
        this.cleanup();
      }
    }, 500);
  }

  // Limpa todos os observers e intervalos
  cleanup() {
    console.log('🧹 Limpando monitor de captcha...');
    
    this.observers.forEach(observer => {
      observer.disconnect();
    });
    this.observers = [];
    
    if (this.checkInterval) {
      clearInterval(this.checkInterval);
      this.checkInterval = null;
    }
  }
}

// Exporta a classe para uso global
window.CaptchaMonitor = CaptchaMonitor; 