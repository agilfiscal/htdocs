<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<?php $title = 'Configurações do Sistema'; ?>

<style>
/* Estilos para as abas */
.nav-tabs {
    border-bottom: 2px solid #dee2e6;
}

.nav-tabs .nav-link {
    color: #212529 !important;
    background: none;
    border: 1px solid transparent;
    border-top-left-radius: 0.25rem;
    border-top-right-radius: 0.25rem;
    padding: 0.75rem 1.25rem;
    font-weight: 500;
    transition: all 0.2s ease-in-out;
}

.nav-tabs .nav-link:hover {
    border-color: #e9ecef #e9ecef #dee2e6;
    color: #0056b3;
}

.nav-tabs .nav-link.active {
    color: #0056b3 !important;
    background-color: #fff !important;
    border-color: #dee2e6 #dee2e6 #fff;
    border-bottom: 2px solid #0056b3;
}

.nav-tabs .nav-link i {
    margin-right: 0.5rem;
}

/* Estilos para o conteúdo das abas */
.tab-content {
    background-color: #fff;
    border: 1px solid #dee2e6;
    border-top: none;
    padding: 1.5rem;
    border-radius: 0 0 0.25rem 0.25rem;
}

/* Estilos para os cards */
.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

/* Estilos para os formulários */
.form-label {
    font-weight: 500;
    color: #495057;
}

.form-control:focus {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

/* Estilos para os botões */
.btn-primary {
    background-color: #0056b3;
    border-color: #0056b3;
}

.btn-primary:hover {
    background-color: #004494;
    border-color: #004494;
}

/* Estilos para os switches */
.form-switch .form-check-input:checked {
    background-color: #0056b3;
    border-color: #0056b3;
}

/* Estilos para os tooltips */
.tooltip {
    font-size: 0.875rem;
}

/* Estilos para os alertas */
.alert {
    border-radius: 0.25rem;
    margin-bottom: 1rem;
}

/* Estilos para os selects */
.form-select:focus {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Configurações do Sistema</h3>
                </div>
                <div class="card-body">
                    <?php if (isset($_SESSION['sucesso'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?= $_SESSION['sucesso'] ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php unset($_SESSION['sucesso']); ?>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['erro'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= $_SESSION['erro'] ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php unset($_SESSION['erro']); ?>
                    <?php endif; ?>

                        <!-- Abas de navegação -->
                        <ul class="nav nav-tabs mb-4" id="configTabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="empresa-tab" data-bs-toggle="tab" href="#empresa" role="tab">
                                    <i class="fas fa-building"></i> Empresa
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="seguranca-tab" data-bs-toggle="tab" href="#seguranca" role="tab">
                                    <i class="fas fa-shield-alt"></i> Segurança
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="notificacoes-tab" data-bs-toggle="tab" href="#notificacoes" role="tab">
                                    <i class="fas fa-bell"></i> Notificações
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="agenda-tab" data-bs-toggle="tab" href="#agenda" role="tab">
                                    <i class="fas fa-calendar"></i> Agenda
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="financeiro-tab" data-bs-toggle="tab" href="#financeiro" role="tab">
                                    <i class="fas fa-dollar-sign"></i> Financeiro
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="integracoes-tab" data-bs-toggle="tab" href="#integracoes" role="tab">
                                    <i class="fas fa-plug"></i> Integrações
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="sistema-tab" data-bs-toggle="tab" href="#sistema" role="tab">
                                    <i class="fas fa-cogs"></i> Sistema
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="usuarios-tab" data-bs-toggle="tab" href="#usuarios" role="tab">
                                    <i class="fas fa-user-cog"></i> Usuários
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="atendimento-tab" data-bs-toggle="tab" href="#atendimento" role="tab">
                                    <i class="fas fa-headset"></i> Atendimento
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="log-tab" data-bs-toggle="tab" href="#log" role="tab">
                                    <i class="fas fa-clipboard-list"></i> LOG
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="release-tab" data-bs-toggle="tab" href="#release" role="tab">
                                    <i class="fas fa-rocket"></i> Release
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="produtos-tab" data-bs-toggle="tab" href="#produtos" role="tab">
                                    <i class="fas fa-box"></i> Produtos
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="arquivos-tab" data-bs-toggle="tab" href="#arquivos" role="tab">
                                    <i class="fas fa-file-alt"></i> Arquivos
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="limpeza-lote-tab" data-bs-toggle="tab" href="#limpeza-lote" role="tab">
                                    <i class="fas fa-eraser"></i> Limpeza em Lote
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="sugestoes-ean-tab" data-bs-toggle="tab" href="#sugestoes-ean" role="tab">
                                    <i class="fas fa-barcode"></i> Sugestões EAN
                                </a>
                            </li>
                        </ul>

                        <!-- Conteúdo das abas -->
                        <div class="tab-content" id="configTabsContent">
                            <!-- Aba Empresa -->
                            <div class="tab-pane fade show active" id="empresa" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h4 class="mb-3">Informações da Empresa</h4>
                                        
                                        <div class="mb-3">
                                            <label for="company_name" class="form-label">Nome da Empresa</label>
                                            <input type="text" class="form-control" id="company_name" name="config[company_name]" 
                                                   value="<?= htmlspecialchars($configuracoes['company_name'] ?? '') ?>">
                                        </div>

                                        <div class="mb-3">
                                            <label for="company_cnpj" class="form-label">CNPJ</label>
                                            <input type="text" class="form-control" id="company_cnpj" name="config[company_cnpj]" 
                                                   value="<?= htmlspecialchars($configuracoes['company_cnpj'] ?? '') ?>">
                                        </div>

                                        <div class="mb-3">
                                            <label for="company_address" class="form-label">Endereço</label>
                                            <input type="text" class="form-control" id="company_address" name="config[company_address]" 
                                                   value="<?= htmlspecialchars($configuracoes['company_address'] ?? '') ?>">
                                        </div>

                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="company_city" class="form-label">Cidade</label>
                                                    <input type="text" class="form-control" id="company_city" name="config[company_city]" 
                                                           value="<?= htmlspecialchars($configuracoes['company_city'] ?? '') ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="company_state" class="form-label">Estado</label>
                                                    <input type="text" class="form-control" id="company_state" name="config[company_state]" 
                                                           value="<?= htmlspecialchars($configuracoes['company_state'] ?? '') ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="company_zip" class="form-label">CEP</label>
                                                    <input type="text" class="form-control" id="company_zip" name="config[company_zip]" 
                                                           value="<?= htmlspecialchars($configuracoes['company_zip'] ?? '') ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <h4 class="mb-3">Identidade Visual</h4>
                                        
                                        <div class="mb-3">
                                            <label for="logo" class="form-label">Logo da Empresa</label>
                                            <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
                                            <?php if (!empty($configuracoes['company_logo'])): ?>
                                                <div class="mt-2">
                                                    <img src="<?= htmlspecialchars($configuracoes['company_logo']) ?>" 
                                                         alt="Logo atual" class="img-thumbnail" style="max-height: 100px;">
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                        <div class="mb-3">
                                            <label for="company_timezone" class="form-label">Fuso Horário</label>
                                            <select class="form-select" id="company_timezone" name="config[company_timezone]">
                                                <option value="America/Sao_Paulo" <?= ($configuracoes['company_timezone'] ?? '') === 'America/Sao_Paulo' ? 'selected' : '' ?>>
                                                    São Paulo (GMT-3)
                                                </option>
                                                <option value="America/Manaus" <?= ($configuracoes['company_timezone'] ?? '') === 'America/Manaus' ? 'selected' : '' ?>>
                                                    Manaus (GMT-4)
                                                </option>
                                                <option value="America/Belem" <?= ($configuracoes['company_timezone'] ?? '') === 'America/Belem' ? 'selected' : '' ?>>
                                                    Belém (GMT-3)
                                                </option>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label for="company_language" class="form-label">Idioma</label>
                                            <select class="form-select" id="company_language" name="config[company_language]">
                                                <option value="pt_BR" <?= ($configuracoes['company_language'] ?? '') === 'pt_BR' ? 'selected' : '' ?>>
                                                    Português (Brasil)
                                                </option>
                                                <option value="en" <?= ($configuracoes['company_language'] ?? '') === 'en' ? 'selected' : '' ?>>
                                                    English
                                                </option>
                                                <option value="es" <?= ($configuracoes['company_language'] ?? '') === 'es' ? 'selected' : '' ?>>
                                                    Español
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Aba Segurança -->
                            <div class="tab-pane fade" id="seguranca" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h4 class="mb-3">Políticas de Senha</h4>
                                        
                                        <div class="mb-3">
                                            <label for="password_min_length" class="form-label">Tamanho Mínimo</label>
                                            <input type="number" class="form-control" id="password_min_length" 
                                                   name="config[password_min_length]" min="6" max="32"
                                                   value="<?= htmlspecialchars($configuracoes['password_min_length'] ?? '8') ?>">
                                        </div>

                                        <div class="mb-3">
                                            <label for="password_expiry" class="form-label">Validade da Senha (dias)</label>
                                            <input type="number" class="form-control" id="password_expiry" 
                                                   name="config[password_expiry]" min="0" max="365"
                                                   value="<?= htmlspecialchars($configuracoes['password_expiry'] ?? '90') ?>">
                                            <div class="form-text">0 = sem expiração</div>
                                        </div>

                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="password_require_special" 
                                                       name="config[password_require_special]" value="1"
                                                       <?= ($configuracoes['password_require_special'] ?? '1') === '1' ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="password_require_special">
                                                    Exigir caracteres especiais
                                                </label>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="password_require_numbers" 
                                                       name="config[password_require_numbers]" value="1"
                                                       <?= ($configuracoes['password_require_numbers'] ?? '1') === '1' ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="password_require_numbers">
                                                    Exigir números
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <h4 class="mb-3">Segurança do Sistema</h4>
                                        
                                        <div class="mb-3">
                                            <label for="session_timeout" class="form-label">Tempo de Sessão (minutos)</label>
                                            <input type="number" class="form-control" id="session_timeout" 
                                                   name="config[session_timeout]" min="5" max="1440"
                                                   value="<?= htmlspecialchars($configuracoes['session_timeout'] ?? '30') ?>">
                                        </div>

                                        <div class="mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="enable_2fa" 
                                                       name="config[enable_2fa]" value="1"
                                                       <?= ($configuracoes['enable_2fa'] ?? '0') === '1' ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="enable_2fa">
                                                    Habilitar Autenticação em Dois Fatores (2FA)
                                                </label>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="allowed_ips" class="form-label">IPs Permitidos</label>
                                            <textarea class="form-control" id="allowed_ips" name="config[allowed_ips][]" 
                                                      rows="3" placeholder="Um IP por linha"><?= htmlspecialchars(implode("\n", $configuracoes['allowed_ips'] ?? [])) ?></textarea>
                                            <div class="form-text">Deixe em branco para permitir todos os IPs</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Aba Notificações -->
                            <div class="tab-pane fade" id="notificacoes" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h4 class="mb-3">Canais de Notificação</h4>
                                        
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="notify_email" 
                                                       name="config[notification_channels][]" value="email"
                                                       <?= in_array('email', $configuracoes['notification_channels'] ?? []) ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="notify_email">
                                                    E-mail
                                                </label>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="notify_sms" 
                                                       name="config[notification_channels][]" value="sms"
                                                       <?= in_array('sms', $configuracoes['notification_channels'] ?? []) ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="notify_sms">
                                                    SMS
                                                </label>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="notify_push" 
                                                       name="config[notification_channels][]" value="push"
                                                       <?= in_array('push', $configuracoes['notification_channels'] ?? []) ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="notify_push">
                                                    Notificações Push
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <h4 class="mb-3">Preferências de Alertas</h4>
                                        
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="alert_system" 
                                                       name="config[alert_types][]" value="system"
                                                       <?= in_array('system', $configuracoes['alert_types'] ?? []) ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="alert_system">
                                                    Alertas do Sistema
                                                </label>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="alert_security" 
                                                       name="config[alert_types][]" value="security"
                                                       <?= in_array('security', $configuracoes['alert_types'] ?? []) ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="alert_security">
                                                    Alertas de Segurança
                                                </label>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="alert_business" 
                                                       name="config[alert_types][]" value="business"
                                                       <?= in_array('business', $configuracoes['alert_types'] ?? []) ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="alert_business">
                                                    Alertas de Negócio
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Aba Agenda -->
                            <div class="tab-pane fade" id="agenda" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h4 class="mb-3">Horário Comercial</h4>
                                        
                                        <div class="mb-3">
                                            <label for="business_hours_start" class="form-label">Horário de Início</label>
                                            <input type="time" class="form-control" id="business_hours_start" 
                                                   name="config[business_hours][start]"
                                                   value="<?= htmlspecialchars($configuracoes['business_hours']['start'] ?? '09:00') ?>">
                                        </div>

                                        <div class="mb-3">
                                            <label for="business_hours_end" class="form-label">Horário de Término</label>
                                            <input type="time" class="form-control" id="business_hours_end" 
                                                   name="config[business_hours][end]"
                                                   value="<?= htmlspecialchars($configuracoes['business_hours']['end'] ?? '18:00') ?>">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <h4 class="mb-3">Feriados</h4>
                                        
                                        <div class="mb-3">
                                            <label for="holidays" class="form-label">Feriados Personalizados</label>
                                            <textarea class="form-control" id="holidays" name="config[holidays][]" 
                                                      rows="5" placeholder="Um feriado por linha (formato: DD/MM/YYYY)"><?= htmlspecialchars(implode("\n", $configuracoes['holidays'] ?? [])) ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Aba Financeiro -->
                            <div class="tab-pane fade" id="financeiro" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h4 class="mb-3">Configurações de Cobrança</h4>
                                        
                                        <div class="mb-3">
                                            <label for="billing_cycle" class="form-label">Ciclo de Cobrança</label>
                                            <select class="form-select" id="billing_cycle" name="config[billing_cycle]">
                                                <option value="monthly" <?= ($configuracoes['billing_cycle'] ?? '') === 'monthly' ? 'selected' : '' ?>>
                                                    Mensal
                                                </option>
                                                <option value="quarterly" <?= ($configuracoes['billing_cycle'] ?? '') === 'quarterly' ? 'selected' : '' ?>>
                                                    Trimestral
                                                </option>
                                                <option value="yearly" <?= ($configuracoes['billing_cycle'] ?? '') === 'yearly' ? 'selected' : '' ?>>
                                                    Anual
                                                </option>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label for="payment_methods" class="form-label">Formas de Pagamento</label>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="payment_credit" 
                                                       name="config[payment_methods][]" value="credit"
                                                       <?= in_array('credit', $configuracoes['payment_methods'] ?? []) ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="payment_credit">
                                                    Cartão de Crédito
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="payment_boleto" 
                                                       name="config[payment_methods][]" value="boleto"
                                                       <?= in_array('boleto', $configuracoes['payment_methods'] ?? []) ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="payment_boleto">
                                                    Boleto Bancário
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="payment_pix" 
                                                       name="config[payment_methods][]" value="pix"
                                                       <?= in_array('pix', $configuracoes['payment_methods'] ?? []) ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="payment_pix">
                                                    PIX
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <h4 class="mb-3">Dados Fiscais</h4>
                                        
                                        <div class="mb-3">
                                            <label for="invoice_series" class="form-label">Série de Notas Fiscais</label>
                                            <input type="text" class="form-control" id="invoice_series" 
                                                   name="config[invoice_series]"
                                                   value="<?= htmlspecialchars($configuracoes['invoice_series'] ?? '') ?>">
                                        </div>

                                        <div class="mb-3">
                                            <label for="invoice_number" class="form-label">Número Inicial de Notas</label>
                                            <input type="number" class="form-control" id="invoice_number" 
                                                   name="config[invoice_number]"
                                                   value="<?= htmlspecialchars($configuracoes['invoice_number'] ?? '1') ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Aba Integrações -->
                            <div class="tab-pane fade" id="integracoes" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h4 class="mb-3">Chaves de API</h4>
                                        
                                        <div class="mb-3">
                                            <label for="api_key" class="form-label">Chave de API Principal</label>
                                            <input type="text" class="form-control" id="api_key" 
                                                   name="config[api_keys][main]"
                                                   value="<?= htmlspecialchars($configuracoes['api_keys']['main'] ?? '') ?>">
                                        </div>

                                        <div class="mb-3">
                                            <label for="api_secret" class="form-label">Chave Secreta</label>
                                            <input type="password" class="form-control" id="api_secret" 
                                                   name="config[api_keys][secret]"
                                                   value="<?= htmlspecialchars($configuracoes['api_keys']['secret'] ?? '') ?>">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <h4 class="mb-3">Webhooks</h4>
                                        
                                        <div class="mb-3">
                                            <label for="webhook_url" class="form-label">URL do Webhook</label>
                                            <input type="url" class="form-control" id="webhook_url" 
                                                   name="config[webhooks][url]"
                                                   value="<?= htmlspecialchars($configuracoes['webhooks']['url'] ?? '') ?>">
                                        </div>

                                        <div class="mb-3">
                                            <label for="webhook_secret" class="form-label">Chave Secreta do Webhook</label>
                                            <input type="password" class="form-control" id="webhook_secret" 
                                                   name="config[webhooks][secret]"
                                                   value="<?= htmlspecialchars($configuracoes['webhooks']['secret'] ?? '') ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Aba Sistema -->
                            <div class="tab-pane fade" id="sistema" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h4 class="mb-3">Aparência</h4>
                                        
                                        <div class="mb-3">
                                            <label for="theme" class="form-label">Tema</label>
                                            <select class="form-select" id="theme" name="config[theme]">
                                                <option value="light" <?= ($configuracoes['theme'] ?? '') === 'light' ? 'selected' : '' ?>>
                                                    Claro
                                                </option>
                                                <option value="dark" <?= ($configuracoes['theme'] ?? '') === 'dark' ? 'selected' : '' ?>>
                                                    Escuro
                                                </option>
                                                <option value="auto" <?= ($configuracoes['theme'] ?? '') === 'auto' ? 'selected' : '' ?>>
                                                    Automático
                                                </option>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label for="date_format" class="form-label">Formato de Data</label>
                                            <select class="form-select" id="date_format" name="config[date_format]">
                                                <option value="d/m/Y" <?= ($configuracoes['date_format'] ?? '') === 'd/m/Y' ? 'selected' : '' ?>>
                                                    DD/MM/YYYY
                                                </option>
                                                <option value="Y-m-d" <?= ($configuracoes['date_format'] ?? '') === 'Y-m-d' ? 'selected' : '' ?>>
                                                    YYYY-MM-DD
                                                </option>
                                                <option value="m/d/Y" <?= ($configuracoes['date_format'] ?? '') === 'm/d/Y' ? 'selected' : '' ?>>
                                                    MM/DD/YYYY
                                                </option>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label for="time_format" class="form-label">Formato de Hora</label>
                                            <select class="form-select" id="time_format" name="config[time_format]">
                                                <option value="H:i" <?= ($configuracoes['time_format'] ?? '') === 'H:i' ? 'selected' : '' ?>>
                                                    24 horas
                                                </option>
                                                <option value="h:i A" <?= ($configuracoes['time_format'] ?? '') === 'h:i A' ? 'selected' : '' ?>>
                                                    12 horas
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <h4 class="mb-3">Limites do Sistema</h4>
                                        
                                        <div class="mb-3">
                                            <label for="max_file_size" class="form-label">Tamanho Máximo de Arquivo (MB)</label>
                                            <input type="number" class="form-control" id="max_file_size" 
                                                   name="config[max_file_size]" min="1" max="100"
                                                   value="<?= htmlspecialchars($configuracoes['max_file_size'] ?? '10') ?>">
                                        </div>

                                        <div class="mb-3">
                                            <label for="max_users" class="form-label">Limite de Usuários</label>
                                            <input type="number" class="form-control" id="max_users" 
                                                   name="config[max_users]" min="1"
                                                   value="<?= htmlspecialchars($configuracoes['max_users'] ?? '10') ?>">
                                        </div>

                                        <div class="mb-3">
                                            <label for="backup_frequency" class="form-label">Frequência de Backup</label>
                                            <select class="form-select" id="backup_frequency" name="config[backup_frequency]">
                                                <option value="daily" <?= ($configuracoes['backup_frequency'] ?? '') === 'daily' ? 'selected' : '' ?>>
                                                    Diário
                                                </option>
                                                <option value="weekly" <?= ($configuracoes['backup_frequency'] ?? '') === 'weekly' ? 'selected' : '' ?>>
                                                    Semanal
                                                </option>
                                                <option value="monthly" <?= ($configuracoes['backup_frequency'] ?? '') === 'monthly' ? 'selected' : '' ?>>
                                                    Mensal
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Aba Usuários -->
                            <div class="tab-pane fade" id="usuarios" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h4 class="mb-3">Políticas de Usuário</h4>
                                        <div class="mb-3">
                                            <label for="user_default_role" class="form-label">Permissão Padrão</label>
                                            <select class="form-select" id="user_default_role" name="config[user_default_role]">
                                                <option value="admin" <?= ($configuracoes['user_default_role'] ?? '') === 'admin' ? 'selected' : '' ?>>Administrador</option>
                                                <option value="master" <?= ($configuracoes['user_default_role'] ?? '') === 'master' ? 'selected' : '' ?>>Master</option>
                                                <option value="operator" <?= ($configuracoes['user_default_role'] ?? '') === 'operator' ? 'selected' : '' ?>>Operador</option>
                                            </select>
                                            <div class="form-text">
                                                <strong>Administrador:</strong> Controle total do sistema, gerencia tudo e tem acesso a todas as empresas.<br>
                                                <strong>Master:</strong> Gerencia apenas a empresa vinculada, pode cadastrar operadores.<br>
                                                <strong>Operador:</strong> Executa tarefas e tem acesso restrito.
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="user_default_status" class="form-label">Status Padrão</label>
                                            <select class="form-select" id="user_default_status" name="config[user_default_status]">
                                                <option value="active" <?= ($configuracoes['user_default_status'] ?? '') === 'active' ? 'selected' : '' ?>>Ativo</option>
                                                <option value="inactive" <?= ($configuracoes['user_default_status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inativo</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="user_limit" class="form-label">Limite de Usuários</label>
                                            <input type="number" class="form-control" id="user_limit" name="config[user_limit]" min="1" value="<?= htmlspecialchars($configuracoes['user_limit'] ?? '10') ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <h4 class="mb-3">Cadastro e Convite</h4>
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="user_invite_enabled" name="config[user_invite_enabled]" value="1" <?= ($configuracoes['user_invite_enabled'] ?? '0') === '1' ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="user_invite_enabled">
                                                    Permitir convite de novos usuários
                                                </label>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="user_auto_activate" name="config[user_auto_activate]" value="1" <?= ($configuracoes['user_auto_activate'] ?? '0') === '1' ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="user_auto_activate">
                                                    Ativação automática de novos usuários
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-12">
                                        <h4 class="mb-3">Permissões dos Operadores</h4>
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-hover">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Usuário</th>
                                                        <?php foreach ($modulosRestritos as $modulo => $nome): ?>
                                                            <th><?= htmlspecialchars($nome) ?></th>
                                                        <?php endforeach; ?>
                                                        <th>Ações</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($usuarios as $usuario): ?>
                                                        <tr>
                                                            <td>
                                                                <?= htmlspecialchars($usuario['nome']) ?><br>
                                                                <small class="text-muted"><?= htmlspecialchars($usuario['email']) ?></small>
                                                            </td>
                                                            <?php foreach ($modulosRestritos as $modulo => $nome): ?>
                                                                <td class="text-center">
                                                                    <div class="form-check form-switch">
                                                                        <input class="form-check-input permissao-check" type="checkbox" 
                                                                               data-usuario="<?= $usuario['id'] ?>" 
                                                                               data-modulo="<?= $modulo ?>"
                                                                               <?= $usuario['permissoes'][$modulo] ? 'checked' : '' ?>>
                                                                    </div>
                                                                </td>
                                                            <?php endforeach; ?>
                                                            <td>
                                                                <button class="btn btn-primary btn-sm salvar-permissoes" 
                                                                        data-usuario="<?= $usuario['id'] ?>">
                                                                    Salvar
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Aba Atendimento -->
                            <div class="tab-pane fade" id="atendimento" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h4 class="mb-3">Empresas Atendidas no Dia</h4>
                                        <p>Selecione as empresas que você irá atender hoje. Esta seleção é obrigatória a cada login.</p>
                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalEmpresasAtendidas">
                                            Selecionar Empresas
                                        </button>
                                        <div id="empresasAtendidasSelecionadas" class="mt-3">
                                            <!-- Lista das empresas selecionadas será exibida aqui -->
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Aba LOG -->
                            <div class="tab-pane fade" id="log" role="tabpanel">
                                <h4 class="mb-3">Logs do Sistema</h4>
                                <!-- Dropdown para seleção de log -->
                                <div class="mb-3" style="max-width:300px;">
                                    <select class="form-select" id="logTipoDropdown">
                                        <option value="log-resolucao" selected>Resolução de Nota</option>
                                        <option value="log-envio-notas">Envio de Notas</option>
                                        <option value="log-romaneio">Notas em Romaneio</option>
                                        <!-- Adicione aqui outros logs futuros -->
                                    </select>
                                </div>
                                <div class="tab-content" id="logTabsContent">
                                    <div class="log-content" id="log-resolucao-content">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped" id="tabela-log-resolucao">
                                                <thead>
                                                    <tr class="filtros-tabela">
                                                        <th>
                                                            <label for="filtroDataInicialResolucao" class="form-label mb-0" style="font-size:12px;">Data Inicial</label>
                                                            <input type="date" class="form-control form-control-sm" id="filtroDataInicialResolucao" placeholder="Data Inicial">
                                                        </th>
                                                        <th>
                                                            <label for="filtroUsuarioResolucao" class="form-label mb-0" style="font-size:12px;">Usuário</label>
                                                            <select class="form-select form-select-sm" id="filtroUsuarioResolucao"><option value="">Todos</option></select>
                                                        </th>
                                                        <th>
                                                            <label for="filtroEmpresaResolucao" class="form-label mb-0" style="font-size:12px;">Empresa</label>
                                                            <select class="form-select form-select-sm" id="filtroEmpresaResolucao"><option value="">Todas</option></select>
                                                        </th>
                                                        <th>
                                                            <label for="filtroDataFinalResolucao" class="form-label mb-0" style="font-size:12px;">Data Final</label>
                                                            <input type="date" class="form-control form-control-sm" id="filtroDataFinalResolucao" placeholder="Data Final">
                                                        </th>
                                                        <th style="vertical-align:bottom;" colspan="2">
                                                            <div class="d-flex gap-1">
                                                                <button type="button" class="btn btn-primary btn-sm w-100" id="btnFiltrarResolucao">Filtrar</button>
                                                                <button type="button" class="btn btn-secondary btn-sm w-100" id="btnLimparResolucao">Limpar</button>
                                                                <button type="button" class="btn btn-success btn-sm w-100" id="btnExportarResolucao">Exportar</button>
                                                            </div>
                                                        </th>
                                                    </tr>
                                                    <tr>
                                                        <th>Data</th>
                                                        <th>Usuário</th>
                                                        <th>Número da Nota</th>
                                                        <th>Empresa</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr><td colspan="5" class="text-center">Carregando...</td></tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="log-content d-none" id="log-envio-notas-content">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped" id="tabela-log-envio-notas">
                                                <thead>
                                                    <tr class="filtros-tabela">
                                                        <th>
                                                            <label for="filtroDataInicialEnvioNotas" class="form-label mb-0" style="font-size:12px;">Data Inicial</label>
                                                            <input type="date" class="form-control form-control-sm" id="filtroDataInicialEnvioNotas" placeholder="Data Inicial">
                                                        </th>
                                                        <th>
                                                            <label for="filtroEmpresaEnvioNotas" class="form-label mb-0" style="font-size:12px;">Empresa</label>
                                                            <select class="form-select form-select-sm" id="filtroEmpresaEnvioNotas"><option value="">Todas</option></select>
                                                        </th>
                                                        <th>
                                                            <label for="filtroDataFinalEnvioNotas" class="form-label mb-0" style="font-size:12px;">Data Final</label>
                                                            <input type="date" class="form-control form-control-sm" id="filtroDataFinalEnvioNotas" placeholder="Data Final">
                                                        </th>
                                                        <th style="vertical-align:bottom;" colspan="9">
                                                            <div class="d-flex gap-1">
                                                                <button type="button" class="btn btn-primary btn-sm w-100" id="btnFiltrarEnvioNotas">Filtrar</button>
                                                                <button type="button" class="btn btn-secondary btn-sm w-100" id="btnLimparEnvioNotas">Limpar</button>
                                                                <button type="button" class="btn btn-success btn-sm w-100" id="btnExportarEnvioNotas">Exportar</button>
                                                            </div>
                                                        </th>
                                                    </tr>
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>Número</th>
                                                        <th>CNPJ</th>
                                                        <th>Razão Social</th>
                                                        <th>Data Emissão</th>
                                                        <th>Valor</th>
                                                        <th>Chave</th>
                                                        <th>UF</th>
                                                        <th>Status</th>
                                                        <th>Tipo</th>
                                                        <th>Data Consulta</th>
                                                        <th>Empresa</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr><td colspan="12" class="text-center">Carregando...</td></tr>
                                                </tbody>
                                            </table>
                                            <div id="paginacao-envio-notas" class="mt-2 d-flex justify-content-center"></div>
                                        </div>
                                    </div>
                                    <div class="log-content d-none" id="log-romaneio-content">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped" id="tabela-log-romaneio">
                                                <thead>
                                                    <tr class="filtros-tabela">
                                                        <th>
                                                            <label for="filtroDataInicialRomaneio" class="form-label mb-0" style="font-size:12px;">Data Inicial</label>
                                                            <input type="date" class="form-control form-control-sm" id="filtroDataInicialRomaneio" placeholder="Data Inicial">
                                                        </th>
                                                        <th>
                                                            <label for="filtroEmpresaRomaneio" class="form-label mb-0" style="font-size:12px;">Empresa</label>
                                                            <select class="form-select form-select-sm" id="filtroEmpresaRomaneio"><option value="">Todas</option></select>
                                                        </th>
                                                        <th>
                                                            <label for="filtroDataFinalRomaneio" class="form-label mb-0" style="font-size:12px;">Data Final</label>
                                                            <input type="date" class="form-control form-control-sm" id="filtroDataFinalRomaneio" placeholder="Data Final">
                                                        </th>
                                                        <th style="vertical-align:bottom;" colspan="2">
                                                            <div class="d-flex gap-1">
                                                                <button type="button" class="btn btn-primary btn-sm w-100" id="btnFiltrarRomaneio">Filtrar</button>
                                                                <button type="button" class="btn btn-secondary btn-sm w-100" id="btnLimparRomaneio">Limpar</button>
                                                                <button type="button" class="btn btn-success btn-sm w-100" id="btnExportarRomaneio">Exportar</button>
                                                            </div>
                                                        </th>
                                                    </tr>
                                                    <tr>
                                                        <th>Data</th>
                                                        <th>Empresa</th>
                                                        <th>Chave</th>
                                                        <th>Tipo Empresa</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr><td colspan="5" class="text-center">Carregando...</td></tr>
                                                </tbody>
                                            </table>
                                            <div id="paginacao-romaneio" class="mt-2 d-flex justify-content-center"></div>
                                        </div>
                                    </div>
                                </div>
                                <script>
                                // Exibir/ocultar conteúdo do log conforme seleção do dropdown
                                document.addEventListener('DOMContentLoaded', function() {
                                    const dropdown = document.getElementById('logTipoDropdown');
                                    const logContents = document.querySelectorAll('.log-content');
                                    function mostrarLogSelecionado() {
                                        logContents.forEach(div => div.classList.add('d-none'));
                                        const selected = dropdown.value + '-content';
                                        const el = document.getElementById(selected);
                                        if (el) el.classList.remove('d-none');
                                        // Carregar dados do log ao trocar
                                        if (dropdown.value === 'log-resolucao') {
                                            carregarLogResolucao();
                                        } else if (dropdown.value === 'log-envio-notas') {
                                            carregarLogEnvioNotas();
                                        } else if (dropdown.value === 'log-romaneio') {
                                            carregarLogRomaneio();
                                        }
                                    }
                                    dropdown.addEventListener('change', mostrarLogSelecionado);
                                    mostrarLogSelecionado();

                                    // Carregar dados ao abrir a tela (garante que ambos logs carregam ao menos uma vez)
                                    carregarLogResolucao();
                                    carregarLogEnvioNotas();
                                    carregarLogRomaneio();
                                });
                                </script>
                            </div>

                            <!-- Aba Release -->
                            <div class="tab-pane fade" id="release" role="tabpanel">
                                <h4 class="mb-3">Release</h4>
                                <ul class="timeline-release" style="list-style:none;padding-left:0;">
                                <li style="margin-bottom:18px;">
                                        <div style="font-weight:bold;color:#0056b3;">v1.0.16 <span style="color:#888;font-size:13px;">- 04/09/2025</span></div>
                                        <div>
                                             • Corrigido a notificação de resolução de notas fiscais para operador.<br>
                                             • Agora o o usuáro Operador só vê as notificações da empresa atrelada.<br>
                                                                                     </div>
                                    </li>
                                <li style="margin-bottom:18px;">
                                        <div style="font-weight:bold;color:#0056b3;">v1.0.15 <span style="color:#888;font-size:13px;">- 03/09/2025</span></div>
                                        <div>
                                             • Conserto das notificações de notas fiscais.<br>
                                             • Inclusão do badge de comentários no sistema.<br>
                                             • Inclusão do dropdown de comentários no sistema.<br>
                                             • Inclusão de histórico de comentários no sistema.<br>
                                             • Implementação de notificação de resolução de notas fiscais para adimn e operador.<br>
                                             • Conserto do bug das notificações de resolução de notas fiscais com mais de uma loja selecionada.<br>
                                             • Retirado o limite de notificações de resolução de notas fiscais.<br>
                                                                                     </div>
                                    </li>
                                <li style="margin-bottom:18px;">
                                        <div style="font-weight:bold;color:#0056b3;">v1.0.14 <span style="color:#888;font-size:13px;">- 01/09/2025</span></div>
                                        <div>
                                             • Inclusão de relatório da agenda de pagamentos.<br>
                                             • Lista de fornecedores a pagar na agenda de pagamentos.<br>
                                             • Reposicionamento da previsão para os próximos 12 meses.<br>
                                             • Consertado o bug do filtro na agenda de pagamentos.<br>
                                        </div>
                                    </li>
                                <li style=
                                <li style="margin-bottom:18px;">
                                        <div style="font-weight:bold;color:#0056b3;">v1.0.13 <span style="color:#888;font-size:13px;">- 28/08/2025</span></div>
                                        <div>
                                             • Correção de bugs dos dados brutos da classificação de desossa.<br>
                                             • Recolhimento automático da seção de dados brutos da classificação de desossa.<br>
                                             • Melhoria do relatório da desossa.<br>
                                        </div>
                                    </li>
                                <li style="margin-bottom:18px;">
                                        <div style="font-weight:bold;color:#0056b3;">v1.0.12 <span style="color:#888;font-size:13px;">- 25/08/2025</span></div>
                                        <div>
                                             • Adcionado a classificação de desossa na seção extras..<br>
                                        </div>
                                    </li>
                                <li style="margin-bottom:18px;">
                                        <div style="font-weight:bold;color:#0056b3;">v1.0.11 <span style="color:#888;font-size:13px;">- 05/08/2025</span></div>
                                        <div>
                                             • Adcionado a calculadora de custo de produção.<br>
                                        </div>
                                    </li>
                                <li style="margin-bottom:18px;">
                                        <div style="font-weight:bold;color:#0056b3;">v1.0.10 <span style="color:#888;font-size:13px;">- 24/07/2025</span></div>
                                        <div>
                                             • Alterado background da tela de login.<br>
                                             • Implementado destaque no menu lateral.<br>
                                        </div>
                                    </li>
                                <li style="margin-bottom:18px;">
                                        <div style="font-weight:bold;color:#0056b3;">v1.0.9 <span style="color:#888;font-size:13px;">- 24/07/2025</span></div>
                                        <div>
                                             • Criação da tabela arius_financeiro para armazenar os dados de financeiro.<br>
                                              • Adicionado funcionalidade de upload de arquivos de financeiro.<br>
                                              • Atualização do modal para exibir os dados do Número do Financeiro.<br>
                                              • Correção das páginas calculadora, consulta CNPJ e construtor de placas que estavam abrindo sem login.<br>
                                              • Ao selecionar uma empresa para o atendimento, o sistema agora seleciona a empresa automaticamente nos filtros de Dashboard e Auditoria.<br>
                                        </div>
                                    </li>
                                <li style="margin-bottom:18px;">
                                        <div style="font-weight:bold;color:#0056b3;">v1.0.8 <span style="color:#888;font-size:13px;">- 23/07/2025</span></div>
                                        <div>
                                             • Adcionado Menu Extras com submenus.<br>
                                              • Adcionado calculadora de margem e preço.<br>
                                              • Adcionado funcionalidade para consultar CNPJ.<br>
                                              • Adicionado construtor de placas.<br>
                                        </div>
                                    </li>
                                <li style="margin-bottom:18px;">
                                        <div style="font-weight:bold;color:#0056b3;">v1.0.7 <span style="color:#888;font-size:13px;">- 21/07/2025</span></div>
                                        <div>
                                             • Adcionado botão de WhatsApp e Copiar Resultado na página de Consulta Tributária.<br>
                                              • Adcionado aba de Sugestões EAN.<br>
                                              • Agora voce pode consultar os EANs sugeridos pelo sistema e exclui-lo após a inclusão no banco de dados.<br>
                                              • Adicionado funcionalidade de exportação TXT para as Sugestões EAN.<br>
                                        </div>
                                    </li>
                                <li style="margin-bottom:18px;">
                                        <div style="font-weight:bold;color:#0056b3;">v1.0.6 <span style="color:#888;font-size:13px;">- 20/07/2025</span></div>
                                        <div>
                                             • Correção das observações do modal de detalhes da escrituração fiscal.<br>
                                             • Atualização de layout do modal.<br>
                                             • Inclusão do campo do número fiscal no modal de detalhes.<br>
                                        </div>
                                    </li>
                                <li style="margin-bottom:18px;">
                                        <div style="font-weight:bold;color:#0056b3;">v1.0.5 <span style="color:#888;font-size:13px;">- 11/07/2025</span></div>
                                        <div>
                                            • Ajuste na lógica de Notas de Transferência: agora considera apenas empresas do tipo <b>filial</b>, removendo a verificação por ids fixos.<br>
                                            • Melhoria de consistência entre Dashboard e Auditoria.<br>
                                            • Nova estilização do Dashboard.<br>
                                        </div>
                                    </li>
                                <li style="margin-bottom:18px;">
                                        <div style="font-weight:bold;color:#0056b3;">v1.0.4 <span style="color:#888;font-size:13px;">- 07/07/2025</span></div>
                                        <div>
                                            • Adicionado uma página para Consulta Tributária.<br>
                                            • Layout detalhado, moderno e responsivo, com blocos para dados do produto, mercadológico, impostos federais e estaduais.<br>
                                            • Exibição dos descritivos mercadológicos (departamento, seção, grupo, subgrupo) ao invés dos códigos numéricos, com fallback para "---" quando não houver descritivo.<br>
                                            • Exibição dos códigos e descrições de CST PIS/COFINS, CST ICMS e CSOSN, integrando tabelas auxiliares.<br>
                                            • Exibição da situação tributária textual, conforme tabela de equivalência.<br>
                                            • Autocomplete obrigatório no campo Descritivo: só permite busca se o usuário selecionar um item da lista sugerida.<br>
                                            • Validação de EAN antes do submit: só permite busca com EANs válidos (EAN-8 ou EAN-13).<br>
                                            • Botão Limpar para resetar todos os filtros rapidamente.<br>
                                            • Quando o usuário digita um EAN válido que não existe na base, o sistema sugere automaticamente esse EAN para futura inclusão, salvando na tabela sugestoes_ean.<br>
                                            • Mensagem amigável ao usuário ao sugerir um novo EAN.<br>
                                            • Ajustes na interface de busca e exibição de produtos, com melhoria na usabilidade e visualização dos dados.<br>
                                            • Criação das tabelas: CST_pis_cofins, cst_icms, CSOSN, equivalencia_cst_icms_csosn, situacao_tributaria, Mercadologico, sugestoes_ean.<br>
                                            • Ajuste do fluxo de sugestão de EAN para garantir que a sugestão seja salva mesmo sem recarregar a página.
                                        </div>
                                    </li>
                                <li style="margin-bottom:18px;">
                                        <div style="font-weight:bold;color:#0056b3;">v1.0.3 <span style="color:#888;font-size:13px;">- 03/07/2025</span></div>
                                        <div>
                                            • Adicionado aba de Limpeza em Lote.<br>
                                            • Exibição da última atualização no dashboard para empresa filtrada.<br>
                                            • Melhorias nos logs: filtros por usuário, empresa e data, exportação CSV e padronização visual dos filtros.<br>
                                            • Log de uploads: listagem completa, botão de desfazer envio com rollback seguro e registro detalhado de exclusão.<br>
                                            • Ajustes de sessão e roteamento para garantir correta identificação do usuário nas ações de desfazer.<br>
                                            • Correção de erros de integridade referencial ao desfazer uploads do tipo Sefaz.<br>
                                            • Padronização do campo tipo_arquivo e status nos logs.<br>
                                            • Relatórios de Romaneio e Envio de Notas: filtros obrigatórios, paginação, exportação completa e layout padronizado.<br>
                                            • Remoção da opção de desfazer uploads de fornecedores.<br>
                                            • Diversos ajustes e correções para garantir consistência e usabilidade nos relatórios e logs.
                                        </div>
                                    </li>
                                    <li style="margin-bottom:18px;">
                                        <div style="font-weight:bold;color:#0056b3;">v1.0.2<span style="color:#888;font-size:13px;">- 01/07/2025</span></div>
                                        <div>• Criação do log de resolução de notas.<br>• Notificações multiempresa e botão de resolver.<br>• Nova interface de notificações.<br>• Abas de LOG, Release, Produtos e Arquivos nas configurações.</div>
                                    </li>
                                    <li style="margin-bottom:18px;">
                                        <div style="font-weight:bold;color:#0056b3;">v1.0.1 <span style="color:#888;font-size:13px;">- 15/06/2025</span></div>
                                        <div>• Ajustes de segurança e performance.<br>• Melhorias no cadastro de empresas.<br>• Ajustes na agenda de pagamentos.</div>
                                    </li>
                                    <li style="margin-bottom:18px;">
                                        <div style="font-weight:bold;color:#0056b3;">v1.0.0 <span style="color:#888;font-size:13px;">- 01/06/2025</span></div>
                                        <div>• Lançamento da plataforma.</div>
                                    </li>
                                </ul>
                            </div>

                            <!-- Aba Produtos -->
                            <div class="tab-pane fade" id="produtos" role="tabpanel">
                                <h4 class="mb-3">Produtos</h4>
                                <div class="row">
                                    <div class="col-md-6">
                                        <form id="formImportaBase" action="/configuracoes/importar-produtos-base" method="POST" enctype="multipart/form-data">
                                            <div class="mb-3">
                                                <label for="planilha_base" class="form-label">Importar Planilha Base</label>
                                                <input type="file" class="form-control" id="planilha_base" name="planilha_base" accept=".xls,.xlsx" required>
                                            </div>
                                            <button type="submit" class="btn btn-primary">Importar Base</button>
                                        </form>
                                    </div>
                                    <div class="col-md-6">
                                        <form id="formImportaRevisada" action="/configuracoes/importar-produtos-revisada" method="POST" enctype="multipart/form-data">
                                            <div class="mb-3">
                                                <label for="planilha_revisada" class="form-label">Importar Planilha Revisada</label>
                                                <input type="file" class="form-control" id="planilha_revisada" name="planilha_revisada" accept=".xls,.xlsx" required>
                                            </div>
                                            <button type="submit" class="btn btn-success">Importar Revisada</button>
                                            
                                        </form>
                                        <label for="planilha_revisada" class="form-label"><br>Aviso Técnico – Processamento de Itens Divergentes<br>
O sistema realiza a análise completa da planilha enviada, independentemente da quantidade de registros. <br>No entanto, por motivos de desempenho e segurança, somente os primeiros 1.000 itens divergentes são processados por envio.<br>
Caso existam mais de 1.000 divergências, será necessário reenviar a mesma planilha até que todas sejam gradualmente atualizadas.<br>
                                    Portanto, verifique se a planilha enviada contém mais de 1.000 itens divergentes.</label>
                                    </div>
                                </div>

                                <?php if (!empty($_SESSION['produtos_revisados_diferencas'])): ?>
                                    <div class="mt-4" style="width:100%;">
                                        <h5>Produtos Revisados com Diferenças</h5>
                                        <form id="formAtualizaSelecionados" method="POST" action="/configuracoes/atualizar-produtos-revisados">
                                        <div class="table-responsive" style="max-height:600px;overflow:auto;width:100%;">
                                            <table class="table table-bordered table-sm align-middle w-100" style="min-width:1200px;table-layout:fixed;width:100%;">
                                                <thead style="position:sticky;top:0;background:#fff;z-index:2;">
                                                    <tr>
                                                        <th style="width:40px; text-align:center;">
                                                            <input type="checkbox" id="checkTodosProdutos" onclick="
                                                                var checks = document.querySelectorAll('.check-produto-revisao');
                                                                for(let i=0;i<checks.length;i++) checks[i].checked = this.checked;
                                                            ">
                                                        </th>
                                                        <th style="width:120px;">EAN</th>
                                                        <th style="width:220px;">Descrição</th>
                                                        <th style="width:160px;">Campo</th>
                                                        <th style="width:320px;">Valor Antigo</th>
                                                        <th style="width:320px;">Novo Valor</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $lastKey = null;
                                                    foreach ($_SESSION['produtos_revisados_diferencas'] as $idx => $prod):
                                                        $key = md5($prod['ean'] . '|' . $prod['descricao']);
                                                        $rowspan = count($prod['diferencas']);
                                                        $first = true;
                                                        foreach ($prod['diferencas'] as $campo => $val): ?>
                                                            <tr>
                                                                <?php if ($first): ?>
                                                                    <td rowspan="<?= $rowspan ?>" style="vertical-align:middle;text-align:center;">
                                                                        <input type="checkbox" class="check-produto-revisao" name="produtos[]" value="<?= htmlspecialchars($prod['ean']) ?>|<?= base64_encode($prod['descricao']) ?>|<?= base64_encode($prod['diferencas']['descricao']['novo'] ?? $prod['descricao']) ?>">
                                                                    </td>
                                                                    <td rowspan="<?= $rowspan ?>" style="vertical-align:middle;word-break:break-all;">
                                                                        <?= htmlspecialchars($prod['ean']) ?>
                                                                    </td>
                                                                    <td rowspan="<?= $rowspan ?>" style="vertical-align:middle;word-break:break-word;">
                                                                        <?= htmlspecialchars($prod['descricao']) ?>
                                                                    </td>
                                                                <?php $first = false; endif; ?>
                                                                <td style="word-break:break-word;">
                                                                    <?= htmlspecialchars(strtoupper(str_replace('_',' ', $campo))) ?>
                                                                </td>
                                                                <td style="background:#ffe0e0;word-break:break-word;white-space:pre-line;">
                                                                    <?= htmlspecialchars($val['antigo']) ?>
                                                                </td>
                                                                <td style="background:#e0ffe0;word-break:break-word;white-space:pre-line;">
                                                                    <?= htmlspecialchars($val['novo']) ?>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach;
                                                    endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <button type="submit" class="btn btn-primary mt-2">Atualizar Selecionados</button>
                                        </form>
                                        <div class="alert alert-info mt-2">Esses produtos já estavam revisados. Para atualizar, selecione e confirme abaixo.</div>
                                    </div>
                                    <?php unset($_SESSION['produtos_revisados_diferencas']); ?>
                                <?php endif; ?>
                            </div>

                            <!-- Aba Arquivos -->
                            <div class="tab-pane fade" id="arquivos" role="tabpanel">
                            <form action="/configuracoes/salvar-arquivos" method="POST" enctype="multipart/form-data">
                                <h4 class="mb-3">Arquivos Enviados</h4>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped" id="tabela-arquivos-enviados">
                                        <thead>
                                            <tr class="filtros-tabela">
                                                <th>
                                                    <label for="filtroDataInicialArquivo" class="form-label mb-0" style="font-size:12px;">Data Inicial</label>
                                                    <input type="date" class="form-control form-control-sm" id="filtroDataInicialArquivo">
                                                </th>
                                                <th>
                                                    <label for="filtroUsuarioArquivo" class="form-label mb-0" style="font-size:12px;">Usuário</label>
                                                    <select class="form-select form-select-sm" id="filtroUsuarioArquivo"><option value="">Todos</option></select>
                                                </th>
                                                <th>
                                                    <label for="filtroEmpresaArquivo" class="form-label mb-0" style="font-size:12px;">Empresa</label>
                                                    <select class="form-select form-select-sm" id="filtroEmpresaArquivo"><option value="">Todas</option></select>
                                                </th>
                                                <th>
                                                    <label for="filtroTipoArquivo" class="form-label mb-0" style="font-size:12px;">Tipo</label>
                                                    <select class="form-select form-select-sm" id="filtroTipoArquivo">
                                                        <option value="">Todos</option>
                                                        <option value="Sefaz">Sefaz</option>
                                                        <option value="Fornecedores">Fornecedores</option>
                                                        <option value="Romaneio">Romaneio</option>
                                                        <option value="Desconhecimento">Desconhecimento</option>
                                                        <option value="Lançamentos">Lançamentos</option>
                                                        <!-- Adicione outros tipos se necessário -->
                                                    </select>
                                                </th>
                                                <th>
                                                    <label for="filtroStatusArquivo" class="form-label mb-0" style="font-size:12px;">Status</label>
                                                    <select class="form-select form-select-sm" id="filtroStatusArquivo">
                                                        <option value="">Todos</option>
                                                        <option value="ativo">Ativo</option>
                                                        <option value="excluido">Excluído</option>
                                                    </select>
                                                </th>
                                                <th>
                                                    <label for="filtroDataFinalArquivo" class="form-label mb-0" style="font-size:12px;">Data Final</label>
                                                    <input type="date" class="form-control form-control-sm" id="filtroDataFinalArquivo">
                                                </th>
                                                <th style="vertical-align:bottom;">
                                                    <button type="button" class="btn btn-primary btn-sm w-100" id="btnFiltrarArquivos">Filtrar</button>
                                                    <button type="button" class="btn btn-secondary btn-sm w-100 mt-1" id="btnLimparArquivos">Limpar</button>
                                                </th>
                                            </tr>
                                            <tr>
                                                <th>Data</th>
                                                <th>Usuário</th>
                                                <th>Empresa</th>
                                                <th>Tipo</th>
                                                <th>Status</th>
                                                <th>Nome Original</th>
                                                <th>Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr><td colspan="7" class="text-center">Carregando...</td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </form>
                            </div>
                            
                            <!-- Aba Sugestões EAN -->
                            <div class="tab-pane fade" id="sugestoes-ean" role="tabpanel">
                                <h4 class="mb-3">Sugestões EAN</h4>
                                <div class="mb-3">
                                    <button type="button" class="btn btn-success btn-sm me-2" onclick="exportarSugestoesEANTXT()">
                                        <i class="fas fa-file-text"></i> Exportar TXT
                                    </button>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped" id="tabela-sugestoes-ean">
                                        <thead>
                                            <tr>
                                                <th>EAN</th>
                                                <th>Data da Sugestão</th>
                                                <th>Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr><td colspan="3" class="text-center">Carregando...</td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <!-- Aba Limpeza em Lote -->
                            <div class="tab-pane fade" id="limpeza-lote" role="tabpanel">
                            <form action="/configuracoes/limpeza-lote" method="POST">
                                <h4 class="mb-3">Limpeza em Lote</h4>
                                <div class="alert alert-warning">Esta ação irá <b>apagar todos os dados</b> das tabelas Auditoria de Notas, Notas, Desconhecimento, Romaneio e Fornecedores da empresa selecionada. <b>Não pode ser desfeita!</b></div>
                                <form id="formLimpezaLote" class="row g-3">
                                    <div class="col-md-6">
                                        <label for="empresaLimpezaLote" class="form-label">Empresa</label>
                                        <select class="form-select" id="empresaLimpezaLote" name="empresa_id" required>
                                            <option value="">Selecione a empresa</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 d-flex align-items-end">
                                        <button type="button" class="btn btn-danger" id="btnZerarBanco" disabled>Zerar Banco</button>
                                    </div>
                                </form>
                                </form>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary" style="background:#2c3e50;color:#fff;border:none;">
                                <i class="fas fa-save"></i> Salvar Configurações
                            </button>
                        </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Seleção de Empresas Atendidas -->
<div class="modal fade" id="modalEmpresasAtendidas" tabindex="-1" aria-labelledby="modalEmpresasAtendidasLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalEmpresasAtendidasLabel">Selecionar Empresas para Atendimento</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <div class="modal-body">
        <form id="formEmpresasAtendidas">
          <div class="mb-3">
            <label for="filtroEmpresasAtendidas" class="form-label">Filtrar por CNPJ ou Razão Social</label>
            <input type="text" class="form-control" id="filtroEmpresasAtendidas" placeholder="Digite o CNPJ ou Razão Social...">
          </div>
          <div class="mb-3" style="max-height: 350px; overflow-y: auto;">
            <div id="listaEmpresasAtendidas">
              <!-- Lista de empresas com checkboxes será preenchida via JS -->
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" id="salvarEmpresasAtendidas">Salvar Seleção</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal de confirmação de desfazer -->
<div class="modal fade" id="modalDesfazerArquivo" tabindex="-1" aria-labelledby="modalDesfazerArquivoLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalDesfazerArquivoLabel">Desfazer Envio de Arquivo</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="idArquivoDesfazer">
        <div class="mb-3">
          <label for="motivoDesfazerArquivo" class="form-label">Motivo da exclusão</label>
          <input type="text" class="form-control" id="motivoDesfazerArquivo" placeholder="Digite o motivo">
        </div>
        <div class="alert alert-warning">Esta ação irá remover todos os dados inseridos por este arquivo e POSTERIORES a ele. Tem certeza? Essa ação não pode ser desfeita.</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-danger" id="btnConfirmarDesfazerArquivo">Desfazer</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal de confirmação de limpeza em lote -->
<div class="modal fade" id="modalConfirmarLimpezaLote" tabindex="-1" aria-labelledby="modalConfirmarLimpezaLoteLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalConfirmarLimpezaLoteLabel">Confirmar Limpeza em Lote</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="empresaIdLimpezaLote">
        <div class="alert alert-danger">Tem certeza que deseja <b>ZERAR</b> todos os dados das tabelas para esta empresa? Esta ação é <b>irreversível</b>!</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-danger" id="btnConfirmarLimpezaLote">Zerar Banco</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal de feedback para importação de produtos -->
<div class="modal fade" id="modalMensagemImportaBase" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Importação de Produtos</h5></div>
      <div class="modal-body"><span id="modalMensagemImportaBaseTexto"></span></div>
      <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button></div>
    </div>
  </div>
</div>

<!-- Spinner de carregamento para importação de produtos -->
<div id="spinnerImportaBase" class="modal" tabindex="-1" style="display:none; background:rgba(0,0,0,0.2)">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="background:transparent; border:none; box-shadow:none;">
      <div class="modal-body text-center">
        <div class="spinner-border text-primary" role="status" style="width: 4rem; height: 4rem;">
          <span class="visually-hidden">Carregando...</span>
        </div>
        <div class="mt-3 text-white">Aguarde, importando produtos...</div>
      </div>
    </div>
  </div>
</div>

<!-- Spinner de carregamento para importação de produtos revisados -->
<div id="spinnerImportaRevisada" class="modal" tabindex="-1" style="display:none; background:rgba(0,0,0,0.2)">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="background:transparent; border:none; box-shadow:none;">
      <div class="modal-body text-center">
        <div class="spinner-border text-success" role="status" style="width: 4rem; height: 4rem;">
          <span class="visually-hidden">Carregando...</span>
        </div>
        <div class="mt-3 text-white">Aguarde, importando produtos revisados...</div>
      </div>
    </div>
  </div>
</div>
<!-- Modal de feedback para importação de produtos revisados -->
<div class="modal fade" id="modalMensagemImportaRevisada" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Importação de Produtos Revisados</h5></div>
      <div class="modal-body"><span id="modalMensagemImportaRevisadaTexto"></span></div>
      <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button></div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializa os tooltips do Bootstrap
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Máscaras para campos
    if (typeof IMask !== 'undefined') {
        IMask(document.getElementById('company_cnpj'), {
            mask: '00.000.000/0000-00'
        });
        IMask(document.getElementById('company_zip'), {
            mask: '00000-000'
        });
    }

    // Manter aba ativa após reload
    const tabLinks = document.querySelectorAll('a[data-bs-toggle="tab"]');
    tabLinks.forEach(function(link) {
        link.addEventListener('shown.bs.tab', function(e) {
            localStorage.setItem('configAbaAtiva', e.target.getAttribute('href'));
        });
    });
    const abaAtiva = localStorage.getItem('configAbaAtiva');
    if (abaAtiva) {
        var tab = new bootstrap.Tab(document.querySelector('a[href="' + abaAtiva + '"]'));
        tab.show();
    }

    // Permissões dos operadores
    const salvarPermissoes = async (usuarioId) => {
        const params = new URLSearchParams();
        params.append('usuario_id', usuarioId);
        document.querySelectorAll(`.permissao-check[data-usuario="${usuarioId}"]`).forEach(check => {
            params.append(`permissoes[${check.dataset.modulo}]`, check.checked ? 1 : 0);
        });
        try {
            const response = await fetch('/configuracoes/salvar', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: params
            });
            if (response.ok) {
                // Mantém a aba ativa após reload
                window.location.reload();
            } else {
                alert('Erro ao salvar permissões');
            }
        } catch (error) {
            console.error('Erro:', error);
            alert('Erro ao salvar permissões');
        }
    };
    document.querySelectorAll('.salvar-permissoes').forEach(button => {
        button.addEventListener('click', () => {
            const usuarioId = button.dataset.usuario;
            salvarPermissoes(usuarioId);
        });
    });
});
</script>

<script>
// --- Atendimento: Seleção de Empresas ---
let empresasCache = [];
let empresasSelecionadas = [];

function renderizarListaEmpresasAtendidas(filtro = '') {
    const lista = document.getElementById('listaEmpresasAtendidas');
    let empresas = empresasCache;
    if (filtro) {
        const f = filtro.toLowerCase();
        empresas = empresas.filter(e =>
            (e.cnpj && e.cnpj.replace(/\D/g, '').includes(f.replace(/\D/g, '')))
            || (e.nome && e.nome.toLowerCase().includes(f))
        );
    }
    if (empresas.length === 0) {
        lista.innerHTML = '<div class="text-muted">Nenhuma empresa encontrada.</div>';
        return;
    }
    empresas.sort((a, b) => parseInt(a.id) - parseInt(b.id));
    lista.innerHTML = empresas.map(e => {
        let tipo = '';
        if (e.tipo_empresa && (e.tipo_empresa.toLowerCase() === 'matriz' || e.tipo_empresa.toLowerCase() === 'filial')) {
            tipo = `- ${e.tipo_empresa.charAt(0).toUpperCase() + e.tipo_empresa.slice(1).toLowerCase()}`;
        }
        return `<div class='form-check'>
            <input class='form-check-input empresa-checkbox' type='checkbox' id='empresa_${e.id}' value='${e.id}' ${empresasSelecionadas.includes(e.id.toString()) ? 'checked' : ''}>
            <label class='form-check-label' for='empresa_${e.id}'>
                <strong>${e.id}</strong> - ${e.nome} ${e.cnpj ? `- ${e.cnpj}` : ''} ${tipo}
            </label>
        </div>`;
    }).join('');
}

document.addEventListener('DOMContentLoaded', function() {
    // ... código existente ...
    const modalEmpresas = document.getElementById('modalEmpresasAtendidas');
    modalEmpresas.addEventListener('show.bs.modal', function () {
        fetch('/configuracoes/empresas-disponiveis')
            .then(response => response.json())
            .then(empresas => {
                empresasCache = empresas;
                // Buscar empresas já selecionadas
                fetch('/configuracoes/empresas-atendidas-usuario')
                    .then(r => r.json())
                    .then(selecionadas => {
                        empresasSelecionadas = selecionadas.map(e => e.id ? e.id.toString() : e.toString());
                        renderizarListaEmpresasAtendidas(document.getElementById('filtroEmpresasAtendidas').value);
                    });
            });
    });
    // Filtro em tempo real
    document.getElementById('filtroEmpresasAtendidas').addEventListener('input', function() {
        renderizarListaEmpresasAtendidas(this.value);
    });
    // Salvar empresas atendidas
    document.getElementById('salvarEmpresasAtendidas').addEventListener('click', function() {
        const selecionadas = Array.from(document.querySelectorAll('.empresa-checkbox:checked')).map(cb => cb.value);
        const formData = new FormData();
        selecionadas.forEach(id => formData.append('empresas_atendidas[]', id));
        fetch('/configuracoes/salvar-empresas-atendidas', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.sucesso) {
                atualizarEmpresasAtendidas();
                var modal = bootstrap.Modal.getInstance(modalEmpresas);
                modal.hide();
            } else {
                alert('Erro ao salvar empresas atendidas!');
            }
        });
    });
    // Atualiza a lista de empresas atendidas na tela
    function atualizarEmpresasAtendidas() {
        fetch('/configuracoes/empresas-atendidas-usuario')
            .then(response => response.json())
            .then(empresas => {
                const div = document.getElementById('empresasAtendidasSelecionadas');
                if (empresas.length === 0) {
                    div.innerHTML = '<span class="text-danger">Nenhuma empresa selecionada para atendimento.</span>';
                } else {
                    div.innerHTML = '<ul>' + empresas.map(e => {
                        let tipo = '';
                        if (e.tipo_empresa && (e.tipo_empresa.toLowerCase() === 'matriz' || e.tipo_empresa.toLowerCase() === 'filial')) {
                            tipo = `- ${e.tipo_empresa.charAt(0).toUpperCase() + e.tipo_empresa.slice(1).toLowerCase()}`;
                        }
                        return `<li><strong>${e.id}</strong> - ${e.nome} ${e.cnpj ? `- ${e.cnpj}` : ''} ${tipo}</li>`;
                    }).join('') + '</ul>';
                }
            });
    }
    atualizarEmpresasAtendidas();
});
</script>

<script>
// --- LOG RESOLUCAO ---
function carregarDropdownUsuariosEmpresasResolucao() {
    // Usuários
    fetch('/configuracoes/usuarios-disponiveis')
        .then(r => r.json())
        .then(usuarios => {
            const select = document.getElementById('filtroUsuarioResolucao');
            select.innerHTML = '<option value="">Todos</option>' + usuarios.map(u => `<option value="${u.nome}">${u.nome}</option>`).join('');
        });
    // Empresas
    fetch('/configuracoes/empresas-disponiveis')
        .then(r => r.json())
        .then(empresas => {
            const select = document.getElementById('filtroEmpresaResolucao');
            select.innerHTML = '<option value="">Todas</option>' + empresas.map(e => `<option value="${e.nome}">${e.nome}${e.tipo_empresa ? ' - ' + e.tipo_empresa.charAt(0).toUpperCase() + e.tipo_empresa.slice(1).toLowerCase() : ''}</option>`).join('');
        });
}
function carregarLogResolucao() {
    const usuario = document.getElementById('filtroUsuarioResolucao')?.value || '';
    const empresa = document.getElementById('filtroEmpresaResolucao')?.value || '';
    const data_inicial = document.getElementById('filtroDataInicialResolucao')?.value || '';
    const data_final = document.getElementById('filtroDataFinalResolucao')?.value || '';
    const params = new URLSearchParams();
    if (usuario) params.append('usuario', usuario);
    if (empresa) params.append('empresa', empresa);
    if (data_inicial) params.append('data_inicial', data_inicial);
    if (data_final) params.append('data_final', data_final);
    let url = '/configuracoes/logs/resolucao-nota';
    if (params.toString()) {
        url += '?' + params.toString();
    }
    fetch(url)
        .then(resp => resp.json())
        .then(function(data) {
            const tbody = document.querySelector('#tabela-log-resolucao tbody');
            tbody.innerHTML = '';
            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center">Nenhum log encontrado.</td></tr>';
                return;
            }
            data.forEach(function(log) {
                let tipo = '';
                if (log.tipo_empresa && (log.tipo_empresa.toLowerCase() === 'matriz' || log.tipo_empresa.toLowerCase() === 'filial')) {
                    tipo = ' - ' + log.tipo_empresa.charAt(0).toUpperCase() + log.tipo_empresa.slice(1).toLowerCase();
                }
                const tr = document.createElement('tr');
                tr.innerHTML = '<td>' + log.data_resolvida + '</td>' +
                    '<td>' + log.usuario_nome + '</td>' +
                    '<td>' + log.numero + '</td>' +
                    '<td>' + log.empresa_nome + tipo + '</td>' +
                    '<td></td>';
                tbody.appendChild(tr);
            });
        });
}
document.addEventListener('DOMContentLoaded', function() {
    carregarDropdownUsuariosEmpresasResolucao();
    document.getElementById('btnFiltrarResolucao').addEventListener('click', carregarLogResolucao);
});
</script>

<script>
// --- LOG ENVIO DE NOTAS ---
function carregarDropdownUsuariosEmpresas() {
    // Usuários
    fetch('/configuracoes/usuarios-disponiveis')
        .then(r => r.json())
        .then(usuarios => {
            const select = document.getElementById('filtroUsuarioEnvioNotas');
            select.innerHTML = '<option value="">Todos</option>' + usuarios.map(u => `<option value="${u.nome}">${u.nome}</option>`).join('');
        });
    // Empresas
    fetch('/configuracoes/empresas-disponiveis')
        .then(r => r.json())
        .then(empresas => {
            const select = document.getElementById('filtroEmpresaEnvioNotas');
            select.innerHTML = '<option value="">Todas</option>' + empresas.map(e => `<option value="${e.nome}">${e.nome}${e.tipo_empresa ? ' - ' + e.tipo_empresa.charAt(0).toUpperCase() + e.tipo_empresa.slice(1).toLowerCase() : ''}</option>`).join('');
        });
}
function carregarLogEnvioNotas() {
    const usuario = document.getElementById('filtroUsuarioEnvioNotas')?.value || '';
    const empresa = document.getElementById('filtroEmpresaEnvioNotas')?.value || '';
    const data_inicial = document.getElementById('filtroDataInicialEnvioNotas')?.value || '';
    const data_final = document.getElementById('filtroDataFinalEnvioNotas')?.value || '';
    const params = new URLSearchParams();
    if (usuario) params.append('usuario', usuario);
    if (empresa) params.append('empresa', empresa);
    if (data_inicial) params.append('data_inicial', data_inicial);
    if (data_final) params.append('data_final', data_final);
    let url = '/configuracoes/logs/envio-notas';
    if (params.toString()) {
        url += '?' + params.toString();
    }
    fetch(url)
        .then(resp => resp.json())
        .then(function(res) {
            const tbody = document.querySelector('#tabela-log-envio-notas tbody');
            tbody.innerHTML = '';
            if (res.erro) {
                tbody.innerHTML = `<tr><td colspan="12" class="text-center text-danger">${res.erro}</td></tr>`;
                document.getElementById('paginacao-envio-notas').innerHTML = '';
                return;
            }
            const data = res.dados || [];
            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="12" class="text-center">Nenhum registro encontrado.</td></tr>';
                document.getElementById('paginacao-envio-notas').innerHTML = '';
                return;
            }
            data.forEach(function(log) {
                tbody.innerHTML += `<tr>
                    <td>${log.id}</td>
                    <td>${log.numero || '-'}</td>
                    <td>${log.cnpj || '-'}</td>
                    <td>${log.razao_social || '-'}</td>
                    <td>${log.data_emissao || '-'}</td>
                    <td>${log.valor || '-'}</td>
                    <td>${log.chave || '-'}</td>
                    <td>${log.uf || '-'}</td>
                    <td>${log.status || '-'}</td>
                    <td>${log.tipo || '-'}</td>
                    <td>${log.data_consulta || '-'}</td>
                    <td>${log.empresa_nome || '-'}</td>
                </tr>`;
            });
            // Paginação
            const paginacao = document.getElementById('paginacao-envio-notas');
            const total = res.total || 0;
            const limite = res.limite || 20;
            const paginaAtual = res.pagina || 1;
            const totalPaginas = Math.ceil(total / limite);
            let html = '';
            if (totalPaginas > 1) {
                html += `<nav><ul class='pagination pagination-sm'>`;
                for (let i = 1; i <= totalPaginas; i++) {
                    html += `<li class='page-item${i === paginaAtual ? ' active' : ''}'><a class='page-link' href='#' onclick='carregarLogEnvioNotas(${i});return false;'>${i}</a></li>`;
                }
                html += `</ul></nav>`;
            }
            paginacao.innerHTML = html;
        });
}
function limparFiltrosEnvioNotas() {
    document.getElementById('filtroDataInicialEnvioNotas').value = '';
    document.getElementById('filtroDataFinalEnvioNotas').value = '';
    document.getElementById('filtroUsuarioEnvioNotas').value = '';
    document.getElementById('filtroEmpresaEnvioNotas').value = '';
    carregarLogEnvioNotas();
}
function exportarEnvioNotasCSV() {
    const empresa = document.getElementById('filtroEmpresaEnvioNotas')?.value || '';
    const data_inicial = document.getElementById('filtroDataInicialEnvioNotas')?.value || '';
    const data_final = document.getElementById('filtroDataFinalEnvioNotas')?.value || '';
    if (!empresa || !data_inicial || !data_final) {
        alert('Preencha empresa e datas para exportar.');
        return;
    }
    const params = new URLSearchParams();
    params.append('empresa', empresa);
    params.append('data_inicial', data_inicial);
    params.append('data_final', data_final);
    params.append('pagina', 1);
    params.append('limite', 10000); // Limite alto para exportar tudo
    let url = '/configuracoes/logs-envio-notas?' + params.toString();
    fetch(url)
        .then(resp => resp.json())
        .then(function(res) {
            const data = res.dados || [];
            if (!data.length) {
                alert('Nenhum registro encontrado para exportar.');
                return;
            }
            let csv = 'ID,Número,CNPJ,Razão Social,Data Emissão,Valor,Chave,UF,Status,Tipo,Data Consulta,Empresa\n';
            data.forEach(log => {
                csv += `"${log.id}","${log.numero || '-'}","${log.cnpj || '-'}","${log.razao_social || '-'}","${log.data_emissao || '-'}","${log.valor || '-'}","${log.chave || '-'}","${log.uf || '-'}","${log.status || '-'}","${log.tipo || '-'}","${log.data_consulta || '-'}","${log.empresa_nome || '-'}"\n`;
            });
            const blob = new Blob([csv], {type: 'text/csv'});
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'log_envio_notas.csv';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        });
}
document.addEventListener('DOMContentLoaded', function() {
    carregarDropdownEmpresasEnvioNotas();
    document.getElementById('btnFiltrarEnvioNotas').addEventListener('click', function(){ paginaEnvioNotas = 1; carregarLogEnvioNotas(1); });
    document.getElementById('btnLimparEnvioNotas').addEventListener('click', function(){ paginaEnvioNotas = 1; limparFiltrosEnvioNotas(); });
    document.getElementById('btnExportarEnvioNotas').addEventListener('click', exportarEnvioNotasCSV);
});
</script>

<script>
function limparFiltrosEnvioNotas() {
    document.getElementById('filtroDataInicialEnvioNotas').value = '';
    document.getElementById('filtroDataFinalEnvioNotas').value = '';
    document.getElementById('filtroUsuarioEnvioNotas').value = '';
    document.getElementById('filtroEmpresaEnvioNotas').value = '';
    carregarLogEnvioNotas();
}
function exportarEnvioNotasCSV() {
    const rows = Array.from(document.querySelectorAll('#tabela-log-envio-notas tbody tr'));
    if (!rows.length) return;
    let csv = 'Data,Usuário,Empresa,Qtd. Notas,Arquivo\n';
    rows.forEach(tr => {
        const cols = Array.from(tr.querySelectorAll('td')).map(td => '"' + td.innerText.replace(/"/g, '""') + '"');
        if (cols.length === 5) csv += cols.join(',') + '\n';
    });
    const blob = new Blob([csv], {type: 'text/csv'});
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'log_envio_notas.csv';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}
function limparFiltrosResolucao() {
    document.getElementById('filtroDataInicialResolucao').value = '';
    document.getElementById('filtroDataFinalResolucao').value = '';
    document.getElementById('filtroUsuarioResolucao').value = '';
    document.getElementById('filtroEmpresaResolucao').value = '';
    carregarLogResolucao();
}
function exportarResolucaoCSV() {
    const rows = Array.from(document.querySelectorAll('#tabela-log-resolucao tbody tr'));
    if (!rows.length) return;
    let csv = 'Data,Usuário,Número da Nota,Empresa\n';
    rows.forEach(tr => {
        const cols = Array.from(tr.querySelectorAll('td')).map(td => '"' + td.innerText.replace(/"/g, '""') + '"');
        if (cols.length >= 4) csv += cols.slice(0,4).join(',') + '\n';
    });
    const blob = new Blob([csv], {type: 'text/csv'});
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'log_resolucao_nota.csv';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}
document.addEventListener('DOMContentLoaded', function() {
    // ... já existentes ...
    document.getElementById('btnLimparEnvioNotas').addEventListener('click', limparFiltrosEnvioNotas);
    document.getElementById('btnExportarEnvioNotas').addEventListener('click', exportarEnvioNotasCSV);
    document.getElementById('btnLimparResolucao').addEventListener('click', limparFiltrosResolucao);
    document.getElementById('btnExportarResolucao').addEventListener('click', exportarResolucaoCSV);
});
</script>

<script>
function carregarDropdownUsuariosEmpresasArquivos() {
    fetch('/configuracoes/usuarios-disponiveis')
        .then(r => r.json())
        .then(usuarios => {
            const select = document.getElementById('filtroUsuarioArquivo');
            select.innerHTML = '<option value="">Todos</option>' + usuarios.map(u => `<option value="${u.nome}">${u.nome}</option>`).join('');
        });
    fetch('/configuracoes/empresas-disponiveis')
        .then(r => r.json())
        .then(empresas => {
            const select = document.getElementById('filtroEmpresaArquivo');
            select.innerHTML = '<option value="">Todas</option>' + empresas.map(e => `<option value="${e.nome}">${e.nome}${e.tipo_empresa ? ' - ' + e.tipo_empresa.charAt(0).toUpperCase() + e.tipo_empresa.slice(1).toLowerCase() : ''}</option>`).join('');
        });
}
function carregarArquivosEnviados() {
    const usuario = document.getElementById('filtroUsuarioArquivo')?.value || '';
    const empresa = document.getElementById('filtroEmpresaArquivo')?.value || '';
    const tipo = document.getElementById('filtroTipoArquivo')?.value || '';
    const status = document.getElementById('filtroStatusArquivo')?.value || '';
    const data_inicial = document.getElementById('filtroDataInicialArquivo')?.value || '';
    const data_final = document.getElementById('filtroDataFinalArquivo')?.value || '';
    const params = new URLSearchParams();
    if (usuario) params.append('usuario', usuario);
    if (empresa) params.append('empresa', empresa);
    if (tipo) params.append('tipo', tipo);
    if (status) params.append('status', status);
    if (data_inicial) params.append('data_inicial', data_inicial);
    if (data_final) params.append('data_final', data_final);
    let url = '/configuracoes/arquivos-enviados';
    if (params.toString()) {
        url += '?' + params.toString();
    }
    fetch(url)
        .then(resp => resp.json())
        .then(function(data) {
            const tbody = document.querySelector('#tabela-arquivos-enviados tbody');
            tbody.innerHTML = '';
            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" class="text-center">Nenhum arquivo encontrado.</td></tr>';
                return;
            }
            data.forEach(function(log) {
                let tipo = log.tipo_empresa && (log.tipo_empresa.toLowerCase() === 'matriz' || log.tipo_empresa.toLowerCase() === 'filial') ? ' - ' + log.tipo_empresa.charAt(0).toUpperCase() + log.tipo_empresa.slice(1).toLowerCase() : '';
                let btnDesfazer = '';
                if (log.tipo_arquivo && log.tipo_arquivo.toLowerCase() === 'fornecedores') {
                    btnDesfazer = '';
                } else if (log.status && log.status.toLowerCase() === 'ativo') {
                    btnDesfazer = `<button type='button' class='btn btn-danger btn-sm desfazer-arquivo' data-id='${log.id}'>Desfazer</button>`;
                } else {
                    btnDesfazer = `<span class='text-muted'>Excluído</span>`;
                }
                tbody.innerHTML += `<tr>
                    <td>${log.data_envio}</td>
                    <td>${log.usuario_nome || '-'}</td>
                    <td>${log.empresa_nome + tipo}</td>
                    <td>${log.tipo_arquivo || '-'}</td>
                    <td>${log.status.charAt(0).toUpperCase() + log.status.slice(1)}</td>
                    <td>${log.nome_original || '-'}</td>
                    <td>${btnDesfazer}</td>
                </tr>`;
            });
            // Adiciona evento aos botões de desfazer
            document.querySelectorAll('.desfazer-arquivo').forEach(btn => {
                console.log('Botão desfazer encontrado', btn);
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    console.log('Click no botão desfazer!');
                    document.getElementById('idArquivoDesfazer').value = btn.dataset.id;
                    document.getElementById('motivoDesfazerArquivo').value = '';
                    var modal = new bootstrap.Modal(document.getElementById('modalDesfazerArquivo'));
                    modal.show();
                });
            });
        });
}
function limparFiltrosArquivos() {
    document.getElementById('filtroDataInicialArquivo').value = '';
    document.getElementById('filtroDataFinalArquivo').value = '';
    document.getElementById('filtroUsuarioArquivo').value = '';
    document.getElementById('filtroEmpresaArquivo').value = '';
    document.getElementById('filtroTipoArquivo').value = '';
    document.getElementById('filtroStatusArquivo').value = '';
    carregarArquivosEnviados();
}
document.addEventListener('DOMContentLoaded', function() {
    carregarDropdownUsuariosEmpresasArquivos();
    carregarArquivosEnviados();
    document.getElementById('btnFiltrarArquivos').addEventListener('click', carregarArquivosEnviados);
    document.getElementById('btnLimparArquivos').addEventListener('click', limparFiltrosArquivos);
    document.getElementById('btnConfirmarDesfazerArquivo').addEventListener('click', function() {
        const id = document.getElementById('idArquivoDesfazer').value;
        const motivo = document.getElementById('motivoDesfazerArquivo').value;
        if (!motivo) {
            alert('Informe o motivo da exclusão!');
            return;
        }
        // Desabilita o botão para evitar duplo clique
        const btn = document.getElementById('btnConfirmarDesfazerArquivo');
        btn.disabled = true;
        fetch('/configuracoes/desfazer-arquivo', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: `id=${encodeURIComponent(id)}&motivo=${encodeURIComponent(motivo)}`,
            credentials: 'same-origin'
        })
        .then(r => r.json())
        .then(data => {
            btn.disabled = false;
            if (data.sucesso) {
                var modal = bootstrap.Modal.getInstance(document.getElementById('modalDesfazerArquivo'));
                modal.hide();
                // Aguarda o modal fechar para recarregar a tabela
                document.getElementById('modalDesfazerArquivo').addEventListener('hidden.bs.modal', function handler() {
                    carregarArquivosEnviados();
                    document.getElementById('modalDesfazerArquivo').removeEventListener('hidden.bs.modal', handler);
                });
            } else {
                alert(data.erro || 'Erro ao desfazer envio!');
            }
        });
    });
});
</script> 

<script>
let paginaRomaneio = 1;
function carregarDropdownEmpresasRomaneio() {
    fetch('/configuracoes/empresas-disponiveis')
        .then(r => r.json())
        .then(empresas => {
            const select = document.getElementById('filtroEmpresaRomaneio');
            select.innerHTML = '<option value="">Todas</option>' + empresas.map(e => `<option value="${e.nome}">${e.nome}${e.tipo_empresa ? ' - ' + e.tipo_empresa.charAt(0).toUpperCase() + e.tipo_empresa.slice(1).toLowerCase() : ''}</option>`).join('');
        });
}
function carregarLogRomaneio(pagina = 1) {
    const empresa = document.getElementById('filtroEmpresaRomaneio')?.value || '';
    const data_inicial = document.getElementById('filtroDataInicialRomaneio')?.value || '';
    const data_final = document.getElementById('filtroDataFinalRomaneio')?.value || '';
    if (!empresa || !data_inicial || !data_final) {
        const tbody = document.querySelector('#tabela-log-romaneio tbody');
        tbody.innerHTML = '<tr><td colspan="5" class="text-center text-danger">Preencha empresa e datas para consultar.</td></tr>';
        document.getElementById('paginacao-romaneio').innerHTML = '';
        return;
    }
    const params = new URLSearchParams();
    params.append('empresa', empresa);
    params.append('data_inicial', data_inicial);
    params.append('data_final', data_final);
    params.append('pagina', pagina);
    let url = '/configuracoes/logs-romaneio?' + params.toString();
    fetch(url)
        .then(resp => resp.json())
        .then(function(res) {
            const tbody = document.querySelector('#tabela-log-romaneio tbody');
            tbody.innerHTML = '';
            if (res.erro) {
                tbody.innerHTML = `<tr><td colspan="5" class="text-center text-danger">${res.erro}</td></tr>`;
                document.getElementById('paginacao-romaneio').innerHTML = '';
                return;
            }
            const data = res.dados || [];
            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center">Nenhum registro encontrado.</td></tr>';
                document.getElementById('paginacao-romaneio').innerHTML = '';
                return;
            }
            data.forEach(function(log) {
                tbody.innerHTML += `<tr>
                    <td>${log.created_at}</td>
                    <td>${log.empresa_nome || '-'}</td>
                    <td>${log.chave}</td>
                    <td>${log.tipo_empresa || '-'}</td>
                    <td></td>
                </tr>`;
            });
            // Paginação
            const paginacao = document.getElementById('paginacao-romaneio');
            const total = res.total || 0;
            const limite = res.limite || 20;
            const paginaAtual = res.pagina || 1;
            const totalPaginas = Math.ceil(total / limite);
            let html = '';
            if (totalPaginas > 1) {
                html += `<nav><ul class='pagination pagination-sm'>`;
                for (let i = 1; i <= totalPaginas; i++) {
                    html += `<li class='page-item${i === paginaAtual ? ' active' : ''}'><a class='page-link' href='#' onclick='carregarLogRomaneio(${i});return false;'>${i}</a></li>`;
                }
                html += `</ul></nav>`;
            }
            paginacao.innerHTML = html;
        });
}
function limparFiltrosRomaneio() {
    document.getElementById('filtroDataInicialRomaneio').value = '';
    document.getElementById('filtroDataFinalRomaneio').value = '';
    document.getElementById('filtroEmpresaRomaneio').value = '';
    carregarLogRomaneio();
}
function exportarRomaneioCSV() {
    const empresa = document.getElementById('filtroEmpresaRomaneio')?.value || '';
    const data_inicial = document.getElementById('filtroDataInicialRomaneio')?.value || '';
    const data_final = document.getElementById('filtroDataFinalRomaneio')?.value || '';
    if (!empresa || !data_inicial || !data_final) {
        alert('Preencha empresa e datas para exportar.');
        return;
    }
    const params = new URLSearchParams();
    params.append('empresa', empresa);
    params.append('data_inicial', data_inicial);
    params.append('data_final', data_final);
    params.append('pagina', 1);
    params.append('limite', 10000); // Limite alto para exportar tudo
    let url = '/configuracoes/logs-romaneio?' + params.toString();
    fetch(url)
        .then(resp => resp.json())
        .then(function(res) {
            const data = res.dados || [];
            if (!data.length) {
                alert('Nenhum registro encontrado para exportar.');
                return;
            }
            let csv = 'Data,Empresa,Chave,Tipo Empresa\n';
            data.forEach(log => {
                csv += `"${log.created_at}","${log.empresa_nome || '-'}","${log.chave}","${log.tipo_empresa || '-'}"\n`;
            });
            const blob = new Blob([csv], {type: 'text/csv'});
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'log_romaneio.csv';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        });
}
document.addEventListener('DOMContentLoaded', function() {
    carregarDropdownEmpresasRomaneio();
    document.getElementById('btnFiltrarRomaneio').addEventListener('click', function(){ paginaRomaneio = 1; carregarLogRomaneio(1); });
    document.getElementById('btnLimparRomaneio').addEventListener('click', function(){ paginaRomaneio = 1; limparFiltrosRomaneio(); });
    document.getElementById('btnExportarRomaneio').addEventListener('click', exportarRomaneioCSV);
});
</script> 

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Preencher dropdown de empresas na Limpeza em Lote
    fetch('/configuracoes/empresas-disponiveis')
        .then(r => r.json())
        .then(empresas => {
            const select = document.getElementById('empresaLimpezaLote');
            if (!select) return;
            select.innerHTML = '<option value="">Selecione a empresa</option>' + empresas.map(e => `<option value="${e.id}">${e.nome}${e.tipo_empresa ? ' - ' + e.tipo_empresa.charAt(0).toUpperCase() + e.tipo_empresa.slice(1).toLowerCase() : ''}</option>`).join('');
        });
    // Habilitar botão só se empresa selecionada
    const selectEmpresa = document.getElementById('empresaLimpezaLote');
    const btnZerar = document.getElementById('btnZerarBanco');
    if (selectEmpresa && btnZerar) {
        selectEmpresa.addEventListener('change', function() {
            btnZerar.disabled = !this.value;
        });
        btnZerar.addEventListener('click', function() {
            document.getElementById('empresaIdLimpezaLote').value = selectEmpresa.value;
            var modal = new bootstrap.Modal(document.getElementById('modalConfirmarLimpezaLote'));
            modal.show();
        });
    }
    // O envio real será implementado depois
});

    // Limpeza em lote - ação AJAX
    const btnConfirmarLimpeza = document.getElementById('btnConfirmarLimpezaLote');
    if (btnConfirmarLimpeza) {
        btnConfirmarLimpeza.addEventListener('click', function() {
            const empresaId = document.getElementById('empresaIdLimpezaLote').value;
            if (!empresaId) return;
            btnConfirmarLimpeza.disabled = true;
            fetch('/configuracoes/limpar-dados-empresa', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'empresa_id=' + encodeURIComponent(empresaId),
                credentials: 'same-origin'
            })
            .then(r => r.json())
            .then(data => {
                btnConfirmarLimpeza.disabled = false;
                var modal = bootstrap.Modal.getInstance(document.getElementById('modalConfirmarLimpezaLote'));
                modal.hide();
                if (data.sucesso) {
                    alert('Banco zerado com sucesso para a empresa selecionada!');
                    document.getElementById('empresaLimpezaLote').value = '';
                    document.getElementById('btnZerarBanco').disabled = true;
                } else {
                    alert(data.erro || 'Erro ao zerar banco.');
                }
            })
            .catch(() => {
                btnConfirmarLimpeza.disabled = false;
                alert('Erro ao zerar banco.');
            });
        });
    }
</script> 

<script>
document.addEventListener('DOMContentLoaded', function() {
    var form = document.getElementById('formImportaBase');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            var spinnerModal = new bootstrap.Modal(document.getElementById('spinnerImportaBase'));
            spinnerModal.show();
            var formData = new FormData(this);
            fetch(this.action, {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                spinnerModal.hide();
                document.getElementById('modalMensagemImportaBaseTexto').innerText = data.mensagem || 'Importação finalizada.';
                var modal = new bootstrap.Modal(document.getElementById('modalMensagemImportaBase'));
                modal.show();
            })
            .catch(() => {
                spinnerModal.hide();
                document.getElementById('modalMensagemImportaBaseTexto').innerText = 'Erro ao importar!';
                var modal = new bootstrap.Modal(document.getElementById('modalMensagemImportaBase'));
                modal.show();
            });
        });
    }
    // Importação revisada (NOVO)
    // var formRevisada = document.getElementById('formImportaRevisada');
    // if (formRevisada) {
    //     formRevisada.addEventListener('submit', function(e) {
    //         e.preventDefault();
    //         var spinnerModal = new bootstrap.Modal(document.getElementById('spinnerImportaRevisada'));
    //         spinnerModal.show();
    //         var formData = new FormData(this);
    //         fetch(this.action, {
    //             method: 'POST',
    //             body: formData
    //         })
    //         .then(r => r.json())
    //         .then(data => {
    //             spinnerModal.hide();
    //             document.getElementById('modalMensagemImportaRevisadaTexto').innerText = data.mensagem || 'Importação finalizada.';
    //             var modal = new bootstrap.Modal(document.getElementById('modalMensagemImportaRevisada'));
    //             modal.show();
    //         })
    //         .catch(() => {
    //             spinnerModal.hide();
    //             document.getElementById('modalMensagemImportaRevisadaTexto').innerText = 'Erro ao importar!';
    //             var modal = new bootstrap.Modal(document.getElementById('modalMensagemImportaRevisada'));
    //             modal.show();
    //         });
    //     });
    // }
});
</script> 

<script>
// Funções para Sugestões EAN
function carregarSugestoesEAN() {
    const tbody = document.querySelector('#tabela-sugestoes-ean tbody');
    tbody.innerHTML = '<tr><td colspan="3" class="text-center">Carregando...</td></tr>';
    
    fetch('/configuracoes/sugestoes-ean')
        .then(resp => resp.json())
        .then(function(res) {
            tbody.innerHTML = '';
            if (res.erro) {
                tbody.innerHTML = `<tr><td colspan="3" class="text-center text-danger">${res.erro}</td></tr>`;
                return;
            }
            const data = res.dados || [];
            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="3" class="text-center">Nenhuma sugestão EAN encontrada.</td></tr>';
                return;
            }
            data.forEach(function(sugestao) {
                const dataFormatada = new Date(sugestao.data_sugestao).toLocaleString('pt-BR');
                tbody.innerHTML += `<tr>
                    <td>${sugestao.ean}</td>
                    <td>${dataFormatada}</td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm" onclick="excluirSugestaoEAN('${sugestao.ean}')">
                            <i class="fas fa-trash"></i> Excluir
                        </button>
                    </td>
                </tr>`;
            });
        })
        .catch(() => {
            tbody.innerHTML = '<tr><td colspan="3" class="text-center text-danger">Erro ao carregar sugestões EAN.</td></tr>';
        });
}

function excluirSugestaoEAN(ean) {
    if (!confirm('Tem certeza que deseja excluir esta sugestão EAN?')) {
        return;
    }
    
    fetch('/configuracoes/excluir-sugestao-ean', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'ean=' + encodeURIComponent(ean),
        credentials: 'same-origin'
    })
    .then(r => r.json())
    .then(data => {
        if (data.sucesso) {
            alert('Sugestão EAN excluída com sucesso!');
            carregarSugestoesEAN();
        } else {
            alert(data.erro || 'Erro ao excluir sugestão EAN.');
        }
    })
    .catch(() => {
        alert('Erro ao excluir sugestão EAN.');
    });
}

function exportarSugestoesEANTXT() {
    fetch('/configuracoes/exportar-sugestoes-ean-txt')
        .then(resp => resp.blob())
        .then(blob => {
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'sugestoes_ean_' + new Date().toISOString().slice(0, 10) + '.txt';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        })
        .catch(() => {
            alert('Erro ao exportar arquivo TXT.');
        });
}



// Carregar sugestões EAN quando a aba for aberta
document.addEventListener('DOMContentLoaded', function() {
    const sugestoesEanTab = document.getElementById('sugestoes-ean-tab');
    if (sugestoesEanTab) {
        sugestoesEanTab.addEventListener('shown.bs.tab', function() {
            carregarSugestoesEAN();
        });
    }
});
</script>