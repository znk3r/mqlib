# Direct demo

A direct exchange routes the message to a specific queue or group of queues. Each queue establishes which keys
wants to receive, and each message is created with a specific key. The message will be routed to the queues
wanting to receive that message, or dropped if there are no queues expecting that key.

If the queue doesn't define a key, it'll receive any message without a defined key.

## Demo

### Basic queue

1) Download and install dependencies

    $ git checkout https://github.com/znk3r/mqlib.git
    $ cd mqlib
    $ composer update

2) Run one consumer on a terminal

    $ php demos/direct/consumer.php
    consumer-11894 connected to mqlib_direct_queue-2087850836 and ready!
    
3) Run the producer script on a different terminal to send messages

    $ php demos/direct/producer.php
    Sent!
    
### Basic routing

1) Download and install dependencies

    $ git checkout https://github.com/znk3r/mqlib.git
    $ cd mqlib
    $ composer update

2) Run one consumer on a terminal listening for keys red and green

    $ php demos/direct/consumer.php red green
    consumer-13023 connected to mqlib_direct_queue-1262709333 and ready!
    Waiting for keys: [ red, green ]

3) Run another consumer on a terminal listening for keys red and blue 

    $ php demos/direct/consumer.php red blue
    consumer-13342 connected to mqlib_direct_queue-4283474 and ready!
    Waiting for keys: [ red, blue ]
    
4) Run the producer script several times using different keys

    $ php demos/direct/producer.php red
    Sent!
    $ php demos/direct/producer.php green
    Sent!
    $ php demos/direct/producer.php brown
    Sent!
    $ php demos/direct/producer.php brown
    Sent!
