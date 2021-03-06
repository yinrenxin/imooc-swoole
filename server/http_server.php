<?php
/**
 * swoole http 服务器
 */

$http = new swoole_http_server("0.0.0.0", 9505);
//设置的一个静态页面
$http->set(
    [
        'enable_static_handler' => true,
        'document_root' => "/Users/joe/Documents/study/php/swoole/thinkphp/public/static/live",
        'worker_num' => 2,
    ]
);

$http->on('WorkerStart',function(swoole_server $server,$worker_id){

    define('APP_PATH', __DIR__ . '/../application/');
    // 这里 引入 base.php  而不引入start.php  是因为
    // start.php 的话 就会执行thinkphp 的相应的控制器方法了
    require __DIR__ . '/../thinkphp/base.php';
});

$http->on('request', function ($request, $response) use($http){

    $_SERVER = [];
    if(isset($request->server)){
        foreach($request->server as $k => $v){
            $_SERVER[strtoupper($k)] = $v;
        }
    }

    if(isset($request->server)){
        foreach($request->server as $k => $v){
            $_SERVER[strtoupper($k)] = $v;
        }
    }

    $_GET = [];
    if(isset($request->get)){
        foreach ($request->get as $k => $v){
            $_GET[$k] = $v;
        }
    }

    $_POST = [];
    if(isset($request->post)){
        foreach ($request->post as $k => $v){
            $_POST[$k] = $v;
        }
    }
    ob_start();
    try {
        think\Container::get('app', [defined('APP_PATH') ? APP_PATH : ''])
            ->run()
            ->send();
    }catch(\Exception $e){
        print_r($e);
    }

    $res = ob_get_contents();
    ob_end_clean();
    $response->end($res);
});
$http->start();
