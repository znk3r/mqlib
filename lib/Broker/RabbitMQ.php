<?php

namespace znk3r\MQlib\Broker;

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
        'host' => 'localhost',
        'port' => 5672,
        'user' => 'guest',
        'pass' => 'guest',
        'vhost' => '/',
        'login_method' => 'AMQPLAIN',
        'locale' => 'en_US',
        'timeout' => 5,
        'channel_id' => null,
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
