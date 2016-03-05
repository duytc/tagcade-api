<?php

namespace Tagcade\Cache\V2\Behavior;

use Doctrine\Common\Collections\Collection;
use Tagcade\Bundle\ApiBundle\Service\ExpressionInJsGenerator;
use Tagcade\Bundle\ApiBundle\Service\ExpressionInJsGeneratorInterface;
use Tagcade\Entity\Core\RonAdSlot;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Exception\LogicException;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\LibraryDisplayAdSlotInterface;
use Tagcade\Model\Core\LibraryDynamicAdSlotInterface;
use Tagcade\Model\Core\LibraryExpressionInterface;
use Tagcade\Model\Core\LibraryNativeAdSlotInterface;
use Tagcade\Model\Core\ReportableLibraryAdSlotInterface;
use Tagcade\Model\Core\RonAdSlotInterface;
use Tagcade\Model\Core\RonAdTagInterface;
use Tagcade\Model\ModelInterface;

trait CreateRonAdSlotDataTrait {
    /**
     * @param ModelInterface $model
     * @return array
     */
    protected function createCacheDataForEntity(ModelInterface $model)
    {
        if (!$model instanceof RonAdSlotInterface) {
            throw new InvalidArgumentException('expect RonAdSlotInterface');
        }

        return $this->createRonAdSlotCacheData($model);
    }



    /**
     * create Ron Ad Slot cache data, depend on type of library ad slot which ron ad slot uses
     * @param RonAdSlotInterface $ronAdSlot
     * @return array|null
     */
    protected function createRonAdSlotCacheData(RonAdSlotInterface $ronAdSlot)
    {
        $libAdSlot = $ronAdSlot->getLibraryAdSlot();

        if ($libAdSlot instanceof LibraryDisplayAdSlotInterface) {
            return $this->createRonAdSlotCacheDataDisplay($ronAdSlot);
        }

        if ($libAdSlot instanceof LibraryNativeAdSlotInterface) {
            return $this->createRonAdSlotCacheDataNative($ronAdSlot);
        }

        if ($libAdSlot instanceof LibraryDynamicAdSlotInterface) {
            return $this->createRonAdSlotCacheDataDynamic($ronAdSlot);
        }

        return null;
    }

    /**
     * create as ron display ad slot, format as:
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
     * @param RonAdSlotInterface $ronAdSlot
     * @return array|null
     */
    protected function createRonAdSlotCacheDataDisplay(RonAdSlotInterface $ronAdSlot)
    {
        $libDisplay = $ronAdSlot->getLibraryAdSlot();
        if (!$libDisplay instanceof LibraryDisplayAdSlotInterface) {
            throw new LogicException('expect ron ad slot for library display ad slot');
        }

        $data = [
            'id' => $ronAdSlot->getId(),
            'type' => 'display',
            'width' => $libDisplay->getWidth(),
            'height' => $libDisplay->getHeight(),
            'passbackMode' => $libDisplay->getPassbackMode(),
            'tags' => []
        ];

        $this->addSegmentsToCache($data, $ronAdSlot);

        if ($libDisplay->isAutoFit()) {
            $data['autoFit'] = true;
        }

        // update rtb cache data if supports
        if ($ronAdSlot->isRTBEnabled()) {
            $data['rtb'] = true;
            $data['exchanges'] = $ronAdSlot->getLibraryAdSlot()->getPublisher()->getExchanges();
            $data['floorPrice'] = $ronAdSlot->getFloorPrice();
        }

        //step 1. get and check adTags
        /** @var RonAdTagInterface[]|Collection $adTags */
        $adTags = $libDisplay->getLibSlotTags();

        if ($adTags instanceof Collection) {
            $adTags = $adTags->toArray();
        }

        if (empty($adTags)) {
            return $data;
        }


        //step 3. build 'tags' for data
        ////sort all adTags by position
        usort($adTags, function (RonAdTagInterface $a, RonAdTagInterface $b) {
                if ($a->getPosition() == $b->getPosition()) {
                    return 0;
                }
                return ($a->getPosition() < $b->getPosition()) ? -1 : 1;
            });

        ////group all adTags which same position into a group (array) with key is position
        ////each group can contain one or more items (as {'id', 'tag', 'cap', 'rot'})
        $groups = array();

        foreach ($adTags as $adTag) {
            if (!$adTag->isActive() || null !== $adTag->getDeletedAt()) {
                continue;
            }

            $dataItem = [
                'id' => $adTag->getId(),
                'tag' => $adTag->getLibraryAdTag()->getHtml(),
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
     * create Ron Ad Slot cache data for Native
     * @param RonAdSlotInterface $ronAdSlot
     * @return array|null
     */
    protected function createRonAdSlotCacheDataNative(RonAdSlotInterface $ronAdSlot)
    {
        $nativeLib = $ronAdSlot->getLibraryAdSlot();
        if (!$nativeLib instanceof LibraryNativeAdSlotInterface) {
            throw new LogicException('expecting ron ad slot associated to LibraryNativeInterface');
        }

        $data = [
            'id' => $ronAdSlot->getId(),
            'type' => 'native',
            'tags' => []
        ];

        $this->addSegmentsToCache($data, $ronAdSlot);

        //step 1. get and check adTags
        /** @var RonAdTagInterface[]|Collection $adTags */
        $adTags = $nativeLib->getLibSlotTags();

        if ($adTags instanceof Collection) {
            $adTags = $adTags->toArray();
        }

        if (empty($adTags)) {
            return $data;
        }

        foreach ($adTags as $adTag) {
            if (!$adTag->isActive() || null !== $adTag->getDeletedAt()) {
                continue;
            }

            $dataItem = [
                'id' => $adTag->getId(),
                'tag' => $adTag->getLibraryAdTag()->getHtml(),
            ];

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

    /**
     * create Ron Ad Slot cache data for Dynamic, format as:
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
     * @param RonAdSlotInterface $ronAdSlot
     * @return array|null
     */
    protected function createRonAdSlotCacheDataDynamic(RonAdSlotInterface $ronAdSlot)
    {
        $dynamicLib = $ronAdSlot->getLibraryAdSlot();
        if (!$dynamicLib instanceof LibraryDynamicAdSlotInterface) {
            throw new LogicException('expecting ron ad slot associated to LibraryDynamicAdSlotInterface');
        }

        $data = [
            'id' => $ronAdSlot->getId(),
            'type' => 'dynamic',
            'native' => $dynamicLib->isSupportedNative(),
            'expressions' => [],
            'slots' => []
        ];

        $this->addSegmentsToCache($data, $ronAdSlot);

        // remove native key if it is not native dynamic ad slot
        if (!$dynamicLib->isSupportedNative()) {
            unset($data['native']);
        }

        ////adSlot (as defaultAdSlot) from DynamicAdSlot:
        $ronAdSlotsForSelecting = array();
        $defaultLibraryAdSlot = $dynamicLib->getDefaultLibraryAdSlot();

        if ($defaultLibraryAdSlot instanceof ReportableLibraryAdSlotInterface) {
            $defaultRonAdSlot = $dynamicLib->getDefaultLibraryAdSlot()->getRonAdSlot();
            if (!$defaultRonAdSlot instanceof RonAdSlotInterface) {
                throw new LogicException('expect default is a ron slot');
            }

            $data['defaultAdSlot'] = $defaultRonAdSlot->getId();
            $ronAdSlotsForSelecting[] = $defaultRonAdSlot;
        }


        //check expressions
        /** @var LibraryExpressionInterface[] $libraryExpressions */
        $libraryExpressions = $dynamicLib->getLibraryExpressions()->toArray();
        if (is_array($libraryExpressions) && !empty($libraryExpressions)) {
            //step 1. set 'expressions' for data: get expressionInJS of each expression in expressions
            array_walk($libraryExpressions,
                function (LibraryExpressionInterface $expression) use (&$data) {
                    array_push($data['expressions'], $this->createExpressionInJs($expression));

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
            $tmpAdSlotsForSelecting = array_map(function (LibraryExpressionInterface $libraryExpression) {
                    return $libraryExpression->getExpectLibraryAdSlot()->getRonAdSlot();
                },
                $libraryExpressions
            );
            $tmpAdSlotsForSelecting = array_filter($tmpAdSlotsForSelecting, function($ronAdSlot) {
                return $ronAdSlot instanceof RonAdSlotInterface;
            });
            $ronAdSlotsForSelecting = array_merge($ronAdSlotsForSelecting, $tmpAdSlotsForSelecting);
        }

        $ronAdSlotsForSelecting = array_unique($ronAdSlotsForSelecting);

        //step 3. build 'slots' for data
        array_walk($ronAdSlotsForSelecting, function (RonAdSlotInterface $ronAdSlot) use (&$data) {
                $data['slots'][$ronAdSlot->getId()] = $this->createRonAdSlotCacheData($ronAdSlot);
            }
        );

        //step 5. return data
        return $data;
    }

    protected function addSegmentsToCache(&$data, RonAdSlotInterface $ronAdSlot)
    {
        $segments = $ronAdSlot->getSegments();
        if (count($segments) > 0) {
            foreach($segments as $segment) {
                $data['segments'][] = $segment->getId();
            }
        }
    }
    /**
     * @param $groupVals
     * @param $data
     */
    private function updateServerVars(array $groupVals, &$data)
    {
        foreach ($groupVals as $groupVal) {
            if (!array_key_exists('groupVal', $groupVal)) {
                $varName = $groupVal['var'];
                if (in_array($varName, ExpressionInJsGenerator::$SERVER_VARS)) {
                    $data['serverVars'][] = $varName;
                }
            } else {
                $this->updateServerVars($groupVal['groupVal'], $data);
            }
        }
    }

    protected function createExpressionInJs(LibraryExpressionInterface $expression)
    {
        $convertedExpression = $this->getExpressionInJsGenerator()->generateExpressionInJs($expression);

        if (null !== $convertedExpression) {

            $expInJs = [
                'vars'=> $convertedExpression['vars'],
                'expression' => $convertedExpression['expression']
            ];

            $libraryAdSlot = $expression->getExpectLibraryAdSlot();
            $ronAdSlot = $libraryAdSlot->getRonAdSlot();

            if ($ronAdSlot instanceof RonAdSlotInterface) {
                $expInJs['expectedAdSlot'] = $ronAdSlot->getId();
            }
            else {
                $expInJs['expectedLibraryAdSlot'] = $expression->getExpectLibraryAdSlot()->getId();
            }

            if (is_int($expression->getStartingPosition())) {
                $expInJs['startingPosition'] = $expression->getStartingPosition();
            }

            return $expInJs;
        }

        return null;
    }

    /**
     * @return ExpressionInJsGeneratorInterface
     */
    protected abstract function getExpressionInJsGenerator();
} 