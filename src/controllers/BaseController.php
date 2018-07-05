<?php
namespace App\Controllers;

use Interop\Container\ContainerInterface;
use \App\Libs\MicroService as MicroService;
use Slim\Http\Response as Response;
use \App\Libs\Response as Resp;

/**
 * 基控制器
 * Class BaseController
 *
 * @package App\Controllers
 */
class BaseController {
    protected $container = null;

    public function __construct( ContainerInterface $container ) {
        $this->container = $container;
    }

    /**
     *  调用微服务
     *
     * @param string $appName
     *
     * @return \App\Libs\MicroService
     */
    public function call( $appName = '' ) {
        return MicroService::instance ( $appName );
    }

    /**
     * 快速调用 response
     *
     * @param $response
     *
     * @return \App\Libs\Response
     */
    public function response( Response $response ) {
        return Resp::instance ( $response );
    }
}
