<?php

$app->get('/',App\Controllers\IndexController::class.':indexAction');

$app->group('/api',function() use ($app){
    $app->any('/test',App\Controllers\IndexController::class.':authAction');
});