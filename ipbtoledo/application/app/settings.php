<?php
declare(strict_types=1);

if (getenv('APP_DEBUG')) {
    $debug = true;
} else {
    $debug = false;
}
return [
    // Slim Settings
    'displayErrorDetails' => $debug,
    'determineRouteBeforeAppMiddleware' => true,

    // Database settings
    'db' => [
        'host' => getenv('DB_HOST'),
        'port' => getenv('DB_PORT'),
        'charset' => getenv('DB_CHARSET'),
        'name' => getenv('DB_NAME'),
        'user' => getenv('DB_USER'),
        'pass' => getenv('DB_PASS'),
    ],

    'mail' => [
        'username' => getenv('EMAIL_USERNAME'),
        'password' => getenv('EMAIL_PASSWORD'),
        'host' => getenv('EMAIL_HOST'),
        'smtpSecureType' => 'tls',
        'port' => '587',
    ],

    'pagseguro' => [
        'env' => getenv('PAGSEGURO_ENV'),
        'email' => getenv('PAGSEGURO_EMAIL'),
        'charset' => getenv('PAGSEGURO_CHARSET'),
        'production' => [
            'token' => getenv('PAGSEGURO_TOKEN_PRODUCTION'),
        ],
        'sandbox' => [
            'token' => getenv('PAGSEGURO_TOKEN_SANDBOX'),
        ],
    ],

    // View settings
    'view' => [
        'template_path' => __DIR__ . '/templates',
        'twig' => [
            'auto_reload' => true,
            'cache' => __DIR__ . '/../cache/twig',
            'debug' => $debug,
        ],
    ],

    // Monolog settings
    'logger' => [
        'name' => 'app',
        'path' => __DIR__ . '/../logs/app.' . date('Y-m-d') . '.log'
    ],
];
