<?php
namespace App\Controllers;

class ArquivosController {
    protected $db;
    protected $uploadDir;

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
        $this->uploadDir = APP_ROOT . '/public/uploads/';
        if (!file_exists($this->uploadDir)) {
            mkdir($this->uploadDir, 0777, true);
        }
    }

    public function index() {
        // Prepara a query base
        $query = 'SELECT * FROM arquivos';
        $params = [];

        $tipo_usuario = $_SESSION['tipo_usuario'] ?? null;
        $loja_id = $_SESSION['loja_id'] ?? null;

        // Se for usuário tipo 3, filtra por loja
        if ($tipo_usuario == 3) {
            $query .= ' WHERE empresa_id = ?';
            $params[] = $loja_id;
        }

        $query .= ' ORDER BY created_at DESC';
        
        // Executa a query com os parâmetros
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        $arquivos = $stmt->fetchAll();
        
        $content = view('arquivos/index', [
            'arquivos' => $arquivos
        ], true);
        
        require APP_ROOT . '/app/Views/layouts/main.php';
    }

    public function upload() {
        // Garante que a sessão está iniciada
        if (session_status() === PHP_SESSION_NONE) session_start();
        $tipo_usuario = $_SESSION['tipo_usuario'] ?? null;
        $loja_id = $_SESSION['loja_id'] ?? null;

        // DEBUG: Logar tipo_usuario e usuario_id
        file_put_contents(__DIR__.'/../../public/debug_tipo_usuario.txt', print_r(['tipo_usuario'=>$tipo_usuario, 'usuario_id'=>$_SESSION['usuario_id'] ?? null], true));

        // Buscar lojas para o dropdown
        if ($tipo_usuario == 3) {
            $usuario_id = $_SESSION['usuario_id'] ?? null;
            $empresas = $this->db->prepare('SELECT e.id, e.razao_social, e.tipo_empresa FROM empresas e INNER JOIN usuario_empresas ue ON ue.empresa_id = e.id WHERE ue.usuario_id = ? ORDER BY e.razao_social');
            $empresas->execute([$usuario_id]);
            $empresas = $empresas->fetchAll();
        } else {
            $empresas = $this->db->query('SELECT id, razao_social, tipo_empresa FROM empresas ORDER BY razao_social')->fetchAll();
        }
        // DEBUG: Logar empresas retornadas
        file_put_contents(__DIR__.'/../../public/debug_empresas_upload.txt', print_r($empresas, true));

        // Garantia extra: se for tipo 3 e vier mais de uma empresa, filtra manualmente
        // (Removido, pois agora pode ter várias empresas vinculadas ao tipo 3)

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                if (!isset($_FILES['arquivo']) || $_FILES['arquivo']['error'] !== UPLOAD_ERR_OK) {
                    throw new \Exception('Erro no upload do arquivo');
                }
                $empresa_id = $_POST['empresa_id'] ?? null;
                if (!$empresa_id) {
                    throw new \Exception('Selecione a loja antes de enviar o arquivo');
                }
                $arquivo = $_FILES['arquivo'];
                $modelo = $_POST['modelo'] ?? '';
                if (!$modelo) {
                    throw new \Exception('Modelo não especificado');
                }

                // Validar tipo de arquivo
                $tiposPermitidos = ['xml', 'pdf', 'txt', 'csv', 'xls', 'xlsx'];
                $extensao = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));
                if (!in_array($extensao, $tiposPermitidos)) {
                    throw new \Exception('Tipo de arquivo não permitido');
                }

                // Gerar nome único para o arquivo
                $nomeArquivo = uniqid() . '.' . $extensao;
                $caminhoCompleto = $this->uploadDir . $nomeArquivo;

                // Mover arquivo
                if (!move_uploaded_file($arquivo['tmp_name'], $caminhoCompleto)) {
                    throw new \Exception('Erro ao salvar arquivo');
                }

                // Definir status padronizado para todos os cases
                $status = 'Ativo';
                $status = trim($status);

                // Processar arquivo de acordo com o modelo
                $now = date('Y-m-d H:i:s');
                switch ($modelo) {
                    case 'romaneio':
                        $linhas = $extensao === 'csv' ? $this->lerCSV($caminhoCompleto) : $this->lerTXT($caminhoCompleto);
                        $sql = 'INSERT IGNORE INTO romaneio (empresa_id, chave, created_at, updated_at) VALUES (?, ?, ?, ?)';
                        $stmt = $this->db->prepare($sql);
                        $total_inseridos = 0;
                        foreach ($linhas as $linha) {
                            $chave = trim($linha[0] ?? '');
                            if (strlen($chave) === 44) {
                                $stmt->execute([$empresa_id, $chave, $now, $now]);
                                $total_inseridos++;
                            }
                        }
                        error_log("Romaneio importado: $total_inseridos registros inseridos");
                        // Registrar log de envio de romaneio
                        $usuario_id = $_SESSION['usuario_id'] ?? null;
                        $stmtLog = $this->db->prepare('INSERT INTO log_envio_arquivos (empresa_id, usuario_id, data_envio, quantidade_notas, nome_arquivo, nome_original, tipo_arquivo, status) VALUES (?, ?, NOW(), ?, ?, ?, ?, ?)');
                        $stmtLog->execute([$empresa_id, $usuario_id, $total_inseridos, $nomeArquivo, $_FILES['arquivo']['name'] ?? $nomeArquivo, 'Romaneio', $status]);
                        break;

                    case 'desconhecimento':
                        $linhas = $extensao === 'csv' ? $this->lerCSV($caminhoCompleto) : $this->lerTXT($caminhoCompleto);
                        $sql = 'INSERT IGNORE INTO desconhecimento (empresa_id, chave, created_at, updated_at) VALUES (?, ?, ?, ?)';
                        $stmt = $this->db->prepare($sql);
                        $total_inseridos = 0;
                        foreach ($linhas as $linha) {
                            $chave = trim($linha[0] ?? '');
                            if (strlen($chave) === 44) {
                                $stmt->execute([$empresa_id, $chave, $now, $now]);
                                $total_inseridos++;
                            }
                        }
                        // Registrar log de envio de desconhecimento
                        $usuario_id = $_SESSION['usuario_id'] ?? null;
                        $stmtLog = $this->db->prepare('INSERT INTO log_envio_arquivos (empresa_id, usuario_id, data_envio, quantidade_notas, nome_arquivo, nome_original, tipo_arquivo, status) VALUES (?, ?, NOW(), ?, ?, ?, ?, ?)');
                        $stmtLog->execute([$empresa_id, $usuario_id, $total_inseridos, $nomeArquivo, $_FILES['arquivo']['name'] ?? $nomeArquivo, 'Desconhecimento', $status]);
                        break;

                    case 'fornecedores':
                        $linhas = $extensao === 'csv' ? $this->lerCSV($caminhoCompleto) : $this->lerTXT($caminhoCompleto);
                        if (!empty($linhas) && isset($linhas[0][1]) && (stripos($linhas[0][1], 'razao') !== false || stripos($linhas[0][2] ?? '', 'cnpj') !== false)) {
                            array_shift($linhas);
                        }
                        $sqlFornecedor = 'INSERT INTO fornecedores (razao_social, cnpj, created_at, updated_at) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE razao_social = VALUES(razao_social), updated_at = VALUES(updated_at)';
                        $stmtFornecedor = $this->db->prepare($sqlFornecedor);
                        $sqlSelectFornecedor = 'SELECT id FROM fornecedores WHERE cnpj = ?';
                        $stmtSelectFornecedor = $this->db->prepare($sqlSelectFornecedor);
                        $sqlEmpresaFornecedor = 'INSERT INTO empresa_fornecedor (empresa_id, fornecedor_id, codigo_interno) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE codigo_interno = VALUES(codigo_interno)';
                        $stmtEmpresaFornecedor = $this->db->prepare($sqlEmpresaFornecedor);
                        $total_inseridos = 0;
                        foreach ($linhas as $linha) {
                            $codigo = trim($linha[0] ?? '');
                            $razao = trim($linha[1] ?? '');
                            $cnpj = preg_replace('/[^0-9]/', '', trim($linha[2] ?? ''));
                            if ($codigo && $razao && $cnpj) {
                                $stmtFornecedor->execute([$razao, $cnpj, $now, $now]);
                                $stmtSelectFornecedor->execute([$cnpj]);
                                $fornecedor = $stmtSelectFornecedor->fetch();
                                if ($fornecedor) {
                                    $fornecedor_id = $fornecedor['id'];
                                    $stmtEmpresaFornecedor->execute([$empresa_id, $fornecedor_id, $codigo]);
                                    $total_inseridos++;
                                }
                            }
                        }
                        // Registrar log de envio de fornecedores
                        $usuario_id = $_SESSION['usuario_id'] ?? null;
                        $stmtLog = $this->db->prepare('INSERT INTO log_envio_arquivos (empresa_id, usuario_id, data_envio, quantidade_notas, nome_arquivo, nome_original, tipo_arquivo, status) VALUES (?, ?, NOW(), ?, ?, ?, ?, ?)');
                        $stmtLog->execute([$empresa_id, $usuario_id, $total_inseridos, $nomeArquivo, $_FILES['arquivo']['name'] ?? $nomeArquivo, 'Fornecedores', $status]);
                        break;

                    case 'notas':
                        $linhas = $extensao === 'csv' ? $this->lerCSV($caminhoCompleto) : $this->lerTXT($caminhoCompleto);
                        // Remove cabeçalho se identificar 'nota' ou 'processo' na primeira linha
                        if (!empty($linhas) && isset($linhas[0][3]) && (stripos($linhas[0][3], 'nota') !== false || stripos($linhas[0][0], 'processo') !== false)) {
                            array_shift($linhas);
                        }
                        $sql = 'INSERT IGNORE INTO notas (empresa_id, codigo_processo, codigo_fiscal, valor_nota, numero_nota, chave_nota, hash, coleta, escriturador, data_entrada, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
                        $stmt = $this->db->prepare($sql);
                        $semChaveContador = 1;
                        $semChaveTimestamp = time();
                        $total_inseridos = 0;
                        foreach ($linhas as $linha) {
                            $codigo_processo = trim($linha[0] ?? '');
                            $codigo_fiscal = trim($linha[1] ?? '');
                            $valor_nota = str_replace([',', 'R$',' '], ['.', '', ''], trim($linha[2] ?? ''));
                            $numero_nota = trim($linha[3] ?? '');
                            $chave_nota = trim($linha[4] ?? '');
                            if ($chave_nota === '' || is_null($chave_nota)) {
                                $chave_nota = 'SEMCHAVE-' . ($numero_nota ?: 'SEMNUMERO') . '-' . ($codigo_fiscal ?: 'SEM_FISCAL');
                            }
                            $hash = $numero_nota && $valor_nota ? trim($numero_nota . '-' . number_format((float)$valor_nota, 2, '.', '')) : null;
                            $coleta = trim($linha[5] ?? '');
                            $escriturador = trim($linha[6] ?? '');
                            $data_entrada = null;
                            if (isset($linha[7]) && trim($linha[7]) !== '') {
                                $data_bruta = trim($linha[7]);
                                // Tenta converter para Y-m-d H:i:s
                                $timestamp = strtotime(str_replace(['/','.'], '-', $data_bruta));
                                if ($timestamp && $timestamp > 0) {
                                    $data_entrada = date('Y-m-d H:i:s', $timestamp);
                                } else {
                                    $data_entrada = null;
                                }
                            }
                            // DEBUG LOG
                            error_log('Linha recebida: ' . print_r($linha, true));
                            error_log('codigo_processo: ' . $codigo_processo);
                            error_log('codigo_fiscal: ' . $codigo_fiscal);
                            error_log('valor_nota: ' . $valor_nota);
                            error_log('numero_nota: ' . $numero_nota);
                            error_log('chave_nota: "' . $chave_nota . '"');
                            if ($codigo_processo && $codigo_fiscal && $valor_nota && $numero_nota) {
                                $stmt->execute([$empresa_id, $codigo_processo, $codigo_fiscal, $valor_nota, $numero_nota, $chave_nota, $hash, $coleta, $escriturador, $data_entrada, $now, $now]);
                                $total_inseridos++;
                            }
                        }
                        // Registrar log de envio de notas (lançamentos)
                        $usuario_id = $_SESSION['usuario_id'] ?? null;
                        $stmtLog = $this->db->prepare('INSERT INTO log_envio_arquivos (empresa_id, usuario_id, data_envio, quantidade_notas, nome_arquivo, nome_original, tipo_arquivo, status) VALUES (?, ?, NOW(), ?, ?, ?, ?, ?)');
                        $stmtLog->execute([$empresa_id, $usuario_id, $total_inseridos, $nomeArquivo, $_FILES['arquivo']['name'] ?? $nomeArquivo, 'Lançamentos', $status]);
                        break;

                    case 'sefaz':
                        $linhas = $extensao === 'csv' ? $this->lerCSV($caminhoCompleto) : $this->lerTXT($caminhoCompleto);
                        // Remove cabeçalho se identificar 'Numero NF-e' na primeira linha
                        if (!empty($linhas) && isset($linhas[0][0]) && stripos($linhas[0][0], 'numero') !== false) {
                            array_shift($linhas);
                        }
                        $sql = 'INSERT IGNORE INTO auditoria_notas (empresa_id, numero, cnpj, razao_social, data_emissao, valor, chave, hash, uf, situacao, tipo, data_consulta) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())';
                        $stmt = $this->db->prepare($sql);
                        $notasInseridas = 0;
                        foreach ($linhas as $linha) {
                            $numero = ltrim(preg_replace('/[^0-9]/', '', trim($linha[0] ?? '')), '0');
                            $cnpj = preg_replace('/[^0-9]/', '', trim($linha[1] ?? ''));
                            $razao_social = trim($linha[2] ?? '');
                            $data_emissao = isset($linha[3]) ? date('Y-m-d H:i:s', strtotime(str_replace('/','-',$linha[3]))) : null;
                            $valor = isset($linha[5]) ? str_replace(['.','R$',' '], ['','', ''], $linha[5]) : null;
                            $valor = str_replace(',', '.', $valor);
                            $chave = trim(str_replace("'", '', $linha[6] ?? ''));
                            $uf = trim($linha[7] ?? '');
                            $situacao = trim($linha[8] ?? '');
                            $tipo = trim($linha[9] ?? '');
                            $hash = $numero && $valor ? trim($numero . '-' . number_format((float)$valor, 2, '.', '')) : null;
                            if ($numero && $cnpj && $razao_social && $data_emissao && $valor && $chave && $uf) {
                                $stmt->execute([$empresa_id, $numero, $cnpj, $razao_social, $data_emissao, $valor, $chave, $hash, $uf, $situacao, $tipo]);
                                $notasInseridas++;
                            }
                        }
                        // Registrar log de envio de notas
                        $usuario_id = $_SESSION['usuario_id'] ?? null;
                        $stmtLog = $this->db->prepare('INSERT INTO log_envio_arquivos (empresa_id, usuario_id, data_envio, quantidade_notas, nome_arquivo, nome_original, tipo_arquivo, status) VALUES (?, ?, NOW(), ?, ?, ?, ?, ?)');
                        $stmtLog->execute([$empresa_id, $usuario_id, $notasInseridas, $nomeArquivo, $_FILES['arquivo']['name'] ?? $nomeArquivo, 'Sefaz', $status]);
                        break;

                    case 'financeiro':
                        $linhas = $extensao === 'csv' ? $this->lerCSV($caminhoCompleto) : $this->lerTXT($caminhoCompleto);
                        // Remove cabeçalho se identificar 'numero_finan' ou 'nf' na primeira linha
                        if (!empty($linhas) && isset($linhas[0][0]) && (stripos($linhas[0][0], 'numero_finan') !== false || stripos($linhas[0][1] ?? '', 'nf') !== false)) {
                            array_shift($linhas);
                        }
                        $sql = 'INSERT IGNORE INTO arius_financeiro (empresa_id, numero_finan, nf, valor, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?)';
                        $stmt = $this->db->prepare($sql);
                        $total_inseridos = 0;
                        foreach ($linhas as $linha) {
                            // Formato: numero_finan;nf;valor
                            $numero_finan = trim($linha[0] ?? '');
                            $nf = trim($linha[1] ?? '');
                            $valor = str_replace([',', 'R$', ' '], ['.', '', ''], trim($linha[2] ?? ''));
                            
                            // Validar se o valor é numérico
                            if (!is_numeric($valor)) {
                                $valor = null;
                            }
                            
                            // Usar o empresa_id do formulário (selecionado na página)
                            if ($empresa_id) {
                                $stmt->execute([$empresa_id, $numero_finan, $nf, $valor, $now, $now]);
                                $total_inseridos++;
                            }
                        }
                        // Registrar log de envio de financeiro
                        $usuario_id = $_SESSION['usuario_id'] ?? null;
                        $stmtLog = $this->db->prepare('INSERT INTO log_envio_arquivos (empresa_id, usuario_id, data_envio, quantidade_notas, nome_arquivo, nome_original, tipo_arquivo, status) VALUES (?, ?, NOW(), ?, ?, ?, ?, ?)');
                        $stmtLog->execute([$empresa_id, $usuario_id, $total_inseridos, $nomeArquivo, $_FILES['arquivo']['name'] ?? $nomeArquivo, 'Financeiro', $status]);
                        break;

                    default:
                        throw new \Exception('Modelo não suportado');
                }

                header('Location: /arquivos?success=1');
                exit;
            } catch (\Exception $e) {
                $error = 'Erro no upload: ' . $e->getMessage();
            }
        }
        $content = view('arquivos/upload', [
            'error' => $error ?? null,
            'empresas' => $empresas
        ], true);
        require APP_ROOT . '/app/Views/layouts/main.php';
    }

    private function lerCSV($arquivo) {
        $dados = [];
        if (($handle = fopen($arquivo, 'r')) !== false) {
            // Detectar separador na primeira linha
            $primeiraLinha = fgets($handle);
            $sep = (substr_count($primeiraLinha, ';') > substr_count($primeiraLinha, ',')) ? ';' : ',';
            // Voltar o ponteiro para o início do arquivo
            rewind($handle);
            while (($row = fgetcsv($handle, 0, $sep)) !== false) {
                $dados[] = $row;
            }
            fclose($handle);
        }
        return $dados;
    }

    private function lerTXT($arquivo) {
        $linhas = file($arquivo, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if (empty($linhas)) return [];
        // Detectar separador na primeira linha
        $sep = (substr_count($linhas[0], ';') > substr_count($linhas[0], "\t")) ? ';' : "\t";
        return array_map(function($l) use ($sep) { return explode($sep, $l); }, $linhas);
    }

    public function excluir($id) {
        try {
            // Buscar informações do arquivo
            $arquivo = $this->db->prepare('SELECT * FROM arquivos WHERE id = ?');
            $arquivo->execute([$id]);
            $arquivo = $arquivo->fetch();
            
            if (!$arquivo) {
                throw new \Exception('Arquivo não encontrado');
            }
            
            // Excluir arquivo físico
            $caminhoCompleto = $this->uploadDir . $arquivo['nome_arquivo'];
            if (file_exists($caminhoCompleto)) {
                unlink($caminhoCompleto);
            }
            
            // Excluir registro do banco
            $sql = 'DELETE FROM arquivos WHERE id = ?';
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            
            header('Location: /arquivos?success=1');
        } catch (\Exception $e) {
            header('Location: /arquivos?error=' . urlencode($e->getMessage()));
        }
        exit;
    }

    public function download($id) {
        try {
            $arquivo = $this->db->prepare('SELECT * FROM arquivos WHERE id = ?');
            $arquivo->execute([$id]);
            $arquivo = $arquivo->fetch();
            
            if (!$arquivo) {
                throw new \Exception('Arquivo não encontrado');
            }
            
            $caminhoCompleto = $this->uploadDir . $arquivo['nome_arquivo'];
            if (!file_exists($caminhoCompleto)) {
                throw new \Exception('Arquivo não encontrado no servidor');
            }
            
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $arquivo['nome_original'] . '"');
            header('Content-Length: ' . filesize($caminhoCompleto));
            readfile($caminhoCompleto);
            exit;
        } catch (\Exception $e) {
            header('Location: /arquivos?error=' . urlencode($e->getMessage()));
            exit;
        }
    }

    public function testeUpload() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            file_put_contents(__DIR__.'/../../public/debug_teste_upload.txt', print_r($_POST, true) . print_r($_FILES, true));
            echo "POST recebido!";
            exit;
        }
        echo '<form method="post" enctype="multipart/form-data">
            <input type="text" name="empresa_id" value="1">
            <input type="file" name="arquivo">
            <button type="submit">Enviar</button>
        </form>';
    }
} 