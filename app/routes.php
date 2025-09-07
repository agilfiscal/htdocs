$router->get('/auditoria/observacao/{id}', 'AuditoriaController@getObservacao');
$router->post('/auditoria/observacao/{id}', 'AuditoriaController@salvarObservacao');
$router->post('/auditoria/relatorio', 'AuditoriaController@gerarRelatorio');

// Rotas de configuração
$router->get('/configuracoes', 'ConfiguracaoController@index');
$router->post('/configuracoes/salvar', 'ConfiguracaoController@salvar');

// Rotas que precisam de verificação de permissão
$router->group(['middleware' => 'CheckPermission'], function($router) {
    $router->get('/documentos', 'DocumentoController@index');
    $router->get('/consulta', 'ConsultaController@index');
    $router->get('/relatorios', 'RelatorioController@index');
    $router->get('/alertas', 'AlertaController@index');
    $router->get('/financeiro', 'FinanceiroController@index');
    $router->get('/configuracoes', 'ConfiguracaoController@index');
});

$router->get('/suporte', 'SuporteController@index');

// Rotas de usuários
$router->get('/usuarios', 'UsuarioController@index');
$router->post('/usuarios/editar', 'UsuarioController@editar');
$router->post('/usuarios/inativar', 'UsuarioController@inativar');
$router->post('/usuarios/ativar', 'UsuarioController@ativar');
$router->post('/usuarios/resetar-senha', 'UsuarioController@resetarSenha');
$router->post('/empresas/atualizar', 'EmpresaController@atualizar'); 
