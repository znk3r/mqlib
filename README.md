# MQlib

MQlib is an AMQP abstraction library over php-amqplib for PHP (5.3+).

php-amqplib is a great library, but really painful to use because of it's bad interface. Every time I had to remember the meaning of each param from exchange_declare() or queue_declare(). This library tries to address that issue provinding an easier interface to quickly develop producers and consumers.

## Installation ##

Via [composer.json](http://getcomposer.org/doc/01-basic-usage.md#composer-json-project-setup)

    "require": {
        "znk3r/mqlib": "dev-master"
    }


## Basic Usage ##

### Producer ###

#### Basic producer ####

    <?php
    
    use znk3r\MQlib\Producer;
    use znk3r\MQlib\Exchange\Fanout;
    use znk3r\MQlib\Message\Json as JsonMessage;
    
    $producer = new Producer;
    $producer->getBroker()->setTimeout(2); // seconds
    
    $exchange = new Fanout('my_exchange');
    $message = new JsonMessage($data);
    
    $producer->publish($message, $exchange);

#### Multi-Message ####

    <?php
    
    use znk3r\MQlib\Producer;
    use znk3r\MQlib\Exchange\Fanout;
    use znk3r\MQlib\Message\Text as TextMessage;
    
    $producer = new Producer;
    $producer->connect();
    $producer->openChannel();
    
    $producer->sendTo(new Fanout('my_exchange'));
    
    foreach ($i = 0; $i < 100; $i++) {
        $producer->publish(new TextMessage($i));
    }
    
    $producer->closeChannel();
    $producer->disconnect();

### Consumer ###

#### Fanout Consumer ####

    <?php
    
    use znk3r\MQlib\Consumer;
    use znk3r\MQlib\Queue\Queue;
    use znk3r\MQlib\Exchange\Fanout;
    
    $exchange = new Fanout('my_exchange');
    
    $queue = new Queue('my_queue);
    $queue->bindTo($exchange);
    
    $consumer = new Consumer('consumer_name');
    $consumer->listen($queue, function($msg) {
        echo $msg->getBody().PHP_EOL;
        $msg->acknowledged();
    });
