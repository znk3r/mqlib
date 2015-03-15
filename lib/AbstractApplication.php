<?php
namespace znk3r\MQlib;
use PhpAmqpLib\Connection\AbstractConnection;
use znk3r\MQlib\Broker\AbstractBroker;
use znk3r\MQlib\Broker\RabbitMQ;

/**
 * Class AbstractApplication.
 *
 * @version   1.0.0
 *
 * @author    Miguel Angel Liebana <mi.liebana@gmail.com>
 * @copyright 2015 Miguel Angel Liebana
 */
abstract class AbstractApplication
{
    /** @var AbstractBroker $broker Broker configuration class */
    protected $broker;

    /**
     * Create a new application and defines its connection with a broker
     *
     * By default, the application will use a RabbitMQ broker with the default options. Those defaults
     * can be changed sending an array of options.
     *
     * @param AbstractBroker|array $broker
     * @throws Exception
     */
    public function __construct($broker = array())
    {
        if ($broker instanceof AbstractBroker) {
            $this->broker = $broker;
        } elseif (is_array($broker)) {
            $this->broker = new RabbitMQ($broker);
        } else {
            throw new Exception('The producer needs a valid broker or a config array.');
        }
    }

    /**
     * @return AbstractBroker
     */
    public function getBroker()
    {
        return $this->broker;
    }

    /**
     * Opens the connection with the broker
     *
     * You can specify the type of connection to create, or use the default one (StreamIO)
     * The method also accepts an optional AbstractConnection object to use, instead of creating a new one.
     *
     * @param string|null $type Connection type (see Broker\Connection\Factory)
     * @param AbstractConnection|null $connection
     *
     * @return $this
     */
    public function connect($type = null, $connection = null)
    {
        if (null !== $type) {
            $this->broker->setConnectionType($type);
        }

        $this->broker->connect($connection);

        return $this;
    }

    /**
     * @return $this
     */
    public function disconnect()
    {
        $this->broker->disconnect();

        return $this;
    }

    /**
     * Open a channel
     *
     * You can send a channel id as parameter, or null to pick a random one, or use the default option from
     * the broker with false.
     *
     * @param int|null|bool $channelId
     *
     * @return $this
     */
    public function openChannel($channelId = false)
    {
        $this->broker->openChannel($channelId);

        return $this;
    }
}