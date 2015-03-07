<?php

namespace znk3r\MQlib\Broker;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AbstractConnection;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Connection\AMQPSSLConnection;
use PhpAmqpLib\Connection\AMQPSocketConnection;
use PhpAmqpLib\Connection\AMQPLazyConnection;
use znk3r\MQlib\Broker\Connection\ConnectionException;
use znk3r\MQlib\Broker\Connection\Factory;

/**
 * Class AbstractBroker.
 *
 * This class represents the channel and the connection with the queue broker (AMPQ Server).
 *
 * @version   1.0.0
 *
 * @author    Miguel Angel Liebana <mi.liebana@gmail.com>
 * @copyright 2015 Miguel Angel Liebana
 */
abstract class AbstractBroker
{
    /** @var int MIN_PORT Minimum port number */
    const MIN_PORT = 1;

    /** @var int MAX_PORT Maximum port number */
    const MAX_PORT = 65535;

    /** @var int MIN_CONNECTION_TIMEOUT Minimum number of seconds for timeout */
    const MIN_CONNECTION_TIMEOUT = 1;

    /** @var int MAX_CONNECTION_TIMEOUT Maximum number of seconds for timeout */
    const MAX_CONNECTION_TIMEOUT = 300;

    /** @var string $connectionType */
    protected $connectionType;

    /** @var AbstractConnection $connection */
    protected $connection;

    /** @var int|null $channelId */
    protected $channelId;

    /** @var AMQPChannel $currentChannel */
    protected $currentChannel;

    /** @var string $host AMQP broker */
    protected $host;

    /** @var int $port AMQP broker port */
    protected $port;

    /** @var string $user User to connect */
    protected $user;

    /** @var string $password Auth password for $user */
    protected $password;

    /** @var string $vhost Connection vhost to use */
    protected $vhost;

    /** @var bool $insist Set to true to avoid redirections between multiple servers */
    protected $insist = false;

    /** @var string $loginMethod */
    protected $loginMethod;

    /** @var string|null $loginResponse */
    protected $loginResponse;

    /** @var string $locale Language locale to use for the broker messages */
    protected $locale;

    /** @var int $timeout Seconds before the connection is closed with a timeout */
    protected $timeout;

    /** @var int $readWriteTimeout Seconds before a read/write operation times out */
    protected $readWriteTimeout;

    /** @var bool $keepAlive */
    protected $keepAlive = false;

    /** @var int $heartbeat */
    protected $heartbeat = 0;

    /** @var array $sslOptions SSL connection options */
    protected $sslOptions = array();

    /**
     * Initialize the broker object with a config array.
     *
     * @param array $options
     */
    public function __construct(array $options)
    {
        foreach ($options as $property => $value) {
            if (property_exists($this, $property)) {
                $this->{$property} = $value;
            }
        }
    }

    /**
     * @param AbstractConnection|null $connection
     *
     * @return $this
     */
    public function connect($connection = null)
    {
        if (null !== $connection) {
            $this->setConnection($connection);
        }

        // The connection is opened when the connection object is created, we just need to check if this is true
        // Calling getConnection() creates a new connection if one doesn't exist
        if (!$this->getConnection()->isConnected()) {
            $this->connection->reconnect();
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function disconnect()
    {
        if ($this->connection) {
            $this->connection->close();
            $this->connection = null;
        }

        return $this;
    }

    /**
     * @param AbstractConnection $connection
     * @return $this
     */
    public function setConnection(AbstractConnection $connection)
    {
        $this->connection = $connection;

        return $this;
    }

    /**
     * @return AMQPStreamConnection|AMQPSSLConnection|AMQPSocketConnection|AMQPLazyConnection
     * @throws ConnectionException
     */
    public function getConnection()
    {
        if (!$this->connection) {
            $this->connection = Factory::create($this, $this->getConnectionType());
        }

        if (!$this->connection instanceof AbstractConnection) {
            throw new ConnectionException('Invalid connection object');
        }

        return $this->connection;
    }

    /**
     * @param string $type
     * @throws ConnectionException
     * @return $this
     */
    public function setConnectionType($type)
    {
        $factoryClass = new \ReflectionClass('Factory');

        foreach($factoryClass->getConstants() as $constantName => $constantValue) {
            if ($constantValue == $type) {
                $this->connectionType = $type;
                return $this;
            }
        }

        throw new ConnectionException('Unknown connection type '.$type);
    }

    /**
     * @return string|null
     */
    public function getConnectionType()
    {
        return $this->connectionType;
    }

    /**
     * @param int|null $channelId
     * @return $this
     * @throws InvalidArgumentException
     */
    public function setChannelId($channelId)
    {
        if (null !== $channelId || !is_integer($channelId)) {
            throw new InvalidArgumentException('Invalid channel id, should be null or an integer');
        }

        $this->channelId = $channelId;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getChannelId()
    {
        return $this->channelId;
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
        if (false !== $channelId) {
            $this->setChannelId($channelId);
        }

        $this->currentChannel = $this->getConnection()->channel($channelId);
        return $this;
    }

    /**
     * @return AMQPChannel|null
     */
    public function getCurrentChannel()
    {
        return $this->currentChannel;
    }


    /**
     * @param $host
     *
     * @return $this
     *
     * @throws InvalidArgumentException
     */
    public function setHost($host)
    {
        if (!is_string($host)) {
            throw new InvalidArgumentException('Invalid broker host, should be a string');
        }

        $this->host = $host;

        return $this;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @param $port
     *
     * @return $this
     *
     * @throws InvalidArgumentException
     */
    public function setPort($port)
    {
        $port = (int) $port;

        if ($port < self::MIN_PORT || $port > self::MAX_PORT) {
            throw new InvalidArgumentException(
                'Invalid port number, should be between '.self::MIN_PORT.' and '.self::MAX_PORT
            );
        }

        $this->port = $port;

        return $this;
    }

    /**
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @param $user
     *
     * @return $this
     *
     * @throws InvalidArgumentException
     */
    public function setUser($user)
    {
        if (!is_string($user)) {
            throw new InvalidArgumentException('Invalid username, should be a string');
        }

        $this->user = $user;

        return $this;
    }

    /**
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param $password
     *
     * @return $this
     *
     * @throws InvalidArgumentException
     */
    public function setPassword($password)
    {
        if (!is_string($password)) {
            throw new InvalidArgumentException('Invalid password, should be a string');
        }

        $this->password = $password;

        return $this;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param $vhost
     *
     * @return $this
     *
     * @throws InvalidArgumentException
     */
    public function setVhost($vhost)
    {
        if (!is_string($vhost)) {
            throw new InvalidArgumentException('Invalid virtual host, should be a string');
        }

        $this->vhost = $vhost;

        return $this;
    }

    /**
     * @return string
     */
    public function getVhost()
    {
        return $this->vhost;
    }

    /**
     * @param $insist
     *
     * @return $this
     */
    public function setInsist($insist)
    {
        $this->insist = (bool) $insist;

        return $this;
    }

    /**
     * @return bool
     */
    public function getInsist()
    {
        return $this->insist;
    }

    /**
     * @param $loginMethod
     *
     * @return $this
     *
     * @throws InvalidArgumentException
     */
    public function setLoginMethod($loginMethod)
    {
        if (!is_string($loginMethod)) {
            throw new InvalidArgumentException('Invalid login method, should be a string');
        }

        $this->loginMethod = $loginMethod;

        return $this;
    }

    /**
     * @return string
     */
    public function getLoginMethod()
    {
        return $this->loginMethod;
    }

    /**
     * @return string|null
     */
    public function getLoginResponse()
    {
        return $this->loginResponse;
    }

    /**
     * @param $locale
     *
     * @return $this
     *
     * @throws InvalidArgumentException
     */
    public function setLocale($locale)
    {
        if (!is_string($locale)) {
            throw new InvalidArgumentException('Invalid locale, should be a string');
        }

        $this->locale = $locale;

        return $this;
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @param $seconds
     *
     * @return $this
     *
     * @throws InvalidArgumentException
     */
    public function setTimeout($seconds)
    {
        $seconds = (int) $seconds;

        if ($seconds < self::MIN_CONNECTION_TIMEOUT || $seconds > self::MAX_CONNECTION_TIMEOUT) {
            throw new InvalidArgumentException(
                'Invalid connection timeout, should be between '.self::MIN_CONNECTION_TIMEOUT.' and '.self::MAX_CONNECTION_TIMEOUT.' seconds'
            );
        }

        $this->timeout = $seconds;

        return $this;
    }

    /**
     * @return int
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * @param $seconds
     *
     * @return $this
     *
     * @throws InvalidArgumentException
     */
    public function setReadWriteTimeout($seconds)
    {
        $seconds = (int) $seconds;

        if ($seconds < self::MIN_CONNECTION_TIMEOUT || $seconds > self::MAX_CONNECTION_TIMEOUT) {
            throw new InvalidArgumentException(
                'Invalid r/w timeout, should be between '.self::MIN_CONNECTION_TIMEOUT.' and '.self::MAX_CONNECTION_TIMEOUT.' seconds'
            );
        }

        $this->readWriteTimeout = $seconds;

        return $this;
    }

    /**
     * @return int
     */
    public function getReadWriteTimeout()
    {
        return $this->readWriteTimeout;
    }

    /**
     * @param bool $keepAlive
     *
     * @return $this
     */
    public function setKeepAlive($keepAlive)
    {
        $this->keepAlive = (bool)$keepAlive;

        return $this;
    }

    /**
     * @return bool
     */
    public function getKeepAlive()
    {
        return $this->keepAlive;
    }

    /**
     * @param int $heartbeat
     *
     * @return $this
     */
    public function setHeartbeat($heartbeat)
    {
        $this->heartbeat = (int)$heartbeat;

        return $this;
    }

    /**
     * @return int
     */
    public function getHeartbeat()
    {
        return $this->heartbeat;
    }

    /**
     * @param array $sslOptions
     *
     * @return $this
     */
    public function setSslConnectionOptions(array $sslOptions)
    {
        $this->sslOptions = $sslOptions;

        return $this;
    }

    /**
     * @return array
     */
    public function getSslConnectionOptions()
    {
        return $this->sslOptions;
    }
}
