<?php
namespace App\Libs;

use \GuzzleHttp\Client as Client;
use GuzzleHttp\Exception\RequestException as RequestException;

/**
 * Class MicroService
 *
 * @package App\Libs
 */
class MicroService extends ClassBase {
    /**
     * GET请求
     */
    const GET = 'GET';
    /**
     * POST请求
     */
    const POST = 'POST';
    /**
     * PUT请求
     */
    const PUT = 'PUT';
    /**
     * DELETE请求
     */
    const DELETE = 'DELETE';

    /**
     * 请求的应用
     *
     * @var string
     */
    protected $app = '';

    /**
     * 请求配置文件
     *
     * @var array
     */
    protected $configs = [];
    /**
     * 类实例对象
     *
     * @var array
     */
    protected static $_instance = [];

    /**
     * 请求URL
     *
     * @var string
     */
    protected $url = '';
    /**
     * 请求method
     *
     * @var string
     */
    protected $method = 'GET';
    /**
     * 请求携带的query参数类似于
     *
     * @var string
     */
    protected $queries  = '';
    protected $body     = '';
    protected $date     = 0;
    protected $response = null;
    protected $headers = [];

    public function __construct($app) {
        if (isset(Config::instance()->Microservice[$app])) {
            $this->configs[$app] = Config::instance()->Microservice[$app];
        } else {
            $this->configs[$app] = [];
        }

        $this->app = $app;
    }

    /**
     * @param string $app
     *
     * @return MicroService
     */
    public static function instance($app = 'default') {
        $className = get_called_class();
        if (!isset(self::$_instance[$app])) {
            self::$_instance[$app] = new $className($app);
        }
        self::$_instance[$app]->app = $app;

        return self::$_instance[$app];
    }

    protected function setUri($uri = '') {
        $this->url = $uri;
    }

    /**
     * 设置queries
     *
     * @param array $data
     *
     * @return $this
     */
    public function setQueries($data = []) {
        $this->queries = $data;

        return $this;
    }

    /**
     * 设置body请求
     *
     * @param array $data
     *
     * @return $this
     */
    public function setBody($data = []) {
        $this->body = $data;

        return $this;
    }

    /**
     * 设置自定义HEADER
     *
     * @param $key
     * @param $value
     *
     * @return $this
     */
    public function setHeader($key,$value) {
        $this->headers[$key] = $value;

        return $this;
    }

    /**
     * 获取自定义HEADER参数
     *
     * @return array
     */
    public function getHeader() {
        return $this->headers;
    }

    protected function send($method = self::GET) {
        $this->method = $method;
        if ($this->url == "") {
            throw new \Exception('url should not empty');
        }
        if (empty($this->configs[$this->app])) {
            throw new \Exception('没有找到' . $this->app . '对应的配置文件');
        }

        $host      = $this->configs[$this->app]['host'];
        $appKey    = $this->configs[$this->app]['appkey'];
        $appSecret = $this->configs[$this->app]['appsecret'];
        if ($host == '') {
            throw new \Exception('没有找到' . $this->app . '[host]配置');
        }
        if ($appKey == '') {
            throw new \Exception('没有找到' . $this->app . '[appid]配置');
        }
        if ($appSecret == '') {
            throw new \Exception('没有找到' . $this->app . '[appsecret]配置');
        }

        $this->setDate();
        $this->url = $this->buildUri();

        if ($this->method == self::GET && !empty($this->body)) {
            $this->queries = array_merge($this->queries, $this->body);
            $this->body    = [];
        }

        ksort($this->queries);
        $sign = $this->generateSign($appKey, $appSecret);

        $client   = new Client(['base_uri' => $host]);
        $response = null;
        $headers = [
            'X-Parse-Appkey' => $appKey,
            'X-Parse-Sign' => $sign,
            'X-Parse-Date' => $this->date,
            'Content-Type' => 'application/json'
        ];
        $headers = array_merge($headers,$this->headers);
        try{
            $response = $client->request($this->method, $this->url, ['query' => $this->queries, 'json' => $this->body, 'headers' => $headers, 'timeout' => 30]);
        }catch(RequestException $e) {
            if($e->hasResponse()){
                $response = $e->getResponse();
            }
        }

        $responseBody = $response->getBody()->getContents();
        if ($response->getStatusCode() != 200) {
            switch ($response->getStatusCode()) {
                case 401:
                case 403:
                    throw new \Exception("没有权限", $response->getStatusCode());
                case 502:
                    throw new \Exception("远程服务暂时无法连接", $response->getStatusCode());
                case 504:
                    throw new \Exception("远程服务连接超时", $response->getStatusCode());
                default:
                    $err     = json_decode($responseBody, true);
                    $errcode = 0;
                    $errmsg  = '';
                    if (isset($err['errcode'])) {
                        $errcode = $err['errcode'];
                    }
                    if (isset($err['errmsg'])) {
                        $errmsg = $err['errmsg'];
                    }
                    if ($errcode != 0 && $errmsg != '') {
                        throw new \Exception($errmsg, $errcode);
                    }
                    throw new \Exception("请求错误:" . $responseBody, $response->getStatusCode());
            }
        }

        return json_decode($responseBody, true);
    }

    /**
     * 构建请求uri
     * 如果uri携带有:xxx的参数同时存在queries中存在该参数,:xxx会被替换成具体的参数同时queries中会删除该参数
     *
     * @return mixed|string
     */
    protected function buildUri() {
        $uri = $this->url;
        if (is_array($this->queries)) {
            foreach ($this->queries as $k => $v) {
                if (FALSE != strpos($uri, ":" . $k)) {
                    $uri = str_replace(':' . $k, $v, $uri);
                    unset($this->queries[$k]);
                }
            }
        }

        return $uri;
    }

    /**
     * 设置日期
     */
    protected function setDate() {
        $this->date = date('Y-m-d H:i:s O', time());
    }

    /**
     * 生成加密签名
     *
     * @param string $appkey    appkey
     * @param string $appsecret appsecret
     *
     * @return string
     */
    protected function generateSign($appkey, $appsecret) {
        $baseStr = $this->url;
        if (strlen(http_build_query($this->queries)) > 0 ) {
            $baseStr = $this->url . "?";
        }
        $baseStr = $baseStr . http_build_query($this->queries) . $appkey . $this->date;
        // 只有当body不为空且非get请求时才进行处理
        if (is_array($this->body) && array_sum(array_keys($this->body)) > 0 && $this->method != self::GET) {
            $baseStr .= json_encode($this->body, true);
        } else {
            if (!empty($this->body) && is_string($this->body)) {
                $baseStr .= $this->body;
            }
        }

        return hash_hmac('sha1', $baseStr, $appsecret);
    }

    /**
     * get请求
     *
     * @param string $uri
     * @param array  $queries
     *
     * @return mixed
     */
    public function get($uri = '', $queries = []) {
        $this->setUri($uri);
        if (count($queries) > 0) {
            $this->queries = $queries;
        }

        return $this->send(self::GET);
    }

    /**
     * post请求
     *
     * @param string $uri
     * @param array  $queries
     * @param array  $body
     *
     * @return mixed
     */
    public function post($uri = '', $queries = [], $body = []) {
        $this->setUri($uri);
        if (count($queries) > 0) {
            $this->queries = $queries;
        }
        if (count($body) > 0) {
            $this->body = $body;
        }

        return $this->send(self::POST);
    }

    /**
     * put请求
     *
     * @param string $uri
     * @param array  $queries
     * @param array  $body
     *
     * @return mixed
     */
    public function put($uri = '', $queries = [], $body = []) {
        $this->setUri($uri);
        if (count($queries) > 0) {
            $this->queries = $queries;
        }
        if (count($body) > 0) {
            $this->body = $body;
        }

        return $this->send(self::PUT);
    }

    /**
     * delete请求
     *
     * @param string $uri
     * @param array  $queries
     * @param array  $body
     *
     * @return mixed
     */
    public function delete($uri = '', $queries = [], $body = []) {
        $this->setUri($uri);
        if (count($queries) > 0) {
            $this->queries = $queries;
        }
        if (count($body) > 0) {
            $this->body = $body;
        }

        return $this->send(self::DELETE);
    }
}