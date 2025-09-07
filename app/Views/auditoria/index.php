<?php $title = 'Auditoria de Notas Fiscais'; if (!isset($total_paginas)) $total_paginas = 1;
$notasPorPagina = 10;
$totalNotasTabela = count($notas);
$paginaTabela = isset($_GET['pagina_tabela']) ? max(1, intval($_GET['pagina_tabela'])) : 1;
$inicio = ($paginaTabela - 1) * $notasPorPagina;
$notasPaginadas = array_slice($notas, $inicio, $notasPorPagina);
$totalPaginasTabela = max(1, ceil($totalNotasTabela / $notasPorPagina));
?>


<h2 class="mb-3">Auditoria de Notas</h2> 
<div class="card mb-4">
    <div class="card-body">
        <form class="row g-3" method="GET" action="/auditoria" id="formFiltros">
            <div class="col-12 col-md-4">
                <label for="empresa_id" class="form-label">Empresa</label>
                <select class="form-select" id="empresa_id" name="empresa_id">
                    <?php
                    // Ordena as empresas por razão social
                    usort($empresas, function($a, $b) {
                        return strcmp(mb_strtoupper($a['razao_social']), mb_strtoupper($b['razao_social']));
                    });
                    foreach ($empresas as $empresa): ?>
                    
                        <option value="<?= $empresa['id'] ?>" <?= $empresa_id == $empresa['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($empresa['razao_social']) ?>
                            <?php if (($empresa['tipo_empresa'] ?? '') === 'matriz'): ?> - Matriz<?php elseif (($empresa['tipo_empresa'] ?? '') === 'filial'): ?> - Filial<?php endif; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-6 col-md-3">
                <label for="data_inicial" class="form-label">Data Inicial</label>
                <input type="date" class="form-control" id="data_inicial" name="data_inicial" value="<?= htmlspecialchars($data_inicial) ?>">
            </div>
            <div class="col-6 col-md-3">
                <label for="data_final" class="form-label">Data Final</label>
                <input type="date" class="form-control" id="data_final" name="data_final" value="<?= htmlspecialchars($data_final) ?>">
            </div>
            <div class="col-12 col-md-2 d-flex align-items-end">
                <!-- Botão de filtrar removido -->
            </div>
            <div class="col-12">
                <input type="text" class="form-control" name="busca" id="busca" placeholder="Buscar por Número, Chave, Valor ou CNPJ" value="<?= htmlspecialchars($busca ?? '') ?>">
            </div>
            <div class="col-12 d-flex flex-wrap gap-2">
                <button type="submit" name="filtro_rapido" value="ultimos_30" class="btn btn-outline-secondary btn-sm<?= ($filtro_rapido === 'ultimos_30' ? ' active' : '') ?>">Últimos 30 dias</button>
                <button type="submit" name="filtro_rapido" value="ultimos_60" class="btn btn-outline-secondary btn-sm<?= ($filtro_rapido === 'ultimos_60' ? ' active' : '') ?>">Últimos 60 dias</button>
                <button type="submit" name="filtro_rapido" value="este_mes" class="btn btn-outline-secondary btn-sm<?= ($filtro_rapido === 'este_mes' ? ' active' : '') ?>">Este Mês</button>
                <button type="submit" name="filtro_rapido" value="mes_passado" class="btn btn-outline-secondary btn-sm<?= ($filtro_rapido === 'mes_passado' ? ' active' : '') ?>">Mês Passado</button>
            </div>
            <div class="col-12 d-flex flex-wrap gap-2">
                <button type="button" class="btn btn-sistema btn-sm" id="btn-relatorio"><i class="bi bi-file-earmark-text"></i> Relatório</button>
                <button type="button" class="btn btn-sistema btn-sm" id="btn-visualizar-danfe"><i class="bi bi-file-earmark-pdf"></i> Visualizar nota</button>
                <button type="button" class="btn btn-sistema btn-sm" id="btn-baixar-xml"><i class="bi bi-file-earmark-code"></i> Baixar XML</button>
                <button type="button" class="btn btn-sistema btn-sm" id="btn-whatsapp"><i class="bi bi-whatsapp"></i> Enviar via WhatsApp</button>
                <button type="button" class="btn btn-sistema btn-sm" id="btn-status-modal"><i class="bi bi-funnel"></i> Filtrar por Status</button>
                <button type="button" class="btn btn-sistema btn-sm" id="btn-limpar"><i class="bi bi-eraser"></i> Limpar</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
            <h5 class="card-title mb-0">Notas Fiscais Encontradas</h5>
            <button type="button" class="btn btn-outline-primary btn-sm" id="btn-colunas"><i class="bi bi-layout-three-columns"></i> Selecionar Colunas</button>
        </div>
        <div id="colunas-modal" class="modal" tabindex="-1" style="display:none; background:rgba(0,0,0,0.3); position:fixed; top:0; left:0; width:100vw; height:100vh; z-index:1050;">
            <div class="modal-dialog" style="margin-top:10vh;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Selecionar Colunas</h5>
                        <button type="button" class="btn-close" id="fechar-modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="form-colunas" class="row g-2">
                            <div class="col-12">
                                <label><input type="checkbox" class="col-toggle" data-col="0" checked> Seleção</label>
                            </div>
                            <div class="col-12">
                                <label><input type="checkbox" class="col-toggle" data-col="1" checked> N° NFe</label>
                            </div>
                            <div class="col-12">
                                <label><input type="checkbox" class="col-toggle" data-col="2" checked> CNPJ</label>
                            </div>
                            <div class="col-12">
                                <label><input type="checkbox" class="col-toggle" data-col="3" checked> Razão Social</label>
                            </div>
                            <div class="col-12">
                                <label><input type="checkbox" class="col-toggle" data-col="4" checked> Emissão</label>
                            </div>
                            <div class="col-12">
                                <label><input type="checkbox" class="col-toggle" data-col="5" checked> Valor</label>
                            </div>
                            <div class="col-12">
                                <label><input type="checkbox" class="col-toggle" data-col="6" checked> Chave</label>
                            </div>
                            <div class="col-12">
                                <label><input type="checkbox" class="col-toggle" data-col="7" checked> UF</label>
                            </div>
                            <div class="col-12">
                                <label><input type="checkbox" class="col-toggle" data-col="8" checked> Situação</label>
                            </div>
                            <div class="col-12">
                                <label><input type="checkbox" class="col-toggle" data-col="9" checked> Tipo</label>
                            </div>
                            <div class="col-12">
                                <label><input type="checkbox" class="col-toggle" data-col="10" checked> Destino</label>
                            </div>
                            <div class="col-12">
                                <label><input type="checkbox" class="col-toggle" data-col="11" checked> Vencimento</label>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" id="fechar-modal2">Fechar</button>
                    </div>
                </div>
            </div>
        </div>
        <?php
        $temNotasValidas = !empty($notas) && is_array($notas) && isset(array_values($notas)[0]['id']);
        if (!$temNotasValidas): ?>
            <div class="alert alert-info text-center" style="font-size:1.1rem; margin: 32px 0;">
                <i class="bi bi-info-circle" style="font-size:1.5rem;"></i><br>
                Nenhuma nota fiscal encontrada para os filtros selecionados.<br>
                Tente ajustar os filtros ou ampliar o período de busca.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle" id="tabela-auditoria">
                    <thead class="table-light">
                        <tr>
                            <th style="width:40px;"></th>
                            <th>N° NFe</th>
                            <th>CNPJ</th>
                            <th>Razão Social</th>
                            <th>Emissão</th>
                            <th>Valor</th>
                            <th>Chave</th>
                            <th>UF</th>
                            <th>Situação</th>
                            <th>Tipo</th>
                            <th>Destino</th>
                            <th>Vencimento</th>
                            <th>Detalhes</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($notasPaginadas as $idx => $nota): ?>
                            <?php if (!is_array($nota) || !isset($nota['id'])) continue; ?>
                            <?php
                                $classeExtra = '';
                                if (isset($nota['situacao']) && mb_strtolower($nota['situacao']) === 'cancelada') {
                                    $classeExtra = 'cancelada-nota';
                                } elseif (isset($nota['tipo']) && mb_strtolower($nota['tipo']) === 'entrada') {
                                    $classeExtra = 'entrada-nota';
                                }
                            ?>
                            <tr class="<?= $classeExtra ?>" data-nota='<?= json_encode(array_merge($nota, ['id' => (int)($nota['id'] ?? 0)]), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>' data-nota-id="<?= (int)($nota['id'] ?? 0) ?>">
                                <td><input type="radio" name="nota_selecionada" value="<?= htmlspecialchars($nota['id'] ?? '') ?>"></td>
                                <td><?= htmlspecialchars($nota['numero']) ?></td>
                                <td><?= htmlspecialchars($nota['cnpj'] ?? '00.000.000/0000-00') ?></td>
                                <td><?= htmlspecialchars($nota['razao_social'] ?? 'Empresa Exemplo') ?></td>
                                <td><?php
                                    $data = $nota['data_emissao'] ?? '';
                                    if ($data && $data !== '0000-00-00 00:00:00' && $data !== '0000-00-00') {
                                        $dt = DateTime::createFromFormat('Y-m-d H:i:s', $data);
                                        if (!$dt) {
                                            $dt = DateTime::createFromFormat('Y-m-d', $data);
                                        }
                                        if (!$dt) {
                                            $dt = DateTime::createFromFormat('d/m/Y', $data); // caso já venha formatado
                                        }
                                        if ($dt) {
                                            echo $dt->format('d-m-Y');
                                        } else {
                                            echo htmlspecialchars($data);
                                        }
                                    } else {
                                        echo '-';
                                    }
                                ?></td>
                                <td>R$ <?= htmlspecialchars($nota['valor']) ?></td>
                                <td><?= htmlspecialchars($nota['chave'] ?? '00000000000000000000000000000000000000000000') ?></td>
                                <td><?= htmlspecialchars($nota['uf'] ?? 'SP') ?></td>
                                <td><?= htmlspecialchars($nota['situacao']) ?></td>
                                <td><?= htmlspecialchars($nota['tipo'] ?? 'Entrada') ?></td>
                                <td>
                                    <select class="form-select form-select-sm destino-select" name="destino[<?= $idx ?>]" data-nota-id="<?= $nota['id'] ?>">
                                        <option value="">Selecione</option>
                                        <?php foreach ($destinos as $dest): ?>
                                            <option value="<?= $dest['id'] ?>" <?= (isset($nota['destino_id']) && $nota['destino_id'] == $dest['id']) ? 'selected' : '' ?>><?= htmlspecialchars($dest['destino']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td>
                                    <div id="vencimentos-<?= $idx ?>" class="vencimentos-box" data-nota-id="<?= $nota['id'] ?>">
                                        <!-- Os inputs de vencimento serão preenchidos via JS, mas sempre mostrar pelo menos um campo -->
                                        <div class="input-group mb-1 vencimento-group">
                                            <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                                            <input type="date" class="form-control form-control-sm venc-input" name="vencimento[<?= $nota['id'] ?>][]">
                                            <button type="button" class="btn btn-outline-secondary btn-sm add-vencimento" data-idx="<?= $nota['id'] ?>">+</button>
                                        </div>
                                    </div>
                                </td>
                                <td style="white-space:nowrap; text-align:right;">
                                    <button type="button" class="salvar-destino-venc btn-auditoria me-0" data-nota-id="<?= $nota['id'] ?>" title="Salvar destino e vencimentos" style="background:none; border:none; padding:0; margin:0 6px 0 0; color:#2c3e50; vertical-align:middle; cursor:pointer; width:25px !important; min-width:25px !important; max-width:25px !important; height:26.53px !important; min-height:26.53px !important; max-height:26.53px !important; display:flex; align-items:center; justify-content:center; line-height:1;">
                                        <i class="bi bi-save" style="font-size:1.4em; width:100%; height:100%; display:flex; align-items:center; justify-content:center; pointer-events:none;"></i>
                                    </button>
                                    <button type="button" class="btn-detalhes btn-auditoria me-0" title="Detalhes da nota" style="background:none; border:none; padding:0; margin:0 6px 0 0; color:#2c3e50; vertical-align:middle; cursor:pointer; width:25px !important; min-width:25px !important; max-width:25px !important; height:26.53px !important; min-height:26.53px !important; max-height:26.53px !important; display:flex; align-items:center; justify-content:center; line-height:1;">
                                        <i class="bi bi-search" style="font-size:1.3em; width:100%; height:100%; display:flex; align-items:center; justify-content:center; pointer-events:none;"></i>
                                    </button>
                                    <?php if (!empty($nota['tem_observacao'])): ?>
                                        <button type="button" class="btn-observacao" title="Possui observação" disabled style="background:none; border:none; padding:0; margin:0; color:#f39c12; vertical-align:middle; pointer-events:none; opacity:0.8; width:25px !important; min-width:25px !important; max-width:25px !important; height:26.53px !important; min-height:26.53px !important; max-height:26.53px !important; display:flex; align-items:center; justify-content:center; line-height:1;">
                                            <i class="bi bi-chat-left-text-fill" style="font-size:1.3em; width:100%; height:100%; display:flex; align-items:center; justify-content:center; pointer-events:none;"></i>
                                        </button>
                                    <?php endif; ?>
                                </td>
                                <td><span class="badge badge-status align-middle d-inline-flex justify-content-center align-items-center 
                                <?php
                                    if (isset($nota['status_lancamento']) && mb_strtolower($nota['status_lancamento']) === 'cancelada') {
                                        echo 'bg-danger';
                                    } elseif (isset($nota['status_lancamento']) && mb_strtolower($nota['status_lancamento']) === 'própria') {
                                        echo 'badge-propria';
                                    } elseif (isset($nota['status_lancamento']) && mb_strtolower($nota['status_lancamento']) === 'anulada') {
                                        echo 'badge-anulada';
                                    } elseif (isset($nota['tipo']) && mb_strtolower($nota['tipo']) === 'entrada') {
                                        echo 'bg-primary';
                                    } elseif ($nota['status_lancamento'] === 'Lançada') {
                                        echo 'bg-success';
                                    } elseif ($nota['status_lancamento'] === 'Desconhecimento') {
                                        echo 'bg-secondary';
                                    } elseif ($nota['status_lancamento'] === 'Romaneio') {
                                        echo 'badge-romaneio';
                                    } elseif ($nota['status_lancamento'] === 'Transferência') {
                                        echo 'bg-info';
                                    } else {
                                        echo 'bg-warning text-dark';
                                    }
                                ?>">
                                <?php
                                    if (isset($nota['status_lancamento']) && mb_strtolower($nota['status_lancamento']) === 'cancelada') {
                                        echo 'Cancelada';
                                    } elseif (isset($nota['status_lancamento']) && mb_strtolower($nota['status_lancamento']) === 'própria') {
                                        echo 'Própria';
                                    } elseif (isset($nota['status_lancamento']) && mb_strtolower($nota['status_lancamento']) === 'anulada') {
                                        echo 'Anulada';
                                    } elseif (isset($nota['tipo']) && mb_strtolower($nota['tipo']) === 'entrada') {
                                        echo 'Entrada';
                                    } else {
                                        echo htmlspecialchars($nota['status_lancamento']);
                                    }
                                ?>
                                </span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php if ($totalPaginasTabela > 1): ?>
<nav aria-label="Navegação de páginas de notas">
    <ul class="pagination justify-content-center mt-3">
        <?php if ($paginaTabela > 1): ?>
            <li class="page-item">
                <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['pagina_tabela' => $paginaTabela - 1])) ?>">Anterior</a>
            </li>
        <?php endif; ?>

        <?php
        $range = 2; // Quantidade de páginas antes e depois da atual
        $start = max(1, $paginaTabela - $range);
        $end = min($totalPaginasTabela, $paginaTabela + $range);

        if ($start > 1) {
            // Primeira página
            echo '<li class="page-item"><a class="page-link" href="?' . http_build_query(array_merge($_GET, ['pagina_tabela' => 1])) . '">1</a></li>';
            if ($start > 2) {
                echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
        }

        for ($i = $start; $i <= $end; $i++) {
            $active = $i == $paginaTabela ? 'active' : '';
            echo '<li class="page-item ' . $active . '"><a class="page-link" href="?' . http_build_query(array_merge($_GET, ['pagina_tabela' => $i])) . '">' . $i . '</a></li>';
        }

        if ($end < $totalPaginasTabela) {
            if ($end < $totalPaginasTabela - 1) {
                echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
            // Última página
            echo '<li class="page-item"><a class="page-link" href="?' . http_build_query(array_merge($_GET, ['pagina_tabela' => $totalPaginasTabela])) . '">' . $totalPaginasTabela . '</a></li>';
        }
        ?>

        <?php if ($paginaTabela < $totalPaginasTabela): ?>
            <li class="page-item">
                <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['pagina_tabela' => $paginaTabela + 1])) ?>">Próxima</a>
            </li>
        <?php endif; ?>
    </ul>
</nav>
<?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php if (!empty($notas) && $total_paginas > 1): ?>
<div class="card mt-3">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                Total de notas: <?= $total_notas ?>
            </div>
            <nav aria-label="Navegação de páginas">
                <ul class="pagination mb-0">
                    <?php if ($pagina_atual > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['pagina' => $pagina_atual - 1])) ?>" aria-label="Anterior">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php
                    $inicio = max(1, $pagina_atual - 2);
                    $fim = min($total_paginas, $pagina_atual + 2);

                    if ($inicio > 1) {
                        echo '<li class="page-item"><a class="page-link" href="?' . http_build_query(array_merge($_GET, ['pagina' => 1])) . '">1</a></li>';
                        if ($inicio > 2) {
                            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                        }
                    }

                    for ($i = $inicio; $i <= $fim; $i++):
                    ?>
                        <li class="page-item <?= $i == $pagina_atual ? 'active' : '' ?>">
                            <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['pagina' => $i])) ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>

                    <?php
                    if ($fim < $total_paginas) {
                        if ($fim < $total_paginas - 1) {
                            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                        }
                        echo '<li class="page-item"><a class="page-link" href="?' . http_build_query(array_merge($_GET, ['pagina' => $total_paginas])) . '">' . $total_paginas . '</a></li>';
                    }
                    ?>

                    <?php if ($pagina_atual < $total_paginas): ?>
                        <li class="page-item">
                            <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['pagina' => $pagina_atual + 1])) ?>" aria-label="Próximo">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Modal único para detalhes -->
<div id="modalDetalhesUnico" style="display:none; background:rgba(0,0,0,0.3); position:fixed; top:0; left:0; width:100vw; height:100vh; z-index:9999; overflow-y: auto;">
    <div class="modal-dialog modal-dialog-scrollable" style="margin:3vh auto; max-width:60%; min-width:300px; width:60%; max-height:94vh;">
        <div class="modal-content" style="border-radius:14px; box-shadow:0 4px 32px rgba(0,0,0,0.15); background:#f7f8fa; max-height:96vh; display:flex; flex-direction:column;">
            <div class="modal-header" style="border-bottom:1px solid #dee2e6; background:#fff; align-items:center; display:flex; gap:16px; flex-shrink:0;">
                <img src="public/assets/images/logo.png" alt="Logo" style="height:40px; width:auto; margin-right:8px;">
                <h5 class="modal-title w-100 text-center" id="modalDetalhesTitulo" style="font-weight:bold; margin:0;">Detalhes da Escrituração Fiscal</h5>
                <button type="button" class="btn-close fechar-modal-detalhes" style="position:absolute; right:16px; top:16px;"></button>
            </div>
            <div class="modal-body" style="padding:12px 16px 8px 16px; background:#f7f8fa; overflow-y:auto; flex-grow:1;">
                <div class="row g-2">
                    <!-- Coluna Esquerda - Informações Estruturadas -->
                    <div class="col-lg-6 col-md-12">
                        <!-- Informações Essenciais -->
                        <div class="card mb-2" style="border-radius:10px; box-shadow:0 2px 8px rgba(0,0,0,0.04); border:1px solid #e5e7eb; background:#fff; padding:10px 12px;">
                            <h6 class="mb-2" style="font-weight:bold; color:#495057; font-size:1rem;">Informações Essenciais</h6>
                            <div class="row g-2">
                                <div class="col-12">
                                    <label class="form-label mb-0">Escriturado por:</label>
                                    <div id="det-escriturado_por" class="form-control bg-light">---</div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label mb-0">Data e hora da escrituração:</label>
                                    <div id="det-data_escrituracao" class="form-control bg-light">---</div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label mb-0">Prazo para escrituração:</label>
                                    <div id="det-prazo_escrituracao" class="form-control bg-light">---</div>
                                </div>
                            </div>
                        </div>

                        <!-- Dados da escrituração -->
                        <div class="card mb-2" style="border-radius:10px; box-shadow:0 2px 8px rgba(0,0,0,0.04); border:1px solid #e5e7eb; background:#fff; padding:10px 12px;">
                            <h6 class="mb-2" style="font-weight:bold; color:#495057; font-size:1rem;">Dados da escrituração</h6>
                            <div class="row g-2">
                                <div class="col-12">
                                    <label class="form-label mb-0">Número Fiscal:</label>
                                    <div id="det-numero_fiscal" class="form-control bg-light">---</div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label mb-0">Número do Processo:</label>
                                    <div id="det-numero_processo" class="form-control bg-light">---</div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label mb-0">Número do Financeiro:</label>
                                    <div id="det-numero_financeiro" class="form-control bg-light">---</div>
                                </div>
                            </div>
                        </div>

                        <!-- Fornecedor -->
                        <div class="card mb-2" style="border-radius:10px; box-shadow:0 2px 8px rgba(0,0,0,0.04); border:1px solid #e5e7eb; background:#fff; padding:10px 12px;">
                            <h6 class="mb-2" style="font-weight:bold; color:#495057; font-size:1rem;">Fornecedor</h6>
                            <div class="row g-2">
                                <div class="col-12">
                                    <label class="form-label mb-0">Código:</label>
                                    <div id="det-codigo_fornecedor" class="form-control bg-light">---</div>
                                </div>
                            </div>
                        </div>

                        <!-- Conferido por -->
                        <div class="card mb-2" style="border-radius:10px; box-shadow:0 2px 8px rgba(0,0,0,0.04); border:1px solid #e5e7eb; background:#fff; padding:10px 12px;">
                            <h6 class="mb-2" style="font-weight:bold; color:#495057; font-size:1rem;">Conferido por</h6>
                            <div class="row g-2">
                                <div class="col-12">
                                    <label class="form-label mb-0">Conferente:</label>
                                    <div id="det-conferente" class="form-control bg-light">---</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Coluna Direita - Observações -->
                    <div class="col-lg-6 col-md-12">
                        <div class="card h-100" style="border-radius:10px; box-shadow:0 2px 8px rgba(0,0,0,0.04); border:1px solid #e5e7eb; background:#fff; padding:10px 12px; display:flex; flex-direction:column;">
                            <h6 class="mb-2" style="font-weight:bold; color:#495057; font-size:1rem;">Observações</h6>
                            <div class="row g-2 flex-grow-1" style="display:flex; flex-direction:column;">
                                <div class="col-12 flex-grow-1" style="display:flex; flex-direction:column;">
                                    <div id="chat-observacoes" class="border rounded p-3 mb-3 flex-grow-1" style="min-height: 250px; max-height: 350px; overflow-y: auto; background:#f8f9fa;">
                                        <!-- As observações serão inseridas aqui via JavaScript -->
                                    </div>
                                    <div class="mt-auto">
                                        <textarea class="form-control" id="det-observacoes" rows="3" style="resize: vertical; min-height: 60px; max-height: 120px;" placeholder="Digite suas observações aqui..."></textarea>
                                        <span id="msg-observacao" class="text-success ms-2" style="display:none;">Salvo!</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer" style="background:#f8f9fa; border-top:1px solid #dee2e6; display:flex; justify-content:flex-end; gap:8px; padding:12px 16px; flex-shrink:0;">
                <button type="button" class="btn btn-success btn-sm" id="btn-salvar-observacao">Salvar</button>
                <button type="button" class="btn btn-secondary btn-sm fechar-modal-detalhes">Cancelar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal customizado para filtro de status -->
<div id="modalStatusFiltro" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.25); z-index:2000; align-items:center; justify-content:center;">
    <div style="background:#fff; border-radius:10px; box-shadow:0 4px 32px rgba(0,0,0,0.15); padding:32px 24px 24px 24px; min-width:260px; max-width:90vw; margin:auto; position:relative;">
        <h5 style="font-weight:bold; margin-bottom:18px; text-align:center;">Filtrar por Status</h5>
        <div class="d-flex flex-column gap-2 mb-3">
            <button class="btn btn-sistema btn-status-option" data-status="todos">Todos</button>
            <button class="btn btn-sistema btn-status-option" data-status="Pendente">Pendentes</button>
            <button class="btn btn-sistema btn-status-option" data-status="Lançada">Lançadas</button>
            <button class="btn btn-sistema btn-status-option" data-status="Desconhecimento">Desconhecimento</button>
            <button class="btn btn-sistema btn-status-option" data-status="Romaneio">Romaneio</button>
            <button class="btn btn-sistema btn-status-option" data-status="Cancelada">Canceladas</button>
            <button class="btn btn-sistema btn-status-option" data-status="Anulada">Anuladas</button>
            <button class="btn btn-sistema btn-status-option" data-status="Entrada">Entradas</button>
            <button class="btn btn-sistema btn-status-option" data-status="Própria">Própria</button>
            <button class="btn btn-sistema btn-status-option" data-status="Transferência">Transferência</button>
        </div>
        <button class="btn btn-sistema w-100" id="btn-fechar-status-modal">Fechar</button>
    </div>
</div>

<!-- Modal de Relatório -->
<div id="modalRelatorios" class="modal" tabindex="-1" style="display:none; background:rgba(0,0,0,0.3); position:fixed; top:0; left:0; width:100vw; height:100vh; z-index:1050;">
    <div class="modal-dialog" style="margin-top:10vh; max-width:400px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Gerar Relatório</h5>
                <button type="button" class="btn-close" id="fechar-modal-relatorio"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2">Selecione o formato do relatório:</div>
                <div class="d-flex flex-column gap-2">
                    <button class="btn btn-sistema btn-status-relatorio" data-formato="pdf"><i class="bi bi-file-earmark-pdf"></i> PDF</button>
                    <button class="btn btn-sistema btn-status-relatorio" data-formato="csv"><i class="bi bi-file-earmark-spreadsheet"></i> CSV</button>
                    <button class="btn btn-sistema btn-status-relatorio" data-formato="txt"><i class="bi bi-file-earmark-text"></i> TXT</button>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" id="fechar-modal-relatorio2">Fechar</button>
            </div>
        </div>
    </div>
</div>

<style>

 
.btn-auditoria {
    min-width: 80px;
    width: 80px;
    font-size: 0.85rem;
    height: 28px;
    padding-top: 2px;
    padding-bottom: 2px;
    margin-bottom: 2px;
}
.btn-sistema {
    background: #2c3e50 !important;
    color: #fff !important;
    border: none !important;
    box-shadow: 0 1px 2px rgba(44,62,80,0.07);
    transition: background 0.2s;
}
.btn-sistema:hover, .btn-sistema:focus {
    background: #34495e !important;
    color: #fff !important;
}
.table-responsive table {
    font-size: 0.85rem;
    /* Reduz a altura das linhas */
}
.table-responsive th,
.table-responsive td {
    padding: 0.25rem 0.5rem !important; /* padding menor */
    height: 32px !important; /* altura mínima menor */
    vertical-align: middle !important;
}

/* Para telas menores, mantém compacto */
@media (max-width: 1200px) {
    .table-responsive th,
    .table-responsive td {
        padding: 0.18rem 0.3rem !important;
        height: 28px !important;
    }
}
@media (max-width: 992px) {
    .table-responsive {
        font-size: 0.8rem;
    }
    
    .table-responsive th,
    .table-responsive td {
        padding: 0.4rem;
    }
    
    .form-label {
        font-size: 0.9rem;
    }
    
    .form-control,
    .form-select {
        font-size: 0.9rem;
        padding: 0.375rem 0.75rem;
    }
}
@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.75rem;
    }
    
    .table-responsive th,
    .table-responsive td {
        padding: 0.3rem;
    }
    
    .card-body {
        padding: 1rem;
    }
    
    .btn-sm {
        padding: 0.2rem 0.4rem;
        font-size: 0.7rem;
    }
    
    .form-label {
        font-size: 0.85rem;
    }
    
    .form-control,
    .form-select {
        font-size: 0.85rem;
        padding: 0.3rem 0.6rem;
    }
    
    .badge-status {
        min-width: 60px;
        width: 60px;
        height: 24px;
        font-size: 0.7rem;
    }
}
.badge-status {
    min-width: 80px;
    width: 80px;
    height: 28px;
    font-size: 2rem;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 6px;
    margin-bottom: 2px;
    padding-top: 2px;
    padding-bottom: 2px;
}

/* Estilos para o dropdown */
.dropdown-menu {
    min-width: 200px;
    padding: 0.5rem 0;
    margin: 0;
    border: 1px solid rgba(0,0,0,.15);
    border-radius: 0.25rem;
    box-shadow: 0 0.5rem 1rem rgba(0,0,0,.175);
}

.dropdown-item {
    padding: 0.5rem 1rem;
    clear: both;
    font-weight: 400;
    color: #212529;
    text-align: inherit;
    white-space: nowrap;
    background-color: transparent;
    border: 0;
}

.dropdown-item:hover, .dropdown-item:focus {
    color: #16181b;
    text-decoration: none;
    background-color: #f8f9fa;
}

.dropdown-item.active, .dropdown-item:active {
    color: #fff;
    text-decoration: none;
    background-color: #2c3e50;
}

.dropdown-divider {
    height: 0;
    margin: 0.5rem 0;
    overflow: hidden;
    border-top: 1px solid #e9ecef;
}

#modalStatusFiltro {
    display: none;
    position: fixed;
    top: 0; left: 0;
    width: 100vw; height: 100vh;
    background: rgba(0,0,0,0.25);
    z-index: 2000;
    align-items: center;
    justify-content: center;
}
#modalStatusFiltro[style*="display: flex"] {
    display: flex !important;
}
#modalStatusFiltro .btn-status-option {
    font-size: 1.1rem;
    padding: 0.5rem 0;
}
#modalStatusFiltro .btn-status-option[data-status="Pendente"] {
    color: #856404;
    border-color: #ffeeba;
    background: #fffbe6;
}
#modalStatusFiltro .btn-status-option[data-status="Lançada"] {
    color: #155724;
    border-color: #c3e6cb;
    background: #e6ffed;
}
#modalStatusFiltro .btn-status-option[data-status="todos"] {
    color: #004085;
    border-color: #b8daff;
    background: #e9f7fe;
}
#modalStatusFiltro .btn-status-option.active, #modalStatusFiltro .btn-status-option:focus {
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
    outline: none;
}
#modalStatusFiltro .btn-secondary {
    margin-top: 10px;
}

/* Estilos para os botões de formato e colunas */
.form-check {
    margin: 0;
    padding: 0;
}

.form-check-input[type="radio"] {
    margin-right: 4px;
}

.form-check-label {
    padding: 6px 12px;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.2s;
    background: #fff;
    margin: 0;
}

.form-check-input[type="radio"]:checked + .form-check-label {
    background: #2c3e50;
    color: #fff;
    border-color: #2c3e50;
}

.form-check-input[type="checkbox"]:checked + .form-check-label {
    background: #2c3e50;
    color: #fff;
    border-color: #2c3e50;
}

.form-check-label:hover {
    background: #f8f9fa;
}

.form-check-input[type="radio"]:checked + .form-check-label:hover,
.form-check-input[type="checkbox"]:checked + .form-check-label:hover {
    background: #34495e;
}

/* Ajuste para o modal de relatórios */
#modalRelatorios .modal-body {
    max-height: 80vh;
    overflow-y: auto;
}

#modalRelatorios .form-check {
    margin-bottom: 4px;
}

/* Estilos para os botões de status no modal de relatórios */
.btn-status-relatorio {
    flex: 1;
    font-size: 0.9rem;
    padding: 0.5rem;
    transition: all 0.2s;
}
.btn-status-relatorio[data-status="todos"] {
    color: #004085;
    border-color: #b8daff;
    background: #e9f7fe;
}
.btn-status-relatorio[data-status="Pendente"] {
    color: #856404;
    border-color: #ffeeba;
    background: #fffbe6;
}
.btn-status-relatorio[data-status="Lançada"] {
    color: #155724;
    border-color: #c3e6cb;
    background: #e6ffed;
}
.btn-status-relatorio.active {
    color: #fff;
    border-color: transparent;
}
.btn-status-relatorio[data-status="todos"].active {
    background: #004085;
}
.btn-status-relatorio[data-status="Pendente"].active {
    background: #856404;
}
.btn-status-relatorio[data-status="Lançada"].active {
    background: #155724;
}
.btn-status-relatorio:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Estilos responsivos para o modal */
@media (max-width: 768px) {
    #modalDetalhesUnico .modal-dialog {
        margin: 2vh auto;
        width: 95%;
        max-height: 96vh;
    }
    
    #modalDetalhesUnico .modal-content {
        max-height: 98vh;
    }
    
    #modalDetalhesUnico .modal-header {
        padding: 8px 12px;
    }
    
    #modalDetalhesUnico .modal-body {
        padding: 8px 12px;
    }
    
    #modalDetalhesUnico .modal-footer {
        padding: 8px 12px;
    }
    
    #modalDetalhesUnico .row {
        margin-right: -3px;
        margin-left: -3px;
    }
    
    #modalDetalhesUnico .col-lg-6,
    #modalDetalhesUnico .col-md-12 {
        padding-right: 3px;
        padding-left: 3px;
    }
    
    #chat-observacoes {
        min-height: 200px !important;
        max-height: 250px !important;
    }
    
    #det-observacoes {
        min-height: 50px !important;
        max-height: 80px !important;
    }
}

@media (max-width: 576px) {
    #modalDetalhesUnico .modal-dialog {
        margin: 1vh auto;
        width: 98%;
        max-height: 98vh;
    }
    
    #modalDetalhesUnico .modal-content {
        max-height: 96vh;
        border-radius: 14px;
    }
    
    #chat-observacoes {
        min-height: 150px !important;
        max-height: 200px !important;
    }
}

/* Ajustes para o chat de observações */
#chat-observacoes {
    min-height: 250px;
    max-height: 350px;
    overflow-y: auto;
    margin-bottom: 10px;
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    padding: 12px;
}

#chat-observacoes .mb-2 {
    margin-bottom: 1rem !important;
    padding: 12px;
    border-bottom: 1px solid #e9ecef;
    background: #fff;
    border-radius: 6px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}

#chat-observacoes .mb-2:last-child {
    border-bottom: none;
    margin-bottom: 0 !important;
}

#chat-observacoes strong {
    font-size: 0.95rem;
    color: #495057;
    font-weight: 600;
}

#chat-observacoes small {
    font-size: 0.8rem;
    color: #6c757d;
}

#chat-observacoes div:last-child {
    margin-top: 8px;
    font-size: 0.9rem;
    line-height: 1.4;
    color: #212529;
}

/* Ajustes para os cards dentro do modal */
#modalDetalhesUnico .card {
    margin-bottom: 15px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

#modalDetalhesUnico .card-body {
    padding: 15px;
}

#modalDetalhesUnico .form-label {
    font-size: 0.9rem;
    margin-bottom: 4px;
}

#modalDetalhesUnico .form-control {
    font-size: 0.9rem;
    padding: 6px 12px;
}

/* Ajustes para o botão de salvar */
#btn-salvar-observacao {
    min-width: 80px;
    padding: 6px 12px;
    font-size: 0.9rem;
}

/* Ajustes para o textarea de observações */
#det-observacoes {
    resize: vertical;
    min-height: 60px;
    max-height: 120px;
}

/* Ajustes para a tabela responsiva */
.table-responsive {
    margin: 0;
    border: 0;
}

.table-responsive table {
    margin-bottom: 0;
}

.table-responsive th,
.table-responsive td {
    white-space: nowrap;
}

/* Ajustes para os botões de ação */
.btn-auditoria {
    min-width: 60px;
    width: 60px;
    font-size: 0.8rem;
    height: 24px;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

/* Ajustes para os filtros */
#formFiltros .btn-group {
    flex-wrap: wrap;
    gap: 0.25rem;
}

#formFiltros .btn {
    margin-bottom: 0.25rem;
}

/* Ajustes para o paginador */
.pagination {
    flex-wrap: wrap;
    justify-content: center;
    gap: 0.25rem;
}

.pagination .page-item {
    margin: 0;
}

/* Ajustes para os cards */
.card {
    margin-bottom: 1rem;
}

.card-body {
    padding: 1.25rem;
}

/* Ajustes para os inputs de vencimento */
.vencimento-group {
    margin-bottom: 0.5rem;
}

.vencimento-group .input-group-text {
    padding: 0.25rem 0.5rem;
}

.vencimento-group .form-control {
    padding: 0.25rem 0.5rem;
}

/* Ajustes para o select de destino */
.destino-select {
    min-width: 120px;
}

/* Ajustes para o botão de WhatsApp */
#btn-whatsapp {
    white-space: nowrap;
}

/* Ajustes para o modal de colunas */
#colunas-modal .modal-dialog {
    margin: 1rem auto;
    max-width: 90%;
}

#colunas-modal .form-check {
    margin-bottom: 0.5rem;
}

/* Ajustes para o modal de status */
#modalStatusFiltro .btn-status-option {
    font-size: 0.9rem;
    padding: 0.4rem 0;
}

/* Ajustes para o título da página */
h2.mb-3 {
    font-size: 1.5rem;
    margin-bottom: 1rem !important;
}

/* Ajustes para o card de filtros */
.card.mb-4 {
    margin-bottom: 1rem !important;
}

/* Ajustes para o card de resultados */
.card:last-child {
    margin-bottom: 0;
}

/* Ajustes para o botão de selecionar colunas */
#btn-colunas {
    white-space: nowrap;
}

/* Ajustes para o alerta de nenhum resultado */
.alert {
    margin: 1rem 0;
    padding: 1rem;
}

/* Ajustes para os inputs de data */
input[type="date"] {
    min-width: 130px;
}

/* Ajustes para o input de busca */
#busca {
    min-width: 200px;
}

.cancelada-nota {
    font-weight: bold;
    color: #c0392b !important;
}
.entrada-nota {
    font-weight: bold;
    color: #0056b3 !important;
}
.badge-romaneio {
    background: rgb(101, 230, 131) !important;
    color: #222 !important;
}
.badge-propria {
    background: #6f42c1 !important;
    color: #fff !important;
}
.badge-anulada {
    background: rgb(226, 96, 209) !important;
    color: #fff !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const empresaSelect = document.getElementById('empresa_id');
    // Verifica se o parâmetro empresa_id está na URL
    const urlParams = new URLSearchParams(window.location.search);
    if (empresaSelect && !urlParams.has('empresa_id')) {
        if (empresaSelect.options.length > 0) {
            empresaSelect.selectedIndex = 0;
            empresaSelect.dispatchEvent(new Event('change'));
        }
    }

    document.querySelectorAll('.add-vencimento').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var idx = this.getAttribute('data-idx');
            var container = document.getElementById('vencimentos-' + idx);
            var count = container.querySelectorAll('.vencimento-group').length;
            if (count < 12) {
                var div = document.createElement('div');
                div.className = 'input-group mb-1 vencimento-group';
                div.innerHTML = `
                    <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                    <input type="date" class="form-control form-control-sm venc-input" name="vencimento[${idx}][]">
                    <button type="button" class="btn btn-outline-danger btn-sm remove-vencimento">-</button>
                `;
                container.appendChild(div);
            }
        });
    });
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-vencimento')) {
            e.target.closest('.vencimento-group').remove();
        }
    });

    // Seleção de colunas
    const btnColunas = document.getElementById('btn-colunas');
    const modalColunas = document.getElementById('colunas-modal');
    const fecharModal = document.getElementById('fechar-modal');
    const fecharModal2 = document.getElementById('fechar-modal2');
    const colToggles = document.querySelectorAll('.col-toggle');
    const tabela = document.getElementById('tabela-auditoria');

    // Função para salvar seleção no localStorage
    function salvarSelecaoColunas() {
        const selecionadas = Array.from(colToggles).map(t => t.checked ? 1 : 0);
        localStorage.setItem('auditoria_colunas', JSON.stringify(selecionadas));
    }
    // Função para restaurar seleção do localStorage
    function restaurarSelecaoColunas() {
        const selecionadas = JSON.parse(localStorage.getItem('auditoria_colunas') || 'null');
        if (Array.isArray(selecionadas) && selecionadas.length === colToggles.length) {
            colToggles.forEach((toggle, idx) => {
                toggle.checked = !!selecionadas[idx];
                // Dispara o evento para aplicar a visibilidade
                toggle.dispatchEvent(new Event('change'));
            });
        }
    }

    btnColunas.addEventListener('click', () => { modalColunas.style.display = 'block'; });
    fecharModal.addEventListener('click', () => { modalColunas.style.display = 'none'; });
    fecharModal2.addEventListener('click', () => { modalColunas.style.display = 'none'; });

    colToggles.forEach(function(toggle) {
        toggle.addEventListener('change', function() {
            const col = parseInt(this.getAttribute('data-col'));
            const show = this.checked;
            // Cabeçalho
            tabela.querySelectorAll('thead th')[col].style.display = show ? '' : 'none';
            // Linhas
            tabela.querySelectorAll('tbody tr').forEach(function(tr) {
                tr.querySelectorAll('td')[col].style.display = show ? '' : 'none';
            });
            salvarSelecaoColunas(); // Salva ao mudar
        });
    });

    // Restaurar seleção ao carregar a página
    restaurarSelecaoColunas();

    // Ajuste de largura das colunas
    function makeResizable(table) {
        const ths = table.querySelectorAll('thead th');
        ths.forEach(function(th, i) {
            th.style.position = 'relative';
            const resizer = document.createElement('div');
            resizer.style.width = '5px';
            resizer.style.height = '100%';
            resizer.style.position = 'absolute';
            resizer.style.top = '0';
            resizer.style.right = '0';
            resizer.style.cursor = 'col-resize';
            resizer.style.userSelect = 'none';
            resizer.addEventListener('mousedown', initResize);
            th.appendChild(resizer);
            function initResize(e) {
                e.preventDefault();
                document.addEventListener('mousemove', resizeCol);
                document.addEventListener('mouseup', stopResize);
                let startX = e.pageX;
                let startWidth = th.offsetWidth;
                function resizeCol(e2) {
                    let newWidth = startWidth + (e2.pageX - startX);
                    th.style.width = newWidth + 'px';
                    tabela.querySelectorAll('tbody tr').forEach(function(tr) {
                        tr.querySelectorAll('td')[i].style.width = newWidth + 'px';
                    });
                }
                function stopResize() {
                    document.removeEventListener('mousemove', resizeCol);
                    document.removeEventListener('mouseup', stopResize);
                }
            }
        });
    }
    if (tabela) {
        makeResizable(tabela);
        makeTableDraggableAndSortable(tabela);
    }

    // Modal de detalhes único
    let notaIdAtual = null;
    document.body.addEventListener('click', function(e) {
        console.log('Click detectado:', e.target);
        if (e.target.classList.contains('btn-detalhes') || (e.target.closest('.btn-detalhes'))) {
            console.log('Botão detalhes clicado!');
            var btn = e.target.closest('.btn-detalhes');
            var tr = btn.closest('tr');
            var nota = {};
            try { nota = JSON.parse(tr.getAttribute('data-nota')); } catch(e) {}
            notaIdAtual = (typeof nota.id !== 'undefined') ? nota.id : null;
            // Cabeçalho do modal
            var numero = nota.numero || '---';
            var fornecedor = nota.razao_social || nota.fornecedor || '---';
            document.getElementById('modalDetalhesTitulo').textContent = 'Detalhes da Escrituração Fiscal - N° ' + numero + ' - ' + fornecedor;
            // Preencher campos do modal com valores padrão
            document.getElementById('det-escriturado_por').textContent = '---';
            document.getElementById('det-data_escrituracao').textContent = '---';
            document.getElementById('det-prazo_escrituracao').textContent = '---';
            document.getElementById('det-numero_fiscal').textContent = '---';
            document.getElementById('det-numero_processo').textContent = '---';
            document.getElementById('det-numero_financeiro').textContent = '---';
            document.getElementById('det-codigo_fornecedor').textContent = '---';
            document.getElementById('det-conferente').textContent = '---';
            // Buscar dados reais da tabela notas
            if (notaIdAtual) {
                fetch('/auditoria/get-nota-detalhes?nota_id=' + notaIdAtual)
                    .then(resp => resp.json())
                    .then(data => {
                        if (data) {
                            document.getElementById('det-escriturado_por').textContent = data.escriturador || '---';
                            // Formatar data_entrada para DD-MM-YYYY HH:MM:SS
                            if (data.data_entrada && data.data_entrada !== '0000-00-00 00:00:00') {
                                const dt = new Date(data.data_entrada.replace(' ', 'T'));
                                if (!isNaN(dt.getTime())) {
                                    const dia = String(dt.getDate()).padStart(2, '0');
                                    const mes = String(dt.getMonth() + 1).padStart(2, '0');
                                    const ano = dt.getFullYear();
                                    const hora = String(dt.getHours()).padStart(2, '0');
                                    const min = String(dt.getMinutes()).padStart(2, '0');
                                    const seg = String(dt.getSeconds()).padStart(2, '0');
                                    document.getElementById('det-data_escrituracao').textContent = `${dia}-${mes}-${ano} ${hora}:${min}:${seg}`;
                                } else {
                                    document.getElementById('det-data_escrituracao').textContent = data.data_entrada;
                                }
                            } else {
                                document.getElementById('det-data_escrituracao').textContent = '---';
                            }
                            document.getElementById('det-numero_fiscal').textContent = data.codigo_fiscal || '---';
                            document.getElementById('det-numero_processo').textContent = data.codigo_processo || '---';
                            document.getElementById('det-numero_financeiro').textContent = data.numero_financeiro || '---';
                            document.getElementById('det-conferente').textContent = data.conferente || '---';
                            // Atualizar o campo Código do fornecedor APENAS com o valor do backend
                            if (typeof data.codigo_fornecedor === 'string' && data.codigo_fornecedor.trim() !== '') {
                                document.getElementById('det-codigo_fornecedor').textContent = data.codigo_fornecedor;
                            } else {
                                document.getElementById('det-codigo_fornecedor').textContent = 'Fornecedor não cadastrado';
                            }
                            // Cálculo do prazo de escrituração
                            if (nota.status_lancamento === 'Lançada') {
                                document.getElementById('det-prazo_escrituracao').textContent = 'Escriturado';
                            } else if (nota.status_lancamento === 'Desconhecimento') {
                                document.getElementById('det-prazo_escrituracao').textContent = 'Desconhecimento da operação';
                            } else if (nota.tipo && nota.tipo.toLowerCase() === 'entrada') {
                                document.getElementById('det-prazo_escrituracao').textContent = 'Nota de Entrada';
                            } else if (nota.situacao && nota.situacao.toLowerCase() === 'cancelada') {
                                document.getElementById('det-prazo_escrituracao').textContent = 'Nota Cancelada';
                            } else if (nota.situacao && nota.situacao.toLowerCase() === 'romaneio') {
                                document.getElementById('det-prazo_escrituracao').textContent = 'Aguardando conferência';
                            } else if (nota.status_lancamento === 'Transferência') {
                                document.getElementById('det-prazo_escrituracao').textContent = 'Transferência';
                            } else if (nota.tipo && nota.tipo.toLowerCase() === 'própria') {
                                document.getElementById('det-prazo_escrituracao').textContent = 'Emissão Própria';
                            } else {
                                var dataBase = data.data_entrada || nota.data_emissao || '';
                                var prazo = '---';
                                if (dataBase && dataBase !== '0000-00-00 00:00:00') {
                                    var dtBase = new Date(dataBase.replace(' ', 'T'));
                                    if (!isNaN(dtBase.getTime())) {
                                        var dtLimite = new Date(dtBase);
                                        dtLimite.setDate(dtLimite.getDate() + 60);
                                        var hoje = new Date();
                                        var diasRestantes = Math.ceil((dtLimite - hoje) / (1000 * 60 * 60 * 24));
                                        if (diasRestantes > 0) {
                                            prazo = diasRestantes + ' dias restantes';
                                        } else {
                                            prazo = 'Prazo expirado';
                                        }
                                    }
                                }
                                document.getElementById('det-prazo_escrituracao').textContent = prazo;
                            }
                        }
                    });
            }
            document.getElementById('det-observacoes').value = '';
            document.getElementById('msg-observacao').style.display = 'none';
            // Buscar observação via AJAX
            if (notaIdAtual) {
                carregarObservacoes(notaIdAtual);
            }
            console.log('Exibindo modal...');
            document.getElementById('modalDetalhesUnico').style.display = 'block';
            console.log('Modal exibido!');
        }
        if (e.target.classList.contains('fechar-modal-detalhes') || e.target.closest('.fechar-modal-detalhes')) {
            document.getElementById('modalDetalhesUnico').style.display = 'none';
        }
    });
    // Fechar modal ao clicar fora do conteúdo
    document.getElementById('modalDetalhesUnico').addEventListener('click', function(e) {
        if (e.target === this) {
            this.style.display = 'none';
        }
    });
    // Salvar observação via AJAX
    document.getElementById('btn-salvar-observacao').addEventListener('click', function() {
        if (!notaIdAtual) {
            console.error('notaIdAtual não está definido');
            return;
        }
        const obs = document.getElementById('det-observacoes').value.trim();
        
        if (!obs) {
            alert('Por favor, digite uma observação.');
            return;
        }
        
        console.log('Salvando observação:', { notaId: notaIdAtual, observacao: obs });
        fetch('/auditoria/observacao/' + notaIdAtual, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ observacao: obs })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('det-observacoes').value = '';
                document.getElementById('msg-observacao').style.display = 'inline';
                setTimeout(() => { document.getElementById('msg-observacao').style.display = 'none'; }, 2000);
                carregarObservacoes(notaIdAtual);
            } else {
                alert('Erro ao salvar observação: ' + (data.erro || 'Erro desconhecido'));
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao salvar observação. Por favor, tente novamente.');
        });
    });

    // Preencher destino e vencimentos ao carregar
    document.querySelectorAll('tr[data-nota-id]').forEach(function(tr) {
        var notaId = tr.getAttribute('data-nota-id');
        var box = tr.querySelector('.vencimentos-box');
        var select = tr.querySelector('.destino-select');
        fetch('/auditoria/get-destino-vencimentos?nota_id=' + notaId)
            .then(resp => resp.json())
            .then(data => {
                // Preencher vencimentos
                box.innerHTML = '';
                (data.vencimentos || []).forEach(function(venc, i) {
                    box.innerHTML += `<div class="input-group mb-1 vencimento-group">
                        <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                        <input type="date" class="form-control form-control-sm venc-input" value="${venc}" name="vencimento[${notaId}][]">
                        <button type="button" class="btn btn-outline-danger btn-sm remove-vencimento">-</button>
                    </div>`;
                });
                // Sempre pelo menos um campo
                if ((data.vencimentos || []).length < 1) {
                    box.innerHTML = `<div class="input-group mb-1 vencimento-group">
                        <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                        <input type="date" class="form-control form-control-sm venc-input" name="vencimento[${notaId}][]">
                        <button type="button" class="btn btn-outline-secondary btn-sm add-vencimento" data-idx="${notaId}">+</button>
                    </div>`;
                } else {
                    box.innerHTML += `<div class="input-group mb-1 vencimento-group">
                        <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                        <input type="date" class="form-control form-control-sm venc-input" name="vencimento[${notaId}][]">
                        <button type="button" class="btn btn-outline-secondary btn-sm add-vencimento" data-idx="${notaId}">+</button>
                    </div>`;
                }
                // Preencher destino_id
                if (select && data.destino_id) {
                    select.value = data.destino_id;
                }
            });
    });
    // Adicionar/remover campos de vencimento
    document.body.addEventListener('click', function(e) {
        if (e.target.classList.contains('add-vencimento')) {
            var box = e.target.closest('.vencimentos-box');
            var count = box.querySelectorAll('.vencimento-group').length;
            if (count < 12) {
                var div = document.createElement('div');
                div.className = 'input-group mb-1 vencimento-group';
                div.innerHTML = `
                    <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                    <input type="date" class="form-control form-control-sm venc-input" name="vencimento[]">
                    <button type="button" class="btn btn-outline-danger btn-sm remove-vencimento">-</button>
                `;
                box.insertBefore(div, e.target.closest('.vencimento-group').nextSibling);
            }
        }
        if (e.target.classList.contains('remove-vencimento')) {
            e.target.closest('.vencimento-group').remove();
        }
        if (e.target.classList.contains('salvar-destino-venc')) {
            var tr = e.target.closest('tr');
            var notaId = tr.getAttribute('data-nota-id');
            var destino_id = tr.querySelector('.destino-select').value;
            var vencInputs = tr.querySelectorAll('.venc-input');
            var vencimentos = [];
            vencInputs.forEach(function(inp) {
                if (inp.value) vencimentos.push(inp.value);
            });
            console.log('Enviando para /auditoria/salvar-destino-vencimentos', { nota_id: notaId, destino_id: destino_id, vencimentos: vencimentos });
            fetch('/auditoria/salvar-destino-vencimentos', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ nota_id: notaId, destino_id: destino_id, vencimentos: vencimentos })
            })
            .then(resp => {
                console.log('Response status:', resp.status);
                console.log('Response headers:', resp.headers);
                return resp.json();
            })
            .then(data => {
                console.log('Response data:', data);
                e.target.classList.remove('btn-success');
                e.target.classList.add('btn-secondary');
                e.target.textContent = 'Salvo!';
                setTimeout(() => {
                    e.target.classList.remove('btn-secondary');
                    e.target.classList.add('btn-success');
                    e.target.textContent = 'Salvar';
                }, 2000);
            })
            .catch(error => {
                console.error('Erro ao salvar:', error);
                alert('Erro ao salvar: ' + error.message);
            });
        }
    });

    // Drag-and-drop e ordenação de colunas
    function makeTableDraggableAndSortable(table) {
        let draggingColIdx = null;
        let startX = null;
        let ghost = null;
        const ths = table.querySelectorAll('thead th');
        ths.forEach((th, idx) => {
            th.draggable = true;
            th.addEventListener('dragstart', function(e) {
                draggingColIdx = idx;
                startX = e.clientX;
                ghost = th.cloneNode(true);
                ghost.style.opacity = '0.5';
                ghost.style.position = 'absolute';
                ghost.style.top = '-9999px';
                document.body.appendChild(ghost);
                e.dataTransfer.setDragImage(ghost, 0, 0);
            });
            th.addEventListener('dragover', function(e) {
                e.preventDefault();
            });
            th.addEventListener('drop', function(e) {
                e.preventDefault();
                if (draggingColIdx === null || draggingColIdx === idx) return;
                moveTableColumn(table, draggingColIdx, idx);
                draggingColIdx = null;
                if (ghost) { document.body.removeChild(ghost); ghost = null; }
            });
            th.addEventListener('dragend', function() {
                draggingColIdx = null;
                if (ghost) { document.body.removeChild(ghost); ghost = null; }
            });
        });
        // Ordenação ao clicar no cabeçalho
        ths.forEach((th, idx) => {
            let order = 1;
            th.style.cursor = 'pointer';
            th.addEventListener('click', function() {
                sortTableByColumn(table, idx, order);
                order *= -1;
            });
        });
    }
    function moveTableColumn(table, from, to) {
        const rows = table.rows;
        for (let i = 0; i < rows.length; i++) {
            const cells = rows[i].children;
            if (to < from) {
                rows[i].insertBefore(cells[from], cells[to]);
            } else {
                rows[i].insertBefore(cells[from], cells[to + 1]);
            }
        }
    }
    function sortTableByColumn(table, colIdx, order) {
        const tbody = table.tBodies[0];
        const rows = Array.from(tbody.querySelectorAll('tr'));
        rows.sort((a, b) => {
            let aText = a.children[colIdx].innerText.trim();
            let bText = b.children[colIdx].innerText.trim();
            // Tenta converter para número ou data
            let aNum = parseFloat(aText.replace(/[^0-9,.-]+/g, '').replace(',', '.'));
            let bNum = parseFloat(bText.replace(/[^0-9,.-]+/g, '').replace(',', '.'));
            if (!isNaN(aNum) && !isNaN(bNum)) {
                return order * (aNum - bNum);
            }
            // Tenta converter para data
            let aDate = Date.parse(aText.split('/').reverse().join('-'));
            let bDate = Date.parse(bText.split('/').reverse().join('-'));
            if (!isNaN(aDate) && !isNaN(bDate)) {
                return order * (aDate - bDate);
            }
            // Fallback: string
            return order * aText.localeCompare(bText);
        });
        rows.forEach(row => tbody.appendChild(row));
    }

    document.getElementById('btn-limpar')?.addEventListener('click', function() {
        // Limpa filtros e recarrega a página
        window.location.href = window.location.pathname;
    });

    // Função para enviar via WhatsApp
    document.getElementById('btn-whatsapp').addEventListener('click', function() {
        const notaSelecionada = document.querySelector('input[name="nota_selecionada"]:checked');
        if (!notaSelecionada) {
            alert('Por favor, selecione uma nota fiscal primeiro.');
            return;
        }

        const tr = notaSelecionada.closest('tr');
        const nota = JSON.parse(tr.getAttribute('data-nota'));
        
        // Formatar a mensagem
        const mensagem = `*Olá! Confira os dados da nota fiscal selecionada:* \n\n` +
            `*Nota Fiscal ${nota.numero}*\n\n` +
            `*Fornecedor:* ${nota.razao_social}\n` +
            `*CNPJ:* ${nota.cnpj}\n` +
            `*Data de Emissão:* ${nota.data_emissao}\n` +
            `*Valor:* R$ ${nota.valor}\n` +
            `*Chave:* ${nota.chave}\n` +
            `*UF:* ${nota.uf}\n` +
            `*Status:* ${nota.status}\n` +
            `*Tipo:* ${nota.tipo}\n` +
            `*Destino:* ${tr.querySelector('.destino-select option:checked')?.textContent || 'Não definido'}\n\n` +
            `*Vencimentos:*\n${Array.from(tr.querySelectorAll('.venc-input'))
                .filter(input => input.value)
                .map(input => `- ${input.value}`)
                .join('\n') || '- Não informado'}\n\n` +
            `Dados gerados a partir do sistema da *Ágil Fiscal*`;

        // Codificar a mensagem para URL
        const mensagemCodificada = encodeURIComponent(mensagem);
        
        // Abrir WhatsApp Web com a mensagem
        window.open(`https://wa.me/?text=${mensagemCodificada}`, '_blank');
    });

    // Função para aplicar filtros automaticamente
    function aplicarFiltros() {
        const form = document.getElementById('formFiltros');
        const formData = new FormData(form);
        const params = new URLSearchParams(formData);
        // Adiciona o parâmetro filtrar=1
        params.append('filtrar', '1');
        // Debug: mostrar parâmetros enviados
        console.log('Filtros enviados:', params.toString());
        // Redireciona para a URL com os parâmetros
        window.location.href = `${form.action}?${params.toString()}`;
    }

    // Adiciona event listeners para os campos de filtro
    document.getElementById('empresa_id').addEventListener('change', aplicarFiltros);
    document.getElementById('data_inicial').addEventListener('change', aplicarFiltros);
    document.getElementById('data_final').addEventListener('change', aplicarFiltros);
    
    // Para o campo de busca, adiciona um debounce para evitar muitas requisições
    let timeoutBusca;
    document.getElementById('busca').addEventListener('input', function() {
        clearTimeout(timeoutBusca);
        timeoutBusca = setTimeout(aplicarFiltros, 500); // Espera 500ms após o usuário parar de digitar
    });

    // Remove o evento de submit do formulário para evitar dupla submissão
    document.getElementById('formFiltros').addEventListener('submit', function(e) {
        // Verifica se o submit foi acionado por um botão de filtro rápido
        const submitter = e.submitter;
        if (submitter && submitter.name === 'filtro_rapido') {
            // Permite o submit normal para botões de filtro rápido
            return true;
        }
        // Para outros casos, previne o submit padrão e usa a função aplicarFiltros
        e.preventDefault();
        aplicarFiltros();
    });

    // Inicializar todos os dropdowns do Bootstrap
    var dropdownElementList = [].slice.call(document.querySelectorAll('[data-bs-toggle="dropdown"]'));
    var dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
        return new bootstrap.Dropdown(dropdownToggleEl, {
            autoClose: true
        });
    });

    // Modal customizado para filtro de status
    const btnStatusModal = document.getElementById('btn-status-modal');
    const modalStatusFiltro = document.getElementById('modalStatusFiltro');
    const btnFecharStatusModal = document.getElementById('btn-fechar-status-modal');
    const statusOptions = document.querySelectorAll('.btn-status-option');

    btnStatusModal.addEventListener('click', function() {
        modalStatusFiltro.style.display = 'flex';
    });
    btnFecharStatusModal.addEventListener('click', function() {
        modalStatusFiltro.style.display = 'none';
    });
    modalStatusFiltro.addEventListener('click', function(e) {
        if (e.target === modalStatusFiltro) {
            modalStatusFiltro.style.display = 'none';
        }
    });
    statusOptions.forEach(function(btn) {
        btn.addEventListener('click', function() {
            const status = this.getAttribute('data-status');
            const url = new URL(window.location.href);
            if (status === 'todos') {
                url.searchParams.delete('status');
            } else {
                url.searchParams.set('status', status);
            }
            window.location.href = url.toString();
        });
    });

    function carregarObservacoes(notaId) {
        const url = '/auditoria/observacao-get/' + notaId;
        console.log('Carregando observações para nota ID:', notaId);
        console.log('URL completa:', url);
        fetch(url)
            .then(response => {
                console.log('Response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Dados recebidos:', data);
                const chatContainer = document.getElementById('chat-observacoes');
                chatContainer.innerHTML = '';
                
                if (data.observacoes && data.observacoes.length > 0) {
                    data.observacoes.forEach(obs => {
                        const dataHora = new Date(obs.data_hora).toLocaleString('pt-BR');
                        const div = document.createElement('div');
                        div.className = 'mb-2 p-2 border-bottom';
                        div.innerHTML = `
                            <div class="d-flex justify-content-between align-items-start">
                                <strong>${obs.nome_usuario}</strong>
                                <small class="text-muted">${dataHora}</small>
                            </div>
                            <div>${obs.observacao}</div>
                        `;
                        chatContainer.appendChild(div);
                    });
                } else {
                    chatContainer.innerHTML = '<div class="text-muted">Nenhuma observação registrada.</div>';
                }
            })
            .catch(error => {
                console.error('Erro ao carregar observações:', error);
                console.log('URL da requisição:', '/auditoria/observacao/' + notaId);
            });
    }

    // Relatório - abrir modal
    const btnRelatorio = document.getElementById('btn-relatorio');
    const modalRelatorios = document.getElementById('modalRelatorios');
    const fecharModalRelatorio = document.getElementById('fechar-modal-relatorio');
    const fecharModalRelatorio2 = document.getElementById('fechar-modal-relatorio2');
    btnRelatorio.addEventListener('click', function() {
        // Checar se há pelo menos um filtro selecionado
        const empresa = document.getElementById('empresa_id').value;
        const dataIni = document.getElementById('data_inicial').value;
        const dataFim = document.getElementById('data_final').value;
        const busca = document.getElementById('busca').value;
        const urlParams = new URLSearchParams(window.location.search);
        const status = urlParams.get('status');
        if (!empresa && !dataIni && !dataFim && !busca && !status) {
            alert('Selecione pelo menos um filtro para gerar o relatório.');
            return;
        }
        modalRelatorios.style.display = 'block';
    });
    fecharModalRelatorio.addEventListener('click', function() { modalRelatorios.style.display = 'none'; });
    fecharModalRelatorio2.addEventListener('click', function() { modalRelatorios.style.display = 'none'; });
    modalRelatorios.addEventListener('click', function(e) {
        if (e.target === modalRelatorios) {
            modalRelatorios.style.display = 'none';
        }
    });
    // Gerar relatório ao clicar no formato
    document.querySelectorAll('.btn-status-relatorio').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const formato = this.getAttribute('data-formato');
            // Montar URL para geração do relatório
            const params = new URLSearchParams(window.location.search);
            params.set('formato', formato);
            params.set('relatorio', '1');
            window.open('/auditoria/gerar-relatorio?' + params.toString(), '_blank');
            modalRelatorios.style.display = 'none';
        });
    });

    // Botão Visualizar nota (DANFE)
    document.getElementById('btn-visualizar-danfe').addEventListener('click', function() {
        const notaSelecionada = document.querySelector('input[name="nota_selecionada"]:checked');
        if (!notaSelecionada) {
            alert('Por favor, selecione uma nota fiscal primeiro.');
            return;
        }
        const tr = notaSelecionada.closest('tr');
        const nota = JSON.parse(tr.getAttribute('data-nota'));
        const chave = (nota.chave || '').replace(/[^0-9]/g, '').trim();
        if (!chave || chave.length !== 44) {
            alert('Chave de acesso da nota inválida ou não encontrada.');
            return;
        }
        window.open(`/auditoria/danfe/${chave}`, '_blank');
    });
    // Botão Baixar XML - Interceptado pela extensão Chrome
    // O event listener é adicionado pelo content-script da extensão
});
</script>

