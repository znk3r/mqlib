<?php

namespace znk3r\MQlib\Message\Outgoing;

use znk3r\MQlib\Message\Outgoing;

/**
 * Text message which can be sent to the queue.
 *
 * @version   1.0.0
 *
 * @author    Miguel Angel Liebana <mi.liebana@gmail.com>
 * @copyright 2015 Miguel Angel Liebana
 */
class Text extends Outgoing
{
    /** @var string CONTENT_TYPE Default text content type */
    const CONTENT_TYPE = 'text/plain';

    /** @var string CONTENT_ENCODING Default text encoding */
    const CONTENT_ENCODING = 'UTF-8';

    /**
     * Encode some data to create a text message.
     *
     * @param mixed $data
     */
    public function __construct($data = null)
    {
        $this->body = $data;
        $this->contentType = self::CONTENT_TYPE;
        $this->contentEncoding = self::CONTENT_ENCODING;
    }
}
