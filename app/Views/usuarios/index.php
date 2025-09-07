<?php $title = 'Usuários'; ?>
<script>
    var tipoUsuarioLogado = '<?= $_SESSION['tipo_usuario'] ?? '' ?>';
</script>
<h3 class="mb-3">Usuários do Sistema</h3>
<!-- Filtro de busca -->
<form class="mb-3" style="max-width: 400px;" onsubmit="event.preventDefault(); filtrarUsuarios();">
    <div class="input-group">
        <input type="text" id="filtroUsuarios" class="form-control" placeholder="Buscar por nome, e-mail, telefone...">
        <button id="btnBuscarUsuario" class="btn btn-outline-secondary" type="submit"><i class="fas fa-search"></i> Buscar</button>
    </div>
</form>
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Nome</th>
                        <th>E-mail</th>
                        <th>Telefone</th>
                        <th>Tipo</th>
                        <th>Empresas</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usuarios as $usuario): ?>
                        <tr data-id="<?= $usuario['id'] ?>" data-nome="<?= htmlspecialchars($usuario['nome']) ?>" data-email="<?= htmlspecialchars($usuario['email']) ?>" data-telefone="<?= htmlspecialchars($usuario['telefone'] ?? '') ?>" data-tipo="<?= htmlspecialchars($usuario['tipo']) ?>">
                            <td><?= htmlspecialchars($usuario['nome']) ?></td>
                            <td><?= htmlspecialchars($usuario['email']) ?></td>
                            <td><?= htmlspecialchars($usuario['telefone'] ?? '-') ?></td>
                            <td><?= $usuario['tipo'] === 'master' ? '<span class="badge bg-success">Master</span>' : ($usuario['tipo'] === 'admin' ? '<span class="badge bg-primary">Administrador</span>' : '<span class="badge bg-secondary">Operador</span>') ?></td>
                            <td><?= $usuario['tipo'] === 'master' ? 'Todas' : htmlspecialchars($usuario['empresas'] ?? '-') ?></td>
                            <td><?= $usuario['ativo'] ? '<span class="badge bg-success">Ativo</span>' : '<span class="badge bg-danger">Inativo</span>' ?></td>
                            <td>
                                <a href="#" class="btn btn-sm btn-warning btn-editar-usuario"><i class="fas fa-edit"></i> Editar</a>
                                <?php if ($usuario['ativo']): ?>
                                    <!-- <a href="#" class="btn btn-sm btn-danger btn-inativar-usuario"><i class="fas fa-user-slash"></i> Inativar</a> -->
                                <?php else: ?>
                                    <a href="#" class="btn btn-sm btn-success btn-ativar-usuario"><i class="fas fa-user-check"></i> Ativar</a>
                                <?php endif; ?>
                                <a href="#" class="btn btn-sm btn-info btn-resetar-senha"><i class="fas fa-key"></i> Resetar Senha</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php if (isset(
    $totalPaginas) && $totalPaginas > 1): ?>
<nav aria-label="Navegação de páginas">
  <ul class="pagination">
    <li class="page-item<?= $pagina <= 1 ? ' disabled' : '' ?>">
      <a class="page-link" href="?pagina=<?= $pagina - 1 ?>" tabindex="-1">Anterior</a>
    </li>
    <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
      <li class="page-item<?= $i == $pagina ? ' active' : '' ?>">
        <a class="page-link" href="?pagina=<?= $i ?>"><?= $i ?></a>
      </li>
    <?php endfor; ?>
    <li class="page-item<?= $pagina >= $totalPaginas ? ' disabled' : '' ?>">
      <a class="page-link" href="?pagina=<?= $pagina + 1 ?>">Próxima</a>
    </li>
  </ul>
</nav>
<?php endif; ?>

<!-- Modal Editar Usuário -->
<div class="modal fade" id="modalEditarUsuario" tabindex="-1" aria-labelledby="modalEditarUsuarioLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="formEditarUsuario">
        <div class="modal-header">
          <h5 class="modal-title" id="modalEditarUsuarioLabel">Editar Usuário</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id" id="editUsuarioId">
          <div class="mb-3">
            <label for="editUsuarioNome" class="form-label">Nome</label>
            <input type="text" class="form-control" id="editUsuarioNome" name="nome" required>
          </div>
          <div class="mb-3">
            <label for="editUsuarioEmail" class="form-label">E-mail</label>
            <input type="email" class="form-control" id="editUsuarioEmail" name="email" required>
          </div>
          <div class="mb-3">
            <label for="editUsuarioTelefone" class="form-label">Telefone</label>
            <input type="text" class="form-control" id="editUsuarioTelefone" name="telefone">
          </div>
          <div class="mb-3 tipo-select">
            <label for="editUsuarioTipoSelect" class="form-label">Tipo</label>
            <select class="form-select" id="editUsuarioTipoSelect" name="tipo">
              <option value="admin">Administrador</option>
              <option value="master">Master</option>
              <option value="operator">Operador</option>
            </select>
          </div>
          <?php if ($_SESSION['tipo'] === 'admin'): ?>
          <div class="mb-3" id="empresas-select-container">
            <label for="editUsuarioEmpresas" class="form-label">Empresas</label>
            <select class="form-select" id="editUsuarioEmpresas" name="empresas[]" multiple>
              <?php foreach ($empresas as $empresa): ?>
                <option value="<?= $empresa['id'] ?>"><?= htmlspecialchars($empresa['razao_social']) ?></option>
              <?php endforeach; ?>
            </select>
            <div class="form-text">Segure Ctrl (ou Cmd) para selecionar várias empresas.</div>
          </div>
          <?php endif; ?>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Salvar Alterações</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Resetar Senha -->
<div class="modal fade" id="modalResetarSenha" tabindex="-1" aria-labelledby="modalResetarSenhaLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="formResetarSenha">
        <div class="modal-header">
          <h5 class="modal-title" id="modalResetarSenhaLabel">Resetar Senha</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id" id="resetUsuarioId">
          <div class="mb-3">
            <label for="novaSenha" class="form-label">Nova Senha</label>
            <input type="password" class="form-control" id="novaSenha" name="nova_senha" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Resetar Senha</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
function filtrarUsuarios() {
  var filtro = document.getElementById('filtroUsuarios').value.toLowerCase();
  document.querySelectorAll('table tbody tr').forEach(function(tr) {
    var nome = tr.querySelector('td:nth-child(1)').textContent.toLowerCase();
    var email = tr.querySelector('td:nth-child(2)').textContent.toLowerCase();
    var telefone = tr.querySelector('td:nth-child(3)').textContent.toLowerCase();
    if (nome.includes(filtro) || email.includes(filtro) || telefone.includes(filtro)) {
      tr.style.display = '';
    } else {
      tr.style.display = 'none';
    }
  });
}
document.addEventListener('DOMContentLoaded', function() {
  document.getElementById('filtroUsuarios').addEventListener('input', filtrarUsuarios);
  document.getElementById('btnBuscarUsuario').addEventListener('click', filtrarUsuarios);

  // Abrir modal de edição
  document.querySelectorAll('.btn-editar-usuario').forEach(function(btn) {
    btn.addEventListener('click', function(e) {
      e.preventDefault();
      var tr = btn.closest('tr');
      var userId = tr.dataset.id;
      var isSelf = userId == '<?= $_SESSION['usuario_id'] ?>';
      var isTipo3 = '<?= $_SESSION['tipo'] ?>' === 'operator';
      
      document.getElementById('editUsuarioId').value = userId;
      document.getElementById('editUsuarioNome').value = tr.dataset.nome;
      document.getElementById('editUsuarioEmail').value = tr.dataset.email;
      document.getElementById('editUsuarioTelefone').value = tr.dataset.telefone;
      
      // Se for usuário tipo 3 (operator) editando ele mesmo, remove o campo de tipo
      if (isTipo3 && isSelf) {
        var tipoSelect = document.querySelector('.tipo-select');
        if (tipoSelect) {
          tipoSelect.remove();
        }
        var empresasSelect = document.getElementById('empresas-select-container');
        if (empresasSelect) empresasSelect.remove();
      } else {
        document.getElementById('editUsuarioTipoSelect').value = tr.dataset.tipo;
        // Se for admin, buscar empresas vinculadas
        <?php if ($_SESSION['tipo'] === 'admin'): ?>
        var select = document.getElementById('editUsuarioEmpresas');
        if (select) {
          // Limpa seleção
          Array.from(select.options).forEach(opt => opt.selected = false);
          fetch('/usuarios/empresasVinculadas?id=' + userId)
            .then(response => response.json())
            .then(data => {
              Array.from(select.options).forEach(opt => {
                if (data.empresas_vinculadas.includes(opt.value)) {
                  opt.selected = true;
                }
              });
            });
        }
        <?php endif; ?>
      }
      
      var modal = new bootstrap.Modal(document.getElementById('modalEditarUsuario'));
      modal.show();
    });
  });

  // Abrir modal de resetar senha
  document.querySelectorAll('.btn-resetar-senha').forEach(function(btn) {
    btn.addEventListener('click', function(e) {
      e.preventDefault();
      var tr = btn.closest('tr');
      document.getElementById('resetUsuarioId').value = tr.dataset.id;
      var modal = new bootstrap.Modal(document.getElementById('modalResetarSenha'));
      modal.show();
    });
  });

  // Submissão do formulário de edição (AJAX)
  document.getElementById('formEditarUsuario').addEventListener('submit', function(e) {
    e.preventDefault();
    var form = e.target;
    var data = new FormData(form);
    
    fetch('/usuarios/editar', {
      method: 'POST',
      body: data
    })
    .then(response => response.json())
    .then(json => {
      if (json.sucesso) {
        location.reload();
      } else {
        alert(json.erro || 'Erro ao salvar alterações.');
      }
    });
  });

  // Submissão do formulário de reset de senha (AJAX)
  document.getElementById('formResetarSenha').addEventListener('submit', function(e) {
    e.preventDefault();
    var form = e.target;
    var data = new FormData(form);
    fetch('/usuarios/resetar-senha', {
      method: 'POST',
      body: data
    })
    .then(response => response.json())
    .then(json => {
      if (json.sucesso) {
        location.reload();
      } else {
        alert(json.erro || 'Erro ao resetar senha.');
      }
    });
  });

  // Evento para ativar usuário
  document.querySelectorAll('.btn-ativar-usuario').forEach(function(btn) {
    btn.addEventListener('click', function(e) {
      e.preventDefault();
      if (confirm('Tem certeza que deseja ativar este usuário?')) {
        var tr = btn.closest('tr');
        var userId = tr.dataset.id;
        
        fetch('/usuarios/ativar', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({ id: userId })
        })
        .then(response => response.json())
        .then(json => {
          if (json.sucesso) {
            location.reload();
          } else {
            alert(json.erro || 'Erro ao ativar usuário.');
          }
        });
      }
    });
  });
});
</script> 