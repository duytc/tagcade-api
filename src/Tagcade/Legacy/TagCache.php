<?php

namespace Tagcade\Legacy;

use Tagcade\DomainManager\AdSlotManagerInterface;
use Tagcade\Form\Type\AdSlotFormType;
use Tagcade\Legacy\Cache\Tag\NamespaceCacheInterface;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\AdSlotInterface;
use Tagcade\Model\Core\AdTagInterface;
use Doctrine\Common\Collections\Collection;

class TagCache implements TagCacheInterface
{
    const NAMESPACE_CACHE_KEY = 'tagcade_adslot_%d';
    const CACHE_KEY_AD_SLOT = 'all_tags_array';

    /** key expressions  for ad slot dynamic select */
    const KEY_EXPRESSIONS = 'expressions';
    /** key defaultAdSlot for ad slot dynamic select */
    const KEY_DEFAULT_AD_SLOT = 'defaultAdSlot';

    protected $cache;
    /**
     * @var AdSlotManagerInterface
     */
    protected $adSlotManager;

    public function __construct(NamespaceCacheInterface $cache, AdSlotManagerInterface $adSlotManager)
    {
        $this->cache = $cache;
        $this->adSlotManager = $adSlotManager;
    }

    public function refreshCacheForAdSlot(AdSlotInterface $adSlot)
    {
        $this->cache->setNamespace($this->getNamespace($adSlot->getId()));

        $oldVersion = (int)$this->cache->getNamespaceVersion();
        $newVersion = $oldVersion + 1;

        // create the new version of the cache first
        $this->cache->setNamespaceVersion($newVersion);
        $this->cache->save(static::CACHE_KEY_AD_SLOT, $this->createAdSlotCacheData($adSlot));

        // delete the old version of the cache
        $this->cache->setNamespaceVersion($oldVersion);

        $this->cache->deleteAll();

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

            if (!in_array($adSlot, $refreshedAdSlots, $strict = true)) {
                $refreshedAdSlots[] = $adSlot;

                $this->refreshCacheForAdSlot($adSlot);
            }

            unset($adSlot, $adTag);
        }
    }

    public function refreshCache()
    {
        $adSlots = $this->adSlotManager->all();

        foreach ($adSlots as $adSlot) {
            $this->refreshCacheForAdSlot($adSlot);
        }

        return $this;
    }

    /**
     * create AdSlot Cache Data.
     *
     * In case of 'enableVariable == false' => formatted as 'static':
     * {
     *     "id": "1",
     *     "type": "static",
     *     "tags": [ {...}, [{...}, ...], ...]
     * }
     *
     * else, in case of 'enableVariable == true' => formatted as 'dynamic':
     * {
     *     "id": "1",
     *     "type": "dynamic",
     *     "expressions":
     *     [
     *         {
     *             "expression": ...,
     *             "expectAdSlot": ...
     *         },
     *         {...},
     *         ...
     *     ],
     *     "slots":
     *     [
     *         //array of 'static' format as above.
     *     ]
     * }
     *
     * @param AdSlotInterface $adSlot
     * @return array
     */
    protected function createAdSlotCacheData(AdSlotInterface $adSlot)
    {
        if (!$adSlot->getEnableVariable()) {
            return $this->createAdSlotCacheDataStatic($adSlot);
        }

        return $this->createAdSlotCacheDataDynamic($adSlot);
    }

    /**
     * create as static, format as:
     *
     * {
     *     'id' => $adSlot->getId(),
     *     'type' => 'static',
     *     'tags' => [... all tags ...]
     * }
     *
     * e.g:
     * {
     *     "id": "1",
     *     "type": "static",
     *     "tags":
     *     [
     *         "0":
     *         //one item
     *         {
     *             "id": "1",
     *             "tag": "<html>http://www.tag.com</html>",
     *             "cap": "10",
     *             "rot": "100",
     *         },
     *
     *         "1":
     *         [
     *             //array of items
     *             [
     *                 {
     *                     "id": "1",
     *                     "tag": "<html>http://www.tag.com</html>",
     *                     "cap": "10",
     *                     "rot": "100",
     *                 },
     *                 {...},
     *                 ...
     *             ]
     *         ],
     *         "n": [...],
     *         ...
     *     ]
     * }
     *
     * @param AdSlotInterface $adSlot
     * @return array
     */
    private function createAdSlotCacheDataStatic($adSlot)
    {
        $data = [];

        //step 1. get and check adTags
        /** @var AdTagInterface[]|Collection $adTags */
        $adTags = $adSlot->getAdTags();

        if ($adTags instanceof Collection) {
            $adTags = $adTags->toArray();
        }

        if (empty($adTags)) {
            return $data;
        }

        //step 2. set 'id' & 'type' for data
        $data['id'] = $adSlot->getId();
        $data['type'] = 'static';

        //step 3. build 'tags' for data
        ////sort all adTags by position
        usort($adTags, function (AdTagInterface $a, AdTagInterface $b) {
            if ($a->getPosition() == $b->getPosition()) {
                return 0;
            }
            return ($a->getPosition() < $b->getPosition()) ? -1 : 1;
        });

        ////group all adTags which same position into a group (array) with key is position
        ////each group can contain one or more items (as {'id', 'tag', 'cap', 'rot'})
        $groups = array();

        foreach ($adTags as $adTag) {
            if (!$adTag->isActive()) {
                continue;
            }

            $dataItem = [
                'id' => $adTag->getId(),
                'tag' => $adTag->getHtml(),
            ];

            if (null !== $adTag->getFrequencyCap()) {
                $dataItem['cap'] = $adTag->getFrequencyCap();
            }

            if (null !== $adTag->getRotation()) {
                $dataItem['rot'] = $adTag->getRotation();
            }

            // grouping same position into array
            $groups[$adTag->getPosition()][] = $dataItem;
        }

        ////now mapping all groups to tags as format: [{..single item...}, [{...}, ..array of items same position..], ...]
        $dataTags = array_map(function ($groupData) {
                return count($groupData) > 1 ? $groupData : $groupData[0];
            },
            $groups
        );

        //step 4. set tags for data
        $data['tags'] = $dataTags;

        //step 5. return data
        return $data;
    }

    /**
     * create as dynamic, format as:
     *
     * [
     *     'id' => $adSlot->getId(),
     *     'type' => 'static',
     *     'expressions' => [
     *          'expressions' => [... expression, expectAdSlot ...],
     *          'defaultAdSlot'
     *     ],
     *     'slots' => [... array of static-adSlot-cache ...]
     * ]
     *
     * @param AdSlotInterface $adSlot
     * @return array
     */
    private function createAdSlotCacheDataDynamic($adSlot)
    {
        $data = [];

        //check adSlot, expressions
        $expressions = $adSlot->getExpressions();
        if (!is_array($expressions) || empty($expressions)) {
            return $data;
        }

        //set 'id', 'type', 'expressions' for data
        $data = [
            'id' => $adSlot->getId(),
            'type' => 'dynamic',
            'expressions' => [
                self::KEY_EXPRESSIONS => $expressions[AdSlotFormType::KEY_EXPRESSIONS],
                self::KEY_DEFAULT_AD_SLOT => $adSlot->getId()]
        ];

        //step 1. get all AdSlots exist in $adSlot's expressions
        ////adSlots from expression
        $adSlotsForSelecting = array_map(function ($expression) {
                return $this->adSlotManager->find($expression[AdSlotFormType::KEY_EXPECT_AD_SLOT]);
            },
            $expressions[AdSlotFormType::KEY_EXPRESSIONS]
        );

        ////adSlot default using this adSlot:
        $adSlotsForSelecting[] = $adSlot;

        //step 2. build 'slots' for data
        $slots = array_map(function ($adSlotForDynamic) {
                return $this->createAdSlotCacheDataStatic($adSlotForDynamic);
            },
            $adSlotsForSelecting
        );

        //step 3. set 'slots' for data
        $data['slots'] = $slots;

        //step 4. return data
        return $data;
    }


    protected function getNamespace($slotId)
    {
        return sprintf(static::NAMESPACE_CACHE_KEY, $slotId);
    }
} 