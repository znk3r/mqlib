<?php

require_once __DIR__.'/../../vendor/autoload.php';

use znk3r\MQlib\Producer;
use znk3r\MQlib\Exchange\Direct;
use znk3r\MQlib\Message\Outgoing\Json as JsonMessage;

// Create producer and change some of the default options from the broker
$producer = new Producer;
$producer->getBroker()->setTimeout(2);

// Declare Direct exchange
// Must be the same exchange as the one used by the producer
$exchange = new Direct('mqlib_direct_exchange');

// Create a JSON message
$message = new JsonMessage(array(
    'a' => rand(),
    'b' => rand(),
    'c' => rand(),
    'd' => rand(),
));

// Add routing key, if any
if (!empty($argv[1])) {
    $message->setRoutingKey($argv[1]);
}

// Send the message to the exchange created
$producer->publish($message, $exchange);

echo "Sent!\n";
