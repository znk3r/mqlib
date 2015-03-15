<?php

namespace znk3r\MQlib\Broker\Channel;

use PhpAmqpLib\Channel\AMQPChannel;
use znk3r\MQlib\Broker\BrokerException;
use znk3r\MQlib\Exchange\AbstractExchange;
use znk3r\MQlib\Message\Incoming;
use znk3r\MQlib\Message\Outgoing;
use znk3r\MQlib\Queue\Queue;

/**
 * Channel abstraction class.
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
     * Declare a exchange in the message broker.
     *
     * If the exchange is already declared, the configuration MUST match. If the exchange has not been
     * defined before, it'll be created.
     *
     * @param AbstractExchange $exchange
     *
     * @return $this
     *
     * @throws BrokerException
     */
    public function declareExchange($exchange)
    {
        if (!$exchange instanceof AbstractExchange) {
            throw new BrokerException("The exchange hasn't been defined");
        }

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
     * Define a queue in the message broker and bind it to an exchange.
     *
     * If the queue is already declared, the configuration MUST be the same. If the queue has not been
     * defined before, a new one will be created.
     *
     * @param $queue
     *
     * @return $this
     *
     * @throws BrokerException
     */
    public function declareQueue($queue)
    {
        if (!$queue instanceof Queue) {
            throw new BrokerException("The queue hasn't been defined");
        }

        $queueConf = $this->channel->queue_declare(
            $queue->getName(),
            $queue->isPassive(),
            $queue->isDurable(),
            $queue->isExclusive(),
            $queue->isDeclaredAutoDelete(),
            $queue->isDeclaredAsNoWait(),
            $queue->getArguments()
        );

        // For unnamed queues, set the random name assigned during the declaration
        if (!$queue->hasName()) {
            $queue->setName($queueConf[0]);
        }

        $routingKeys = $queue->getRoutingKeys();

        // getRoutingKeys() must have at least 1 element, which may be null
        if (count($routingKeys) < 1) {
            throw new BrokerException('Assert: there must be at least 1 routing key');
        }

        // This element will be used to bind the queue and the exchange together.
        foreach ($queue->getRoutingKeys() as $key) {
            $this->channel->queue_bind(
                $queue->getName(),
                $queue->getExchange()->getName(),
                $key, // key can be null
                $queue->isDeclaredAsNoWait()
            );
        }

        return $this;
    }

    /**
     * @param Outgoing         $message
     * @param AbstractExchange $exchange
     *
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

    public function consume(Queue $queue, $consumerOptions, $action)
    {
        $this->channel->basic_consume(
            $queue->getName(),
            $consumerOptions['name'],
            $consumerOptions['noLocal'],
            $consumerOptions['noAck'],
            $consumerOptions['exclusive'],
            $consumerOptions['noWait'],
            function ($queueMessage) use ($action) {
                $action(new Incoming($queueMessage));
            }
        );

        // Listen to the socket/stream
        while (count($this->channel->callbacks)) {
            $this->channel->wait();
        }
    }

    /**
     * Allows to specify basic quality of service for the channel or connection.
     *
     * The queue sends messages in advance to the consumer, so that when the client finishes processing a
     * message, the following message is already local, rather than needing to be sent down the channel.
     *
     * Pre-fetching gives a performance improvement, so this method allows to select the prefetch window in
     * bytes or select a maximum number of messages.
     *
     * If prefetchSize is set, the server will send a message in advance if it is equal to or smaller in size
     * than the available prefetch size. By default, there's no limit.
     *
     * If isGlobal is set to true, the QoS settings will be for all the channels using the connection, instead
     * of only to the channel setting the QoS rules.
     *
     * By default, the message broker pushes all the queue's messages to the clients as fast as the network and
     * the clients allow. The consumers will balloon in memory as they buffer all the messages in their own RAM.
     * The queue may appear empty if you ask the broker, but there may be millions of messages unacknowledged as
     * they sit in the consumers, ready for processing by the client application. Prefetch messages will not be
     * sent to new consumers connecting to the queue.
     *
     * @see  http://www.rabbitmq.com/blog/2012/05/11/some-queuing-theory-throughput-latency-and-bandwidth/
     * @note This is only relevant with messages needing ack. If no-ack is set, QoS will be ignored.
     *
     * @param int  $prefetchSize  in bytes
     * @param int  $prefetchCount as number of messages
     * @param bool $isGlobal
     *
     * @return $this
     */
    public function setQualityOfService($prefetchSize, $prefetchCount, $isGlobal)
    {
        $this->channel->basic_qos(
            $prefetchSize,
            $prefetchCount,
            $isGlobal
        );

        return $this;
    }

    /**
     * Acknowledges the message as received and processed.
     *
     * If the message is persistent (delivery_mode 2), we'll need to notify the broker
     * that it has been processed. The message will persist in the queue until it
     * has been acknowledged.
     *
     * @param int $deliveryId
     *
     * @return $this
     */
    public function ack($deliveryId)
    {
        $this->channel->basic_ack($deliveryId);

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
     * @param int  $deliveryId
     * @param bool $reQueueAgain True if the message must return to the queue
     *
     * @return $this
     */
    public function nack($deliveryId, $reQueueAgain = false)
    {
        $this->channel->basic_nack($deliveryId, false, $reQueueAgain);

        return $this;
    }

    /**
     * Cancel a specific consumer.
     *
     * @param string $consumerId
     *
     * @return $this
     */
    public function cancelConsumer($consumerId)
    {
        $this->channel->basic_cancel($consumerId);

        return $this;
    }
}
