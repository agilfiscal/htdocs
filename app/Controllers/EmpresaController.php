<?php
namespace App\Controllers;

use App\Models\Empresa;

class EmpresaController {
    protected $db;
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
        $this->empresaModel = new Empresa($this->db);
    }

    public function index() {
        $busca = isset($_GET['busca']) ? trim($_GET['busca']) : '';
        $pagina = isset($_GET['pagina']) && is_numeric($_GET['pagina']) && $_GET['pagina'] > 0 ? (int)$_GET['pagina'] : 1;
        $porPagina = 10;
        $offset = ($pagina - 1) * $porPagina;
        $db = $this->db;
        $params = [];
        $where = '';
        if ($busca !== '') {
            $where = 'WHERE razao_social LIKE :busca OR cnpj LIKE :busca OR cidade LIKE :busca';
            $params[':busca'] = "%$busca%";
        }
        $tipo_usuario = $_SESSION['tipo_usuario'] ?? ($_SESSION['tipo'] ?? null);
        $usuario_id = $_SESSION['usuario_id'] ?? null;
        if ($tipo_usuario == 3) {
            // Busca empresas vinculadas ao usuário tipo 3
            $sql = 'SELECT e.* FROM empresas e INNER JOIN usuario_empresas ue ON ue.empresa_id = e.id WHERE ue.usuario_id = :usuario_id ORDER BY e.criado_em DESC LIMIT :limit OFFSET :offset';
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':usuario_id', $usuario_id, \PDO::PARAM_INT);
            $stmt->bindValue(':limit', $porPagina, \PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
            $stmt->execute();
            $empresas = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            // Conta total de empresas vinculadas
            $sqlCount = 'SELECT COUNT(*) FROM empresas e INNER JOIN usuario_empresas ue ON ue.empresa_id = e.id WHERE ue.usuario_id = :usuario_id';
            $stmtCount = $db->prepare($sqlCount);
            $stmtCount->bindValue(':usuario_id', $usuario_id, \PDO::PARAM_INT);
            $stmtCount->execute();
            $totalEmpresas = $stmtCount->fetchColumn();
            $totalPaginas = ceil($totalEmpresas / $porPagina);
        } else {
            // Conta total de empresas para paginação
            $sqlCount = "SELECT COUNT(*) FROM empresas $where";
            $stmtCount = $db->prepare($sqlCount);
            $stmtCount->execute($params);
            $totalEmpresas = $stmtCount->fetchColumn();
            $totalPaginas = ceil($totalEmpresas / $porPagina);
            // Busca empresas paginadas
            $sql = "SELECT * FROM empresas $where ORDER BY criado_em DESC LIMIT :limit OFFSET :offset";
            $stmt = $db->prepare($sql);
            foreach ($params as $k => $v) {
                $stmt->bindValue($k, $v);
            }
            $stmt->bindValue(':limit', $porPagina, \PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
            $stmt->execute();
            $empresas = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }
        // Busca todas as empresas para o select de matriz
        $stmt = $this->db->query('
            SELECT id, razao_social 
            FROM empresas 
            ORDER BY razao_social
        ');
        $empresas_matriz = $stmt->fetchAll();
        $content = view('empresas/index', [
            'empresas' => $empresas,
            'empresas_matriz' => $empresas_matriz,
            'pagina' => $pagina,
            'totalPaginas' => $totalPaginas,
            'busca' => $busca,
            'totalEmpresas' => $totalEmpresas
        ], true);
        require APP_ROOT . '/app/Views/layouts/main.php';
    }

    public function cadastrar() {
        $erro = null;
        $empresa = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $cnpj = preg_replace('/\D/', '', $_POST['cnpj'] ?? '');
            if (strlen($cnpj) !== 14) {
                $erro = 'CNPJ inválido.';
            } else {
                $apiUrl = 'https://open.cnpja.com/office/' . $cnpj;
                $ch = curl_init($apiUrl);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                $data = json_decode($response, true);
                if (isset($data['company']['name'])) {
                    $empresa = [
                        'cnpj' => $data['taxId'] ?? $cnpj,
                        'razao_social' => $data['company']['name'] ?? '',
                        'uf' => $data['address']['state'] ?? '',
                        'cidade' => $data['address']['city'] ?? '',
                        'inscricao_estadual' => $data['registrations'][0]['number'] ?? '',
                        'telefone' => isset($data['phones'][0]) ? ($data['phones'][0]['area'] . $data['phones'][0]['number']) : '',
                        'email' => $data['emails'][0]['address'] ?? '',
                        'status' => $data['status']['text'] ?? '',
                        'regime' => $data['company']['nature']['text'] ?? '',
                        'representante_legal' => $data['company']['members'][0]['person']['name'] ?? '',
                        'ativo' => 1
                    ];
                    // Verifica se já existe
                    if (!$this->empresaModel->findByCnpj($empresa['cnpj'])) {
                        $empresaId = $this->empresaModel->create($empresa);
                        // Vincula o usuário logado à empresa criada
                        $stmt = $this->db->prepare('INSERT INTO usuario_empresas (usuario_id, empresa_id) VALUES (?, ?)');
                        $stmt->execute([$_SESSION['usuario_id'], $empresaId]);
                        // Criação automática da pasta e arquivos
                        $baseDir = APP_ROOT . '/public/uploads/' . $empresaId . '/';
                        if (!is_dir($baseDir)) {
                            mkdir($baseDir, 0777, true);
                            file_put_contents($baseDir . 'Auditoria.txt', "");
                            file_put_contents($baseDir . 'Fornecedores.txt', "");
                            file_put_contents($baseDir . 'Manifestadas.txt', "");
                            file_put_contents($baseDir . 'Romaneio.txt', "");
                        }
                        header('Location: /empresas');
                        exit;
                    } else {
                        $erro = 'Empresa já cadastrada.';
                    }
                } else {
                    $erro = 'Empresa não encontrada na API.';
                    $erro .= '<br><small>HTTP: ' . $httpCode . '</small>';
                    $erro .= '<br><small>Resposta: ' . htmlspecialchars($response) . '</small>';
                }
            }
        }
        $content = view('empresas/cadastrar', ['erro' => $erro, 'empresa' => $empresa], true);
        require APP_ROOT . '/app/Views/layouts/main.php';
    }

    public function ativar($id) {
        $this->empresaModel->toggleAtivo($id, 1);
        header('Location: /empresas');
        exit;
    }

    public function inativar($id) {
        $this->empresaModel->toggleAtivo($id, 0);
        header('Location: /empresas');
        exit;
    }

    public function editar($id) {
        $empresa = $this->empresaModel->find($id);
        $erro = null;
        if (!$empresa) {
            $erro = 'Empresa não encontrada.';
        }

        // Busca empresas matriz disponíveis
        $stmt = $this->db->prepare('
            SELECT id, razao_social 
            FROM empresas 
            WHERE tipo_empresa = "matriz" 
            AND id != ? 
            ORDER BY razao_social
        ');
        $stmt->execute([$id]);
        $empresas_matriz = $stmt->fetchAll();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $dados = [
                'razao_social' => $_POST['razao_social'] ?? '',
                'uf' => $_POST['uf'] ?? '',
                'cidade' => $_POST['cidade'] ?? '',
                'inscricao_estadual' => $_POST['inscricao_estadual'] ?? '',
                'telefone' => $_POST['telefone'] ?? '',
                'email' => $_POST['email'] ?? '',
                'status' => $_POST['status'] ?? '',
                'regime' => $_POST['regime'] ?? '',
                'representante_legal' => $_POST['representante_legal'] ?? '',
                'tipo_empresa' => $_POST['tipo_empresa'] ?? 'independente',
                'empresa_matriz_id' => $_POST['tipo_empresa'] === 'filial' ? ($_POST['empresa_matriz_id'] ?? null) : null,
                'ativo' => isset($_POST['ativo']) ? 1 : 0
            ];

            // Validações
            if ($dados['tipo_empresa'] === 'filial' && empty($dados['empresa_matriz_id'])) {
                $erro = 'É necessário selecionar uma empresa matriz para filiais.';
            } else {
                $this->empresaModel->update($id, $dados);
                header('Location: /empresas');
                exit;
            }
        }

        $content = view('empresas/editar', [
            'empresa' => $empresa, 
            'erro' => $erro,
            'empresas_matriz' => $empresas_matriz
        ], true);
        require APP_ROOT . '/app/Views/layouts/main.php';
    }

    public function compartilhar($id) {
        $empresa = $this->empresaModel->find($id);
        $content = view('empresas/compartilhar', ['empresa' => $empresa], true);
        require APP_ROOT . '/app/Views/layouts/main.php';
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $empresa = [
                'cnpj' => $_POST['cnpj'],
                'razao_social' => $_POST['razao_social'],
                'uf' => $_POST['uf'],
                'cidade' => $_POST['cidade'],
                'inscricao_estadual' => $_POST['inscricao_estadual'],
                'telefone' => $_POST['telefone'],
                'email' => $_POST['email'],
                'status' => $_POST['status'],
                'regime' => $_POST['regime'],
                'representante_legal' => $_POST['representante_legal'],
                'ativo' => 1
            ];

            // Verifica se já existe
            if (!$this->empresaModel->findByCnpj($empresa['cnpj'])) {
                $empresaId = $this->empresaModel->create($empresa);
                // Vincula o usuário logado à empresa criada
                $stmt = $this->db->prepare('INSERT INTO usuario_empresas (usuario_id, empresa_id) VALUES (?, ?)');
                $stmt->execute([$_SESSION['usuario_id'], $empresaId]);
                header('Location: /empresas');
                exit;
            }
        }
        require_once __DIR__ . '/../Views/empresas/create.php';
    }

    public function filiais($id) {
        $empresa = $this->empresaModel->find($id);
        if (!$empresa) {
            header('Location: /empresas');
            exit;
        }

        // Busca as filiais vinculadas
        $stmt = $this->db->prepare('
            SELECT e.* FROM empresas e
            WHERE e.empresa_matriz_id = ?
            ORDER BY e.razao_social
        ');
        $stmt->execute([$id]);
        $filiais = $stmt->fetchAll();

        // Busca empresas disponíveis para vincular (que não são filiais de outra matriz)
        $stmt = $this->db->prepare('
            SELECT e.* FROM empresas e
            WHERE e.id != ? 
            AND e.tipo_empresa = "independente"
            AND e.empresa_matriz_id IS NULL
            ORDER BY e.razao_social
        ');
        $stmt->execute([$id]);
        $empresas_disponiveis = $stmt->fetchAll();

        $content = view('empresas/filiais', [
            'empresa' => $empresa,
            'filiais' => $filiais,
            'empresas_disponiveis' => $empresas_disponiveis
        ], true);
        require APP_ROOT . '/app/Views/layouts/main.php';
    }

    public function vincularFilial() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /empresas');
            exit;
        }

        $empresa_matriz_id = $_POST['empresa_matriz_id'] ?? null;
        $empresa_filial_id = $_POST['empresa_filial_id'] ?? null;

        if (!$empresa_matriz_id || !$empresa_filial_id) {
            $_SESSION['erro'] = 'Dados inválidos.';
            header('Location: /empresas');
            exit;
        }

        // Atualiza a empresa para ser filial
        $stmt = $this->db->prepare('
            UPDATE empresas 
            SET tipo_empresa = "filial", 
                empresa_matriz_id = ? 
            WHERE id = ? 
            AND tipo_empresa = "independente"
        ');
        
        if ($stmt->execute([$empresa_matriz_id, $empresa_filial_id])) {
            $_SESSION['sucesso'] = 'Filial vinculada com sucesso.';
        } else {
            $_SESSION['erro'] = 'Erro ao vincular filial.';
        }

        header('Location: /empresas/filiais/' . $empresa_matriz_id);
        exit;
    }

    public function desvincularFilial() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /empresas');
            exit;
        }

        $empresa_matriz_id = $_POST['empresa_matriz_id'] ?? null;
        $empresa_filial_id = $_POST['empresa_filial_id'] ?? null;

        if (!$empresa_matriz_id || !$empresa_filial_id) {
            $_SESSION['erro'] = 'Dados inválidos.';
            header('Location: /empresas');
            exit;
        }

        // Atualiza a empresa para ser independente
        $stmt = $this->db->prepare('
            UPDATE empresas 
            SET tipo_empresa = "independente", 
                empresa_matriz_id = NULL 
            WHERE id = ? 
            AND empresa_matriz_id = ?
        ');
        
        if ($stmt->execute([$empresa_filial_id, $empresa_matriz_id])) {
            $_SESSION['sucesso'] = 'Filial desvinculada com sucesso.';
        } else {
            $_SESSION['erro'] = 'Erro ao desvincular filial.';
        }

        header('Location: /empresas/filiais/' . $empresa_matriz_id);
        exit;
    }

    public function atualizar() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método não permitido']);
            exit;
        }

        $id = $_POST['id'] ?? null;
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'ID da empresa não fornecido']);
            exit;
        }

        // Log dos dados recebidos
        error_log('Dados recebidos: ' . print_r($_POST, true));

        $dados = [
            'razao_social' => $_POST['razao_social'] ?? '',
            'uf' => $_POST['uf'] ?? '',
            'cidade' => $_POST['cidade'] ?? '',
            'inscricao_estadual' => $_POST['inscricao_estadual'] ?? '',
            'telefone' => $_POST['telefone'] ?? '',
            'email' => $_POST['email'] ?? '',
            'status' => $_POST['status'] ?? '',
            'regime' => $_POST['regime'] ?? '',
            'representante_legal' => $_POST['representante_legal'] ?? '',
            'tipo_empresa' => $_POST['tipo_empresa'] ?? 'independente',
            'empresa_matriz_id' => $_POST['tipo_empresa'] === 'filial' ? ($_POST['empresa_matriz_id'] ?? null) : null,
            'ativo' => isset($_POST['ativo']) ? 1 : 0
        ];

        // Log dos dados processados
        error_log('Dados processados: ' . print_r($dados, true));

        // Validações
        if ($dados['tipo_empresa'] === 'filial' && empty($dados['empresa_matriz_id'])) {
            echo json_encode(['success' => false, 'message' => 'É necessário selecionar uma empresa matriz para filiais']);
            exit;
        }

        try {
            // Se for matriz, garante que empresa_matriz_id seja NULL
            if ($dados['tipo_empresa'] === 'matriz') {
                $dados['empresa_matriz_id'] = null;
            }

            // Log da query que será executada
            error_log('Tentando atualizar empresa ID: ' . $id);
            
            if ($this->empresaModel->update($id, $dados)) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erro ao atualizar empresa']);
            }
        } catch (\Exception $e) {
            error_log('Erro ao atualizar empresa: ' . $e->getMessage());
            error_log('Stack trace: ' . $e->getTraceAsString());
            echo json_encode(['success' => false, 'message' => 'Erro ao atualizar empresa: ' . $e->getMessage()]);
        }
        exit;
    }
} 