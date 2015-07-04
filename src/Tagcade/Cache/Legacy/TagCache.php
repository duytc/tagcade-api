<?php

namespace Tagcade\Cache\Legacy;

use Doctrine\Common\Collections\Collection;
use Tagcade\Cache\Legacy\Cache\Tag\NamespaceCacheInterface;
use Tagcade\Cache\TagCacheAbstract;
use Tagcade\Cache\TagCacheInterface;
use Tagcade\DomainManager\AdSlotManagerInterface;
use Tagcade\DomainManager\DisplayAdSlotManagerInterface;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\DisplayAdSlotInterface;
use Tagcade\Model\Core\AdTagInterface;

class TagCache extends TagCacheAbstract implements TagCacheInterface
{
    const NAMESPACE_CACHE_KEY = 'tagcade_adslot_%d';
    const VERSION = 1;
    /**
     * @var DisplayAdSlotManagerInterface
     */
    private $displayAdSlotManager;

    public function __construct(NamespaceCacheInterface $cache, DisplayAdSlotManagerInterface $displayAdSlotManager)
    {
        parent::__construct($cache);

        $this->displayAdSlotManager = $displayAdSlotManager;
    }

    public function supportVersion($version)
    {
        return $version === self::VERSION;
    }

    public function refreshCache()
    {
        $adSlots = $this->displayAdSlotManager->all();

        foreach ($adSlots as $adSlot) {
            $this->refreshCacheForDisplayAdSlot($adSlot);
        }

        return $this;
    }

    /**
     * @param AdNetworkInterface $adNetwork
     * @return $this
     */
    public function refreshCacheForAdNetwork(AdNetworkInterface $adNetwork)
    {
        $adTags = $adNetwork->getAdTags();

        $refreshedAdSlots = [];

        foreach ($adTags as $adTag) {
            /**
             * @var AdTagInterface $adTag
             */
            $adSlot = $adTag->getAdSlot();

            if (!$adSlot instanceof DisplayAdSlotInterface) {
                continue;
            }

            if (!in_array($adSlot, $refreshedAdSlots, $strict = true)) {
                $refreshedAdSlots[] = $adSlot;

                $this->refreshCacheForDisplayAdSlot($adSlot);
            }

            unset($adSlot, $adTag);
        }
    }

    /**
     * @inheritdoc
     */
    protected function createAdSlotCacheData(DisplayAdSlotInterface $adSlot)
    {
        $data = [];

        /** @var AdTagInterface[]|Collection $adTags */
        $adTags = $adSlot->getAdTags();

        if ($adTags instanceof Collection) {
            $adTags = $adTags->toArray();
        }

        if (empty($adTags)) {
            return $data;
        }

        usort($adTags, function (AdTagInterface $a, AdTagInterface $b) {
            if ($a->getPosition() == $b->getPosition()) {
                return 0;
            }
            return ($a->getPosition() < $b->getPosition()) ? -1 : 1;
        });

        $lastPosition = 0;
        foreach ($adTags as $adTag) {
            if (!$adTag->isActive()) {
                continue;
            }

            if ($adTag->getPosition() <= $lastPosition) {
                continue;
            }// not include ad tag with repeated position
            $lastPosition = $adTag->getPosition();

            $dataItem = [
                'id' => $adTag->getId(),
                'tag' => $adTag->getHtml(),
            ];

            if (null !== $adTag->getFrequencyCap()) {
                $dataItem['cap'] = $adTag->getFrequencyCap();
            }

            $data[] = $dataItem;
        }

        return $data;
    }

    protected function getNamespace($slotId)
    {
        return sprintf(static::NAMESPACE_CACHE_KEY, $slotId);
    }
}