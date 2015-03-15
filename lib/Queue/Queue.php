<?php

namespace znk3r\MQlib\Queue;
use znk3r\MQlib\Exchange\AbstractExchange;

/**
 * Queue class.
 *
 * @version   1.0.0
 *
 * @author    Miguel Angel Liebana <mi.liebana@gmail.com>
 * @copyright 2015 Miguel Angel Liebana
 */
class Queue
{
    /** @var string $name Name of the queue */
    protected $name;

    /** @var AbstractExchange $exchange */
    protected $exchange;

    /**
     * If set the server will reply with Declare-Ok if the queue already exists with the same name, and raise an
     * error if not. This can be used to check if the queue exists or not, without modifying the server state.
     * When true all other fields except name and no-wait are ignored. If false and the queue exists, the server
     * checks if the existing queue has the same values for durable, exclusive, auto-delete and arguments and raises
     * a channel exception if not.
     * Default is false, where the exchange will be declared if not exists. That's usually the most common case.
     *
     * @var bool
     */
    protected $passive = false;

    /**
     * If set when creating a new queue, this will be marked as durable and remain active even if the server restarts.
     * Non-durable queues are purgued when the server restarts, and usually re-created when a publisher/consumer
     * request that.
     *
     * It's advisable to use durable queues on production and non-durable on dev environments.
     *
     * @note Durable queues do not necessarily hold persistent messages
     *
     * @var bool
     */
    protected $durable = false;

    /**
     * If set the queue is marked as exclusive and can only be accessed by the current connection.
     * It'll also be deleted when the connection ends.
     *
     * @var bool
     */
    protected $exclusive = false;

    /**
     * If set, the queue is deleted when all consumers have finished using it.
     * The last consumer can be cancelled either explicitly or because its channel is closed.
     * If there was no consumer ever on the queue, it won't be deleted.
     *
     * @var bool
     */
    protected $autoDelete = false;

    /**
     * If set, the server will not respond to the method. The client should not wait for a reply method.
     * If the server could not complete the method it will raise a channel or connection exception.
     * This relates to the communication between the script and the server, not between publisher and consumer.
     *
     * @var bool
     */
    protected $nowait = false;

    /**
     * An array of optional arguments for the declaration. Each broker has its own arguments. Examples:.
     *
     *  @see http://www.rabbitmq.com/dlx.html
     *  "x-dead-letter-exchange" => array("S", "t_test1")
     *  @see https://www.rabbitmq.com/ttl.html
     *  "x-message-ttl" => array("I", 15000)
     *  "x-expires" => array("I", 16000)
     *  @see https://www.rabbitmq.com/maxlength.html
     *  "x-max-length" => array("I", 10)
     *
     * @var array
     */
    protected $arguments;

    /**
     * Used as routing key while binding with an exchange. Many exchanges send messages to queues matching a
     * routing condition. Multiple keys will require multiple binding between the same queue and exchange.
     *
     * @example http://www.rabbitmq.com/tutorials/tutorial-four-php.html
     *
     * @var array
     */
    protected $routingKeys = array();

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        $this->setName($name);
    }

    /**
     * @param string $name
     *
     * @return $this
     *
     * @throws InvalidArgumentException
     */
    public function setName($name)
    {
        if (!is_string($name) || empty($name)) {
            throw new InvalidArgumentException('Invalid queue name '.$name);
        }

        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Bind the queue to a specific exchange.
     *
     * The queue will listen and receive messages from this exchange. Producer and consumer should share
     * the same exchange configuration.
     *
     * @param AbstractExchange $exchange
     * @return $this
     */
    public function bindTo(AbstractExchange $exchange)
    {
        $this->exchange = $exchange;

        return $this;
    }

    /**
     * Changes if the queue will be declared as passive or not.
     *
     * @param bool $value
     *
     * @return $this
     */
    public function declareAsPassive($value)
    {
        $this->passive = (bool) $value;

        return $this;
    }

    /**
     * @return bool
     */
    public function isPassive()
    {
        return $this->passive;
    }

    /**
     * Changes if the queue will be declared as durable and survive server restarts.
     *
     * @param bool $value
     *
     * @return $this
     */
    public function declareAsDurable($value)
    {
        $this->durable = (bool) $value;

        return $this;
    }

    /**
     * @return bool
     */
    public function isDurable()
    {
        return $this->durable;
    }

    /**
     * Changes if the queue will be declared as exclusive for this connection.
     *
     * @param bool $value
     *
     * @return $this
     */
    public function declareAsExclusive($value)
    {
        $this->exclusive = (bool) $value;

        return $this;
    }

    /**
     * @return bool
     */
    public function isExclusive()
    {
        return $this->exclusive;
    }

    /**
     * Change if the queue will be declared with auto delete and be deleted when all the consumers end or not.
     *
     * @param bool $value
     *
     * @return $this
     */
    public function declareAsAutoDelete($value)
    {
        $this->autoDelete = (bool) $value;

        return $this;
    }

    /**
     * @return bool
     */
    public function isDeclaredAutoDelete()
    {
        return $this->autoDelete;
    }

    /**
     * Changes if the queue will be declared as no_wait when declared with the server/broker.
     *
     * @param bool $value
     *
     * @return $this
     */
    public function declareAsNoWait($value)
    {
        $this->nowait = (bool) $value;

        return $this;
    }

    /**
     * @return bool
     */
    public function isDeclaredAsNoWait()
    {
        return $this->nowait;
    }

    /**
     * Changes the declaration arguments for the queue.
     *
     * @param array $arguments
     *
     * @return $this
     */
    public function setArguments(array $arguments)
    {
        $this->arguments = $arguments;

        return $this;
    }

    /**
     * Returns the exchange arguments.
     *
     * @return array
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * Set the routing keys configured for the queue.
     *
     * @param array $keys
     *
     * @return $this
     */
    public function setRoutingKeys(array $keys)
    {
        $this->routingKeys = $keys;

        return $this;
    }

    /**
     * Returns the routing keys for the queue.
     *
     * @return array
     */
    public function getRoutingKeys()
    {
        return empty($this->routingKeys)
            ? array(null)
            : $this->routingKeys;
    }

    /**
     * @param string $key
     *
     * @return $this
     */
    public function addRoutingKey($key)
    {
        $this->routingKeys[] = $key;

        return $this;
    }
}
