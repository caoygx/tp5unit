<?php
namespace app\common\util;
class http
{
    protected $baseUrl = 'http://localhost';
    protected $client;
    protected $cookieJar;

    function __construct()
    {

        $this->cookieJar = new \GuzzleHttp\Cookie\CookieJar();

        $config = [
            'base_uri' => "",
            'timeout'  => 10.0,
            'cookies'  => $this->cookieJar,
            'cookies'  => true,
            //'proxy' => 'http://192.168.16.16:8888',
            // 'allow_redirects' => false,
        ];

        $config['proxy'] = 'http://192.168.16.96:8888';

        $this->client = new \GuzzleHttp\Client($config);
    }

    function request($uri, $method, $param, $header = [])
    {

        $method = strtolower($method);

        $options            = [];
        $options['cookies'] = $this->cookieJar;
        $options['headers'] = $header;
        $options['verify']  = false;


        if (is_array($param)) {
            $options['form_params'] = $param;
        } elseif (is_string($param)) {
            $options['body'] = $param;
        }

        $method = strtoupper($method);
        $r      = $this->client->request($method, $uri, $options);
        return $r;

    }


}