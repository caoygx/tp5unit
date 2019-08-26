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

class UserTest extends BaseCase
{



    function testRegister(){
        $this->sendRequestByRaw();
    }


    /*function register(){
        $mobile = '1380000'.mt_rand(1000,9999);
        //发验证码
        $rDoc = db('doc',$this->docDbConfig)->where(['url'=>'/index/Member/getVcode'])->find();
        $url = $rDoc['url'];
        $parameter = $rDoc['param_json'];
        $arrParameter = json_decode($parameter,1);
        $arrParameter['phone'] = $mobile;
        $response = $this->request($url,$rDoc['method'],$arrParameter);
        //$resultLogin = jsonp_to_json($response->getBody());
        //$resultLogin= json_decode($resultLogin,1);
        //sleep(3);
        //echo $response->getBody();
        //var_dump(json_decode($response->getBody()));exit;


        $code =  $this->getRegisterCode($mobile);
        if(empty($code)) {
            exit( '验证码为空');
        }
        //登录
        $rDoc = db('doc',$this->docDbConfig)->where(['url'=>'/index/Member/Register'])->find();
        $url = $rDoc['url'];
        $parameter = $rDoc['param_json'];
        $arrParameter = json_decode($parameter,1);
        $arrParameter['phone'] = $mobile;
        $arrParameter['verifyCode'] = $code;

        $response = $this->request($url,$rDoc['method'],$arrParameter);
        $resultApi = jsonp_to_json($response->getBody());
        $resultApi= json_decode($resultApi,1);
        $this->assertNotEmpty($resultApi['openid']);

//var_dump(json_decode($rDoc['return_json']);
        check_recursive(json_decode($rDoc['return_json'],1), $resultApi);


        return $resultApi;



    }*/



}