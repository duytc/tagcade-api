<?php

namespace Tagcade\Model\Core;

class Exchange implements ExchangeInterface
{
    protected $id;
    protected $name;
    protected $canonicalName;
    protected $publisherExchanges;

    public function __construct()
    {
    }

    /**
     * @return mixed
     */
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
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCanonicalName()
    {
        return $this->canonicalName;
    }

    /**
     * @param mixed $canonicalName
     * @return self
     */
    public function setCanonicalName($canonicalName)
    {
        $this->canonicalName = $canonicalName;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPublisherExchanges()
    {
        return $this->publisherExchanges;
    }

    /**
     * @param mixed $publisherExchanges
     * @return self
     */
    public function setPublisherExchanges($publisherExchanges)
    {
        $this->publisherExchanges = $publisherExchanges;

        return $this;
    }


    public function __toString()
    {
        return $this->id . $this->getName();
    }
}