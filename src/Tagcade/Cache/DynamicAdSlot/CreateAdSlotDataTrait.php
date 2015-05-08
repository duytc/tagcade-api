<?php

namespace Tagcade\Cache\DynamicAdSlot;


use Doctrine\Common\Collections\Collection;
use Tagcade\Model\Core\AdSlotInterface;
use Tagcade\Model\Core\AdTagInterface;

trait CreateAdSlotDataTrait {
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
    protected function createAdSlotCacheDataStatic(AdSlotInterface $adSlot)
    {
        $data = [
            'id' => $adSlot->getId(),
            'type' => 'static',
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
} 