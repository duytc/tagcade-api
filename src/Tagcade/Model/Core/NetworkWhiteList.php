<?php

namespace Tagcade\Model\Core;

use Tagcade\Model\User\Role\PublisherInterface;

class NetworkWhiteList implements NetworkWhiteListInterface
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var PublisherInterface $publisher
     */
    protected $publisher;

    /**
     * @var AdNetworkInterface $adNetwork
     */
    protected $adNetwork;

    /**
     * @var DisplayWhiteListInterface $displayWhiteList
     */
    protected $displayWhiteList;


    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }
    
    /**
     * @return AdNetworkInterface
     */
    public function getAdNetwork()
    {
        return $this->adNetwork;
    }

    /**
     * @param AdNetworkInterface $adNetwork
     */
    public function setAdNetwork($adNetwork)
    {
        $this->adNetwork = $adNetwork;
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
     * @return DisplayWhiteListInterface
     */
    public function getDisplayWhiteList()
    {
        return $this->displayWhiteList;
    }

    /**
     * @param DisplayWhiteListInterface $displayWhiteList
     * @return self
     */
    public function setDisplayWhiteList($displayWhiteList)
    {
        $this->displayWhiteList = $displayWhiteList;
        return $this;
    }
}