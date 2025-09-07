// Script para forçar clique no botão Continuar
console.log('Script de clique forçado carregado');

// Função para ocultar botões desnecessários
function hideUnnecessaryButtons() {
  console.log('🎨 Ocultando botões e campos desnecessários...');
  
  // Elementos para ocultar
  const elementsToHide = [
    '#ctl00_ContentPlaceHolder1_btnConsultarHCaptcha', // Botão Consultar
    '#ctl00_ContentPlaceHolder1_txtChaveAcessoResumo', // Campo da chave de acesso
    'input[value="Limpar"]', // Botão Limpar
    'input[value="Consultar"]', // Outros botões de consulta
    'input[type="submit"][value*="Limpar"]', // Botões que contenham "Limpar"
    'input[type="submit"][value*="Consultar"]', // Botões que contenham "Consultar"
    'input[name*="txtChaveAcesso"]', // Outros campos de chave de acesso
    'input[id*="txtChaveAcesso"]' // Campos de chave por ID
  ];
  
  let hiddenCount = 0;
  
  elementsToHide.forEach(selector => {
    const elements = document.querySelectorAll(selector);
    elements.forEach(element => {
      if (element && !element.style.display.includes('none')) {
        element.style.display = 'none';
        element.style.visibility = 'hidden';
        element.style.opacity = '0';
        element.style.position = 'absolute';
        element.style.left = '-9999px';
        console.log(`✅ Ocultado: ${selector}`);
        hiddenCount++;
      }
    });
  });
  
  console.log(`🎨 Total de elementos ocultados: ${hiddenCount}`);
  
  // Adiciona CSS global para garantir que os elementos fiquem ocultos
  const style = document.createElement('style');
  style.textContent = `
    /* Ocultar botões e campos desnecessários */
    #ctl00_ContentPlaceHolder1_btnConsultarHCaptcha,
    #ctl00_ContentPlaceHolder1_txtChaveAcessoResumo,
    input[value="Limpar"],
    input[value="Consultar"],
    input[type="submit"][value*="Limpar"],
    input[type="submit"][value*="Consultar"],
    input[name*="txtChaveAcesso"],
    input[id*="txtChaveAcesso"] {
      display: none !important;
      visibility: hidden !important;
      opacity: 0 !important;
      position: absolute !important;
      left: -9999px !important;
      pointer-events: none !important;
    }
    
    /* Ocultar labels relacionados à chave de acesso */
    label[for*="txtChaveAcesso"],
    label[for*="ChaveAcesso"],
    td:contains("Chave de Acesso"),
    th:contains("Chave de Acesso") {
      display: none !important;
      visibility: hidden !important;
    }
    
    /* Estilo para o botão manual da extensão */
    .agil-fiscal-manual-button {
      position: fixed !important;
      top: 10px !important;
      right: 10px !important;
      z-index: 10000 !important;
      background: #2c3e50 !important;
      color: white !important;
      border: none !important;
      padding: 10px !important;
      border-radius: 5px !important;
      cursor: pointer !important;
      font-size: 12px !important;
      box-shadow: 0 2px 5px rgba(0,0,0,0.3) !important;
    }
    
    .agil-fiscal-manual-button:hover {
      background: #34495e !important;
    }
    
    /* Mensagem informativa sobre automação */
    .agil-fiscal-info {
      position: fixed !important;
      top: 50px !important;
      right: 10px !important;
      z-index: 9999 !important;
      background: #27ae60 !important;
      color: white !important;
      border: none !important;
      padding: 8px 12px !important;
      border-radius: 5px !important;
      font-size: 11px !important;
      box-shadow: 0 2px 5px rgba(0,0,0,0.3) !important;
      max-width: 200px !important;
      text-align: center !important;
    }
  `;
  document.head.appendChild(style);
  console.log('🎨 CSS global adicionado');
  
  // Adiciona mensagem informativa
  addInfoMessage();
}

// Função para adicionar mensagem informativa
function addInfoMessage() {
  // Remove mensagem anterior se existir
  const existingMessage = document.querySelector('.agil-fiscal-info');
  if (existingMessage) {
    existingMessage.remove();
  }
  
  const message = document.createElement('div');
  message.className = 'agil-fiscal-info';
  message.textContent = '✅ Chave preenchida automaticamente';
  
  document.body.appendChild(message);
  console.log('ℹ️ Mensagem informativa adicionada');
  
  // Remove a mensagem após 5 segundos
  setTimeout(() => {
    if (message.parentNode) {
      message.remove();
    }
  }, 5000);
}

// Função para forçar o clique no botão
function forceClickContinueButton() {
  console.log('🔧 Tentando clique forçado no botão Continuar...');
  
  const botaoContinuar = document.querySelector('#ctl00_ContentPlaceHolder1_btnConsultarHCaptcha');
  
  if (!botaoContinuar) {
    console.log('❌ Botão Continuar não encontrado');
    return false;
  }
  
  console.log('✅ Botão encontrado, tentando clicar...');
  
  // Método 1: Clique direto
  try {
    botaoContinuar.click();
    console.log('✅ Clique direto executado');
    return true;
  } catch (e) {
    console.log('❌ Erro no clique direto:', e);
  }
  
  // Método 2: Disparar evento de clique
  try {
    const clickEvent = new MouseEvent('click', {
      view: window,
      bubbles: true,
      cancelable: true
    });
    botaoContinuar.dispatchEvent(clickEvent);
    console.log('✅ Evento de clique disparado');
    return true;
  } catch (e) {
    console.log('❌ Erro no evento de clique:', e);
  }
  
  // Método 3: Executar onclick diretamente
  try {
    if (botaoContinuar.onclick) {
      botaoContinuar.onclick();
      console.log('✅ Onclick executado');
      return true;
    }
  } catch (e) {
    console.log('❌ Erro no onclick:', e);
  }
  
  // Método 4: Simular clique com coordenadas
  try {
    const rect = botaoContinuar.getBoundingClientRect();
    const clickEvent = new MouseEvent('click', {
      view: window,
      bubbles: true,
      cancelable: true,
      clientX: rect.left + rect.width / 2,
      clientY: rect.top + rect.height / 2
    });
    botaoContinuar.dispatchEvent(clickEvent);
    console.log('✅ Clique com coordenadas executado');
    return true;
  } catch (e) {
    console.log('❌ Erro no clique com coordenadas:', e);
  }
  
  // Método 5: Executar JavaScript diretamente
  try {
    const script = document.createElement('script');
    script.textContent = `
      document.querySelector('#ctl00_ContentPlaceHolder1_btnConsultarHCaptcha').click();
    `;
    document.head.appendChild(script);
    document.head.removeChild(script);
    console.log('✅ Clique via script executado');
    return true;
  } catch (e) {
    console.log('❌ Erro no clique via script:', e);
  }
  
  console.log('❌ Todos os métodos de clique falharam');
  return false;
}

// Função para verificar se o captcha foi resolvido
function checkCaptchaResolved() {
  console.log('🔍 Verificando se captcha foi resolvido...');
  
  // Verificar atributo data-hcaptcha-response
  const hCaptchaElement = document.querySelector('.h-captcha[data-hcaptcha-response]');
  if (hCaptchaElement && hCaptchaElement.getAttribute('data-hcaptcha-response')) {
    console.log('✅ Captcha resolvido detectado via data-hcaptcha-response');
    return true;
  }
  
  // Verificar se não há iframe do captcha
  const iframeCaptcha = document.querySelector('iframe[src*="hcaptcha"]');
  if (!iframeCaptcha) {
    console.log('✅ Captcha resolvido detectado via remoção do iframe');
    return true;
  }
  
  // Verificar se o botão está habilitado
  const botaoContinuar = document.querySelector('#ctl00_ContentPlaceHolder1_btnConsultarHCaptcha');
  if (botaoContinuar && !botaoContinuar.disabled) {
    console.log('✅ Captcha resolvido detectado via botão habilitado');
    return true;
  }
  
  console.log('❌ Captcha ainda não resolvido');
  return false;
}

// Função principal para monitorar e clicar
function monitorAndClick() {
  console.log('🚀 Iniciando monitoramento e clique forçado...');
  
  let attempts = 0;
  const maxAttempts = 60; // 30 segundos
  
  const interval = setInterval(() => {
    attempts++;
    console.log(`🔄 Tentativa ${attempts}/${maxAttempts}`);
    
    if (checkCaptchaResolved()) {
      console.log('🎉 Captcha resolvido! Tentando clique...');
      clearInterval(interval);
      
      setTimeout(() => {
        if (forceClickContinueButton()) {
          console.log('✅ Clique executado com sucesso!');
        } else {
          console.log('❌ Falha ao executar clique');
        }
      }, 1000);
    } else if (attempts >= maxAttempts) {
      console.log('⏰ Tempo limite excedido');
      clearInterval(interval);
    }
  }, 500);
}

// Adiciona botão manual para teste
function addManualButton() {
  // Remove botão anterior se existir
  const existingButton = document.querySelector('.agil-fiscal-manual-button');
  if (existingButton) {
    existingButton.remove();
  }
  
  const button = document.createElement('button');
  button.textContent = '🔧 Clique Manual';
  button.className = 'agil-fiscal-manual-button';
  
  button.addEventListener('click', () => {
    console.log('🔧 Clique manual acionado');
    if (checkCaptchaResolved()) {
      forceClickContinueButton();
    } else {
      alert('Captcha ainda não foi resolvido. Resolva o captcha primeiro.');
    }
  });
  
  document.body.appendChild(button);
  console.log('🔧 Botão manual adicionado');
}

// Inicialização
setTimeout(() => {
  hideUnnecessaryButtons(); // Ocultar botões e campos desnecessários
  addManualButton(); // Adicionar botão manual
  monitorAndClick(); // Iniciar monitoramento
}, 2000);

// Exporta funções para uso global
window.forceClickContinueButton = forceClickContinueButton;
window.checkCaptchaResolved = checkCaptchaResolved;
window.monitorAndClick = monitorAndClick;
window.hideUnnecessaryButtons = hideUnnecessaryButtons; 