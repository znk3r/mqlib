<?php

require_once('vendor/autoload.php');

use znk3r\MQlib\Producer;
use znk3r\MQlib\Exchange\Fanout;
use znk3r\MQlib\Message\Outgoing\Json as JsonMessage;

$producer = new Producer;
$producer->getBroker()->setTimeout(2);

$exchange = new Fanout('mqlib_ex1');
$message = new JsonMessage(array(
    'a' => rand(),
    'b' => rand(),
    'c' => rand(),
    'd' => rand(),
));

$producer->publish($message, $exchange);

echo "Sent!\n";
