<?php
namespace znk3r\MQlib;
use znk3r\MQlib\Exchange\AbstractExchange;
use znk3r\MQlib\Message\Outgoing;

/**
 * Class Producer.
 *
 * @version   1.0.0
 *
 * @author    Miguel Angel Liebana <mi.liebana@gmail.com>
 * @copyright 2015 Miguel Angel Liebana
 */
class Producer extends AbstractApplication
{
    /** @var AbstractExchange $exchange */
    protected $exchange;

    /**
     * @param AbstractExchange $exchange
     * @return $this
     */
    public function sendTo(AbstractExchange $exchange)
    {
        $this->exchange = $exchange;
        $this->getBroker()->getChannel()->declareExchange($exchange);

        return $this;
    }

    /**
     * @return AbstractExchange
     */
    public function getExchange()
    {
        return $this->exchange;
    }

    /**
     * @return bool
     */
    public function hasExchange()
    {
        return $this->exchange instanceof AbstractExchange;
    }

    /**
     * @param Outgoing $message
     * @param AbstractExchange|null $exchange
     * @return $this
     * @throws Exception
     */
    public function publish(Outgoing $message, $exchange = null)
    {
        if (null !== $exchange) {
            $this->sendTo($exchange);
        }

        if (!$this->hasExchange()) {
            throw new Exception("The exchange hasn't been defined");
        }

        $this->getBroker()->getChannel()->sendMessage($message, $this->getExchange());

        return $this;
    }
}
