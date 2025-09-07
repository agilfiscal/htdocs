document.addEventListener('DOMContentLoaded', function() {
    const testBtn = document.getElementById('testBtn');
    
    testBtn.addEventListener('click', function() {
        // Testa se a extensão está funcionando abrindo uma consulta de teste
        chrome.runtime.sendMessage({
            tipo: "AbrirConsulta",
            chave: "12345678901234567890123456789012345678901234" // Chave de teste
        }, (response) => {
            if (chrome.runtime.lastError) {
                alert('Erro ao testar extensão: ' + chrome.runtime.lastError.message);
            } else if (response && response.success) {
                alert('Extensão funcionando! Janela de consulta aberta.');
            } else {
                alert('Erro ao abrir consulta de teste.');
            }
        });
    });
});