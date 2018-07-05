<?php

use Slim\Http\Request as Request;
use Slim\Http\Response as Response;

// cli模式下控制台输出request信息
if (DEBUG && PHP_SAPI == 'cli-server') {
    $app->add(function(Request $request, Response $response, $next) {
        $ch = fopen('php://stdout', 0755);
        fputs($ch, sprintf("=======request======\n"));
        fputs($ch, sprintf("url:[%s]%s\n", $request->getMethod(),$request->getUri()));
        $headers = '';
        foreach ($request->getHeaders() as $k => $v) {
            $headers .= sprintf("%s: %s\n", $k, $v[0]);
        }

        $response = $next($request, $response);

        fputs($ch, sprintf("headers:\n%s\n", $headers));
        fputs($ch,"=======response======\n");
        fputs($ch,sprintf("status code:%d\n",$response->getStatusCode()));
        fputs($ch,sprintf("response:%s\n",$response->getBody()->getContents()));

        return $response;
    });
}

