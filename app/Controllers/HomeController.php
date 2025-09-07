<?php
namespace App\Controllers;

use PDO;

class HomeController {
    public function index() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: /login');
            exit;
        }
        $db = new PDO(
            'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]
        );
        // Filtros
        $empresaIds = isset($_GET['empresa_ids']) && is_array($_GET['empresa_ids']) ? $_GET['empresa_ids'] : (isset($_GET['empresa_ids']) ? [$_GET['empresa_ids']] : []);
        $data_inicial = isset($_GET['data_inicial']) && $_GET['data_inicial'] ? $_GET['data_inicial'] : date('Y-01-01');
        $data_final = isset($_GET['data_final']) && $_GET['data_final'] ? $_GET['data_final'] : date('Y-m-d');



        // Empresas para o filtro
        require_once APP_ROOT . '/app/Models/Empresa.php';
        $empresaModel = new \App\Models\Empresa($db);
        $empresas = $empresaModel->all();

        // Se não houver empresas selecionadas via GET, usar as empresas que o usuário está atendendo
        if (empty($empresaIds) && isset($_SESSION['usuario_id'])) {
            // Buscar empresas que o usuário está atendendo
            $empresasAtendidasIds = [];
            if (!empty($_SESSION['empresas_atendidas_ids']) && is_array($_SESSION['empresas_atendidas_ids'])) {
                $empresasAtendidasIds = $_SESSION['empresas_atendidas_ids'];
            } elseif (!empty($_SESSION['empresa_atendida_id'])) {
                $empresasAtendidasIds = [$_SESSION['empresa_atendida_id']];
            }
            
            // Se não tem empresas atendidas definidas, buscar empresas vinculadas ao usuário
            if (empty($empresasAtendidasIds)) {
                $stmt = $db->prepare('SELECT empresa_id FROM usuario_empresas WHERE usuario_id = ?');
                $stmt->execute([$_SESSION['usuario_id']]);
                $empresasAtendidasIds = array_column($stmt->fetchAll(), 'empresa_id');
            }
            
            // Se ainda não tem empresas atendidas, verificar se é admin/master para usar todas as empresas
            if (empty($empresasAtendidasIds)) {
                $stmt = $db->prepare('SELECT tipo FROM usuarios WHERE id = ?');
                $stmt->execute([$_SESSION['usuario_id']]);
                $tipo = $stmt->fetchColumn();
                
                if ($tipo === 'admin' || $tipo === 'master') {
                    $todasEmpresasIds = array_column($empresas, 'id');
                    sort($todasEmpresasIds, SORT_NUMERIC);
                    $empresasAtendidasIds = $todasEmpresasIds;
                }
            }
            
            // Usar as empresas atendidas e selecionar a de menor ID
            if (!empty($empresasAtendidasIds)) {
                sort($empresasAtendidasIds, SORT_NUMERIC);
                $empresaIds = [$empresasAtendidasIds[0]]; // Seleciona a menor ID entre as atendidas
            }
        }
        
        // Monta o WHERE dos filtros
        $where = 'WHERE 1=1';
        $params = [];
        if ($empresaIds && count($empresaIds) > 0) {
            $in = implode(',', array_fill(0, count($empresaIds), '?'));
            $where .= " AND an.empresa_id IN ($in)";
            $params = array_merge($params, $empresaIds);
        }
        if ($data_inicial) {
            $where .= ' AND an.data_emissao >= ?';
            $params[] = $data_inicial . ' 00:00:00';
        }
        if ($data_final) {
            $where .= ' AND an.data_emissao <= ?';
            $params[] = $data_final . ' 23:59:59';
        }

        // Indicadores filtrados
        $totalNotas = $db->prepare('SELECT COUNT(*) as total FROM auditoria_notas an ' . $where);
        $totalNotas->execute($params);
        $totalNotas = $totalNotas->fetch()['total'];

        // Notas lançadas
        $whereLancadas = "WHERE r.chave IS NULL";
        if ($empresaIds && count($empresaIds) > 0) {
            $in = implode(',', array_fill(0, count($empresaIds), '?'));
            $whereLancadas .= " AND an.empresa_id IN ($in)";
            $paramsLancadas = $empresaIds;
        }
        if ($data_inicial) {
            $whereLancadas .= ' AND an.data_emissao >= ?';
            $paramsLancadas[] = $data_inicial . ' 00:00:00';
        }
        if ($data_final) {
            $whereLancadas .= ' AND an.data_emissao <= ?';
            $paramsLancadas[] = $data_final . ' 23:59:59';
        }

        $totalLancadas = $db->prepare("
            SELECT COUNT(*) as total 
            FROM auditoria_notas an 
            INNER JOIN notas n ON n.hash = an.hash AND n.empresa_id = an.empresa_id 
            LEFT JOIN romaneio r ON r.chave COLLATE utf8mb4_unicode_ci = an.chave COLLATE utf8mb4_unicode_ci AND r.empresa_id = an.empresa_id 
            " . $whereLancadas
        );
        $totalLancadas->execute($paramsLancadas);
        $totalLancadas = $totalLancadas->fetch()['total'];

        $valorLancadas = $db->prepare("
            SELECT SUM(an.valor) as total 
            FROM auditoria_notas an 
            INNER JOIN notas n ON n.hash = an.hash AND n.empresa_id = an.empresa_id 
            LEFT JOIN romaneio r ON r.chave COLLATE utf8mb4_unicode_ci = an.chave COLLATE utf8mb4_unicode_ci AND r.empresa_id = an.empresa_id 
            " . $whereLancadas
        );
        $valorLancadas->execute($paramsLancadas);
        $valorLancadas = $valorLancadas->fetch()['total'] ?? 0;

        // --- NOVA LÓGICA PARA NOTAS PENDENTES (igual à auditoria) ---
        // Buscar todas as notas relevantes
        $sqlNotas = "SELECT an.*, n.hash as nota_hash, n.empresa_id as nota_empresa_id, d.chave as desconhecimento_chave, r.chave as romaneio_chave, e.cnpj as empresa_cnpj, e.tipo_empresa, e.id as empresa_id_ref
            FROM auditoria_notas an
            LEFT JOIN notas n ON n.hash = an.hash AND n.empresa_id = an.empresa_id
            LEFT JOIN desconhecimento d ON d.chave COLLATE utf8mb4_unicode_ci = an.chave COLLATE utf8mb4_unicode_ci AND d.empresa_id = an.empresa_id
            LEFT JOIN romaneio r ON r.chave COLLATE utf8mb4_unicode_ci = an.chave COLLATE utf8mb4_unicode_ci AND r.empresa_id = an.empresa_id
            LEFT JOIN empresas e ON e.cnpj COLLATE utf8mb4_unicode_ci = an.cnpj COLLATE utf8mb4_unicode_ci
            " . $where;
        $stmtNotas = $db->prepare($sqlNotas);
        $stmtNotas->execute($params);
        $todasNotas = $stmtNotas->fetchAll();

        // Buscar CNPJs das empresas selecionadas (para "Própria")
        $empresaCnpjs = [];
        if (!empty($empresaIds)) {
            foreach ($empresas as $emp) {
                if (in_array($emp['id'], $empresaIds)) {
                    $empresaCnpjs[] = preg_replace('/[^0-9]/', '', $emp['cnpj']);
                }
            }
        }

        // --- MARCAÇÃO DE STATUS COM PRIORIDADE (igual à auditoria) ---
        // Buscar empresa selecionada
        $empresaSelecionadaObj = null;
        if (!empty($empresaIds)) {
            foreach ($empresas as $emp) {
                if ($emp['id'] == $empresaIds[0]) {
                    $empresaSelecionadaObj = $emp;
                    break;
                }
            }
        }
        foreach ($todasNotas as &$nota) {
            $nota['status_lancamento'] = 'Pendente';
            $notaCnpjLimpo = isset($nota['cnpj']) ? preg_replace('/[^0-9]/', '', $nota['cnpj']) : '';
            $empresaCnpjLimpo = null;
            if (!empty($empresaCnpjs) && $notaCnpjLimpo) {
                foreach ($empresaCnpjs as $cnpj) {
                    if ($cnpj === $notaCnpjLimpo) {
                        $empresaCnpjLimpo = $cnpj;
                        break;
                    }
                }
            }
            // Cancelada tem prioridade máxima
            if (isset($nota['situacao']) && mb_strtolower($nota['situacao']) === 'cancelada') {
                $nota['status_lancamento'] = 'Cancelada';
                continue;
            }
            if ($empresaCnpjLimpo && $notaCnpjLimpo && $empresaCnpjLimpo === $notaCnpjLimpo) {
                $nota['status_lancamento'] = 'Própria';
                continue;
            }
            if (!empty($nota['romaneio_chave'])) {
                $nota['status_lancamento'] = 'Romaneio';
                continue;
            }
            if (!empty($nota['nota_hash'])) {
                $nota['status_lancamento'] = 'Lançada';
                continue;
            }
            if (!empty($nota['desconhecimento_chave'])) {
                $nota['status_lancamento'] = 'Desconhecimento';
                continue;
            }
            // Transferência (grupo de empresas)
            if ($empresaSelecionadaObj && \App\Models\Empresa::isGrupo($empresaSelecionadaObj) && !empty($empresaIds) && count($empresas) > 1) {
                $empresaSelecionada = null;
                $grupoCnpjs = [];
                foreach ($empresas as $emp) {
                    $cnpjLimpo = preg_replace('/[^0-9]/', '', $emp['cnpj']);
                    if ($emp['id'] == $empresaIds[0]) {
                        $empresaSelecionada = $cnpjLimpo;
                    }
                    $grupoCnpjs[] = $cnpjLimpo;
                }
                if ($empresaSelecionada && count($grupoCnpjs) > 1 && in_array($notaCnpjLimpo, $grupoCnpjs) && $notaCnpjLimpo !== $empresaSelecionada) {
                    $nota['status_lancamento'] = 'Transferência';
                    continue;
                }
            }
        }
        unset($nota);
        // --- MARCAÇÃO DE ANULADA (apenas se ainda for pendente) ---
        $notasPendentesSaida = [];
        $notasEntrada = [];
        foreach ($todasNotas as $idx => $nota) {
            if (
                $nota['status_lancamento'] === 'Pendente' &&
                (empty($nota['tipo']) || mb_strtolower($nota['tipo']) !== 'entrada')
            ) {
                $notasPendentesSaida[$idx] = $nota;
            } elseif (
                isset($nota['tipo']) && mb_strtolower($nota['tipo']) === 'entrada' &&
                $nota['status_lancamento'] === 'Pendente'
            ) {
                $notasEntrada[$idx] = $nota;
            }
        }
        $anuladasIdx = [];
        foreach ($notasEntrada as $idxEntrada => $entrada) {
            $fornecedorEntrada = preg_replace('/[^0-9]/', '', $entrada['cnpj'] ?? '');
            $valorEntrada = floatval(str_replace([',', 'R$', ' '], ['.', '', ''], $entrada['valor'] ?? 0));
            $dataEntrada = isset($entrada['data_emissao']) ? strtotime($entrada['data_emissao']) : null;
            $candidatas = [];
            foreach ($notasPendentesSaida as $idxSaida => $saida) {
                $fornecedorSaida = preg_replace('/[^0-9]/', '', $saida['cnpj'] ?? '');
                $valorSaida = floatval(str_replace([',', 'R$', ' '], ['.', '', ''], $saida['valor'] ?? 0));
                $dataSaida = isset($saida['data_emissao']) ? strtotime($saida['data_emissao']) : null;
                if (
                    $fornecedorEntrada === $fornecedorSaida &&
                    abs($valorEntrada - $valorSaida) < 0.01 &&
                    $dataSaida !== null && $dataEntrada !== null &&
                    $dataSaida <= $dataEntrada
                ) {
                    $candidatas[$idxSaida] = [
                        'diff' => abs($dataEntrada - $dataSaida),
                        'data' => $dataSaida
                    ];
                }
            }
            if (!empty($candidatas)) {
                uasort($candidatas, function($a, $b) {
                    if ($a['diff'] === $b['diff']) return $b['data'] <=> $a['data'];
                    return $a['diff'] <=> $b['diff'];
                });
                $idxAnulada = array_key_first($candidatas);
                $anuladasIdx[] = $idxAnulada;
                unset($notasPendentesSaida[$idxAnulada]);
            }
        }
        foreach ($anuladasIdx as $idx) {
            $todasNotas[$idx]['status_lancamento'] = 'Anulada';
        }
        // --- MARCAÇÃO DE RETORNADA (apenas se ainda for pendente) ---
        foreach ($todasNotas as &$nota) {
            if (
                isset($nota['tipo']) && mb_strtolower($nota['tipo']) === 'entrada' &&
                $nota['status_lancamento'] === 'Pendente'
            ) {
                $nota['status_lancamento'] = 'Retornada';
            }
        }
        unset($nota);
        // --- NOVO FILTRO DE PENDENTES ---
        $totalPendentes = 0;
        $valorPendentes = 0;
        foreach ($todasNotas as $nota) {
            $status = $nota['status_lancamento'] ?? 'Pendente';
            if (
                $status === 'Pendente'
            ) {
                $totalPendentes++;
                $valorPendentes += floatval($nota['valor']);
            }
        }

        $totalCanceladas = $db->prepare("SELECT COUNT(*) as total FROM auditoria_notas an " . $where . " AND an.situacao = 'Cancelada'");
        $totalCanceladas->execute($params);
        $totalCanceladas = $totalCanceladas->fetch()['total'];

        // --- NOVA LÓGICA PARA NOTAS DESCONHECIDAS (igual à auditoria: status_lancamento == 'Desconhecimento') ---
        $totalDesconhecidas = 0;
        $valorDesconhecidas = 0;
        foreach ($todasNotas as $nota) {
            // Calcular status_lancamento igual à auditoria
            $status_lancamento = 'Pendente';
            $notaCnpjLimpo = isset($nota['cnpj']) ? preg_replace('/[^0-9]/', '', $nota['cnpj']) : '';
            $empresaCnpjLimpo = null;
            if (!empty($empresaCnpjs) && $notaCnpjLimpo) {
                foreach ($empresaCnpjs as $cnpj) {
                    if ($cnpj === $notaCnpjLimpo) {
                        $empresaCnpjLimpo = $cnpj;
                        break;
                    }
                }
            }
            if (isset($nota['situacao']) && mb_strtolower($nota['situacao']) === 'cancelada') {
                $status_lancamento = 'Cancelada';
            } elseif ($empresaCnpjLimpo && $notaCnpjLimpo && $empresaCnpjLimpo === $notaCnpjLimpo) {
                $status_lancamento = 'Própria';
            } elseif (!empty($nota['romaneio_chave'])) {
                $status_lancamento = 'Romaneio';
            } elseif (!empty($nota['nota_hash'])) {
                $status_lancamento = 'Lançada';
            } elseif (!empty($nota['desconhecimento_chave'])) {
                $status_lancamento = 'Desconhecimento';
            } elseif (!empty($nota['empresa_cnpj']) && (
                ($nota['tipo_empresa'] === 'filial')
            )) {
                $status_lancamento = 'Transferência';
            }
            // Lógica igual à auditoria para 'Desconhecimento'
            if ($status_lancamento === 'Desconhecimento') {
                $totalDesconhecidas++;
                $valorDesconhecidas += floatval($nota['valor']);
            }
        }

        // --- CONTAGEM DE NOTAS RETORNADAS (igual ao filtro da auditoria) ---
        $totalRetorno = 0;
        $valorRetorno = 0;
        foreach ($todasNotas as $nota) {
            if (
                isset($nota['tipo']) && mb_strtolower($nota['tipo']) === 'entrada'
                && $nota['status_lancamento'] === 'Retornada'
            ) {
                $totalRetorno++;
                $valorRetorno += floatval($nota['valor']);
            }
        }

        $totalRomaneio = $db->prepare("
            SELECT COUNT(*) as total 
            FROM romaneio r 
            INNER JOIN auditoria_notas an ON an.chave COLLATE utf8mb4_unicode_ci = r.chave COLLATE utf8mb4_unicode_ci 
            AND an.empresa_id = r.empresa_id
            WHERE " . (!empty($empresaIds) ? "r.empresa_id IN (" . implode(',', $empresaIds) . ") AND " : "") . "
            an.data_emissao BETWEEN ? AND ?
        ");
        $totalRomaneio->execute([$data_inicial, $data_final]);
        $totalRomaneio = $totalRomaneio->fetch()['total'];

        // Buscar CNPJs das empresas selecionadas
        $empresaCnpjs = [];
        if (!empty($empresaIds)) {
            foreach ($empresas as $emp) {
                if (in_array($emp['id'], $empresaIds)) {
                    $empresaCnpjs[] = preg_replace('/[^0-9]/', '', $emp['cnpj']);
                }
            }
        }
        // --- CONTAGEM DE NOTAS PRÓPRIAS (igual ao filtro da auditoria) ---
        $totalProprias = 0;
        $valorProprias = 0;
        foreach ($todasNotas as $nota) {
            if (
                isset($nota['status_lancamento']) && $nota['status_lancamento'] === 'Própria'
            ) {
                $totalProprias++;
                $valorProprias += floatval($nota['valor']);
            }
        }

        $valorTotal = $db->prepare('SELECT SUM(an.valor) as total FROM auditoria_notas an ' . $where);
        $valorTotal->execute($params);
        $valorTotal = $valorTotal->fetch()['total'] ?? 0;

        $valorCanceladas = $db->prepare("SELECT SUM(an.valor) as total FROM auditoria_notas an " . $where . " AND an.situacao = 'Cancelada'");
        $valorCanceladas->execute($params);
        $valorCanceladas = $valorCanceladas->fetch()['total'] ?? 0;

        $valorRomaneio = $db->prepare("
            SELECT COALESCE(SUM(an.valor), 0) as total 
            FROM romaneio r 
            INNER JOIN auditoria_notas an ON an.chave COLLATE utf8mb4_unicode_ci = r.chave COLLATE utf8mb4_unicode_ci 
            AND an.empresa_id = r.empresa_id
            WHERE " . (!empty($empresaIds) ? "r.empresa_id IN (" . implode(',', $empresaIds) . ") AND " : "") . "
            an.data_emissao BETWEEN ? AND ?
        ");
        $valorRomaneio->execute([$data_inicial, $data_final]);
        $valorRomaneio = $valorRomaneio->fetch()['total'] ?? 0;

        // Gráfico de linhas: notas lançadas por dia
        $sqlLinhas = "SELECT DATE(an.data_emissao) as dia, COUNT(*) as total FROM auditoria_notas an INNER JOIN notas n ON n.hash = an.hash AND n.empresa_id = an.empresa_id " . $where . " GROUP BY dia ORDER BY dia";
        $stmtLinhas = $db->prepare($sqlLinhas);
        $stmtLinhas->execute($params);
        $notasPorDia = $stmtLinhas->fetchAll();

        // Gráfico de pizza: notas por escriturador
        $sqlEscriturador = "SELECT n.escriturador, COUNT(*) as total FROM auditoria_notas an INNER JOIN notas n ON n.hash = an.hash AND n.empresa_id = an.empresa_id " . $where . " GROUP BY n.escriturador ORDER BY total DESC";
        $stmtEscriturador = $db->prepare($sqlEscriturador);
        $stmtEscriturador->execute($params);
        $notasPorEscriturador = $stmtEscriturador->fetchAll();

        // Gráfico de mapa: notas por UF
        $whereUF = $where . " AND an.uf IS NOT NULL AND an.uf != ''";
        $sqlUF = "SELECT UPPER(TRIM(an.uf)) as uf, COUNT(*) as total FROM auditoria_notas an " . $whereUF . " GROUP BY uf ORDER BY total DESC";
        $stmtUF = $db->prepare($sqlUF);
        $stmtUF->execute($params);
        $notasPorUF = $stmtUF->fetchAll();

        // Top 10 fornecedores em valor
        $sqlTopValor = "SELECT an.cnpj, an.razao_social, SUM(an.valor) as total FROM auditoria_notas an " . $where . " GROUP BY an.cnpj, an.razao_social ORDER BY total DESC LIMIT 10";
        $stmtTopValor = $db->prepare($sqlTopValor);
        $stmtTopValor->execute($params);
        $topFornecedoresValor = $stmtTopValor->fetchAll();

        // Top 10 fornecedores mais recorrentes
        $sqlTopRecorrentes = "SELECT an.cnpj, an.razao_social, COUNT(*) as total FROM auditoria_notas an " . $where . " GROUP BY an.cnpj, an.razao_social ORDER BY total DESC LIMIT 10";
        $stmtTopRecorrentes = $db->prepare($sqlTopRecorrentes);
        $stmtTopRecorrentes->execute($params);
        $topFornecedoresRecorrentes = $stmtTopRecorrentes->fetchAll();

        // --- BUSCA DA ÚLTIMA ATUALIZAÇÃO DA AUDITORIA_NOTAS ---
        $ultimaAtualizacao = null;
        if (!empty($empresaIds) && count($empresaIds) === 1) {
            $stmtUltima = $db->prepare('SELECT MAX(data_consulta) as ultima_atualizacao FROM auditoria_notas WHERE empresa_id = ?');
            $stmtUltima->execute([$empresaIds[0]]);
            $rowUltima = $stmtUltima->fetch();
            $ultimaAtualizacao = $rowUltima['ultima_atualizacao'] ?? null;
        }

        // --- CONTAGEM DE NOTAS DE TRANSFERÊNCIA (igual ao filtro da auditoria) ---
        $totalTransferencia = 0;
        $valorTransferencia = 0;
        foreach ($todasNotas as $nota) {
            if (
                mb_strtolower($nota['status_lancamento'] ?? '') === 'transferência'
                && (!isset($nota['situacao']) || mb_strtolower($nota['situacao']) !== 'cancelada')
            ) {
                $totalTransferencia++;
                $valorTransferencia += floatval($nota['valor']);
            }
        }

        // --- NOVA LÓGICA PARA NOTAS ANULADAS (pendente + retornada, mesmo valor e fornecedor) ---
        $notasPendentes = [];
        $notasRetornadas = [];
        foreach ($todasNotas as $nota) {
            // Status igual à auditoria
            $status_lancamento = 'Pendente';
            $notaCnpjLimpo = isset($nota['cnpj']) ? preg_replace('/[^0-9]/', '', $nota['cnpj']) : '';
            $empresaCnpjLimpo = null;
            if (!empty($empresaCnpjs) && $notaCnpjLimpo) {
                foreach ($empresaCnpjs as $cnpj) {
                    if ($cnpj === $notaCnpjLimpo) {
                        $empresaCnpjLimpo = $cnpj;
                        break;
                    }
                }
            }
            if (isset($nota['situacao']) && mb_strtolower($nota['situacao']) === 'cancelada') {
                $status_lancamento = 'Cancelada';
            } elseif ($empresaCnpjLimpo && $notaCnpjLimpo && $empresaCnpjLimpo === $notaCnpjLimpo) {
                $status_lancamento = 'Própria';
            } elseif (!empty($nota['romaneio_chave'])) {
                $status_lancamento = 'Romaneio';
            } elseif (!empty($nota['nota_hash'])) {
                $status_lancamento = 'Lançada';
            } elseif (!empty($nota['desconhecimento_chave'])) {
                $status_lancamento = 'Desconhecimento';
            } elseif (!empty($nota['empresa_cnpj']) && (
                ($nota['tipo_empresa'] === 'filial')
            )) {
                $status_lancamento = 'Transferência';
            }
            // Pendente
            if (
                $status_lancamento !== 'Lançada' &&
                (!isset($nota['situacao']) || $nota['situacao'] !== 'Cancelada') &&
                (!isset($nota['tipo']) || $nota['tipo'] !== 'Entrada') &&
                $status_lancamento !== 'Transferência' &&
                $status_lancamento !== 'Desconhecimento' &&
                $status_lancamento !== 'Romaneio' &&
                $status_lancamento !== 'Própria'
            ) {
                $notasPendentes[] = $nota;
            }
            // Retornada
            if (
                isset($nota['tipo']) && $nota['tipo'] === 'Entrada' &&
                mb_strtolower($status_lancamento) !== 'própria'
            ) {
                $notasRetornadas[] = $nota;
            }
        }
        // Cruzamento: mesmo valor e mesmo fornecedor (CNPJ)
        $anuladas = [];
        foreach ($notasPendentes as $pendente) {
            foreach ($notasRetornadas as $retornada) {
                if (
                    floatval($pendente['valor']) === floatval($retornada['valor']) &&
                    preg_replace('/[^0-9]/', '', $pendente['cnpj']) === preg_replace('/[^0-9]/', '', $retornada['cnpj'])
                ) {
                    $anuladas[] = $pendente;
                    break;
                }
            }
        }
        $totalAnuladas = count($anuladas);
        $valorAnuladas = array_sum(array_map(function($n){ return floatval($n['valor']); }, $anuladas));

        // Gráfico de pizza: notas por coleta
        $sqlColeta = "SELECT n.coleta, COUNT(*) as total FROM auditoria_notas an INNER JOIN notas n ON n.hash = an.hash AND n.empresa_id = an.empresa_id " . $where . " GROUP BY n.coleta ORDER BY total DESC";
        $stmtColeta = $db->prepare($sqlColeta);
        $stmtColeta->execute($params);
        $notasPorColeta = $stmtColeta->fetchAll();

        $content = view('home/index', [
            'empresas' => $empresas,
            'empresaIds' => $empresaIds,
            'data_inicial' => $data_inicial,
            'data_final' => $data_final,
            'totalNotas' => $totalNotas,
            'totalLancadas' => $totalLancadas,
            'totalPendentes' => $totalPendentes,
            'totalCanceladas' => $totalCanceladas,
            'totalDesconhecidas' => $totalDesconhecidas,
            'totalRetorno' => $totalRetorno,
            'totalRomaneio' => $totalRomaneio,
            'totalProprias' => $totalProprias,
            'valorLancadas' => $valorLancadas,
            'valorPendentes' => $valorPendentes,
            'valorCanceladas' => $valorCanceladas,
            'valorDesconhecidas' => $valorDesconhecidas,
            'valorRetorno' => $valorRetorno,
            'valorRomaneio' => $valorRomaneio,
            'valorTotal' => $valorTotal,
            'valorProprias' => $valorProprias,
            'notasPorDia' => $notasPorDia,
            'notasPorUF' => $notasPorUF,
            'topFornecedoresValor' => $topFornecedoresValor,
            'topFornecedoresRecorrentes' => $topFornecedoresRecorrentes,
            'totalTransferencia' => $totalTransferencia,
            'valorTransferencia' => $valorTransferencia,
            'totalAnuladas' => $totalAnuladas,
            'valorAnuladas' => $valorAnuladas,
            'notasPorEscriturador' => $notasPorEscriturador,
            'notasPorColeta' => $notasPorColeta,
            'ultimaAtualizacao' => $ultimaAtualizacao
        ], true);
        require APP_ROOT . '/app/Views/layouts/main.php';
    }
    
    public function sobre() {
        $content = view('home/sobre', ['title' => 'Sobre - ' . APP_NAME], true);
        require APP_ROOT . '/app/Views/layouts/main.php';
    }
    
    public function contato() {
        $content = view('home/contato', ['title' => 'Contato - ' . APP_NAME], true);
        require APP_ROOT . '/app/Views/layouts/main.php';
    }

    public function redirectDashboard() {
        header('Location: /dashboard');
        exit;
    }
} 