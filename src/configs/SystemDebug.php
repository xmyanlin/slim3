<?php
/**
 * Created by PhpStorm.
 * User: scofield
 * Date: 08/11/2017
 * Time: 18:17
 */
return [
    // app信息
    'app' => [
        // app名称
        'name' => 'api bootstrap',
        // 版本
        'version' => '0.1'
    ],
    // 是否开启远程请求验证
    'apiAuth' => true,
    'logger' => [
        // 模式,支持info,warning,mode
        'mode' => \Monolog\Logger::DEBUG ,
        'path' => isset($_ENV['docker']) ? 'php://stdout' : APP_PATH . '/../logs/'.date('Y-m-d',time()).'.log',
        // 远程日志推送
        'remote' => [
            // 是否开启
            'status' => false,
            // appid
            'appid' => 'default',
            // secret
            'secret' => '25b3062b09ecf557c078aa5761683167',
            // channel
            'channel' => 'default',
            // 远端地址
            'url' => 'http://127.0.0.1:2110/api/v1/push'
        ]
    ]
];