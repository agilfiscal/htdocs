<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$mostrarModalAtendimento = false;
if (isset($_SESSION['usuario_id']) && isset($_SESSION['tipo']) && $_SESSION['tipo'] === 'admin') {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM empresas_atendidas WHERE usuario_id = ? AND data = CURDATE()');
    $stmt->execute([$_SESSION['usuario_id']]);
    $count = $stmt->fetchColumn();
    if ($count == 0) {
        $mostrarModalAtendimento = true;
    }
}
$saudacao = '';
$nomeUsuario = isset($_SESSION['nome']) ? $_SESSION['nome'] : (isset($_SESSION['usuario_nome']) ? $_SESSION['usuario_nome'] : 'Usuário');
$hora = (int)date('H');
if ($hora < 12) {
    $saudacao = 'Bom dia';
} elseif ($hora < 18) {
    $saudacao = 'Boa tarde';
} else {
    $saudacao = 'Boa noite';
}

// Função para detectar a página atual e destacar o menu ativo
function isCurrentPage($url) {
    $currentUrl = $_SERVER['REQUEST_URI'];
    $currentPath = parse_url($currentUrl, PHP_URL_PATH);
    
    // Remove barras iniciais e finais para comparação
    $currentPath = trim($currentPath, '/');
    $url = trim($url, '/');
    
    // Se a URL for exata, retorna true
    if ($currentPath === $url) {
        return true;
    }
    
    // Para páginas que começam com o mesmo padrão (ex: /financeiro/agenda-pagamentos)
    if ($url && strpos($currentPath, $url) === 0) {
        return true;
    }
    
    // Caso especial para dashboard (página inicial)
    if ($url === 'dashboard' && ($currentPath === '' || $currentPath === 'dashboard')) {
        return true;
    }
    
    // Para submenus, verificar se a página atual está dentro do grupo
    if ($url === 'extras' && strpos($currentPath, 'extras/') === 0) {
        return true;
    }
    
    // Para financeiro, verificar se está em qualquer página do financeiro
    if ($url === 'financeiro' && strpos($currentPath, 'financeiro/') === 0) {
        return true;
    }
    
    return false;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Ágil Fiscal' ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/assets/css/main.css">
    
    <link rel="icon" type="image/png" href="/assets/images/favicon-32x32.png">
    
    <!-- Estilos personalizados -->
    <style>
        :root {
            --primary-color: #0056b3;
            --secondary-color: #6c757d;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --info-color: #17a2b8;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
        }

        .navbar {
            background-color: var(--primary-color);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .navbar-brand {
            color: white !important;
            font-weight: 600;
        }

        .nav-link {
            color: rgba(255,255,255,0.8) !important;
        }

        .nav-link:hover {
            color: white !important;
        }

        .card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
            margin-bottom: 1rem;
        }

        .card-header {
            background-color: white;
            border-bottom: 1px solid rgba(0,0,0,0.125);
            padding: 1rem;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: #004494;
            border-color: #004494;
        }

        footer {
            background-color: var(--dark-color);
            color: white;
            padding: 1rem 0;
            margin-top: 2rem;
        }

        .sidebar {
            display: flex;
            flex-direction: column;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            width: 250px;
            background: #223046;
            color: #fff;
            z-index: 1000;
            transition: width 0.2s;
        }
        .sidebar.collapsed {
            width: 60px;
        }
        .sidebar .logo {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .sidebar .logo img {
            max-width: 180px;
            max-height: 100px;
            transition: max-width 0.2s, max-height 0.2s;
        }
        .sidebar.collapsed .logo img {
            max-width: 60px !important;
            max-height: 60px !important;
        }
        .sidebar .collapse-btn {
            position: absolute;
            top: 40px;
            right: 10px;
            background: none;
            border: none;
            color: #fff;
            font-size: 1.3rem;
            cursor: pointer;
            z-index: 1100;
        }
        .sidebar .user-info, .sidebar .user-version {
            transition: opacity 0.2s, max-height 0.2s;
        }
        .sidebar.collapsed .user-info, .sidebar.collapsed .user-version {
            opacity: 0;
            max-height: 0;
            overflow: hidden;
        }
        .sidebar .menu a.nav-link {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.35rem 0.5rem;
            font-size: 1rem;
            margin-bottom: 0.15rem;
            transition: padding 0.2s, font-size 0.2s;
        }
        .sidebar.collapsed .menu a.nav-link {
            justify-content: center;
            padding: 0.35rem 0.2rem;
            font-size: 1.3rem;
        }
        .sidebar.collapsed .menu a.nav-link span {
            display: none;
        }
        
        /* Estilo para menu ativo */
        .sidebar .menu a.nav-link.active {
            background-color: rgba(255, 255, 255, 0.15) !important;
            border-left: 4px solid #fff !important;
            color: #fff !important;
            font-weight: 600;
        }
        .sidebar .menu a.nav-link.active:hover {
            background-color: rgba(255, 255, 255, 0.2) !important;
        }
        
        /* Estilos para submenu */
        .submenu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
            background: rgba(255,255,255,0.05);
            margin-left: 1rem;
            border-radius: 4px;
        }
        .submenu.expanded {
            max-height: 500px;
        }
        .submenu a.nav-link {
            padding: 0.25rem 0.5rem;
            font-size: 0.9rem;
            margin-bottom: 0.1rem;
            color: rgba(255,255,255,0.7) !important;
        }
        .submenu a.nav-link:hover {
            color: rgba(255,255,255,0.9) !important;
        }
        .submenu a.nav-link.active {
            background-color: rgba(255, 255, 255, 0.1) !important;
            border-left: 3px solid #fff !important;
            color: #fff !important;
            font-weight: 600;
        }
        .sidebar.collapsed .submenu {
            display: none;
        }
        .menu-item-with-submenu {
            cursor: pointer;
        }
        .menu-item-with-submenu .nav-link {
            position: relative;
        }

        .sidebar .logout {
            margin: 0.5rem 1rem 1rem 1rem;
            text-align: center;
        }
        .sidebar.collapsed .logout {
            margin: 0.5rem 0.2rem 1rem 0.2rem;
        }
        .sidebar .logout-link {
            color: #fff;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            font-size: 1rem;
            padding: 0.5rem 0;
            border-radius: 4px;
            transition: background 0.2s;
        }
        .sidebar.collapsed .logout-link {
            font-size: 1.3rem;
        }
        .sidebar.collapsed .logout-link span {
            display: none;
        }
        .main-content {
            margin-left: 250px;
            padding: 2rem 2rem 0 2rem;
            transition: margin-left 0.2s;
        }
        .sidebar.collapsed ~ .main-content {
            margin-left: 60px;
        }
        @media (max-width: 991px) {
            .sidebar { width: 100%; position: relative; height: auto; }
            .main-content { margin-left: 0; padding: 1rem; }
        }
        .sidebar .menu-title {
            margin: 1rem 0 0.5rem 0;
            font-size: 0.95rem;
            color: #b0b8c1;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: opacity 0.2s, max-height 0.2s;
        }
        .sidebar.collapsed .menu-title {
            opacity: 0;
            max-height: 0;
            overflow: hidden;
        }
    </style>
</head>
<body>
<?php if (isset($_SESSION['usuario_id'])): ?>
    <aside class="sidebar" id="sidebar">
        <button class="collapse-btn" id="sidebarToggle" title="Recolher/Expandir Menu"><i class="fas fa-angle-double-left"></i></button>
        <div class="logo">
            <img src="/assets/images/logo.png" alt="Logo da Empresa">
        </div>
        <div class="user-info" style="text-align: center; position: relative;">
            Usuário:
            <?php
            // Buscar nome do usuário logado
            if (isset($_SESSION['usuario_id'])) {
                $db = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4', DB_USER, DB_PASS, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]);
                $stmt = $db->prepare('SELECT nome FROM usuarios WHERE id = ?');
                $stmt->execute([$_SESSION['usuario_id']]);
                $user = $stmt->fetch();
                echo ' ' . htmlspecialchars($user['nome'] ?? 'Usuário');
            }
            ?>
            <span id="notificacao-bell" style="cursor:pointer; margin-left:8px; position:relative;">
                <i class="fa fa-bell"></i>
                <span id="notificacao-badge" class="badge bg-danger" style="position:absolute; top:-8px; right:-8px; display:none;">0</span>
            </span>
            <span id="comentario-envelope" style="cursor:pointer; margin-left:8px; position:relative;">
                <i class="fa fa-envelope"></i>
                <span id="comentario-badge" class="badge bg-primary" style="position:absolute; top:-8px; right:-8px; display:none;">0</span>
            </span>
            <div id="notificacao-dropdown" style="display:none; position:absolute; left:50%; transform:translateX(-50%); top:35px; background:#fff; color:#222; border:1px solid #ccc; border-radius:6px; min-width:200px; max-width:90vw; width:250px; z-index:9999; box-shadow:0 2px 8px rgba(0,0,0,0.15); text-align:left;">
                <style>
                    @media (max-width: 768px) {
                        #notificacao-dropdown {
                            width: 95vw !important;
                            max-width: 95vw !important;
                            min-width: 95vw !important;
                            left: 2.5vw !important;
                            transform: none !important;
                        }
                        .tab-button {
                            font-size: 12px !important;
                            padding: 6px 4px !important;
                        }
                        .badge {
                            font-size: 0.6em !important;
                        }
                        #notificacao-resolucao-lista li {
                            flex-direction: column !important;
                            align-items: flex-start !important;
                        }
                        #notificacao-resolucao-lista li > div:first-child {
                            width: 100% !important;
                            margin-bottom: 8px !important;
                        }
                        #notificacao-resolucao-lista li > div:last-child {
                            width: 100% !important;
                            justify-content: flex-start !important;
                        }
                    }
                    
                    /* Forçar quebra de texto em todos os elementos */
                    #notificacao-dropdown * {
                        word-wrap: break-word !important;
                        word-break: break-word !important;
                        overflow-wrap: break-word !important;
                        white-space: normal !important;
                        hyphens: auto !important;
                    }
                    
                    #notificacao-resolucao-lista li {
                        overflow: visible !important;
                        word-wrap: break-word !important;
                        word-break: break-word !important;
                        white-space: normal !important;
                        max-width: 100% !important;
                    }
                    
                    #notificacao-resolucao-lista li > div {
                        overflow: visible !important;
                        word-wrap: break-word !important;
                        word-break: break-word !important;
                        max-width: 100% !important;
                    }
                    
                    /* Garantir que o dropdown não seja cortado */
                    #notificacao-dropdown {
                        overflow: visible !important;
                    }
                    
                    #notificacao-resolucao-lista {
                        overflow-y: auto !important;
                        overflow-x: visible !important;
                        padding: 0 8px !important;
                        max-height: 300px !important;
                        display: block !important;
                    }
                    
                    /* Garantir que o scroll funcione */
                    #notificacao-resolucao-lista::-webkit-scrollbar {
                        width: 6px;
                    }
                    
                    #notificacao-resolucao-lista::-webkit-scrollbar-track {
                        background: #f1f1f1;
                        border-radius: 3px;
                    }
                    
                    #notificacao-resolucao-lista::-webkit-scrollbar-thumb {
                        background: #c1c1c1;
                        border-radius: 3px;
                    }
                    
                    #notificacao-resolucao-lista::-webkit-scrollbar-thumb:hover {
                        background: #a8a8a8;
                    }
                    
                    /* Ajustar posicionamento em telas pequenas */
                    @media (max-width: 480px) {
                        #notificacao-dropdown {
                            left: 1vw !important;
                            width: 98vw !important;
                            min-width: 98vw !important;
                        }
                        
                        #notificacao-resolucao-lista {
                            max-height: 250px !important;
                            overflow-y: auto !important;
                        }
                    }
                </style>
                <div style="padding:8px 12px; border-bottom:1px solid #eee; font-weight:bold;">Notificações</div>
                
                <!-- Abas -->
                <div id="abas-container" style="display:flex; border-bottom:1px solid #eee;">
                    <button id="tab-vencimentos" class="tab-button active" style="flex:1; padding:8px; border:none; background:#f8f9fa; cursor:pointer; border-bottom:2px solid #007bff;">
                        Solicitações <span id="badge-vencimentos" class="badge bg-danger" style="font-size:0.7em; margin-left:4px;">0</span>
                    </button>
                    <button id="tab-resolucoes" class="tab-button" style="flex:1; padding:8px; border:none; background:#f8f9fa; cursor:pointer; border-bottom:2px solid transparent;">
                        Resoluções <span id="badge-resolucoes" class="badge bg-success" style="font-size:0.7em; margin-left:4px;">0</span>
                    </button>
                </div>
                
                <!-- Conteúdo das abas -->
                <div id="conteudo-vencimentos">
                <ul id="notificacao-lista" style="list-style:none; margin:0; padding:0; max-height:300px; overflow-y:auto;"></ul>
                </div>
                
                <div id="conteudo-resolucoes" style="display:none;">
                    <ul id="notificacao-resolucao-lista" style="list-style:none; margin:0; padding:8px; max-height:300px; overflow-y:auto; overflow-x:visible; display:block;"></ul>
                </div>
                
                <div style="padding:6px 12px; text-align:right;">
                    <a href="#" id="fechar-notificacao" style="font-size:0.9em; color:#888;">Fechar</a>
                </div>
            </div>
            
            <!-- Dropdown de Comentários -->
            <div id="comentario-dropdown" style="display:none; position:absolute; left:50%; transform:translateX(-50%); top:35px; background:#fff; color:#222; border:1px solid #ccc; border-radius:6px; min-width:200px; max-width:90vw; width:250px; z-index:9999; box-shadow:0 2px 8px rgba(0,0,0,0.15); text-align:left;">
                <style>
                    @media (max-width: 768px) {
                        #comentario-dropdown {
                            width: 95vw !important;
                            min-width: 95vw !important;
                            left: 2.5vw !important;
                            transform: none !important;
                        }
                    }
                    
                    #comentario-dropdown * {
                        word-wrap: break-word !important;
                        word-break: break-word !important;
                        overflow-wrap: break-word !important;
                        white-space: normal !important;
                        hyphens: auto !important;
                    }
                    
                    #comentario-dropdown li {
                        max-width: 100% !important;
                    }
                    
                    @media (max-width: 480px) {
                        #comentario-dropdown {
                            width: 95vw !important;
                            min-width: 95vw !important;
                            left: 2.5vw !important;
                            transform: none !important;
                        }
                    }
                </style>
                
                <div style="padding:8px 12px; border-bottom:1px solid #eee; font-weight:bold;">Comentários</div>
                
                <ul id="comentario-lista" style="list-style:none; margin:0; padding:0; max-height:300px; overflow-y:auto; overflow-x:visible; display:block;">
                    <li style="padding:10px; color:#888;">Carregando comentários...</li>
                </ul>
                
                <div style="padding:6px 12px; text-align:right;">
                    <a href="#" id="fechar-comentario" style="font-size:0.9em; color:#888;">Fechar</a>
                </div>
            </div>
        </div>
        <div class="user-version" style="text-align: center;">
            v1.0.16
        </div>
        <nav class="menu">
            <div class="menu-title">Menu Principal</div>
            <a href="/dashboard" class="nav-link <?= isCurrentPage('dashboard') ? 'active' : '' ?>"><i class="fas fa-tachometer-alt"></i> <span>Dashboard</span></a>
            <a href="/auditoria" class="nav-link <?= isCurrentPage('auditoria') ? 'active' : '' ?>"><i class="fas fa-search"></i> <span>Auditoria</span></a>
            <a href="/consulta-tributaria" class="nav-link <?= isCurrentPage('consulta-tributaria') ? 'active' : '' ?>"><i class="fas fa-balance-scale"></i> <span>Consulta Tributária</span></a>
            <?php
            // Buscar permissões do usuário operador
            $permissoes = [];
            if (isset($_SESSION['usuario_id']) && isset($_SESSION['tipo']) && $_SESSION['tipo'] === '3') {
                $db = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4', DB_USER, DB_PASS, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]);
                $stmt = $db->prepare('SELECT modulo FROM usuario_permissoes WHERE usuario_id = ? AND permitido = 1');
                $stmt->execute([$_SESSION['usuario_id']]);
                $permissoes = array_column($stmt->fetchAll(), 'modulo');
            }
            ?>
            <a href="/financeiro/agenda-pagamentos" class="nav-link <?= isCurrentPage('financeiro/agenda-pagamentos') ? 'active' : '' ?>"><i class="fas fa-calendar-alt"></i> <span>Agenda</span></a>  
            <a href="/empresas" class="nav-link <?= isCurrentPage('empresas') ? 'active' : '' ?>"><i class="fas fa-building"></i> <span>Empresas</span></a>
            <a href="/usuarios" class="nav-link <?= isCurrentPage('usuarios') ? 'active' : '' ?>"><i class="fas fa-users"></i> <span>Usuários</span></a>
            <a href="/arquivos/upload" class="nav-link <?= isCurrentPage('arquivos/upload') ? 'active' : '' ?>"><i class="fas fa-upload"></i> <span>Envio de arquivos</span></a>
            
            <!-- Menu Extras com submenus -->
            <div class="menu-item-with-submenu <?= isCurrentPage('extras') ? 'expanded' : '' ?>" id="menuExtras">
                <a class="nav-link <?= isCurrentPage('extras') ? 'active' : '' ?>"><i class="fas fa-plus-circle"></i> <span>Extras</span></a>
                <div class="submenu <?= isCurrentPage('extras') ? 'expanded' : '' ?>" id="submenuExtras">
                    <a href="/extras/consultar-cnpj" class="nav-link <?= isCurrentPage('extras/consultar-cnpj') ? 'active' : '' ?>"><i class="fas fa-search"></i> <span>Consultar CNPJ</span></a>
                    <a href="/extras/calculadora-preco" class="nav-link <?= isCurrentPage('extras/calculadora-preco') ? 'active' : '' ?>"><i class="fas fa-calculator"></i> <span>Calculadora de Preço</span></a>
                    <a href="/extras/construtor-placas" class="nav-link <?= isCurrentPage('extras/construtor-placas') ? 'active' : '' ?>"><i class="fas fa-tags"></i> <span>Construtor de Placas</span></a>
                            <a href="/extras/calculadora-custo" class="nav-link <?= isCurrentPage('extras/calculadora-custo') ? 'active' : '' ?>"><i class="fas fa-industry"></i> <span>Calculadora de Custo</span></a>
                    <a href="/extras/classificacao-desossa" class="nav-link <?= isCurrentPage('extras/classificacao-desossa') ? 'active' : '' ?>"><i class="fas fa-tag"></i> <span>Classificação de Desossa</span></a>
                    
                                        
                </div>
            </div>
            
            <?php if (isset($_SESSION['tipo']) && ($_SESSION['tipo'] === 'admin' || $_SESSION['tipo'] === 'master')): ?>
                <a class="nav-link <?= isCurrentPage('configuracoes') ? 'active' : '' ?>" href="/configuracoes">
                    <i class="fas fa-cog"></i> <span>Configurações</span>
                </a>
            <?php elseif (isset($_SESSION['tipo']) && $_SESSION['tipo'] === '3' && in_array('configuracoes', $permissoes)): ?>
                <a class="nav-link <?= isCurrentPage('configuracoes') ? 'active' : '' ?>" href="/configuracoes">
                    <i class="fas fa-cog"></i> <span>Configurações</span>
                </a>
            <?php endif; ?>
            
        </nav>
        <div class="logout mt-auto">
            <a href="/logout" class="logout-link"><i class="fas fa-sign-out-alt"></i> <span>Sair</span></a>
        </div>
    </aside>
    <main class="main-content">
        <?php if (isset($_SESSION['warning'])): ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <?= $_SESSION['warning'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['warning']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <?= $_SESSION['success'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-times-circle me-2"></i>
                <?= $_SESSION['error'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        <?= $content ?? '' ?>
    </main>
<?php else: ?>
    <main class="main-content" style="margin-left:0;">
        <?= $content ?? '' ?>
    </main>
<?php endif; ?>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/your-code.js" crossorigin="anonymous"></script>
    <!-- IMask para máscaras de campos -->
    <script src="https://unpkg.com/imask"></script>
    <!-- Scripts personalizados -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializa os tooltips do Bootstrap
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Inicializa os dropdowns do Bootstrap
        var dropdownTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="dropdown"]'));
        var dropdownList = dropdownTriggerList.map(function (dropdownTriggerEl) {
            return new bootstrap.Dropdown(dropdownTriggerEl, {
                autoClose: true
            });
        });

        // Inicializa as máscaras de campos
        if (typeof IMask !== 'undefined') {
            // Máscara para CNPJ
            var cnpjInputs = document.querySelectorAll('input[id*="cnpj"]');
            cnpjInputs.forEach(function(input) {
                IMask(input, {
                    mask: '00.000.000/0000-00'
                });
            });

            // Máscara para CEP
            var cepInputs = document.querySelectorAll('input[id*="zip"], input[id*="cep"]');
            cepInputs.forEach(function(input) {
                IMask(input, {
                    mask: '00000-000'
                });
            });

            // Máscara para telefone
            var phoneInputs = document.querySelectorAll('input[id*="phone"], input[id*="telefone"]');
            phoneInputs.forEach(function(input) {
                IMask(input, {
                    mask: '(00) 00000-0000'
                });
            });
        }
        // Botão de recolher/expandir menu lateral
        var sidebar = document.getElementById('sidebar');
        var toggleBtn = document.getElementById('sidebarToggle');
        toggleBtn.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
            var icon = toggleBtn.querySelector('i');
            if (sidebar.classList.contains('collapsed')) {
                icon.classList.remove('fa-angle-double-left');
                icon.classList.add('fa-angle-double-right');
            } else {
                icon.classList.remove('fa-angle-double-right');
                icon.classList.add('fa-angle-double-left');
            }
        });

        // Controle de submenus
        const menuExtras = document.getElementById('menuExtras');
        const submenuExtras = document.getElementById('submenuExtras');
        
        if (menuExtras && submenuExtras) {
            // Adiciona evento apenas no link do menu principal (não nos submenus)
            const menuLink = menuExtras.querySelector('.nav-link');
            
            if (menuLink) {
                menuLink.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    // Toggle da classe expanded
                    menuExtras.classList.toggle('expanded');
                    submenuExtras.classList.toggle('expanded');
                    
                    // Salvar estado no localStorage
                    const isExpanded = submenuExtras.classList.contains('expanded');
                    localStorage.setItem('menuExtrasExpanded', isExpanded);
                });
            }
            
            // Previne que cliques nos links dos submenus fechem o menu
            const submenuLinks = submenuExtras.querySelectorAll('.nav-link');
            submenuLinks.forEach(function(link) {
                link.addEventListener('click', function(e) {
                    // Permite que o link funcione normalmente
                    e.stopPropagation();
                    // Não chama preventDefault() para permitir a navegação
                });
            });
            
            // Restaurar estado do submenu ao carregar a página
            const wasExpanded = localStorage.getItem('menuExtrasExpanded') === 'true';
            if (wasExpanded) {
                menuExtras.classList.add('expanded');
                submenuExtras.classList.add('expanded');
            }
        }

        // Notificações
        const bell = document.getElementById('notificacao-bell');
        const badge = document.getElementById('notificacao-badge');
        const dropdown = document.getElementById('notificacao-dropdown');
        const lista = document.getElementById('notificacao-lista');
        const listaResolucao = document.getElementById('notificacao-resolucao-lista');
        const fechar = document.getElementById('fechar-notificacao');
        const tabVencimentos = document.getElementById('tab-vencimentos');
        const tabResolucoes = document.getElementById('tab-resolucoes');
        const conteudoVencimentos = document.getElementById('conteudo-vencimentos');
        const conteudoResolucoes = document.getElementById('conteudo-resolucoes');
        const badgeVencimentos = document.getElementById('badge-vencimentos');
        const badgeResolucoes = document.getElementById('badge-resolucoes');
        
        // Elementos para comentários
        const envelope = document.getElementById('comentario-envelope');
        const comentarioBadge = document.getElementById('comentario-badge');
        const comentarioDropdown = document.getElementById('comentario-dropdown');
        const comentarioLista = document.getElementById('comentario-lista');
        const fecharComentario = document.getElementById('fechar-comentario');
        
        let notificacoes = [];
        let notificacoesResolucao = [];
        let comentarios = [];
        
        // Inicializar badges como zero
        badge.textContent = '0';
        badge.style.display = 'none';
        badgeVencimentos.textContent = '0';
        badgeVencimentos.style.display = 'none';
        badgeResolucoes.textContent = '0';
        badgeResolucoes.style.display = 'none';
        comentarioBadge.textContent = '0';
        comentarioBadge.style.display = 'none';
        
        // Verificar tipo de usuário
        const tipoUsuario = '<?= $_SESSION['tipo'] ?? 'operator' ?>';
        const isOperador = tipoUsuario === '3' || tipoUsuario === 'operator';
        

        


        function carregarNotificacoes() {
            // Se for operador, não carrega notificações de vencimento
            if (isOperador) {
                notificacoes = [];
                badgeVencimentos.textContent = '0';
                badgeVencimentos.style.display = 'none';
                // Atualizar badge geral mesmo para operadores
                atualizarBadgeNotificacoes();
                return;
            }
            
            fetch('/notificacoes')
                .then(resp => resp.json())
                .then(data => {
                    notificacoes = data.notificacoes || [];
                    badgeVencimentos.textContent = notificacoes.length;
                    badgeVencimentos.style.display = notificacoes.length > 0 ? 'inline-block' : 'none';
                    
                    // Atualizar badge geral
                    atualizarBadgeNotificacoes();
                    lista.innerHTML = '';
                    if (notificacoes.length === 0) {
                        lista.innerHTML = '<li style="padding:10px; color:#888;">Sem novas notificações.</li>';
                    } else {
                        notificacoes.forEach(function(n) {
                            const li = document.createElement('li');
                            li.style.listStyle = 'none';
                            li.style.margin = '10px 0';
                            li.style.background = '#fff';
                            li.style.borderRadius = '8px';
                            li.style.boxShadow = '0 2px 8px rgba(0,0,0,0.07)';
                            li.style.display = 'flex';
                            li.style.alignItems = 'center';
                            li.style.justifyContent = 'space-between';
                            li.style.padding = '12px 16px';
                            li.style.gap = '12px';
                            li.style.transition = 'box-shadow 0.2s';
                            li.onmouseover = function() { li.style.boxShadow = '0 4px 16px rgba(0,0,0,0.13)'; };
                            li.onmouseout = function() { li.style.boxShadow = '0 2px 8px rgba(0,0,0,0.07)'; };

                            // Esquerda: Empresa e nota
                            const infoDiv = document.createElement('div');
                            infoDiv.style.display = 'flex';
                            infoDiv.style.flexDirection = 'column';
                            
                            // Formatar data e hora
                            let dataHora = '';
                            if (n.data_criacao) {
                                const data = new Date(n.data_criacao);
                                dataHora = data.toLocaleString('pt-BR', {
                                    day: '2-digit',
                                    month: '2-digit',
                                    year: 'numeric',
                                    hour: '2-digit',
                                    minute: '2-digit'
                                });
                            }
                            
                            infoDiv.innerHTML = '<span style="font-weight:600;font-size:15px;">' + n.empresa_nome + '</span>' +
                                '<span style="color:#0056b3;font-size:14px;cursor:pointer;" class="nota-numero-copiar" data-numero="' + n.numero + '" title="Clique para copiar">Nota: ' + n.numero + '</span>' +
                                (dataHora ? '<span style="color:#666;font-size:12px;margin-top:2px;">' + dataHora + '</span>' : '');

                            // Direita: Ações
                            const actionsDiv = document.createElement('div');
                            actionsDiv.style.display = 'flex';
                            actionsDiv.style.alignItems = 'center';
                            actionsDiv.style.gap = '8px';

                            // Botão resolver
                            const btnResolver = document.createElement('button');
                            btnResolver.className = 'btn-resolver-nota';
                            btnResolver.title = 'Resolver notificação';
                            btnResolver.style.background = '#28a745';
                            btnResolver.style.border = 'none';
                            btnResolver.style.color = '#fff';
                            btnResolver.style.borderRadius = '4px';
                            btnResolver.style.padding = '4px 8px';
                            btnResolver.style.fontSize = '13px';
                            btnResolver.style.cursor = 'pointer';
                            btnResolver.style.display = 'flex';
                            btnResolver.style.alignItems = 'center';
                            btnResolver.innerHTML = '<i class="fa fa-check"></i>';
                            btnResolver.onmouseover = function() { btnResolver.style.background = '#218838'; };
                            btnResolver.onmouseout = function() { btnResolver.style.background = '#28a745'; };
                            btnResolver.onclick = function(e) {
                                e.stopPropagation();
                                btnResolver.disabled = true;
                                fetch('/notificacoes/resolver/' + n.id, {method: 'POST'})
                                    .then(resp => resp.json())
                                    .then(function(resp) {
                                        if (resp.sucesso) {
                                            li.remove();
                                            atualizarBadgeNotificacoes();
                                        } else {
                                            btnResolver.disabled = false;
                                            alert(resp.error || 'Erro ao resolver notificação');
                                        }
                                    });
                            };
                            actionsDiv.appendChild(btnResolver);

                            // Adicionar evento de clique para copiar número da nota
                            li.addEventListener('click', function(e) {
                                const notaElement = li.querySelector('.nota-numero-copiar');
                                if (notaElement && e.target === notaElement) {
                                    e.stopPropagation();
                                    const numero = notaElement.getAttribute('data-numero');
                                    navigator.clipboard.writeText(numero).then(function() {
                                        const originalText = notaElement.innerHTML;
                                        notaElement.innerHTML = '<span style="color:green;font-size:13px;">Copiado!</span>';
                                        setTimeout(function() {
                                            notaElement.innerHTML = originalText;
                                        }, 1200);
                                    }).catch(function(err) {
                                        console.error('Erro ao copiar:', err);
                                        alert('Erro ao copiar número da nota');
                                    });
                                }
                            });

                            li.appendChild(infoDiv);
                            li.appendChild(actionsDiv);
                            lista.appendChild(li);
                        });
                    }
                })
                .catch(error => {
                    console.error('Erro ao carregar notificações:', error);
                    badgeVencimentos.textContent = '0';
                    badgeVencimentos.style.display = 'none';
                    atualizarBadgeNotificacoes();
                });
        }

        function carregarNotificacoesResolucao() {
            // Buscar as últimas 50 resoluções (histórico completo)
            fetch('/notificacoes/historico')
                .then(resp => resp.json())
                .then(data => {
                    notificacoesResolucao = data.historico || [];
                    const naoVisualizadas = notificacoesResolucao.filter(n => !n.visualizada);
                    

                    

                    
                    // Badge de resoluções: para operadores mostra apenas não visualizadas, para admins NÃO mostra (apenas histórico)
                    const totalResolucoesBadge = isOperador ? naoVisualizadas.length : 0;
                    badgeResolucoes.textContent = totalResolucoesBadge;
                    badgeResolucoes.style.display = totalResolucoesBadge > 0 ? 'inline-block' : 'none';
                    
                    // Atualizar badge geral
                    atualizarBadgeNotificacoes();
                    

                    listaResolucao.innerHTML = '';
                    
                    if (notificacoesResolucao.length === 0) {
                        listaResolucao.innerHTML = '<li style="padding:10px; color:#888;">Nenhuma resolução encontrada.</li>';
                    } else {
                        notificacoesResolucao.forEach(function(n) {
                            const li = document.createElement('li');
                            li.style.listStyle = 'none';
                            li.style.margin = '8px 0';
                            li.style.background = n.visualizada ? '#f8f9fa' : '#e8f5e8';
                            li.style.borderRadius = '6px';
                            li.style.padding = '12px';
                            li.style.borderLeft = '3px solid ' + (n.visualizada ? '#6c757d' : '#28a745');
                            li.style.transition = 'box-shadow 0.2s';
                            li.style.overflow = 'visible';
                            li.style.wordWrap = 'break-word';
                            li.style.whiteSpace = 'normal';
                            li.onmouseover = function() { li.style.boxShadow = '0 2px 8px rgba(0,0,0,0.1)'; };
                            li.onmouseout = function() { li.style.boxShadow = 'none'; };

                            // Container principal - layout vertical para melhor quebra
                            const containerDiv = document.createElement('div');
                            containerDiv.style.display = 'block';
                            containerDiv.style.width = '100%';
                            containerDiv.style.overflow = 'visible';
                            containerDiv.style.wordWrap = 'break-word';
                            containerDiv.style.wordBreak = 'break-word';

                            // Informações da resolução
                            const infoDiv = document.createElement('div');
                            infoDiv.style.width = '100%';
                            infoDiv.style.overflow = 'visible';
                            infoDiv.style.wordWrap = 'break-word';
                            infoDiv.style.wordBreak = 'break-word';
                            infoDiv.style.whiteSpace = 'normal';
                            
                            const dataFormatada = new Date(n.data_criacao).toLocaleString('pt-BR');
                            
                            // Para administradores, mostrar também o nome do operador
                            let operadorInfo = '';
                            if (!isOperador && n.operador_nome) {
                                operadorInfo = '<div style="margin-bottom:4px; word-break:break-word;"><span style="color:#6c757d;font-size:12px; font-style:italic;">Operador: ' + n.operador_nome + '</span></div>';
                            }
                            
                            infoDiv.innerHTML = '<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px; flex-wrap:wrap; width:100%;">' +
                                '<span style="font-weight:600;font-size:14px; color:#28a745; word-break:break-word;">✓ Escriturado</span>' +
                                '<span style="color:#888; font-size:11px; word-break:break-word;">' + dataFormatada + '</span>' +
                                '</div>' +
                                '<div style="margin-bottom:4px; word-break:break-word;"><span style="color:#0056b3;font-size:13px; font-weight:500;">Nota: ' + n.nota_numero + '</span></div>' +
                                '<div style="margin-bottom:4px; word-break:break-word;"><span style="color:#666;font-size:12px;">' + n.empresa_nome + '</span></div>' +
                                operadorInfo +
                                '<div style="word-break:break-word; line-height:1.3;"><span style="color:#888;font-size:11px;">' + n.mensagem + '</span></div>';

                            // Container para ações
                            const actionsDiv = document.createElement('div');
                            actionsDiv.style.display = 'flex';
                            actionsDiv.style.justifyContent = 'flex-end';
                            actionsDiv.style.marginTop = '8px';
                            actionsDiv.style.width = '100%';



                            containerDiv.appendChild(infoDiv);
                            containerDiv.appendChild(actionsDiv);
                            li.appendChild(containerDiv);
                            listaResolucao.appendChild(li);
                        });
                    }
                })
                .catch(error => {
                    console.error('Erro ao carregar notificações de resolução:', error);
                    badgeResolucoes.textContent = '0';
                    badgeResolucoes.style.display = 'none';
                    atualizarBadgeNotificacoes();
                });
        }

        function carregarComentarios() {
            // Buscar comentários
            fetch('/comentarios/notificacoes')
                .then(resp => resp.json())
                .then(data => {
                    comentarios = data.comentarios || [];
                    const naoVisualizados = comentarios.filter(c => !c.visualizada);
                    
                    // Atualizar badge de comentários (só não visualizados)
                    comentarioBadge.textContent = naoVisualizados.length;
                    comentarioBadge.style.display = naoVisualizados.length > 0 ? 'inline-block' : 'none';
                    
                    // Limpar lista
                    comentarioLista.innerHTML = '';
                    
                    if (comentarios.length === 0) {
                        comentarioLista.innerHTML = '<li style="padding:10px; color:#888;">Nenhum comentário encontrado.</li>';
                    } else {
                        // Mostrar histórico dos últimos 10 comentários (incluindo visualizados)
                        comentarios.forEach(function(c) {
                            const li = document.createElement('li');
                            li.style.padding = '10px';
                            li.style.borderBottom = '1px solid #eee';
                            li.style.cursor = 'pointer';
                            li.style.background = c.visualizada ? '#f8f9fa' : '#e8f5e8';
                            li.style.borderLeft = '3px solid ' + (c.visualizada ? '#6c757d' : '#007bff');
                            
                            // Container principal
                            const containerDiv = document.createElement('div');
                            containerDiv.style.display = 'block';
                            containerDiv.style.width = '100%';
                            
                            // Formatar data
                            const dataFormatada = new Date(c.data_criacao).toLocaleString('pt-BR', {
                                day: '2-digit',
                                month: '2-digit',
                                year: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit',
                                second: '2-digit'
                            });
                            
                            // Informações do comentário no novo layout
                            const infoDiv = document.createElement('div');
                            infoDiv.style.marginBottom = '4px';
                            
                            // Criar container para o número da nota com botão de copiar
                            const notaContainer = document.createElement('div');
                            notaContainer.style.marginBottom = '4px';
                            notaContainer.style.wordBreak = 'break-word';
                            notaContainer.style.lineHeight = '1.4';
                            
                            const textoNota = document.createElement('span');
                            textoNota.style.color = '#333';
                            textoNota.style.fontSize = '13px';
                            textoNota.innerHTML = 'O usuário <strong>' + c.usuario_origem_nome + '</strong> adicionou um novo comentário na NF ';
                            
                            const numeroNota = document.createElement('strong');
                            numeroNota.textContent = c.nota_numero;
                            numeroNota.style.cursor = 'pointer';
                            numeroNota.style.color = '#007bff';
                            numeroNota.style.textDecoration = 'underline';
                            numeroNota.title = 'Clique para copiar o número da nota';
                            
                            const textoEmpresa = document.createElement('span');
                            textoEmpresa.style.color = '#333';
                            textoEmpresa.style.fontSize = '13px';
                            textoEmpresa.innerHTML = ' da empresa <strong>' + c.empresa_nome + '</strong>';
                            
                            // Adicionar evento de clique para copiar
                            numeroNota.onclick = function(e) {
                                e.stopPropagation();
                                navigator.clipboard.writeText(c.nota_numero).then(function() {
                                    // Feedback visual
                                    const originalText = numeroNota.textContent;
                                    numeroNota.textContent = 'Copiado!';
                                    numeroNota.style.color = '#28a745';
                                    setTimeout(function() {
                                        numeroNota.textContent = originalText;
                                        numeroNota.style.color = '#007bff';
                                    }, 1500);
                                }).catch(function(err) {
                                    console.error('Erro ao copiar:', err);
                                    // Fallback para navegadores mais antigos
                                    const textArea = document.createElement('textarea');
                                    textArea.value = c.nota_numero;
                                    document.body.appendChild(textArea);
                                    textArea.select();
                                    document.execCommand('copy');
                                    document.body.removeChild(textArea);
                                    
                                    // Feedback visual
                                    const originalText = numeroNota.textContent;
                                    numeroNota.textContent = 'Copiado!';
                                    numeroNota.style.color = '#28a745';
                                    setTimeout(function() {
                                        numeroNota.textContent = originalText;
                                        numeroNota.style.color = '#007bff';
                                    }, 1500);
                                });
                            };
                            
                            notaContainer.appendChild(textoNota);
                            notaContainer.appendChild(numeroNota);
                            notaContainer.appendChild(textoEmpresa);
                            
                            infoDiv.innerHTML = 
                                '<div style="margin-bottom:4px; word-break:break-word; line-height:1.3;"><span style="color:#666;font-size:12px; font-style:italic;">' + c.observacao + '</span></div>' +
                                '<div style="margin-top:4px;"><span style="color:#999;font-size:11px;">' + dataFormatada + '</span></div>';
                            
                            // Inserir o container da nota no início
                            infoDiv.insertBefore(notaContainer, infoDiv.firstChild);
                            
                            containerDiv.appendChild(infoDiv);
                            li.appendChild(containerDiv);
                            comentarioLista.appendChild(li);
                        });
                    }
                })
                .catch(error => {
                    console.error('Erro ao carregar comentários:', error);
                    comentarioBadge.textContent = '0';
                    comentarioBadge.style.display = 'none';
                });
        }

        function atualizarBadgeNotificacoes() {
            const totalVencimentos = isOperador ? 0 : notificacoes.length;
            // Para operadores, conta apenas resoluções não visualizadas no badge geral
            // Para administradores, NÃO conta resoluções no badge (apenas histórico)
            const totalResolucoes = isOperador ? 
                notificacoesResolucao.filter(n => !n.visualizada).length : 
                0; // Admin não recebe notificações de resolução
            const total = totalVencimentos + totalResolucoes;
            

            
            badge.textContent = total;
            badge.style.display = total > 0 ? 'inline-block' : 'none';
            
            if (!isOperador) {
                badgeVencimentos.textContent = totalVencimentos;
                badgeVencimentos.style.display = totalVencimentos > 0 ? 'inline-block' : 'none';
            }
            
            // Badge de resoluções: para operadores mostra apenas não visualizadas, para admins NÃO mostra (apenas histórico)
            const totalResolucoesBadge = isOperador ? 
                notificacoesResolucao.filter(n => !n.visualizada).length : 
                0; // Admin não recebe notificações de resolução
            badgeResolucoes.textContent = totalResolucoesBadge;
            badgeResolucoes.style.display = totalResolucoesBadge > 0 ? 'inline-block' : 'none';
        }

        function trocarAba(abaAtiva) {
            // Remove classe active de todas as abas
            tabVencimentos.classList.remove('active');
            tabResolucoes.classList.remove('active');
            
            // Esconde todos os conteúdos
            conteudoVencimentos.style.display = 'none';
            conteudoResolucoes.style.display = 'none';
            
            // Remove bordas de todas as abas
            tabVencimentos.style.borderBottom = '2px solid transparent';
            tabResolucoes.style.borderBottom = '2px solid transparent';
            
            // Ativa a aba selecionada
            if (abaAtiva === 'vencimentos' && !isOperador) {
                tabVencimentos.classList.add('active');
                tabVencimentos.style.borderBottom = '2px solid #007bff';
                conteudoVencimentos.style.display = 'block';
            } else {
                // Para operadores, sempre mostra resoluções
                tabResolucoes.classList.add('active');
                tabResolucoes.style.borderBottom = '2px solid #007bff';
                conteudoResolucoes.style.display = 'block';
            }
        }

        // Configurar interface baseada no tipo de usuário
        function configurarInterfaceUsuario() {
            if (isOperador) {
                // Para operadores: esconder aba de vencimentos e mostrar apenas resoluções
                tabVencimentos.style.display = 'none';
                tabResolucoes.style.flex = '1';
                trocarAba('resolucoes');
            } else {
                // Para admins: mostrar ambas as abas
                tabVencimentos.style.display = 'block';
                tabResolucoes.style.flex = '1';
                trocarAba('vencimentos');
            }
        }

        // Event listeners para as abas
        tabVencimentos.onclick = function() {
            trocarAba('vencimentos');
        };
        
        tabResolucoes.onclick = function() {
            trocarAba('resolucoes');
            // Para administradores, a aba resoluções é apenas histórico - não há contador para zerar
        };

        // Event listeners para comentários
        envelope.onclick = function(e) {
            e.stopPropagation();
            comentarioDropdown.style.display = comentarioDropdown.style.display === 'block' ? 'none' : 'block';
            if (comentarioDropdown.style.display === 'block') {
                // Fechar painel de notificações se estiver aberto
                dropdown.style.display = 'none';
                
                carregarComentarios();
                
                // Marcar todos os comentários como visualizados quando abrir o dropdown
                fetch('/comentarios/marcar-visualizados', {method: 'POST'})
                    .then(resp => resp.json())
                    .then(function(data) {
                        if (data.sucesso) {
                            // Marcar todos os comentários como visualizados localmente
                            comentarios.forEach(function(comentario) {
                                comentario.visualizada = true;
                            });
                            
                            // Zerar badge (mas manter histórico visível)
                            comentarioBadge.textContent = '0';
                            comentarioBadge.style.display = 'none';
                            
                            // Atualizar visual dos itens para mostrar como visualizados
                            const items = comentarioLista.querySelectorAll('li');
                            items.forEach(function(item) {
                                item.style.background = '#f8f9fa';
                                item.style.borderLeft = '3px solid #6c757d';
                            });
                        }
                    })
                    .catch(function(error) {
                        console.error('Erro ao marcar comentários como visualizados:', error);
                    });
            }
        };
        
        fecharComentario.onclick = function(e) {
            e.preventDefault();
            comentarioDropdown.style.display = 'none';
        };

        // Configurar interface baseada no tipo de usuário
        configurarInterfaceUsuario();
        
                        // Carregar notificações iniciais com pequeno delay para garantir que a página esteja carregada
        setTimeout(function() {
        carregarNotificacoes();
            carregarNotificacoesResolucao();
            carregarComentarios();
        }, 500);
        
        // Atualizar notificações a cada 30 segundos
        setInterval(function() {
            carregarNotificacoes();
            carregarNotificacoesResolucao();
            carregarComentarios();
        }, 30000);
        bell.onclick = function(e) {
            e.stopPropagation();
            dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
            if (dropdown.style.display === 'block') {
                // Fechar painel de comentários se estiver aberto
                comentarioDropdown.style.display = 'none';
                
                carregarNotificacoes();
                carregarNotificacoesResolucao();
                
                // Para operadores, zerar o contador quando abrir o dropdown
                if (isOperador) {

                    
                    // Marcar todas as notificações como visualizadas no banco de dados
                    fetch('/notificacoes/marcar-visualizadas', {method: 'POST'})
                        .then(resp => resp.json())
                        .then(function(data) {
                            console.log('Resposta do servidor:', data);
                            if (data.sucesso) {
                                // Marcar todas as notificações não visualizadas como visualizadas localmente
                                notificacoesResolucao.forEach(function(notif) {
                                    if (!notif.visualizada) {
                                        notif.visualizada = true;
                                    }
                                });
                                

                                
                                // Atualizar badges imediatamente
                                atualizarBadgeNotificacoes();
                            }
                        })
                        .catch(function(error) {
                            console.error('Erro ao marcar notificações como visualizadas:', error);
                        });
                }
            }
        };
        fechar.onclick = function(e) {
            e.preventDefault();
            dropdown.style.display = 'none';
        };
        document.addEventListener('click', function(e) {
            if (!dropdown.contains(e.target) && e.target !== bell) {
                dropdown.style.display = 'none';
            }
            if (!comentarioDropdown.contains(e.target) && e.target !== envelope) {
                comentarioDropdown.style.display = 'none';
            }
        });
    });
    </script>
    <!-- Modal Seleção de Empresas Atendidas (global) -->
    <div class="modal fade" id="modalEmpresasAtendidas" tabindex="-1" aria-labelledby="modalEmpresasAtendidasLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalEmpresasAtendidasLabel">
              <?= $saudacao ?>, <?= htmlspecialchars($nomeUsuario) ?>!!!<br>
              <small>Para começarmos, escolha ao menos uma empresa que você irá atender.</small>
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar" id="fecharModalEmpresasAtendidas" style="display:none;"></button>
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
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btnCancelarEmpresasAtendidas">Cancelar</button>
            <button type="button" class="btn btn-primary" id="salvarEmpresasAtendidas">Salvar Seleção</button>
          </div>
        </div>
      </div>
    </div>
    <script>
    // --- Atendimento: Seleção de Empresas (global) ---
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
        // Modal bloqueante de atendimento
        const modalEmpresas = new bootstrap.Modal(document.getElementById('modalEmpresasAtendidas'), {backdrop: 'static', keyboard: false});
        <?php if ($mostrarModalAtendimento): ?>
        setTimeout(() => { modalEmpresas.show(); }, 300);
        <?php endif; ?>
        document.getElementById('modalEmpresasAtendidas').addEventListener('show.bs.modal', function () {
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
            if (selecionadas.length === 0) {
                alert('Selecione pelo menos uma empresa para atendimento!');
                return;
            }
            const formData = new FormData();
            selecionadas.forEach(id => formData.append('empresas_atendidas[]', id));
            fetch('/configuracoes/salvar-empresas-atendidas', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.sucesso) {
                    // Fecha o modal e redireciona para o dashboard
                    modalEmpresas.hide();
                    // Redireciona para o dashboard para aplicar a nova seleção
                    window.location.href = '/dashboard';
                } else {
                    alert('Erro ao salvar empresas atendidas!');
                }
            });
        });
        // Impede fechar o modal sem selecionar empresa
        document.getElementById('btnCancelarEmpresasAtendidas').addEventListener('click', function(e) {
            e.preventDefault();
            alert('Selecione pelo menos uma empresa para atendimento!');
        });
        document.getElementById('fecharModalEmpresasAtendidas').addEventListener('click', function(e) {
            e.preventDefault();
            alert('Selecione pelo menos uma empresa para atendimento!');
        });
    });
    </script>
    <!-- Adicionar responsividade e estilos extras para o dropdown de notificações -->
    <style>
    #notificacoes-dropdown {
        max-width: 350px;
        min-width: 260px;
        box-shadow: 0 4px 24px rgba(0,0,0,0.13);
        border-radius: 10px;
        background: #f8f9fa;
        padding: 12px 0 0 0;
    }
    #notificacoes-dropdown ul {
        margin: 0;
        padding: 0 0 8px 0;
    }
    #notificacoes-dropdown li:last-child {
        border-bottom: none;
    }
    @media (max-width: 600px) {
        #notificacoes-dropdown {
            max-width: 98vw;
            min-width: 0;
            left: 2vw !important;
            right: 2vw !important;
        }
    }
    </style>
</body>
</html> 