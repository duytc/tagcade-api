<?php

namespace Tagcade\Model\Core;

use Tagcade\Model\ModelInterface;

interface ExchangeInterface extends ModelInterface
{
    /**
     * @param mixed $id
     * @return self
     */
    public function setId($id);

    /**
     * @return mixed
     */
    public function getName();

    /**
     * @param mixed $name
     * @return self
     */
    public function setName($name);

    /**
     * @return mixed
     */
    public function getCanonicalName();

    /**
     * @param mixed $canonicalName
     * @return self
     */
    public function setCanonicalName($canonicalName);

    /**
     * @return mixed
     */
    public function getPublisherExchanges();

    /**
     * @param mixed $publisherExchanges
     * @return self
     */
    public function setPublisherExchanges($publisherExchanges);
}