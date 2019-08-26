<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2015 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: yunwuxin <448901948@qq.com>
// +----------------------------------------------------------------------
namespace tests;

class BaseCase extends \think\testing\TestCase
{
    protected $baseUrl = 'http://qianyan.uzipm.com';

    public $docDbConfig = [
        'type'     => 'mysql',
        'hostname' => '127.0.0.1',
        'database' => 'doc',
        'username' => 'root',
        'password' => '123456',
        'prefix'   => '',
    ];

    public $msmDbConfig = [
        'type'     => 'mysql',
        'hostname' => '127.0.0.1',
        'database' => 'pm_msm',
        'username' => 'root',
        'password' => '123456',
        'prefix'   => 'pm_',
    ];

    public function setUp()
    {

        $this->url_user = "http://pm.yy.com/index.php/";
        $this->url_www  = "http://pm.yy.com/index.php/";

        //$this->client = new \GuzzleHttp\Client( [ 'base_uri' => 'http://www.s.cn', 'http_errors' => false,  ]);
        $this->cookieJar = new \GuzzleHttp\Cookie\CookieJar();

        $options = [
            'base_uri' => $this->url_user,
            'timeout'  => 10.0,
            'cookies'  => $this->cookieJar,
            'cookies'  => true,
            //'proxy' => 'http://192.168.16.16:8888',
            // 'allow_redirects' => false,
        ];
        //if(!file_exists(ROOT_PATH."/test")&&!file_exists(ROOT_PATH."/online")){
        //$options['proxy'] = 'http://192.168.16.16:8888';
        //}

        $this->client = new \GuzzleHttp\Client($options);


    }

    public function login($username = '', $password = '')
    {
        //登录
        $rDoc         = db('doc', $this->docDbConfig)->where(['url' => '/index/Member/Login'])->find();
        $url          = $rDoc['url'];
        $parameter    = $rDoc['param_json'];
        $arrParameter = json_decode($parameter, 1);
        if (!empty($username) && !empty($password)) {
            $arrParameter['phone']    = $username;
            $arrParameter['password'] = $password;
        }

        $response    = $this->request($url, $rDoc['method'], $arrParameter);
        $resultLogin = jsonp_to_json($response->getBody());
        $resultLogin = json_decode($resultLogin, 1);
        $this->assertNotEmpty($resultLogin['openid']);
        return $resultLogin;
    }

    /**
     * @param $url
     * @param $method
     * @param $param
     * @param string $type
     * 'json' => ['foo' => 'bar']
     * 'multipart' => [    [    'name'     => 'field_name',    'contents' => 'abc'    ]]
     * @return mixed
     */
    function request($url, $method, $param, $header = [])
    {

        $method = strtolower($method);
        //if(empty($type)){
        //$type = ($method == 'post') ? 'form_params' : 'query';
        //}
        if (empty($header)) {
            $header = [
                'User-Agent'       => 'testing/1.0',
                'X-Wtfs-Signature' => 'testtesttesttesttest-test-test-test-testtesttesttest',
                'Referer'          => 'http://pm.yz314.com//front/view/app/user/bindPhone.html',
            ];
        }

        if ($method == 'post') {
            $r = $this->client->request('POST', $this->url_user . $url, [
                "form_params" => $param,
                'cookies'     => $this->cookieJar,
                'headers'     => $header,
            ]);
        } else {
            $r = $this->client->request('GET', $this->url_user . $url, [
                'query'   => $param,
                'cookies' => $this->cookieJar,
                'headers' => $header,
            ]);
        }
        return $r;

    }

    function getRegisterCode($mobile)
    {

        $code = db('vcode', $this->msmDbConfig)->where(['mobile' => $mobile, 'if_use' => 0])->order("id desc")->find();
        return $code['vcode'];
        //$code = $code['vcode'];

        //$code = 'a:5:{s:3:"uid";s:6:"105759";s:3:"pwd";s:32:"a85a6ba585d358da952900b94c4b437f";s:6:"mobile";s:11:"13022164481";s:8:"srcphone";s:12:"106907445759";s:3:"msg";s:79:"【极速拍卖】您本次注册的验证码是873994，有效期为5分钟。";}';
        $code = db('sms_log', $this->msmDbConfig)->where(['mobile' => $mobile])->order("id desc")->find();
        if (empty($code['senddata'])) return '';
        $code = $code['senddata'];
        $code = unserialize($code);
        if (empty($code['msg'])) return "";
        $code = $code['msg'];
        preg_match('/.*验证码是(\d+)，.*/', $code, $out);
        if (empty($out[1])) return '';
        $code = $out[1];
        return $code;
        var_dump($code);
        exit;
    }

    function register()
    {

        $mobile = '13' . mt_rand(0, 9) . '0000' . mt_rand(1000, 9999);
        //发验证码
        $rDoc                  = db('doc', $this->docDbConfig)->where(['url' => '/index/Member/getVcode'])->find();
        $url                   = $rDoc['url'];
        $parameter             = $rDoc['param_json'];
        $arrParameter          = json_decode($parameter, 1);
        $arrParameter['phone'] = $mobile;
        $response              = $this->request($url, $rDoc['method'], $arrParameter);
        //$resultLogin = jsonp_to_json($response->getBody());
        //$resultLogin= json_decode($resultLogin,1);
        //sleep(3);
        //echo $response->getBody();
        //var_dump(json_decode($response->getBody()));exit;


        $code = $this->getRegisterCode($mobile);
        if (empty($code)) {
            exit('验证码为空');
        }
        //登录
        $rDoc                       = db('doc', $this->docDbConfig)->where(['url' => '/index/Member/Register'])->find();
        $url                        = $rDoc['url'];
        $parameter                  = $rDoc['param_json'];
        $arrParameter               = json_decode($parameter, 1);
        $arrParameter['phone']      = $mobile;
        $arrParameter['verifyCode'] = $code;

        $response  = $this->request($url, $rDoc['method'], $arrParameter);
        $resultApi = jsonp_to_json($response->getBody());
        $resultApi = json_decode($resultApi, 1);
        $this->assertNotEmpty($resultApi['openid']);

//var_dump(json_decode($rDoc['return_json']);
        check_recursive(json_decode($rDoc['return_json'], 1), $resultApi);


        return $resultApi;


    }

    function socket_post($url, $data, $referer = '')
    {
        if (!is_array($data)) {
            return;
        }

        $data = http_build_query($data);
        $url  = parse_url($url);

        if (!isset($url['scheme']) || $url['scheme'] != 'http') {
            die('Error: Only HTTP request are supported !');
        }

        $host = $url['host'];
        $path = isset($url['path']) ? $url['path'] . '?' . $url['query'] : '/';

        // open a socket connection on port 80 - timeout: 30 sec
        $fp = fsockopen($host, 80, $errno, $errstr, 30);

        if ($fp) {
            // send the request headers:
            $length = strlen($data);
            $POST   = <<<HEADER
POST {$path} HTTP/1.1
Host: {$host}
Accept: text/plain, text/html
Accept-Language: zh-CN,zh;q=0.8
Content-Type: application/x-www-form-urlencodem
Cookie: token=value; pub_cookietime=2592000; pub_sauth1=value; pub_sauth2=value
User-Agent: Mozilla/5.0 (iPhone; CPU iPhone OS 11_0 like Mac OS X) AppleWebKit/604.1.38 (KHTML, like Gecko) Version/11.0 Mobile/15A372 Safari/604.1
Content-Length: {$length}
Pragma: no-cache
Cache-Control: no-cache
Connection: close
Accept-Encoding: gzip, deflate
Cookie: baJf_2132_forum_lastvisit=D_36_1423124866D_2_1423125031;

{$data}
HEADER;
            // echo $POST;exit('x11111');
            fwrite($fp, $POST);
            $result = '';
            while (!feof($fp)) {
                // receive the results of the request
                $result .= fread($fp, 512);
            }
        } else {
            return array(
                'status' => 'error',
                'error'  => "$errstr ($errno)"
            );
        }

        // close the socket connection:
        fclose($fp);

        // split the result header from the content
        $result = explode("\r\n\r\n", $result, 2);

        // var_dump($host);

        // var_dump($path);
        // var_dump($result);exit('x');
        // return as structured array:

        $resp_body = $this->unchunk($result[1], (bool)stristr($result[0], 'chunk'), (bool)stristr($result[0], 'gzip'));

        echo $resp_body;
        exit;
        return array(
            'status'  => 'ok',
            'header'  => isset($result[0]) ? $result[0] : '',
            'content' => isset($result[1]) ? $result[1] : ''
        );
    }


    function sendRequestByRaw()
    {
        $data               = [];
        $data['mobile']     = '13800010017';
        $data['verifycode'] = 12345;
        $data['pwd']        = 123456;

        $r = $this->socket_post('http://qianyan.uzipm.com/app/index.php?i=16&c=entry&m=ewei_shopv2&do=mobile&r=account.register', $data);
        var_dump($r);
        exit;
    }

    function sendRequestByRaw2()
    {

        $host = 'qianyan.uzipm.com';
        $fp   = fsockopen($host, 80, $errno, $errstr, 30);
        if (!$fp) {
            echo "$errstr ($errno)<br />\n";
        } else {
            $raw = 'POST /app/index.php?i=16&c=entry&m=ewei_shopv2&do=mobile&r=account.register HTTP/1.1
Host: qianyan.uzipm.com
Connection: keep-alive
Content-Length: 46
Pragma: no-cache
Cache-Control: no-cache
Accept: application/json, text/javascript, */*; q=0.01
Origin: http://qianyan.uzipm.com
X-Requested-With: XMLHttpRequest
User-Agent: Mozilla/5.0 (iPhone; CPU iPhone OS 11_0 like Mac OS X) AppleWebKit/604.1.38 (KHTML, like Gecko) Version/11.0 Mobile/15A372 Safari/604.1
Content-Type: application/x-www-form-urlencoded; charset=UTF-8
Socketlog: SocketLog(tabid=293&client_id=)
Referer: http://qianyan.uzipm.com/app/index.php?i=16&c=entry&m=ewei_shopv2&do=mobile&r=account.register
Accept-Encoding: gzip, deflate
Accept-Language: zh-CN,zh;q=0.9

mobile=13800010017&verifycode=12345&pwd=123456';

            fwrite($fp, $raw);
            while (!feof($fp)) {
                echo fgets($fp, 128);

            }
            /* $f='';
            while (!feof($fp)) {
                $f .= fgets($fp, 1024);
            }

                        list($resp_headers, $resp_body) = explode("\r\n\r\n", trim($f), 2);

                        /*
                                   $resp_body = $this->unchunk($resp_body, (bool)stristr($resp_headers,'chunk'), (bool)stristr($resp_headers,'gzip'));

                                   echo $resp_body;*/

            fclose($fp);
            eixt('sb');

        }
    }

    // Unchunk and gzip the response data
    function unchunk($str, $chunked = 1, $gzipped = 0)
    {

        // if neither gzipped nor chunked...
        if (!$gzipped && !$chunked) return $str;

        // else if only gzipped, and not chunked...
        if ($gzipped && !$chunked) {
            $g = 'gzip_file.txt';
            file_put_contents($g, $str);
            ob_start();
            readgzfile($g);
            $d = ob_get_clean();
            @unlink($g);
            return $d;
        }

        // else, if chunked (and maybe gzipped), do the following...
        $tmp = $str;
        $lrn = strlen("\r\n");
        $str = '';
        $ofs = 0;
        do {
            $p   = strpos($tmp, "\r\n", $ofs);
            $len = hexdec(substr($tmp, $ofs, $p - $ofs));
            $str .= substr($tmp, $p + $lrn, $len);
            $ofs = $p + $lrn * 2 + $len;
        } while ($tmp[$ofs] !== '0');

        if ($gzipped) {
            $str = substr($str, 10);
            $str = gzinflate($str);
        }

        return $str;
    }


}