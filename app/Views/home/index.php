<?php include_once __DIR__ . '/../layouts/header.php'; ?>
<?php $title = 'Dashboard'; ?>



<!-- Modal de seleção de empresas -->
<div class="modal fade" id="modalEmpresas" tabindex="-1" aria-labelledby="modalEmpresasLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalEmpresasLabel">Selecionar Empresas</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <div class="modal-body">
        <input type="text" class="form-control mb-3" id="buscaEmpresaModal" placeholder="Buscar por razão social..." onkeyup="filtrarEmpresasModal()">
        <div id="listaEmpresasModal">
        <!-- Opção Todas -->
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="empresa_todas" onclick="toggleTodasEmpresas(this)">
            <label class="form-check-label" for="empresa_todas"><strong>Todas</strong></label>
        </div>
        <?php
        // Ordena as empresas por razão social
        usort($empresas, function($a, $b) {
            return strcmp(mb_strtoupper($a['razao_social']), mb_strtoupper($b['razao_social']));
        });
        foreach ($empresas as $empresa): ?>
          <div class="form-check">
            <input class="form-check-input" type="checkbox" name="empresa_ids[]" value="<?= $empresa['id'] ?>" id="empresa_<?= $empresa['id'] ?>"
              <?= in_array($empresa['id'], $empresaIds ?? []) ? 'checked' : '' ?>>
            <label class="form-check-label" for="empresa_<?= $empresa['id'] ?>">
              <?= htmlspecialchars($empresa['razao_social']) ?>
              <?php if (($empresa['tipo_empresa'] ?? '') === 'matriz'): ?> - Matriz<?php elseif (($empresa['tipo_empresa'] ?? '') === 'filial'): ?> - Filial<?php endif; ?>
            </label>
          </div>
        <?php endforeach; ?>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-sistema" style="background:#2c3e50;color:#fff;border:none;" data-bs-dismiss="modal">Fechar</button>
        <button type="button" class="btn btn-sistema" style="background:#2c3e50;color:#fff;border:none;" onclick="limparEmpresas()">Limpar</button>
        <button type="button" class="btn btn-sistema" style="background:#2c3e50;color:#fff;border:none;" onclick="aplicarEmpresas()">Aplicar</button>
      </div>
    </div>
  </div>
</div>

<div class="row mb-4">
    <div class="col-12 text-center">
        <h1 class="display-4">Bem-vindo à Ágil Fiscal</h1>
        <p class="lead">Painel de Indicadores e Monitoramento de Notas Fiscais</p>
    </div>
</div>

<form method="GET" class="row g-3 align-items-end mb-4" id="filtroDashboard">
    <div class="col-md-4">
        <label class="form-label">Empresas</label>
        <div class="input-group">
            <input type="text" class="form-control" id="empresasSelecionadas" value="<?php
                if (empty($empresaIds)) {
                    echo 'Todas';
                } else {
                    $nomes = [];
                    foreach ($empresas as $empresa) {
                        if (in_array($empresa['id'], $empresaIds)) {
                            $nomesCompletos = explode(' ', $empresa['razao_social']);
                            $primeiroNome = $nomesCompletos[0];
                            $segundoNome = isset($nomesCompletos[1]) ? $nomesCompletos[1] : '';
                            $nomes[] = trim($primeiroNome . ' ' . $segundoNome);
                        }
                    }
                    echo implode(', ', $nomes);
                }
            ?>" readonly>
            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalEmpresas">Selecionar Empresas</button>
        </div>
        <!-- Campos ocultos para manter seleção ao enviar o form -->
        <div id="hiddenEmpresas">
        <?php if (!empty($empresaIds)) foreach ($empresaIds as $eid): ?>
            <input type="hidden" name="empresa_ids[]" value="<?= $eid ?>">
        <?php endforeach; ?>
        </div>
    </div>
    <div class="col-md-3">
        <label for="data_inicial" class="form-label">Data Inicial</label>
        <input type="date" class="form-control" id="data_inicial" name="data_inicial" value="<?= htmlspecialchars($data_inicial) ?>">
    </div>
    <div class="col-md-3">
        <label for="data_final" class="form-label">Data Final</label>
        <input type="date" class="form-control" id="data_final" name="data_final" value="<?= htmlspecialchars($data_final) ?>">
    </div>
    <div class="col-md-2 d-flex gap-2">
        <button type="submit" class="btn btn-primary w-100" style="background:#2c3e50;color:#fff;border:none;">Filtrar</button>
    </div>
    <div class="col-12 mt-2 d-flex gap-2 align-items-center">
        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setAtalho('30')">Últimos 30 dias</button>
        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setAtalho('60')">Últimos 60 dias</button>
        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setAtalho('mes_corrente')">Este Mês</button>
        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setAtalho('mes_passado')">Mês Passado</button>
        <?php if (!empty(
            $ultimaAtualizacao) && !empty($empresaIds) && count($empresaIds) === 1): ?>
            <span class="ms-2 text-muted" style="font-size:0.95em;">
                Última atualização: <?= date('d/m/Y H:i', strtotime($ultimaAtualizacao)) ?>
            </span>
        <?php endif; ?>
    </div>
</form>
<script>
function setAtalho(tipo) {
    const hoje = new Date();
    let dataInicial, dataFinal;
    if (tipo === '30') {
        dataFinal = hoje.toISOString().slice(0,10);
        const d = new Date(hoje); d.setDate(d.getDate() - 29);
        dataInicial = d.toISOString().slice(0,10);
    } else if (tipo === '60') {
        dataFinal = hoje.toISOString().slice(0,10);
        const d = new Date(hoje); d.setDate(d.getDate() - 59);
        dataInicial = d.toISOString().slice(0,10);
    } else if (tipo === 'mes_corrente') {
        dataInicial = new Date(hoje.getFullYear(), hoje.getMonth(), 1).toISOString().slice(0,10);
        dataFinal = hoje.toISOString().slice(0,10);
    } else if (tipo === 'mes_passado') {
        const primeiroDiaMesPassado = new Date(hoje.getFullYear(), hoje.getMonth()-1, 1);
        const ultimoDiaMesPassado = new Date(hoje.getFullYear(), hoje.getMonth(), 0);
        dataInicial = primeiroDiaMesPassado.toISOString().slice(0,10);
        dataFinal = ultimoDiaMesPassado.toISOString().slice(0,10);
    }
    document.getElementById('data_inicial').value = dataInicial;
    document.getElementById('data_final').value = dataFinal;
}
function aplicarEmpresas() {
    // Limpa os campos ocultos antigos
    document.querySelectorAll('#hiddenEmpresas input[name="empresa_ids[]"]').forEach(e => e.remove());
    // Adiciona os novos campos ocultos
    let selecionadas = [];
    let nomesEmpresas = [];
    document.querySelectorAll('#modalEmpresas input[type=checkbox]:checked').forEach(cb => {
        if (cb.id === 'empresa_todas') return; // Ignora o checkbox "Todas"
        selecionadas.push(cb.value);
        // Pega o texto do label e extrai a primeira palavra
        let label = cb.nextElementSibling.textContent;
        let primeiraPalavra = label.split(' ')[0];
        nomesEmpresas.push(primeiraPalavra);
        let input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'empresa_ids[]';
        input.value = cb.value;
        document.getElementById('hiddenEmpresas').appendChild(input);
    });
    let textoExibicao = selecionadas.length === 0 ? 'Todas' : nomesEmpresas.join(', ');
    document.getElementById('empresasSelecionadas').value = textoExibicao;
    var modal = bootstrap.Modal.getInstance(document.getElementById('modalEmpresas'));
    modal.hide();
    document.getElementById('filtroDashboard').submit();
}
function limparEmpresas() {
    document.querySelectorAll('#modalEmpresas input[type=checkbox]').forEach(cb => cb.checked = false);
}
// Ao abrir o modal, marcar/desmarcar checkboxes conforme seleção atual
const modalEmpresas = document.getElementById('modalEmpresas');
modalEmpresas.addEventListener('show.bs.modal', function () {
    // Pega os IDs selecionados atualmente
    let selecionadas = Array.from(document.querySelectorAll('#hiddenEmpresas input[name="empresa_ids[]"]')).map(e => e.value);
    
    // Se não há empresas selecionadas via GET, usar as empresas atendidas como padrão
    if (selecionadas.length === 0) {
        // Buscar empresas atendidas da sessão (se disponível)
        // Isso será tratado pelo backend, mas podemos melhorar a UX aqui
        console.log('Nenhuma empresa selecionada via GET, usando seleção automática do backend');
    }
    
    document.querySelectorAll('#modalEmpresas input[type=checkbox]').forEach(cb => {
        cb.checked = selecionadas.includes(cb.value);
    });
    // Atualiza o checkbox "Todas"
    const checks = document.querySelectorAll('#listaEmpresasModal input[type=checkbox]:not(#empresa_todas)');
    const todasMarcadas = Array.from(checks).every(cb => cb.checked);
    document.getElementById('empresa_todas').checked = todasMarcadas;
});
function filtrarEmpresasModal() {
  var input = document.getElementById('buscaEmpresaModal');
  var filtro = input.value.toLowerCase();
  var lista = document.getElementById('listaEmpresasModal');
  var checks = lista.querySelectorAll('.form-check');
  checks.forEach(function(div) {
    var label = div.querySelector('label').innerText.toLowerCase();
    div.style.display = label.includes(filtro) ? '' : 'none';
  });
}
function toggleTodasEmpresas(cb) {
    const checks = document.querySelectorAll('#listaEmpresasModal input[type=checkbox]:not(#empresa_todas)');
    checks.forEach(c => c.checked = cb.checked);
}
// Atualizar o checkbox "Todas" ao marcar/desmarcar individualmente
const checksIndividuais = document.querySelectorAll('#listaEmpresasModal input[type=checkbox]:not(#empresa_todas)');
checksIndividuais.forEach(cb => {
    cb.addEventListener('change', function() {
        const checks = document.querySelectorAll('#listaEmpresasModal input[type=checkbox]:not(#empresa_todas)');
        const todasMarcadas = Array.from(checks).every(c => c.checked);
        document.getElementById('empresa_todas').checked = todasMarcadas;
    });
});
</script>



<div class="row mb-4">
    <div class="col mb-3">
        <div class="card text-center shadow-sm">
            <div class="card-body">
                <h6 class="card-title">Total de Notas</h6>
                <h2 class="text-primary"><?= $totalNotas ?></h2>
                <div class="text-muted" style="font-size: 1rem;">R$ <?= number_format($valorTotal, 2, ',', '.') ?></div>
            </div>
        </div>
    </div>
    <div class="col mb-3">
        <div class="card text-center shadow-sm">
            <div class="card-body">
                <h6 class="card-title">Notas Lançadas</h6>
                <h2 class="text-success"><?= $totalLancadas ?></h2>
                <div class="text-muted" style="font-size: 1rem;">R$ <?= number_format($valorLancadas, 2, ',', '.') ?></div>
            </div>
        </div>
    </div>
    <div class="col mb-3">
        <a href="/auditoria?empresa_id=<?= $empresaIds[0] ?? '' ?>&data_inicial=<?= $data_inicial ?>&data_final=<?= $data_final ?>&status=Romaneio" style="text-decoration: none; color: inherit;">
            <div class="card text-center shadow-sm" style="cursor:pointer;">
                <div class="card-body">
                    <h6 class="card-title">Notas em Romaneio</h6>
                    <h2 style="color:rgb(101, 230, 131) !important;"><?= $totalRomaneio ?></h2>
                    <div class="text-muted" style="font-size: 1rem;">R$ <?= number_format($valorRomaneio, 2, ',', '.') ?></div>
                </div>
            </div>
        </a>
    </div>
    <div class="col mb-3">
        <a href="/auditoria?empresa_id=<?= $empresaIds[0] ?? '' ?>&data_inicial=<?= $data_inicial ?>&data_final=<?= $data_final ?>&status=Pendente" style="text-decoration: none; color: inherit;">
            <div class="card text-center shadow-sm" style="cursor:pointer;">
                <div class="card-body">
                    <h6 class="card-title">Notas Pendentes</h6>
                    <h2 class="text-warning"><?= $totalPendentes ?></h2>
                    <div class="text-muted" style="font-size: 1rem;">R$ <?= number_format($valorPendentes, 2, ',', '.') ?></div>
                </div>
            </div>
        </a>
    </div>
    <div class="col mb-3">
        <a href="/auditoria?empresa_id=<?= $empresaIds[0] ?? '' ?>&data_inicial=<?= $data_inicial ?>&data_final=<?= $data_final ?>&status=Própria" style="text-decoration: none; color: inherit;">
            <div class="card text-center shadow-sm" style="cursor:pointer;">
                <div class="card-body">
                    <h6 class="card-title">Notas Próprias</h6>
                    <h2 style="color: #6f42c1 !important;"><?= $totalProprias ?></h2>
                    <div class="text-muted" style="font-size: 1rem;">R$ <?= number_format($valorProprias, 2, ',', '.') ?></div>
                </div>
            </div>
        </a>
    </div>
</div>
<div class="row mb-4">
    <div class="col mb-3">
        <a href="/auditoria?empresa_id=<?= $empresaIds[0] ?? '' ?>&data_inicial=<?= $data_inicial ?>&data_final=<?= $data_final ?>&status=Cancelada" style="text-decoration: none; color: inherit;">
            <div class="card text-center shadow-sm" style="cursor:pointer;">
                <div class="card-body">
                    <h6 class="card-title">Notas Canceladas</h6>
                    <h2 class="text-danger"><?= $totalCanceladas ?></h2>
                    <div class="text-muted" style="font-size: 1rem;">R$ <?= number_format($valorCanceladas, 2, ',', '.') ?></div>
                </div>
            </div>
        </a>
    </div>
    <div class="col mb-3">
        <a href="/auditoria?empresa_id=<?= $empresaIds[0] ?? '' ?>&data_inicial=<?= $data_inicial ?>&data_final=<?= $data_final ?>&status=Entrada" style="text-decoration: none; color: inherit;">
            <div class="card text-center shadow-sm" style="cursor:pointer;">
                <div class="card-body">
                    <h6 class="card-title">Notas Retornadas</h6>
                    <h2 class="text-info"><?= $totalRetorno ?></h2>
                    <div class="text-muted" style="font-size: 1rem;">R$ <?= number_format($valorRetorno, 2, ',', '.') ?></div>
                </div>
            </div>
        </a>
    </div>
    <div class="col mb-3">
        <a href="/auditoria?empresa_id=<?= $empresaIds[0] ?? '' ?>&data_inicial=<?= $data_inicial ?>&data_final=<?= $data_final ?>&status=Anulada" style="text-decoration: none; color: inherit;">
            <div class="card text-center shadow-sm" style="cursor:pointer;">
                <div class="card-body">
                    <h6 class="card-title">Notas Anuladas</h6>
                    <h2 style="color:rgb(226, 96, 209) !important;"><?= $totalAnuladas ?></h2>
                    <div class="text-muted" style="font-size: 1rem;">R$ <?= number_format($valorAnuladas, 2, ',', '.') ?></div>
                </div>
            </div>
        </a>
    </div>
    <div class="col mb-3">
        <a href="/auditoria?empresa_id=<?= $empresaIds[0] ?? '' ?>&data_inicial=<?= $data_inicial ?>&data_final=<?= $data_final ?>&status=<?= urlencode('Transferência') ?>" style="text-decoration: none; color: inherit;">
            <div class="card text-center shadow-sm" style="cursor:pointer;">
                <div class="card-body">
                    <h6 class="card-title">Notas de Transferência</h6>
                    <h2 style="color: #8B4513 !important;"><?= $totalTransferencia ?? 0 ?></h2>
                    <div class="text-muted" style="font-size: 1rem;">R$ <?= number_format($valorTransferencia ?? 0, 2, ',', '.') ?></div>
                </div>
            </div>
        </a>
    </div>
    <div class="col mb-3">
        <a href="/auditoria?empresa_id=<?= $empresaIds[0] ?? '' ?>&data_inicial=<?= $data_inicial ?>&data_final=<?= $data_final ?>&status=Desconhecimento" style="text-decoration: none; color: inherit;">
            <div class="card text-center shadow-sm" style="cursor:pointer;">
                <div class="card-body">
                    <h6 class="card-title">Notas Desconhecidas</h6>
                    <h2 class="text-secondary"><?= $totalDesconhecidas ?></h2>
                    <div class="text-muted" style="font-size: 1rem;">R$ <?= number_format($valorDesconhecidas, 2, ',', '.') ?></div>
                </div>
            </div>
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title">Notas Lançadas por Dia</h5>
                <canvas id="graficoLinhas"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title">Origem das Notas (UF)</h5>
                <div id="graficoMapa" style="height: 350px;"></div>
            </div>
        </div>
    </div>
</div>
<!-- Gráfico de pizza: Funcionário que mais escriturou notas e quem mais coletou -->
<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title">Escriturador</h5>
                <canvas id="graficoEscriturador" style="max-height:350px;"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title">Conferente</h5>
                <canvas id="graficoColeta" style="max-height:350px;"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title">Top 10 Fornecedores em Valor</h5>
                <canvas id="graficoTopValor"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title">Top 10 Fornecedores Mais Recorrentes</h5>
                <canvas id="graficoTopRecorrentes"></canvas>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://www.gstatic.com/charts/loader.js"></script>
<script>
    // Gráfico de linhas - Notas lançadas por dia
    const notasPorDia = <?= json_encode($notasPorDia) ?>;
    const labelsLinhas = notasPorDia.map(item => item.dia);
    const dataLinhas = notasPorDia.map(item => parseInt(item.total));
    new Chart(document.getElementById('graficoLinhas'), {
        type: 'line',
        data: {
            labels: labelsLinhas,
            datasets: [{
                label: 'Notas Lançadas',
                data: dataLinhas,
                borderColor: '#198754',
                backgroundColor: 'rgba(25,135,84,0.1)',
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    // Gráfico de mapa - Notas por UF
    google.charts.load('current', {
        'packages':['geochart'],
        'mapsApiKey': ''
    });
    google.charts.setOnLoadCallback(drawRegionsMap);
    function drawRegionsMap() {
        var data = google.visualization.arrayToDataTable([
            ['State', 'Notas'],
            <?php foreach ($notasPorUF as $uf): ?>
                ['BR-<?= $uf['uf'] ?>', <?= $uf['total'] ?>],
            <?php endforeach; ?>
        ]);
        var options = {
            region: 'BR',
            displayMode: 'regions',
            resolution: 'provinces',
            colorAxis: {colors: ['#e0f2f1', '#198754']},
            backgroundColor: '#f8f9fa',
            datalessRegionColor: '#f1f1f1',
            defaultColor: '#f5f5f5',
        };
        var chart = new google.visualization.GeoChart(document.getElementById('graficoMapa'));
        chart.draw(data, options);
    }

    // Gráfico de colunas - Top 10 fornecedores em valor
    const topValor = <?= json_encode($topFornecedoresValor) ?>;
    const labelsTopValor = topValor.map(item => (item.razao_social || item.cnpj));
    const dataTopValor = topValor.map(item => parseFloat(item.total));
    new Chart(document.getElementById('graficoTopValor'), {
        type: 'bar',
        data: {
            labels: labelsTopValor,
            datasets: [{
                label: 'Valor Total (R$)',
                data: dataTopValor,
                backgroundColor: '#0d6efd',
            }]
        },
        options: {
            indexAxis: 'x',
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    // Gráfico de barras - Top 10 fornecedores mais recorrentes
    const topRecorrentes = <?= json_encode($topFornecedoresRecorrentes) ?>;
    const labelsTopRec = topRecorrentes.map(item => (item.razao_social || item.cnpj));
    const dataTopRec = topRecorrentes.map(item => parseInt(item.total));
    new Chart(document.getElementById('graficoTopRecorrentes'), {
        type: 'bar',
        data: {
            labels: labelsTopRec,
            datasets: [{
                label: 'Quantidade de Notas',
                data: dataTopRec,
                backgroundColor: '#ffc107',
            }]
        },
        options: {
            indexAxis: 'y',
            plugins: {
                legend: { display: false }
            },
            scales: {
                x: { beginAtZero: true }
            }
        }
    });

    // Gráfico de pizza - Funcionário que mais escriturou notas
    const notasPorEscriturador = <?= json_encode($notasPorEscriturador) ?>;
    const labelsEscriturador = notasPorEscriturador.map(item => item.escriturador || 'Não informado');
    const dataEscriturador = notasPorEscriturador.map(item => parseInt(item.total));
    const backgroundColors = [
        '#0d6efd', '#198754', '#ffc107', '#dc3545', '#6f42c1', '#fd7e14', '#20c997', '#6610f2', '#e83e8c', '#8B4513', '#343a40', '#adb5bd'
    ];
    new Chart(document.getElementById('graficoEscriturador'), {
        type: 'pie',
        data: {
            labels: labelsEscriturador,
            datasets: [{
                data: dataEscriturador,
                backgroundColor: backgroundColors,
            }]
        },
        options: {
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });

    // Gráfico de pizza - Funcionário que mais coletou notas
    const notasPorColeta = <?= json_encode($notasPorColeta) ?>;
    const labelsColeta = notasPorColeta.map(item => item.coleta || 'Não informado');
    const dataColeta = notasPorColeta.map(item => parseInt(item.total));
    new Chart(document.getElementById('graficoColeta'), {
        type: 'doughnut',
        data: {
            labels: labelsColeta,
            datasets: [{
                data: dataColeta,
                backgroundColor: backgroundColors,
            }]
        },
        options: {
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
</script>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?> 