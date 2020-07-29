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


class DemandTest extends BaseCase
{


    function testSave()
    {
        //$this->sendRequestByRaw(); //post原生发送，返回结果会包含一些符号，返回5b\n{"status":0,"result":{"message":"\u60a8\u8f93\u5165\u7684\u8d26\u53f7\u5df2\u5b58\u5728~"}}\n0
        $url    = $this->baseUrl . "/demand/save";
        $mobile = '1380000' . mt_rand(1000, 9999);

        $test1 = fopen(__DIR__ . '/assert/test1.png', 'r');
        $test2 = fopen(__DIR__ . '/assert/test2.png', 'r');
        /*
                //register

                $params['website_img'] = $mobile;
                $params['code']        = '12345';
                $params['password']    = '123456';
                $params['repassword']  = '1234567';
                $res                   = $this->request($url, 'post', $params);
                $body                  = $res->getBody();
                $body                  = json_decode($body, true);

                $this->assertEquals('1', $body['code']);*/


        $params      = [];
        $multipart   = [];
        $multipart[] = ['name' => 'catgory', 'contents' => '1'];
        $multipart[] = ['name' => 'supplementary1', 'contents' => '1'];
        $multipart[] = ['name' => 'supplementary2', 'contents' => '1'];
        $multipart[] = ['name' => 'title', 'contents' => '1'];
        $multipart[] = ['name' => 'intro', 'contents' => '1'];
        $multipart[] = ['name' => 'website', 'contents' => '1'];
        $multipart[] = ['name' => 'website_intro', 'contents' => '1'];
        $multipart[] = ['name' => 'website_img[]', 'contents' => $test1];
        $multipart[] = ['name' => 'website_img[]', 'contents' => $test2];

        $params['multipart'] = $multipart;
        /*[
            'multipart' => [
                [
                    'name'     => 'field_name',
                    'contents' => 'abc'
                ],
                [
                    'name'     => 'website_img[]',
                    'contents' => $test1
                ],
                [
                    'name'     => 'website_img[]',
                    'contents' => $test2
                ],
            ]
        ]
    ]*/

        $res = $this->client->request('POST', $url, $params);

        $body = $res->getBody();
        $body = json_decode($body, true);
        $this->assertEquals('1', $body['code']);

    }


}