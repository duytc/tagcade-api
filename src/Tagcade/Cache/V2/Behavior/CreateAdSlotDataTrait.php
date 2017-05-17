<?php

namespace Tagcade\Cache\V2\Behavior;


use Doctrine\Common\Collections\Collection;
use Tagcade\Bundle\ApiBundle\Service\ExpressionInJsGenerator;
use Tagcade\Exception\LogicException;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\DisplayAdSlotInterface;
use Tagcade\Model\Core\DisplayBlacklistInterface;
use Tagcade\Model\Core\DisplayWhiteListInterface;
use Tagcade\Model\Core\DynamicAdSlotInterface;
use Tagcade\Model\Core\ExpressionInterface;
use Tagcade\Model\Core\NativeAdSlotInterface;
use Tagcade\Model\Core\ReportableAdSlotInterface;
use Tagcade\Model\ModelInterface;
use Tagcade\Service\TagGenerator;

trait CreateAdSlotDataTrait
{
    /**
     * @param ModelInterface $model
     * @return array
     */
    protected function createCacheDataForEntity(ModelInterface $model)
    {
        if ($model instanceof DynamicAdSlotInterface) {
            return $this->createAdSlotCacheDataDynamic($model);
        }

        if ($model instanceof NativeAdSlotInterface) {
            return $this->createNativeAdSlotCacheData($model);
        }

        if ($model instanceof DisplayAdSlotInterface) {
            return $this->createDisplayAdSlotCacheData($model);
        }

        throw new LogicException(sprintf('Do not support cache v2 of ', get_class($model)));

    }

    /**
     * create as display, format as:
     *
     * {
     *     'id' => $adSlot->getId(),
     *     'type' => 'display',
     *     'width' => $adSlot->getWidth(),
     *     'height' => $adSlot->getHeight(),
     *     'passbackMode' => $adSlot->getPassbackMode(),
     *     'autoFit' => unset or true due to $adSlot->isAutoFit()
     *     'rtb' => unset or true due to $adSlot->getRtb()
     *     'exchanges' => unset or [... all supported exchanges ...] due to $adSlot->getRtb()
     *     'floorPrice' => unset or $adSlot->isAutoFit() due to $adSlot->getRtb()
     *     'tags' => [... all tags ...]
     * }
     *
     * e.g:
     * {
     *     "id": "1",
     *     "type": "display",
     *     "width": "200",
     *     "height": "300",
     *     "passbackMode": "position",
     *     "autoFit": "true",
     *     "rtb": "true",
     *     "exchanges": ["openX", "rubicon", , "indexExchange"],
     *     "floorPrice": "16.3",
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
     * @param DisplayAdSlotInterface $adSlot
     * @return array
     */
    protected function createDisplayAdSlotCacheData(DisplayAdSlotInterface $adSlot)
    {
        $data = [
            'id' => $adSlot->getId(),
            'type' => 'display',
            'width' => $adSlot->getWidth(),
            'height' => $adSlot->getHeight(),
            'passbackMode' => $adSlot->getPassbackMode(),
            'jsTag' => $this->getTagGenerator()->createJsTags($adSlot),
            'tags' => [],
            'cpm' => $adSlot->getHbBidPrice()
        ];

        if ($adSlot->isAutoFit()) {
            $data['autoFit'] = true;
        }

        // update rtb cache data if supports
        if ($adSlot->isRTBEnabled()) {
            $data['rtb'] = true;
            $data['exchanges'] = $adSlot->getSite()->getPublisher()->getExchanges();
            $data['floorPrice'] = $adSlot->getFloorPrice();
        }

        //step 1. get and check adTags
        /** @var AdTagInterface[]|Collection $adTags */
        $adTags = $adSlot->getAdTags();

        if ($adTags instanceof Collection) {
            $adTags = $adTags->toArray();
        }

        if (empty($adTags)) {
            return $data;
        }


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

            $adTagBlacklist = $this->getDisplayBlacklistForAdTag($adTag);
            if (count($adTagBlacklist) > 0) {
                $dataItem['blacklist'] = implode(',', $adTagBlacklist);
                $dataItem['hasBlacklist'] = true;
            }

            $adTagWhiteList = $this->getDisplayWhiteListsForAdTag($adTag);
            if (count($adTagWhiteList) > 0) {
                $dataItem['whiteList'] = implode(',', $adTagWhiteList);
                $dataItem['hasWhiteList'] = true;
            }

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
        array_walk($groups,
            function ($groupData) use (&$data) {
                array_push($data['tags'], count($groupData) > 1 ? $groupData : $groupData[0]);

                return count($groupData) > 1 ? $groupData : $groupData[0];
            }
        );

        //step 4. return data
        return $data;
    }

    /**
     * create as dynamic, format as:
     *
     * [
     *     'id' => $adSlot->getId(),
     *     'type' => 'dynamic',
     *     'expressions' => [
     *          'expressions' => [... expression, expectAdSlot ...],
     *          'defaultAdSlot'
     *     ],
     *     'slots' => [... array of static-adSlot-cache ...]
     * ]
     *
     * @param DynamicAdSlotInterface $dynamicAdSlot
     * @return array
     */
    private function createAdSlotCacheDataDynamic(DynamicAdSlotInterface $dynamicAdSlot)
    {
        $data = [
            'id' => $dynamicAdSlot->getId(),
            'type' => 'dynamic',
            'native' => $dynamicAdSlot->isSupportedNative(),
            'expressions' => [],
            'defaultAdSlot' => $dynamicAdSlot->getDefaultAdSlot() instanceof ReportableAdSlotInterface ? $dynamicAdSlot->getDefaultAdSlot()->getId() : null,
            'jsTag' => $this->getTagGenerator()->createJsTags($dynamicAdSlot),
            'slots' => []
        ];

        // remove native key if it is not native dynamic ad slot
        if (!$dynamicAdSlot->isSupportedNative()) {
            unset($data['native']);
        }

        ////adSlot (as defaultAdSlot) from DynamicAdSlot:
        $adSlotsForSelecting = array();
        if ($dynamicAdSlot->getDefaultAdSlot() instanceof ReportableAdSlotInterface) {
            $adSlotsForSelecting[] = $dynamicAdSlot->getDefaultAdSlot();
        }

        //check expressions
        /** @var ExpressionInterface[] $expressions */
        $expressions = $dynamicAdSlot->getExpressions()->toArray();
        if (is_array($expressions) && !empty($expressions)) {
            //step 1. set 'expressions' for data: get expressionInJS of each expression in expressions
            array_walk($expressions,
                function (ExpressionInterface $expression) use (&$data) {
                    array_push($data['expressions'], $expression->getExpressionInJs());

                    $expressionDescriptor = $expression->getExpressionDescriptor();
                    $groupVals = $expressionDescriptor['groupVal'];
                    if (!is_array($groupVals)) {
                        return;
                    }

                    $this->updateServerVars($groupVals, $data);
                }
            );

            //step 2. get all AdSlots related to DynamicAdSlot and Expressions
            ////adSlots from expressionInJS of Expressions
            $tmpAdSlotsForSelecting = array_map(function (ExpressionInterface $expression) {
                return $expression->getExpectAdSlot();
            },
                $expressions
            );

            $adSlotsForSelecting = array_merge($adSlotsForSelecting, $tmpAdSlotsForSelecting);
        }

        $adSlotsForSelecting = array_unique($adSlotsForSelecting);

        //step 3. build 'slots' for data
        array_walk($adSlotsForSelecting, function (ReportableAdSlotInterface $adSlot) use (&$data) {
            if ($adSlot instanceof DisplayAdSlotInterface) {
                $data['slots'][$adSlot->getId()] = $this->createDisplayAdSlotCacheData($adSlot);
            } else if ($adSlot instanceof NativeAdSlotInterface) {
                $data['slots'][$adSlot->getId()] = $this->createNativeAdSlotCacheData($adSlot);
            }
        }
        );

        //step 5. return data
        return $data;
    }

    /**
     * @param $groupVals
     * @param $data
     */
    protected function updateServerVars(array $groupVals, &$data)
    {
        foreach ($groupVals as $groupVal) {
            if (!array_key_exists('groupVal', $groupVal)) {
                $varName = $groupVal['var'];
                if (in_array($varName, ExpressionInJsGenerator::$SERVER_VARS) && (!isset($data['serverVars']) || !in_array($varName, $data['serverVars']))) {
                    $data['serverVars'][] = $varName;
                }
            } else {
                $this->updateServerVars($groupVal['groupVal'], $data);
            }
        }
    }

    protected function createNativeAdSlotCacheData(NativeAdSlotInterface $nativeAdSlot)
    {
        $data = [
            'id' => $nativeAdSlot->getId(),
            'type' => 'native',
            'tags' => []
        ];

        //step 1. get and check adTags
        /** @var AdTagInterface[]|Collection $adTags */
        $adTags = $nativeAdSlot->getAdTags();

        if ($adTags instanceof Collection) {
            $adTags = $adTags->toArray();
        }

        if (empty($adTags)) {
            return $data;
        }

        foreach ($adTags as $adTag) {
            if (!$adTag->isActive()) {
                continue;
            }

            $dataItem = [
                'id' => $adTag->getId(),
                'tag' => $adTag->getHtml(),
            ];

            $adTagBlacklist = $this->getDisplayBlacklistForAdTag($adTag);
            if (count($adTagBlacklist) > 0) {
                $dataItem['blacklist'] = implode(',', $adTagBlacklist);
                $dataItem['hasBlacklist'] = true;
            }

            $adTagWhiteList = $this->getDisplayWhiteListsForAdTag($adTag);
            if (count($adTagWhiteList) > 0) {
                $dataItem['whiteList'] = implode(',', $adTagWhiteList);
                $dataItem['hasWhiteList'] = true;
            }

            if (null !== $adTag->getFrequencyCap()) {
                $dataItem['cap'] = $adTag->getFrequencyCap();
            }

            if (null !== $adTag->getRotation()) {
                $dataItem['rot'] = $adTag->getRotation();
            }

            array_push($data['tags'], $dataItem);
        }

        return $data;
    }

    private function getDisplayBlacklistForAdTag(AdTagInterface $adTag)
    {
        $blacklists = [];

        $displayBlacklists = $adTag->getAdNetwork()->getDisplayBlacklists();
        foreach ($displayBlacklists as $displayBlacklist) {
            if ($displayBlacklist instanceof DisplayBlacklistInterface) {
                $blacklists[] = sprintf('%s', $displayBlacklist->getId());
            }
        }

        return array_values(array_unique($blacklists));
    }


    private function getDisplayWhiteListsForAdTag(AdTagInterface $adTag)
    {
        $whiteLists = [];

        $displayWhiteLists = $adTag->getAdNetwork()->getDisplayWhiteLists();
        foreach ($displayWhiteLists as $displayWhiteList) {
            if ($displayWhiteList instanceof DisplayWhiteListInterface) {
                $whiteLists[] = sprintf('%s', $displayWhiteList->getId());
            }
        }

        return array_values(array_unique($whiteLists));
    }

    /**
     * @return TagGenerator
     */
    abstract protected function getTagGenerator();

    /**
     * @return string
     */
    abstract protected function getBlacklistPrefix();

    /**
     * @return string
     */
    abstract protected function getWhiteListPrefix();
}