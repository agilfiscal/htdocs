<?php

namespace App\Models;

class Embalagem extends Model
{
    protected $table = 'embalagens';
    protected $fillable = ['embalagem', 'quantidade', 'volume', 'custo', 'usuario_id'];

    /**
     * Buscar embalagens por usuário
     */
    public function getEmbalagensByUsuario($usuarioId)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE usuario_id = ? ORDER BY embalagem ASC");
        $stmt->execute([$usuarioId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Buscar embalagem por ID e usuário
     */
    public function getEmbalagemById($id, $usuarioId)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = ? AND usuario_id = ?");
        $stmt->execute([$id, $usuarioId]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Calcular custo total baseado na quantidade
     */
    public function calcularCusto($embalagemId, $quantidade, $usuarioId)
    {
        $embalagem = $this->getEmbalagemById($embalagemId, $usuarioId);
        if ($embalagem) {
            return $embalagem['custo'] * $quantidade;
        }
        return 0;
    }

    /**
     * Inserir nova embalagem
     */
    public function insert($data)
    {
        return $this->create($data);
    }
} 