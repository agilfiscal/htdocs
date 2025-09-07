<?php
namespace App\Models;

use PDO;

class Empresa {
    protected $db;
    public function __construct($db) {
        $this->db = $db;
    }

    public function all() {
        // Se for admin ou master, retorna todas as empresas
        if (isset($_SESSION['usuario_id'])) {
            $db = $this->db;
            $stmt = $db->prepare('SELECT tipo FROM usuarios WHERE id = ?');
            $stmt->execute([$_SESSION['usuario_id']]);
            $tipo = $stmt->fetchColumn();
            if ($tipo === 'admin' || $tipo === 'master') {
                $stmt = $db->query('SELECT * FROM empresas ORDER BY criado_em DESC');
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } else {
                $sql = 'SELECT e.* FROM empresas e
                        INNER JOIN usuario_empresas ue ON ue.empresa_id = e.id
                        WHERE ue.usuario_id = ?
                        ORDER BY e.criado_em DESC';
                $stmt = $db->prepare($sql);
                $stmt->execute([$_SESSION['usuario_id']]);
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        }
        // fallback
        $stmt = $this->db->query('SELECT * FROM empresas ORDER BY criado_em DESC');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find($id) {
        $sql = 'SELECT id, cnpj, razao_social, uf, cidade, inscricao_estadual, telefone, email, status, regime, representante_legal, certificado_path, certificado_senha, ativo, criado_em, atualizado_em FROM empresas WHERE id = ?';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findByCnpj($cnpj) {
        $stmt = $this->db->prepare('SELECT * FROM empresas WHERE cnpj = ?');
        $stmt->execute([$cnpj]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($dados) {
        $sql = 'INSERT INTO empresas (cnpj, razao_social, uf, cidade, inscricao_estadual, telefone, email, status, regime, representante_legal, ativo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $dados['cnpj'] ?? '',
            $dados['razao_social'] ?? '',
            $dados['uf'] ?? '',
            $dados['cidade'] ?? '',
            $dados['inscricao_estadual'] ?? '',
            $dados['telefone'] ?? '',
            $dados['email'] ?? '',
            $dados['status'] ?? '',
            $dados['regime'] ?? '',
            $dados['representante_legal'] ?? '',
            $dados['ativo'] ?? 1
        ]);
        return $this->db->lastInsertId();
    }

    public function update($id, $data) {
        // Busca os dados atuais
        $empresaAtual = $this->find($id);
        if (!$empresaAtual) {
            return false;
        }

        // Mescla os dados novos com os antigos
        $dados = array_merge($empresaAtual, $data);

        // Se for matriz, garante que empresa_matriz_id seja NULL
        if ($dados['tipo_empresa'] === 'matriz') {
            $dados['empresa_matriz_id'] = null;
        }

        // Se for filial, garante que tenha uma matriz
        if ($dados['tipo_empresa'] === 'filial' && empty($dados['empresa_matriz_id'])) {
            return false;
        }

        // Se for independente, garante que empresa_matriz_id seja NULL
        if ($dados['tipo_empresa'] === 'independente') {
            $dados['empresa_matriz_id'] = null;
        }

        $sql = 'UPDATE empresas SET 
            razao_social=?, 
            uf=?, 
            cidade=?, 
            inscricao_estadual=?, 
            telefone=?, 
            email=?, 
            status=?, 
            regime=?, 
            representante_legal=?, 
            tipo_empresa=?,
            empresa_matriz_id=?,
            ativo=?, 
            certificado_path=?, 
            certificado_senha=? 
            WHERE id=?';
            
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $dados['razao_social'],
            $dados['uf'],
            $dados['cidade'],
            $dados['inscricao_estadual'],
            $dados['telefone'],
            $dados['email'],
            $dados['status'],
            $dados['regime'],
            $dados['representante_legal'],
            $dados['tipo_empresa'],
            $dados['empresa_matriz_id'],
            $dados['ativo'],
            $dados['certificado_path'] ?? null,
            $dados['certificado_senha'] ?? null,
            $id
        ]);
    }

    public function toggleAtivo($id, $ativo) {
        $stmt = $this->db->prepare('UPDATE empresas SET ativo=? WHERE id=?');
        return $stmt->execute([$ativo, $id]);
    }

    /**
     * Verifica se a empresa é independente
     */
    public static function isIndependente($empresa) {
        return isset($empresa['tipo_empresa']) && $empresa['tipo_empresa'] === 'independente';
    }

    /**
     * Verifica se a empresa é matriz
     */
    public static function isMatriz($empresa) {
        return isset($empresa['tipo_empresa']) && $empresa['tipo_empresa'] === 'matriz';
    }

    /**
     * Verifica se a empresa é filial
     */
    public static function isFilial($empresa) {
        return isset($empresa['tipo_empresa']) && $empresa['tipo_empresa'] === 'filial';
    }

    /**
     * Verifica se a empresa faz parte de um grupo (matriz/filial)
     */
    public static function isGrupo($empresa) {
        return (isset($empresa['tipo_empresa']) && ($empresa['tipo_empresa'] === 'matriz' || $empresa['tipo_empresa'] === 'filial'));
    }
} 