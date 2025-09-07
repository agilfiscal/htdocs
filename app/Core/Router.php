<?php
class Router {
    protected $routes = [];
    protected $params = [];
    protected $basePath;
    protected $middlewareGroups = [];
    protected $routeMiddleware = [];

    public function __construct() {
        // Obtém o caminho base do projeto
        $scriptName = $_SERVER['SCRIPT_NAME'];
        $this->basePath = dirname($scriptName);
        
        // Debug
        error_log("Router inicializado com basePath: " . $this->basePath);
        error_log("SCRIPT_NAME: " . $scriptName);

        // Carrega as rotas do arquivo de configuração
        $routesConfig = require APP_ROOT . '/config/routes.php';
        
        // Carrega os grupos de middleware
        if (isset($routesConfig['middleware_groups'])) {
            $this->middlewareGroups = $routesConfig['middleware_groups'];
        }
        
        // Carrega os middlewares de rota
        if (isset($routesConfig['route_middleware'])) {
            $this->routeMiddleware = $routesConfig['route_middleware'];
        }
        
        if (isset($routesConfig['routes'])) {
            foreach ($routesConfig['routes'] as $route => $handler) {
                // Extrai o método HTTP e o caminho
                list($method, $path) = explode(' ', $route);
                
                // Verifica se o handler é um array com middleware
                if (is_array($handler)) {
                    $controllerAction = $handler[0];
                    $middleware = $handler[1]['middleware'] ?? [];
                } else {
                    $controllerAction = $handler;
                    $middleware = [];
                }
                
                // Extrai o controller e a action
                list($controller, $action) = explode('@', $controllerAction);
                
                // Adiciona a rota
                $this->add($path, [
                    'controller' => $controller,
                    'action' => $action,
                    'method' => $method,
                    'middleware' => $middleware
                ]);
            }
        }

        // Adiciona as rotas específicas
        $this->add('auditoria/danfe/{chave}', ['controller' => 'AuditoriaController', 'action' => 'danfe']);
        $this->add('auditoria/baixarXml/{chave}', ['controller' => 'AuditoriaController', 'action' => 'baixarXml']);
    }

    public function add($route, $params = []) {
        // Remove barras iniciais e finais
        $route = trim($route, '/');
        
        // Converte a rota em uma expressão regular
        $route = preg_replace('/\//', '\\/', $route);
        $route = preg_replace('/\{([a-z_]+)\}/', '(?P<$1>[a-zA-Z0-9_-]+)', $route);
        $route = '/^' . $route . '$/i';
        
        $this->routes[$route] = $params;
        
        // Debug
        error_log("Rota adicionada: " . $route . " => " . print_r($params, true));
    }

    public function match($url) {
        // Remove o caminho base da URL
        $url = str_replace($this->basePath, '', $url);
        $url = trim($url, '/');
        
        // Se a URL estiver vazia, considera como a rota raiz
        if (empty($url)) {
            $url = '';
        }

        // Obtém o método HTTP da requisição
        $method = $_SERVER['REQUEST_METHOD'];

        // Debug
        error_log("URL sendo processada: " . $url);
        error_log("Método HTTP: " . $method);
        error_log("Rotas registradas: " . print_r($this->routes, true));

        foreach ($this->routes as $route => $params) {
            if (preg_match($route, $url, $matches)) {
                // Verifica se o método HTTP corresponde
                if (isset($params['method']) && $params['method'] !== $method) {
                    continue;
                }

                foreach ($matches as $key => $match) {
                    if (is_string($key)) {
                        $params[$key] = $match;
                    }
                }
                $this->params = $params;
                return true;
            }
        }
        return false;
    }

    protected function processMiddleware($middleware) {
        if (empty($middleware)) {
            return true;
        }

        foreach ($middleware as $name) {
            // Verifica se é um grupo de middleware
            if (isset($this->middlewareGroups[$name])) {
                foreach ($this->middlewareGroups[$name] as $groupMiddleware) {
                    $instance = new $groupMiddleware();
                    if (!$instance->handle()) {
                        return false;
                    }
                }
            }
            // Verifica se é um middleware de rota
            elseif (isset($this->routeMiddleware[$name])) {
                $instance = new $this->routeMiddleware[$name]();
                if (!$instance->handle()) {
                    return false;
                }
            }
        }
        return true;
    }

    public function dispatch() {
        $url = $_SERVER['REQUEST_URI'];
        $url = parse_url($url, PHP_URL_PATH); // Ignora query string
        // Debug
        error_log("URL original: " . $url);
        error_log("Caminho base: " . $this->basePath);
        
        if ($this->match($url)) {
            // Processa os middlewares
            if (isset($this->params['middleware'])) {
                if (!$this->processMiddleware($this->params['middleware'])) {
                    return;
                }
            }

            $controller = $this->params['controller'];
            $controller = $this->getNamespace() . $controller;

            // Debug
            error_log("Controller: " . $controller);
            error_log("Action: " . $this->params['action']);
            error_log("Parâmetros: " . print_r($this->params, true));

            if (class_exists($controller)) {
                $controller_object = new $controller($this->params);

                $action = $this->params['action'];
                if (method_exists($controller_object, $action)) {
                    // Passar os parâmetros como um array associativo
                    $params = $this->params;
                    unset($params['controller'], $params['action'], $params['namespace'], $params['middleware']);
                    
                    // Se houver um ID nos parâmetros, passa ele diretamente
                    if (isset($params['id'])) {
                        $controller_object->$action($params['id']);
                    } else {
                        $controller_object->$action($params);
                    }
                } else {
                    throw new \Exception("Método $action não encontrado no controller $controller");
                }
            } else {
                throw new \Exception("Controller $controller não encontrado");
            }
        } else {
            // Debug
            error_log("Rota não encontrada para URL: " . $url);
            error_log("Rotas disponíveis: " . print_r($this->routes, true));
            
            throw new \Exception('Rota não encontrada', 404);
        }
    }

    protected function getNamespace() {
        $namespace = 'App\\Controllers\\';
        if (array_key_exists('namespace', $this->params)) {
            $namespace .= $this->params['namespace'] . '\\';
        }
        return $namespace;
    }

    public function getRoutes() {
        return $this->routes;
    }
} 