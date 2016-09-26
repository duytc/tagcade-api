<?php

namespace Tagcade\Service\Core\VideoWaterfallTagItem;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Tagcade\DomainManager\VideoDemandAdTagManagerInterface;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Exception\RuntimeException;
use Tagcade\Model\Core\VideoDemandAdTagInterface;
use Tagcade\Model\Core\VideoWaterfallTagInterface;
use Tagcade\Entity\Core\VideoWaterfallTagItem;
use Tagcade\Model\Core\VideoWaterfallTagItemInterface;

class VideoWaterfallTagItemPositionEditor implements VideoWaterfallTagItemPositionEditorInterface
{
    /**
     * @var ContainerInterface
     *
     * Using the container to avoid circular dependency injection.
     */
    private $container;

    /** @var EntityManagerInterface */
    private $em;

    /**
     * @param ContainerInterface $container
     * @param EntityManagerInterface $em
     */
    function __construct(ContainerInterface $container, EntityManagerInterface $em)
    {
        $this->container = $container;
        $this->em = $em;
    }

    /**
     * @return VideoDemandAdTagManagerInterface
     */
    private function getVideoDemandAdTagManager()
    {
        return $this->container->get('tagcade.domain_manager.video_demand_ad_tag');
    }

    /**
     * @inheritdoc
     */
    public function setVideoWaterfallTagItemPositionForVideoWaterfallTag(VideoWaterfallTagInterface $videoWaterfallTag, array $newOrderedVideoWaterfallTagItems)
    {
        return $this->updatePositionForVideoWaterfallTagItems($videoWaterfallTag, $newOrderedVideoWaterfallTagItems);
    }

    /**
     * Update position of $videoWaterfallTagItems to new order list of $newVideoWaterfallTagItemOrderIds
     *
     * @param VideoWaterfallTagInterface $videoWaterfallTag
     * @param array $newOrderedVideoWaterfallTagItems
     * @return array
     * @throws \Doctrine\DBAL\ConnectionException
     */
    protected function updatePositionForVideoWaterfallTagItems(VideoWaterfallTagInterface $videoWaterfallTag, array $newOrderedVideoWaterfallTagItems)
    {
        $videoWaterfallTagItems = $videoWaterfallTag->getVideoWaterfallTagItems();

        if ($videoWaterfallTagItems instanceof Collection) {
            $videoWaterfallTagItems = $videoWaterfallTagItems->toArray();
        }

        if (empty($videoWaterfallTagItems)) {
            return [];
        }

        $videoWaterfallTagItemsMap = array();
        foreach ($videoWaterfallTagItems as $videoWaterfallTagItem) {
            /** @var VideoWaterfallTagItemInterface $videoWaterfallTagItem */
            $videoWaterfallTagItemsMap[$videoWaterfallTagItem->getId()] = $videoWaterfallTagItem;
        }

        $position = 1;
        $orderedVideoWaterfallTagItems = [];
        $processedVideoWaterfallTagItems = [];

        try {
            $this->em->getConnection()->beginTransaction();

            /* newVideoWaterfallTagItemOrderIds format:
             * [
             *      "videoWaterfallTagItems":[
             *          {
             *              "videoWaterfallTagItem":11,
             *              "videoDemandAdTags":[13]
             *          },
             *          ...
             *      ]
             * ]
             */
            foreach ($newOrderedVideoWaterfallTagItems as $idx => $videoWaterfallTagItemConfig) {
                // validate $videoWaterfallTagItemConfig
                if (!array_key_exists('videoWaterfallTagItem', $videoWaterfallTagItemConfig)
                    || !array_key_exists('videoDemandAdTags', $videoWaterfallTagItemConfig)
                ) {
                    throw new InvalidArgumentException('Missing key VideoWaterfallTagItem or VideoDemandAdTags in videoWaterfallTagItems');
                }

                $videoWaterfallTagItemId = $videoWaterfallTagItemConfig['videoWaterfallTagItem'];
                $videoDemandAdTagIds = $videoWaterfallTagItemConfig['videoDemandAdTags'];

                if (!is_array($videoDemandAdTagIds)) {
                    throw new InvalidArgumentException('Require VideoDemandAdTagIds is array in videoWaterfallTagItems');
                }

                if (count($videoDemandAdTagIds) < 1) {
                    $waterfallTagItemRepository = $this->em->getRepository(VideoWaterfallTagItem::class);
                    $waterfallTagItem = $waterfallTagItemRepository->find($videoWaterfallTagItemId);
                    if ($waterfallTagItem instanceof VideoWaterfallTagItemInterface) {
                        $waterfallTagItem->setDeletedAt(new \DateTime('today'));
                        $this->em->merge($waterfallTagItem);
                    }
                    continue;
                }

                // check if videoWaterfallTagItemId does not exist in current videoWaterfallTagItems
                if (null != $videoWaterfallTagItemId && !array_key_exists($videoWaterfallTagItemId, $videoWaterfallTagItemsMap)) {
                    throw new InvalidArgumentException('One of ids not existed in current videoWaterfallTagItems');
                }

                // check if duplicate set position for videoWaterfallTagItemId
                if (null != $videoWaterfallTagItemId && in_array($videoWaterfallTagItemId, $processedVideoWaterfallTagItems)) {
                    throw new InvalidArgumentException('There is duplication videoWaterfallTagItemId in newVideoWaterfallTagItemOrderIds');
                }

                if (null == $videoWaterfallTagItemId) {
                    // create new VideoWaterfallTagItem when split from a group (with submitted id == null)
                    $videoWaterfallTagItem = (new VideoWaterfallTagItem())
                        //->setPosition() // set below
                        //->setDeletedAt() // auto by Doctrine
                        //->setStrategy() // using default
                        //->setVideoDemandAdTags() // set below
                        ->setVideoWaterfallTag($videoWaterfallTag);

                    $this->em->persist($videoWaterfallTagItem);

                    // add new videoWaterfallTagItem to current videoWaterfallTag
                    $videoWaterfallTag->addVideoWaterfallTagItem($videoWaterfallTagItem);
                } else {
                    $videoWaterfallTagItem = $videoWaterfallTagItemsMap[$videoWaterfallTagItemId];
                }
                // set new position
                $videoWaterfallTagItem->setPosition($position);
                // set videoDemandAdTags
                $videoDemandAdTags = array_map(function ($videoDemandAdTagId) use ($videoWaterfallTagItem) {
                    $videoDemandAdTag = $this->getVideoDemandAdTagManager()->find($videoDemandAdTagId);

                    if (!$videoDemandAdTag instanceof VideoDemandAdTagInterface) {
                        throw new RuntimeException('One VideoDemandAdTag does not exist');
                    }

                    // set videoWaterfallTagItem back for VideoDemandAdTag
                    $videoDemandAdTag->setVideoWaterfallTagItem($videoWaterfallTagItem);

                    return $videoDemandAdTag;
                }, $videoDemandAdTagIds);

                $videoWaterfallTagItem->setVideoDemandAdTags($videoDemandAdTags);

                // save temp vars
                $processedVideoWaterfallTagItems[] = $videoWaterfallTagItem->getId();
                $orderedVideoWaterfallTagItems[] = $videoWaterfallTagItem;
                $position++;
            }

            $this->em->flush();
            $this->em->getConnection()->commit();
        } catch (\Exception $e) {
            $this->em->getConnection()->rollBack();
            throw new RuntimeException($e);
        }

        return $orderedVideoWaterfallTagItems;
    }
}