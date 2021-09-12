<?php

class Ws {

    CONST HOST = "0.0.0.0";
    CONST PORT = 8812;

    public $ws = null;
    public function __construct() {
        $this->ws = new swoole_websocket_server(SELF::HOST, SELF::PORT);

        $this->ws->set(
            [
                'worker_num' => 2,
                'task_worker_num' => 2,
            ]
        );
        $this->ws->on("open", [$this, 'onOpen']);
        $this->ws->on("message", [$this, 'onMessage']);
        $this->ws->on("task", [$this, 'onTask']);
        $this->ws->on("finish", [$this, 'onFinish']);
        $this->ws->on("close", [$this, 'onClose']);

        $this->ws->start();
    }

    /**
     * 监听ws连接事件
     * @param $ws
     * @param $request
     */
    public function onOpen($ws, $request) {
        var_dump($request->fd);
        if($request->fd == 1) {
            // 每2秒执行- 定时器
            swoole_timer_tick(2000, function($timer_id){
                //echo "2s: timerId:{$timer_id}\n";
            });
        }
    }

    /**
     * 监听ws消息事件
     * @param $ws
     * @param $frame
     */
    public function onMessage($ws, $frame) {
        echo "'server:'onmessage:{$frame->data}\n";

        // 耗时场景 20s
        $data = [
            'task' => 1,
            'fd' => $frame->fd,
        ];
        $ws->task($data);

        // 5秒后执行-延时
        swoole_timer_after(5000, function() use($ws, $frame) {
            echo "5s-after\n";
            $ws->push($frame->fd, "server-time-after:");
        });
        $ws->push($frame->fd, "wsserver-date:".date("Y-m-d H:i:s"));
    }

    /**
     * @param $serv socket对象
     * @param $taskId 任务ID
     * @param $workerId worker进程
     * @param $data
     */
    public function onTask($serv, $taskId, $workerId, $data) {
        print_r($data);
        // 耗时场景 20s
        sleep(20);
        return 'taksId='.$taskId.'_wokrderId='. $workerId.' on task finish'; // 告诉worker,onfinish会接受这个值
    }

    /**
     * @param $serv
     * @param $taskId
     * @param $data
     */
    public function onFinish($serv, $taskId, $data) {
        echo "taskId:{$taskId}\n";
        echo "finish-data-sucess:{$data}\n";
    }

    /**
     * close
     * @param $ws
     * @param $fd
     */
    public function onClose($ws, $fd) {
        echo "clientid:{$fd}\n";
    }
}

$obj = new Ws();