<?php

namespace znk3r\MQlib\Message;

/**
 * Abstraction of the message sent or received.
 *
 * @version   1.0.0
 *
 * @author    Miguel Angel Liebana <mi.liebana@gmail.com>
 * @copyright 2015 Miguel Angel Liebana
 */
abstract class Message
{
    /** @var string $body Body of the message as sent/received */
    protected $body;

    /** @var string $contentType Content type of the body message */
    protected $contentType;

    /** @var string $contentEncoding Content encoding of the body message, set to UTF-8 by default */
    protected $contentEncoding = 'UTF-8';

    /**
     * Sets the delivery mode:
     *  1: Non-persistent. The message is stored in memory and can be lost if server restarts.
     *  2: Persistent. Message is stored in memory and disk and preserved between server restarts.
     *
     * @var bool
     */
    protected $isPersistent;

    /**
     * Sets the priority of the message inside a queue.
     *
     * @var int|null
     * @note RabbitMQ hasn't implement priority yet (v3.2.1). At the moment the property is ignored
     *               and the only priority is FIFO.
     */
    protected $priority;

    /**
     * TTL for the message in ms. Can be used to expire the message at a specific time (ex. at midnight).
     *
     * @var int|null
     */
    protected $expiration;

    /** @var string|null $messageId Unique ID for the message. If not set, the property will be empty */
    protected $messageId;

    /** @var int|null $timestamp Timestamp when the message was created/sent */
    protected $timestamp;

    /** @var string|null $userId Which broker user has sent the message. Must be known by the server. */
    protected $userId;

    /** @var string|null $appId Which application has created/sent the message */
    protected $appId;

    /**
     * Additional application headers to send, for example with a ttl for the message or what to do if there's no queue.
     *
     * @var array|null
     */
    protected $applicationHeaders;

    /**
     * Name of the callback queue, in case you want to notify something when the message has been processed.
     *
     * @var string|null
     */
    protected $replyTo;

    /**
     * References the original message inside a callback and must be unique.
     * messageId is unique for the original message and the reply, but both messages share the same correlationId.
     *
     * @var string|null
     */
    protected $correlationId;

    /**
     * Set the body of the message.
     *
     * @param string $body
     *
     * @return $this
     *
     * @throws MessageException
     */
    public function setBody($body)
    {
        if (!is_string($body)) {
            throw new MessageException('The body of the message must be a string.');
        }

        $this->body = $body;

        return $this;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param $contentType
     *
     * @return $this
     *
     * @throws InvalidArgumentException
     */
    public function setContentType($contentType)
    {
        if (!is_string($contentType)) {
            throw new InvalidArgumentException('The content type must be a string');
        }

        $this->contentType = $contentType;

        return $this;
    }

    /**
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    public function setContentEncoding($contentEncoding)
    {
        if (!is_string($contentEncoding)) {
            throw new InvalidArgumentException('The content encoding must be a string');
        }

        $this->contentEncoding = $contentEncoding;

        return $this;
    }

    /**
     * @return string
     */
    public function getContentEncoding()
    {
        return $this->contentEncoding;
    }

    /**
     * Declare the message as persistent inside the queue.
     *
     * @param bool $persistent
     *
     * @return $this
     */
    public function markAsPersistent($persistent = true)
    {
        $this->isPersistent = (bool) $persistent;

        return $this;
    }

    /**
     * @return bool
     */
    public function isPersistent()
    {
        return $this->isPersistent;
    }

    /**
     * @param int $priority
     *
     * @return $this
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * @param int|null $ttl
     *
     * @return $this
     *
     * @throws InvalidArgumentException
     */
    public function setExpirationTime($ttl)
    {
        if (null !== $ttl) {
            $ttl = (int) $ttl;

            if ($ttl < 1 || $ttl > PHP_INT_MAX) {
                throw new InvalidArgumentException('Invalid expiration time');
            }
        }

        $this->expiration = $ttl;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getExpirationTime()
    {
        return $this->expiration;
    }

    /**
     * @param string $messageId
     *
     * @return $this
     */
    public function setMessageId($messageId)
    {
        $this->messageId = $messageId;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getMessageId()
    {
        return $this->messageId;
    }

    /**
     * @param int $timestamp
     *
     * @return $this
     *
     * @throws InvalidArgumentException
     */
    public function setTimestamp($timestamp)
    {
        $timestamp = (int) $timestamp;

        if ($timestamp < 1 || $timestamp > PHP_INT_MAX) {
            throw new InvalidArgumentException('Invalid message timestamp');
        }

        $this->timestamp = $timestamp;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @param string $userId
     *
     * @return mixed
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $userId;
    }

    /**
     * @return null|string
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param string $appId
     *
     * @return mixed
     */
    public function setAppId($appId)
    {
        $this->appId = $appId;

        return $appId;
    }

    /**
     * @return null|string
     */
    public function getAppId()
    {
        return $this->appId;
    }

    /**
     * @param array $headers
     *
     * @return $this
     */
    public function setApplicationHeaders(array $headers)
    {
        $this->applicationHeaders = $headers;

        return $this;
    }

    /**
     * @return array|null
     */
    public function getApplicationHeaders()
    {
        return $this->applicationHeaders;
    }

    /**
     * Set the name of the queue to reply as callback.
     *
     * @param string $replyTo
     *
     * @return $this
     */
    public function setReplyTo($replyTo)
    {
        $this->replyTo = $replyTo;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getReplyTo()
    {
        return $this->replyTo;
    }

    /**
     * @param string $id
     *
     * @return $this
     */
    public function setCorrelationId($id)
    {
        $this->correlationId = $id;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getCorrelationId()
    {
        return $this->correlationId;
    }
}
