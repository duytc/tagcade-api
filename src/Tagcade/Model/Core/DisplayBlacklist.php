<?php

namespace Tagcade\Model\Core;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\PersistentCollection;
use Tagcade\Model\User\Role\PublisherInterface;

class DisplayBlacklist implements DisplayBlacklistInterface
{
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $domains;

    /**
     * @var bool
     */
    protected $isDefault;

    /**
     * @var PublisherInterface
     */
    protected $publisher;

    /**
     * @var NetworkBlacklistInterface
     */
    protected $networkBlacklists;

    public function getId()
    {
        return $this->id;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function setId($id)
    {
        return $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return array
     */
    public function getDomains()
    {
        return $this->domains;
    }

    /**
     * @param array $domains
     * @return self
     */
    public function setDomains($domains)
    {
        $this->domains = $domains;
        return $this;
    }

    public function __toString()
    {
        return $this->id . '-' . $this->getName();
    }

    /**
     * @return NetworkBlacklistInterface
     */
    public function getNetworkBlacklists()
    {
        return $this->networkBlacklists;
    }

    /**
     * @param NetworkBlacklistInterface $networkBlacklists
     */
    public function setNetworkBlacklists($networkBlacklists)
    {
        $this->networkBlacklists = $networkBlacklists;
    }

    /**
     * @param NetworkBlacklistInterface $networkBlacklist
     * @return $this
     */
    public function addNetworkBlacklist(NetworkBlacklistInterface $networkBlacklist)
    {
        if (!$this->networkBlacklists instanceof PersistentCollection) {
            $this->networkBlacklists = new ArrayCollection();
        }

        $this->networkBlacklists->add($networkBlacklist);
        return $this;
    }


    /**
     * @return boolean
     */
    public function isDefault()
    {
        return $this->isDefault;
    }

    /**
     * @param boolean $pubDefault
     */
    public function setIsDefault($pubDefault)
    {
        $this->isDefault = $pubDefault;
    }

    /**
     * @return array
     */
    public function getAdNetworks()
    {
        $adNetworks = [];
        $networkBlacklists = $this->getNetworkBlacklists();
        /**
         * @var  NetworkBlacklistInterface $networkBlacklist
         */
        foreach ($networkBlacklists as $networkBlacklist) {
            $adNetworks[] = $networkBlacklist->getAdNetwork();
        }

        return $adNetworks;
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
     */
    public function setPublisher($publisher)
    {
        $this->publisher = $publisher;
    }
}
