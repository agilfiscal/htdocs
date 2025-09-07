<?php

namespace App\Controllers;

class RelatorioController extends Controller {
    public function index() {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: /login');
            exit;
        }

        $content = $this->view('relatorios/index', [
            'titulo' => 'Relatórios',
            'usuario' => $_SESSION['usuario_nome'] ?? ''
        ], true);

        require APP_ROOT . '/app/Views/layouts/main.php';
    }
} 