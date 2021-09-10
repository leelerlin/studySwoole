<?php

// 连接 swoole udp 服务
$client = new Swoole\Client(SWOOLE_SOCK_UDP);

$host = '127.0.0.1';
$port = 5201;
if(!$client->connect($host, $port)) {
    echo "连接失败";
    exit;
}

/// php cli常量
fwrite(STDOUT, "请输入消息:");
$msg = trim(fgets(STDIN));

// 发送消息给 tcp server服务器
$client->send($msg);

// 接受来自server 的数据
$result = $client->recv();
echo $result;
