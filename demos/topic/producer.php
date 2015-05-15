<?php

require_once __DIR__.'/../../vendor/autoload.php';

use znk3r\MQlib\Producer;
use znk3r\MQlib\Exchange\Topic;
use znk3r\MQlib\Message\Outgoing\Text as TextMessage;

// Create producer and change some of the default options from the broker
$producer = new Producer;
$producer->getBroker()->setTimeout(2);

// Declare Topic exchange
// Must be the same exchange as the one used by the producer
$exchange = new Topic('mqlib_topic_exchange');

if ($argc != 3) {
    fwrite(STDERR, 'Invalid argument'.PHP_EOL);
    fwrite(STDERR, 'Use: php producer.php "routing.key" "message"'.PHP_EOL);
    exit(1);
}

// Create a JSON message
$message = new TextMessage($argv[2]);

// Add routing key, if any
if (!empty($argv[1])) {
    $message->setRoutingKey($argv[1]);
}

// Send the message to the exchange created
$producer->publish($message, $exchange);

echo "Sent!\n";
