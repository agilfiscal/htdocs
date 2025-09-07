<?php
namespace App\Models;

use PDO;

class Usuario {
    protected $db;
    public function __construct($db) {
        $this->db = $db;
    }

    public function allWithEmpresas($limit = null, $offset = null) {
        if (isset($_SESSION['usuario_id'])) {
            $db = $this->db;
            $stmt = $db->prepare('SELECT tipo FROM usuarios WHERE id = ?');
            $stmt->execute([$_SESSION['usuario_id']]);
            $tipo = $stmt->fetchColumn();
            if ($tipo === 'admin' || $tipo === 'master') {
                $sql = 'SELECT u.*, GROUP_CONCAT(e.razao_social SEPARATOR ", ") as empresas
                        FROM usuarios u
                        LEFT JOIN usuario_empresas ue ON ue.usuario_id = u.id
                        LEFT JOIN empresas e ON e.id = ue.empresa_id
                        GROUP BY u.id';
                if ($limit !== null && $offset !== null) {
                    $sql .= ' LIMIT :limit OFFSET :offset';
                    $stmt = $db->prepare($sql);
                    $stmt->bindValue(':limit', (int)$limit, \PDO::PARAM_INT);
                    $stmt->bindValue(':offset', (int)$offset, \PDO::PARAM_INT);
                    $stmt->execute();
                } else {
                    $stmt = $db->query($sql);
                }
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } else {
                $sql = 'SELECT u.*, GROUP_CONCAT(e.razao_social SEPARATOR ", ") as empresas
                        FROM usuarios u
                        LEFT JOIN usuario_empresas ue ON ue.usuario_id = u.id
                        LEFT JOIN empresas e ON e.id = ue.empresa_id
                        WHERE u.id = ?
                        GROUP BY u.id';
                $stmt = $db->prepare($sql);
                $stmt->execute([$_SESSION['usuario_id']]);
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        }
        // fallback
        $sql = 'SELECT u.*, GROUP_CONCAT(e.razao_social SEPARATOR ", ") as empresas
                FROM usuarios u
                LEFT JOIN usuario_empresas ue ON ue.usuario_id = u.id
                LEFT JOIN empresas e ON e.id = ue.empresa_id
                GROUP BY u.id';
        if ($limit !== null && $offset !== null) {
            $stmt = $this->db->prepare($sql . ' LIMIT :limit OFFSET :offset');
            $stmt->bindValue(':limit', (int)$limit, \PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$offset, \PDO::PARAM_INT);
            $stmt->execute();
        } else {
            $stmt = $this->db->query($sql);
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update($id, $dados) {
        try {
            $campos = [];
            $params = [];
            foreach ($dados as $campo => $valor) {
                $campos[] = "`$campo` = ?";
                $params[] = $valor;
            }
            $params[] = $id;
            
            $sql = 'UPDATE `usuarios` SET ' . implode(', ', $campos) . ' WHERE `id` = ?';
            
            // Log para debug
            error_log("SQL Update: " . $sql);
            error_log("Parâmetros: " . print_r($params, true));
            
            $stmt = $this->db->prepare($sql);
            if (!$stmt) {
                throw new \PDOException("Erro ao preparar a query: " . implode(" ", $this->db->errorInfo()));
            }
            
            $resultado = $stmt->execute($params);
            if (!$resultado) {
                throw new \PDOException("Erro ao executar a query: " . implode(" ", $stmt->errorInfo()));
            }
            
            // Log do resultado
            error_log("Linhas afetadas: " . $stmt->rowCount());
            
            return $stmt->rowCount() > 0;
        } catch (\PDOException $e) {
            error_log("Erro no update: " . $e->getMessage());
            throw $e;
        }
    }

    public function updateSenha($id, $novaSenha) {
        $hash = password_hash($novaSenha, PASSWORD_DEFAULT);
        $sql = 'UPDATE usuarios SET password = ? WHERE id = ?';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$hash, $id]);
    }

    public function findByEmail($email) {
        $stmt = $this->db->prepare('SELECT * FROM usuarios WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($dados) {
        $sql = 'INSERT INTO usuarios (nome, email, telefone, password, tipo, ativo) VALUES (?, ?, ?, ?, ?, ?)';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $dados['nome'],
            $dados['email'],
            $dados['telefone'],
            $dados['password'],
            $dados['tipo'],
            $dados['ativo'] ?? 1
        ]);
        return $this->db->lastInsertId();
    }
} 