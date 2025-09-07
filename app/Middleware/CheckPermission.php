<?php
namespace App\Middleware;

use PDO;
use PDOException;

class CheckPermission {
    protected $db;
    protected $modulosRestritos = [
        'documentos' => 'Gestão de documentos',
        'consulta' => 'Consulta Tributária',
        'relatorios' => 'Relatórios',
        'alertas' => 'Alertas / Notificações',
        'financeiro' => 'Financeiro',
        'configuracoes' => 'Configurações'
    ];

    public function __construct() {
        try {
            $this->db = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8",
                DB_USER,
                DB_PASS,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
        } catch (PDOException $e) {
            die("Erro de conexão: " . $e->getMessage());
        }
    }

    public function handle() {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: /login');
            exit;
        }

        $stmt = $this->db->prepare("SELECT tipo FROM usuarios WHERE id = ?");
        $stmt->execute([$_SESSION['usuario_id']]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        // Se for admin ou master, permite acesso a tudo
        if ($usuario['tipo'] === 'admin' || $usuario['tipo'] === 'master') {
            return true;
        }

        // Se for usuário tipo 3, verifica as permissões
        if ($usuario['tipo'] === '3') {
            $modulo = $this->getModuloFromUrl($_SERVER['REQUEST_URI']);
            
            // Se o módulo estiver na lista de restritos, verifica a permissão
            if (isset($this->modulosRestritos[$modulo])) {
                $stmt = $this->db->prepare("
                    SELECT permitido 
                    FROM usuario_permissoes 
                    WHERE usuario_id = ? AND modulo = ?
                ");
                $stmt->execute([$_SESSION['usuario_id'], $modulo]);
                $permissao = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$permissao || !$permissao['permitido']) {
                    $_SESSION['erro'] = 'Você não tem permissão para acessar este módulo.';
                    header('Location: /mde');
                    exit;
                }
            }
        }

        return true;
    }

    protected function getModuloFromUrl($url) {
        $path = parse_url($url, PHP_URL_PATH);
        $segments = explode('/', trim($path, '/'));
        
        // Remove o prefixo 'mde' se existir
        if ($segments[0] === 'mde') {
            array_shift($segments);
        }
        
        return $segments[0] ?? '';
    }
} 