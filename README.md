接口自动测试工具，依赖下面的库实现的： 
thinkphp5，phpunit，Guzzle  

运行方法 
./think unit 

1.只测试某个文件
./think unit  testGetSubsidy 或 ./think unit  testGetSubsidy.php

2.只测试某个方法
./think unit --filter Subsidy

编写测试代码
```
function testRegister(){
    
    $url    = $this->baseUrl."/user/register";
    $mobile = '1380000' . mt_rand(1000, 9999);

    //register
    $params               = [];
    $params['mobile']     = $mobile;
    $params['code'] = '12345';
    $params['password']   = '123456';
    $params['repassword']   = '1234567';
    $res                  = $this->request($url, 'post', $params);
    $body = $res->getBody();
    $body = json_decode($body,true);

    $this->assertEquals('1',$body['status']);
}

```