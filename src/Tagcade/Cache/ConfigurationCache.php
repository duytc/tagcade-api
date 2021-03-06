<?php

namespace Tagcade\Cache;


use Tagcade\Behaviors\CreateSiteTokenTrait;
use Tagcade\Exception\LogicException;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\ReportableAdSlotInterface;
use Tagcade\Model\Core\RonAdSlotInterface;
use Tagcade\Model\Core\RonAdTagInterface;

class ConfigurationCache implements ConfigurationCacheInterface
{
    use CreateSiteTokenTrait;

    const REDIS_HASH_EXISTING_RON_TAG = 'event_processor:existing_ron_tag_in_slot'; // provide existence check and mapping from (ron ad tag, slot) to ad tag id for counting event
    const FIELD_RON_TAG_SLOT = 'ron_tag_%d:slot_%d';
    const REDIS_HASH_EXISTING_RON_SLOT_DOMAIN = 'event_processor:existing_ron_slot_in_domain'; // provide existence check and mapping from (domain, ron slot) to ad slot id for counting event
    const FIELD_RON_SLOT_DOMAIN = 'ron_slot_%d:domain_%s';
    const NONE = 0;
    const SUCCESS = 1;
    /**
     * @var RedisCacheInterface
     */
    private $redis;

    function __construct(RedisCacheInterface $redis)
    {
        $this->redis = $redis;
    }

    public function removeRonSlotDomainCacheForAdSlot(BaseAdSlotInterface $adSlot)
    {
        $ronAdSlot = $adSlot->getLibraryAdSlot()->getRonAdSlot();
        if (!$ronAdSlot instanceof RonAdSlotInterface) {
            return self::NONE;
        }

        $ronSlotDomainKey = $this->getRonSlotDomainKey($adSlot);
        if (false === $ronSlotDomainKey) {
            return self::NONE;
        }

        return $this->redis->hDelete(self::REDIS_HASH_EXISTING_RON_SLOT_DOMAIN, $ronSlotDomainKey);
    }

    public function removeRonSlotDomainCacheForAdSlots(array $adSlots)
    {
        $successCount = 0;
        foreach ($adSlots as $adSlot) {
            if (!$adSlot instanceof BaseAdSlotInterface) {
                continue;
            }

            $successCount += $this->removeRonSlotDomainCacheForAdSlot($adSlot);
        }

        return $successCount;
    }

    public function addAdSlotToRonSlotDomainCache(BaseAdSlotInterface $adSlot)
    {
        // This is a new ad slot creation. It could be for ron ad slot
        if (!$adSlot->getLibraryAdSlot()->getRonAdSlot() instanceof RonAdSlotInterface) {
            return self::NONE; // not a ron ad slot
        }

        $ronSlotDomainKey = $this->getRonSlotDomainKey($adSlot);
        if (false === $ronSlotDomainKey) {
            return self::NONE; // we don't create mapping in cache for duplicated domain.
        }

        $slotId = $this->redis->hFetch(self::REDIS_HASH_EXISTING_RON_SLOT_DOMAIN, $ronSlotDomainKey);
        if (false === $slotId) { // not created in redis yet
            $this->redis->hSave(self::REDIS_HASH_EXISTING_RON_SLOT_DOMAIN, $ronSlotDomainKey, $adSlot->getId());
        }

        return self::SUCCESS;
    }

    /**
     * Add ad tag to ron tag slot cache
     * @param AdTagInterface $adTag
     * @param RonAdTagInterface $ronAdTag
     * @return int
     */
    public function addAdTagToRonTagSlotCache(AdTagInterface $adTag, RonAdTagInterface $ronAdTag)
    {
        $libraryAdSlot = $adTag->getAdSlot()->getLibraryAdSlot();
        if (!$libraryAdSlot->getRonAdSlot() instanceof RonAdSlotInterface || $this->isAdSlotInDuplicatedSite($adTag->getAdSlot())) {
            return self::NONE; // the ad tag is not created from ron ad slot
        }

        $ronTagSlotKey = $this->getRonTagSlotKey($ronAdTag, $adTag->getAdSlot());
        if (false === $ronTagSlotKey) {
            return self::NONE; // the ad tag is in slot that belonging to duplicated site
        }

        $adTagId = $this->redis->hFetch(self::REDIS_HASH_EXISTING_RON_TAG, $ronTagSlotKey);

        if (false === $adTagId) { // not created in redis yet
            $this->redis->hSave(self::REDIS_HASH_EXISTING_RON_TAG, $ronTagSlotKey, $adTag->getId());
        }

        return self::SUCCESS;
    }

    /**
     * @inheritdoc
     */
    public function removeRonTagSlotCacheForAdTag(AdTagInterface $adTag, RonAdTagInterface $ronAdTag)
    {
        /** @var ReportableAdSlotInterface $adSlot */
        $adSlot = $adTag->getAdSlot();
        if (!$adSlot->getLibraryAdSlot()->getRonAdSlot() instanceof RonAdSlotInterface) {
            return self::NONE;
        }

        $ronTagSlotKey = $this->getRonTagSlotKey($ronAdTag, $adSlot);
        if (false === $ronTagSlotKey) {
            return self::NONE; // the ad tag is in slot that belonging to duplicated site
        }

        $this->redis->hDelete(self::REDIS_HASH_EXISTING_RON_TAG, $ronTagSlotKey);

        return self::SUCCESS;
    }

    public function removeRonTagSlotCacheForRonAdTag(RonAdTagInterface $ronAdTag)
    {
        $allRelatedAdTags = array_filter(
            $ronAdTag->getLibraryAdTag()->getAdTags()->toArray(),
            function (AdTagInterface $adTag) {
                return $adTag->getAdSlot()->getLibraryAdSlot()->getRonAdSlot() instanceof RonAdSlotInterface;
            }
        );

        $successCount = 0;
        foreach ($allRelatedAdTags as $adTag) {
            $successCount += $this->removeRonTagSlotCacheForAdTag($adTag, $ronAdTag);
        }

        return $successCount;
    }

    public function removeAll()
    {
        $this->redis->delete(self::REDIS_HASH_EXISTING_RON_SLOT_DOMAIN);
        $this->redis->delete(self::REDIS_HASH_EXISTING_RON_TAG);
    }

    public function refreshForRonAdSlots(array $ronAdSlots)
    {
        $this->removeAll();

        foreach ($ronAdSlots as $ronAdSlot) {
            if (!$ronAdSlot instanceof RonAdSlotInterface) {
                continue;
            }
            /**
             * @var RonAdSlotInterface $ronAdSlot
             */
            foreach ($ronAdSlot->getLibraryAdSlot()->getAdSlots() as $adSlot) {
                /**
                 * @var BaseAdSlotInterface $adSlot
                 */
                $this->addAdSlotToRonSlotDomainCache($adSlot);
            }

            foreach ($ronAdSlot->getRonAdTags() as $ronAdTag) {
                /**
                 * @var RonAdTagInterface $ronAdTag
                 */
                $allRelatedAdTags = array_filter(
                    $ronAdTag->getLibraryAdTag()->getAdTags()->toArray(),
                    function (AdTagInterface $adTag) {
                        return $adTag->getAdSlot()->getLibraryAdSlot()->getRonAdSlot() instanceof RonAdSlotInterface;
                    }
                );

                foreach ($allRelatedAdTags as $adTag) {
                    $this->addAdTagToRonTagSlotCache($adTag, $ronAdTag);
                }
            }
        }

        return $this;
    }

    /**
     * Create ron slot domain key. This works only if the site containing ad slot has hash value, not the token uuid (to ignore duplicated domain).
     *
     * @param BaseAdSlotInterface $adSlot
     * @return string|false
     *
     */
    protected function getRonSlotDomainKey(BaseAdSlotInterface $adSlot)
    {
        if ($this->isAdSlotInDuplicatedSite($adSlot)) {
            return false;
        }

        // This is a new ad slot creation. It could be for ron ad slot
        $ronAdSlot = $adSlot->getLibraryAdSlot()->getRonAdSlot();
        if (!$ronAdSlot instanceof RonAdSlotInterface) {
            throw new LogicException('the ad slot must be generated from a ron ad slot');
        }

        return sprintf(self::FIELD_RON_SLOT_DOMAIN, $ronAdSlot->getId(), $adSlot->getSite()->getDomain());
    }

    protected function getRonTagSlotKey(RonAdTagInterface $ronTag, ReportableAdSlotInterface $adSlot)
    {
        if ($this->isAdSlotInDuplicatedSite($adSlot)) {
            return false;
        }

        return sprintf(self::FIELD_RON_TAG_SLOT, $ronTag->getId(), $adSlot->getId());
    }

    protected function isAdSlotInDuplicatedSite(BaseAdSlotInterface $adSlot)
    {
        $site = $adSlot->getSite();
        $publisherId = $site->getPublisherId();
        $hash = $this->createSiteHash($publisherId, $site->getDomain());

        return $hash != $site->getSiteToken();
    }
}