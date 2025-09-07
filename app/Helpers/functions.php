<?php

if (!function_exists('env')) {
    /**
     * Obtém o valor de uma variável de ambiente
     */
    function env($key, $default = null) {
        $value = getenv($key);
        if ($value === false) {
            return $default;
        }
        return $value;
    }
}

if (!function_exists('config')) {
    /**
     * Obtém uma configuração do arquivo config.php
     */
    function config($key, $default = null) {
        global $config;
        return $config[$key] ?? $default;
    }
}

if (!function_exists('view')) {
    /**
     * Renderiza uma view
     * @param string $name Nome da view (ex: 'home/index')
     * @param array $data Dados para a view
     * @param bool $return Se true, retorna o conteúdo como string
     */
    function view($name, $data = [], $return = false) {
        extract($data);
        $viewPath = APP_ROOT . '/app/Views/' . $name . '.php';
        if (file_exists($viewPath)) {
            ob_start();
            require $viewPath;
            $content = ob_get_clean();
            if ($return) {
                return $content;
            } else {
                echo $content;
            }
        } else {
            throw new Exception("View {$name} não encontrada");
        }
    }
}

if (!function_exists('redirect')) {
    /**
     * Redireciona para uma URL
     */
    function redirect($url) {
        header('Location: ' . $url);
        exit;
    }
}

if (!function_exists('asset')) {
    /**
     * Gera URL para um asset
     */
    function asset($path) {
        return APP_URL . '/assets/' . ltrim($path, '/');
    }
}

if (!function_exists('url')) {
    /**
     * Gera URL para uma rota
     */
    function url($path = '') {
        return APP_URL . '/' . ltrim($path, '/');
    }
}

if (!function_exists('csrf_token')) {
    /**
     * Gera um token CSRF
     */
    function csrf_token() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
}

if (!function_exists('csrf_field')) {
    /**
     * Gera um campo hidden com o token CSRF
     */
    function csrf_field() {
        return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
    }
}

if (!function_exists('old')) {
    /**
     * Retorna o valor antigo de um campo de formulário
     */
    function old($key, $default = '') {
        return $_SESSION['old'][$key] ?? $default;
    }
}

if (!function_exists('flash')) {
    /**
     * Define uma mensagem flash
     */
    function flash($key, $message) {
        $_SESSION['flash'][$key] = $message;
    }
}

if (!function_exists('has_flash')) {
    /**
     * Verifica se existe uma mensagem flash
     */
    function has_flash($key) {
        return isset($_SESSION['flash'][$key]);
    }
}

if (!function_exists('get_flash')) {
    /**
     * Obtém e remove uma mensagem flash
     */
    function get_flash($key) {
        if (has_flash($key)) {
            $message = $_SESSION['flash'][$key];
            unset($_SESSION['flash'][$key]);
            return $message;
        }
        return null;
    }
}

if (!function_exists('dd')) {
    /**
     * Debug and die
     */
    function dd($data) {
        echo '<pre>';
        var_dump($data);
        echo '</pre>';
        die();
    }
}

if (!function_exists('e')) {
    /**
     * Escapa HTML
     */
    function e($string) {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('str_slug')) {
    /**
     * Converte string para slug
     */
    function str_slug($string) {
        $string = preg_replace('/[\'"]/', '', $string);
        $string = preg_replace('/[^a-zA-Z0-9\s]/', '', $string);
        $string = strtolower(trim($string));
        $string = preg_replace('/\s+/', '-', $string);
        return $string;
    }
}

if (!function_exists('format_date')) {
    /**
     * Formata uma data
     */
    function format_date($date, $format = 'd/m/Y H:i:s') {
        return date($format, strtotime($date));
    }
}

if (!function_exists('format_money')) {
    /**
     * Formata um valor monetário
     */
    function format_money($value) {
        return 'R$ ' . number_format($value, 2, ',', '.');
    }
}

if (!function_exists('is_active')) {
    /**
     * Verifica se a rota atual está ativa
     */
    function is_active($path) {
        $currentPath = trim($_SERVER['REQUEST_URI'], '/');
        return $currentPath === trim($path, '/') ? 'active' : '';
    }
} 