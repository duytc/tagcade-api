<?php


namespace Tagcade\Model\Core;


use Tagcade\Model\User\Role\PublisherInterface;

class LibraryVideoDemandAdTag implements LibraryVideoDemandAdTagInterface, VideoTargetingInterface
{
    const PLATFORM_JAVASCRIPT = 'js';
    const PLATFORM_FLASH = 'flash';

    const LIST_DOMAIN_SUFFIX_KEY = 'suffixKey';
    const LIST_DOMAIN_NAME_KEY = 'name';

    protected $id;

    protected $tagURL;
    protected $name;
    protected $timeout;
    protected $targeting;
    protected $sellPrice;
    protected $deletedAt;

    /** @var VideoDemandPartnerInterface */
    protected $videoDemandPartner;
    protected $videoDemandAdTags;
    protected $waterfallPlacementRules;

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
        return $this->tagURL;
    }

    /**
     * @inheritdoc
     */
    public function setTagURL($tagURL)
    {
        $this->tagURL = $tagURL;
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
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * @inheritdoc
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
        return $this;
    }

    public function getPublisherId()
    {
        $publisher = $this->videoDemandPartner->getPublisher();
        if ($publisher instanceof PublisherInterface) {
            return $publisher->getId();
        }

        return null;
    }
    /**
     * @inheritdoc
     */
    public function getSellPrice()
    {
        return $this->sellPrice;
    }

    /**
     * @inheritdoc
     */
    public function setSellPrice($sellPrice)
    {
        $this->sellPrice = $sellPrice;
        return $this;
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
    public function getVideoDemandPartner()
    {
        return $this->videoDemandPartner;
    }

    /**
     * @inheritdoc
     */
    public function setVideoDemandPartner(VideoDemandPartnerInterface $videoDemandPartner)
    {
        $this->videoDemandPartner = $videoDemandPartner;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getVideoDemandAdTags()
    {
        return $this->videoDemandAdTags;
    }

    /**
     * @return mixed
     */
    public function getWaterfallPlacementRules()
    {
        return $this->waterfallPlacementRules;
    }

    /**
     * @inheritdoc
     */
    public function getLinkedCount()
    {
        return count($this->videoDemandAdTags);
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
}