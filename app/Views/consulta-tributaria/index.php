<style>
.ct-row {
    display: flex;
    gap: 16px;
    margin-bottom: 16px;
}
.ct-col {
    background: #f5f7f8;
    border: 2px solid #19506e;
    border-radius: 2px;
    flex: 1 1 0;
    min-width: 320px;
    padding: 12px 24px 18px 24px;
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
}
.ct-title {
    font-size: 1.2rem;
    color: #19506e;
    font-weight: bold;
    text-align: center;
    margin-bottom: 8px;
    border-bottom: 2px solid #19506e;
    padding-bottom: 4px;
    letter-spacing: 0.5px;
}
.ct-fields {
    display: flex;
    flex-wrap: wrap;
    gap: 0 32px;
}
.ct-field-col {
    flex: 1 1 0;
    min-width: 180px;
}
.ct-field {
    font-weight: bold;
    margin-bottom: 6px;
    font-size: 1rem;
}
@media (max-width: 900px) {
    .ct-row { flex-direction: column; }
    .ct-col { min-width: unset; }
}
</style>
<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<div class="container mt-4">
    <h2>Consulta Tributária</h2>
    <form method="POST" action="/consulta-tributaria/buscar" class="row g-3 mb-4" id="formConsultaTributaria" autocomplete="off">
        <div class="col-md-3">
            <label for="ean" class="form-label">EAN</label>
            <input type="text" class="form-control" id="ean" name="ean" value="<?= htmlspecialchars($ean ?? '') ?>" placeholder="Digite o EAN">
        </div>
        <div class="col-md-5 position-relative">
            <label for="descritivo" class="form-label">Descritivo</label>
            <input type="text" class="form-control" id="descritivo" name="descritivo" value="<?= htmlspecialchars($descritivo ?? '') ?>" placeholder="Digite parte do descritivo" autocomplete="off">
            <div id="autocomplete-descritivo" class="list-group position-absolute w-100" style="z-index:10; display:none;"></div>
        </div>
        <!-- Removido o campo de regime fiscal -->
        <div class="col-md-2 d-flex align-items-end gap-2">
            <button type="submit" class="btn btn-primary w-50">Buscar</button>
            <button type="button" class="btn btn-secondary w-50" id="btnLimparFiltro">Limpar</button>
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <button type="button" class="btn btn-success w-50 me-2" id="btnWhatsapp" style="white-space:nowrap;">WhatsApp</button>
            <button type="button" class="btn btn-outline-secondary w-50" id="btnCopyMsg" style="white-space:nowrap;">Copiar</button>
        </div>
    </form>
    <?php if (!empty($resultados)): ?>
        <?php foreach ($resultados as $prod): ?>
            <div class="mb-5">
                <div class="ct-row">
                    <div class="ct-col">
                        <div class="ct-title">Dados do Produto</div>
                        <div class="ct-fields">
                            <div class="ct-field-col">
                                <div class="ct-field">EAN:</div>
                                <div><?= htmlspecialchars($prod['ean'] ?? '') ?></div>
                                <div class="ct-field">Produto:</div>
                                <div><?= htmlspecialchars($prod['descritivo'] ?? '') ?></div>
                            </div>
                            <div class="ct-field-col">
                                <div class="ct-field">NCM:</div>
                                <div><?= htmlspecialchars($prod['ncm'] ?? '') ?></div>
                                <div class="ct-field">CEST:</div>
                                <div><?= htmlspecialchars($prod['cest_revisao'] ?? '') ?></div>
                                <div class="ct-field">Unidade de medida:</div>
                                <div><?= htmlspecialchars($prod['unidade_venda'] ?? '') ?></div>
                            </div>
                            <div class="ct-field-col">
                                <div class="ct-field">FCP:</div>
                                <div><?= htmlspecialchars($prod['fcp_revisao'] ?? '') ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="ct-col">
                        <div class="ct-title">Mercadológico</div>
                        <div class="ct-fields">
                            <div class="ct-field-col">
                                <div class="ct-field">Departamento:</div>
                                <div><?= ($prod['depto'] ?? null) == 0 ? '---' : htmlspecialchars($prod['depto_desc'] ?? '') ?></div>
                                <div class="ct-field">Grupo:</div>
                                <div><?= ($prod['grupo'] ?? null) == 0 ? '---' : htmlspecialchars($prod['grupo_desc'] ?? '') ?></div>
                                
                            </div>
                            <div class="ct-field-col">
                                <div class="ct-field">Seção:</div>
                                <div><?= ($prod['secao'] ?? null) == 0 ? '---' : htmlspecialchars($prod['secao_desc'] ?? '') ?></div>
                                <div class="ct-field">Subgrupo:</div>
                                <div><?= ($prod['subgrupo'] ?? null) == 0 ? '---' : htmlspecialchars($prod['subgrupo_desc'] ?? '') ?></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="ct-row">
                    <div class="ct-col">
                        <div class="ct-title">Impostos Federais</div>
                        <div class="ct-fields">
                            <div class="ct-field-col">
                                <div class="ct-field">CST PIS/COFINS ENTRADA:</div>
                                <div><?= htmlspecialchars($prod['cst_pis_cofins_entrada'] ?? '') ?><?= isset($prod['cst_pis_cofins_entrada_desc']) && $prod['cst_pis_cofins_entrada_desc'] ? ' - ' . htmlspecialchars($prod['cst_pis_cofins_entrada_desc']) : '' ?></div>
                                <div class="ct-field">CST PIS/COFINS SAÍDA:</div>
                                <div><?= htmlspecialchars($prod['cst_pis_cofins_saida'] ?? '') ?><?= isset($prod['cst_pis_cofins_saida_desc']) && $prod['cst_pis_cofins_saida_desc'] ? ' - ' . htmlspecialchars($prod['cst_pis_cofins_saida_desc']) : '' ?></div>
                                <div class="ct-field">ALÍQUOTA IPI:</div>
                                <div><?= htmlspecialchars($prod['aliquota_ipi'] ?? '') ?></div>
                                <div class="ct-field">CST IPI:</div>
                                <div><?= htmlspecialchars($prod['cst_ipi'] ?? '') ?></div>
                                <div class="ct-field">ALÍQUOTA PIS:</div>
                                <div><?= htmlspecialchars($prod['aliquota_pis'] ?? '') ?></div>
                                <div class="ct-field">ALÍQUOTA COFINS:</div>
                                <div><?= htmlspecialchars($prod['aliquota_cofins'] ?? '') ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="ct-col">
                        <div class="ct-title">Impostos Estaduais</div>
                        <div class="ct-fields">
                            <div class="ct-field-col">
                                <div class="ct-field">CST/CSOSN:</div>
                                <div><?= htmlspecialchars($prod['cst_csosn'] ?? '') ?><?= isset($prod['cst_csosn_desc']) && $prod['cst_csosn_desc'] ? ' - ' . htmlspecialchars($prod['cst_csosn_desc']) : '' ?></div>
                                <div class="ct-field">ALÍQUOTA ICMS:</div>
                                <div><?= htmlspecialchars($prod['aliquota_icms'] ?? '') ?></div>
                                <div class="ct-field">AD REM ICMS:</div>
                                <div><?= htmlspecialchars($prod['ad_rem_icms'] ?? '') ?></div>
                                <div class="ct-field">% ALÍQ. RED. BASE CÁLC. ICMS:</div>
                                <div><?= htmlspecialchars($prod['aliquota_red_base_calculo_icms'] ?? '') ?></div>
                                <div class="ct-field">% RED. BASE CÁLC. ICMS:</div>
                                <div><?= htmlspecialchars($prod['red_base_calculo_icms'] ?? '') ?></div>
                                <div class="ct-field">% RED. BASE CÁLC. ICMS ST:</div>
                                <div><?= htmlspecialchars($prod['red_base_calculo_icms_st'] ?? '') ?></div>
                                <div class="ct-field">CÓD. BENEFÍCIO FISCAL:</div>
                                <div><?= htmlspecialchars($prod['cod_beneficio_fiscal'] ?? '') ?></div>
                                <div class="ct-field">PERCENTUAL DIFERIMENTO:</div>
                                <div><?= htmlspecialchars($prod['percentual_diferimento'] ?? '') ?></div>
                                <div class="ct-field">CÓDIGO BENEFÍCIO:</div>
                                <div><?= htmlspecialchars($prod['codigo_beneficio'] ?? '') ?></div>
                            </div>
                            <div class="ct-field-col">
                                <div class="ct-field">SITUAÇÃO TRIBUTÁRIA:</div>
                                <div><?= htmlspecialchars($prod['situacao_tributaria_nome'] ?? '') ?></div>
                                <div class="ct-field">FCP:</div>
                                <div><?= htmlspecialchars($prod['fcp_revisao'] ?? '') ?></div>
                                <div class="ct-field">IVA/MVA:</div>
                                <div><?= htmlspecialchars($prod['iva_mva'] ?? '') ?></div>
                                <div class="ct-field">ANTECIPADO:</div>
                                <div><?= htmlspecialchars($prod['antecipado'] ?? '') ?></div>
                                <div class="ct-field">ALÍQ. ICMS ST:</div>
                                <div><?= htmlspecialchars($prod['aliquota_icms_st'] ?? '') ?></div>
                                <div class="ct-field">DESONERAÇÃO:</div>
                                <div><?= htmlspecialchars($prod['desoneracao'] ?? '') ?></div>
                                <div class="ct-field">CÓDIGO ANP:</div>
                                <div><?= htmlspecialchars($prod['codigo_anp'] ?? '') ?></div>
                                <div class="ct-field">PERCENTUAL ISENÇÃO:</div>
                                <div><?= htmlspecialchars($prod['percentual_isencao'] ?? '') ?></div>
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
        <div class="alert alert-warning">Nenhum produto encontrado para os critérios informados.</div>
    <?php endif; ?>
</div>
<?php if (!empty($ean_sugerir)): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    fetch('/consulta-tributaria/sugerir-ean', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ ean: '<?= htmlspecialchars($ean_sugerir) ?>' })
    })
    .then(r => r.json())
    .then(data => {
        alert(data.message || 'EAN sugerido para cadastro.');
    });
});
</script>
<?php endif; ?>
<script>
// Botão Limpar
const btnLimpar = document.getElementById('btnLimparFiltro');
if (btnLimpar) {
    btnLimpar.onclick = function(e) {
        e.preventDefault();
        window.location.href = '/consulta-tributaria';
    };
}
// Botão WhatsApp e Copiar Mensagem
const btnWhatsapp = document.getElementById('btnWhatsapp');
const btnCopyMsg = document.getElementById('btnCopyMsg');
function montarMensagemConsulta() {
    const resultado = document.querySelector('.mb-5');
    if (!resultado) {
        return null;
    }
    let mensagem = 'Segue resultado da consulta tributária solicitada:\n\n';
    function getValor(label) {
        const fields = resultado.querySelectorAll('.ct-field');
        for (let field of fields) {
            if (field.textContent.trim() === label) {
                const valDiv = field.nextElementSibling;
                if (valDiv) return valDiv.textContent.trim();
            }
        }
        return '';
    }
    mensagem += '*Produto:* ' + getValor('Produto:') + '\n';
    mensagem += '*EAN:* ' + getValor('EAN:') + '\n';
    mensagem += '*NCM:* ' + getValor('NCM:') + '\n';
    mensagem += '*CEST:* ' + getValor('CEST:') + '\n';
    mensagem += '*CST:* ' + getValor('CST/CSOSN:') + '\n';
    mensagem += '*Alíquota ICMS:* ' + getValor('ALÍQUOTA ICMS:') + '\n';
    mensagem += '*CST PIS/COFINS SAÍDA:* ' + getValor('CST PIS/COFINS SAÍDA:') + '\n';
    mensagem += '*Alíquota PIS:* ' + getValor('ALÍQUOTA PIS:') + '\n';
    mensagem += '*Alíquota COFINS:* ' + getValor('ALÍQUOTA COFINS:') + '\n';
    mensagem += '*FCP:* ' + getValor('FCP:') + '\n';
    mensagem += '\nEnviado via *Portal Ágil FIscal* - Consulta Tributária.';
    mensagem += '\nwww.portal.agilfiscal.com.br';
    return mensagem;
}
if (btnWhatsapp) {
    btnWhatsapp.onclick = function(e) {
        e.preventDefault();
        const mensagem = montarMensagemConsulta();
        if (!mensagem) {
            alert('Nenhum resultado para enviar.');
            return;
        }
        const url = 'https://wa.me/?text=' + encodeURIComponent(mensagem);
        window.open(url, '_blank');
    };
}
if (btnCopyMsg) {
    btnCopyMsg.onclick = function(e) {
        e.preventDefault();
        const mensagem = montarMensagemConsulta();
        if (!mensagem) {
            alert('Nenhum resultado para copiar.');
            return;
        }
        navigator.clipboard.writeText(mensagem).then(function() {
            alert('Resultado copiado com sucesso');
        }, function() {
            alert('Não foi possível copiar o resultado.');
        });
    };
}
// Autocomplete Descritivo
const inputDesc = document.getElementById('descritivo');
const autoDiv = document.getElementById('autocomplete-descritivo');
let timer = null;
let autocompleteList = [];
inputDesc.addEventListener('input', function() {
    clearTimeout(timer);
    const val = this.value.trim();
    if (val.length < 2) { autoDiv.style.display = 'none'; autocompleteList = []; return; }
    timer = setTimeout(() => {
        fetch('/consulta-tributaria/autocomplete-descritivo?q=' + encodeURIComponent(val))
            .then(r => r.json())
            .then(data => {
                autoDiv.innerHTML = '';
                autocompleteList = data;
                if (data.length) {
                    data.forEach(item => {
                        const el = document.createElement('button');
                        el.type = 'button';
                        el.className = 'list-group-item list-group-item-action';
                        el.textContent = item;
                        el.onclick = function() {
                            inputDesc.value = item;
                            autoDiv.style.display = 'none';
                        };
                        autoDiv.appendChild(el);
                    });
                    autoDiv.style.display = 'block';
                } else {
                    autoDiv.style.display = 'none';
                }
            });
    }, 250);
});
document.addEventListener('click', function(e) {
    if (!autoDiv.contains(e.target) && e.target !== inputDesc) {
        autoDiv.style.display = 'none';
    }
});
// Forçar seleção do autocomplete
const form = document.getElementById('formConsultaTributaria');
form.addEventListener('submit', function(e) {
    const eanVal = document.getElementById('ean').value.trim();
    const descVal = inputDesc.value.trim();

    // Validação EAN antes do submit
    if (eanVal.length > 0 && !validarEAN(eanVal)) {
        e.preventDefault();
        alert('EAN inválido. Por favor, digite um EAN válido.');
        document.getElementById('ean').focus();
        return false;
    }

    // Se o descritivo estiver preenchido, forçar seleção do autocomplete
    if (descVal.length > 0 && !autocompleteList.includes(descVal)) {
        e.preventDefault();
        alert('Por favor, selecione um item da lista de descritivos sugeridos.');
        inputDesc.focus();
        return false;
    }
    // Permite o submit normalmente (por EAN, por descritivo, ou ambos)
});
// Validador de EAN (EAN-8 ou EAN-13)
function validarEAN(ean) {
    if (!/^[0-9]{8}$|^[0-9]{13}$/.test(ean)) return false;
    let soma = 0, i, mult;
    if (ean.length === 8) {
        for (i = 0; i < 7; i++) {
            soma += parseInt(ean[i]) * (i % 2 === 0 ? 3 : 1);
        }
        let digito = (10 - (soma % 10)) % 10;
        return digito === parseInt(ean[7]);
    } else if (ean.length === 13) {
        for (i = 0; i < 12; i++) {
            mult = i % 2 === 0 ? 1 : 3;
            soma += parseInt(ean[i]) * mult;
        }
        let digito = (10 - (soma % 10)) % 10;
        return digito === parseInt(ean[12]);
    }
    return false;
}
</script> 