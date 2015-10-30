<?php

namespace Tagcade\Cache;


use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\RonAdTagInterface;

interface ConfigurationCacheInterface {
    /**
     * Remove ron slot domain field
     *
     * @param BaseAdSlotInterface $adSlot
     * @return int
     */
    public function removeRonSlotDomainCacheForAdSlot(BaseAdSlotInterface $adSlot);

    /**
     * @param array $adSlots
     * @return int
     */
    public function removeRonSlotDomainCacheForAdSlots(array $adSlots);

    /**
     *
     * @param BaseAdSlotInterface $adSlot
     * @return int
     */
    public function addAdSlotToRonSlotDomainCache(BaseAdSlotInterface $adSlot);

    /**
     * Add ad tag to ron tag slot cache
     * @param AdTagInterface $adTag
     * @param RonAdTagInterface $ronAdTag
     *
     * @return int
     */
    public function addAdTagToRonTagSlotCache(AdTagInterface $adTag, RonAdTagInterface $ronAdTag);

    /**
     * @param AdTagInterface $adTag
     * @param RonAdTagInterface $ronAdTag
     * @return int
     */
    public function removeRonTagSlotCacheForAdTag(AdTagInterface $adTag, RonAdTagInterface $ronAdTag);

    public function removeRonTagSlotCacheForRonAdTag(RonAdTagInterface $ronAdTag);

    public function removeAll();

    public function refreshForRonAdSlots(array $ronAdSlots);
}