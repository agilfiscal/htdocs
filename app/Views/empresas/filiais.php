<?php $title = 'Gerenciar Filiais'; ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Gerenciar Filiais - <?= htmlspecialchars($empresa['razao_social']) ?></h2>
        <a href="/empresas" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Voltar</a>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Filiais Vinculadas</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($filiais)): ?>
                        <div class="alert alert-info">Nenhuma filial vinculada.</div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Razão Social</th>
                                        <th>CNPJ</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($filiais as $filial): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($filial['razao_social']) ?></td>
                                            <td><?= htmlspecialchars($filial['cnpj']) ?></td>
                                            <td>
                                                <form method="POST" action="/empresas/desvincular-filial" class="d-inline">
                                                    <input type="hidden" name="empresa_matriz_id" value="<?= $empresa['id'] ?>">
                                                    <input type="hidden" name="empresa_filial_id" value="<?= $filial['id'] ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja desvincular esta filial?')">
                                                        <i class="fas fa-unlink"></i> Desvincular
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Vincular Nova Filial</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="/empresas/vincular-filial">
                        <input type="hidden" name="empresa_matriz_id" value="<?= $empresa['id'] ?>">
                        <div class="mb-3">
                            <label for="empresa_filial_id" class="form-label">Selecione a Empresa</label>
                            <select class="form-select" id="empresa_filial_id" name="empresa_filial_id" required>
                                <option value="">Selecione uma empresa...</option>
                                <?php foreach ($empresas_disponiveis as $emp): ?>
                                    <option value="<?= $emp['id'] ?>"><?= htmlspecialchars($emp['razao_social']) ?> (<?= htmlspecialchars($emp['cnpj']) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Vincular Filial</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div> 