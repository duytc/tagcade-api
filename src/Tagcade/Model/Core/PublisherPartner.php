<?php

namespace Tagcade\Model\Core;


use Tagcade\Model\ModelInterface;

class PublisherPartner implements ModelInterface
{
    protected $id;
    /**
     * @var int
     */
    protected $publisherId;

    /**
     * @var AdNetworkPartner
     */
    protected $adNetworkPartner;
    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return AdNetworkPartner
     */
    public function getAdNetworkPartner()
    {
        return $this->adNetworkPartner;
    }

    /**
     * @param AdNetworkPartner $adNetworkPartner
     * @return $this
     */
    public function setAdNetworkPartner($adNetworkPartner)
    {
        $this->adNetworkPartner = $adNetworkPartner;

        return $this;
    }

    /**
     * @return int
     */
    public function getPublisherId()
    {
        return $this->publisherId;
    }

    /**
     * @param int $publisherId
     * @return $this
     */
    public function setPublisherId($publisherId)
    {
        $this->publisherId = $publisherId;

        return $this;
    }
}