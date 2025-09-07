<?php $title = 'Editar Empresa'; ?>
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card">
            <div class="card-body">
                <h3 class="mb-4">Editar Empresa</h3>
                <?php if ($erro): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
                <?php endif; ?>
                <?php if (!is_array($empresa)): ?>
                    <div class="alert alert-danger">Empresa não encontrada.</div>
                <?php else: ?>
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="razao_social" class="form-label">Razão Social</label>
                        <input type="text" class="form-control" id="razao_social" name="razao_social" value="<?= htmlspecialchars($empresa['razao_social']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="uf" class="form-label">UF</label>
                        <input type="text" class="form-control" id="uf" name="uf" value="<?= htmlspecialchars($empresa['uf']) ?>">
                    </div>
                    <div class="mb-3">
                        <label for="cidade" class="form-label">Cidade</label>
                        <input type="text" class="form-control" id="cidade" name="cidade" value="<?= htmlspecialchars($empresa['cidade']) ?>">
                    </div>
                    <div class="mb-3">
                        <label for="inscricao_estadual" class="form-label">Inscrição Estadual</label>
                        <input type="text" class="form-control" id="inscricao_estadual" name="inscricao_estadual" value="<?= htmlspecialchars($empresa['inscricao_estadual']) ?>">
                    </div>
                    <div class="mb-3">
                        <label for="telefone" class="form-label">Telefone</label>
                        <input type="text" class="form-control" id="telefone" name="telefone" value="<?= htmlspecialchars($empresa['telefone']) ?>">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($empresa['email']) ?>">
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <input type="text" class="form-control" id="status" name="status" value="<?= htmlspecialchars($empresa['status']) ?>">
                    </div>
                    <div class="mb-3">
                        <label for="regime" class="form-label">Regime</label>
                        <input type="text" class="form-control" id="regime" name="regime" value="<?= htmlspecialchars($empresa['regime']) ?>">
                    </div>
                    <div class="mb-3">
                        <label for="representante_legal" class="form-label">Representante Legal</label>
                        <input type="text" class="form-control" id="representante_legal" name="representante_legal" value="<?= htmlspecialchars($empresa['representante_legal']) ?>">
                    </div>
                    <div class="mb-3">
                        <label for="tipo_empresa" class="form-label">Tipo de Empresa</label>
                        <select class="form-select" id="tipo_empresa" name="tipo_empresa">
                            <option value="independente" <?= ($empresa['tipo_empresa'] ?? '') === 'independente' ? 'selected' : '' ?>>Independente</option>
                            <option value="matriz" <?= ($empresa['tipo_empresa'] ?? '') === 'matriz' ? 'selected' : '' ?>>Matriz</option>
                            <option value="filial" <?= ($empresa['tipo_empresa'] ?? '') === 'filial' ? 'selected' : '' ?>>Filial</option>
                        </select>
                    </div>
                    <div class="mb-3" id="empresa_matriz_container" style="display: none;">
                        <label for="empresa_matriz_id" class="form-label">Empresa Matriz</label>
                        <select class="form-select" id="empresa_matriz_id" name="empresa_matriz_id">
                            <option value="">Selecione uma empresa matriz...</option>
                            <?php foreach ($empresas_matriz as $matriz): ?>
                                <option value="<?= $matriz['id'] ?>" <?= ($empresa['empresa_matriz_id'] ?? '') == $matriz['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($matriz['razao_social']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="ativo" name="ativo" value="1" <?= ($empresa['ativo'] ?? 1) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="ativo">Ativo</label>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                    <a href="/empresas" class="btn btn-secondary">Cancelar</a>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tipoEmpresaSelect = document.getElementById('tipo_empresa');
    const empresaMatrizContainer = document.getElementById('empresa_matriz_container');
    
    function toggleEmpresaMatriz() {
        if (tipoEmpresaSelect.value === 'filial') {
            empresaMatrizContainer.style.display = 'block';
        } else {
            empresaMatrizContainer.style.display = 'none';
        }
    }
    
    tipoEmpresaSelect.addEventListener('change', toggleEmpresaMatriz);
    toggleEmpresaMatriz(); // Executa na carga inicial
});
</script> 