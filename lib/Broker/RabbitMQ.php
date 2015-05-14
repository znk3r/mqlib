<?php

namespace znk3r\MQlib\Broker;
use znk3r\MQlib\Broker\Connection\Factory;

/**
 * RabbitMQ broker with default configuration.
 *
 * @version   1.0.0
 *
 * @author    Miguel Angel Liebana <mi.liebana@gmail.com>
 * @copyright 2015 Miguel Angel Liebana
 */
class RabbitMQ extends AbstractBroker
{
    /** @var array $defaultOptions Default options for RabbitMQ */
    protected $defaultOptions = array(
        'connectionType' => Factory::CONNECTION_STREAM,
        'channelId' => null,
        'host' => 'localhost',
        'port' => 5672,
        'user' => 'guest',
        'password' => 'guest',
        'vhost' => '/',
        'loginMethod' => 'AMQPLAIN',
        'locale' => 'en_US',
        'timeout' => 5,
        '$readWriteTimeout' => 5,
    );

    /**
     * Initialize a RabbitMQ broker with the default options.
     *
     * {@inheritdoc}
     */
    public function __construct(array $options = array())
    {
        parent::__construct(
            array_merge($this->defaultOptions, $options)
        );
    }
}
