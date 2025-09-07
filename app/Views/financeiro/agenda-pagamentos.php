<?php
// Verificar se as constantes estão definidas
if (!defined('DB_HOST')) {
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'seu_banco');
    define('DB_USER', 'seu_usuario');
    define('DB_PASS', 'sua_senha');
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agenda de Pagamentos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .cursor-pointer {
            cursor: pointer !important;
            transition: all 0.2s ease;
        }
        .cursor-pointer:hover {
            text-decoration: underline !important;
            opacity: 0.8 !important;
            transform: scale(1.05) !important;
        }
        .cursor-pointer:active {
            transform: scale(0.95) !important;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        .modal.show {
            display: block;
        }
        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 0;
            border: 1px solid #888;
            width: 80%;
            max-width: 800px;
            border-radius: 8px;
        }
        .modal-header {
            padding: 15px 20px;
            border-bottom: 1px solid #dee2e6;
            background-color: #ffffff !important;
            border-radius: 8px 8px 0 0;
        }

        .modal-header .btn-close {
            color: #000000 !important;
            font-weight: bold !important;
            font-size: 1.5rem !important;
            opacity: 1 !important;
        }
        .modal-header .btn-close:hover {
            color: #000000 !important;
            opacity: 0.8 !important;
        }
        .modal-body {
            padding: 20px;
        }
        .modal-footer {
            padding: 15px 20px;
            border-top: 1px solid #dee2e6;
            background-color: #f8f9fa;
            border-radius: 0 0 8px 8px;
        }
        .btn-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="main-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <h1 class="h3 mb-4 text-gray-800">Agenda de Pagamentos</h1>
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex flex-wrap align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">Previsão</h6>
                            <?php
                            // Buscar empresas do banco de dados conforme o tipo de usuário
                            try {
                                $db = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4', DB_USER, DB_PASS, [
                                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                                ]);
                                $empresas = [];
                                $usuario_id = $_SESSION['usuario_id'] ?? 1;
                                $tipo = $_SESSION['tipo_usuario'] ?? ($_SESSION['tipo'] ?? 1);
                                
                                if ($tipo == 1 || $tipo == 2) {
                                    $stmt = $db->query('SELECT id, razao_social as nome, tipo_empresa FROM empresas ORDER BY razao_social');
                                    $empresas = $stmt->fetchAll();
                                } else if ($tipo == 3) {
                                    $stmt = $db->prepare('
                                        SELECT e.id, e.razao_social as nome, e.tipo_empresa
                                        FROM empresas e
                                        JOIN usuario_empresas ue ON ue.empresa_id = e.id
                                        WHERE ue.usuario_id = ?
                                        ORDER BY e.razao_social
                                    ');
                                    $stmt->execute([$usuario_id]);
                                    $empresas = $stmt->fetchAll();
                                }
                                
                                if (empty($empresas)) {
                                    // Dados de teste se não houver empresas
                                    $empresas = [['id' => 1, 'nome' => 'Empresa Teste', 'tipo_empresa' => 'matriz']];
                                }
                                
                                $empresaSelecionada = isset($_GET['empresa']) ? (int)$_GET['empresa'] : ($empresas[0]['id'] ?? 1);
                            } catch (Exception $e) {
                                $empresas = [['id' => 1, 'nome' => 'Empresa Teste', 'tipo_empresa' => 'matriz']];
                                $empresaSelecionada = 1;
                            }
                            ?>
                            <form class="d-flex align-items-center gap-2 flex-wrap" method="get">
                                <label for="empresa" class="mb-0">Empresa</label>
                                <select name="empresa" id="empresa" class="form-select form-select-sm w-auto">
                                    <?php foreach ($empresas as $empresa): ?>
                                        <option value="<?= $empresa['id'] ?>" <?= $empresa['id'] == $empresaSelecionada ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($empresa['nome']) ?><?= isset($empresa['tipo_empresa']) && in_array($empresa['tipo_empresa'], ['matriz','filial']) ? ' - ' . ucfirst($empresa['tipo_empresa']) : '' ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="ano" class="mb-0 ms-2">Ano</label>
                                <select name="ano" id="ano" class="form-select form-select-sm w-auto">
                                    <?php 
                                    $anoSelecionado = isset($_GET['ano']) ? (int)$_GET['ano'] : date('Y');
                                    for ($a = date('Y')-2; $a <= date('Y')+2; $a++): ?>
                                        <option value="<?= $a ?>" <?= $a == $anoSelecionado ? 'selected' : '' ?>><?= $a ?></option>
                                    <?php endfor; ?>
                                </select>
                                <label for="mes" class="mb-0 ms-2">Mês</label>
                                <select name="mes" id="mes" class="form-select form-select-sm w-auto">
                                    <?php 
                                    $mesSelecionado = isset($_GET['mes']) ? (int)$_GET['mes'] : date('n');
                                    foreach ([1=>'Janeiro',2=>'Fevereiro',3=>'Março',4=>'Abril',5=>'Maio',6=>'Junho',7=>'Julho',8=>'Agosto',9=>'Setembro',10=>'Outubro',11=>'Novembro',12=>'Dezembro'] as $num=>$nome): ?>
                                        <option value="<?= $num ?>" <?= $num == $mesSelecionado ? 'selected' : '' ?>><?= $nome ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="submit" class="btn btn-primary btn-sm ms-2">Filtrar</button>
                            </form>
                        </div>
                        <div class="card-body">
                            <?php
                            // Parâmetros do mês/ano
                            $ano = isset($_GET['ano']) ? (int)$_GET['ano'] : date('Y');
                            $mes = isset($_GET['mes']) ? (int)$_GET['mes'] : date('n');
                            $primeiroDia = mktime(0,0,0,$mes,1,$ano);
                            $ultimoDia = date('t', $primeiroDia);
                            $diaSemana = date('w', $primeiroDia); // 0=domingo
                            $hoje = date('Y-m-d');

                            // --- FERIADOS NACIONAIS ---
                            $anoFeriado = $ano;
                            $feriados = [
                                "$anoFeriado-01-01", // Confraternização Universal
                                "$anoFeriado-04-21", // Tiradentes
                                "$anoFeriado-05-01", // Dia do Trabalho
                                "$anoFeriado-09-07", // Independência
                                "$anoFeriado-07-02", // Independência da Bahia
                                "$anoFeriado-10-12", // N. Sra. Aparecida
                                "$anoFeriado-11-02", // Finados
                                "$anoFeriado-06-24", // São João
                                "$anoFeriado-11-15", // Proclamação da República
                                "$anoFeriado-12-25", // Natal
                            ];

                            // Buscar pagamentos reais da tabela auditoria_notas_vencimentos
                            $pagamentos = [];
                            $detalhesPagamentos = [];
                            
                            try {
                                if ($empresaSelecionada && $ano && $mes) {
                                    // Consulta para buscar os dados da tabela auditoria_notas_vencimentos
                                    $stmt = $db->prepare('
                                        SELECT 
                                            v.data_vencimento, 
                                            n.valor, 
                                            v.nota_id, 
                                            n.numero as numero_nota, 
                                            n.razao_social as fornecedor
                                        FROM auditoria_notas_vencimentos v
                                        JOIN auditoria_notas n ON n.id = v.nota_id
                                        WHERE n.empresa_id = :empresa_id
                                          AND YEAR(v.data_vencimento) = :ano
                                          AND MONTH(v.data_vencimento) = :mes
                                        ORDER BY v.data_vencimento, n.valor DESC
                                    ');
                                    $stmt->execute([
                                        ':empresa_id' => $empresaSelecionada,
                                        ':ano' => $ano,
                                        ':mes' => $mes
                                    ]);
                                    
                                    $resultados = $stmt->fetchAll();
                                    
                                    // Processar os resultados para agrupar por data
                                    foreach ($resultados as $row) {
                                        $data = $row['data_vencimento'];
                                        
                                        // Inicializar arrays se não existirem
                                        if (!isset($pagamentos[$data])) {
                                            $pagamentos[$data] = 0;
                                            $detalhesPagamentos[$data] = [];
                                        }
                                        
                                        // Somar valores por data
                                        $pagamentos[$data] += (float)$row['valor'];
                                        
                                        // Armazenar detalhes para o modal
                                        $detalhesPagamentos[$data][] = [
                                            'nota_id' => $row['nota_id'],
                                            'numero_nota' => $row['numero_nota'] ?? 'N/A',
                                            'fornecedor' => $row['fornecedor'] ?? 'Fornecedor não informado',
                                            'valor' => (float)$row['valor']
                                        ];
                                    }
                                    
                                    // Debug: verificar se há dados
                                    if (empty($resultados)) {
                                        error_log("Nenhum resultado encontrado para empresa: $empresaSelecionada, ano: $ano, mês: $mes");
                                    } else {
                                        error_log("Encontrados " . count($resultados) . " registros de vencimentos");
                                    }
                                }
                            } catch (Exception $e) {
                                error_log("Erro na consulta de vencimentos: " . $e->getMessage());
                                
                                // Se der erro, criar dados de teste
                                $hoje = date('Y-m-d');
                                $pagamentos[$hoje] = 1500.00;
                                $detalhesPagamentos[$hoje] = [
                                    [
                                        'nota_id' => 1,
                                        'numero_nota' => 'NF001/2025',
                                        'fornecedor' => 'Fornecedor Teste',
                                        'valor' => 1500.00
                                    ]
                                ];
                            }

                            // --- AJUSTE DE PAGAMENTOS PARA DIAS ÚTEIS ---
                            $pagamentosAjustados = [];
                            $detalhesAjustados = [];
                            foreach ($pagamentos as $data => $valor) {
                                $dataAjustada = $data;
                                while (in_array(date('w', strtotime($dataAjustada)), [0,6]) || in_array($dataAjustada, $feriados)) {
                                    $dataAjustada = date('Y-m-d', strtotime($dataAjustada . ' +1 day'));
                                }
                                if (!isset($pagamentosAjustados[$dataAjustada])) {
                                    $pagamentosAjustados[$dataAjustada] = 0;
                                    $detalhesAjustados[$dataAjustada] = [];
                                }
                                $pagamentosAjustados[$dataAjustada] += $valor;
                                if (isset($detalhesPagamentos[$data])) {
                                    foreach ($detalhesPagamentos[$data] as $detalhe) {
                                        $detalhesAjustados[$dataAjustada][] = $detalhe;
                                    }
                                }
                            }
                            $pagamentos = $pagamentosAjustados;
                            $detalhesPagamentos = $detalhesAjustados;

                            // Montar matriz do calendário
                            $calendario = [];
                            $semana = [];
                            for ($i=0; $i<$diaSemana; $i++) $semana[] = null;
                            for ($dia=1; $dia<=$ultimoDia; $dia++) {
                                $data = sprintf('%04d-%02d-%02d', $ano, $mes, $dia);
                                $semana[] = $data;
                                if (count($semana) == 7) {
                                    $calendario[] = $semana;
                                    $semana = [];
                                }
                            }
                            if (count($semana)) {
                                while (count($semana) < 7) $semana[] = null;
                                $calendario[] = $semana;
                            }
                            
                            // Calcular totais
                            $totalMes = 0;
                            $totaisSemana = [];
                            foreach ($calendario as $idx => $sem) {
                                $totaisSemana[$idx] = 0;
                                foreach ($sem as $data) {
                                    if ($data && isset($pagamentos[$data])) {
                                        $totaisSemana[$idx] += $pagamentos[$data];
                                        $totalMes += $pagamentos[$data];
                                    }
                                }
                            }

                            // --- PREVISÃO PARA OS PRÓXIMOS 12 MESES (EXCLUINDO O MÊS SELECIONADO) ---
                            $previsao12meses = 0;
                            try {
                                if ($empresaSelecionada && $ano && $mes) {
                                    // Primeiro dia do mês seguinte ao selecionado
                                    $inicio = date('Y-m-01', strtotime("$ano-$mes-01 +1 month"));
                                    // Último dia do mês 12 meses após o mês selecionado
                                    $fim = date('Y-m-t', strtotime("$inicio +11 months"));
                                    
                                    $stmt = $db->prepare('
                                        SELECT v.data_vencimento, n.valor
                                        FROM auditoria_notas_vencimentos v
                                        JOIN auditoria_notas n ON n.id = v.nota_id
                                        WHERE n.empresa_id = :empresa_id
                                          AND v.data_vencimento BETWEEN :inicio AND :fim
                                    ');
                                    $stmt->execute([
                                        ':empresa_id' => $empresaSelecionada,
                                        ':inicio' => $inicio,
                                        ':fim' => $fim
                                    ]);
                                    
                                    $pagamentosFuturos = $stmt->fetchAll();
                                    
                                    // Ajustar para dias úteis e somar
                                    $ajustados = [];
                                    foreach ($pagamentosFuturos as $row) {
                                        $dataAjustada = $row['data_vencimento'];
                                        while (in_array(date('w', strtotime($dataAjustada)), [0,6]) || in_array($dataAjustada, $feriados)) {
                                            $dataAjustada = date('Y-m-d', strtotime($dataAjustada . ' +1 day'));
                                        }
                                        if (!isset($ajustados[$dataAjustada])) {
                                            $ajustados[$dataAjustada] = 0;
                                        }
                                        $ajustados[$dataAjustada] += (float)$row['valor'];
                                    }
                                    $previsao12meses = array_sum($ajustados);
                                }
                            } catch (Exception $e) {
                                error_log("Erro na consulta de previsão 12 meses: " . $e->getMessage());
                                $previsao12meses = 0;
                            }
                            ?>
                            
                            <div class="mb-3">
                                <span class="fw-bold fs-5">Total do mês:</span>
                                <span class="fs-5 text-success">R$ <?= number_format($totalMes,2,',','.') ?></span>
                            </div>
                            
                            <div class="mb-3">
                                <span class="fw-bold fs-5">Previsão para os próximos 12 meses:</span>
                                <span class="fs-5 text-primary">R$ <?= number_format($previsao12meses,2,',','.') ?></span>
                            </div>
                            
                            <div class="table-responsive">
                                <table class="table table-bordered text-center align-middle" style="min-width:900px">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Dom</th><th>Seg</th><th>Ter</th><th>Qua</th><th>Qui</th><th>Sex</th><th>Sáb</th>
                                            <th class="bg-light">Total Semana</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($calendario as $idx => $sem): ?>
                                        <tr>
                                            <?php foreach ($sem as $data): ?>
                                                <?php if ($data): ?>
                                                    <td style="height:70px;<?= $data==$hoje?'background:#e3f2fd;':'' ?>">
                                                        <div class="fw-bold"><?= (int)substr($data,8,2) ?></div>
                                                        <?php if (isset($pagamentos[$data]) && $pagamentos[$data]>0): ?>
                                                            <div class="text-success cursor-pointer" 
                                                                 onclick="abrirModalPagamentos('<?= $data ?>', '<?= date('d/m/Y', strtotime($data)) ?>', <?= htmlspecialchars(json_encode($detalhesPagamentos[$data] ?? [])) ?>)">
                                                                R$ <?= number_format($pagamentos[$data],2,',','.') ?>
                                                            </div>
                                                        <?php endif; ?>
                                                    </td>
                                                <?php else: ?>
                                                    <td></td>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                            <td class="bg-light fw-bold">
                                                R$ <?= number_format($totaisSemana[$idx],2,',','.') ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card mt-4">
                        <div class="card-header bg-info text-white">
                            <h6 class="m-0">Datas importantes</h6>
                        </div>
                        <div class="card-body">
                            <ul class="mb-0">
                                <li><strong>Dia 9:</strong> ICMS</li>
                                <li><strong>Dia 20:</strong> DAS Simples Nacional</li>
                                <li><strong>Dia 25:</strong> PIS/COFINS</li>
                                <li><strong>Último dia útil de Abril, Julho, Outubro e Janeiro:</strong> IRPJ e CSLL</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Detalhes dos Pagamentos -->
    <div id="modalPagamentos" class="modal">
        <div class="modal-content">
            <!-- Título personalizado posicionado manualmente -->
            <div id="tituloModal" style="position: absolute; top: 15px; left: 20px; color: #000000; font-weight: bold; font-size: 20px; z-index: 1001; background: white; padding: 5px;">Detalhes dos Pagamentos</div>
            
            <div class="modal-header" style="background-color: #ffffff !important; position: relative;">
                <div style="height: 30px;"></div> <!-- Espaço para o título -->
                <button type="button" class="btn-close" onclick="fecharModal()" style="position: absolute; top: 15px; right: 20px;">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-12">
                        <h6 class="text-primary" id="dataModal"></h6>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="tabelaDetalhes">
                        <thead class="table-dark">
                            <tr>
                                <th style="color:rgb(255, 255, 255) !important; background-color:rgb(52, 73, 94) !important; cursor: pointer;" onclick="ordenarTabela(0)">
                                    Nº da Nota <span id="sort-0">↕</span>
                                </th>
                                <th style="color: #ffffff !important; background-color:rgb(52, 73, 94) !important; cursor: pointer;" onclick="ordenarTabela(1)">
                                    Fornecedor <span id="sort-1">↕</span>
                                </th>
                                <th class="text-end" style="color: #ffffff !important; background-color:rgb(52, 73, 94) !important; cursor: pointer;" onclick="ordenarTabela(2)">
                                    Valor <span id="sort-2">↕</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody id="corpoTabela">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <div class="row w-100 align-items-center">
                    <div class="col-md-6">
                        <span class="fw-bold">Total de títulos: <span class="text-primary" id="totalTitulos">0</span></span>
                    </div>
                    <div class="col-md-6 text-end">
                        <button type="button" class="btn btn-secondary" onclick="fecharModal()">Fechar</button>
                        <button type="button" class="btn btn-primary" onclick="gerarRelatorio()">
                            📄 Gerar Relatório
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Capturar nome do usuário logado
        window.usuarioLogado = '<?= $_SESSION['nome'] ?? $_SESSION['usuario'] ?? 'Usuário' ?>';
        // Função para abrir modal
        function abrirModalPagamentos(data, dataFormatada, detalhes) {
            console.log('Função chamada:', {data, dataFormatada, detalhes});
            
            if (!data || !detalhes || detalhes.length === 0) {
                alert('Nenhum detalhe encontrado para esta data.');
                return;
            }
            
            // Atualizar título do modal
            document.getElementById('dataModal').textContent = `Pagamentos para ${dataFormatada}`;
            
            // Limpar e preencher tabela
            const tbody = document.getElementById('corpoTabela');
            tbody.innerHTML = '';
            
            let totalTitulos = 0;
            let totalValor = 0;
            
            detalhes.forEach((item) => {
                const row = tbody.insertRow();
                row.innerHTML = `
                    <td>${item.numero_nota || 'N/A'}</td>
                    <td>${item.fornecedor || 'N/A'}</td>
                    <td class="text-end">R$ ${parseFloat(item.valor).toLocaleString('pt-BR', {minimumFractionDigits: 2})}</td>
                `;
                totalTitulos++;
                totalValor += parseFloat(item.valor);
            });
            
            // Adicionar linha de total
            const rowTotal = tbody.insertRow();
            rowTotal.className = 'table-info fw-bold';
            rowTotal.innerHTML = `
                <td colspan="2" class="text-end"><strong>TOTAL:</strong></td>
                <td class="text-end"><strong>R$ ${totalValor.toLocaleString('pt-BR', {minimumFractionDigits: 2})}</strong></td>
            `;
            
            // Atualizar contador
            document.getElementById('totalTitulos').textContent = totalTitulos;
            
            // Armazenar dados para o relatório
            window.dadosRelatorio = {
                data: data,
                dataFormatada: dataFormatada,
                detalhes: detalhes,
                totalTitulos: totalTitulos,
                totalValor: totalValor
            };
            
            // Abrir modal
            document.getElementById('modalPagamentos').classList.add('show');
            
            // Forçar visibilidade do título após abrir o modal
            setTimeout(() => {
                const titulo = document.getElementById('tituloModal');
                if (titulo) {
                    titulo.style.color = '#000000';
                    titulo.style.fontWeight = 'bold';
                    titulo.style.fontSize = '20px';
                    titulo.style.opacity = '1';
                    titulo.style.visibility = 'visible';
                    titulo.style.display = 'block';
                    titulo.style.background = 'white';
                    titulo.style.padding = '5px';
                }
            }, 100);
        }
        
        // Função para fechar modal
        function fecharModal() {
            document.getElementById('modalPagamentos').classList.remove('show');
        }
        
        // Função para gerar relatório
        function gerarRelatorio() {
            if (!window.dadosRelatorio) {
                alert('Nenhum dado disponível para gerar relatório.');
                return;
            }
            
            const { data, dataFormatada, totalTitulos, totalValor } = window.dadosRelatorio;
            
            // Capturar dados da tabela já ordenada
            const tbody = document.getElementById('corpoTabela');
            const linhas = Array.from(tbody.querySelectorAll('tr'));
            
            // Remover linha de total
            const linhasDados = linhas.filter(linha => !linha.classList.contains('table-info'));
            
            // Extrair dados das linhas ordenadas
            const detalhesOrdenados = linhasDados.map(linha => {
                const celulas = linha.querySelectorAll('td');
                return {
                    numero_nota: celulas[0].textContent.trim(),
                    fornecedor: celulas[1].textContent.trim(),
                    valor: celulas[2].textContent.trim()
                };
            });
            
            // Determinar informação de ordenação
            let infoOrdenacao = '';
            if (ordenacaoAtual.coluna !== -1) {
                const colunas = ['Nº da Nota', 'Fornecedor', 'Valor'];
                const direcoes = { 'asc': 'crescente', 'desc': 'decrescente' };
                infoOrdenacao = `Ordenado por: ${colunas[ordenacaoAtual.coluna]} (${direcoes[ordenacaoAtual.direcao]})`;
            }
            
            // Preload da logo e aguardar carregamento
            const logoUrl = `${window.location.origin}/public/assets/images/Agil_Login.png`;
            const logoImg = new Image();
            
            logoImg.onload = function() {
                gerarRelatorioComLogo(logoUrl, detalhesOrdenados, dataFormatada, totalTitulos, totalValor, infoOrdenacao);
            };
            
            logoImg.onerror = function() {
                // Se a logo não carregar, gerar relatório sem ela
                gerarRelatorioComLogo('', detalhesOrdenados, dataFormatada, totalTitulos, totalValor, infoOrdenacao);
            };
            
            logoImg.src = logoUrl;
        }
        
        // Função auxiliar para gerar relatório com logo
        function gerarRelatorioComLogo(logoUrl, detalhesOrdenados, dataFormatada, totalTitulos, totalValor, infoOrdenacao) {
            
            const conteudoRelatorio = `
                <!DOCTYPE html>
                <html lang="pt-BR">
                <head>
                    <meta charset="UTF-8">
                    <title>Relatório de Pagamentos - ${dataFormatada}</title>
                    <style>
                        body { font-family: Arial, sans-serif; margin: 20px; font-size: 12px; }
                        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 20px; margin-bottom: 30px; }
                        .logo { max-height: 50px; margin-bottom: 15px; }
                        .info { margin-bottom: 20px; font-size: 12px; color: #666; }
                        table { width: 100%; border-collapse: collapse; margin: 20px 0; font-size: 11px; }
                        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                        th { background-color: #34495e; color: white; font-weight: bold; font-size: 11px; }
                        .total-row { background-color: #ecf0f1; font-weight: bold; }
                        .valor { text-align: right; white-space: nowrap; }
                        .numero-nota { white-space: nowrap; }
                        .fornecedor { word-wrap: break-word; max-width: 200px; }
                        .footer { margin-top: 40px; text-align: center; font-size: 10px; color: #666; border-top: 1px solid #ddd; padding-top: 20px; }
                    </style>
                </head>
                <body>
                    <div class="header">
                        ${logoUrl ? `<img src="${logoUrl}" alt="Logo AgilFiscal" class="logo">` : ''}
                        <h1>Relatório de Pagamentos</h1>
                        <div>Data: ${dataFormatada}</div>
                        ${infoOrdenacao ? `<div class="info">${infoOrdenacao}</div>` : ''}
                    </div>
                    
                    <table>
                        <thead>
                            <tr>
                                <th>Nº da Nota</th>
                                <th>Fornecedor</th>
                                <th class="valor">Valor</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${detalhesOrdenados.map(item => `
                                <tr>
                                    <td class="numero-nota">${item.numero_nota}</td>
                                    <td class="fornecedor">${item.fornecedor}</td>
                                    <td class="valor">${item.valor}</td>
                                </tr>
                            `).join('')}
                            <tr class="total-row">
                                <td colspan="2" class="total">TOTAL:</td>
                                <td class="total valor">R$ ${totalValor.toLocaleString('pt-BR', {minimumFractionDigits: 2})}</td>
                            </tr>
                        </tbody>
                    </table>
                    
                    <div class="footer">
                        <p><strong>Gerado em:</strong> ${new Date().toLocaleString('pt-BR')} por ${window.usuarioLogado || 'Usuário'}</p>
                        <p><strong>www.agilfiscal.com.br</strong></p>
                    </div>
                </body>
                </html>
            `;
            
            const novaJanela = window.open('', '_blank');
            novaJanela.document.write(conteudoRelatorio);
            novaJanela.document.close();
            novaJanela.print();
        }
        
        // Fechar modal ao clicar fora
        window.onclick = function(event) {
            const modal = document.getElementById('modalPagamentos');
            if (event.target === modal) {
                fecharModal();
            }
        }
        
        // Variáveis para controle de ordenação
        let ordenacaoAtual = { coluna: -1, direcao: 'asc' };
        
        // Função para ordenar a tabela
        function ordenarTabela(coluna) {
            const tbody = document.getElementById('corpoTabela');
            const linhas = Array.from(tbody.querySelectorAll('tr'));
            
            // Remover linha de total temporariamente
            const linhaTotal = linhas.pop();
            
            // Determinar direção da ordenação
            if (ordenacaoAtual.coluna === coluna) {
                ordenacaoAtual.direcao = ordenacaoAtual.direcao === 'asc' ? 'desc' : 'asc';
            } else {
                ordenacaoAtual.direcao = 'asc';
            }
            ordenacaoAtual.coluna = coluna;
            
            // Ordenar as linhas
            linhas.sort((a, b) => {
                let valorA = a.cells[coluna].textContent.trim();
                let valorB = b.cells[coluna].textContent.trim();
                
                // Tratamento especial para coluna de valor (coluna 2)
                if (coluna === 2) {
                    // Remover R$, espaços e converter vírgula para ponto
                    valorA = parseFloat(valorA.replace('R$', '').replace(/\./g, '').replace(',', '.').trim());
                    valorB = parseFloat(valorB.replace('R$', '').replace(/\./g, '').replace(',', '.').trim());
                }
                // Tratamento especial para coluna de Nº da Nota (coluna 0)
                else if (coluna === 0) {
                    // Extrair apenas números da nota
                    valorA = parseInt(valorA.replace(/\D/g, '')) || 0;
                    valorB = parseInt(valorB.replace(/\D/g, '')) || 0;
                }
                // Para Fornecedor (coluna 1), manter como string
                else {
                    valorA = valorA.toLowerCase();
                    valorB = valorB.toLowerCase();
                }
                
                // Comparação
                let resultado = 0;
                if (valorA < valorB) resultado = -1;
                else if (valorA > valorB) resultado = 1;
                
                return ordenacaoAtual.direcao === 'asc' ? resultado : -resultado;
            });
            
            // Limpar tbody e reordenar
            tbody.innerHTML = '';
            linhas.forEach(linha => tbody.appendChild(linha));
            tbody.appendChild(linhaTotal);
            
            // Atualizar indicadores de ordenação
            for (let i = 0; i < 3; i++) {
                const span = document.getElementById(`sort-${i}`);
                if (i === coluna) {
                    span.textContent = ordenacaoAtual.direcao === 'asc' ? '↑' : '↓';
                } else {
                    span.textContent = '↕';
                }
            }
        }
        
        // Debug
        console.log('Script carregado!');
        console.log('Funções disponíveis:', {
            abrirModalPagamentos: typeof abrirModalPagamentos,
            fecharModal: typeof fecharModal,
            gerarRelatorio: typeof gerarRelatorio,
            ordenarTabela: typeof ordenarTabela
        });
    </script>
</body>
</html>