<?php
namespace App\Controllers;

class WebhooksController {
    protected $db;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: /login');
            exit;
        }
        $this->db = new \PDO(
            'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
            DB_USER,
            DB_PASS,
            [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
            ]
        );
    }

    public function index() {
        $webhooks = $this->db->query('SELECT * FROM webhooks ORDER BY created_at DESC')->fetchAll();
        
        $content = view('webhooks/index', [
            'webhooks' => $webhooks
        ], true);
        
        require APP_ROOT . '/app/Views/layouts/main.php';
    }

    public function criar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $url = $_POST['url'] ?? '';
            $evento = $_POST['evento'] ?? '';
            $ativo = isset($_POST['ativo']) ? 1 : 0;
            
            try {
                $sql = 'INSERT INTO webhooks (url, evento, ativo, created_at) VALUES (?, ?, ?, NOW())';
                $stmt = $this->db->prepare($sql);
                $stmt->execute([$url, $evento, $ativo]);
                
                header('Location: /webhooks?success=1');
                exit;
            } catch (\Exception $e) {
                $error = 'Erro ao criar webhook: ' . $e->getMessage();
            }
        }
        
        $content = view('webhooks/criar', [
            'error' => $error ?? null
        ], true);
        
        require APP_ROOT . '/app/Views/layouts/main.php';
    }

    public function editar($id) {
        $webhook = $this->db->prepare('SELECT * FROM webhooks WHERE id = ?');
        $webhook->execute([$id]);
        $webhook = $webhook->fetch();
        
        if (!$webhook) {
            header('Location: /webhooks');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $url = $_POST['url'] ?? '';
            $evento = $_POST['evento'] ?? '';
            $ativo = isset($_POST['ativo']) ? 1 : 0;
            
            try {
                $sql = 'UPDATE webhooks SET url = ?, evento = ?, ativo = ? WHERE id = ?';
                $stmt = $this->db->prepare($sql);
                $stmt->execute([$url, $evento, $ativo, $id]);
                
                header('Location: /webhooks?success=1');
                exit;
            } catch (\Exception $e) {
                $error = 'Erro ao atualizar webhook: ' . $e->getMessage();
            }
        }
        
        $content = view('webhooks/editar', [
            'webhook' => $webhook,
            'error' => $error ?? null
        ], true);
        
        require APP_ROOT . '/app/Views/layouts/main.php';
    }

    public function excluir($id) {
        try {
            $sql = 'DELETE FROM webhooks WHERE id = ?';
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            
            header('Location: /webhooks?success=1');
        } catch (\Exception $e) {
            header('Location: /webhooks?error=' . urlencode($e->getMessage()));
        }
        exit;
    }

    public function testar($id) {
        $webhook = $this->db->prepare('SELECT * FROM webhooks WHERE id = ?');
        $webhook->execute([$id]);
        $webhook = $webhook->fetch();
        
        if (!$webhook) {
            echo json_encode(['error' => 'Webhook não encontrado']);
            exit;
        }
        
        try {
            $ch = curl_init($webhook['url']);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
                'evento' => $webhook['evento'],
                'teste' => true,
                'timestamp' => date('Y-m-d H:i:s')
            ]));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            echo json_encode([
                'success' => true,
                'http_code' => $httpCode,
                'response' => $response
            ]);
        } catch (\Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
        exit;
    }
} 