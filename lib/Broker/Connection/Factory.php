<?php

namespace znk3r\MQlib\Broker\Connection;

use znk3r\MQlib\Broker\AbstractBroker;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Connection\AMQPSSLConnection;
use PhpAmqpLib\Connection\AMQPSocketConnection;
use PhpAmqpLib\Connection\AMQPLazyConnection;

/**
 * Factory class to create a connection object based on a type.
 *
 * @version   1.0.0
 *
 * @author    Miguel Angel Liebana <mi.liebana@gmail.com>
 * @copyright 2015 Miguel Angel Liebana
 */
final class Factory
{
    /** @var string CONNECTION_STREAM Use a StreamIO for the connection to the broker */
    const CONNECTION_STREAM = 'StreamIO';

    /** @var string CONNECTION_STREAM_SSL Use StreamIO over SSL */
    const CONNECTION_STREAM_SSL = 'StreamIO_SSL';

    /** @var string CONNECTION_STREAM_LAZY Use StreamIO with lazy connection */
    const CONNECTION_STREAM_LAZY = 'StreamIO_Lazy';

    /** @var string CONNECTION_SOCKET Use SocketIo for the connection tothe broker */
    const CONNECTION_SOCKET = 'SocketIO';

    /**
     * Generate the connection object to use.
     *
     * @param AbstractBroker $broker
     * @param string         $type   Connection type
     *
     * @throws ConnectionException
     *
     * @return AMQPStreamConnection|AMQPSSLConnection|AMQPSocketConnection|AMQPLazyConnection
     */
    public static function create(AbstractBroker $broker, $type = self::CONNECTION_STREAM)
    {
        switch ($type) {
            case self::CONNECTION_STREAM:
                return self::getStreamConnection($broker);

            case self::CONNECTION_STREAM_SSL:
                return self::getSslStreamConnection($broker);

            case self::CONNECTION_STREAM_LAZY:
                return self::getStreamLazyConnection($broker);

            case self::CONNECTION_SOCKET:
                return self::getSocketConnection($broker);
                break;

            default:
                throw new ConnectionException('Unknown connection type '.$type);
        }
    }

    /**
     * @param AbstractBroker $broker
     *
     * @return AMQPStreamConnection
     */
    private static function getStreamConnection(AbstractBroker $broker)
    {
        return new AMQPStreamConnection(
            $broker->getHost(),
            $broker->getPort(),
            $broker->getUser(),
            $broker->getPassword(),
            $broker->getVhost(),
            $broker->getInsist(),
            $broker->getLoginMethod(),
            $broker->getLoginResponse(),
            $broker->getLocale(),
            $broker->getTimeout(),
            $broker->getReadWriteTimeout(),
            null,
            $broker->getKeepAlive(),
            $broker->getHeartbeat()
        );
    }

    /**
     * @param AbstractBroker $broker
     *
     * @return AMQPSSLConnection
     */
    private static function getSslStreamConnection(AbstractBroker $broker)
    {
        $options = array(
            'insist' => $broker->getInsist(),
            'login_method' => $broker->getLoginMethod(),
            'login_response' => $broker->getLoginResponse(),
            'locale' => $broker->getLocale(),
            'connection_timeout' => $broker->getTimeout(),
            'read_write_timeout' => $broker->getReadWriteTimeout(),
            'keepalive' => $broker->getKeepAlive(),
            'heartbeat' => $broker->getHeartbeat(),
        );

        return new AMQPSSLConnection(
            $broker->getHost(),
            $broker->getPort(),
            $broker->getUser(),
            $broker->getPassword(),
            $broker->getVhost(),
            $broker->getSslConnectionOptions(),
            $options
        );
    }

    /**
     * @param AbstractBroker $broker
     *
     * @return AMQPLazyConnection
     */
    private static function getStreamLazyConnection(AbstractBroker $broker)
    {
        return new AMQPLazyConnection(
            $broker->getHost(),
            $broker->getPort(),
            $broker->getUser(),
            $broker->getPassword(),
            $broker->getVhost(),
            $broker->getInsist(),
            $broker->getLoginMethod(),
            $broker->getLoginResponse(),
            $broker->getLocale(),
            $broker->getTimeout(),
            $broker->getReadWriteTimeout(),
            null,
            $broker->getKeepAlive(),
            $broker->getHeartbeat()
        );
    }

    /**
     * @param AbstractBroker $broker
     *
     * @return AMQPSocketConnection
     */
    private static function getSocketConnection(AbstractBroker $broker)
    {
        return new AMQPSocketConnection(
            $broker->getHost(),
            $broker->getPort(),
            $broker->getUser(),
            $broker->getPassword(),
            $broker->getVhost(),
            $broker->getInsist(),
            $broker->getLoginMethod(),
            $broker->getLoginResponse(),
            $broker->getLocale(),
            $broker->getTimeout(),
            $broker->getKeepAlive()
        );
    }
}
