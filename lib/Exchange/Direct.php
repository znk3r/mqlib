<?php

namespace znk3r\MQlib\Exchange;

/**
 * Class Direct exchange.
 *
 * A direct exchange sends the message to a specific queue
 *
 * @version   1.0.0
 *
 * @author    Miguel Angel Liebana <mi.liebana@gmail.com>
 * @copyright 2015 Miguel Angel Liebana
 */
class Direct extends AbstractExchange
{
    /** @var string TYPE Exchange type */
    const TYPE = 'direct';

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
