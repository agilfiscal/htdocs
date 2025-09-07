<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../meu_erro.log');
error_reporting(E_ALL);
require_once __DIR__ . '/../app/Core/Autoloader.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/Core/Router.php';
require_once __DIR__ . '/../app/Helpers/functions.php';

// Registra o autoloader
Autoloader::register();

// Inicializa o roteador
$router = new Router();

// Carrega as rotas
require_once __DIR__ . '/../routes/web.php';
require_once __DIR__ . '/../config/routes.php';

// Inicia a aplicação
$router->dispatch(); 

