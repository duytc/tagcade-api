<?php

namespace Tagcade\Model\Core;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\PersistentCollection;
use Tagcade\Model\User\Role\PublisherInterface;

class DisplayWhiteList implements DisplayWhiteListInterface
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
     * @var PublisherInterface
     */
    protected $publisher;

    /**
     * @var NetworkWhiteListInterface
     */
    protected $networkWhiteLists;

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
     * @return NetworkWhiteListInterface
     */
    public function getNetworkWhiteLists()
    {
        return $this->networkWhiteLists;
    }

    /**
     * @param NetworkWhiteListInterface $networkWhiteLists
     * @return self
     */
    public function setNetworkWhiteLists($networkWhiteLists)
    {
        $this->networkWhiteLists = $networkWhiteLists;
        return $this;
    }

    public function addNetworkWhiteList(NetworkWhiteListInterface $networkWhiteList)
    {
        if (!$this->networkWhiteLists instanceof PersistentCollection) {
            $this->networkWhiteLists = new ArrayCollection();
        }

        $this->networkWhiteLists->add($networkWhiteList);
        return $this;
    }

    /**
     * @return array
     */
    public function getAdNetworks()
    {
        $adNetworks = [];
        $networkWhiteLists = $this->getNetworkWhiteLists();
        /**
         * @var NetworkWhiteListInterface $networkWhiteList
         */
        foreach ($networkWhiteLists as $networkWhiteList) {
            $adNetworks[] = $networkWhiteList->getAdNetwork();
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
