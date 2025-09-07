<?php
namespace App\Models;

use PDO;

class AuditoriaNota {
    protected $db;
    public function __construct($db) {
        $this->db = $db;
    }

    public function inserir($data) {
        // Evita duplicidade pela chave e empresa
        $sql = 'INSERT IGNORE INTO auditoria_notas (empresa_id, numero, cnpj, razao_social, data_emissao, valor, chave, hash, uf, situacao, tipo, data_consulta) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())';
        $hash = $data['numero'] && $data['valor'] ? trim($data['numero'] . '-' . $data['valor']) : null;
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['empresa_id'],
            $data['numero'],
            $data['cnpj'],
            $data['razao_social'],
            $data['data_emissao'],
            $data['valor'],
            $data['chave'],
            $hash,
            $data['uf'],
            $data['situacao'],
            $data['tipo']
        ]);
    }

    public function historico($empresa_id, $dias = 1875, $data_inicial = null, $data_final = null, $busca = null) {
        $sql = 'SELECT * FROM auditoria_notas WHERE empresa_id = ? AND data_consulta >= DATE_SUB(NOW(), INTERVAL ? DAY)';
        $params = [$empresa_id, $dias];
        if ($data_inicial) {
            $sql .= ' AND data_emissao >= ?';
            $params[] = $data_inicial . ' 00:00:00';
        }
        if ($data_final) {
            $sql .= ' AND data_emissao <= ?';
            $params[] = $data_final . ' 23:59:59';
        }
        if ($busca) {
            $sql .= ' AND (
                numero LIKE ? OR
                chave LIKE ? OR
                cnpj LIKE ? OR
                valor LIKE ?
            )';
            $params[] = "%$busca%";
            $params[] = "%$busca%";
            $params[] = "%$busca%";
            $params[] = "%$busca%";
        }
        $sql .= ' ORDER BY data_emissao DESC, id DESC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUltimaConsulta($empresa_id) {
        $sql = 'SELECT ultima_consulta FROM auditoria_consultas WHERE empresa_id = ?';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$empresa_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['ultima_consulta'] : null;
    }

    public function setUltimaConsulta($empresa_id) {
        $sql = 'INSERT INTO auditoria_consultas (empresa_id, ultima_consulta) VALUES (?, NOW()) ON DUPLICATE KEY UPDATE ultima_consulta = NOW()';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$empresa_id]);
    }

    public function getQuantidadeLinhasInseridas($empresa_id) {
        $sql = 'SELECT COUNT(*) as total FROM auditoria_notas WHERE empresa_id = ?';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$empresa_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['total'] : 0;
    }

    public function forcarConsulta($empresa_id) {
        try {
            // Aqui você deve implementar a lógica real de consulta à SEFAZ
            // Por enquanto, vamos apenas atualizar a data da última consulta
            $this->setUltimaConsulta($empresa_id);
            return true;
        } catch (\Exception $e) {
            error_log('Erro ao forçar consulta: ' . $e->getMessage());
            return false;
        }
    }

    public function getDestinoEVencimentos($nota_id) {
        // Buscar destino
        $stmt = $this->db->prepare('SELECT destino_id FROM auditoria_destino WHERE nota_id = ?');
        $stmt->execute([$nota_id]);
        $destino_id = $stmt->fetchColumn();
        // Buscar vencimentos
        $stmt = $this->db->prepare('SELECT data_vencimento FROM auditoria_notas_vencimentos WHERE nota_id = ? ORDER BY data_vencimento');
        $stmt->execute([$nota_id]);
        $vencimentos = $stmt->fetchAll(\PDO::FETCH_COLUMN);
        // Formatar datas para yyyy-mm-dd (caso venham com hora)
        $vencimentos = array_map(function($d) {
            if (strpos($d, ' ') !== false) return substr($d, 0, 10);
            return $d;
        }, $vencimentos);
        return [
            'destino_id' => $destino_id,
            'vencimentos' => $vencimentos
        ];
    }

    public function historicoComDestino($empresa_id, $dias = 1875, $data_inicial = null, $data_final = null, $busca = '', $pagina = 1, $por_pagina = 15, $status = '') {
        $where = 'WHERE empresa_id = ?';
        $params = [$empresa_id];
        if ($data_inicial) {
            $where .= ' AND data_emissao >= ?';
            $params[] = $data_inicial;
        }
        if ($data_final) {
            $where .= ' AND data_emissao <= ?';
            $params[] = $data_final;
        }
        if ($busca) {
            $where .= ' AND (numero LIKE ? OR chave LIKE ? OR valor LIKE ? OR cnpj LIKE ?)';
            $params[] = "%$busca%";
            $params[] = "%$busca%";
            $params[] = "%$busca%";
            $params[] = "%$busca%";
        }
        // Filtro por status_lancamento
        if ($status && $status !== 'todos') {
            if ($status === 'Cancelada') {
                $where .= ' AND situacao = ?';
                $params[] = $status;
            }
            // Para outros status, o filtro será feito no PHP, pois status_lancamento não existe no banco
        }
        // Contar total
        $sqlCount = "SELECT COUNT(*) FROM auditoria_notas $where";
        $stmtCount = $this->db->prepare($sqlCount);
        $stmtCount->execute($params);
        $total = $stmtCount->fetchColumn();
        // Paginação
        $offset = ($pagina - 1) * $por_pagina;
        $sql = "SELECT * FROM auditoria_notas $where ORDER BY data_emissao DESC LIMIT ? OFFSET ?";
        $stmt = $this->db->prepare($sql);
        $bindParams = $params;
        $stmt->bindValue(count($bindParams) + 1, (int)$por_pagina, \PDO::PARAM_INT);
        $stmt->bindValue(count($bindParams) + 2, (int)$offset, \PDO::PARAM_INT);
        foreach ($bindParams as $i => $param) {
            $stmt->bindValue($i + 1, $param);
        }
        $stmt->execute();
        $notas = $stmt->fetchAll();
        $total_paginas = ceil($total / $por_pagina);
        return [
            'notas' => $notas,
            'total' => $total,
            'total_paginas' => $total_paginas
        ];
    }

    /**
     * Verifica se já existe uma nota com a chave informada
     */
    public function existeChave($chave) {
        $sql = 'SELECT id FROM auditoria_notas WHERE chave = ? LIMIT 1';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$chave]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ? true : false;
    }

    /**
     * Atualiza os dados da nota pela chave
     */
    public function atualizarPorChave($chave, $dados) {
        $sql = 'UPDATE auditoria_notas SET empresa_id = ?, numero = ?, cnpj = ?, razao_social = ?, data_emissao = ?, valor = ?, uf = ?, situacao = ?, tipo = ?, hash = ?, data_consulta = NOW() WHERE chave = ?';
        $hash = $dados['numero'] && $dados['valor'] ? trim($dados['numero'] . '-' . $dados['valor']) : null;
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute([
            $dados['empresa_id'],
            $dados['numero'],
            $dados['cnpj'],
            $dados['razao_social'],
            $dados['data_emissao'],
            $dados['valor'],
            $dados['uf'],
            $dados['situacao'],
            $dados['tipo'],
            $hash,
            $chave
        ]);

        if ($result) {
            $nota_id = $this->getIdPorChave($chave);
            if ($nota_id) {
                // Atualizar tributos
                $tributos = [
                    'valor_icms' => $dados['valor_icms'] ?? null,
                    'valor_pis' => $dados['valor_pis'] ?? null,
                    'valor_cofins' => $dados['valor_cofins'] ?? null,
                    'valor_ipi' => $dados['valor_ipi'] ?? null,
                    'valor_fcp' => $dados['valor_fcp'] ?? null
                ];
                $this->atualizarTributos($nota_id, $tributos);

                // Atualizar itens
                $quantidade_itens = $dados['quantidade_itens'] ?? 0;
                $this->atualizarItens($nota_id, $quantidade_itens);
            }
        }

        return $result;
    }

    public function atualizarTributos($nota_id, $tributos) {
        // Primeiro, remove os tributos existentes
        $sql = 'DELETE FROM auditoria_tributos WHERE nota_id = ?';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$nota_id]);

        // Depois, insere os novos tributos
        return $this->inserirTributos($nota_id, $tributos);
    }

    public function atualizarItens($nota_id, $quantidade_itens) {
        // Primeiro, remove os itens existentes
        $sql = 'DELETE FROM auditoria_itens WHERE nota_id = ?';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$nota_id]);

        // Depois, insere os novos itens
        return $this->inserirItens($nota_id, $quantidade_itens);
    }

    public function getIdPorChave($chave) {
        $sql = 'SELECT id FROM auditoria_notas WHERE chave = ? LIMIT 1';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$chave]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['id'] : null;
    }

    public function inserirTributos($nota_id, $tributos) {
        $sql = 'INSERT INTO auditoria_tributos (nota_id, valor_icms, valor_pis, valor_cofins, valor_ipi, valor_fcp) VALUES (?, ?, ?, ?, ?, ?)';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $nota_id,
            $tributos['valor_icms'] ?? null,
            $tributos['valor_pis'] ?? null,
            $tributos['valor_cofins'] ?? null,
            $tributos['valor_ipi'] ?? null,
            $tributos['valor_fcp'] ?? null
        ]);
    }

    public function inserirItens($nota_id, $quantidade_itens) {
        $sql = 'INSERT INTO auditoria_itens (nota_id, quantidade_itens) VALUES (?, ?)';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$nota_id, $quantidade_itens]);
    }
} 