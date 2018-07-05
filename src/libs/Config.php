<?php
namespace App\Libs;

    /**
     * 配置类
     *
     * @copyright (c) 2013, FunInSoft.
     * @license       http://www.funinsoft.com/license
     */

/**
 * 配置类
 *
 * 自动加载Protected/Configs/ 目录中的配置文件
 * 示例：
 *   CConfig::instance()->Db['default']['dsn']
 *   获取Db配置文件中的default->dsn的相关配置信息
 *
 */
class Config extends ClassBase {

    /**
     * 配置信息
     *
     * @var array
     */
    public $_config = [];

    /**
     * 获取本类单例
     *
     * @return Config
     */
    public static function instance() {
        return parent::instance();
    }

    /**
     * 自动重载获取配置项
     *
     * @param string $name 配置集名
     *
     * @return string
     */
    public function __get($name) {
        if (!isset($this->$name)) {
            $this->$name = $this->_loadFile($name);
        }

        if (isset($this->$name)) {
            return $this->$name;
        }

        return null;
    }

    /**
     * 刷新配置缓存
     *
     * @param string $name 配置集名
     */
    public function refreshCache($name) {
        $this->$name = $this->_loadFile($name);
    }

    /**
     * 加载配置文件
     *
     * @param string $name 配置集名
     */
    private function _loadFile($name) {
        $configPath = APP_PATH . '/configs/';
        if (defined('DEBUG')) {
            $filePath = $configPath . $name . 'RdDebug.php';
            if (!file_exists($filePath)) {
                $filePath = $configPath . $name . 'Debug.php';
                if (!file_exists($filePath)) {
                    $filePath = $configPath . $name . '.php';
                }
            }
        } else {
            $filePath = $configPath . $name . '.php';
        }
        if (is_file($filePath)) {
            return include $filePath;
        }

        return null;
    }
}
