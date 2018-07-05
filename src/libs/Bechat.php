<?php
namespace App\Libs;

class Bechat extends ClassBase {
    // api的配置信息
    private $_config;

    private $_timestamp = 0;

    /**
     * 获取本类操作实例
     *
     * @return \App\Libs\Bechat
     **/
    public static function instance() {
        return parent::instance();
    }

    public function __construct() {
        $this->_config = Config::instance()->System['logger']['remote'];
    }

    /**
     * 生成sign
     *
     * @return string
     */
    protected function generateSign() {
        return md5($this->_config['appid'] . $this->_timestamp . $this->_config['secret']);
    }

    /**
     * 设置时间戳
     *
     * @return $this
     */
    protected function setTimestamp() {
        $this->_timestamp = time();

        return $this;
    }

    /**
     * 请求bechat
     *
     * @param $data
     *
     * @return bool
     * @throws \Exception
     */
    private function _send($data) {
        if ($this->_config['url'] == '') {
            throw new \Exception('system[logger][remote][url] not found');
        }

        $this->setTimestamp();
        if (is_array($data)) {
            $data = json_encode($data);
        }

        $curl = curl_init();
        curl_setopt_array($curl, [CURLOPT_URL => $this->_config['url'], CURLOPT_RETURNTRANSFER => TRUE, CURLOPT_TIMEOUT => 30, CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, CURLOPT_CUSTOMREQUEST => 'POST', CURLOPT_HTTPHEADER => ['Content-Type: application/json', sprintf("app-bechat-appid: %s", $this->_config['appid']), sprintf("app-bechat-time: %s", $this->_timestamp), sprintf("app-bechat-sign: %s", $this->generateSign())], CURLOPT_HEADER => 1, CURLOPT_POSTFIELDS => $data]);

        $response = curl_exec($curl);
        if (curl_errno($curl)) {
            throw new \Exception(curl_error($curl));
        }
        if (200 === curl_getinfo($curl, CURLINFO_HTTP_CODE)) {
            return true;
        }

        return false;
    }

    public function run($data) {
        if (is_array($data)) {
            $data = json_encode($data);
        }

        return $this->_send(['channel' => $this->_config['channel'], 'content' => $data]);
    }
}