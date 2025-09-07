<?php
namespace App\Controllers;

use App\Models\Empresa;
use App\Models\Certificado;

class CertificadoController {
    protected $db;
    protected $empresaModel;
    protected $certificadoModel;

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
        $this->empresaModel = new Empresa($this->db);
        $this->certificadoModel = new Certificado($this->db);
    }

    public function cadastrar() {
        $erro = null;
        $sucesso = null;
        $empresas = $this->empresaModel->all();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $empresa_id = $_POST['empresa_id'] ?? '';
            $senha = $_POST['senha'] ?? '';
            if (!$empresa_id || !$senha || !isset($_FILES['arquivo']) || $_FILES['arquivo']['error'] !== UPLOAD_ERR_OK) {
                $erro = 'Preencha todos os campos e selecione o arquivo.';
            } else {
                // Gera o nome do arquivo com timestamp
                $timestamp = uniqid('cert_');
                $nomeOriginal = basename($_FILES['arquivo']['name']);
                $nomeArquivo = $timestamp . '_' . $nomeOriginal;
                $destino = APP_ROOT . '/Certificados/' . $nomeArquivo;
                
                if (!is_dir(APP_ROOT . '/Certificados')) {
                    mkdir(APP_ROOT . '/Certificados', 0775, true);
                }
                
                if (move_uploaded_file($_FILES['arquivo']['tmp_name'], $destino)) {
                    // Armazena apenas o timestamp no banco
                    $this->certificadoModel->create([
                        'empresa_id' => $empresa_id,
                        'arquivo' => $timestamp,
                        'senha' => password_hash($senha, PASSWORD_DEFAULT)
                    ]);
                    $sucesso = 'Certificado cadastrado com sucesso!';
                } else {
                    $erro = 'Erro ao salvar o arquivo.';
                }
            }
        }
        $content = view('certificados/cadastrar', [
            'erro' => $erro,
            'sucesso' => $sucesso,
            'empresas' => $empresas
        ], true);
        require APP_ROOT . '/app/Views/layouts/main.php';
    }

    public function index() {
        $certificados = $this->certificadoModel->all();
        $content = view('certificados/index', ['certificados' => $certificados], true);
        require APP_ROOT . '/app/Views/layouts/main.php';
    }

    public function download($id) {
        error_log('ID recebido no download: ' . print_r($id, true));
        error_log('Tipo do ID: ' . gettype($id));
        
        // Converte o ID para inteiro se for string
        if (is_string($id)) {
            $id = (int)$id;
        }
        
        if (!is_numeric($id)) {
            die('ID inválido. Valor recebido: ' . $id);
        }
        
        $cert = $this->certificadoModel->find($id);
        if (!$cert) {
            die('Certificado não encontrado.');
        }
        
        if (!isset($cert['arquivo']) || empty($cert['arquivo'])) {
            die('Nome do arquivo não encontrado no certificado.');
        }
        
        // Procura o arquivo no diretório
        $dir = APP_ROOT . '/Certificados/';
        $files = glob($dir . $cert['arquivo'] . '_*');
        
        if (empty($files)) {
            die('Arquivo não encontrado.');
        }
        
        $file = $files[0]; // Pega o primeiro arquivo encontrado
        
        header('Content-Description: File Transfer');
        header('Content-Type: application/x-pkcs12');
        header('Content-Disposition: attachment; filename="' . basename($file) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        readfile($file);
        exit;
    }

    public function excluir($id) {
        $cert = $this->certificadoModel->find($id);
        if ($cert) {
            $file = APP_ROOT . '/Certificados/' . $cert['arquivo'];
            if (file_exists($file)) {
                unlink($file);
            }
            $this->certificadoModel->delete($id);
        }
        header('Location: /mde/certificados');
        exit;
    }

    // Validação real da senha do .pfx
    private function validarSenhaPfx($arquivo, $senha) {
        if (!extension_loaded('openssl')) return true;
        $pkcs12 = file_get_contents($arquivo);
        $certs = [];
        $ok = openssl_pkcs12_read($pkcs12, $certs, $senha);
        if (!$ok) {
            error_log('Erro ao validar certificado: ' . openssl_error_string());
        }
        return $ok;
    }
} 