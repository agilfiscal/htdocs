<?php

namespace App\Controllers;

class FinanceiroController extends Controller {
    public function index() {
        // Verifica se o usuário está autenticado
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: /mde/login');
            exit;
        }

        $content = $this->view('financeiro/index', [
            'titulo' => 'Financeiro',
            'usuario' => $_SESSION['usuario_nome'] ?? ''
        ], true);

        require APP_ROOT . '/app/Views/layouts/main.php';
    }

    public function agendaPagamentos() {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: /mde/login');
            exit;
        }
        $content = $this->view('financeiro/agenda-pagamentos', [
            'titulo' => 'Agenda de Pagamentos',
            'usuario' => $_SESSION['usuario_nome'] ?? ''
        ], true);
        require APP_ROOT . '/app/Views/layouts/main.php';
    }

    public function assinatura() {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: /mde/login');
            exit;
        }
        $content = $this->view('financeiro/assinatura', [
            'titulo' => 'Assinatura Ágil Fiscal',
            'usuario' => $_SESSION['usuario_nome'] ?? ''
        ], true);
        require APP_ROOT . '/app/Views/layouts/main.php';
    }
} 