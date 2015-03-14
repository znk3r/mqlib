<?php

namespace znk3r\MQlib\Exchange;

/**
 * Class Fanout exchange.
 *
 * A fanout exchange sends a copy of the message to all the queues connected to it.
 *
 * @version   1.0.0
 *
 * @author    Miguel Angel Liebana <mi.liebana@gmail.com>
 * @copyright 2015 Miguel Angel Liebana
 */
class Fanout extends AbstractExchange
{
    /** @var string TYPE Exchange type */
    const TYPE = 'fanout';

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
