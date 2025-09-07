<?php

namespace App\Controllers;

class AlertaController extends Controller {
    public function index() {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: /mde/login');
            exit;
        }

        $content = $this->view('alertas/index', [
            'titulo' => 'Alertas',
            'usuario' => $_SESSION['usuario_nome'] ?? ''
        ], true);

        require APP_ROOT . '/app/Views/layouts/main.php';
    }
} 