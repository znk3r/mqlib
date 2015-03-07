<?php

namespace znk3r\MQlib\Broker;

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

    /** @var int|null $channelId Channel id to use with the broker. If null, it uses a random channel for each connection */
    protected $channelId;

    /**
     * Initialize the broker object with a config array.
     *
     * @param array $options {
     *
     *      @var string
     *      @var int
     *      @var string
     *      @var string
     *      @var string
     *      @var bool
     *      @var string
     *      @var string
     *      @var string
     *      @var int
     *      @var int
     * }
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
                'Invalid timeout, should be between '.self::MIN_CONNECTION_TIMEOUT.' and '.self::MAX_CONNECTION_TIMEOUT.' seconds'
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
     * @param $channelId
     *
     * @return $this
     */
    public function setChannelId($channelId)
    {
        if (!is_integer($channelId) || !is_null($channelId)) {
            throw new \InvalidArgumentException('Invalid channel ID, should be an integer or null');
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
}
