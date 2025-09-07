<?php $title = 'Compartilhar Empresa'; ?>
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card">
            <div class="card-body">
                <h3 class="mb-4">Compartilhar Empresa</h3>
                <p>Copie os dados abaixo para compartilhar:</p>
                <textarea class="form-control mb-3" id="dadosEmpresa" rows="8" readonly><?=
"CNPJ: {$empresa['cnpj']}\n" .
"Razão Social: {$empresa['razao_social']}\n" .
"UF: {$empresa['uf']}\n" .
"Cidade: {$empresa['cidade']}\n" .
"Inscrição Estadual: {$empresa['inscricao_estadual']}\n" .
"Telefone: {$empresa['telefone']}\n" .
"Email: {$empresa['email']}\n" .
"Status: {$empresa['status']}\n" .
"Regime: {$empresa['regime']}\n" .
"Representante Legal: {$empresa['representante_legal']}"
?></textarea>
                <button class="btn btn-primary" onclick="copiarDados()"><i class="fas fa-copy"></i> Copiar</button>
                <a href="/empresas" class="btn btn-secondary ms-2">Voltar</a>
            </div>
        </div>
    </div>
</div>
<script>
function copiarDados() {
    var textarea = document.getElementById('dadosEmpresa');
    textarea.select();
    document.execCommand('copy');
    alert('Dados copiados para a área de transferência!');
}
</script> 