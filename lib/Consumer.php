<?php

namespace znk3r\MQlib;

use znk3r\MQlib\Broker\AbstractBroker;
use znk3r\MQlib\Queue\Queue;

/**
 * Class Consumer.
 *
 * @version   1.0.0
 *
 * @author    Miguel Angel Liebana <mi.liebana@gmail.com>
 * @copyright 2015 Miguel Angel Liebana
 */
class Consumer extends AbstractApplication
{
    /** @var string $consumerName */
    protected $consumerName;

    /**
     * Allows the server to send messages in advance to consumers if they are equal or smaller to the prefetch
     * size in Bytes. Without limit by default.
     *
     * @var int|null
     */
    protected $qosPrefetchSize;

    /**
     * Allows the server to send this number of messages in advance to the consumer. Set to null to remove the limit.
     *
     * @var int|null
     */
    protected $qosPrefetchCount;

    /**
     * Specifies if the qos properties are applied to the connection (global = true) or only to the channel
     * (global = false).
     *
     * @var bool
     */
    protected $qosGlobal;

    /**
     * Create a new consumer and defining its connection with a broker.
     *
     * By default, the consumer will try to use a RabbitMQ broker with the default options. Those defaults
     * can be changed sending an array of options.
     *
     * @param string               $consumerName
     * @param AbstractBroker|array $broker
     *
     * @throws Exception
     */
    public function __construct($consumerName, $broker = array())
    {
        $this->setConsumerName($consumerName);
        parent::__construct($broker);
    }

    public function listen(Queue $queue, $action)
    {
        $channel = $this->getBroker()->getChannel();

        $channel
            ->declareExchange($queue->getExchange())
            ->declareQueue($queue)
            ->setQualityOfService(
                $this->getQosPrefetchSize(),
                $this->getQosPrefetchCount(),
                $this->isQosForConnection()
            );

        $consumerOptions = array(
            'name' => $this->getConsumerName(),
            'noLocal' => false,
            'noAck' => false,
            'exclusive' => false,
            'noWait' => false,
        );

        $channel->consume(
            $queue,
            $consumerOptions,
            $action
        );
    }

    /**
     * @param string $consumerName
     *
     * @return $this
     *
     * @throws Exception
     */
    public function setConsumerName($consumerName)
    {
        if (!is_string($consumerName)) {
            throw new Exception('Invalid consumer name, must be a string.');
        }

        $this->consumerName = $consumerName;

        return $this;
    }

    /**
     * @return string
     */
    public function getConsumerName()
    {
        return $this->consumerName;
    }

    /**
     * @param int|null $bytes
     *
     * @return $this
     *
     * @throws Exception
     */
    public function setQosPrefetchSize($bytes)
    {
        if (null !== $bytes && !is_integer($bytes)) {
            throw new Exception('Invalid number of bytes for the QOS prefetch size, must be an integer or null');
        }

        $this->qosPrefetchSize = $bytes;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getQosPrefetchSize()
    {
        return $this->qosPrefetchSize;
    }

    /**
     * @param int|null $numberOfMessages
     *
     * @return $this
     *
     * @throws Exception
     */
    public function setQosPrefetchCount($numberOfMessages)
    {
        if (null !== $numberOfMessages && !is_integer($numberOfMessages)) {
            throw new Exception('Invalid number of messages to prefetch, must be an integer or null');
        }

        $this->qosPrefetchCount = $numberOfMessages;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getQosPrefetchCount()
    {
        return $this->qosPrefetchCount;
    }

    /**
     * The QoS rules will be applied to all the connection.
     *
     * @return $this
     */
    public function setQosForConnection()
    {
        $this->qosGlobal = true;

        return $this;
    }

    /**
     * The QoS rules will be applied only to the channel.
     *
     * @return $this
     */
    public function setQosForChannel()
    {
        $this->qosGlobal = false;

        return $this;
    }

    /**
     * Are the QoS rules applied by connection or only by channel.
     *
     * @return bool
     */
    public function isQosForConnection()
    {
        return $this->qosGlobal;
    }
}
