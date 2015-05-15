<?php

require_once __DIR__.'/../../vendor/autoload.php';

use znk3r\MQlib\Producer;
use znk3r\MQlib\Exchange\Fanout;
use znk3r\MQlib\Message\Outgoing\Json as JsonMessage;

// Create producer and change some of the default options from the broker
$producer = new Producer;
$producer->getBroker()->setTimeout(2);

// Declare fanout exchange
// Must be the same exchange as the one used by the producer
$exchange = new Fanout('mqlib_fanout_exchange');

// Create a JSON message
$message = new JsonMessage(array(
    'a' => rand(),
    'b' => rand(),
    'c' => rand(),
    'd' => rand(),
));

// Send the message to the exchange created
$producer->publish($message, $exchange);

echo "Sent!\n";
