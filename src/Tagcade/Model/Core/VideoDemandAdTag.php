<?php


namespace Tagcade\Model\Core;


use DateTime;

class VideoDemandAdTag implements VideoDemandAdTagInterface, VideoTargetingInterface
{
    const ACTIVE = 1;
    const PAUSED = 0;
    const AUTO_PAUSED = -1;

    const ACTIVE_DEFAULT = true;

    const PLATFORM_JAVASCRIPT = 'js';
    const PLATFORM_FLASH = 'flash';

    /**
     * @var int
     */
    protected $id;

    /**
     * @var int
     */
    protected $priority;

    /**
     * @var int
     */
    protected $rotationWeight;

    /**
     * @var bool
     */
    protected $active;

    /**
     * @var array
     */
    protected $targeting;

    /**
     * @var bool
     */
    protected $targetingOverride;

    /**
     * @var DateTime
     */
    protected $deletedAt;

    /**
     * @var LibraryVideoDemandAdTagInterface
     */
    protected $libraryVideoDemandAdTag;

    /**
     * @var VideoWaterfallTagItemInterface
     */
    protected $videoWaterfallTagItem;

    function __construct()
    {
        $this->active = self::ACTIVE_DEFAULT;
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
    public function getTagURL()
    {
        if ($this->libraryVideoDemandAdTag instanceof LibraryVideoDemandAdTagInterface) {
            return $this->libraryVideoDemandAdTag->getTagURL();
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function setTagURL($tagURL)
    {
        if ($this->libraryVideoDemandAdTag instanceof LibraryVideoDemandAdTagInterface) {
            $this->libraryVideoDemandAdTag->setTagURL($tagURL);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        if ($this->libraryVideoDemandAdTag instanceof LibraryVideoDemandAdTagInterface) {
            return $this->libraryVideoDemandAdTag->getName();
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function setName($name)
    {
        if ($this->libraryVideoDemandAdTag instanceof LibraryVideoDemandAdTagInterface) {
            $this->libraryVideoDemandAdTag->setName($name);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * @inheritdoc
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getTimeout()
    {
        if ($this->libraryVideoDemandAdTag instanceof LibraryVideoDemandAdTagInterface) {
            return $this->libraryVideoDemandAdTag->getTimeout();
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function setTimeout($timeout)
    {
        if ($this->libraryVideoDemandAdTag instanceof LibraryVideoDemandAdTagInterface) {
            $this->libraryVideoDemandAdTag->setTimeout($timeout);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getRotationWeight()
    {
        return $this->rotationWeight;
    }

    /**
     * @inheritdoc
     */
    public function setRotationWeight($rotationWeight)
    {
        $this->rotationWeight = $rotationWeight;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getTargeting()
    {
        if ($this->targetingOverride === false && $this->libraryVideoDemandAdTag instanceof LibraryVideoDemandAdTagInterface) {
            return $this->libraryVideoDemandAdTag->getTargeting();
        }

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
     * @return boolean
     */
    public function isTargetingOverride()
    {
        return $this->targetingOverride;
    }

    /**
     * @param boolean $targetingOverride
     * @return self
     */
    public function setTargetingOverride($targetingOverride)
    {
        $this->targetingOverride = $targetingOverride;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @inheritdoc
     */
    public function setActive($active)
    {
        $this->active = $active;
    }

    /**
     * @inheritdoc
     */
    public function getLibraryVideoDemandAdTag()
    {
        return $this->libraryVideoDemandAdTag;
    }

    /**
     * @inheritdoc
     */
    public function setLibraryVideoDemandAdTag($libraryVideoDemandAdTag)
    {
        $this->libraryVideoDemandAdTag = $libraryVideoDemandAdTag;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getVideoDemandPartner()
    {
        if ($this->libraryVideoDemandAdTag instanceof LibraryVideoDemandAdTagInterface) {
            return $this->libraryVideoDemandAdTag->getVideoDemandPartner();
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function getVideoWaterfallTagItem()
    {
        return $this->videoWaterfallTagItem;
    }

    /**
     * @inheritdoc
     */
    public function setVideoWaterfallTagItem($videoWaterfallTagItem)
    {
        $this->videoWaterfallTagItem = $videoWaterfallTagItem;
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
    public function hasFlashPlatform()
    {
        return
            is_array($this->targeting)
            && array_key_exists(self::TARGETING_KEY_PLATFORM, $this->targeting)
            && array_key_exists(self::PLATFORM_FLASH, $this->targeting[self::TARGETING_KEY_PLATFORM]);
    }

    /**
     * @inheritdoc
     */
    public function hasJavascriptPlatform()
    {
        return array_key_exists(self::TARGETING_KEY_PLATFORM, $this->getTargeting()) && array_key_exists(self::PLATFORM_JAVASCRIPT, $this->getTargeting()[self::TARGETING_KEY_PLATFORM]);
    }

    /**
     * @inheritdoc
     */
    public static function getSupportedTargetingKeys()
    {
        return [
            self::TARGETING_KEY_REQUIRED_MACROS,
            self::TARGETING_KEY_PLAYER_SIZE,
            self::TARGETING_KEY_COUNTRIES,
            self::TARGETING_KEY_EXCLUDE_COUNTRIES,
            self::TARGETING_KEY_DOMAINS,
            self::TARGETING_KEY_EXCLUDE_DOMAINS,
            self::TARGETING_KEY_PLATFORM,
        ];
    }

    /**
     * @inheritdoc
     */
    public function __toString()
    {
        return $this->id . $this->getName();
    }
}