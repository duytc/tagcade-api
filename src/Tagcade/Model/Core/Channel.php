<?php

namespace Tagcade\Model\Core;

use Doctrine\Common\Collections\ArrayCollection;
use Tagcade\Model\User\Role\PublisherInterface;

class Channel implements ChannelInterface
{
    const RTB_STATUS_DEFAULT = self::RTB_DISABLED;

    protected $id;

    /** @var PublisherInterface */
    protected $publisher;

    protected $name;

    protected $rtbStatus;

    protected $deletedAt;

    /** @var ChannelSiteInterface[] */
    protected $channelSites;

    public function __construct()
    {
        $this->rtbStatus = self::RTB_STATUS_DEFAULT;
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
        foreach ($channelSites as $channelSite) {
            $sites[] = $channelSite->getSite();
        }

        return $sites;
    }

    /**
     * @inheritdoc
     */
    public function isRTBEnabled()
    {
        if ($this->getPublisher() === null || !$this->getPublisher()->hasRtbModule()) {
            return false;
        }

        return $this->rtbStatus === self::RTB_ENABLED;
    }

    /**
     * @return int
     */
    public function getRtbStatus()
    {
        return $this->rtbStatus;
    }

    /**
     * @param int $rtbStatus
     * @return self
     */
    public function setRtbStatus($rtbStatus)
    {
        $this->rtbStatus = null === $rtbStatus ? self::RTB_STATUS_DEFAULT : $rtbStatus;
        return $this;
    }

    public function __toString()
    {
        return $this->id . '-' . $this->getName();
    }
}
