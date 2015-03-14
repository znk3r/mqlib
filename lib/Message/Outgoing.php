<?php
namespace znk3r\MQlib\Message;

/**
 * Message that's going to be send to a queue
 *
 * @version   1.0.0
 *
 * @author    Miguel Angel Liebana <mi.liebana@gmail.com>
 * @copyright 2015 Miguel Angel Liebana
 */
class Outgoing extends Message
{
    /** @var string|null $routingKey Key used to route the message in some exchanges (topic and direct) */
    protected $routingKey;

    /**
     * This flag tells the server how to react if the message cannot be routed to a queue.
     * If true, the server will return an unroutable message with a Return method. a return listener must be
     * declared for the channel using set_return_listener. If false, the server silently drops the message.
     *
     * @var bool $mandatory
     */
    protected $mandatory = false;

    /**
     * This flag tells the server how to react if the message cannot be routed to a queue consumer immediately.
     * If true, the server will return an undeliverable message with a Return method.
     * If false, the server will queue the message but with no guarantee that it will ever be consumed.
     * With a false, the message will continue on the server until there's a queue to consume it, instead of
     * returning an error.
     *
     * @var bool $immediate
     */
    protected $immediate = false;

    /**
     * Function to be executed if there's a return message from the server.
     * Used when immediate or mandatory flags are set to true.
     * These are the parameters passed to the function:
     *
     * - param int         $reply_code
     * - param string      $reply_text
     * - param string      $exchange
     * - param string      $routing_key
     * - param AMQPMessage $msg
     *
     * @var callable $returnListener
     */
    protected $returnListener = null;

    /**
     * Set message routing key for topic and direct exchanges
     *
     * @param string
     * @return $this
     */
    public function setRoutingKey($key)
    {
        $this->routingKey = $key;
        return $this;
    }

    /**
     * Get message routing key for topic and direct exchanges
     *
     * @return string
     */
    public function getRoutingKey()
    {
        return $this->routingKey;
    }

    /**
     * Specifies the function to call for return messages from the server.
     * Needed for Mandatory and Immediate messages
     *
     * @param callable $listener
     * @return $this
     */
    public function setReturnListener($listener)
    {
        $this->returnListener = $listener;
        return $this;
    }

    /**
     * Get the return listener
     *
     * @return callable
     */
    public function getReturnListener()
    {
        return $this->returnListener;
    }

    /**
     * A return listener has been defined?
     *
     * @return bool
     */
    public function hasReturnListener()
    {
        return is_callable($this->returnListener);
    }

    /**
     * Set if the message is going to be mandatory
     *
     * @param  bool $mandatory
     * @param  callable $listener Optional return listener
     * @return $this
     */
    public function setMandatory($mandatory, $listener = null)
    {
        $this->mandatory = (bool) $mandatory;

        if ($listener) {
            $this->setReturnListener($listener);
        }
        return $this;
    }

    /**
     * Get if the message is mandatory
     *
     * @throws MessageException
     * @return bool
     */
    public function isMandatory()
    {
        if ($this->mandatory && !$this->hasReturnListener()) {
            throw new MessageException('Mandatory flag cannot be used without declaring a return listener.');
        }

        return $this->mandatory;
    }

    /**
     * Set if the message is immediate
     *
     * @param  bool $immediate
     * @param  callable $listener Optional return listener
     * @return $this
     */
    public function setImmediate($immediate, $listener = null)
    {
        $this->immediate = (bool) $immediate;

        if ($listener) {
            $this->setReturnListener($listener);
        }
        return $this;
    }

    /**
     * Get if the message is immediate
     *
     * @throws MessageException
     * @return bool
     */
    public function isImmediate()
    {
        if ($this->immediate && !$this->hasReturnListener()) {
            throw new MessageException('Immediate flag cannot be used without declaring a return listener.');
        }

        return $this->immediate;
    }
}