<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titulo ?? 'MDE' ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="/assets/css/style.css" rel="stylesheet">
    <style>
    body {
        background-color: #f4f6fa !important;
    }
    .card, .card.text-center, .card.shadow-sm {
        border: 2px solid #e0e3e8 !important;
        box-shadow: 0 4px 18px 0 rgba(37, 41, 46, 0.13), 0 2px 8px 0 rgba(44,62,80,0.10) !important;
        border-radius: 14px !important;
        background: #fff !important;
    }
    .card .card-body {
        border-radius: 14px !important;
        box-shadow: 0 2px 8px 0 rgba(37, 41, 46, 0.13) !important;
        background: #fff !important;
    }
    #graficoLinhas, #graficoMapa, #graficoEscriturador, #graficoColeta, #graficoTopValor, #graficoTopRecorrentes {
        background: #f8fafc !important;
        border: 2px solid #e0e3e8 !important;
        border-radius: 14px !important;
        box-shadow: 0 2px 8px 0 rgba(37, 41, 46, 0.13) !important;
        padding: 12px !important;
    }
    </style>
</head>
<body>
     <!-- Mensagens de erro/sucesso -->
    <?php if (isset($_SESSION['erro'])): ?>
        <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
            <?= $_SESSION['erro'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['erro']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['sucesso'])): ?>
        <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
            <?= $_SESSION['sucesso'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['sucesso']); ?>
    <?php endif; ?> 