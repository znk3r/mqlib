<?php

namespace znk3r\MQlib\Exchange;

/**
 * Class Headers exchange.
 *
 * A headers exchange sends the message to one or more queues based on the message headers
 *
 * @version   1.0.0
 *
 * @author    Miguel Angel Liebana <mi.liebana@gmail.com>
 * @copyright 2015 Miguel Angel Liebana
 */
class Headers extends AbstractExchange
{
    /** @var string TYPE Exchange type */
    const TYPE = 'headers';

    /**
     * Return exchange type.
     *
     * @return string
     */
    public function getExchangeType()
    {
        return self::TYPE;
    }
}
