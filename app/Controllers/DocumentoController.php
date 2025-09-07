<?php
namespace App\Controllers;

class DocumentoController {
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
        // Busca todas as empresas
        $stmt = $this->db->query('SELECT id, razao_social, cnpj FROM empresas ORDER BY razao_social');
        $empresas = $stmt->fetchAll();

        // Prepara a query base
        $query = '
            SELECT d.*, e.razao_social as empresa_nome 
            FROM documentos d 
            JOIN empresas e ON d.empresa_id = e.id 
            WHERE 1=1
        ';
        $params = [];

        // Aplica os filtros
        if (!empty($_GET['empresa'])) {
            $query .= ' AND d.empresa_id = ?';
            $params[] = $_GET['empresa'];
        }

        if (!empty($_GET['tipo'])) {
            $query .= ' AND d.tipo_documento = ?';
            $params[] = $_GET['tipo'];
        }

        if (!empty($_GET['status'])) {
            $query .= ' AND d.status = ?';
            $params[] = $_GET['status'];
        }

        if (!empty($_GET['data_inicio'])) {
            $query .= ' AND DATE(d.data_upload) >= ?';
            $params[] = $_GET['data_inicio'];
        }

        if (!empty($_GET['data_fim'])) {
            $query .= ' AND DATE(d.data_upload) <= ?';
            $params[] = $_GET['data_fim'];
        }

        $query .= ' ORDER BY d.data_upload DESC';

        // Executa a query com os filtros
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        $documentos = $stmt->fetchAll();

        $content = view('documentos/index', [
            'title' => 'Gestão de Documentos - ' . APP_NAME,
            'empresas' => $empresas,
            'documentos' => $documentos
        ], true);
        require APP_ROOT . '/app/Views/layouts/main.php';
    }

    public function upload() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit;
        }

        try {
            if (!isset($_FILES['documento']) || !isset($_POST['empresa_id']) || !isset($_POST['tipo'])) {
                throw new \Exception('Dados incompletos');
            }

            $empresa_id = (int)$_POST['empresa_id'];
            $tipo = $_POST['tipo'];
            $descricao = $_POST['descricao'] ?? '';
            $observacoes = $_POST['observacoes'] ?? '';
            $sped_periodo = null;

            // Verifica se é um SPED e se tem período
            if (($tipo === 'sped_fiscal' || $tipo === 'sped_contribuicoes') && empty($_POST['sped_periodo'])) {
                throw new \Exception('Período SPED é obrigatório');
            }
            $sped_periodo = $_POST['sped_periodo'] ?? null;

            // Busca informações da empresa
            $stmt = $this->db->prepare('SELECT cnpj FROM empresas WHERE id = ?');
            $stmt->execute([$empresa_id]);
            $empresa = $stmt->fetch();

            if (!$empresa) {
                throw new \Exception('Empresa não encontrada');
            }

            $cnpj = preg_replace('/[^0-9]/', '', $empresa['cnpj']);
            $uploadDir = APP_ROOT . '/public/uploads/' . $cnpj;
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $arquivo = $_FILES['documento'];
            $nomeArquivo = basename($arquivo['name']);
            $caminhoRelativo = 'uploads/' . $cnpj . '/' . $nomeArquivo;
            $caminhoCompleto = $uploadDir . '/' . $nomeArquivo;

            // Move o arquivo
            if (!move_uploaded_file($arquivo['tmp_name'], $caminhoCompleto)) {
                throw new \Exception('Erro ao mover o arquivo');
            }

            // Salva no banco de dados o caminho relativo
            $stmt = $this->db->prepare('
                INSERT INTO documentos (
                    empresa_id, nome_arquivo, tipo_documento, descricao, 
                    caminho_arquivo, tamanho, status, sped_periodo, observacoes
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ');

            $stmt->execute([
                $empresa_id,
                $nomeArquivo,
                $tipo,
                $descricao,
                $caminhoRelativo,
                $arquivo['size'],
                'pendente',
                $sped_periodo,
                $observacoes
            ]);

            header('Location: /mde/documentos?success=1');
            exit;

        } catch (\Exception $e) {
            header('Location: /mde/documentos?error=' . urlencode($e->getMessage()));
            exit;
        }
    }

    public function editar($id) {
        $stmt = $this->db->prepare('
            SELECT d.*, e.razao_social as empresa_nome, e.cnpj as empresa_cnpj
            FROM documentos d 
            JOIN empresas e ON d.empresa_id = e.id 
            WHERE d.id = ?
        ');
        $stmt->execute([$id]);
        $documento = $stmt->fetch();

        if (!$documento) {
            header('Location: /mde/documentos?error=Documento não encontrado');
            exit;
        }

        $content = view('documentos/editar', [
            'title' => 'Editar Documento - ' . APP_NAME,
            'documento' => $documento
        ], true);
        require APP_ROOT . '/app/Views/layouts/main.php';
    }

    public function detalhes($id) {
        $stmt = $this->db->prepare('
            SELECT d.*, e.razao_social as empresa_nome, e.cnpj as empresa_cnpj
            FROM documentos d 
            JOIN empresas e ON d.empresa_id = e.id 
            WHERE d.id = ?
        ');
        $stmt->execute([$id]);
        $documento = $stmt->fetch();

        if (!$documento) {
            header('Location: /mde/documentos?error=Documento não encontrado');
            exit;
        }

        $content = view('documentos/detalhes', [
            'title' => 'Detalhes do Documento - ' . APP_NAME,
            'documento' => $documento
        ], true);
        require APP_ROOT . '/app/Views/layouts/main.php';
    }

    public function download($id) {
        if (is_array($id) && isset($id['id'])) {
            $id = $id['id'];
        }

        $stmt = $this->db->prepare('SELECT * FROM documentos WHERE id = ?');
        $stmt->execute([$id]);
        $documento = $stmt->fetch();

        if (!$documento) {
            header('Location: /mde/documentos?error=Documento não encontrado');
            exit;
        }

        $caminhoRelativo = $documento['caminho_arquivo'];
        $caminhoAbsoluto = APP_ROOT . '/public/' . $caminhoRelativo;

        if (!file_exists($caminhoAbsoluto)) {
            header('Location: /mde/documentos?error=Arquivo não encontrado no servidor');
            exit;
        }

        // Headers para download forçado
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($documento['nome_arquivo']) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($caminhoAbsoluto));
        flush();
        readfile($caminhoAbsoluto);
        exit;
    }

    public function excluir($id) {
        try {
            // Busca o documento
            $stmt = $this->db->prepare('SELECT * FROM documentos WHERE id = ?');
            $stmt->execute([$id]);
            $documento = $stmt->fetch();

            if (!$documento) {
                throw new \Exception('Documento não encontrado');
            }

            // Remove o arquivo físico
            if (file_exists($documento['caminho_arquivo'])) {
                unlink($documento['caminho_arquivo']);
            }

            // Remove o registro do banco
            $stmt = $this->db->prepare('DELETE FROM documentos WHERE id = ?');
            $stmt->execute([$id]);

            header('Location: /mde/documentos?success=Documento excluído com sucesso');
            exit;

        } catch (\Exception $e) {
            header('Location: /mde/documentos?error=' . urlencode($e->getMessage()));
            exit;
        }
    }

    public function atualizar($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit;
        }

        try {
            if (!isset($_POST['tipo']) || !isset($_POST['status'])) {
                throw new \Exception('Dados incompletos');
            }

            $tipo = $_POST['tipo'];
            $status = $_POST['status'];
            $descricao = $_POST['descricao'] ?? '';
            $observacoes = $_POST['observacoes'] ?? '';
            $sped_periodo = null;

            // Verifica se é um SPED e se tem período
            if (($tipo === 'sped_fiscal' || $tipo === 'sped_contribuicoes') && empty($_POST['sped_periodo'])) {
                throw new \Exception('Período SPED é obrigatório');
            }
            $sped_periodo = $_POST['sped_periodo'] ?? null;

            // Atualiza no banco de dados
            $stmt = $this->db->prepare('
                UPDATE documentos 
                SET tipo_documento = ?, 
                    status = ?, 
                    descricao = ?, 
                    observacoes = ?,
                    sped_periodo = ?,
                    data_processamento = CASE 
                        WHEN status != ? AND ? = "processado" THEN CURRENT_TIMESTAMP 
                        ELSE data_processamento 
                    END
                WHERE id = ?
            ');

            $stmt->execute([
                $tipo,
                $status,
                $descricao,
                $observacoes,
                $sped_periodo,
                $status,
                $status,
                $id
            ]);

            header('Location: /mde/documentos?success=Documento atualizado com sucesso');
            exit;

        } catch (\Exception $e) {
            header('Location: /mde/documentos?error=' . urlencode($e->getMessage()));
            exit;
        }
    }

    public function relatorio() {
        // Prepara a query base
        $query = '
            SELECT d.*, e.razao_social as empresa_nome, e.cnpj as empresa_cnpj
            FROM documentos d 
            JOIN empresas e ON d.empresa_id = e.id 
            WHERE 1=1
        ';
        $params = [];

        // Aplica os filtros
        if (!empty($_GET['empresa'])) {
            $query .= ' AND d.empresa_id = ?';
            $params[] = $_GET['empresa'];
        }

        if (!empty($_GET['tipo'])) {
            $query .= ' AND d.tipo_documento = ?';
            $params[] = $_GET['tipo'];
        }

        if (!empty($_GET['status'])) {
            $query .= ' AND d.status = ?';
            $params[] = $_GET['status'];
        }

        if (!empty($_GET['data_inicio'])) {
            $query .= ' AND DATE(d.data_upload) >= ?';
            $params[] = $_GET['data_inicio'];
        }

        if (!empty($_GET['data_fim'])) {
            $query .= ' AND DATE(d.data_upload) <= ?';
            $params[] = $_GET['data_fim'];
        }

        $query .= ' ORDER BY d.data_upload DESC';

        // Executa a query com os filtros
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        $documentos = $stmt->fetchAll();

        // Define os headers para download do Excel
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="relatorio_documentos_' . date('Y-m-d') . '.xls"');
        header('Pragma: no-cache');
        header('Expires: 0');

        // Cria o conteúdo do Excel
        echo "Relatório de Documentos\n\n";
        echo "Data de Geração: " . date('d/m/Y H:i:s') . "\n\n";

        echo "Nome do Arquivo\tEmpresa\tCNPJ\tTipo\tStatus\tData Upload\tData Processamento\tTamanho\tDescrição\tObservações\n";

        foreach ($documentos as $doc) {
            echo implode("\t", [
                $doc['nome_arquivo'],
                $doc['empresa_nome'],
                $doc['empresa_cnpj'],
                $doc['tipo_documento'],
                $doc['status'],
                date('d/m/Y H:i', strtotime($doc['data_upload'])),
                $doc['data_processamento'] ? date('d/m/Y H:i', strtotime($doc['data_processamento'])) : '',
                number_format($doc['tamanho'] / 1024, 2) . ' KB',
                str_replace(["\t", "\n", "\r"], ' ', $doc['descricao']),
                str_replace(["\t", "\n", "\r"], ' ', $doc['observacoes'])
            ]) . "\n";
        }
        exit;
    }
} 