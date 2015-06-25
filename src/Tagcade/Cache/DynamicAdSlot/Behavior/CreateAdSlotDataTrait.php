<?php

namespace Tagcade\Cache\DynamicAdSlot\Behavior;


use Doctrine\Common\Collections\Collection;
use Tagcade\Exception\LogicException;
use Tagcade\Model\Core\AdSlotInterface;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\DynamicAdSlotInterface;
use Tagcade\Model\Core\ExpressionInterface;
use Tagcade\Model\Core\NativeAdSlotInterface;
use Tagcade\Model\Core\ReportableAdSlotInterface;
use Tagcade\Model\ModelInterface;

trait CreateAdSlotDataTrait {


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

        throw new LogicException(sprintf('Do not support cache v2 of ', get_class($model)));

    }

    /**
     * create as display, format as:
     *
     * {
     *     'id' => $adSlot->getId(),
     *     'type' => 'display',
     *     'tags' => [... all tags ...]
     * }
     *
     * e.g:
     * {
     *     "id": "1",
     *     "type": "display",
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
    protected function createDisplayAdSlotCacheData(AdSlotInterface $adSlot)
    {
        $data = [
            'id' => $adSlot->getId(),
            'type' => 'display',
            'width' => $adSlot->getWidth(),
            'height' => $adSlot->getHeight(),
            'tags' => []
        ];

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
            'native' =>  $dynamicAdSlot->isSupportedNative(),
            'expressions' => [],
            'defaultAdSlot' => $dynamicAdSlot->getDefaultAdSlot() instanceof ReportableAdSlotInterface ? $dynamicAdSlot->getDefaultAdSlot()->getId() : null,
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
                function(ExpressionInterface $expression ) use (&$data){
                    array_push($data['expressions'], $expression->getExpressionInJs());
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
        array_walk($adSlotsForSelecting, function(ReportableAdSlotInterface $adSlot) use (&$data){
                if ($adSlot instanceof AdSlotInterface) {
                    $data['slots'][$adSlot->getId()] =  $this->createDisplayAdSlotCacheData($adSlot);
                }else if ($adSlot instanceof NativeAdSlotInterface) {
                    $data['slots'][$adSlot->getId()] =  $this->createNativeAdSlotCacheData($adSlot);
                }
            }
        );

        //step 5. return data
        return $data;
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
}