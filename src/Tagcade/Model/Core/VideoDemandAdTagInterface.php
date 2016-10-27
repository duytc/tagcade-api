<?php


namespace Tagcade\Model\Core;

use Tagcade\Model\ModelInterface;

interface VideoDemandAdTagInterface extends ModelInterface
{
    /**
     * @return mixed
     */
    public function getTagURL();

    /**
     * @param mixed $tagURL
     * @return self
     */
    public function setTagURL($tagURL);

    /**
     * @return mixed
     */
    public function getName();

    /**
     * @return float
     */
    public function getSellPrice();

    /**
     * @param mixed $name
     * @return self
     */
    public function setName($name);

    /**
     * @return mixed
     */
    public function getPriority();

    /**
     * @param mixed $priority
     * @return self
     */
    public function setPriority($priority);

    /**
     * @return mixed
     */
    public function getTimeout();

    /**
     * @param mixed $timeout
     * @return self
     */
    public function setTimeout($timeout);

    /**
     * @return mixed
     */
    public function getRotationWeight();

    /**
     * @param mixed $rotationWeight
     * @return self
     */
    public function setRotationWeight($rotationWeight);

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
     * @return boolean
     */
    public function isTargetingOverride();

    /**
     * @param boolean $targetingOverride
     * @return self
     */
    public function setTargetingOverride($targetingOverride);

    /**
     * @return mixed
     */
    public function getActive();

    /**
     * @param mixed $active
     */
    public function setActive($active);

    /**
     * @return LibraryVideoDemandAdTagInterface
     */
    public function getLibraryVideoDemandAdTag();

    /**
     * @return VideoDemandPartnerInterface
     */
    public function getVideoDemandPartner();

    /**
     * @param LibraryVideoDemandAdTagInterface $libraryVideoDemandAdTag
     * @return self
     */
    public function setLibraryVideoDemandAdTag($libraryVideoDemandAdTag);

    /**
     * @return WaterfallPlacementRuleInterface
     */
    public function getWaterfallPlacementRule();

    /**
     * @param WaterfallPlacementRuleInterface $waterfallPlacementRule
     * @return self
     */
    public function setWaterfallPlacementRule($waterfallPlacementRule);

    /**
     * @return VideoWaterfallTagItemInterface
     */
    public function getVideoWaterfallTagItem();

    /**
     * @param mixed $videoWaterfallTagItem
     */
    public function setVideoWaterfallTagItem($videoWaterfallTagItem);

    /**
     * @return mixed
     */
    public function getDeletedAt();

    /**
     * @return bool
     */
    public function hasFlashPlatform();

    /**
     * @return bool
     */
    public function hasJavascriptPlatform();
} 