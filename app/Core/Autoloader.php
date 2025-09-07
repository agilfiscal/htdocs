<?php
class Autoloader {
    public static function register() {
        spl_autoload_register(function ($class) {
            // Converte namespace para caminho do arquivo
            $file = str_replace('\\', DIRECTORY_SEPARATOR, $class);
            $file = __DIR__ . '/../../' . $file . '.php';
            $file = str_replace('/App/', '/app/', $file); // CORRIGE O CASE
            
            // Se o arquivo existir, carrega-o
            if (file_exists($file)) {
                require_once $file;
                return true;
            }
            return false;
        });
    }
}