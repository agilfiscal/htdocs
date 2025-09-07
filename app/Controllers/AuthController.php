<?php
namespace App\Controllers;

use App\Models\Usuario;
use App\Models\Empresa;

class AuthController {
    protected $db;
    protected $usuarioModel;
    protected $empresaModel;

    public function __construct() {
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

    public function login() {
        $erro = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $senha = $_POST['senha'] ?? '';
            $usuario = $this->usuarioModel->findByEmail($email);
            if ($usuario && password_verify($senha, $usuario['password'])) {
                $tipo = $usuario['tipo'];
                if (!is_numeric($tipo)) {
                    if ($tipo === 'admin') $tipo = 1;
                    elseif ($tipo === 'master') $tipo = 2;
                    elseif ($tipo === 'operator') $tipo = 3;
                }
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['tipo'] = $usuario['tipo'];
                $_SESSION['tipo_usuario'] = $tipo;
                $_SESSION['nome'] = $usuario['nome'];
                header('Location: /dashboard');
                exit;
            } else {
                $erro = 'E-mail ou senha inválidos.';
            }
        }
        $content = view('auth/login', ['erro' => $erro], true);
        require APP_ROOT . '/app/Views/layouts/main.php';
    }

    public function registro() {
        $erro = null;
        $sucesso = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nome = trim($_POST['nome'] ?? '');
            $telefone = trim($_POST['telefone'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $senha = $_POST['senha'] ?? '';
            $senha2 = $_POST['senha2'] ?? '';
            $cnpj = preg_replace('/\D/', '', $_POST['cnpj'] ?? '');

            if (!$nome || !$telefone || !$email || !$senha || !$senha2 || !$cnpj) {
                $erro = 'Preencha todos os campos.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $erro = 'E-mail inválido.';
            } elseif ($senha !== $senha2) {
                $erro = 'As senhas não coincidem.';
            } elseif ($this->usuarioModel->findByEmail($email)) {
                $erro = 'E-mail já cadastrado.';
            } else {
                // Verifica se a empresa já existe
                $empresa = $this->empresaModel->findByCnpj($cnpj);
                if (!$empresa) {
                    // Cria a empresa
                    $this->empresaModel->create([
                        'cnpj' => $cnpj,
                        'razao_social' => 'Empresa ' . $cnpj,
                        'ativo' => 1
                    ]);
                    $empresa = $this->empresaModel->findByCnpj($cnpj);
                }
                // Cria o usuário
                $usuarioId = $this->usuarioModel->create([
                    'nome' => $nome,
                    'email' => $email,
                    'telefone' => $telefone,
                    'password' => password_hash($senha, PASSWORD_DEFAULT),
                    'tipo' => 3,
                    'ativo' => 1
                ]);
                // Vincula usuário à empresa
                $stmt = $this->db->prepare('INSERT INTO usuario_empresas (usuario_id, empresa_id) VALUES (?, ?)');
                $stmt->execute([$usuarioId, $empresa['id']]);
                $sucesso = 'Cadastro realizado com sucesso! Agora você pode fazer login.';
            }
        }
        $content = view('auth/login', ['erro' => $erro, 'sucesso' => $sucesso], true);
        require APP_ROOT . '/app/Views/layouts/main.php';
    }

    public function logout() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (isset($_SESSION['usuario_id'])) {
            $stmt = $this->db->prepare('DELETE FROM empresas_atendidas WHERE usuario_id = ?');
            $stmt->execute([$_SESSION['usuario_id']]);
        }
        session_destroy();
        header('Location: /login');
        exit;
    }
} 