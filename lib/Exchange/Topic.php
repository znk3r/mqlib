<?php

namespace znk3r\MQlib\Exchange;

/**
 * Class Topic exchange.
 *
 * Each message is sent with a set of topics, which are used to route it to one or more queues
 *
 * @version   1.0.0
 *
 * @author    Miguel Angel Liebana <mi.liebana@gmail.com>
 * @copyright 2015 Miguel Angel Liebana
 */
class Topic extends AbstractExchange
{
    /** @var string TYPE Exchange type */
    const TYPE = 'topic';

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
