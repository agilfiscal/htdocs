<?php
namespace App\Models;

use PDO;

class Certificado {
    protected $db;
    public function __construct($db) {
        $this->db = $db;
    }

    public function all() {
        if (isset($_SESSION['usuario_id'])) {
            $db = $this->db;
            $stmt = $db->prepare('SELECT tipo FROM usuarios WHERE id = ?');
            $stmt->execute([$_SESSION['usuario_id']]);
            $tipo = $stmt->fetchColumn();
            
            if ($tipo === 'admin' || $tipo === 'master') {
                $stmt = $db->query('SELECT c.*, e.razao_social FROM certificados c JOIN empresas e ON c.empresa_id = e.id ORDER BY c.criado_em DESC');
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } else {
                $sql = 'SELECT c.*, e.razao_social 
                        FROM certificados c 
                        JOIN empresas e ON c.empresa_id = e.id
                        INNER JOIN usuario_empresas ue ON ue.empresa_id = e.id
                        WHERE ue.usuario_id = ?
                        ORDER BY c.criado_em DESC';
                $stmt = $db->prepare($sql);
                $stmt->execute([$_SESSION['usuario_id']]);
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        }
        // fallback
        $stmt = $this->db->query('SELECT c.*, e.razao_social FROM certificados c JOIN empresas e ON c.empresa_id = e.id ORDER BY c.criado_em DESC');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $sql = 'INSERT INTO certificados (empresa_id, arquivo, senha) VALUES (?, ?, ?)';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['empresa_id'],
            $data['arquivo'],
            $data['senha']
        ]);
    }

    public function find($id) {
        if (!is_numeric($id)) {
            return false;
        }
        
        $stmt = $this->db->prepare('SELECT c.*, e.razao_social 
                                   FROM certificados c 
                                   JOIN empresas e ON c.empresa_id = e.id 
                                   WHERE c.id = ?');
        $stmt->execute([(int)$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$result) {
            return false;
        }
        
        return $result;
    }

    public function delete($id) {
        $stmt = $this->db->prepare('DELETE FROM certificados WHERE id = ?');
        return $stmt->execute([$id]);
    }

    public function findByEmpresa($empresa_id) {
        $stmt = $this->db->prepare('SELECT * FROM certificados WHERE empresa_id = ? ORDER BY criado_em DESC LIMIT 1');
        $stmt->execute([$empresa_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
} 