<?php

namespace App\Controllers;

class ComentarioController {
    private $db;

    public function __construct() {
        require_once __DIR__ . '/../../config/config.php';
        $this->db = new \PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
        $this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    // Endpoint para buscar notificações de comentários
    public function getNotificacoesComentarios() {
        if (!isset($_SESSION['usuario_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Usuário não autenticado']);
            return;
        }
        
        $usuario_id = $_SESSION['usuario_id'];
        $tipo_usuario = $_SESSION['tipo'] ?? null;
        
        // Buscar notificações de comentários (últimos 10, incluindo visualizadas)
        $sql = 'SELECT nc.id, nc.observacao_id, nc.nota_id, nc.usuario_origem_id, nc.data_criacao, nc.visualizada,
                       on_obs.observacao, on_obs.data_hora as data_observacao,
                       n.numero as nota_numero, e.razao_social as empresa_nome, e.tipo_empresa,
                       u_origem.nome as usuario_origem_nome, u_origem.tipo as usuario_origem_tipo
                FROM notificacoes_comentarios nc
                JOIN observacoes_nota on_obs ON on_obs.id = nc.observacao_id
                JOIN auditoria_notas n ON n.id = nc.nota_id
                JOIN empresas e ON e.id = n.empresa_id
                JOIN usuarios u_origem ON u_origem.id = nc.usuario_origem_id
                WHERE nc.usuario_destino_id = ?
                ORDER BY nc.data_criacao DESC
                LIMIT 10';
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$usuario_id]);
        $notificacoes = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
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
        echo json_encode(['comentarios' => $notificacoes]);
    }

    // Endpoint para marcar comentário como visualizado
    public function marcarComentarioVisualizado($params) {
        if (!isset($_SESSION['usuario_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Usuário não autenticado']);
            return;
        }
        
        $usuario_id = $_SESSION['usuario_id'];
        $comentario_id = $params['id'] ?? ($_POST['id'] ?? null);
        
        if (!$comentario_id) {
            http_response_code(400);
            echo json_encode(['error' => 'ID do comentário não informado']);
            return;
        }
        
        $stmt = $this->db->prepare('UPDATE notificacoes_comentarios SET visualizada = 1, data_visualizacao = NOW() WHERE id = ? AND usuario_destino_id = ?');
        $stmt->execute([$comentario_id, $usuario_id]);
        
        echo json_encode(['sucesso' => true]);
    }

    // Endpoint para marcar todos os comentários como visualizados
    public function marcarTodosComentariosVisualizados() {
        if (!isset($_SESSION['usuario_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Usuário não autenticado']);
            return;
        }
        
        $usuario_id = $_SESSION['usuario_id'];
        
        $stmt = $this->db->prepare('UPDATE notificacoes_comentarios SET visualizada = 1, data_visualizacao = NOW() WHERE usuario_destino_id = ? AND visualizada = 0');
        $stmt->execute([$usuario_id]);
        
        echo json_encode(['sucesso' => true, 'atualizadas' => $stmt->rowCount()]);
    }

    // Método para criar notificação quando uma observação é inserida
    public static function criarNotificacaoComentario($observacao_id, $nota_id, $usuario_origem_id, $usuario_destino_id) {
        try {
            require_once __DIR__ . '/../../config/config.php';
            $db = new \PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
            $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            
            $sql = 'INSERT INTO notificacoes_comentarios (observacao_id, nota_id, usuario_origem_id, usuario_destino_id) VALUES (?, ?, ?, ?)';
            $stmt = $db->prepare($sql);
            $stmt->execute([$observacao_id, $nota_id, $usuario_origem_id, $usuario_destino_id]);
            
            return true;
        } catch (Exception $e) {
            error_log("Erro ao criar notificação de comentário: " . $e->getMessage());
            return false;
        }
    }
}
?>
