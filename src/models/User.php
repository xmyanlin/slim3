<?php
namespace App\Models;
use App\Libs\BaseModel;

/**
 * Created by PhpStorm.
 * User: scofield
 * Date: 09/11/2017
 * Time: 18:31
 */
class User extends BaseModel {
    protected $table = "user";
    protected $_connection = "test";
}