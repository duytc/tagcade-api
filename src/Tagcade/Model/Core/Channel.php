<?php

namespace Tagcade\Model\Core;

use Doctrine\Common\Collections\Collection;
use Tagcade\Model\User\Role\PublisherInterface;
use Doctrine\Common\Collections\ArrayCollection;

class Channel implements ChannelInterface
{
    protected $id;

    /** @var PublisherInterface */
    protected $publisher;

    protected $name;

    protected $deletedAt;

    /** @var ChannelSiteInterface[] */
    protected $channelSites;

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
        $this->channelSites = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getPublisherId()
    {
        if (!$this->publisher) {
            return null;
        }

        return $this->publisher->getId();
    }

    /**
     * @inheritdoc
     */
    public function getPublisher()
    {
        return $this->publisher;
    }

    /**
     * @inheritdoc
     */
    public function setPublisher(PublisherInterface $publisher)
    {
        $this->publisher = $publisher;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @inheritdoc
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    /**
     * @inheritdoc
     */
    public function setChannelSites($channelSites)
    {
        $this->channelSites = $channelSites;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getChannelSites()
    {
        if (null === $this->channelSites) {
            $this->channelSites = new ArrayCollection();
        }

        return $this->channelSites;
    }

    /**
     * @inheritdoc
     */
    public function getSites()
    {
        $sites = [];
        $channelSites = $this->getChannelSites();
        /**
         * @var ChannelSiteInterface $channelSite
         */
        foreach($channelSites as $channelSite) {
            $sites[] = $channelSite->getSite();
        }

        return $sites;
    }


    public function __toString()
    {
        return $this->id . '-' . $this->getName();
    }
}
