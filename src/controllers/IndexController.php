<?php
namespace App\Controllers;

use App\Models\User;
use \Slim\Http\Request as Request;
use \Slim\Http\Response as Response;

/**
 * Created by PhpStorm.
 * User: scofield
 * Date: 09/11/2017
 * Time: 18:26
 */
class IndexController extends BaseController {
    public function indexAction(Request $request, Response $response, $args) {
        $user = User::get();
        return $this->response($response)->success($user);
    }
}