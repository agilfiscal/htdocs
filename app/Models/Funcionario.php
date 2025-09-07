<?php

namespace App\Models;

class Funcionario extends Model
{
    protected $table = 'funcionarios';
    protected $fillable = ['profissao', 'salario', 'usuario_id'];

    /**
     * Buscar funcionários por usuário
     */
    public function getFuncionariosByUsuario($usuarioId)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE usuario_id = ? ORDER BY profissao ASC");
        $stmt->execute([$usuarioId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Buscar funcionário por ID e usuário
     */
    public function getFuncionarioById($id, $usuarioId)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = ? AND usuario_id = ?");
        $stmt->execute([$id, $usuarioId]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Calcular valor por hora baseado no salário mensal
     */
    public function calcularValorHora($funcionarioId, $usuarioId)
    {
        $funcionario = $this->getFuncionarioById($funcionarioId, $usuarioId);
        if ($funcionario) {
            // Considerando 160 horas por mês (média)
            return $funcionario['salario'] / 160;
        }
        return 0;
    }

    /**
     * Inserir novo funcionário
     */
    public function insert($data)
    {
        return $this->create($data);
    }
} 