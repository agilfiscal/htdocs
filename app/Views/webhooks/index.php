<?php
$title = 'Webhooks';
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Webhooks</h1>
    
    <?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        Operação realizada com sucesso!
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>
    
    <?php if (isset($_GET['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php echo htmlspecialchars($_GET['error']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>
    
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-table me-1"></i>
                Lista de Webhooks
            </div>
            <a href="/webhooks/criar" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Novo Webhook
            </a>
        </div>
        <div class="card-body">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>URL</th>
                        <th>Eventos</th>
                        <th>Status</th>
                        <th>Último Envio</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($webhooks as $webhook): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($webhook['nome']); ?></td>
                        <td><?php echo htmlspecialchars($webhook['url']); ?></td>
                        <td><?php echo htmlspecialchars($webhook['eventos']); ?></td>
                        <td>
                            <span class="badge bg-<?php echo $webhook['status'] === 'ativo' ? 'success' : 'danger'; ?>">
                                <?php echo ucfirst($webhook['status']); ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($webhook['ultimo_envio']): ?>
                                <?php echo date('d/m/Y H:i', strtotime($webhook['ultimo_envio'])); ?>
                            <?php else: ?>
                                Nunca
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="/webhooks/editar/<?php echo $webhook['id']; ?>" class="btn btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-info btn-testar" data-id="<?php echo $webhook['id']; ?>">
                                    <i class="fas fa-play"></i>
                                </button>
                                <button type="button" class="btn btn-danger btn-excluir" data-id="<?php echo $webhook['id']; ?>">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal de Confirmação de Exclusão -->
<div class="modal fade" id="modalExcluir" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Tem certeza que deseja excluir este webhook?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <a href="#" class="btn btn-danger" id="btnConfirmarExclusao">Excluir</a>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Excluir webhook
    document.querySelectorAll('.btn-excluir').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const modal = new bootstrap.Modal(document.getElementById('modalExcluir'));
            document.getElementById('btnConfirmarExclusao').href = `/webhooks/excluir/${id}`;
            modal.show();
        });
    });
    
    // Testar webhook
    document.querySelectorAll('.btn-testar').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const btn = this;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            
            fetch(`/webhooks/testar/${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Webhook testado com sucesso!');
                    } else {
                        alert('Erro ao testar webhook: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('Erro ao testar webhook: ' + error.message);
                })
                .finally(() => {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-play"></i>';
                });
        });
    });
});
</script> 