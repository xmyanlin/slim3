<?php
if (PHP_SAPI == 'cli-server') {
    // To help the built-in PHP dev server, check if the request was actually for
    // something which should probably be served as a static file
    $url  = parse_url($_SERVER['REQUEST_URI']);
    $file = __DIR__ . $url['path'];
    if (is_file($file)) {
        return FALSE;
    }
}

// 设置时区
if (!ini_get('date.timezone')) {
    date_default_timezone_set('Asia/Shanghai');
}

// 定义应用常量
define('APP_PATH', dirname(__FILE__) . '/../src/');
define('LOG_PATH', dirname(__FILE__) . '/../logs/');

// 检测debug环境
if (file_exists(LOG_PATH . 'DEBUG') || getenv('DEBUG')) {
    $_ENV['DEBUG'] = True;
    define('DEBUG', True);
} else {
    define('DEBUG', false);
    $_ENV['DEBUG'] = false;
}
require __DIR__ . '/../vendor/autoload.php';

session_start();

// Instantiate the app
$settings = require __DIR__ . '/../src/settings.php';
$app      = new \Slim\App($settings);

// Set up dependencies
require __DIR__ . '/../src/dependencies.php';

// Register middlewares
require __DIR__ . '/../src/middleware.php';

// Register routes
require __DIR__ . '/../src/routes.php';

// Run app
$app->run();
