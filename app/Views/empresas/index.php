<?php $title = 'Empresas'; ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-3">Empresas Cadastradas</h2>
    <a href="/empresas/cadastrar" class="btn btn-primary" style="background:#2c3e50;color:#fff;border:none;"><i class="fas fa-plus"></i> Nova Empresa</a>
</div>
<!-- Filtro de busca -->
<form method="get" class="mb-3" style="max-width: 400px;">
    <div class="input-group">
        <input type="text" name="busca" class="form-control" placeholder="Buscar por nome, CNPJ, cidade..." value="<?= htmlspecialchars($_GET['busca'] ?? '') ?>">
        <button class="btn btn-outline-secondary" type="submit"><i class="fas fa-search"></i> Buscar</button>
    </div>
</form>
<?php if (empty($empresas)): ?>
    <div class="alert alert-info">Nenhuma empresa cadastrada.</div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>CNPJ</th>
                    <th>Razão Social</th>
                    <th>UF</th>
                    <th>Cidade</th>
                    <th>Inscrição Estadual</th>
                    <th>Telefone</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Regime</th>
                    <th>Representante Legal</th>
                    <th>Tipo</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($empresas as $empresa): ?>
                    <tr>
                        <td><?= ($empresa['cnpj'] ?? '') !== '' ? htmlspecialchars($empresa['cnpj']) : '-' ?></td>
                        <td><?= ($empresa['razao_social'] ?? '') !== '' ? htmlspecialchars($empresa['razao_social']) : '-' ?></td>
                        <td><?= ($empresa['uf'] ?? '') !== '' ? htmlspecialchars($empresa['uf']) : '-' ?></td>
                        <td><?= ($empresa['cidade'] ?? '') !== '' ? htmlspecialchars($empresa['cidade']) : '-' ?></td>
                        <td><?= ($empresa['inscricao_estadual'] ?? '') !== '' ? htmlspecialchars($empresa['inscricao_estadual']) : '-' ?></td>
                        <td><?= ($empresa['telefone'] ?? '') !== '' ? htmlspecialchars($empresa['telefone']) : '-' ?></td>
                        <td><?= ($empresa['email'] ?? '') !== '' ? htmlspecialchars($empresa['email']) : '-' ?></td>
                        <td><?= ($empresa['status'] ?? '') !== '' ? htmlspecialchars($empresa['status']) : '-' ?></td>
                        <td><?= ($empresa['regime'] ?? '') !== '' ? htmlspecialchars($empresa['regime']) : '-' ?></td>
                        <td><?= ($empresa['representante_legal'] ?? '') !== '' ? htmlspecialchars($empresa['representante_legal']) : '-' ?></td>
                        <td>
                            <?php
                            $tipo = $empresa['tipo_empresa'] ?? 'independente';
                            $badge_class = [
                                'matriz' => 'bg-primary',
                                'filial' => 'bg-success',
                                'independente' => 'bg-secondary'
                            ][$tipo];
                            ?>
                            <span class="badge <?= $badge_class ?>"><?= ucfirst($tipo) ?></span>
                        </td>
                        <td>
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-primary" onclick="editarEmpresa(<?= htmlspecialchars(json_encode($empresa)) ?>)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <?php if ($tipo === 'matriz'): ?>
                                <a href="/empresas/filiais/<?= $empresa['id'] ?>" class="btn btn-sm btn-info" title="Gerenciar Filiais">
                                    <i class="fas fa-building"></i>
                                </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php if (($totalPaginas ?? 1) > 1): ?>
<nav aria-label="Paginação de empresas">
    <ul class="pagination justify-content-center mt-3">
        <?php $buscaQuery = $busca ? '&busca=' . urlencode($busca) : ''; ?>
        <li class="page-item<?= $pagina <= 1 ? ' disabled' : '' ?>">
            <a class="page-link" href="?pagina=<?= $pagina-1 . $buscaQuery ?>" tabindex="-1">Anterior</a>
        </li>
        <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
            <li class="page-item<?= $i == $pagina ? ' active' : '' ?>">
                <a class="page-link" href="?pagina=<?= $i . $buscaQuery ?>"><?= $i ?></a>
            </li>
        <?php endfor; ?>
        <li class="page-item<?= $pagina >= $totalPaginas ? ' disabled' : '' ?>">
            <a class="page-link" href="?pagina=<?= $pagina+1 . $buscaQuery ?>">Próxima</a>
        </li>
    </ul>
</nav>
<?php endif; ?>

<!-- Modal de Edição -->
<div class="modal fade" id="modalEditarEmpresa" tabindex="-1" aria-labelledby="modalEditarEmpresaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditarEmpresaLabel">Editar Empresa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <form id="formEditarEmpresa" method="POST" action="/empresas/atualizar">
                    <input type="hidden" id="empresa_id" name="id">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="razao_social" class="form-label">Razão Social</label>
                            <input type="text" class="form-control" id="razao_social" name="razao_social" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="cnpj" class="form-label">CNPJ</label>
                            <input type="text" class="form-control" id="cnpj" readonly>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="uf" class="form-label">UF</label>
                            <input type="text" class="form-control" id="uf" name="uf">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="cidade" class="form-label">Cidade</label>
                            <input type="text" class="form-control" id="cidade" name="cidade">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="inscricao_estadual" class="form-label">Inscrição Estadual</label>
                            <input type="text" class="form-control" id="inscricao_estadual" name="inscricao_estadual">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="telefone" class="form-label">Telefone</label>
                            <input type="text" class="form-control" id="telefone" name="telefone">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Status</label>
                            <input type="text" class="form-control" id="status" name="status">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="regime" class="form-label">Regime</label>
                            <input type="text" class="form-control" id="regime" name="regime">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="representante_legal" class="form-label">Representante Legal</label>
                            <input type="text" class="form-control" id="representante_legal" name="representante_legal">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="tipo_empresa" class="form-label">Tipo de Empresa</label>
                            <select class="form-select" id="tipo_empresa" name="tipo_empresa">
                                <option value="independente">Independente</option>
                                <option value="matriz">Matriz</option>
                                <option value="filial">Filial</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3" id="empresa_matriz_container" style="display: none;">
                            <label for="empresa_matriz_id" class="form-label">Empresa Matriz</label>
                            <select class="form-select" id="empresa_matriz_id" name="empresa_matriz_id">
                                <option value="">Selecione uma empresa matriz...</option>
                                <?php foreach ($empresas_matriz as $matriz): ?>
                                    <option value="<?= $matriz['id'] ?>"><?= htmlspecialchars($matriz['razao_social']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="ativo" name="ativo" value="1">
                            <label class="form-check-label" for="ativo">Ativo</label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="salvarEmpresa()">Salvar</button>
            </div>
        </div>
    </div>
</div>

<script>
function editarEmpresa(empresa) {
    // Preenche o formulário com os dados da empresa
    document.getElementById('empresa_id').value = empresa.id;
    document.getElementById('razao_social').value = empresa.razao_social;
    document.getElementById('cnpj').value = empresa.cnpj;
    document.getElementById('uf').value = empresa.uf;
    document.getElementById('cidade').value = empresa.cidade;
    document.getElementById('inscricao_estadual').value = empresa.inscricao_estadual;
    document.getElementById('telefone').value = empresa.telefone;
    document.getElementById('email').value = empresa.email;
    document.getElementById('status').value = empresa.status;
    document.getElementById('regime').value = empresa.regime;
    document.getElementById('representante_legal').value = empresa.representante_legal;
    document.getElementById('tipo_empresa').value = empresa.tipo_empresa || 'independente';
    document.getElementById('empresa_matriz_id').value = empresa.empresa_matriz_id || '';
    document.getElementById('ativo').checked = empresa.ativo == 1;

    // Mostra/esconde o campo de empresa matriz
    toggleEmpresaMatriz();

    // Abre o modal
    new bootstrap.Modal(document.getElementById('modalEditarEmpresa')).show();
}

function toggleEmpresaMatriz() {
    const tipoEmpresa = document.getElementById('tipo_empresa').value;
    const empresaMatrizContainer = document.getElementById('empresa_matriz_container');
    empresaMatrizContainer.style.display = tipoEmpresa === 'filial' ? 'block' : 'none';
}

document.getElementById('tipo_empresa').addEventListener('change', toggleEmpresaMatriz);

function salvarEmpresa() {
    const form = document.getElementById('formEditarEmpresa');
    const formData = new FormData(form);

    fetch('/empresas/atualizar', {
        method: 'POST',
        body: formData
    })
    .then(async response => {
        const text = await response.text();
        try {
            const data = JSON.parse(text);
            if (data.success) {
                window.location.reload();
            } else {
                alert(data.message || 'Erro ao salvar empresa');
            }
        } catch (e) {
            // Se não for JSON, mostra o erro bruto em um modal
            mostrarErroBruto(text);
        }
    })
    .catch(error => {
        alert('Erro ao salvar empresa: ' + error.message);
    });
}

// Função para mostrar o erro bruto em um modal
function mostrarErroBruto(mensagem) {
    let modal = document.getElementById('modalErroBruto');
    if (!modal) {
        modal = document.createElement('div');
        modal.id = 'modalErroBruto';
        modal.innerHTML = `
            <div style="position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.5);z-index:9999;display:flex;align-items:center;justify-content:center;">
                <div style="background:#fff;padding:24px;border-radius:8px;max-width:90vw;max-height:80vh;overflow:auto;">
                    <h5>Erro bruto do servidor</h5>
                    <pre id="erroBrutoTexto" style="color:#c00;white-space:pre-wrap;word-break:break-all;"></pre>
                    <button onclick="document.getElementById('modalErroBruto').remove()" class="btn btn-secondary mt-3">Fechar</button>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
    }
    document.getElementById('erroBrutoTexto').textContent = mensagem;
}
</script> 