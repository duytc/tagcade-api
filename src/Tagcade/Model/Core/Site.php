<?php

namespace Tagcade\Model\Core;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Tagcade\Bundle\AdminApiBundle\Model\SourceReportSiteConfigInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\UserEntityInterface;

class Site implements SiteInterface
{
    protected $id;

    /** @var UserEntityInterface */
    protected $publisher;
    protected $name;
    protected $domain;
    protected $siteToken;
    /**
     * @var bool
     */
    protected $autoCreate = false;
    protected $adSlots;
    protected $enableSourceReport;
    /** @var SourceReportSiteConfigInterface[] */
    protected $sourceReportSiteConfigs;

    /** @var ChannelSiteInterface[] */
    protected $channelSites;
    protected $players;

    protected $deletedAt;

    protected $subPublisher;

    public function __construct()
    {
        $this->adSlots = new ArrayCollection();
        $this->channelSites = new ArrayCollection();
        $this->autoCreate = false;
    }

    public function getId()
    {
        return $this->id;
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
    public function setPublisher(PublisherInterface $publisher)
    {
        $this->publisher = $publisher->getUser();
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
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @inheritdoc
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
        return $this;
    }

    public function getDisplayAdSlots()
    {
        if (!$this->adSlots instanceof Collection) {
            return [];
        }

        return array_filter($this->adSlots->toArray(), function (BaseAdSlotInterface $adSlot) {
            return $adSlot instanceof DisplayAdSlotInterface;
        });
    }

    /**
     * @inheritdoc
     */
    public function getReportableAdSlots()
    {
        if (!$this->adSlots instanceof Collection) {
            return [];
        }

        return array_filter($this->adSlots->toArray(), function (BaseAdSlotInterface $adSlot) {
            return $adSlot instanceof ReportableAdSlotInterface;
        });
    }

    /**
     * @inheritdoc
     */
    public function getAllAdSlots()
    {
    /*      if (!$this->adSlots instanceof Collection) {
            $this->adSlots = new ArrayCollection();
        }*/

        return $this->adSlots;
    }

    /**
     * @inheritdoc
     */
    public function setAdSlots($adSlots)
    {
        $this->adSlots = $adSlots;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getEnableSourceReport()
    {
        return $this->enableSourceReport;
    }

    /**
     * @inheritdoc
     */
    public function setEnableSourceReport($enableSourceReport)
    {
        $this->enableSourceReport = $enableSourceReport;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getSourceReportSiteConfigs()
    {
        return $this->sourceReportSiteConfigs;
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
        if(null === $this->channelSites) {
            $this->channelSites = new ArrayCollection();
        }
        return $this->channelSites;
    }

    /**
     * @inheritdoc
     */
    public function getChannels()
    {
        $channels = [];
        $channelSites = $this->getChannelSites();
        /**
         * @var ChannelSiteInterface $channelSite
         */
        foreach($channelSites as $channelSite) {
            $channels[] = $channelSite->getChannel();
        }

        return $channels;
    }

    /**
     * @inheritdoc
     */
    public function getPlayers()
    {
        if (!is_array($this->players)) {
            $this->players = [];
        }

        return $this->players;
    }

    /**
     * @inheritdoc
     */
    public function setPlayers($players)
    {
        $this->players = is_array($players) ? $players : (null === $players ? [] : [$players]);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function isAutoCreate()
    {
        return $this->autoCreate;
    }

    /**
     * @inheritdoc
     */
    public function setAutoCreate($autoCreate)
    {
        $this->autoCreate = $autoCreate;
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
    public function getSiteToken()
    {
        return $this->siteToken;
    }

    /**
     * @inheritdoc
     */
    public function setSiteToken($siteToken)
    {
        $this->siteToken = $siteToken;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getSubPublishers()
    {
        throw new \Exception('should not call this method');
    }

    /**
     * @return mixed
     */
    public function getSubPublisher()
    {
        return $this->subPublisher;
    }

    /**
     * @param mixed $subPublisher
     */
    public function setSubPublisher($subPublisher)
    {
        $this->subPublisher = $subPublisher;
    }

    public function __toString()
    {
        return $this->id . $this->getName();
    }
}
