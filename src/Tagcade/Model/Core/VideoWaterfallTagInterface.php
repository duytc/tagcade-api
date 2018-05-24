<?php


namespace Tagcade\Model\Core;


use Tagcade\Model\ModelInterface;
use Tagcade\Model\User\UserEntityInterface;

interface VideoWaterfallTagInterface extends ModelInterface
{
    /**
     * @return mixed
     */
    public function getUuid();

    /**
     * @param mixed $uuid
     * @return self
     */
    public function setUuid($uuid);

    /**
     * @return mixed
     */
    public function getPlatform();

    /**
     * @param mixed $platform
     * @return self
     */
    public function setPlatform($platform);

    /**
     * @return mixed
     */
    public function getAdDuration();

    /**
     * @param mixed $adDuration
     * @return self
     */
    public function setAdDuration($adDuration);

    /**
     * @return float
     */
    public function getBuyPrice();

    /**
     * @param float $buyPrice
     * @return self
     */
    public function setBuyPrice($buyPrice);

    /**
     * @return mixed
     */
    public function getCompanionAds();

    /**
     * @param mixed $companionAds
     * @return self
     */
    public function setCompanionAds($companionAds);

    /**
     * @return VideoPublisherInterface
     */
    public function getVideoPublisher();

    /**
     * @return null|int
     */
    public function getVideoPublisherId();

    /**
     * @param VideoPublisherInterface $videoPublisher
     * @return self
     */
    public function setVideoPublisher(VideoPublisherInterface $videoPublisher);

    /**
     * @return null|UserEntityInterface
     */
    public function getPublisher();

    /**
     * @return array
     */
    public function getVideoWaterfallTagItems();

    /**
     * @param VideoWaterfallTagItemInterface $videoWaterfallTagItem
     * @return $this
     */
    public function addVideoWaterfallTagItem(VideoWaterfallTagItemInterface $videoWaterfallTagItem);

    /**
     * @param array $videoWaterfallTagItems
     * @return self
     */
    public function setVideoWaterfallTagItems($videoWaterfallTagItems);

    /**
     * @return array
     */
    public function getIvtPixelWaterfallTags();

    /**
     * @param IvtPixelWaterfallTagInterface $ivtPixelWaterfallTags
     * @return self
     */
    public function addIvtPixelWaterfallTag(IvtPixelWaterfallTagInterface $ivtPixelWaterfallTags);

    /**
     * @param array $ivtPixelWaterfallTags
     * @return self
     */
    public function setIvtPixelWaterfallTags($ivtPixelWaterfallTags);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     * @return self
     */
    public function setName($name);

    /**
     * @return mixed
     */
    public function getDeletedAt();

    /**
     * @return mixed
     */
    public function getTargeting();

    /**
     * @param mixed $targeting
     * @return self
     */
    public function setTargeting($targeting);

    /**
     * @return string
     */
    public function getRunOn();

    /**
     * @param string $runOn
     * @return self
     */
    public function setRunOn($runOn);

    /**
     * @return bool
     */
    public function isAutoOptimize();

    /**
     * @param bool $autoOptimize
     * @return self
     */
    public function setAutoOptimize($autoOptimize);

    /**
     * @return int
     */
    public function getOptimizationIntegration();

    /**
     * @param int $optimizationIntegration
     * @return self
     */
    public function setOptimizationIntegration($optimizationIntegration);
}