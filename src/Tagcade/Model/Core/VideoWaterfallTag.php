<?php

namespace Tagcade\Model\Core;

use Doctrine\Common\Collections\ArrayCollection;

class VideoWaterfallTag implements VideoWaterfallTagInterface, VideoTargetingInterface
{
    const DEFAULT_AD_DURATION = 30;

    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    protected $uuid;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $platform;

    /**
     * @var int
     */
    protected $adDuration = self::DEFAULT_AD_DURATION;

    /**
     * @var array
     */
    protected $companionAds;

    /**
     * @var VideoPublisherInterface $videoPublisher
     */
    protected $videoPublisher;

    /**
     * @var float
     */
    protected $buyPrice;

    protected $deletedAt;
    protected $targeting;

    /* new feature: Server-Side VAST+VAPID, Server-Side VAST Only, Client-Side VAST+VAPID (default)*/
    protected $runOn;

    /**
     * @var VideoWaterfallTagItemInterface[]
     */
    protected $videoWaterfallTagItems;

    /**
     * @var IvtPixelWaterfallTagInterface[]
     */
    protected $ivtPixelWaterfallTags;

    function __construct()
    {
        $this->adDuration = self::DEFAULT_AD_DURATION;
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * @inheritdoc
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;
        return $this;
    }

    /**
     * @return string
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
    public function getPlatform()
    {
        return $this->platform;
    }

    /**
     * @inheritdoc
     */
    public function setPlatform($platform)
    {
        $this->platform = $platform;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getAdDuration()
    {
        return $this->adDuration;
    }

    /**
     * @return float
     */
    public function getBuyPrice()
    {
        return $this->buyPrice;
    }

    /**
     * @param float $buyPrice
     * @return self
     */
    public function setBuyPrice($buyPrice)
    {
        $this->buyPrice = $buyPrice;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setAdDuration($adDuration)
    {
        $this->adDuration = $adDuration;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCompanionAds()
    {
        return $this->companionAds;
    }

    /**
     * @inheritdoc
     */
    public function setCompanionAds($companionAds)
    {
        $this->companionAds = $companionAds;
        return $this;
    }

    /**
     * @return VideoPublisherInterface
     */
    public function getVideoPublisher()
    {
        return $this->videoPublisher;
    }

    /**
     * @inheritdoc
     */
    public function getVideoPublisherId()
    {
        return ($this->videoPublisher instanceof VideoPublisherInterface) ? $this->videoPublisher->getId() : null;
    }

    /**
     * @inheritdoc
     */
    public function setVideoPublisher(VideoPublisherInterface $videoPublisher)
    {
        $this->videoPublisher = $videoPublisher;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getPublisher()
    {
        if ($this->videoPublisher instanceof VideoPublisherInterface) {
            return $this->videoPublisher->getPublisher();
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function getVideoWaterfallTagItems()
    {
        return $this->videoWaterfallTagItems;
    }

    /**
     * @inheritdoc
     */
    public function addVideoWaterfallTagItem(VideoWaterfallTagItemInterface $videoWaterfallTagItem)
    {
        if ($this->videoWaterfallTagItems === null) {
            $this->videoWaterfallTagItems = new ArrayCollection();
        }

        $this->videoWaterfallTagItems[] = $videoWaterfallTagItem;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setVideoWaterfallTagItems($videoWaterfallTagItems)
    {
        $this->videoWaterfallTagItems = $videoWaterfallTagItems;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getIvtPixelWaterfallTags()
    {
        if ($this->ivtPixelWaterfallTags === null) {
            return new ArrayCollection();
        }

        return $this->ivtPixelWaterfallTags;
    }

    /**
     * @inheritdoc
     */
    public function addIvtPixelWaterfallTag(IvtPixelWaterfallTagInterface $ivtPixelWaterfallTags)
    {
        if ($this->ivtPixelWaterfallTags === null) {
            $this->ivtPixelWaterfallTags = new ArrayCollection();
        }

        $this->ivtPixelWaterfallTags[] = $ivtPixelWaterfallTags;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setIvtPixelWaterfallTags($ivtPixelWaterfallTags)
    {
        $this->ivtPixelWaterfallTags = $ivtPixelWaterfallTags;
        return $this;
    }
    /**
     * @inheritdoc
     */
    public function setDeletedAt($deletedAt)
    {
        $this->deletedAt = $deletedAt;
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
    public function getTargeting()
    {
        return $this->targeting;
    }

    /**
     * @inheritdoc
     */
    public function setTargeting($targeting)
    {
        $this->targeting = $targeting;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public static function getSupportedTargetingKeys()
    {
        return [self::TARGETING_KEY_PLAYER_SIZE];
    }

    /**
     * @inheritdoc
     */
    public function getRunOn()
    {
        return $this->runOn;
    }

    /**
     * @inheritdoc
     */
    public function setRunOn($runOn)
    {
        $this->runOn = $runOn;
        return $this;
    }
}
