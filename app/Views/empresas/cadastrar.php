<?php $title = 'Cadastrar Empresa'; ?>
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card">
            <div class="card-body">
                <h3 class="mb-4">Cadastrar Nova Empresa</h3>
                <?php if ($erro): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
                <?php endif; ?>
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="cnpj" class="form-label">CNPJ</label>
                        <input type="text" class="form-control" id="cnpj" name="cnpj" maxlength="18" placeholder="Digite o CNPJ" required>
                        <div class="form-text">Apenas números, sem pontos ou traços.</div>
                    </div>
                    <button type="submit" class="btn btn-success"><i class="fas fa-search"></i> Consultar e Cadastrar</button>
                    <a href="/empresas" class="btn btn-secondary ms-2">Voltar</a>
                </form>
            </div>
        </div>
    </div>
</div> 