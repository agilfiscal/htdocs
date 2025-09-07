<?php
return [
    /*
    |--------------------------------------------------------------------------
    | Driver de Email Padrão
    |--------------------------------------------------------------------------
    */
    'default' => env('MAIL_DRIVER', 'smtp'),
    
    /*
    |--------------------------------------------------------------------------
    | Configurações de Email
    |--------------------------------------------------------------------------
    */
    'mailers' => [
        'smtp' => [
            'transport' => 'smtp',
            'host' => env('MAIL_HOST', 'smtp.gmail.com'),
            'port' => env('MAIL_PORT', 587),
            'encryption' => env('MAIL_ENCRYPTION', 'tls'),
            'username' => env('MAIL_USERNAME'),
            'password' => env('MAIL_PASSWORD'),
            'timeout' => null,
            'auth_mode' => null,
        ],
        
        'ses' => [
            'transport' => 'ses',
        ],
        
        'mailgun' => [
            'transport' => 'mailgun',
        ],
        
        'sendmail' => [
            'transport' => 'sendmail',
            'path' => '/usr/sbin/sendmail -bs',
        ],
        
        'log' => [
            'transport' => 'log',
            'channel' => env('MAIL_LOG_CHANNEL'),
        ],
        
        'array' => [
            'transport' => 'array',
        ],
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Configurações de Email
    |--------------------------------------------------------------------------
    */
    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'contato@meusite.com'),
        'name' => env('MAIL_FROM_NAME', 'Meu Site'),
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Configurações de Email
    |--------------------------------------------------------------------------
    */
    'markdown' => [
        'theme' => 'default',
        'paths' => [
            resource_path('views/vendor/mail'),
        ],
    ],
]; 