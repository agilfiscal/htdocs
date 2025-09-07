<?php
// Rotas principais
$router->add('', ['controller' => 'HomeController', 'action' => 'index', 'method' => 'GET']);
$router->add('home', ['controller' => 'HomeController', 'action' => 'index', 'method' => 'GET']);
$router->add('sobre', ['controller' => 'HomeController', 'action' => 'sobre', 'method' => 'GET']);
$router->add('contato', ['controller' => 'HomeController', 'action' => 'contato', 'method' => 'GET']);
$router->add('dashboard', ['controller' => 'HomeController', 'action' => 'index', 'method' => 'GET']);

// Rotas de autenticação
$router->add('login', ['controller' => 'AuthController', 'action' => 'login']);
$router->add('logout', ['controller' => 'AuthController', 'action' => 'logout']);
$router->add('registro', ['controller' => 'AuthController', 'action' => 'registro']);

// Rotas de empresas
$router->add('empresas', ['controller' => 'EmpresaController', 'action' => 'index']);
$router->add('empresas/cadastrar', ['controller' => 'EmpresaController', 'action' => 'cadastrar']);
$router->add('empresas/ativar/{id}', ['controller' => 'EmpresaController', 'action' => 'ativar']);
$router->add('empresas/inativar/{id}', ['controller' => 'EmpresaController', 'action' => 'inativar']);
$router->add('empresas/editar/{id}', ['controller' => 'EmpresaController', 'action' => 'editar']);
$router->add('empresas/compartilhar/{id}', ['controller' => 'EmpresaController', 'action' => 'compartilhar']);
$router->add('empresas/atualizar', ['controller' => 'EmpresaController', 'action' => 'atualizar', 'method' => 'POST']);
$router->add('empresas/filiais/{id}', ['controller' => 'EmpresaController', 'action' => 'filiais', 'method' => 'GET']);
$router->add('empresas/vincular-filial', ['controller' => 'EmpresaController', 'action' => 'vincularFilial', 'method' => 'POST']);
$router->add('empresas/desvincular-filial', ['controller' => 'EmpresaController', 'action' => 'desvincularFilial', 'method' => 'POST']);

// Rotas de auditoria
$router->add('auditoria', ['controller' => 'AuditoriaController', 'action' => 'index']);
$router->add('auditoria/observacao/{id}', ['controller' => 'AuditoriaController', 'action' => 'getObservacao', 'method' => 'GET']);
$router->add('auditoria/observacao-get/{id}', ['controller' => 'AuditoriaController', 'action' => 'getObservacao', 'method' => 'GET']);
$router->add('auditoria/observacao/{id}', ['controller' => 'AuditoriaController', 'action' => 'salvarObservacao', 'method' => 'POST']);
$router->add('auditoria/teste-observacao/{id}', ['controller' => 'AuditoriaController', 'action' => 'testeObservacao', 'method' => 'GET']);
$router->add('auditoria/relatorio', ['controller' => 'AuditoriaController', 'action' => 'gerarRelatorio', 'method' => 'POST']);
$router->add('auditoria/salvar-destino-vencimentos', ['controller' => 'AuditoriaController', 'action' => 'salvarDestinoVencimentos']);
$router->add('auditoria/get-destino-vencimentos', ['controller' => 'AuditoriaController', 'action' => 'getDestinoEVencimentos']);
$router->add('auditoria/get-nota-detalhes', ['controller' => 'AuditoriaController', 'action' => 'getNotaDetalhes']);
$router->add('auditoria/gerar-relatorio', ['controller' => 'AuditoriaController', 'action' => 'gerarRelatorio', 'method' => 'GET']);

// Rotas de usuários
$router->add('usuarios', ['controller' => 'UsuarioController', 'action' => 'index']);
$router->add('usuarios/editar', ['controller' => 'UsuarioController', 'action' => 'editar']);
$router->add('usuarios/resetar-senha', ['controller' => 'UsuarioController', 'action' => 'resetarSenha']);
$router->add('usuarios/inativar', ['controller' => 'UsuarioController', 'action' => 'inativar']);
$router->add('usuarios/ativar', ['controller' => 'UsuarioController', 'action' => 'ativar']);

// Rotas de arquivos
$router->add('arquivos', ['controller' => 'ArquivosController', 'action' => 'index']);
$router->add('arquivos/upload', ['controller' => 'ArquivosController', 'action' => 'upload']);
$router->add('arquivos/excluir/{id}', ['controller' => 'ArquivosController', 'action' => 'excluir']);
$router->add('arquivos/download/{id}', ['controller' => 'ArquivosController', 'action' => 'download']);
$router->add('arquivos/teste', ['controller' => 'ArquivosController', 'action' => 'teste']);
$router->add('arquivos/teste-upload', ['controller' => 'ArquivosController', 'action' => 'testeUpload']);

// Rotas de configurações
$router->add('configuracoes', ['controller' => 'ConfiguracaoController', 'action' => 'index', 'method' => 'GET']);
$router->add('configuracoes/salvar', ['controller' => 'ConfiguracaoController', 'action' => 'salvar', 'method' => 'POST']);

// Rotas de empresas atendidas (Atendimento)
$router->add('configuracoes/empresas-disponiveis', ['controller' => 'ConfiguracaoController','action' => 'getEmpresasDisponiveis','method' => 'GET']);
$router->add('configuracoes/salvar-empresas-atendidas', ['controller' => 'ConfiguracaoController','action' => 'salvarEmpresasAtendidas','method' => 'POST']);
$router->add('configuracoes/empresas-atendidas-usuario', ['controller' => 'ConfiguracaoController','action' => 'empresasAtendidasUsuario','method' => 'GET']);

// Rotas de notificações
$router->add('notificacoes', ['controller' => 'NotificacaoController', 'action' => 'getNotificacoes', 'method' => 'GET']);
$router->add('notificacoes/detalhes', ['controller' => 'NotificacaoController', 'action' => 'getNotasDetalhes', 'method' => 'GET']);
$router->add('notificacoes/resolver/{id}', ['controller' => 'NotificacaoController', 'action' => 'resolverNotificacao', 'method' => 'POST']);
$router->add('notificacoes/resolucao', ['controller' => 'NotificacaoController', 'action' => 'getNotificacoesResolucao', 'method' => 'GET']);

$router->add('notificacoes/historico', ['controller' => 'NotificacaoController', 'action' => 'getHistoricoResolucoes', 'method' => 'GET']);
$router->add('notificacoes/marcar-visualizadas', ['controller' => 'NotificacaoController', 'action' => 'marcarTodasComoVisualizadas', 'method' => 'POST']);

// Rotas de comentários
$router->add('comentarios/notificacoes', ['controller' => 'ComentarioController', 'action' => 'getNotificacoesComentarios', 'method' => 'GET']);
$router->add('comentarios/visualizar/{id}', ['controller' => 'ComentarioController', 'action' => 'marcarComentarioVisualizado', 'method' => 'POST']);
$router->add('comentarios/marcar-visualizados', ['controller' => 'ComentarioController', 'action' => 'marcarTodosComentariosVisualizados', 'method' => 'POST']);


// Outras rotas do seu sistema podem ser adicionadas aqui...
$router->add('configuracoes/logs/resolucao-nota', ['controller' => 'ConfiguracaoController', 'action' => 'logsResolucaoNota', 'method' => 'GET']);
$router->add('configuracoes/logs/envio-notas', ['controller' => 'ConfiguracaoController', 'action' => 'logsEnvioNotas', 'method' => 'GET']);
$router->add('configuracoes/usuarios-disponiveis', ['controller' => 'ConfiguracaoController', 'action' => 'getUsuariosDisponiveis', 'method' => 'GET']);
$router->add('configuracoes/arquivos-enviados', ['controller' => 'ConfiguracaoController', 'action' => 'listarArquivosEnviados', 'method' => 'GET']);
$router->add('configuracoes/desfazer-arquivo', ['controller' => 'ConfiguracaoController', 'action' => 'desfazerEnvioArquivo', 'method' => 'POST']);
$router->add('configuracoes/logs-romaneio', ['controller' => 'ConfiguracaoController', 'action' => 'logsRomaneio', 'method' => 'GET']);
$router->add('configuracoes/importar-produtos-base', ['controller' => 'ConfiguracaoController','action' => 'importarProdutosBase','method' => 'POST']);
$router->add('configuracoes/importar-produtos-revisada', ['controller' => 'ConfiguracaoController','action' => 'importarProdutosRevisada','method' => 'POST']);
$router->add('configuracoes/importar-produtos-base-get', ['controller' => 'ConfiguracaoController','action' => 'importarProdutosBaseGet','method' => 'GET']);
$router->add('configuracoes/atualizar-produtos-revisados', ['controller' => 'ConfiguracaoController','action' => 'atualizarProdutosRevisados','methods' => ['POST']]);
$router->add('consulta-tributaria', [
    'controller' => 'ConsultaTributariaController',
    'action' => 'index',
    'method' => 'GET'
]);
$router->add('consulta-tributaria/buscar', [
    'controller' => 'ConsultaTributariaController',
    'action' => 'buscar',
    'method' => 'POST'
]);
$router->add('consulta-tributaria/autocomplete-descritivo', [
    'controller' => 'ConsultaTributariaController',
    'action' => 'autocompleteDescritivo',
    'method' => 'GET'
]);
$router->add('configuracoes/autocomplete-descritivo-consulta-tributaria', [
    'controller' => 'ConfiguracaoController',
    'action' => 'autocompleteDescritivoConsultaTributaria',
    'method' => 'GET'
]);
$router->add('consulta-tributaria/sugerir-ean', [
    'controller' => 'ConsultaTributariaController',
    'action' => 'sugerirEan',
    'method' => 'POST'
]);

// Rotas para Sugestões EAN
$router->add('configuracoes/sugestoes-ean', [
    'controller' => 'ConfiguracaoController',
    'action' => 'sugestoesEAN',
    'method' => 'GET'
]);
$router->add('configuracoes/excluir-sugestao-ean', [
    'controller' => 'ConfiguracaoController',
    'action' => 'excluirSugestaoEAN',
    'method' => 'POST'
]);
$router->add('configuracoes/limpar-dados-empresa', [
    'controller' => 'ConfiguracaoController',
    'action' => 'limparDadosEmpresa',
    'method' => 'POST'
]);
$router->add('configuracoes/exportar-sugestoes-ean-txt', [
    'controller' => 'ConfiguracaoController',
    'action' => 'exportarSugestoesEANTXT',
    'method' => 'GET'
]);

// Rotas para funcionalidades extras - REMOVIDAS (já estão em config/routes.php)
