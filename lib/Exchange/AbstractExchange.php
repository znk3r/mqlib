<?php
namespace znk3r\MQlib\Exchange;

/**
 * Abstract class Exchange.
 *
 * @version   1.0.0
 *
 * @author    Miguel Angel Liebana <mi.liebana@gmail.com>
 * @copyright 2015 Miguel Angel Liebana
 */
abstract class AbstractExchange
{
    /** @var string $name Name of the exchange */
    protected $name = null;

    /**
     * If true, the server will reply with Declare-Ok if the exchange already exists with the same name, and raise
     * an error if not. This can be used to check if the exchange exists or not, without modifying the server state.
     * When true all other fields except no-wait are ignored.
     * Default is false, where the exchange will be declared if not exists. That's usually the most common case.
     *
     * @var bool $passive
     */
    protected $passive = false;

    /**
     * If set when creating a new exchange, this will be marked as durable and remain active even if the server
     * restarts. Exchanges contain routing information that you may not want to lose in a crash, so it's useful
     * if you also have durable queues and messages.
     * Non-durable exchanges are deleted when the server restarts, and usually re-created when a producer/consumer
     * request that.
     *
     * @note You may need a plugin to delete durable exchanges from RabbitMQ
     * @var bool $durable
     */
    protected $durable = true;

    /**
     * If set, the exchange is deleted when all queues have finished using it.
     * Ignored if the exchange already exists
     *
     * @note Useful to have as true for dev and tests, but false for production
     * @var bool
     */
    protected $autoDelete = false;

    /**
     * If set, the exchange may not be used by publishers, and only when bound to other exchanges.
     * Internal exchanges are used to construct wiring that is not visible to applications.
     *
     * @var bool
     */
    protected $internal = false;

    /**
     * If set, the server will not respond to the method.
     * The client should not wait for a reply method. If the server could not complete the method it will raise
     * a channel or connection exception. This relates to the communication between the script and the server, not
     * between producer and consumer.
     *
     * @var bool
     */
    protected $nowait = false;

    /**
     * An array of optional arguments for the declaration. Each broker has its own arguments.
     *
     * @var array
     */
    protected $arguments = null;

    abstract public function getExchangeType();

    /**
     * @param string $name
     * @return $this
     * @throws InvalidArgumentException
     */
    public function setName($name)
    {
        if (!is_string($name) || empty($name)) {
            throw new InvalidArgumentException('Invalid exchange name '.$name);
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
     * Changes if the exchange will be declared as passive or not.
     *
     * @param  bool $value
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
     * Changes if the exchange will be declared as durable and survive server restarts.
     *
     * @param  bool $value
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
     * Change if the exchange will be declared with auto_delete and be deleted when all the queues end or not.
     *
     * @param  bool $value
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
     * Changes if the exchange will be declared as internal/private and being only available for other exchanges.
     *
     * @param  bool $value
     * @return $this
     */
    public function declareAsInternal($value)
    {
        $this->internal = (bool) $value;
        return $this;
    }

    /**
     * @return bool
     */
    public function isInternal()
    {
        return $this->internal;
    }

    /**
     * Changes if the exchange will be declared as no_wait when declared with the server/broker.
     *
     * @param  bool $value
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
     * Changes the declaration arguments for the exchange
     *
     * @param  array $arguments
     * @return $this
     */
    public function setArguments(array $arguments)
    {
        $this->arguments = $arguments;
        return $this;
    }

    /**
     * Returns the exchange arguments
     *
     * @return array
     */
    public function getArguments()
    {
        return $this->arguments;
    }
}