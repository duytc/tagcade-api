<?php

namespace Tagcade\Model\Core;

use Tagcade\Model\User\Role\PublisherInterface;

class PublisherExchange implements PublisherExchangeInterface {

    protected $id;
    /**
     * @var PublisherInterface
     */
    protected $publisher;
    /**
     * @var ExchangeInterface
     */
    protected $exchange;



    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }


    /**
     * @return PublisherInterface
     */
    public function getPublisher()
    {
        return $this->publisher;
    }

    /**
     * @param PublisherInterface $publisher
     * @return self
     */
    public function setPublisher($publisher)
    {
        $this->publisher = $publisher;

        return $this;
    }

    /**
     * @return ExchangeInterface
     */
    public function getExchange()
    {
        return $this->exchange;
    }

    /**
     * @param ExchangeInterface $exchange
     * @return self
     */
    public function setExchange($exchange)
    {
        $this->exchange = $exchange;

        return $this;
    }
}