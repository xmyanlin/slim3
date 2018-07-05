<?php
namespace App\Libs;
/**
 * Redis操作类
 *
 * 需要安装 phpredis - 2.2.5 扩展，扩展地址https://github.com/nicolasff/phpredis
 * CRedis::instance('default')获取操作实例，获取的操作函数请查看 https://github.com/nicolasff/phpredis/blob/master/README.markdown
 *
 * 配置说明
 *
 * 连接单个Redis服务器
 * array(
 *   array(
 *       'default' => array(
 *           'host'=>'192.168.20.207', //服务器地址
 *           'port'=>'',//服务器端口号
 *           'timeout'=>'2',//超时时间
 *           'dbname'=>'',//数据库名
 *           'password'=>'',//连接密码
 *           'serialize'=>true,//是否允许序列化
 *           'prefix'=>'',//KEY前缀，自动为所有的KEY添加前缀
 *       )
 * );
 *
 * 连接多台服务器时
 * array(
 *   'default' => array(
 *       'hosts'=>array(
 *           '127.0.0.1:6379',
 *           '127.0.0.1:6380',
 *           ),
 *        'option'=>array(
 *           'autorehash'=>true,
 *        )
 *    )
 * );
 *
 *
 */
class Redis {
    //类实例
    private static $_instance = array();

    private static function getConnect($configName = 'default') {
        $link = null;
        $config = Config::instance()->Redis;
        if (isset($config[$configName])) {
            if (isset($config[$configName]['host'])) {
                $link = new \Redis();

                $link->connect($config[$configName]['host'], (isset($config[$configName]['port']) && !empty($config[$configName]['port']) ? $config[$configName]['port'] : 6379), (isset($config[$configName]['timeout']) && intval($config[$configName]['timeout']) ? $config[$configName]['timeout'] : 2)) or die('Can`t connect to Redis server:' . $config[$configName]['host']);

                if (isset($config[$configName]['password']) && !empty($config[$configName]['password'])) {
                    $link->auth($config[$configName]['password']);
                }

                if (isset($config[$configName]['dbname']) && is_numeric($config[$configName]['dbname'])) {
                    $link->select($config[$configName]['dbname']);
                }

                if (isset($config[$configName]['serialize']) && !empty($config[$configName]['serialize'])) {
                    $link->setOption(\Redis::OPT_SERIALIZER, $config[$configName]['serialize']);
                }

                if (isset($config[$configName]['prefix']) && !empty($config[$configName]['prefix'])) {
                    $link->setOption(\Redis::OPT_SERIALIZER, $config[$configName]['prefix']);
                }
            } elseif (isset($config[$configName]['hosts'])) {
                $link = new \RedisArray($config[$configName]['hosts'], (isset($config[$configName]['option'])) ? $config[$configName]['option'] : '');
            }
        }
        return $link;
    }

    /**
     * 获取本类操作实例
     *
     * @param string $configName redis服务器配置名
     *
*@return Redis
     */
    public static function instance($configName = 'default') {
        if (!isset(self::$_instance[$configName])) {
            self::$_instance[$configName] = self::getConnect($configName);
        }
        return self::$_instance[$configName];
    }
}