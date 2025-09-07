<?php
$title = 'Editar Webhook';
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Editar Webhook</h1>
    
    <?php if (isset($error)): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php echo htmlspecialchars($error); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>
    
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-edit me-1"></i>
            Editar Webhook
        </div>
        <div class="card-body">
            <form action="/webhooks/editar/<?php echo $webhook['id']; ?>" method="post">
                <div class="mb-3">
                    <label for="nome" class="form-label">Nome</label>
                    <input type="text" class="form-control" id="nome" name="nome" value="<?php echo htmlspecialchars($webhook['nome']); ?>" required>
                </div>
                
                <div class="mb-3">
                    <label for="url" class="form-label">URL</label>
                    <input type="url" class="form-control" id="url" name="url" value="<?php echo htmlspecialchars($webhook['url']); ?>" required>
                    <div class="form-text">URL completa para onde os eventos serão enviados</div>
                </div>
                
                <div class="mb-3">
                    <label for="eventos" class="form-label">Eventos</label>
                    <select class="form-select" id="eventos" name="eventos[]" multiple required>
                        <option value="nota_entrada" <?php echo in_array('nota_entrada', explode(',', $webhook['eventos'])) ? 'selected' : ''; ?>>Nota de Entrada</option>
                        <option value="nota_saida" <?php echo in_array('nota_saida', explode(',', $webhook['eventos'])) ? 'selected' : ''; ?>>Nota de Saída</option>
                        <option value="nota_cancelada" <?php echo in_array('nota_cancelada', explode(',', $webhook['eventos'])) ? 'selected' : ''; ?>>Nota Cancelada</option>
                        <option value="nota_inutilizada" <?php echo in_array('nota_inutilizada', explode(',', $webhook['eventos'])) ? 'selected' : ''; ?>>Nota Inutilizada</option>
                        <option value="nota_denegada" <?php echo in_array('nota_denegada', explode(',', $webhook['eventos'])) ? 'selected' : ''; ?>>Nota Denegada</option>
                    </select>
                    <div class="form-text">Selecione os eventos que este webhook deve receber</div>
                </div>
                
                <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status" required>
                        <option value="ativo" <?php echo $webhook['status'] === 'ativo' ? 'selected' : ''; ?>>Ativo</option>
                        <option value="inativo" <?php echo $webhook['status'] === 'inativo' ? 'selected' : ''; ?>>Inativo</option>
                    </select>
                </div>
                
                <div class="mb-3">
                    <label for="secret" class="form-label">Chave Secreta</label>
                    <input type="text" class="form-control" id="secret" name="secret" value="<?php echo htmlspecialchars($webhook['secret'] ?? ''); ?>">
                    <div class="form-text">Chave secreta para assinatura dos eventos (opcional)</div>
                </div>
                
                <div class="d-flex justify-content-between">
                    <a href="/webhooks" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar select2 para múltipla seleção
    $('#eventos').select2({
        theme: 'bootstrap-5',
        placeholder: 'Selecione os eventos',
        allowClear: true
    });
});
</script> 