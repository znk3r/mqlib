# Fanout demo

A fanout exchange, sends a copy of each message received to all the connected queues. Each queue will have to
decide what does it want to do with that message.

## Demo

1) Download and install dependencies

    $ git checkout https://github.com/znk3r/mqlib.git
    $ cd mqlib
    $ composer update

2) Run one or more consumers on different terminals

    $ php demos/fanout/consumer.php
    consumer-11894 connected to mqlib_fanout_queue-2087850836 and ready!
    
3) Run the producer script on a different terminal to send messages

    $ php demos/fanout/producer.php
    Sent!
    
Each consumer will receive a copy of the message