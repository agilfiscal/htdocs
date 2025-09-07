<?php
namespace App\Controllers;

use PDO;

class NotificacaoController {
    protected $db;
    public function __construct($params = null) {
        if (session_status() === PHP_SESSION_NONE) session_start();
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

    // Endpoint para buscar notificações do usuário logado
    public function getNotificacoes() {
        if (!isset($_SESSION['usuario_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Usuário não autenticado']);
            return;
        }
        $usuario_id = $_SESSION['usuario_id'];
        $tipo_usuario = $_SESSION['tipo'] ?? null;

        // Para operadores, não retorna notificações de vencimento
        if ($tipo_usuario == 3) {
            echo json_encode(['notificacoes' => []]);
            return;
        }

        // Para administradores, buscar vencimentos apenas das empresas que estão atendendo
        $sql = 'SELECT v.id, v.nota_id, n.numero, e.razao_social as empresa_nome, e.tipo_empresa, v.data_vencimento, v.data_criacao
                FROM auditoria_notas_vencimentos v
                JOIN auditoria_notas n ON n.id = v.nota_id
                JOIN empresas e ON e.id = n.empresa_id
                JOIN empresas_atendidas ea ON ea.empresa_id = e.id
                WHERE ea.usuario_id = ? 
                AND v.nota_id NOT IN (
                    SELECT nota_id FROM notificacoes_resolvidas
                )
                ORDER BY v.id DESC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$_SESSION['usuario_id']]);
        $notificacoes = $stmt->fetchAll();

        // Adiciona a tag Matriz/Filial ao nome da empresa
        foreach ($notificacoes as &$n) {
            if (isset($n['tipo_empresa'])) {
                if (strtolower($n['tipo_empresa']) === 'matriz') {
                    $n['empresa_nome'] .= ' - Matriz';
                } elseif (strtolower($n['tipo_empresa']) === 'filial') {
                    $n['empresa_nome'] .= ' - Filial';
                }
            }
        }

        header('Content-Type: application/json');
        echo json_encode(['notificacoes' => $notificacoes]);
    }

    // Endpoint para exibir detalhes das notas ao clicar na notificação
    public function getNotasDetalhes() {
        if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['empresa_atendida_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Usuário não autenticado ou empresa não selecionada']);
            return;
        }
        $empresa_id = $_SESSION['empresa_atendida_id'];
        $sql = 'SELECT n.id, n.numero, n.razao_social as empresa_nome, n.data_emissao, n.valor, n.chave
                FROM auditoria_notas n
                WHERE n.empresa_id = :empresa_id
                ORDER BY n.id DESC LIMIT 20';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':empresa_id' => $empresa_id]);
        $notas = $stmt->fetchAll();
        header('Content-Type: application/json');
        echo json_encode(['notas' => $notas]);
    }

    // Endpoint para resolver/excluir notificação
    public function resolverNotificacao($params) {
        if (!isset($_SESSION['usuario_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Usuário não autenticado']);
            return;
        }
        $usuario_id = $_SESSION['usuario_id'];
        // Aceita id tanto por $params['id'] quanto por $_POST['id']
        $vencimento_id = $params['id'] ?? ($_POST['id'] ?? null);
        if (!$vencimento_id) {
            // Tenta pegar da URL (ex: /notificacoes/resolver/123)
            if (isset($_SERVER['REQUEST_URI'])) {
                $partes = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
                $idx = array_search('resolver', $partes);
                if ($idx !== false && isset($partes[$idx+1])) {
                    $vencimento_id = $partes[$idx+1];
                }
            }
        }
        if (!$vencimento_id) {
            http_response_code(400);
            echo json_encode(['error' => 'ID da notificação não informado']);
            return;
        }
        
        try {
            $this->db->beginTransaction();
            
            // Buscar dados do vencimento incluindo quem criou
            $stmt = $this->db->prepare('SELECT nota_id, usuario_criador_id FROM auditoria_notas_vencimentos WHERE id = ?');
            $stmt->execute([$vencimento_id]);
            $vencimento_data = $stmt->fetch();
            
            if (!$vencimento_data) {
                http_response_code(404);
                echo json_encode(['error' => 'Vencimento não encontrado']);
                return;
            }
            
            $nota_id = $vencimento_data['nota_id'];
            $operador_id = $vencimento_data['usuario_criador_id'];
            
            // Verificar se a nota já foi resolvida por algum admin
            $stmt = $this->db->prepare('SELECT id FROM notificacoes_resolvidas WHERE nota_id = ?');
            $stmt->execute([$nota_id]);
            $jaResolvida = $stmt->fetch();
            
            // Se não foi resolvida ainda, inserir na tabela notificacoes_resolvidas
            if (!$jaResolvida) {
                $stmt = $this->db->prepare('INSERT INTO notificacoes_resolvidas (usuario_id, nota_id, data_resolvida) VALUES (?, ?, NOW())');
                $stmt->execute([$usuario_id, $nota_id]);
            }
            
            // Se existe um operador que criou o vencimento e é diferente do admin que está resolvendo
            if ($operador_id && $operador_id != $usuario_id) {
                // Buscar dados da nota para a mensagem
                $stmt = $this->db->prepare('SELECT numero FROM auditoria_notas WHERE id = ?');
                $stmt->execute([$nota_id]);
                $nota_numero = $stmt->fetchColumn();
                
                // Buscar nome do admin que resolveu
                $stmt = $this->db->prepare('SELECT nome FROM usuarios WHERE id = ?');
                $stmt->execute([$usuario_id]);
                $admin_nome = $stmt->fetchColumn();
                
                $mensagem = "Sua solicitação de escrituração da nota {$nota_numero} foi feita pelo usuário {$admin_nome}.";
                
                // Criar notificação para o operador
                $stmt = $this->db->prepare('INSERT INTO notificacoes_resolucao_operador (operador_id, admin_id, nota_id, vencimento_id, mensagem) VALUES (?, ?, ?, ?, ?)');
                $stmt->execute([$operador_id, $usuario_id, $nota_id, $vencimento_id, $mensagem]);
            }
            
            $this->db->commit();
            echo json_encode(['sucesso' => true]);
            
        } catch (\Exception $e) {
            $this->db->rollBack();
            error_log('Erro ao resolver notificação: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => 'Erro interno do servidor']);
        }
    }

    // Endpoint para buscar notificações de resolução para operadores
    public function getNotificacoesResolucao() {
        if (!isset($_SESSION['usuario_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Usuário não autenticado']);
            return;
        }
        
        $usuario_id = $_SESSION['usuario_id'];
        
        $sql = 'SELECT nro.id, nro.mensagem, nro.data_criacao, nro.visualizada,
                       n.numero as nota_numero, e.razao_social as empresa_nome, e.tipo_empresa,
                       u.nome as admin_nome
                FROM notificacoes_resolucao_operador nro
                JOIN auditoria_notas n ON n.id = nro.nota_id
                JOIN empresas e ON e.id = n.empresa_id
                JOIN usuarios u ON u.id = nro.admin_id
                JOIN usuario_empresas ue ON ue.empresa_id = e.id
                WHERE nro.operador_id = ? AND ue.usuario_id = ?
                ORDER BY nro.data_criacao DESC LIMIT 20';
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$usuario_id, $usuario_id]);
        $notificacoes = $stmt->fetchAll();

        // Adiciona a tag Matriz/Filial ao nome da empresa
        foreach ($notificacoes as &$n) {
            if (isset($n['tipo_empresa'])) {
                if (strtolower($n['tipo_empresa']) === 'matriz') {
                    $n['empresa_nome'] .= ' - Matriz';
                } elseif (strtolower($n['tipo_empresa']) === 'filial') {
                    $n['empresa_nome'] .= ' - Filial';
                }
            }
        }

        header('Content-Type: application/json');
        echo json_encode(['notificacoes_resolucao' => $notificacoes]);
    }



    // Endpoint para buscar histórico de resoluções
    public function getHistoricoResolucoes() {
        if (!isset($_SESSION['usuario_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Usuário não autenticado']);
            return;
        }
        
        $usuario_id = $_SESSION['usuario_id'];
        $tipo_usuario = $_SESSION['tipo'] ?? null;
        
        
        // Se for operador, busca apenas suas notificações das empresas atreladas
        // Se for admin/master, busca todas as notificações de resolução
        if ($tipo_usuario == 3 || $tipo_usuario === '3' || $tipo_usuario === 'operator') { // Operador
            $sql = 'SELECT nro.id, nro.mensagem, nro.data_criacao, nro.visualizada,
                           n.numero as nota_numero, e.razao_social as empresa_nome, e.tipo_empresa,
                           u.nome as admin_nome
                    FROM notificacoes_resolucao_operador nro
                    JOIN auditoria_notas n ON n.id = nro.nota_id
                    JOIN empresas e ON e.id = n.empresa_id
                    JOIN usuarios u ON u.id = nro.admin_id
                    JOIN usuario_empresas ue ON ue.empresa_id = e.id
                    WHERE nro.operador_id = ? AND ue.usuario_id = ?
                    ORDER BY nro.data_criacao DESC LIMIT 50';
            $params = [$usuario_id, $usuario_id];
        } else { // Admin/Master - vê todas as resoluções
            $sql = 'SELECT nro.id, nro.mensagem, nro.data_criacao, nro.visualizada,
                           n.numero as nota_numero, e.razao_social as empresa_nome, e.tipo_empresa,
                           u.nome as admin_nome, op.nome as operador_nome
                    FROM notificacoes_resolucao_operador nro
                    JOIN auditoria_notas n ON n.id = nro.nota_id
                    JOIN empresas e ON e.id = n.empresa_id
                    JOIN usuarios u ON u.id = nro.admin_id
                    JOIN usuarios op ON op.id = nro.operador_id
                    ORDER BY nro.data_criacao DESC LIMIT 50';
            $params = [];
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $historico = $stmt->fetchAll();
        



        // Adiciona a tag Matriz/Filial ao nome da empresa
        foreach ($historico as &$h) {
            if (isset($h['tipo_empresa'])) {
                if (strtolower($h['tipo_empresa']) === 'matriz') {
                    $h['empresa_nome'] .= ' - Matriz';
                } elseif (strtolower($h['tipo_empresa']) === 'filial') {
                    $h['empresa_nome'] .= ' - Filial';
                }
            }
            
            // Converter visualizada para boolean
            $h['visualizada'] = (bool) $h['visualizada'];
        }

        header('Content-Type: application/json');
        echo json_encode(['historico' => $historico]);
    }

    // Endpoint para marcar todas as notificações de resolução como visualizadas
    public function marcarTodasComoVisualizadas() {
        if (!isset($_SESSION['usuario_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Usuário não autenticado']);
            return;
        }
        
        $usuario_id = $_SESSION['usuario_id'];
        $tipo_usuario = $_SESSION['tipo'] ?? null;
        
        // Só operadores podem marcar suas notificações como visualizadas
        // Aceitar tanto string "3" quanto número 3
        if ($tipo_usuario != 3 && $tipo_usuario !== '3' && $tipo_usuario !== 'operator') {
            http_response_code(403);
            echo json_encode(['error' => 'Acesso negado']);
            return;
        }
        
        $stmt = $this->db->prepare('UPDATE notificacoes_resolucao_operador SET visualizada = 1, data_visualizacao = NOW() WHERE operador_id = ? AND visualizada = 0');
        $stmt->execute([$usuario_id]);
        
        echo json_encode(['sucesso' => true, 'atualizadas' => $stmt->rowCount()]);
    }


} 