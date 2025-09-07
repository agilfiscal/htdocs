# 🔄 Solução para Mudança de URL - Extensão Ágil Fiscal

## 📋 Problema Identificado

O console ficava em branco após resolver o captcha porque a URL mudava de `consultaRecaptcha.aspx` para `consultaResumo.aspx`, e os logs não eram preservados entre as mudanças de página. O monitoramento da página de resultado não estava sendo iniciado corretamente.

**PROBLEMA CRÍTICO DESCOBERTO:** O script estava re-inicializando o monitoramento de captcha na nova página (`consultaResumo.aspx`) em vez de iniciar o monitoramento do botão Download.

## ✨ Solução Implementada

### **1. Detecção de Mudança de URL**
- ✅ **URL específica**: `consultaResumo.aspx` (não apenas `!consultaRecaptcha.aspx`)
- ✅ **Múltiplos listeners**: MutationObserver, popstate, pushstate, replacestate
- ✅ **SessionStorage**: Preserva informação entre mudanças de página
- ✅ **Detecção imediata**: Listener específico para `consultaResumo.aspx`
- ✅ **Múltiplos pontos de entrada**: load, DOMContentLoaded, readystatechange

### **2. Preservação de Estado**
- ✅ **Beforeunload**: Salva informação antes da mudança de página
- ✅ **Unload**: Salva informação durante a mudança de página
- ✅ **Load**: Verifica e inicia monitoramento na nova página
- ✅ **SessionStorage**: Armazena flag `agil_fiscal_monitorar_download`

### **3. Monitoramento Robusto**
- ✅ **Detecção específica**: Para URL `consultaResumo.aspx`
- ✅ **Múltiplos métodos**: Busca do botão Download
- ✅ **Logs detalhados**: Com emojis para fácil identificação
- ✅ **Busca avançada**: Por texto e elementos próximos
- ✅ **Verificação de DOM**: Aguarda página carregar completamente
- ✅ **INIBIÇÃO DE CAPTCHA**: Evita re-inicialização do monitoramento de captcha
- ✅ **Variável Global**: `window.AGIL_FISCAL_RESULT_PAGE` para controle centralizado

## 🔧 Como Funciona

### **Fluxo Completo:**
1. **Usuário resolve** o captcha manualmente
2. **Extensão detecta** captcha resolvido
3. **Salva flag** no sessionStorage: `agil_fiscal_monitorar_download = true`
4. **Clica automaticamente** no botão "Continuar"
5. **Página muda** para `consultaResumo.aspx`
6. **Nova página carrega** e verifica sessionStorage
7. **Define variável global** `window.AGIL_FISCAL_RESULT_PAGE = true`
8. **INIBE monitoramento de captcha** na nova página
9. **Inicia monitoramento** da página de resultado
10. **Procura botão** Download com múltiplos métodos
11. **Clica automaticamente** no botão Download
12. **XML é baixado** automaticamente

### **Correção Crítica - Variável Global de Controle:**
```javascript
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
```

### **Inibição em Todas as Funções de Captcha:**
```javascript
function iniciarMonitoramentoCaptcha() {
  // Verificar se estamos na página de resultado
  if (window.AGIL_FISCAL_RESULT_PAGE) {
    console.log('🚫 INIBINDO monitoramento de captcha - página de resultado detectada');
    return;
  }
  // ... resto da função
}

function monitoramentoBasicoCaptcha() {
  // Verificar se estamos na página de resultado
  if (window.AGIL_FISCAL_RESULT_PAGE) {
    console.log('🚫 INIBINDO monitoramento básico de captcha - página de resultado detectada');
    return;
  }
  // ... resto da função
}

function iniciarMonitoramentoCaptchaComDownload() {
  // Verificar se estamos na página de resultado
  if (window.AGIL_FISCAL_RESULT_PAGE) {
    console.log('🚫 INIBINDO monitoramento de captcha com download - página de resultado detectada');
    return;
  }
  // ... resto da função
}

function monitoramentoBasicoCaptchaComDownload() {
  // Verificar se estamos na página de resultado
  if (window.AGIL_FISCAL_RESULT_PAGE) {
    console.log('🚫 INIBINDO monitoramento básico de captcha com download - página de resultado detectada');
    return;
  }
  // ... resto da função
}
```

### **Listeners Implementados:**
```javascript
// Listener para mudanças de URL
let lastUrl = location.href;
new MutationObserver(() => {
  const url = location.href;
  if (url !== lastUrl) {
    lastUrl = url;
    if (!url.includes('consultaRecaptcha.aspx')) {
      setTimeout(() => {
        monitorarPaginaResultado();
      }, 1000);
    }
  }
}).observe(document, { subtree: true, childList: true });

// Listener para beforeunload
window.addEventListener('beforeunload', () => {
  sessionStorage.setItem('agil_fiscal_monitorar_download', 'true');
});

// Listener para load com INIBIÇÃO de captcha
window.addEventListener('load', () => {
  const currentUrl = window.location.href;
  const isResultPage = currentUrl.includes('consultaResumo.aspx') || 
                      currentUrl.includes('consulta.aspx');
  
  // Se estamos na página de resultado, NÃO iniciar monitoramento de captcha
  if (isResultPage) {
    window.AGIL_FISCAL_RESULT_PAGE = true;
    console.log('🎯 Página de resultado detectada - INIBINDO monitoramento de captcha');
    setTimeout(() => {
      monitorarPaginaResultado();
    }, 2000);
    return; // IMPORTANTE: Sair aqui para não executar o resto
  }
  
  // Lógica para página inicial...
});

// Função para verificar página consultaResumo.aspx
function verificarPaginaResultado() {
  if (window.location.href.includes('consultaResumo.aspx')) {
    window.AGIL_FISCAL_RESULT_PAGE = true;
    console.log('🎯 Página consultaResumo.aspx detectada - INIBINDO monitoramento de captcha');
    setTimeout(() => {
      monitorarPaginaResultado();
    }, 1000);
  }
}

// Múltiplos listeners para consultaResumo.aspx
verificarPaginaResultado();
window.addEventListener('load', verificarPaginaResultado);
window.addEventListener('DOMContentLoaded', verificarPaginaResultado);
document.addEventListener('readystatechange', () => {
  if (document.readyState === 'complete' && window.location.href.includes('consultaResumo.aspx')) {
    setTimeout(() => {
      monitorarPaginaResultado();
    }, 1000);
  }
});
```

### **Detecção da Página de Resultado:**
```javascript
const isResultPage = currentUrl.includes('consultaResumo.aspx') || 
                    currentUrl.includes('consulta.aspx') ||
                    document.querySelector('#ctl00_ContentPlaceHolder1_btnDownload') ||
                    document.querySelector('input[value="Download do documento"]') ||
                    document.querySelector('input[value*="Download"]') ||
                    document.querySelector('input[value*="download"]');
```

### **Métodos de Busca do Botão Download:**
1. **XPath específico**: `//*[@id="ctl00_ContentPlaceHolder1_btnDownload"]`
2. **ID direto**: `#ctl00_ContentPlaceHolder1_btnDownload`
3. **Valor do botão**: `input[value="Download do documento"]`
4. **Texto do botão**: busca por "download", "baixar", "xml"
5. **Elementos próximos**: busca por texto "Download do documento" e botão próximo

### **Verificação de DOM:**
```javascript
if (document.readyState === 'complete' || document.readyState === 'interactive') {
  console.log('✅ DOM está pronto, continuando monitoramento...');
} else {
  console.log('⏳ DOM ainda carregando, aguardando...');
  return; // Continuar no próximo intervalo
}
```

## 📁 Arquivos Modificados

### **`extensao/injector.js`**
- ✅ **Listeners de mudança de URL** - múltiplos métodos de detecção
- ✅ **SessionStorage** - preservação de estado entre páginas
- ✅ **Detecção específica** - para URL `consultaResumo.aspx`
- ✅ **Logs detalhados** - com emojis para debug
- ✅ **Listener específico** - para página `consultaResumo.aspx`
- ✅ **Busca avançada** - múltiplos métodos para encontrar botão
- ✅ **Múltiplos pontos de entrada** - load, DOMContentLoaded, readystatechange
- ✅ **Verificação de DOM** - aguarda página carregar completamente
- ✅ **INIBIÇÃO DE CAPTCHA** - evita re-inicialização do monitoramento de captcha
- ✅ **Verificação inicial** - detecta página de resultado antes de qualquer execução
- ✅ **Variável Global** - `window.AGIL_FISCAL_RESULT_PAGE` para controle centralizado
- ✅ **Inibição em todas as funções** - verificação em todas as funções de captcha

## 🔍 Como Testar

### **Fluxo de Teste:**
1. **Acesse** a página de auditoria
2. **Selecione** uma nota fiscal
3. **Clique** em "Baixar XML"
4. **Abra o console** (F12) e ative "Preserve log"
5. **Resolva** o captcha manualmente
6. **Aguarde** os logs de mudança de URL
7. **Confirme** que o monitoramento inicia na nova página
8. **Verifique** se o botão Download é encontrado e clicado

### **Logs Esperados:**
```
[Timestamp] Captcha resolvido - preparando para mudança de página...
[Timestamp] 🔄 Beforeunload detectado - página vai mudar
[Timestamp] 🎯 PÁGINA DE RESULTADO DETECTADA - INIBINDO TODOS OS MONITORAMENTOS DE CAPTCHA
[Timestamp] 🚀 Iniciando monitoramento da página de resultado imediatamente...
[Timestamp] 🔄 Página carregada - verificando se deve monitorar download
[Timestamp] 🎯 Página de resultado detectada - INIBINDO monitoramento de captcha
[Timestamp] 🎯 Página consultaResumo.aspx detectada - INIBINDO monitoramento de captcha
[Timestamp] 📄 DOM carregado - verificando página de resultado
[Timestamp] 🎯 DOM carregado - página consultaResumo.aspx detectada
[Timestamp] 📄 ReadyState mudou para: complete
[Timestamp] 🎯 ReadyState complete - página consultaResumo.aspx detectada
[Timestamp] 🚀 Iniciando monitoramento da página de resultado...
[Timestamp] URL atual: [URL com consultaResumo.aspx]
[Timestamp] ✅ Página consultaResumo.aspx detectada - forçando como página de resultado
[Timestamp] ✅ DOM está pronto, continuando monitoramento...
[Timestamp] ✅ Página de resultado detectada, procurando botão Download...
[Timestamp] ✅ Botão Download encontrado via XPath
[Timestamp] 🎯 Botão Download encontrado!
[Timestamp] 🖱️ Tentando clique direto...
[Timestamp] ✅ Clique no botão Download executado com sucesso!
```

## 🛠️ Solução de Problemas

### **Se o Monitoramento não Iniciar:**
1. **Verifique** se "Preserve log" está ativado no console
2. **Confirme** que a URL mudou para `consultaResumo.aspx`
3. **Verifique** se há logs de "Beforeunload" e "Página carregada"
4. **Confirme** se o sessionStorage está funcionando
5. **Procure** pelos logs de "PÁGINA DE RESULTADO DETECTADA - INIBINDO"
6. **Verifique** se há logs de "DOM carregado" e "ReadyState"
7. **Confirme** que NÃO há logs de "Iniciando monitoramento de captcha" na nova página
8. **Procure** pelos logs de "🚫 INIBINDO monitoramento de captcha"

### **Debug Avançado:**
```javascript
// No console do navegador, execute:
console.log('SessionStorage:', sessionStorage.getItem('agil_fiscal_monitorar_download'));
console.log('URL atual:', window.location.href);
console.log('ReadyState:', document.readyState);
console.log('Variável Global:', window.AGIL_FISCAL_RESULT_PAGE);
console.log('Botão Download:', document.querySelector('#ctl00_ContentPlaceHolder1_btnDownload'));
console.log('Todos os botões:', Array.from(document.querySelectorAll('input[type="submit"], button')).map(b => b.value || b.textContent));
```

### **Verificar SessionStorage:**
```javascript
// No console do navegador, execute:
console.log('Todos os itens do sessionStorage:');
for (let i = 0; i < sessionStorage.length; i++) {
  const key = sessionStorage.key(i);
  console.log(key + ':', sessionStorage.getItem(key));
}
```

## 📊 Logs de Debug

### **Logs de Mudança de Página:**
```
[Timestamp] Captcha resolvido - preparando para mudança de página...
[Timestamp] 🔄 Beforeunload detectado - página vai mudar
[Timestamp] 🎯 PÁGINA DE RESULTADO DETECTADA - INIBINDO TODOS OS MONITORAMENTOS DE CAPTCHA
[Timestamp] 🚀 Iniciando monitoramento da página de resultado imediatamente...
[Timestamp] 🔄 Página carregada - verificando se deve monitorar download
[Timestamp] 🎯 Página de resultado detectada - INIBINDO monitoramento de captcha
[Timestamp] 🎯 Página consultaResumo.aspx detectada - INIBINDO monitoramento de captcha
[Timestamp] 📄 DOM carregado - verificando página de resultado
[Timestamp] 🎯 DOM carregado - página consultaResumo.aspx detectada
[Timestamp] 📄 ReadyState mudou para: complete
[Timestamp] 🎯 ReadyState complete - página consultaResumo.aspx detectada
```

### **Logs de Monitoramento:**
```
[Timestamp] 🚀 Iniciando monitoramento da página de resultado...
[Timestamp] URL atual: [URL com consultaResumo.aspx]
[Timestamp] ✅ Página consultaResumo.aspx detectada - forçando como página de resultado
[Timestamp] ✅ DOM está pronto, continuando monitoramento...
[Timestamp] ✅ Página de resultado detectada, procurando botão Download...
[Timestamp] ✅ Botão Download encontrado via XPath
[Timestamp] 🎯 Botão Download encontrado!
[Timestamp] 🖱️ Tentando clique direto...
[Timestamp] ✅ Clique no botão Download executado com sucesso!
```

### **Logs de Inibição (Importantes):**
```
[Timestamp] 🚫 INIBINDO monitoramento de captcha - página de resultado detectada
[Timestamp] 🚫 INIBINDO monitoramento básico de captcha - página de resultado detectada
[Timestamp] 🚫 INIBINDO monitoramento de captcha com download - página de resultado detectada
[Timestamp] 🚫 INIBINDO monitoramento básico de captcha com download - página de resultado detectada
```

## ✅ Status da Implementação

- ✅ **Detecção de mudança de URL** - Implementado
- ✅ **Preservação de estado** - Implementado
- ✅ **SessionStorage** - Implementado
- ✅ **Listeners múltiplos** - Implementado
- ✅ **Detecção específica** - Implementado
- ✅ **Listener específico** - Implementado
- ✅ **Busca avançada** - Implementado
- ✅ **Múltiplos pontos de entrada** - Implementado
- ✅ **Verificação de DOM** - Implementado
- ✅ **INIBIÇÃO DE CAPTCHA** - Implementado
- ✅ **Verificação inicial** - Implementado
- ✅ **Variável Global** - Implementado
- ✅ **Inibição em todas as funções** - Implementado
- ✅ **Logs detalhados** - Implementado
- ✅ **Testes realizados** - Implementado

---

**Versão:** 1.4  
**Última atualização:** Janeiro 2025  
**Status:** Solução para mudança de URL implementada com variável global e inibição completa de captcha 