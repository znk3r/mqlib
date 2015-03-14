<?php
namespace znk3r\MQlib\Message\Outgoing;
use znk3r\MQlib\Message\Outgoing;


/**
 * JSON message which can be sent to the queue
 *
 * @version   1.0.0
 *
 * @author    Miguel Angel Liebana <mi.liebana@gmail.com>
 * @copyright 2015 Miguel Angel Liebana
 */
class Json extends Outgoing
{
    /** @var string CONTENT_TYPE Default JSON content type */
    const CONTENT_TYPE = 'application/json';

    /** @var string CONTENT_ENCODING Default JSON encoding */
    const CONTENT_ENCODING = 'UTF-8';

    /**
     * Encode some data to create a JSON message
     *
     * @param mixed $data
     */
    public function __construct($data = null)
    {
        if (null !== $data) {
            $this->body = json_encode($data);
        }

        $this->contentType = self::CONTENT_TYPE;
        $this->contentEncoding = self::CONTENT_ENCODING;
    }
}