<?php

namespace Tagcade\Model\Core;

use Tagcade\Model\User\Role\PublisherInterface;

class NetworkBlacklist implements NetworkBlacklistInterface
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
     * @var DisplayBlacklistInterface $displayBlacklist
     */
    protected $displayBlacklist;


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
     * @return DisplayBlacklistInterface
     */
    public function getDisplayBlacklist()
    {
        return $this->displayBlacklist;
    }

    /**
     * @param DisplayBlacklistInterface $displayBlacklist
     */
    public function setDisplayBlacklist($displayBlacklist)
    {
        $this->displayBlacklist = $displayBlacklist;
    }
}