<?php
namespace App\Controllers;

class ConsultaTributariaController {
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
        $content = view('consulta-tributaria/index', [
            'title' => 'Consulta Tributária - ' . APP_NAME
        ], true);
        require APP_ROOT . '/app/Views/layouts/main.php';
    }

    public function sugerirEan() {
        file_put_contents(__DIR__ . '/debug_sugerir_ean.txt', json_encode(file_get_contents('php://input')) . PHP_EOL, FILE_APPEND);
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true);
        $ean = trim($input['ean'] ?? '');
        if (!$ean) {
            echo json_encode(['success' => false, 'message' => 'EAN não informado.']);
            exit;
        }
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM sugestoes_ean WHERE ean = ?');
        $stmt->execute([$ean]);
        if ($stmt->fetchColumn() == 0) {
            $stmt = $this->db->prepare('INSERT INTO sugestoes_ean (ean) VALUES (?)');
            $stmt->execute([$ean]);
            echo json_encode(['success' => true, 'message' => 'O EAN informado não foi encontrado em nosso banco de dados. Ele foi registrado como sugestão para futura análise e possível inclusão.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'O EAN informado já está em análise por nossa equipe. Em breve, ele poderá ser incorporado ao nosso banco de dados.']);
        }
        exit;
    }

    public function autocompleteDescritivo() {
        header('Content-Type: application/json');
        $q = trim($_GET['q'] ?? '');
        if (strlen($q) < 2) {
            echo json_encode([]);
            exit;
        }
        $stmt = $this->db->prepare("SELECT DISTINCT descritivo FROM produtos WHERE descritivo LIKE ? AND revisado = 'sim' LIMIT 10");
        $stmt->execute(['%' . $q . '%']);
        $result = $stmt->fetchAll(\PDO::FETCH_COLUMN);
        echo json_encode($result);
        exit;
    }

    public function buscar() {
        try {
            $ean = trim($_POST['ean'] ?? '');
            $descritivo = trim($_POST['descritivo'] ?? '');

            $sql = "SELECT 
                p.*,
                m.descritivo AS depto_desc,
                m2.descritivo AS secao_desc,
                m3.descritivo AS grupo_desc,
                m4.descritivo AS subgrupo_desc,
                cst_entrada.descricao AS cst_pis_cofins_entrada_desc,
                cst_saida.descricao AS cst_pis_cofins_saida_desc,
                cst_icms.descricao AS cst_csosn_desc,
                st.situacao_tributaria AS situacao_tributaria_nome
            FROM produtos p
            LEFT JOIN mercadologico m ON p.depto = m.depto AND m.secao = 0 AND m.grupo = 0 AND m.subgrupo = 0
            LEFT JOIN mercadologico m2 ON p.depto = m2.depto AND p.secao = m2.secao AND m2.grupo = 0 AND m2.subgrupo = 0
            LEFT JOIN mercadologico m3 ON p.depto = m3.depto AND p.secao = m3.secao AND p.grupo = m3.grupo AND m3.subgrupo = 0
            LEFT JOIN mercadologico m4 ON p.depto = m4.depto AND p.secao = m4.secao AND p.grupo = m4.grupo AND p.subgrupo = m4.subgrupo
            LEFT JOIN cst_pis_cofins cst_entrada ON p.cst_pis_cofins_entrada = cst_entrada.cst
            LEFT JOIN cst_pis_cofins cst_saida ON p.cst_pis_cofins_saida = cst_saida.cst
            LEFT JOIN cst_icms cst_icms ON p.cst_csosn = cst_icms.cst
            LEFT JOIN situacao_tributaria st ON p.cst_csosn = st.cst_icms
            WHERE 1=1";
            $where = [];
            $params = [];
            if ($ean !== '') {
                $where[] = "p.ean = ?";
                $params[] = $ean;
            }
            if ($descritivo !== '') {
                $where[] = "p.descritivo LIKE ?";
                $params[] = '%' . $descritivo . '%';
            }
            if ($ean !== '' || $descritivo !== '') {
                $where[] = "p.revisado = 'sim'";
            }
            $sql .= count($where) ? ' AND ' . implode(' AND ', $where) : '';

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $resultados = $stmt->fetchAll();

            $ean_sugerir = null;
            if ($ean !== '' && $descritivo === '' && empty($resultados) && preg_match('/^[0-9]{8}$|^[0-9]{13}$/', $ean)) {
                $ean_sugerir = $ean;
            }
            $content = null;
            try {
                $content = view('consulta-tributaria/index', [
                    'resultados' => $resultados,
                    'ean' => $ean,
                    'descritivo' => $descritivo,
                    'ean_sugerir' => $ean_sugerir,
                    'title' => 'Consulta Tributária - ' . APP_NAME
                ], true);
            } catch (\Throwable $e) {
                echo "<pre>Erro na view: " . $e->getMessage() . "\n" . $e->getTraceAsString() . "</pre>";
                exit;
            }
            require APP_ROOT . '/app/Views/layouts/main.php';
        } catch (\Throwable $e) {
            echo "<pre>Erro no controller: " . $e->getMessage() . "\n" . $e->getTraceAsString() . "</pre>";
            exit;
        }
    }
} 