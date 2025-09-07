<?php
$title = 'Arquivos';
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Arquivos</h1>
    
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
                Lista de Arquivos
            </div>
            <a href="/arquivos/upload" class="btn btn-primary btn-sm">
                <i class="fas fa-upload"></i> Enviar Arquivo
            </a>
        </div>
        <div class="card-body">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Nome Original</th>
                        <th>Tipo</th>
                        <th>Descrição</th>
                        <th>Tamanho</th>
                        <th>Data de Upload</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($arquivos as $arquivo): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($arquivo['nome_original']); ?></td>
                        <td>
                            <span class="badge bg-<?php 
                                echo match($arquivo['tipo']) {
                                    'xml' => 'primary',
                                    'pdf' => 'danger',
                                    'txt' => 'info',
                                    default => 'secondary'
                                };
                            ?>">
                                <?php echo strtoupper($arquivo['tipo']); ?>
                            </span>
                        </td>
                        <td><?php echo htmlspecialchars($arquivo['descricao']); ?></td>
                        <td><?php echo number_format($arquivo['tamanho'] / 1024, 2) . ' KB'; ?></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($arquivo['created_at'])); ?></td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="/arquivos/download/<?php echo $arquivo['id']; ?>" class="btn btn-info">
                                    <i class="fas fa-download"></i>
                                </a>
                                <button type="button" class="btn btn-danger btn-excluir" data-id="<?php echo $arquivo['id']; ?>">
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
                Tem certeza que deseja excluir este arquivo?
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
    // Excluir arquivo
    document.querySelectorAll('.btn-excluir').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const modal = new bootstrap.Modal(document.getElementById('modalExcluir'));
            document.getElementById('btnConfirmarExclusao').href = `/arquivos/excluir/${id}`;
            modal.show();
        });
    });
});
</script> 