<?php
namespace App\Libs;

use \Psr\Http\Message\ResponseInterface as Resp;

/**
 * 返回成功
 * Created by PhpStorm.
 * User: scofield
 * Date: 08/11/2017
 * Time: 19:25
 */
class Response {
    protected static $self     = null;
    protected        $response = null;

    /**
     * 实例化
     *
     * @param Resp $response
     *
     * @return Response|null
     */
    public static function instance( Resp $response ) {
        if ( is_null ( self::$self ) ) {
            self::$self           = new Response();
            self::$self->response = $response;
        }

        return self::$self;
    }

    /**
     * 快捷成功输出
     *
     * @return mixed
     */
    public function ok() {
        return $this->success ( [ 'status' => 'ok' ] );
    }

    /**
     * 成功输出
     *
     * @param $data
     *
     * @return mixed
     */
    public function success( $data ) {
        return $this->withJson ( 200, $data );
    }

    /**
     * 返回json数据
     *
     * @param $statusCode
     * @param $data
     *
     * @return mixed
     */
    public function withJson( $statusCode, $data ) {
        return $this->response->withStatus ( $statusCode )->withJson ( $data );
    }

    /**
     * 错误输出
     *
     * @param $code
     * @param $msg
     *
     * @return mixed
     */
    public function error( $code, $msg ) {
        return $this->withJson ( 400, [ 'errcode' => $code, 'errmsg' => $msg ] );
    }
}