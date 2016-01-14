<?php

namespace Tagcade\Model\Core;

use Doctrine\Common\Collections\ArrayCollection;
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

    /**
     * @param string $name
     * @param string $domain
     */
    public function __construct($name, $domain)
    {
        $this->name = $name;
        $this->domain = $domain;
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
        $this->adSlots;
    }

    /**
     * @inheritdoc
     */
    public function getReportableAdSlots()
    {
        return array_filter($this->adSlots->toArray(), function (BaseAdSlotInterface $adSlot) {
                return $adSlot instanceof ReportableAdSlotInterface;
            });
    }

    public function getAllAdSlots()
    {
        return $this->adSlots;
    }

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
     * @return array
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
     * @return mixed
     */
    public function getPlayers()
    {
        return $this->players;
    }

    /**
     * @param mixed $players
     */
    public function setPlayers($players)
    {
        $this->players = $players;
    }

    /**
     * @return boolean
     */
    public function isAutoCreate()
    {
        return $this->autoCreate;
    }

    /**
     * @param boolean $autoCreate
     * @return self
     */
    public function setAutoCreate($autoCreate)
    {
        $this->autoCreate = $autoCreate;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    /**
     * @return mixed
     */
    public function getSiteToken()
    {
        return $this->siteToken;
    }

    /**
     * @param mixed $siteToken
     */
    public function setSiteToken($siteToken)
    {
        $this->siteToken = $siteToken;
    }

    public function __toString()
    {
        return $this->id . $this->getName();
    }
}
