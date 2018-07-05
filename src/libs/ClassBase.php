<?php
namespace App\Libs;

/**
 * 类的基础类
 *
 * @copyright (c) 2013, FunInSoft.
 * @license http://www.funinsoft.com/license
 */
class ClassBase {

    /**
     * 静态单例实例数组
     * @var array
     */
    static private $_instances = array();

    /**
     * 获取本类的单例
     *
     * @return object
     */
    public static function instance() {
        $className = get_called_class();
        if (!isset(self::$_instances[$className])) {
            self::$_instances[$className] = new $className();
        }
        return self::$_instances[$className];
    }

}