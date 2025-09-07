<?php $title = 'Login'; ?>
<div class="container d-flex align-items-center justify-content-center min-vh-100">
  <div class="card shadow-lg p-4" style="max-width: 400px; width: 100%; background-color: rgba(236, 245, 245, 0.95); backdrop-filter: blur(10px);">
    <div class="text-center mb-4">
      <img src="/assets/images/Agil_Login.png" alt="Logo" class="logo-img">
    </div>
    <ul class="nav nav-tabs mb-3" id="loginTabs" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link active" id="login-tab" data-bs-toggle="tab" data-bs-target="#login" type="button" role="tab">Entrar</button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="register-tab" data-bs-toggle="tab" data-bs-target="#register" type="button" role="tab">Cadastrar</button>
      </li>
    </ul>
    <div class="tab-content" id="loginTabsContent">
      <!-- Login -->
      <div class="tab-pane fade show active" id="login" role="tabpanel">
        <form action="/login" method="POST">
          <div class="mb-3">
            <label for="loginEmail" class="form-label">E-mail</label>
            <input type="email" class="form-control" id="loginEmail" name="email" required>
          </div>
          <div class="mb-3">
            <label for="loginSenha" class="form-label">Senha</label>
            <input type="password" class="form-control" id="loginSenha" name="senha" required>
          </div>
          <button type="submit" class="btn btn-primary w-100">Entrar</button>
        </form>
      </div>
      <!-- Cadastro -->
      <div class="tab-pane fade" id="register" role="tabpanel">
        <form action="/registro" method="POST">
          <div class="mb-3">
            <label for="registerNome" class="form-label">Nome</label>
            <input type="text" class="form-control" id="registerNome" name="nome" required>
          </div>
          <div class="mb-3">
            <label for="registerTelefone" class="form-label">Telefone</label>
            <input type="text" class="form-control" id="registerTelefone" name="telefone" required>
          </div>
          <div class="mb-3">
            <label for="registerEmail" class="form-label">E-mail</label>
            <input type="email" class="form-control" id="registerEmail" name="email" required>
          </div>
          <div class="mb-3">
            <label for="registerSenha" class="form-label">Senha</label>
            <input type="password" class="form-control" id="registerSenha" name="senha" required>
          </div>
          <div class="mb-3">
            <label for="registerSenha2" class="form-label">Repetir Senha</label>
            <input type="password" class="form-control" id="registerSenha2" name="senha2" required>
          </div>
          <div class="mb-3">
            <label for="registerCnpj" class="form-label">CNPJ da Empresa</label>
            <input type="text" class="form-control" id="registerCnpj" name="cnpj" required>
          </div>
          <button type="submit" class="btn btn-success w-100">Cadastrar</button>
        </form>
      </div>
    </div>
  </div>
</div>
<script>
// Máscara para telefone e CNPJ
if (typeof IMask !== 'undefined') {
  IMask(document.getElementById('registerTelefone'), { mask: '(00) 00000-0000' });
  IMask(document.getElementById('registerCnpj'), { mask: '00.000.000/0000-00' });
}
</script>
<style>
html, body {
    height: 100%;
    margin: 0;
    padding: 0;
    background-image: url('/assets/images/Background.png');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    background-attachment: fixed;
}

.logo-img {
    max-width: 100%;
    height: auto;
    max-height: 120px;
    width: auto;
    object-fit: contain;
    display: block;
    margin: 0 auto;
    transition: transform 0.3s ease;
}

.logo-img:hover {
    transform: scale(1.05);
}

.nav-tabs {
    border-bottom: 2px solid #dee2e6;
}
.nav-tabs .nav-link {
    color: #223046 !important;
    background:rgb(214, 234, 253) !important;
    border: 1px solid #dee2e6 !important;
    border-bottom: none !important;
    font-weight: 500;
    border-radius: 0.25rem 0.25rem 0 0;
    margin-right: 2px;
}
.nav-tabs .nav-link.active {
    color: #fff !important;
    background: #0056b3 !important;
    border-color: #0056b3 #0056b3 #fff !important;
}
</style> 