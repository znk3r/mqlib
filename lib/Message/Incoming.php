<?php

namespace znk3r\MQlib\Message;

use PhpAmqpLib\Message\AMQPMessage;
use znk3r\MQlib\Broker\Channel\Channel;

/**
 * Message received from the queue.
 *
 * @version   1.0.0
 *
 * @author    Miguel Angel Liebana <mi.liebana@gmail.com>
 * @copyright 2015 Miguel Angel Liebana
 */
class Incoming extends Message
{
    /** @var string $clusterId */
    protected $clusterId;

    /** @var Channel $channel */
    protected $channel;

    /** @var string $consumerId ID of the consumer script processing the message */
    protected $consumerId;

    /** @var int $deliveryId Server and channel assigned to deliver this tag. Used to ack/no-ack the message */
    protected $deliveryId;

    /** @var int $redelivered Indicates that the message has been previously delivered to this or another client */
    protected $redelivered;

    /** @var string $exchangeName Name of the exchange routing the message */
    protected $exchangeName;

    /** @var string $routingKey Routing key that sent the message to this queue, if any */
    protected $routingKey;

    /**
     * @param AMQPMessage $queueMessage
     */
    public function __construct(AMQPMessage $queueMessage)
    {
        $this->body = $queueMessage->body;

        $this
            ->setPropertiesFromMessage($queueMessage)
            ->setDeliveryInfo($queueMessage);
    }

    /**
     * @param AMQPMessage $queueMessage
     *
     * @return $this
     */
    protected function setPropertiesFromMessage($queueMessage)
    {
        foreach ($this->propertyMapping as $mqlibProperty => $amqpProperty) {
            if ($queueMessage->has($amqpProperty) && property_exists($this, $mqlibProperty)) {
                $this->$mqlibProperty = $amqpProperty;
            }
        }

        return $this;
    }

    /**
     * @param AMQPMessage $queueMessage
     *
     * @return $this
     */
    protected function setDeliveryInfo($queueMessage)
    {
        $deliveryInfo = $queueMessage->delivery_info;

        $this->channel = new Channel($deliveryInfo['channel']);
        $this->consumerId = $deliveryInfo['consumer_tag'];
        $this->deliveryId = $deliveryInfo['delivery_tag'];
        $this->redelivered = $deliveryInfo['redelivered'];
        $this->exchangeName = $deliveryInfo['exchange'];
        $this->routingKey = $deliveryInfo['routing_key'];

        return $this;
    }

    /**
     * Acknowledges the message as received and processed.
     *
     * If the message is persistent (delivery_mode 2), we'll need to notify the broker
     * that it has been processed. The message will persist in the queue until it
     * has been acknowledged.
     *
     * @return $this
     */
    public function acknowledged()
    {
        $this->getChannel()->ack($this->deliveryId);

        return $this;
    }

    /**
     * Rejects a message sending a NACK to the message broker.
     *
     * It can be used to interrupt and cancel larg incoming messages, or return untreatable messages to their
     * original queue. The NACK is also used by the server to inform publishers on channels in confirm mode of
     * unhandled messages. If a publisher receives this signal, it probably needs to republish the offending messages.
     *
     * This shouldn't be used as a mean for selecting messages to process.
     *
     * The server can try to re-queue the message once (if $reQueueAgain=true). If $reQueueAgain is false or the re-queue
     * attempt fails, the message will be discarded or dead-lettered. The server will try to re-queue the message
     * to an alternative consumer if possible.
     *
     * @param bool $reQueueAgain True if the message must return to the queue
     *
     * @return $this
     */
    public function notAcknowledged($reQueueAgain = false)
    {
        $this->getChannel()->nack($this->deliveryId, $reQueueAgain);

        return $this;
    }

    /**
     * Handy method to stop the consumer from inside the callback function.
     *
     * Cancels the consumer and the queue. This does not affect already delivered messages (which will still be
     * processed, and the consumer will stop after that), but the server will not send any more messages for
     * this consumer.
     */
    public function cancelConsumer()
    {
        $this->getChannel()->cancelConsumer($this->consumerId);
    }

    /**
     * @return string
     */
    public function getClusterId()
    {
        return $this->clusterId;
    }

    /**
     * @return Channel
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * @return string
     */
    public function getConsumerId()
    {
        return $this->consumerId;
    }

    /**
     * @return int
     */
    public function getDeliveryId()
    {
        return $this->deliveryId;
    }

    /**
     * @return int
     */
    public function getRedelivered()
    {
        return $this->redelivered;
    }

    /**
     * @return string
     */
    public function getExchangeName()
    {
        return $this->exchangeName;
    }

    /**
     * @return string
     */
    public function getRoutingKey()
    {
        return $this->routingKey;
    }
}
