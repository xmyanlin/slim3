<?php
// DIC configuration
$container = $app->getContainer();

// monolog
$container['logger'] = function($c) {
    $settings = App\Libs\Config::instance()->System['logger'];

    $logger = new Monolog\Logger(App\Libs\Config::instance()->System['app']['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());

    $dateFormat = "Y-m-d H:i:s";
    $output     = "[%level_name%]%datetime%:%message%:%context%:%extra%\n";
    $formatter  = new \Monolog\Formatter\LineFormatter($output, $dateFormat);

    $handler = new Monolog\Handler\StreamHandler($settings['path'], $settings['mode']);
    $handler->setFormatter($formatter);
    $logger->pushHandler($handler);

    if ($settings['remote']['status']) {
        $bechatHandler = new App\Middlewares\Logger($settings['mode']);
        $bechatHandler->setFormatter($formatter);
        $logger->pushHandler($bechatHandler);
    }

    return $logger;
};

$container['errorHandler'] = function($c) {
    return function($request, $response, \Exception $exception) use ($c) {
        $c->logger->error('errorHandler trigger', ['msg' => $exception->getMessage(), 'code' => $exception->getCode(), 'file' => $exception->getFile(), 'line' => $exception->getLine()]);

        return $c['response']->withStatus(500)->withHeader('Content-Type', 'application/json')->withJson(['errcode' => 100500, 'errmsg' => 'server error']);
    };
};

$container['notFoundHandler'] = function($c) {
    return function($request, $response) use ($c) {
        return $c['response']->withStatus(404)->withHeader('Content-Type', 'application/json')->withJson(['errcode' => 100400, 'errmsg' => 'not found']);
    };
};

$container['phpErrorHandler'] = function($c) {
    return function($request, $response, $error) use ($c) {
        $c->logger->error($error);

        return $c['response']->withStatus(500)->withHeader('Content-Type', 'application/json')->withJson(['errcode' => 100500, 'errmsg' => 'server error']);
    };
};

$container['notAllowedHandler'] = function($c) {
    return function($request, $response, $methods) use ($c) {
        return $c['response']->withStatus(405)->withHeader('Content-Type', 'application/json')->withJson(['errcode' => 405, 'errmsg' => 'method not allowed']);
    };
};
