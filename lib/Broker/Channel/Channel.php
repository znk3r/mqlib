<?php

namespace znk3r\MQlib\Broker\Channel;
use PhpAmqpLib\Channel\AMQPChannel;
use znk3r\MQlib\Exchange\AbstractExchange;
use znk3r\MQlib\Message\Outgoing;

/**
 * Channel abstraction class
 *
 * @version   1.0.0
 *
 * @author    Miguel Angel Liebana <mi.liebana@gmail.com>
 * @copyright 2015 Miguel Angel Liebana
 */
class Channel
{
    /** @var AMQPChannel $channel */
    protected $channel;

    /**
     * @param AMQPChannel $channel
     */
    public function __construct(AMQPChannel $channel)
    {
        $this->channel = $channel;
    }

    /**
     * @param AbstractExchange $exchange
     * @return $this
     */
    public function declareExchange(AbstractExchange $exchange)
    {
        $this->channel->exchange_declare(
            $exchange->getName(),
            $exchange->getExchangeType(),
            $exchange->isPassive(),
            $exchange->isDurable(),
            $exchange->isDeclaredAutoDelete(),
            $exchange->isInternal(),
            $exchange->isDeclaredAsNoWait(),
            $exchange->getArguments()
        );

        return $this;
    }

    /**
     * @param Outgoing         $message
     * @param AbstractExchange $exchange
     * @return $this
     */
    public function sendMessage(Outgoing $message, AbstractExchange $exchange)
    {
        if ($message->hasReturnListener()) {
            $this->channel->set_return_listener($message->getReturnListener());
        }

        $this->channel->basic_publish(
            $message->getBody(),
            $exchange->getName(),
            $message->getRoutingKey(),
            $message->isMandatory(),
            $message->isImmediate()
        );

        return $this;
    }
}