<?php
return [
    'settings' => [
        'displayErrorDetails' => DEBUG, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header
        // API是否需要验证
        // Monolog settings
        'logger' => [
            'name' => \App\Libs\Config::instance()->System['app']['name'],
            'path' => isset($_ENV['docker']) ? 'php://stdout' : APP_PATH . '/../logs/app.log',
            'level' => \Monolog\Logger::DEBUG,
        ],
    ],
];
