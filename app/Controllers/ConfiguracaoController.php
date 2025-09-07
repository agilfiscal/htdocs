<?php
namespace App\Controllers;

class ConfiguracaoController {
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
        // Verifica se é admin ou master
        $stmt = $this->db->prepare('SELECT tipo FROM usuarios WHERE id = ?');
        $stmt->execute([$_SESSION['usuario_id']]);
        $tipo = $stmt->fetchColumn();
        
        if ($tipo !== 'admin' && $tipo !== 'master') {
            $_SESSION['erro'] = 'Você não tem permissão para acessar esta página.';
            header('Location: /mde');
            exit;
        }

        // Lista de módulos restritos
        $modulosRestritos = [
            'documentos' => 'Gestão de documentos',
            'consulta-tributaria' => 'Consulta Tributária',
            'relatorios' => 'Relatórios',
            'alertas' => 'Alertas / Notificações',
            'financeiro' => 'Financeiro',
            'configuracoes' => 'Configurações'
        ];

        // Busca todos os usuários tipo 3
        $stmt = $this->db->query('SELECT id, nome, email FROM usuarios WHERE tipo = 3 ORDER BY nome');
        $usuarios = $stmt->fetchAll();

        // Para cada usuário, busca suas permissões
        foreach ($usuarios as &$usuario) {
            $stmt = $this->db->prepare('SELECT modulo, permitido FROM usuario_permissoes WHERE usuario_id = ?');
            $stmt->execute([$usuario['id']]);
            $permissoes = $stmt->fetchAll();
            
            $usuario['permissoes'] = [];
            foreach ($modulosRestritos as $modulo => $nome) {
                $permitido = false;
                foreach ($permissoes as $permissao) {
                    if ($permissao['modulo'] === $modulo) {
                        $permitido = (bool)$permissao['permitido'];
                        break;
                    }
                }
                $usuario['permissoes'][$modulo] = $permitido;
            }
        }

        $content = view('configuracoes/index', [
            'usuarios' => $usuarios,
            'modulosRestritos' => $modulosRestritos
        ], true);
        require APP_ROOT . '/app/Views/layouts/main.php';
    }

    public function salvar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /configuracoes');
            exit;
        }

        // Verifica se é admin ou master
        $stmt = $this->db->prepare('SELECT tipo FROM usuarios WHERE id = ?');
        $stmt->execute([$_SESSION['usuario_id']]);
        $tipo = $stmt->fetchColumn();
        
        if ($tipo !== 'admin' && $tipo !== 'master') {
            $_SESSION['erro'] = 'Você não tem permissão para realizar esta ação.';
            header('Location: /mde');
            exit;
        }

        $usuario_id = $_POST['usuario_id'] ?? null;
        $permissoes = $_POST['permissoes'] ?? [];

        // Só exige usuario_id se o campo estiver presente no POST
        if (isset($_POST['usuario_id']) && !$usuario_id) {
            $_SESSION['erro'] = 'Usuário não especificado.';
            header('Location: /configuracoes');
            exit;
        }

        if (!$usuario_id) {
            // Não é um post de permissões, apenas retorna
            header('Location: /configuracoes');
            exit;
        }

        // Remove todas as permissões existentes do usuário
        $stmt = $this->db->prepare('DELETE FROM usuario_permissoes WHERE usuario_id = ?');
        $stmt->execute([$usuario_id]);

        // Insere as novas permissões
        $stmt = $this->db->prepare('INSERT INTO usuario_permissoes (usuario_id, modulo, permitido) VALUES (?, ?, ?)');
        foreach ($permissoes as $modulo => $permitido) {
            $stmt->execute([$usuario_id, $modulo, $permitido ? 1 : 0]);
        }

        $_SESSION['sucesso'] = 'Permissões atualizadas com sucesso!';
        header('Location: /configuracoes');
        exit;
    }

    // Retorna as empresas disponíveis para o usuário selecionar (JSON)
    public function getEmpresasDisponiveis() {
        header('Content-Type: application/json');
        try {
            $stmt = $this->db->query('SELECT id, razao_social as nome, cnpj, tipo_empresa FROM empresas ORDER BY id');
            $empresas = $stmt->fetchAll();
            echo json_encode($empresas);
        } catch (\PDOException $e) {
            http_response_code(500);
            echo json_encode(['erro' => $e->getMessage()]);
        }
        exit;
    }

    // Salva as empresas atendidas pelo usuário logado
    public function salvarEmpresasAtendidas() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['erro' => 'Método não permitido']);
            exit;
        }
        $usuario_id = $_SESSION['usuario_id'];
        $empresas = $_POST['empresas_atendidas'] ?? [];
        if (!is_array($empresas)) $empresas = [];

        // Limpa as empresas atendidas do usuário
        $stmt = $this->db->prepare('DELETE FROM empresas_atendidas WHERE usuario_id = ?');
        $stmt->execute([$usuario_id]);

        // Insere as novas empresas selecionadas
        $stmt = $this->db->prepare('INSERT INTO empresas_atendidas (usuario_id, empresa_id, data, created_at, updated_at) VALUES (?, ?, CURDATE(), NOW(), NOW())');
        foreach ($empresas as $empresa_id) {
            $stmt->execute([$usuario_id, $empresa_id]);
        }
        // Atualiza a sessão com a primeira empresa selecionada
        if (count($empresas) > 0) {
            $_SESSION['empresa_atendida_id'] = $empresas[0];
            $_SESSION['empresas_atendidas_ids'] = $empresas;
        } else {
            unset($_SESSION['empresa_atendida_id']);
            unset($_SESSION['empresas_atendidas_ids']);
        }
        echo json_encode(['sucesso' => true]);
        exit;
    }

    // Retorna as empresas atendidas pelo usuário logado (JSON)
    public function empresasAtendidasUsuario() {
        header('Content-Type: application/json');
        $usuario_id = $_SESSION['usuario_id'];
        $stmt = $this->db->prepare('SELECT e.id, e.razao_social as nome, e.cnpj, e.tipo_empresa FROM empresas_atendidas ea JOIN empresas e ON ea.empresa_id = e.id WHERE ea.usuario_id = ? AND ea.data = CURDATE()');
        $stmt->execute([$usuario_id]);
        $empresas = $stmt->fetchAll();
        // Se for para seleção, retorna apenas os IDs, senão retorna nome/cnpj
        if (isset($_GET['ids']) && $_GET['ids'] == '1') {
            echo json_encode(array_column($empresas, 'id'));
        } else {
            echo json_encode($empresas);
        }
        exit;
    }

    // Endpoint para log de resolução de nota
    public function logsResolucaoNota() {
        header('Content-Type: application/json');
        $where = [];
        $params = [];
        if (!empty($_GET['usuario'])) {
            $where[] = 'u.nome LIKE ?';
            $params[] = '%' . $_GET['usuario'] . '%';
        }
        if (!empty($_GET['empresa'])) {
            $where[] = 'e.razao_social LIKE ?';
            $params[] = '%' . $_GET['empresa'] . '%';
        }
        if (!empty($_GET['data_inicial'])) {
            $where[] = 'DATE(r.data_resolvida) >= ?';
            $params[] = $_GET['data_inicial'];
        }
        if (!empty($_GET['data_final'])) {
            $where[] = 'DATE(r.data_resolvida) <= ?';
            $params[] = $_GET['data_final'];
        }
        $sql = 'SELECT r.data_resolvida, u.nome as usuario_nome, n.numero, e.razao_social as empresa_nome, e.tipo_empresa
                FROM notificacoes_resolvidas r
                LEFT JOIN usuarios u ON u.id = r.usuario_id
                LEFT JOIN auditoria_notas n ON n.id = r.nota_id
                LEFT JOIN empresas e ON e.id = n.empresa_id';
        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        $sql .= ' ORDER BY r.data_resolvida DESC LIMIT 100';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $logs = $stmt->fetchAll();
        error_log('LOG DEBUG RESOLUCAO: ' . json_encode($logs));
        echo json_encode($logs);
    }

    // Endpoint para log de envio de notas via upload
    public function logsEnvioNotas() {
        header('Content-Type: application/json');
        $where = [];
        $params = [];
        // Filtros obrigatórios
        $empresa = $_GET['empresa'] ?? '';
        $data_inicial = $_GET['data_inicial'] ?? '';
        $data_final = $_GET['data_final'] ?? '';
        if (!$empresa || !$data_inicial || !$data_final) {
            echo json_encode(['erro' => 'Preencha empresa e datas para consultar.']);
            exit;
        }
        $where[] = 'e.razao_social LIKE ?';
        $params[] = '%' . $empresa . '%';
        $where[] = 'DATE(a.data_consulta) >= ?';
        $params[] = $data_inicial;
        $where[] = 'DATE(a.data_consulta) <= ?';
        $params[] = $data_final;
        // Paginação
        $pagina = max(1, intval($_GET['pagina'] ?? 1));
        $limite = isset($_GET['limite']) ? intval($_GET['limite']) : 20;
        if ($limite < 1) $limite = 20;
        $offset = ($pagina - 1) * $limite;
        $sql = 'SELECT a.id, a.numero, a.cnpj, a.razao_social, a.data_emissao, a.valor, a.chave, a.uf, a.status, a.tipo, a.data_consulta, e.razao_social as empresa_nome
                FROM auditoria_notas a
                LEFT JOIN empresas e ON e.id = a.empresa_id';
        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        $sql .= ' ORDER BY a.data_consulta DESC LIMIT ' . $limite . ' OFFSET ' . $offset;
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $logs = $stmt->fetchAll();
        // Total de registros para paginação
        $sqlCount = 'SELECT COUNT(*) FROM auditoria_notas a LEFT JOIN empresas e ON e.id = a.empresa_id';
        if ($where) {
            $sqlCount .= ' WHERE ' . implode(' AND ', $where);
        }
        $stmtCount = $this->db->prepare($sqlCount);
        $stmtCount->execute($params);
        $total = $stmtCount->fetchColumn();
        echo json_encode(['dados' => $logs, 'total' => intval($total), 'pagina' => $pagina, 'limite' => $limite]);
    }

    // Retorna todos os usuários para filtro (JSON)
    public function getUsuariosDisponiveis() {
        header('Content-Type: application/json');
        try {
            $stmt = $this->db->query('SELECT id, nome FROM usuarios ORDER BY nome');
            $usuarios = $stmt->fetchAll();
            echo json_encode($usuarios);
        } catch (\PDOException $e) {
            http_response_code(500);
            echo json_encode(['erro' => $e->getMessage()]);
        }
        exit;
    }

    // Endpoint para listar arquivos enviados (log de uploads)
    public function listarArquivosEnviados() {
        header('Content-Type: application/json');
        $where = [];
        $params = [];
        if (!empty($_GET['usuario'])) {
            $where[] = 'u.nome LIKE ?';
            $params[] = '%' . $_GET['usuario'] . '%';
        }
        if (!empty($_GET['empresa'])) {
            $where[] = 'e.razao_social LIKE ?';
            $params[] = '%' . $_GET['empresa'] . '%';
        }
        if (!empty($_GET['tipo'])) {
            $where[] = 'l.tipo_arquivo = ?';
            $params[] = $_GET['tipo'];
        }
        if (!empty($_GET['status'])) {
            $where[] = 'l.status = ?';
            $params[] = $_GET['status'];
        }
        if (!empty($_GET['data_inicial'])) {
            $where[] = 'DATE(l.data_envio) >= ?';
            $params[] = $_GET['data_inicial'];
        }
        if (!empty($_GET['data_final'])) {
            $where[] = 'DATE(l.data_envio) <= ?';
            $params[] = $_GET['data_final'];
        }
        $sql = 'SELECT l.*, u.nome as usuario_nome, e.razao_social as empresa_nome, e.tipo_empresa
                FROM log_envio_arquivos l
                LEFT JOIN usuarios u ON u.id = l.usuario_id
                LEFT JOIN empresas e ON e.id = l.empresa_id';
        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        $sql .= ' ORDER BY l.data_envio DESC LIMIT 100';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $logs = $stmt->fetchAll();
        echo json_encode($logs);
    }

    // Endpoint para desfazer envio de arquivo
    public function desfazerEnvioArquivo() {
        try {
            if (session_status() === PHP_SESSION_NONE) session_start();
            file_put_contents(__DIR__.'/../../public/debug_desfazer.txt', print_r([
                'session' => $_SESSION,
                'cookies' => $_COOKIE,
                'post' => $_POST,
            ], true));
            header('Content-Type: application/json');
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                file_put_contents(__DIR__.'/../../public/debug_desfazer.txt', "LOG: método não permitido\n", FILE_APPEND);
                http_response_code(405);
                echo json_encode(['erro' => 'Método não permitido']);
                exit;
            }
            $id = $_POST['id'] ?? null;
            $motivo = $_POST['motivo'] ?? '';
            $usuario_id = $_SESSION['usuario_id'] ?? null;
            if (!$id || !$usuario_id) {
                file_put_contents(__DIR__.'/../../public/debug_desfazer.txt', "LOG: dados insuficientes\n", FILE_APPEND);
                http_response_code(400);
                echo json_encode(['erro' => 'Dados insuficientes']);
                exit;
            }
            // Buscar log
            $stmt = $this->db->prepare('SELECT * FROM log_envio_arquivos WHERE id = ?');
            $stmt->execute([$id]);
            $log = $stmt->fetch();
            file_put_contents(__DIR__.'/../../public/debug_desfazer.txt', "LOG: log buscado\n", FILE_APPEND);
            if (!$log || mb_strtolower(trim($log['status'])) !== 'ativo') {
                file_put_contents(__DIR__.'/../../public/debug_desfazer.txt', "LOG: arquivo não encontrado ou já excluído\n", FILE_APPEND);
                http_response_code(404);
                echo json_encode(['erro' => 'Arquivo não encontrado ou já excluído']);
                exit;
            }
            $empresa_id = $log['empresa_id'];
            $data_envio = $log['data_envio'];
            $tipo_arquivo = $log['tipo_arquivo'];
            $status = isset($log['status']) ? trim($log['status']) : '';
            file_put_contents(__DIR__.'/../../public/debug_desfazer.txt', "LOG: status = '{$log['status']}', tipo_arquivo = '{$log['tipo_arquivo']}'\n", FILE_APPEND);
            $tipo_arquivo_lower = mb_strtolower(trim($tipo_arquivo));
            file_put_contents(__DIR__.'/../../public/debug_desfazer.txt', "LOG: tipo_arquivo_lower = '{$tipo_arquivo_lower}'\n", FILE_APPEND);
            // Subtrai 2 minutos da data_envio
            $data_limite = date('Y-m-d H:i:s', strtotime($data_envio . ' -2 minutes'));
            file_put_contents(__DIR__.'/../../public/debug_desfazer.txt', "LOG: data_limite = $data_limite\n", FILE_APPEND);
            // Apagar dados inseridos pelo arquivo, conforme tipo
            if ($status && mb_strtolower($status) === 'ativo') {
                if ($tipo_arquivo_lower === 'notas' || $tipo_arquivo_lower === 'lançamentos') {
                    $stmt = $this->db->prepare('DELETE FROM notas WHERE empresa_id = ? AND created_at >= ?');
                    $stmt->execute([$empresa_id, $data_limite]);
                    file_put_contents(__DIR__.'/../../public/debug_desfazer.txt', "LOG: DELETE notas: " . $stmt->rowCount() . "\n", FILE_APPEND);
                }
                if ($tipo_arquivo_lower === 'fornecedores') {
                    $stmt = $this->db->prepare('DELETE FROM fornecedores WHERE empresa_id = ? AND created_at >= ?');
                    $stmt->execute([$empresa_id, $data_limite]);
                    file_put_contents(__DIR__.'/../../public/debug_desfazer.txt', "LOG: DELETE fornecedores: " . $stmt->rowCount() . "\n", FILE_APPEND);
                }
                if ($tipo_arquivo_lower === 'romaneio') {
                    $stmt = $this->db->prepare('DELETE FROM romaneio WHERE empresa_id = ? AND created_at >= ?');
                    $stmt->execute([$empresa_id, $data_limite]);
                    file_put_contents(__DIR__.'/../../public/debug_desfazer.txt', "LOG: DELETE romaneio: " . $stmt->rowCount() . "\n", FILE_APPEND);
                }
                if ($tipo_arquivo_lower === 'desconhecimento') {
                    $stmt = $this->db->prepare('DELETE FROM desconhecimento WHERE empresa_id = ? AND created_at >= ?');
                    $stmt->execute([$empresa_id, $data_limite]);
                    file_put_contents(__DIR__.'/../../public/debug_desfazer.txt', "LOG: DELETE desconhecimento: " . $stmt->rowCount() . "\n", FILE_APPEND);
                }
                if ($tipo_arquivo_lower === 'sefaz') {
                    try {
                        file_put_contents(__DIR__.'/../../public/debug_desfazer.txt', "LOG: antes DELETE auditoria_notas\n", FILE_APPEND);
                        // Deletar filhos direto via JOIN
                        $del = $this->db->prepare(
                            'DELETE nr FROM notificacoes_resolvidas nr
                             JOIN auditoria_notas an ON nr.nota_id = an.id
                             WHERE an.empresa_id = ? AND an.data_consulta >= ?'
                        );
                        $del->execute([$empresa_id, $data_limite]);
                        file_put_contents(__DIR__.'/../../public/debug_desfazer.txt', "LOG: DELETE notificacoes_resolvidas: " . $del->rowCount() . "\n", FILE_APPEND);
                        // Agora pode deletar de auditoria_notas normalmente
                        $stmt = $this->db->prepare('DELETE FROM auditoria_notas WHERE empresa_id = ? AND data_consulta >= ?');
                        $stmt->execute([$empresa_id, $data_limite]);
                        file_put_contents(__DIR__.'/../../public/debug_desfazer.txt', "LOG: DELETE auditoria_notas: " . $stmt->rowCount() . "\n", FILE_APPEND);
                    } catch (Exception $e) {
                        file_put_contents(__DIR__.'/../../public/debug_desfazer.txt', "LOG: ERRO auditoria_notas: " . $e->getMessage() . "\n", FILE_APPEND);
                    }
                }
            }
            // Remover arquivo físico
            $caminho = __DIR__ . '/../../public/uploads/' . $log['nome_arquivo'];
            if (file_exists($caminho)) {
                unlink($caminho);
                file_put_contents(__DIR__.'/../../public/debug_desfazer.txt', "LOG: arquivo removido\n", FILE_APPEND);
            } else {
                file_put_contents(__DIR__.'/../../public/debug_desfazer.txt', "LOG: arquivo não encontrado para remover\n", FILE_APPEND);
            }
            // Atualizar log
            $stmt = $this->db->prepare('UPDATE log_envio_arquivos SET status = ?, data_exclusao = NOW(), usuario_exclusao_id = ?, motivo_exclusao = ? WHERE id = ?');
            $stmt->execute(['excluido', $usuario_id, $motivo, $id]);
            file_put_contents(__DIR__.'/../../public/debug_desfazer.txt', "LOG: log atualizado\n", FILE_APPEND);
            echo json_encode(['sucesso' => true]);
            exit;
        } catch (Exception $e) {
            file_put_contents(__DIR__.'/../../public/debug_desfazer.txt', "LOG: ERRO GERAL: " . $e->getMessage() . "\n", FILE_APPEND);
            http_response_code(500);
            echo json_encode(['erro' => 'Erro interno: ' . $e->getMessage()]);
            exit;
        }
    }

    // Relatório de Notas em Romaneio
    public function logsRomaneio() {
        header('Content-Type: application/json');
        $where = [];
        $params = [];
        // Filtros obrigatórios
        $empresa = $_GET['empresa'] ?? '';
        $data_inicial = $_GET['data_inicial'] ?? '';
        $data_final = $_GET['data_final'] ?? '';
        if (!$empresa || !$data_inicial || !$data_final) {
            echo json_encode(['erro' => 'Preencha empresa e datas para consultar.']);
            exit;
        }
        $where[] = 'e.razao_social LIKE ?';
        $params[] = '%' . $empresa . '%';
        $where[] = 'DATE(r.created_at) >= ?';
        $params[] = $data_inicial;
        $where[] = 'DATE(r.created_at) <= ?';
        $params[] = $data_final;
        // Paginação
        $pagina = max(1, intval($_GET['pagina'] ?? 1));
        $limite = isset($_GET['limite']) ? intval($_GET['limite']) : 20;
        if ($limite < 1) $limite = 20;
        $offset = ($pagina - 1) * $limite;
        $sql = 'SELECT r.id, r.chave, r.created_at, r.updated_at, e.razao_social as empresa_nome, e.tipo_empresa
                FROM romaneio r
                LEFT JOIN empresas e ON e.id = r.empresa_id';
        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        $sql .= ' ORDER BY r.created_at DESC LIMIT ' . $limite . ' OFFSET ' . $offset;
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $logs = $stmt->fetchAll();
        // Total de registros para paginação
        $sqlCount = 'SELECT COUNT(*) FROM romaneio r LEFT JOIN empresas e ON e.id = r.empresa_id';
        if ($where) {
            $sqlCount .= ' WHERE ' . implode(' AND ', $where);
        }
        $stmtCount = $this->db->prepare($sqlCount);
        $stmtCount->execute($params);
        $total = $stmtCount->fetchColumn();
        echo json_encode(['dados' => $logs, 'total' => intval($total), 'pagina' => $pagina, 'limite' => $limite]);
    }

    // Limpeza em lote de dados de uma empresa
    public function limparDadosEmpresa() {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['erro' => 'Método não permitido']);
            exit;
        }
        if (session_status() === PHP_SESSION_NONE) session_start();
        $usuario_id = $_SESSION['usuario_id'] ?? null;
        if (!$usuario_id) {
            http_response_code(401);
            echo json_encode(['erro' => 'Usuário não autenticado']);
            exit;
        }
        // Verifica se é admin ou master
        $stmt = $this->db->prepare('SELECT tipo FROM usuarios WHERE id = ?');
        $stmt->execute([$usuario_id]);
        $tipo = $stmt->fetchColumn();
        if ($tipo !== 'admin' && $tipo !== 'master') {
            http_response_code(403);
            echo json_encode(['erro' => 'Você não tem permissão para realizar esta ação.']);
            exit;
        }
        $empresa_id = $_POST['empresa_id'] ?? null;
        if (!$empresa_id) {
            http_response_code(400);
            echo json_encode(['erro' => 'Empresa não informada']);
            exit;
        }
        try {
            $tabelas = ['auditoria_notas', 'notas', 'desconhecimento', 'romaneio', 'empresa_fornecedor'];
            foreach ($tabelas as $tabela) {
                $sql = "DELETE FROM $tabela WHERE empresa_id = ?";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([$empresa_id]);
            }
            echo json_encode(['sucesso' => true]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['erro' => 'Erro ao limpar dados: ' . $e->getMessage()]);
        }
        exit;
    }

    // Importação da planilha base de produtos
    public function importarProdutosBase() {
        header('Content-Type: application/json');
        set_time_limit(300);
        $debugFile = __DIR__ . '/../../public/debug_importa_produtos.txt';
        file_put_contents($debugFile, "INICIANDO IMPORTAÇÃO DE PRODUTOS BASE\n");
        set_error_handler(function($errno, $errstr, $errfile, $errline) use ($debugFile) {
            file_put_contents($debugFile, "ERRO PHP: [$errno] $errstr em $errfile:$errline\n", FILE_APPEND);
            return false;
        });
        register_shutdown_function(function() use ($debugFile) {
            $error = error_get_last();
            if ($error) {
                file_put_contents($debugFile, "SHUTDOWN ERROR: " . print_r($error, true) . "\n", FILE_APPEND);
            }
        });
        try {
            if (session_status() === PHP_SESSION_NONE) session_start();
            if (!isset($_SESSION['usuario_id'])) {
                throw new \Exception('Usuário não autenticado.');
            }
            if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['planilha_base'])) {
                throw new \Exception('Arquivo não enviado.');
            }
            $arquivo = $_FILES['planilha_base']['tmp_name'];
            if (!file_exists($arquivo)) {
                throw new \Exception('Arquivo não encontrado.');
            }
            file_put_contents($debugFile, "APP_ROOT: " . (defined('APP_ROOT') ? APP_ROOT : 'NÃO DEFINIDO') . "\n", FILE_APPEND);
            file_put_contents($debugFile, "autoload existe? " . (defined('APP_ROOT') && file_exists(APP_ROOT . '/vendor/autoload.php') ? 'SIM' : 'NÃO') . "\n", FILE_APPEND);
            file_put_contents($debugFile, "Antes do require_once\n", FILE_APPEND);
            require_once APP_ROOT . '/vendor/autoload.php';
            file_put_contents($debugFile, "Depois do require_once\n", FILE_APPEND);
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($arquivo);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray(null, true, true, false);
            file_put_contents($debugFile, "Total de linhas lidas: " . count($rows) . "\n", FILE_APPEND);
            if (count($rows) < 2) {
                throw new \Exception('Planilha sem dados.');
            }
            $header = array_map('trim', $rows[0]);
            file_put_contents($debugFile, "Cabeçalho detectado: " . print_r($header, true) . "\n", FILE_APPEND);
            $colIndex = array_flip($header);
            $inseridos = 0;
            for ($i = 1; $i < count($rows); $i++) {
                $row = $rows[$i];
                file_put_contents($debugFile, "Linha $i: " . print_r($row, true) . "\n", FILE_APPEND);
                $ean = $row[$colIndex['EAN']] ?? null;
                $descritivo = $row[$colIndex['DESCRITIVO']] ?? null;
                if (!$descritivo) {
                    file_put_contents($debugFile, "Linha $i pulada: descritivo vazio\n", FILE_APPEND);
                    continue;
                }
                $query = 'SELECT id FROM produtos WHERE ';
                $params = [];
                if ($ean) {
                    $query .= 'ean = ?';
                    $params[] = $ean;
                } else {
                    $query .= 'descritivo = ?';
                    $params[] = $descritivo;
                }
                $stmt = $this->db->prepare($query);
                $stmt->execute($params);
                if ($stmt->fetch()) {
                    file_put_contents($debugFile, "Linha $i pulada: produto já existe (EAN ou Descritivo)\n", FILE_APPEND);
                    continue;
                }
                $sql = 'INSERT INTO produtos (ean, descritivo, unidade_venda, politica, custo, pmz, venda, depto, secao, grupo, subgrupo, classificacao_fiscal, piscopins, monofasico, cest, tributacao_venda, icms_venda, st_venda, reducao_venda, iva, fcp, ipi, categoria_fiscal, revisado) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)';
                $stmt = $this->db->prepare($sql);
                $stmt->execute([
                    $ean,
                    $descritivo,
                    $row[$colIndex['UNIDADE_VENDA']] ?? null,
                    $row[$colIndex['POLITICA']] ?? null,
                    $row[$colIndex['CUSTO']] ?? null,
                    $row[$colIndex['PMZ']] ?? null,
                    $row[$colIndex['VENDA']] ?? null,
                    $row[$colIndex['DEPTO']] ?? null,
                    $row[$colIndex['SECAO']] ?? null,
                    $row[$colIndex['GRUPO']] ?? null,
                    $row[$colIndex['SUBGRUPO']] ?? null,
                    $row[$colIndex['CLASSIFICACAO_FISCAL']] ?? null,
                    $row[$colIndex['PISCOFINS']] ?? null,
                    $row[$colIndex['MONOFASICO']] ?? null,
                    $row[$colIndex['CEST']] ?? null,
                    $row[$colIndex['TRIBUTACAO_VENDA']] ?? null,
                    $row[$colIndex['ICMS_VENDA']] ?? null,
                    $row[$colIndex['ST_VENDA']] ?? null,
                    $row[$colIndex['REDUCAO_VENDA']] ?? null,
                    $row[$colIndex['IVA']] ?? null,
                    $row[$colIndex['FCP']] ?? null,
                    $row[$colIndex['IPI']] ?? null,
                    $row[$colIndex['CATEGORIA_FISCAL']] ?? null,
                    'nao'
                ]);
                $inseridos++;
                file_put_contents($debugFile, "Linha $i: produto inserido com sucesso\n", FILE_APPEND);
            }
            file_put_contents($debugFile, "Total inseridos: $inseridos\n", FILE_APPEND);
            if ($inseridos > 0) {
                echo json_encode(['status' => 'sucesso', 'mensagem' => "$inseridos produtos importados com sucesso!"]);
            } else {
                echo json_encode(['status' => 'aviso', 'mensagem' => 'Nenhum produto novo foi importado.']);
            }
        } catch (\Exception $e) {
            file_put_contents($debugFile, "ERRO: " . $e->getMessage() . "\n", FILE_APPEND);
            echo json_encode(['status' => 'erro', 'mensagem' => 'Erro: ' . $e->getMessage()]);
        }
        exit;
    }

    // Importação da planilha revisada de produtos
    public function importarProdutosRevisada() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['usuario_id'])) {
            $_SESSION['erro'] = 'Usuário não autenticado.';
            header('Location: /login');
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['planilha_revisada'])) {
            $_SESSION['erro'] = 'Arquivo não enviado.';
            header('Location: /configuracoes');
            exit;
        }
        $arquivo = $_FILES['planilha_revisada']['tmp_name'];
        if (!file_exists($arquivo)) {
            $_SESSION['erro'] = 'Arquivo não encontrado.';
            header('Location: /configuracoes');
            exit;
        }
        require_once APP_ROOT . '/vendor/autoload.php';
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($arquivo);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);
        $header = array_map('strtolower', array_map('trim', $rows[1]));

        $camposComparar = [
            'descricao', 'ncm', 'cest_revisao', 'aliquota_ipi', 'cst_ipi', 'cst_pis_cofins_entrada', 'cst_pis_cofins_saida',
            'codigo_sped', 'aliquota_pis', 'aliquota_cofins', 'base_legal_pis_cofins', 'cfop', 'cst_csosn',
            'ad_rem_icms', 'aliquota_icms', 'aliquota_red_base_calculo_icms', 'situacao_tributaria',
            'red_base_calculo_icms', 'red_base_calculo_icms_st', 'aliquota_icms_st', 'iva_mva', 'fcp_revisao',
            'cod_beneficio_fiscal', 'antecipado', 'desoneracao', 'percentual_diferimento', 'percentual_isencao',
            'codigo_anp', 'codigo_beneficio', 'base_legal_icms'
        ];
        $mapaColunas = [
            'descricao' => 'B',
            'ncm' => 'C',
            'cest_revisao' => 'D',
            'aliquota_ipi' => 'E',
            'cst_ipi' => 'F',
            'cst_pis_cofins_entrada' => 'G',
            'cst_pis_cofins_saida' => 'H',
            'codigo_sped' => 'I',
            'aliquota_pis' => 'J',
            'aliquota_cofins' => 'K',
            'base_legal_pis_cofins' => 'L',
            'cfop' => 'M',
            'cst_csosn' => 'N',
            'ad_rem_icms' => 'O',
            'aliquota_icms' => 'P',
            'aliquota_red_base_calculo_icms' => 'Q',
            'situacao_tributaria' => 'R',
            'red_base_calculo_icms' => 'S',
            'red_base_calculo_icms_st' => 'T',
            'aliquota_icms_st' => 'U',
            'iva_mva' => 'V',
            'fcp_revisao' => 'W',
            'cod_beneficio_fiscal' => 'X',
            'antecipado' => 'Y',
            'desoneracao' => 'Z',
            'percentual_diferimento' => 'AA',
            'percentual_isencao' => 'AB',
            'codigo_anp' => 'AC',
            'codigo_beneficio' => 'AD',
            'base_legal_icms' => 'AE'
        ];

        $diferencas = [];
        $novosValores = [];
        $atualizados = 0;
        for ($i = 2; $i <= count($rows); $i++) {
            $row = $rows[$i];
            $ean = $row['A'] ?? null;
            $descricao = $row['B'] ?? null;
            if (!$ean && !$descricao) continue;
            $query = 'SELECT * FROM produtos WHERE ';
            $params = [];
            if ($ean) {
                $query .= 'ean = ?';
                $params[] = $ean;
            } else {
                $query .= 'descritivo = ?';
                $params[] = $descricao;
            }
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            $produto = $stmt->fetch(\PDO::FETCH_ASSOC);
            if (!$produto) continue;

            if ($produto['revisado'] === 'nao' || $produto['revisado'] === null || $produto['revisado'] === '') {
                $sql = 'UPDATE produtos SET descricao=?, ncm=?, cest_revisao=?, aliquota_ipi=?, cst_ipi=?, cst_pis_cofins_entrada=?, cst_pis_cofins_saida=?, codigo_sped=?, aliquota_pis=?, aliquota_cofins=?, base_legal_pis_cofins=?, cfop=?, cst_csosn=?, ad_rem_icms=?, aliquota_icms=?, aliquota_red_base_calculo_icms=?, situacao_tributaria=?, red_base_calculo_icms=?, red_base_calculo_icms_st=?, aliquota_icms_st=?, iva_mva=?, fcp_revisao=?, cod_beneficio_fiscal=?, antecipado=?, desoneracao=?, percentual_diferimento=?, percentual_isencao=?, codigo_anp=?, codigo_beneficio=?, base_legal_icms=?, revisado="sim" WHERE id=?';
                $stmt = $this->db->prepare($sql);
                $stmt->execute([
                    $row[$mapaColunas['descricao']] ?? null,
                    $row[$mapaColunas['ncm']] ?? null,
                    $row[$mapaColunas['cest_revisao']] ?? null,
                    $row[$mapaColunas['aliquota_ipi']] ?? null,
                    $row[$mapaColunas['cst_ipi']] ?? null,
                    $row[$mapaColunas['cst_pis_cofins_entrada']] ?? null,
                    $row[$mapaColunas['cst_pis_cofins_saida']] ?? null,
                    $row[$mapaColunas['codigo_sped']] ?? null,
                    $row[$mapaColunas['aliquota_pis']] ?? null,
                    $row[$mapaColunas['aliquota_cofins']] ?? null,
                    $row[$mapaColunas['base_legal_pis_cofins']] ?? null,
                    $row[$mapaColunas['cfop']] ?? null,
                    $row[$mapaColunas['cst_csosn']] ?? null,
                    $row[$mapaColunas['ad_rem_icms']] ?? null,
                    $row[$mapaColunas['aliquota_icms']] ?? null,
                    $row[$mapaColunas['aliquota_red_base_calculo_icms']] ?? null,
                    $row[$mapaColunas['situacao_tributaria']] ?? null,
                    $row[$mapaColunas['red_base_calculo_icms']] ?? null,
                    $row[$mapaColunas['red_base_calculo_icms_st']] ?? null,
                    $row[$mapaColunas['aliquota_icms_st']] ?? null,
                    $row[$mapaColunas['iva_mva']] ?? null,
                    $row[$mapaColunas['fcp_revisao']] ?? null,
                    $row[$mapaColunas['cod_beneficio_fiscal']] ?? null,
                    $row[$mapaColunas['antecipado']] ?? null,
                    $row[$mapaColunas['desoneracao']] ?? null,
                    $row[$mapaColunas['percentual_diferimento']] ?? null,
                    $row[$mapaColunas['percentual_isencao']] ?? null,
                    $row[$mapaColunas['codigo_anp']] ?? null,
                    $row[$mapaColunas['codigo_beneficio']] ?? null,
                    $row[$mapaColunas['base_legal_icms']] ?? null,
                    $produto['id']
                ]);
                $atualizados++;
            } else if ($produto['revisado'] === 'sim') {
                $diff = [];
                foreach ($camposComparar as $campo) {
                    $valor_antigo = $produto[$campo] ?? '';
                    $valor_novo = $row[$mapaColunas[$campo]] ?? '';
                    if (trim((string)$valor_antigo) !== trim((string)$valor_novo)) {
                        $diff[$campo] = [
                            'antigo' => $valor_antigo,
                            'novo' => $valor_novo
                        ];
                    }
                }
                if (count($diff) > 0) {
                    $diferencas[] = [
                        'ean' => $ean,
                        'descricao' => $descricao,
                        'diferencas' => $diff
                    ];
                    // Salvar os novos valores completos para atualização posterior
                    $novosValores[$ean . '|' . $descricao] = [];
                    foreach ($camposComparar as $campo) {
                        $novosValores[$ean . '|' . $descricao][$campo] = $row[$mapaColunas[$campo]] ?? null;
                    }
                }
            }
        }
        $_SESSION['produtos_revisados_diferencas'] = $diferencas;
        $_SESSION['produtos_revisados_novos_valores'] = $novosValores;
        $_SESSION['sucesso'] = "$atualizados produtos revisados atualizados com sucesso!";
        header('Location: /configuracoes#produtos');
        exit;
    }

    // Teste de rota GET para debug
    public function importarProdutosBaseGet() {
        file_put_contents(__DIR__ . '/../../public/debug_teste.txt', "Teste de escrita via GET\n");
        echo 'Método GET chamado com sucesso!';
        exit;
    }

    public function atualizarProdutosRevisados() {
        file_put_contents('public/debug_atualiza_produto.txt', "INICIO DO MÉTODO\n", FILE_APPEND);
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['usuario_id'])) {
            $_SESSION['erro'] = 'Usuário não autenticado.';
            header('Location: /login');
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['produtos'])) {
            $_SESSION['erro'] = 'Nenhum produto selecionado.';
            header('Location: /configuracoes#produtos');
            exit;
        }
        $produtosSelecionados = $_POST['produtos'];
        $novosValores = $_SESSION['produtos_revisados_novos_valores'] ?? [];
        $camposComparar = [
            'descricao', 'ncm', 'cest_revisao', 'aliquota_ipi', 'cst_ipi', 'cst_pis_cofins_entrada', 'cst_pis_cofins_saida',
            'codigo_sped', 'aliquota_pis', 'aliquota_cofins', 'base_legal_pis_cofins', 'cfop', 'cst_csosn',
            'ad_rem_icms', 'aliquota_icms', 'aliquota_red_base_calculo_icms', 'situacao_tributaria',
            'red_base_calculo_icms', 'red_base_calculo_icms_st', 'aliquota_icms_st', 'iva_mva', 'fcp_revisao',
            'cod_beneficio_fiscal', 'antecipado', 'desoneracao', 'percentual_diferimento', 'percentual_isencao',
            'codigo_anp', 'codigo_beneficio', 'base_legal_icms'
        ];
        $atualizados = 0;
        foreach ($produtosSelecionados as $produtoKey) {
            // Agora o value é: EAN|descricao_antiga_base64|descricao_nova_base64
            $partes = explode('|', $produtoKey);
            $ean = $partes[0] ?? '';
            $descricao_antiga = isset($partes[1]) ? base64_decode($partes[1]) : '';
            $descricao_nova = isset($partes[2]) ? base64_decode($partes[2]) : '';

            if ($ean) {
                $query = 'SELECT * FROM produtos WHERE ean = ?';
                $stmt = $this->db->prepare($query);
                $stmt->execute([$ean]);
                $produto = $stmt->fetch(\PDO::FETCH_ASSOC);
            } else {
                $query = 'SELECT * FROM produtos WHERE descricao = ?';
                $stmt = $this->db->prepare($query);
                $stmt->execute([$descricao_antiga]);
                $produto = $stmt->fetch(\PDO::FETCH_ASSOC);
                // Só atualiza se o descritivo do banco for igual ao descritivo novo da planilha
                if (!$produto || trim($produto['descricao']) !== trim($descricao_nova)) {
                    continue;
                }
            }
            if (!$produto) continue;
            $valoresNovos = $novosValores[$ean . '|' . $descricao_antiga] ?? $novosValores['|' . $descricao_antiga] ?? null;
            if (!$valoresNovos) continue;
            $set = [];
            $params = [];
            foreach ($camposComparar as $campo) {
                if ($campo === 'descricao') {
                    $set[] = 'descricao = ?';
                    $params[] = $descricao_nova ?: ($valoresNovos['descricao'] ?? null);
                } else if ($campo !== 'descritivo') {
                    $set[] = "$campo = ?";
                    $params[] = $valoresNovos[$campo] ?? null;
                }
            }
            $set[] = 'revisado = "sim"';
            $set[] = 'updated_at = NOW()';
            $sql = 'UPDATE produtos SET ' . implode(', ', $set) . ' WHERE id = ?';
            $params[] = $produto['id'];
            file_put_contents('public/debug_atualiza_produto.txt', print_r([
                'ean' => $ean,
                'descricao_antiga' => $descricao_antiga,
                'descricao_nova' => $descricao_nova,
                'valoresNovos' => $valoresNovos,
                'params' => $params,
                'sql' => $sql
            ], true), FILE_APPEND);
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $atualizados++;
        }
        unset($_SESSION['produtos_revisados_novos_valores']);
        $_SESSION['sucesso'] = "$atualizados produtos revisados atualizados com sucesso!";
        header('Location: /configuracoes#produtos');
        exit;
    }

    public function consultaTributaria() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $resultados = [];
        $ean = '';
        $descritivo = '';
        $regimeFiscalId = $_SESSION['regime_fiscal_id'] ?? '';
        $stmt = $this->db->query('SELECT id, nome FROM regime_fiscal ORDER BY nome');
        $regimesFiscais = $stmt->fetchAll();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ean = trim($_POST['ean'] ?? '');
            $descritivo = trim($_POST['descritivo'] ?? '');
            $regimeFiscalId = $_POST['regime_fiscal_id'] ?? $regimeFiscalId;
            $_SESSION['regime_fiscal_id'] = $regimeFiscalId;
            $where = ["revisado = 'sim'"];
            $params = [];
            $temFiltro = false;
            if ($ean !== '') {
                $where[] = 'ean = ?';
                $params[] = $ean;
                $temFiltro = true;
            }
            if ($descritivo !== '') {
                $where[] = 'descritivo LIKE ?';
                $params[] = '%' . $descritivo . '%';
                $temFiltro = true;
            }
            if ($regimeFiscalId !== '') {
                $where[] = 'regime_fiscal_id = ?';
                $params[] = $regimeFiscalId;
                $temFiltro = true;
            }
            if ($temFiltro) {
                $sql = 'SELECT * FROM produtos WHERE ' . implode(' AND ', $where) . ' ORDER BY id DESC LIMIT 100';
                $stmt = $this->db->prepare($sql);
                $stmt->execute($params);
                $resultados = $stmt->fetchAll();
                // Buscar descrições dos CST PIS/COFINS, CST ICMS, situação tributária e mercadológico
                if ($resultados) {
                    $cstsPisCofins = [];
                    $cstsIcms = [];
                    $cstsSituacao = [];
                    $mercadologicoKeys = [];
                    foreach ($resultados as $prod) {
                        if (!empty($prod['cst_pis_cofins_entrada'])) $cstsPisCofins[$prod['cst_pis_cofins_entrada']] = true;
                        if (!empty($prod['cst_pis_cofins_saida'])) $cstsPisCofins[$prod['cst_pis_cofins_saida']] = true;
                        if (!empty($prod['cst_csosn'])) $cstsIcms[$prod['cst_csosn']] = true;
                        if (!empty($prod['cst_csosn'])) $cstsSituacao[$prod['cst_csosn']] = true;
                        $mercadologicoKeys[] = [
                            (int)($prod['depto'] ?? 0),
                            (int)($prod['secao'] ?? 0),
                            (int)($prod['grupo'] ?? 0),
                            (int)($prod['subgrupo'] ?? 0)
                        ];
                    }
                    // CST PIS/COFINS
                    $mapaCstPisCofins = [];
                    if ($cstsPisCofins) {
                        $in = implode(',', array_fill(0, count($cstsPisCofins), '?'));
                        $sqlCst = 'SELECT cst, descricao FROM CST_pis_cofins WHERE cst IN (' . $in . ')';
                        $stmtCst = $this->db->prepare($sqlCst);
                        $stmtCst->execute(array_keys($cstsPisCofins));
                        foreach ($stmtCst->fetchAll() as $row) {
                            $mapaCstPisCofins[$row['cst']] = $row['descricao'];
                        }
                    }
                    // CST ICMS
                    $mapaCstIcms = [];
                    if ($cstsIcms) {
                        $in = implode(',', array_fill(0, count($cstsIcms), '?'));
                        $sqlCstIcms = 'SELECT cst, descricao FROM cst_icms WHERE cst IN (' . $in . ')';
                        $stmtCstIcms = $this->db->prepare($sqlCstIcms);
                        $stmtCstIcms->execute(array_keys($cstsIcms));
                        foreach ($stmtCstIcms->fetchAll() as $row) {
                            $mapaCstIcms[$row['cst']] = $row['descricao'];
                        }
                    }
                    // Situação Tributária
                    $mapaSituacao = [];
                    if ($cstsSituacao) {
                        $in = implode(',', array_fill(0, count($cstsSituacao), '?'));
                        $sqlSit = 'SELECT cst_icms, situacao_tributaria FROM situacao_tributaria WHERE cst_icms IN (' . $in . ')';
                        $stmtSit = $this->db->prepare($sqlSit);
                        $stmtSit->execute(array_keys($cstsSituacao));
                        foreach ($stmtSit->fetchAll() as $row) {
                            $mapaSituacao[$row['cst_icms']] = $row['situacao_tributaria'];
                        }
                    }
                    // Mercadológico
                    $mercadologicoBusca = [];
                    foreach ($resultados as $prod) {
                        $depto = (int)($prod['depto'] ?? 0);
                        $secao = (int)($prod['secao'] ?? 0);
                        $grupo = (int)($prod['grupo'] ?? 0);
                        $subgrupo = (int)($prod['subgrupo'] ?? 0);
                        $mercadologicoBusca[] = [
                            'depto' => $depto, 'secao' => 0, 'grupo' => 0, 'subgrupo' => 0
                        ];
                        $mercadologicoBusca[] = [
                            'depto' => $depto, 'secao' => $secao, 'grupo' => 0, 'subgrupo' => 0
                        ];
                        $mercadologicoBusca[] = [
                            'depto' => $depto, 'secao' => $secao, 'grupo' => $grupo, 'subgrupo' => 0
                        ];
                        $mercadologicoBusca[] = [
                            'depto' => $depto, 'secao' => $secao, 'grupo' => $grupo, 'subgrupo' => $subgrupo
                        ];
                    }
                    // Remover duplicados
                    $mercadologicoBusca = array_map('unserialize', array_unique(array_map('serialize', $mercadologicoBusca)));
                    $mapaMerc = [];
                    if ($mercadologicoBusca) {
                        $in = implode(' OR ', array_map(function($k){
                            return '(depto=' . (int)$k['depto'] . ' AND secao=' . (int)$k['secao'] . ' AND grupo=' . (int)$k['grupo'] . ' AND subgrupo=' . (int)$k['subgrupo'] . ')';
                        }, $mercadologicoBusca));
                        $sqlMerc = 'SELECT * FROM Mercadologico WHERE ' . $in;
                        $stmtMerc = $this->db->query($sqlMerc);
                        foreach ($stmtMerc->fetchAll() as $row) {
                            $mapaMerc[$row['depto'] . '-' . $row['secao'] . '-' . $row['grupo'] . '-' . $row['subgrupo']] = $row['descritivo'];
                        }
                    }
                    foreach ($resultados as &$prod) {
                        $cstEnt = $prod['cst_pis_cofins_entrada'] ?? '';
                        $cstSai = $prod['cst_pis_cofins_saida'] ?? '';
                        $prod['cst_pis_cofins_entrada_desc'] = $cstEnt !== '' ? ($cstEnt . ' - ' . ($mapaCstPisCofins[$cstEnt] ?? '')) : '';
                        $prod['cst_pis_cofins_saida_desc'] = $cstSai !== '' ? ($cstSai . ' - ' . ($mapaCstPisCofins[$cstSai] ?? '')) : '';
                        $cstIcms = $prod['cst_csosn'] ?? '';
                        $prod['cst_csosn_desc'] = $cstIcms !== '' ? ($cstIcms . ' - ' . ($mapaCstIcms[$cstIcms] ?? '')) : '';
                        $prod['situacao_tributaria_nome'] = $cstIcms !== '' ? ($mapaSituacao[$cstIcms] ?? '') : '';
                        $depto = (int)($prod['depto'] ?? 0);
                        $secao = (int)($prod['secao'] ?? 0);
                        $grupo = (int)($prod['grupo'] ?? 0);
                        $subgrupo = (int)($prod['subgrupo'] ?? 0);
                        $prod['depto_desc'] = $mapaMerc[$depto.'-0-0-0'] ?? '---';
                        $prod['secao_desc'] = $secao ? ($mapaMerc[$depto.'-'.$secao.'-0-0'] ?? '---') : '---';
                        $prod['grupo_desc'] = $grupo ? ($mapaMerc[$depto.'-'.$secao.'-'.$grupo.'-0'] ?? '---') : '---';
                        $prod['subgrupo_desc'] = $subgrupo ? ($mapaMerc[$depto.'-'.$secao.'-'.$grupo.'-'.$subgrupo] ?? '---') : '---';
                    }
                    unset($prod);
                }
            }
        }
        $content = view('consulta-tributaria/index', [
            'resultados' => $resultados,
            'ean' => $ean,
            'descritivo' => $descritivo,
            'regimesFiscais' => $regimesFiscais,
            'regimeFiscalId' => $regimeFiscalId
        ], true);
        require APP_ROOT . '/app/Views/layouts/main.php';
    }

    public function autocompleteDescritivoConsultaTributaria() {
        header('Content-Type: application/json');
        $q = trim($_GET['q'] ?? '');
        if (strlen($q) < 2) {
            echo json_encode([]);
            exit;
        }
        $stmt = $this->db->prepare("SELECT DISTINCT descritivo FROM produtos WHERE revisado = 'sim' AND descritivo LIKE ? ORDER BY descritivo LIMIT 10");
        $stmt->execute(['%' . $q . '%']);
        $sugestoes = array_column($stmt->fetchAll(), 'descritivo');
        echo json_encode($sugestoes);
        exit;
    }

    public function sugestoesEAN() {
        header('Content-Type: application/json');
        
        // Verifica se é admin ou master
        $stmt = $this->db->prepare('SELECT tipo FROM usuarios WHERE id = ?');
        $stmt->execute([$_SESSION['usuario_id']]);
        $tipo = $stmt->fetchColumn();
        
        if ($tipo !== 'admin' && $tipo !== 'master') {
            echo json_encode(['erro' => 'Você não tem permissão para acessar esta funcionalidade.']);
            exit;
        }

        try {
            $stmt = $this->db->query('SELECT ean, data_sugestao FROM sugestoes_ean ORDER BY data_sugestao DESC');
            $sugestoes = $stmt->fetchAll();
            echo json_encode(['dados' => $sugestoes]);
        } catch (Exception $e) {
            echo json_encode(['erro' => 'Erro ao buscar sugestões EAN: ' . $e->getMessage()]);
        }
        exit;
    }

    public function excluirSugestaoEAN() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['erro' => 'Método não permitido']);
            exit;
        }

        // Verifica se é admin ou master
        $stmt = $this->db->prepare('SELECT tipo FROM usuarios WHERE id = ?');
        $stmt->execute([$_SESSION['usuario_id']]);
        $tipo = $stmt->fetchColumn();
        
        if ($tipo !== 'admin' && $tipo !== 'master') {
            echo json_encode(['erro' => 'Você não tem permissão para realizar esta ação.']);
            exit;
        }

        $ean = trim($_POST['ean'] ?? '');
        if (empty($ean)) {
            echo json_encode(['erro' => 'EAN não informado']);
            exit;
        }

        try {
            $stmt = $this->db->prepare('DELETE FROM sugestoes_ean WHERE ean = ?');
            $resultado = $stmt->execute([$ean]);
            
            if ($resultado && $stmt->rowCount() > 0) {
                echo json_encode(['sucesso' => true]);
            } else {
                echo json_encode(['erro' => 'Sugestão EAN não encontrada']);
            }
        } catch (Exception $e) {
            echo json_encode(['erro' => 'Erro ao excluir sugestão EAN: ' . $e->getMessage()]);
        }
        exit;
    }

    public function exportarSugestoesEANTXT() {
        // Verifica se é admin ou master
        $stmt = $this->db->prepare('SELECT tipo FROM usuarios WHERE id = ?');
        $stmt->execute([$_SESSION['usuario_id']]);
        $tipo = $stmt->fetchColumn();
        
        if ($tipo !== 'admin' && $tipo !== 'master') {
            header('HTTP/1.1 403 Forbidden');
            exit('Acesso negado');
        }

        try {
            $stmt = $this->db->query('SELECT ean, data_sugestao FROM sugestoes_ean ORDER BY data_sugestao DESC');
            $sugestoes = $stmt->fetchAll();
            
            $filename = 'sugestoes_ean_' . date('Y-m-d') . '.txt';
            
            header('Content-Type: text/plain; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Cache-Control: no-cache, must-revalidate');
            header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
            
            echo "SUGESTÕES EAN - " . date('d/m/Y H:i:s') . "\n";
            echo str_repeat("=", 50) . "\n\n";
            echo "EAN\t\t\tDATA DA SUGESTÃO\n";
            echo str_repeat("-", 50) . "\n";
            
            foreach ($sugestoes as $sugestao) {
                $dataFormatada = date('d/m/Y H:i:s', strtotime($sugestao['data_sugestao']));
                echo $sugestao['ean'] . "\t\t" . $dataFormatada . "\n";
            }
            
            if (empty($sugestoes)) {
                echo "Nenhuma sugestão EAN encontrada.\n";
            }
            
        } catch (Exception $e) {
            header('HTTP/1.1 500 Internal Server Error');
            exit('Erro ao gerar arquivo TXT: ' . $e->getMessage());
        }
        exit;
    }


} 