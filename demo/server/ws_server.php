<?php

$server = new Swoole\WebSocket\Server("0.0.0.0", 8812);
//$server->set([]);
$server->set(
    [
        'enable_static_handler' => true,
        'document_root' => "/Users/leeler/work/project/study/swoole/data",
    ]
);
//监听websocket连接打开事件
$server->on('open', 'onOpen');
function onOpen($server, $request) {
    $msg = $request->fd .'-socket is open';
    echo $msg;
    $server->push($request->fd, $msg);
}

// 监听ws消息事件
$server->on('message', function (Swoole\WebSocket\Server $server, $frame) {
    echo "receive from {$frame->fd}:{$frame->data},opcode:{$frame->opcode},fin:{$frame->finish}\n";
    $server->push($frame->fd, "leeler-push-secesss");
});

$server->on('close', function ($ser, $fd) {
    echo "test client {$fd} closed\n";
});

$server->start();