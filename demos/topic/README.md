# Topic demo

A topic exchange uses a list of words, delimited by dots, as routing key. The producer must use a specific key,
but the consumer can use some special characters to match multiple cases.

- A * (star) can substitute for exactly one word.
- A # (hash) can substitute for zero or more words.

## Demo

1) Download and install dependencies

    $ git checkout https://github.com/znk3r/mqlib.git
    $ cd mqlib
    $ composer update

2) Run some consumers on different terminals listening for different combinations of keys

    $ php demos/topic/consumer.php "#"
    consumer-13023 connected to mqlib_direct_queue-1262709333 and ready!
    Waiting for keys: [ # ]
    
    $ php demos/topic/consumer.php "*.orange.*"
    consumer-13023 connected to mqlib_direct_queue-1262709333 and ready!
    Waiting for keys: [ "*.orange.*" ]
    
    $ php demos/topic/consumer.php "*.*.rabbit"
    consumer-13023 connected to mqlib_direct_queue-1262709333 and ready!
    Waiting for keys: [ "*.*.rabbit" ]

    $ php demos/topic/consumer.php "lazy.#" "sleepy.#"
    consumer-13023 connected to mqlib_direct_queue-1262709333 and ready!
    Waiting for keys: [ "lazy.#", "sleepy.#" ]

3) Run the producer script several times using different keys and see the results

    $ php demos/topic/producer.php "quick.orange.rabbit" "Quick orange rabbit." 
    Sent!
    
    $ php demos/topic/producer.php "lazy.orange.elephant" "Lazy orange elephant." 
    Sent!
    
    $ php demos/topic/producer.php "quick.orange.fox" "Quick orange fox." 
    Sent!
    
    $ php demos/topic/producer.php "quick.pink.fox" "Quick pink fox." 
    Sent!
