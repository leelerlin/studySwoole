<?php

$host = '127.0.0.1';
$port = 5201;
$server = new Swoole\Server($host, $port, SWOOLE_PROCESS, SWOOLE_SOCK_UDP);

$server->set([
    'worker_num' => 1 , // worker进程数 cpu 1-4
    'max_request' => 10000,
]);

$server->on('Packet', function ($server, $data, $clientInfo) {
    var_dump($clientInfo);
    $server->sendto($clientInfo['address'], $clientInfo['port'], "Server：{$data}");
});

$server->start();