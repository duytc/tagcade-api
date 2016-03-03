<?php

namespace Tagcade\Model\Core;


use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\ModelInterface;

interface PublisherExchangeInterface extends ModelInterface{
    /**
     * @param mixed $id
     * @return self
     */
    public function setId($id);

    /**
     * @return PublisherInterface
     */
    public function getPublisher();

    /**
     * @param PublisherInterface $publisher
     * @return self
     */
    public function setPublisher($publisher);

    /**
     * @return ExchangeInterface
     */
    public function getExchange();

    /**
     * @param ExchangeInterface $exchange
     * @return self
     */
    public function setExchange($exchange);
}