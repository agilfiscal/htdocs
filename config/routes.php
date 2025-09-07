<?php
return [
    /*
    |--------------------------------------------------------------------------
    | Configurações de Rotas
    |--------------------------------------------------------------------------
    */
    'prefix' => '',
    'middleware' => ['web'],
    'namespace' => 'App\\Controllers',
    
    /*
    |--------------------------------------------------------------------------
    | Rotas da Aplicação
    |--------------------------------------------------------------------------
    */
    'routes' => [
        // Rotas públicas
        'GET /' => 'HomeController@index',
        'GET /sobre' => 'HomeController@sobre',
        'GET /contato' => 'HomeController@contato',
        'GET /test_importar_mde' => 'MdeController@testImportar',
        'GET /suporte' => 'SuporteController@index',
        'GET /rota-teste' => 'HomeController@index',
        
        // Rotas de autenticação
        'GET /login' => 'AuthController@showLoginForm',
        'POST /login' => 'AuthController@login',
        'GET /registro' => 'AuthController@showRegistrationForm',
        'POST /registro' => 'AuthController@register',
        'POST /logout' => 'AuthController@logout',
        
        // Rotas do usuário
        'GET /perfil' => 'UserController@profile',
        'PUT /perfil' => 'UserController@updateProfile',
        'GET /perfil/senha' => 'UserController@showChangePasswordForm',
        'PUT /perfil/senha' => 'UserController@changePassword',
        
        // Rotas de documentos
        'GET /documentos' => ['DocumentoController@index', ['middleware' => ['auth', 'CheckPermission']]],
        'POST /documentos/upload' => ['DocumentoController@upload', ['middleware' => ['auth', 'CheckPermission']]],
        'GET /documentos/detalhes/{id}' => ['DocumentoController@detalhes', ['middleware' => ['auth', 'CheckPermission']]],
        'GET /documentos/download/{id}' => ['DocumentoController@download', ['middleware' => ['auth', 'CheckPermission']]],
        'GET /documentos/editar/{id}' => ['DocumentoController@editar', ['middleware' => ['auth', 'CheckPermission']]],
        'POST /documentos/atualizar/{id}' => ['DocumentoController@atualizar', ['middleware' => ['auth', 'CheckPermission']]],
        'GET /documentos/relatorio' => ['DocumentoController@relatorio', ['middleware' => ['auth', 'CheckPermission']]],
        
        // Rotas de consulta tributária
        // 'GET /consulta-tributaria' => ['ConsultaTributariaController@index', ['middleware' => ['auth', 'CheckPermission']]],
        
        // Rotas do financeiro
        'GET /financeiro' => ['FinanceiroController@index', ['middleware' => ['auth', 'CheckPermission']]],
        
        // Rotas de relatórios
        // 'GET /relatorios' => ['RelatorioController@index', ['middleware' => ['auth', 'CheckPermission']]],
        
        // Rotas de alertas
        // 'GET /alertas' => ['AlertaController@index', ['middleware' => ['auth', 'CheckPermission']]],
        
        // Rotas da API
        'GET /api/users' => 'Api\\UserController@index',
        'GET /api/users/{id}' => 'Api\\UserController@show',
        'POST /api/users' => 'Api\\UserController@store',
        'PUT /api/users/{id}' => 'Api\\UserController@update',
        'DELETE /api/users/{id}' => 'Api\\UserController@destroy',
        
        // Rotas adicionais
        'POST /auditoria/relatorio' => 'AuditoriaController@gerarRelatorio',
        'GET /auditoria/observacao/{id}' => 'AuditoriaController@getObservacao',
        'POST /auditoria/observacao/{id}' => 'AuditoriaController@salvarObservacao',
        'POST /empresas/atualizar' => 'EmpresaController@atualizar',
        
        // Submenus de financeiro
        'GET /financeiro/agenda-pagamentos' => ['FinanceiroController@agendaPagamentos', ['middleware' => ['auth', 'CheckPermission']]],
        // 'GET /financeiro/assinatura' => ['FinanceiroController@assinatura', ['middleware' => ['auth', 'CheckPermission']]],
        
        // Nova rota
        'POST /configuracoes/limpar-dados-empresa' => 'ConfiguracaoController@limparDadosEmpresa',
        
        // Rotas para funcionalidades extras
        'GET /extras/consultar-cnpj' => ['ExtrasController@consultarCnpj', ['middleware' => ['auth', 'CheckPermission']]],
        'POST /extras/buscar-cnpj' => ['ExtrasController@buscarCnpj', ['middleware' => ['auth', 'CheckPermission']]],
        'GET /extras/calculadora-preco' => ['ExtrasController@calculadoraPreco', ['middleware' => ['auth', 'CheckPermission']]],
        'POST /extras/calcular-preco' => ['ExtrasController@calcularPreco', ['middleware' => ['auth', 'CheckPermission']]],
        'GET /extras/construtor-placas' => ['ExtrasController@construtorPlacas', ['middleware' => ['auth', 'CheckPermission']]],
        'POST /extras/buscar-produto-ean' => ['ExtrasController@buscarProdutoPorEan', ['middleware' => ['auth', 'CheckPermission']]],
        'POST /extras/gerar-placa' => ['ExtrasController@gerarPlaca', ['middleware' => ['auth', 'CheckPermission']]],
        'GET /extras/relatorios' => ['ExtrasController@relatorios', ['middleware' => ['auth', 'CheckPermission']]],
        'GET /extras/importacao' => ['ExtrasController@importacao', ['middleware' => ['auth', 'CheckPermission']]],
        'GET /extras/exportacao' => ['ExtrasController@exportacao', ['middleware' => ['auth', 'CheckPermission']]],
        'GET /extras/backup' => ['ExtrasController@backup', ['middleware' => ['auth', 'CheckPermission']]],
        'GET /extras/logs' => ['ExtrasController@logs', ['middleware' => ['auth', 'CheckPermission']]],
        'GET /extras/utilitarios' => ['ExtrasController@utilitarios', ['middleware' => ['auth', 'CheckPermission']]],
        'GET /extras/calculadora-custo' => ['ExtrasController@calculadoraCusto', ['middleware' => ['auth', 'CheckPermission']]],
        'GET /extras/classificacao-desossa' => ['ExtrasController@classificacaoDesossa', ['middleware' => ['auth', 'CheckPermission']]],
        
        // Rotas para gerenciar insumos
        'POST /extras/cadastrar-insumo' => ['ExtrasController@cadastrarInsumo', ['middleware' => ['auth', 'CheckPermission']]],
        'GET /extras/buscar-insumos' => ['ExtrasController@buscarInsumos', ['middleware' => ['auth', 'CheckPermission']]],
        'POST /extras/buscar-insumo-por-id' => ['ExtrasController@buscarInsumoPorId', ['middleware' => ['auth', 'CheckPermission']]],
        'POST /extras/calcular-custo-insumo' => ['ExtrasController@calcularCustoInsumo', ['middleware' => ['auth', 'CheckPermission']]],
        
        // Rotas para gerenciar embalagens
        'POST /extras/cadastrar-embalagem' => ['ExtrasController@cadastrarEmbalagem', ['middleware' => ['auth', 'CheckPermission']]],
        'GET /extras/buscar-embalagens' => ['ExtrasController@buscarEmbalagens', ['middleware' => ['auth', 'CheckPermission']]],
        'POST /extras/buscar-embalagem-por-id' => ['ExtrasController@buscarEmbalagemPorId', ['middleware' => ['auth', 'CheckPermission']]],
        
        // Rotas para gerenciar funcionários
        'POST /extras/cadastrar-funcionario' => ['ExtrasController@cadastrarFuncionario', ['middleware' => ['auth', 'CheckPermission']]],
        'GET /extras/buscar-funcionarios' => ['ExtrasController@buscarFuncionarios', ['middleware' => ['auth', 'CheckPermission']]],
        'POST /extras/buscar-funcionario-por-id' => ['ExtrasController@buscarFuncionarioPorId', ['middleware' => ['auth', 'CheckPermission']]],
        'POST /extras/calcular-valor-hora' => ['ExtrasController@calcularValorHora', ['middleware' => ['auth', 'CheckPermission']]],
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Middleware de Rotas
    |--------------------------------------------------------------------------
    */
    'middleware_groups' => [
        'web' => [
            \App\Middleware\EncryptCookies::class,
            \App\Middleware\VerifyCsrfToken::class,
            \App\Middleware\StartSession::class
        ],
        
        'api' => [
            \App\Middleware\ApiAuthentication::class,
            \App\Middleware\ApiRateLimit::class
        ],
        
        'auth' => [
            \App\Middleware\Authenticate::class
        ],
        
        'guest' => [
            \App\Middleware\RedirectIfAuthenticated::class
        ],
        
        'CheckPermission' => [
            \App\Middleware\CheckPermission::class
        ]
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Middleware de Rota
    |--------------------------------------------------------------------------
    */
    'route_middleware' => [
        'auth' => \App\Middleware\Authenticate::class,
        'guest' => \App\Middleware\RedirectIfAuthenticated::class,
        'admin' => \App\Middleware\AdminMiddleware::class,
        'throttle' => \App\Middleware\ThrottleRequests::class,
        'CheckPermission' => \App\Middleware\CheckPermission::class
    ]
]; 