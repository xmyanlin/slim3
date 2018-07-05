<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/7/5
 * Time: 10:25
 */
namespace App\Libs;


use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model {

    protected $_connection = "default";

    private static $_instance = array();
    /**
     * 构造函数
     *
     * @param 设置数据库链接
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->connection();
    }

    /**
     * 链接数据库
     *
     *
     */
    public function connection(){
        if (!isset(Config::instance()->Db[$this->_connection])) {
            throw new \Exception('The database configuration is not found.', 500);
        }
        if(!isset(self::$_instance[$this->_connection])){
            $capsule = new Manager;
            $config = Config::instance()->Db[$this->_connection];
            $setting = [
                'driver' => 'mysql',
                'host' => "localhost",
                'database' => "test",
                'username' => $config["user"],
                'password' => $config["pass"],
                'charset'   => 'utf8',
                'prefix'    => '',
            ];
            $capsule->addConnection($setting);

            $capsule->setAsGlobal();
            $capsule->bootEloquent();
            self::$_instance[$this->_connection] = true;
        }
    }
}