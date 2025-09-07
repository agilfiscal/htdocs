<?php

namespace App\Models;

class Insumo extends Model
{
    protected $table = 'insumos';
    protected $fillable = ['insumo', 'medida', 'custo', 'usuario_id'];

    /**
     * Buscar insumos por usuário
     */
    public function getInsumosByUsuario($usuarioId)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE usuario_id = ? ORDER BY insumo ASC");
        $stmt->execute([$usuarioId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Buscar insumo por ID e usuário
     */
    public function getInsumoById($id, $usuarioId)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = ? AND usuario_id = ?");
        $stmt->execute([$id, $usuarioId]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Calcular custo total baseado na quantidade
     */
    public function calcularCusto($insumoId, $quantidade, $usuarioId)
    {
        $insumo = $this->getInsumoById($insumoId, $usuarioId);
        if ($insumo) {
            return $insumo['custo'] * $quantidade;
        }
        return 0;
    }

    /**
     * Inserir novo insumo
     */
    public function insert($data)
    {
        return $this->create($data);
    }
} 