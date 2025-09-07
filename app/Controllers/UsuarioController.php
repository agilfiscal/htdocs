<?php
namespace App\Controllers;

use App\Models\Usuario;
use App\Models\Empresa;

class UsuarioController {
    protected $db;
    protected $usuarioModel;
    protected $empresaModel;

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
        $this->usuarioModel = new Usuario($this->db);
        $this->empresaModel = new Empresa($this->db);
    }

    public function index() {
        $pagina = isset($_GET['pagina']) && is_numeric($_GET['pagina']) && $_GET['pagina'] > 0 ? (int)$_GET['pagina'] : 1;
        $porPagina = 10;
        $offset = ($pagina - 1) * $porPagina;
        // Conta total de usuários
        $stmtCount = $this->db->query('SELECT COUNT(*) FROM usuarios');
        $totalUsuarios = $stmtCount->fetchColumn();
        $totalPaginas = ceil($totalUsuarios / $porPagina);
        $usuarios = $this->usuarioModel->allWithEmpresas($porPagina, $offset);
        $empresas = $this->empresaModel->all();
        $content = view('usuarios/index', [
            'usuarios' => $usuarios,
            'empresas' => $empresas,
            'pagina' => $pagina,
            'totalPaginas' => $totalPaginas
        ], true);
        require APP_ROOT . '/app/Views/layouts/main.php';
    }

    public function editar() {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['erro' => 'Método não permitido.']);
            exit;
        }

        // Verifica se o usuário logado é do tipo operator
        $stmt = $this->db->prepare('SELECT tipo FROM usuarios WHERE id = ?');
        $stmt->execute([$_SESSION['usuario_id']]);
        $tipoUsuarioLogado = $stmt->fetchColumn();

        $id = $_POST['id'] ?? null;
        $nome = $_POST['nome'] ?? '';
        $email = $_POST['email'] ?? '';
        $telefone = $_POST['telefone'] ?? '';

        if (!$id || !$nome || !$email) {
            echo json_encode(['erro' => 'Preencha todos os campos obrigatórios.']);
            exit;
        }

        // Busca o usuário no banco de dados
        $stmt = $this->db->prepare('SELECT tipo FROM usuarios WHERE id = ?');
        $stmt->execute([$id]);
        $usuario = $stmt->fetch();

        if (!$usuario) {
            echo json_encode(['erro' => 'Usuário não encontrado.']);
            exit;
        }

        // Prepara os dados para atualização
        $dados = [
            'nome' => $nome,
            'email' => $email,
            'telefone' => $telefone
        ];

        // Se não for usuário operator editando ele mesmo, permite alterar o tipo
        if (!($tipoUsuarioLogado === 'operator' && $id == $_SESSION['usuario_id'])) {
            $tipo = $_POST['tipo'] ?? '';
            if (!$tipo) {
                echo json_encode(['erro' => 'Tipo é obrigatório.']);
                exit;
            }
            $dados['tipo'] = $tipo;
        }

        $ok = $this->usuarioModel->update($id, $dados);

        // VÍNCULO DE EMPRESAS (apenas admin)
        if ($tipoUsuarioLogado === 'admin') {
            $empresas = $_POST['empresas'] ?? [];
            // Remove vínculos antigos
            $stmt = $this->db->prepare('DELETE FROM usuario_empresas WHERE usuario_id = ?');
            $stmt->execute([$id]);
            // Adiciona os novos vínculos
            foreach ($empresas as $empresa_id) {
                $stmt = $this->db->prepare('INSERT INTO usuario_empresas (usuario_id, empresa_id) VALUES (?, ?)');
                $stmt->execute([$id, $empresa_id]);
            }
        }

        echo json_encode(['sucesso' => true]);
        exit;
    }

    public function resetarSenha() {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['erro' => 'Método não permitido.']);
            exit;
        }
        $id = $_POST['id'] ?? null;
        $novaSenha = $_POST['nova_senha'] ?? '';
        if (!$id || !$novaSenha) {
            echo json_encode(['erro' => 'Preencha todos os campos.']);
            exit;
        }
        $ok = $this->usuarioModel->updateSenha($id, $novaSenha);
        if ($ok) {
            echo json_encode(['sucesso' => true]);
        } else {
            echo json_encode(['erro' => 'Erro ao resetar senha.']);
        }
        exit;
    }

    public function inativar() {
        error_log("DEBUG inativar: Entrou no método");
        error_log("DEBUG inativar: Método HTTP: " . json_encode($_SERVER['REQUEST_METHOD']));
        $inputRaw = file_get_contents('php://input');
        error_log("DEBUG inativar: Input bruto: " . $inputRaw);
        // Reposiciona o ponteiro do php://input para leitura posterior
        $data = json_decode($inputRaw, true);
        // Desabilita a exibição de erros do PHP
        ini_set('display_errors', 0);
        error_reporting(0);
        
        // Limpa qualquer saída anterior
        if (ob_get_length()) ob_clean();
        
        // Força o tipo de conteúdo como JSON
        header('Content-Type: application/json; charset=utf-8');
        
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new \Exception('Método não permitido');
            }

            if (empty($inputRaw)) {
                throw new \Exception('Dados não fornecidos');
            }

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Erro ao decodificar JSON: ' . json_last_error_msg());
            }

            $id = $data['id'] ?? null;
            if (!$id) {
                throw new \Exception('ID do usuário não fornecido');
            }

            // Verificar estrutura da tabela
            $stmt = $this->db->query("SHOW COLUMNS FROM usuarios LIKE 'ativo'");
            $coluna = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$coluna) {
                throw new \Exception('Coluna "ativo" não encontrada na tabela usuarios');
            }
            
            error_log("Tentando inativar usuário ID: " . $id);
            
            $resultado = $this->usuarioModel->update($id, ['ativo' => 0]);
            
            if (!$resultado) {
                throw new \Exception('Falha ao atualizar o registro');
            }
            
            die(json_encode(['sucesso' => true]));
            
        } catch (\Exception $e) {
            error_log("DEBUG inativar: Exceção capturada: " . $e->getMessage());
            http_response_code(500);
            die(json_encode(['erro' => $e->getMessage()]));
        }
    }

    public function ativar() {
        // Desabilita a exibição de erros do PHP
        ini_set('display_errors', 0);
        error_reporting(0);
        
        // Limpa qualquer saída anterior
        if (ob_get_length()) ob_clean();
        
        // Força o tipo de conteúdo como JSON
        header('Content-Type: application/json; charset=utf-8');
        
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new \Exception('Método não permitido');
            }

            $input = file_get_contents('php://input');
            if (empty($input)) {
                throw new \Exception('Dados não fornecidos');
            }

            $data = json_decode($input, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Erro ao decodificar JSON: ' . json_last_error_msg());
            }

            $id = $data['id'] ?? null;
            if (!$id) {
                throw new \Exception('ID do usuário não fornecido');
            }

            // Verificar estrutura da tabela
            $stmt = $this->db->query("SHOW COLUMNS FROM usuarios LIKE 'ativo'");
            $coluna = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$coluna) {
                throw new \Exception('Coluna "ativo" não encontrada na tabela usuarios');
            }
            
            error_log("Tentando ativar usuário ID: " . $id);
            
            $resultado = $this->usuarioModel->update($id, ['ativo' => 1]);
            
            if (!$resultado) {
                throw new \Exception('Falha ao atualizar o registro');
            }
            
            die(json_encode(['sucesso' => true]));
            
        } catch (\Exception $e) {
            error_log("Erro ao ativar usuário: " . $e->getMessage());
            http_response_code(500);
            die(json_encode(['erro' => $e->getMessage()]));
        }
    }

    // NOVO ENDPOINT: Retorna empresas vinculadas a um usuário
    public function empresasVinculadas() {
        header('Content-Type: application/json');
        $usuario_id = $_GET['id'] ?? null;
        if (!$usuario_id) {
            echo json_encode(['empresas_vinculadas' => []]);
            exit;
        }
        $stmt = $this->db->prepare('SELECT empresa_id FROM usuario_empresas WHERE usuario_id = ?');
        $stmt->execute([$usuario_id]);
        $empresas = $stmt->fetchAll(\PDO::FETCH_COLUMN);
        echo json_encode(['empresas_vinculadas' => $empresas]);
        exit;
    }
} 