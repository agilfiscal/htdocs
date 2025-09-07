<?php
/**
 * Arquivo de teste para verificar se as rotas de DANFE estão funcionando
 * Acesse: http://localhost/teste_danfe.php
 */

// Simular uma chave de teste (44 dígitos)
$chave_teste = "12345678901234567890123456789012345678901234";

echo "<h1>🧪 Teste de Rotas DANFE/XML</h1>";

echo "<h2>📋 Informações do Sistema</h2>";
echo "<ul>";
echo "<li><strong>Chave de Teste:</strong> $chave_teste</li>";
echo "<li><strong>Extensão ID:</strong> fnalonmlenogoaknbeikifdbaokkhmjj</li>";
echo "<li><strong>Link da Extensão:</strong> <a href='https://chromewebstore.google.com/detail/gerar-danfedacte/fnalonmlenogoaknbeikifdbaokkhmjj' target='_blank'>Chrome Web Store</a></li>";
echo "</ul>";

echo "<h2>🔗 Links de Teste</h2>";
echo "<div style='margin: 20px 0;'>";
echo "<a href='/auditoria/danfe/$chave_teste' target='_blank' style='display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; margin: 10px;'>";
echo "📄 Testar Visualizar DANFE";
echo "</a>";

echo "<a href='/auditoria/baixarXml/$chave_teste' target='_blank' style='display: inline-block; padding: 10px 20px; background: #28a745; color: white; text-decoration: none; border-radius: 5px; margin: 10px;'>";
echo "💻 Testar Baixar XML";
echo "</a>";
echo "</div>";

echo "<h2>📖 Instruções</h2>";
echo "<ol>";
echo "<li>Instale a extensão do Chrome: <a href='https://chromewebstore.google.com/detail/gerar-danfedacte/fnalonmlenogoaknbeikifdbaokkhmjj' target='_blank'>Link da Extensão</a></li>";
echo "<li>Clique nos links de teste acima</li>";
echo "<li>Verifique se a página da SEFAZ abre com a chave inserida automaticamente</li>";
echo "<li>A extensão deve detectar automaticamente a página e a chave</li>";
echo "</ol>";

echo "<h2>🔧 Verificação de Rotas</h2>";
echo "<p>Verifique se os seguintes arquivos existem:</p>";
echo "<ul>";
echo "<li>✅ routes/web.php - Deve conter as rotas de danfe e baixarXml</li>";
echo "<li>✅ app/Controllers/AuditoriaController.php - Deve ter os métodos danfe() e baixarXml()</li>";
echo "<li>✅ app/Views/auditoria/index.php - Deve ter os botões de ação</li>";
echo "</ul>";

echo "<h2>📝 Logs de Debug</h2>";
echo "<p>Verifique os logs do PHP para erros:</p>";
echo "<ul>";
echo "<li>error_log do PHP</li>";
echo "<li>Logs do navegador (F12 → Console)</li>";
echo "<li>Logs do servidor web (Apache/Nginx)</li>";
echo "</ul>";

echo "<h2>🎯 Próximos Passos</h2>";
echo "<ol>";
echo "<li>Teste as rotas com uma chave real de nota fiscal</li>";
echo "<li>Verifique se a extensão está funcionando corretamente</li>";
echo "<li>Teste a integração completa no sistema de auditoria</li>";
echo "</ol>";

echo "<hr>";
echo "<p><em>Teste criado em: " . date('d/m/Y H:i:s') . "</em></p>";
?> 