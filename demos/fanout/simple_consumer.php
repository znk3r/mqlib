<?php

require_once('vendor/autoload.php');

use znk3r\MQlib\Consumer;
use znk3r\MQlib\Queue\Queue;
use znk3r\MQlib\Exchange\Fanout;

$exchange = new Fanout('mqlib_ex1');

$queue = new Queue('mqlib_queue1');
$queue->bindTo($exchange);

$consumer = new Consumer('my_consumer');
$consumer->listen($queue, function($msg) {
    echo $msg->getBody().PHP_EOL;
    $msg->acknowledged();
});
