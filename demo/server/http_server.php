<?php

$http = new Swoole\Http\Server("0.0.0.0", 8811);

$http->set(
    [
        'enable_static_handler' => true,
        'document_root' => "/Users/leeler/work/project/study/swoole/data",
    ]
);
$http->on('request', function($request, $response) {
    //print_r($request->get);
    $content = [
        'date:' => date("Ymd H:i:s"),
        'get:' => $request->get,
        'post:' => $request->post,
        'header:' => $request->header,
    ];

    //swoole_async_writefile("/tmp/access.log", json_encode($content).PHP_EOL, function($filename){
        // todo
    //}, FILE_APPEND);
    $response->cookie("singwa", "xsssss", time() + 1800);
    $response->end("httpserver_return:". json_encode($content));
});

$http->start();