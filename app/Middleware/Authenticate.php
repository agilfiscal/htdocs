<?php
namespace App\Middleware;

class Authenticate {
    public function handle() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: /login');
            exit;
        }
        
        return true;
    }
} 