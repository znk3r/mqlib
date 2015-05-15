<?php

require_once __DIR__.'/../../vendor/autoload.php';

use znk3r\MQlib\Consumer;
use znk3r\MQlib\Queue\Queue;
use znk3r\MQlib\Exchange\Direct;

$exchangeName = 'mqlib_direct_exchange';
$queueName = 'mqlib_direct_queue-'.rand();
$consumerName = 'consumer-'.getmypid();

// Declare direct exchange
// Must be the same exchange as the one used by the producer
$exchange = new Direct($exchangeName);

// Create queue and bind it to the exchange
$queue = new Queue($queueName);
$queue->bindTo($exchange);

// Add routing keys
$routingKeys = $argv;
array_shift($routingKeys);
$queue->setRoutingKeys($routingKeys);

// Setup this consumer
$consumer = new Consumer($consumerName);

echo "$consumerName connected to $queueName and ready!".PHP_EOL;
if (!empty($routingKeys)) {
    echo 'Waiting for keys: [ '.implode(', ', $routingKeys).' ]'.PHP_EOL;
}

// Bind this consumer to the queue and define "what" code must be run for each message
// received from the queue
$consumer->listen($queue, function($msg) {
    /** @var znk3r\MQlib\Message\Incoming $msg */
    try {
        // $msg is a Message\Incoming object
        echo $msg->getBody().PHP_EOL;

        // Ack the message as processed to the broker
        $msg->acknowledged();
    } catch (Exception $e) {
        // nack the message if there's a problem
        $msg->notAcknowledged();
    }
});

