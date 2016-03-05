<?php

namespace Tagcade\Model\Core;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Tagcade\Bundle\AdminApiBundle\Model\SourceReportSiteConfigInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\SubPublisherInterface;
use Tagcade\Model\User\UserEntityInterface;

class Site implements SiteInterface
{
    const RTB_STATUS_DEFAULT = self::RTB_DISABLED;

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

    /** @var SubPublisherInterface[] */
    protected $subPublisherSites;

    protected $deletedAt;

    protected $rtbStatus;

    public function __construct()
    {
        $this->adSlots = new ArrayCollection();
        $this->channelSites = new ArrayCollection();
        $this->autoCreate = false;
        $this->rtbStatus = self::RTB_STATUS_DEFAULT;
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
        if (!$this->adSlots instanceof Collection) {
            $this->adSlots = new ArrayCollection();
        }

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


    /**
     * @return mixed
     */
    public function isRTBEnabled()
    {
        if ($this->getPublisher() === null || !$this->getPublisher()->hasRtbModule()) {
            return false;
        }

        if ($this->rtbStatus === self::RTB_INHERITED) {
            $channels = $this->getChannels();
            if (empty($channels)) {
                return $this->getPublisher()->hasRtbModule();
            }

            $rtbEnabled = false;
            /** @var ChannelInterface $channel */
            foreach($channels as $channel) {
                if ($channel->isRTBEnabled())
                {
                    return true;
                }
            }

            return $rtbEnabled;
        }

        return $this->rtbStatus === self::RTB_ENABLED;
    }

    /**
     * @inheritdoc
     */
    public function setSubPublisherSites($subPublisherSites)
    {
        $this->subPublisherSites = $subPublisherSites;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getSubPublisherSites()
    {
        if(null === $this->subPublisherSites) {
            $this->subPublisherSites = new ArrayCollection();
        }
        return $this->subPublisherSites;
    }

    /**
     * @inheritdoc
     */
    public function getSubPublishers()
    {
        $subPublishers = [];
        $subPublisherSites = $this->getSubPublisherSites();
        /**
         * @var SubPublisherSiteInterface $subPublisherSite
         */
        foreach($subPublisherSites as $subPublisherSite) {
            $subPublishers[] = $subPublisherSite->getSubPublisher();
        }

        return $subPublishers;
    }

    public function __toString()
    {
        return $this->id . $this->getName();
    }
}
