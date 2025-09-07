<?php
$title = 'Integrações';
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Integrações</h1>
    
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
                Lista de Integrações
            </div>
            <a href="/integracoes/criar" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Nova Integração
            </a>
        </div>
        <div class="card-body">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Tipo</th>
                        <th>Status</th>
                        <th>Data de Criação</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($integracoes as $integracao): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($integracao['nome']); ?></td>
                        <td>
                            <span class="badge bg-<?php 
                                echo match($integracao['tipo']) {
                                    'erp' => 'primary',
                                    'crm' => 'success',
                                    'bi' => 'info',
                                    default => 'secondary'
                                };
                            ?>">
                                <?php echo strtoupper($integracao['tipo']); ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-<?php echo $integracao['status'] === 'ativo' ? 'success' : 'danger'; ?>">
                                <?php echo ucfirst($integracao['status']); ?>
                            </span>
                        </td>
                        <td><?php echo date('d/m/Y H:i', strtotime($integracao['created_at'])); ?></td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="/integracoes/editar/<?php echo $integracao['id']; ?>" class="btn btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-info btn-testar" data-id="<?php echo $integracao['id']; ?>">
                                    <i class="fas fa-play"></i>
                                </button>
                                <button type="button" class="btn btn-danger btn-excluir" data-id="<?php echo $integracao['id']; ?>">
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
                Tem certeza que deseja excluir esta integração?
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
    // Excluir integração
    document.querySelectorAll('.btn-excluir').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const modal = new bootstrap.Modal(document.getElementById('modalExcluir'));
            document.getElementById('btnConfirmarExclusao').href = `/integracoes/excluir/${id}`;
            modal.show();
        });
    });
    
    // Testar integração
    document.querySelectorAll('.btn-testar').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const btn = this;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            
            fetch(`/integracoes/testar/${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Integração testada com sucesso!');
                    } else {
                        alert('Erro ao testar integração: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('Erro ao testar integração: ' + error.message);
                })
                .finally(() => {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-play"></i>';
                });
        });
    });
});
</script> 