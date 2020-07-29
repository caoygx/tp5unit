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

use rest\index\model\Drawprize;
use rest\index\model\Robot;
use  rest\index\model\Subsidy;
use rest\index\model\User;
use think\Db;
use \app\common\util\http;


class UserTest extends BaseCase
{


    function testRegister()
    {
        //$this->sendRequestByRaw(); //post原生发送，返回结果会包含一些符号，返回5b\n{"status":0,"result":{"message":"\u60a8\u8f93\u5165\u7684\u8d26\u53f7\u5df2\u5b58\u5728~"}}\n0
        $url    = $this->baseUrl . "/user/register";
        $mobile = '1380000' . mt_rand(1000, 9999);

        //register
        $params               = [];
        $params['mobile']     = $mobile;
        $params['code']       = '12345';
        $params['password']   = '123456';
        $params['repassword'] = '1234567';
        $res                  = $this->request($url, 'post', $params);
        $body                 = $res->getBody();
        $body                 = json_decode($body, true);
        $this->assertEquals('1', $body['code']);
    }


    function testLogin()
    {
        $url                = $this->baseUrl . "/user/login";
        $mobile             = '13812341232';
        $params             = [];
        $params['mobile']   = $mobile;
        $params['password'] = '123456';
        $res                = $this->request($url, 'post', $params);
        $body               = $res->getBody();
        $body               = json_decode($body, true);
        $this->assertEquals('1', $body['code']);
        $cookieUser_id = $this->cookieJar->getCookieByName('cdb4___ewei_shopv2_member_session_2');
        echo $cookieUser_id;
        //$url='http://qy.uzipm.com/app/index.php?i=2&c=entry&m=ewei_shopv2&do=mobile&r=account.login';
        //$this->request($url,'get',[]);
    }

    function testLoginForCode()
    {
        $url              = $this->baseUrl . "/user/loginForCode";
        $mobile           = '13812341232';
        $params           = [];
        $params['mobile'] = $mobile;
        $params['code']   = '123456';
        $res              = $this->request($url, 'post', $params);
        $body             = $res->getBody();
        $body             = json_decode($body, true);
        $this->assertEquals('1', $body['code']);

    }

    function testGetCode()
    {
        $url              = $this->baseUrl . "/user/getCode";
        $mobile           = '13162836361';
        $params           = [];
        $params['mobile'] = $mobile;
        $params['code']   = '123456';
        $res              = $this->request($url, 'post', $params);
        $body             = $res->getBody();
        $body             = json_decode($body, true);
        $this->assertEquals('1', $body['code']);
    }

    function testFindPassword()
    {
        $url                  = $this->baseUrl . "/user/findPassword";
        $mobile               = '13812341232';
        $params               = [];
        $params['mobile']     = $mobile;
        $params['code']       = '123456';
        $params['password']   = '123456';
        $params['repassword'] = '123456';
        $res                  = $this->request($url, 'post', $params);
        $body                 = $res->getBody();
        $body                 = json_decode($body, true);
        $this->assertEquals('1', $body['code']);
    }

}