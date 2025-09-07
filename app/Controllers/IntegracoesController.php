<?php
namespace App\Controllers;

class IntegracoesController {
    protected $db;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: /mde/login');
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
        $integracoes = $this->db->query('SELECT * FROM integracoes ORDER BY nome')->fetchAll();
        
        $content = view('integracoes/index', [
            'integracoes' => $integracoes
        ], true);
        
        require APP_ROOT . '/app/Views/layouts/main.php';
    }

    public function criar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $nome = $_POST['nome'] ?? '';
                $tipo = $_POST['tipo'] ?? '';
                $config = $_POST['config'] ?? '';
                $status = $_POST['status'] ?? 'inativo';
                
                if (empty($nome) || empty($tipo)) {
                    throw new \Exception('Nome e tipo são obrigatórios');
                }
                
                // Validar configuração JSON
                if (!empty($config)) {
                    json_decode($config);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        throw new \Exception('Configuração inválida (formato JSON)');
                    }
                }
                
                $sql = 'INSERT INTO integracoes (nome, tipo, config, status, created_at) VALUES (?, ?, ?, ?, NOW())';
                $stmt = $this->db->prepare($sql);
                $stmt->execute([$nome, $tipo, $config, $status]);
                
                header('Location: /mde/integracoes?success=1');
                exit;
            } catch (\Exception $e) {
                $error = 'Erro ao criar integração: ' . $e->getMessage();
            }
        }
        
        $content = view('integracoes/criar', [
            'error' => $error ?? null
        ], true);
        
        require APP_ROOT . '/app/Views/layouts/main.php';
    }

    public function editar($id) {
        $integracao = $this->db->prepare('SELECT * FROM integracoes WHERE id = ?');
        $integracao->execute([$id]);
        $integracao = $integracao->fetch();
        
        if (!$integracao) {
            header('Location: /mde/integracoes?error=Integração não encontrada');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $nome = $_POST['nome'] ?? '';
                $tipo = $_POST['tipo'] ?? '';
                $config = $_POST['config'] ?? '';
                $status = $_POST['status'] ?? 'inativo';
                
                if (empty($nome) || empty($tipo)) {
                    throw new \Exception('Nome e tipo são obrigatórios');
                }
                
                // Validar configuração JSON
                if (!empty($config)) {
                    json_decode($config);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        throw new \Exception('Configuração inválida (formato JSON)');
                    }
                }
                
                $sql = 'UPDATE integracoes SET nome = ?, tipo = ?, config = ?, status = ? WHERE id = ?';
                $stmt = $this->db->prepare($sql);
                $stmt->execute([$nome, $tipo, $config, $status, $id]);
                
                header('Location: /mde/integracoes?success=1');
                exit;
            } catch (\Exception $e) {
                $error = 'Erro ao atualizar integração: ' . $e->getMessage();
            }
        }
        
        $content = view('integracoes/editar', [
            'integracao' => $integracao,
            'error' => $error ?? null
        ], true);
        
        require APP_ROOT . '/app/Views/layouts/main.php';
    }

    public function excluir($id) {
        try {
            $sql = 'DELETE FROM integracoes WHERE id = ?';
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            
            header('Location: /mde/integracoes?success=1');
        } catch (\Exception $e) {
            header('Location: /mde/integracoes?error=' . urlencode($e->getMessage()));
        }
        exit;
    }

    public function testar($id) {
        try {
            $integracao = $this->db->prepare('SELECT * FROM integracoes WHERE id = ?');
            $integracao->execute([$id]);
            $integracao = $integracao->fetch();
            
            if (!$integracao) {
                throw new \Exception('Integração não encontrada');
            }
            
            if ($integracao['status'] !== 'ativo') {
                throw new \Exception('Integração está inativa');
            }
            
            $config = json_decode($integracao['config'], true);
            if (!$config) {
                throw new \Exception('Configuração inválida');
            }
            
            // Aqui você implementaria a lógica específica para cada tipo de integração
            // Por exemplo, para uma integração com ERP:
            if ($integracao['tipo'] === 'erp') {
                // Testar conexão com API do ERP
                $response = $this->testarConexaoERP($config);
                echo json_encode(['success' => true, 'message' => 'Conexão com ERP estabelecida com sucesso']);
            } else {
                throw new \Exception('Tipo de integração não suportado');
            }
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    protected function testarConexaoERP($config) {
        // Implementar lógica de teste para ERP
        // Por exemplo, fazer uma requisição para a API do ERP
        $ch = curl_init($config['url'] . '/test');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $config['token'],
            'Content-Type: application/json'
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode !== 200) {
            throw new \Exception('Erro ao conectar com ERP: ' . $response);
        }
        
        return $response;
    }
} 