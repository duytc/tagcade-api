<?php

namespace Tagcade\Service\Core\AdTag;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\PersistentCollection;
use Tagcade\DomainManager\AdTagManagerInterface;
use Tagcade\DomainManager\LibrarySlotTagManagerInterface;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Exception\RuntimeException;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\DisplayAdSlotInterface;
use Tagcade\Model\Core\LibraryDisplayAdSlotInterface;
use Tagcade\Model\Core\LibrarySlotTagInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\Hierarchy\Platform\AdSlotInterface;

class AdTagPositionEditor implements AdTagPositionEditorInterface
{
    /**
     * @var AdTagManagerInterface
     */
    private $adTagManager;

    /**
     * @var LibrarySlotTagManagerInterface
     */
    private $librarySlotTagManager;
    /**
     * @var EntityManagerInterface
     */
    private $em;

    function __construct(AdTagManagerInterface $adTagManager, LibrarySlotTagManagerInterface $librarySlotTagManager,  EntityManagerInterface $em)
    {
        $this->adTagManager = $adTagManager;
        $this->em = $em;
        $this->librarySlotTagManager = $librarySlotTagManager;
    }

    public function setAdTagPositionForAdNetworkAndSites(AdNetworkInterface $adNetwork, $position, $sites = null)
    {
        if (!is_int($position) || $position < 1) {
            throw new InvalidArgumentException('expect positive integer for ad tag position');
        }

        if (null === $sites || (is_array($sites) && count($sites) < 1)) {
            return $this->setAdTagPositionForAdNetwork($adNetwork, $position);
        }

        $filterSites = [];

        if(!is_array($sites)) {
            $filterSites[] = $sites;
        }
        else {
            $filterSites = array_filter($sites, function($site) {return $site instanceof SiteInterface;});
        }

        if (!current($filterSites) instanceof SiteInterface) {
            throw new InvalidArgumentException('Expect site interface');
        }

        $adTags = $this->adTagManager->getAdTagsForAdNetworkAndSites($adNetwork, $filterSites);

        return $this->updatePosition($adTags, $position);

    }

    /**
     * @param DisplayAdSlotInterface $adSlot
     * @param array $newAdTagOrderIds array of array [[adtag1_pos_1, adtag2_pos_1], [adtag3_pos2]]
     * @return \Tagcade\Model\Core\AdTagInterface[]
     */
    public function setAdTagPositionForAdSlot(DisplayAdSlotInterface $adSlot, array $newAdTagOrderIds) {
        $adTags = $adSlot->getAdTags()->toArray();

        if (empty($adTags)) {
            return [];
        }

        $adTagMap = array();
        foreach ($adTags as $adTag) {
            /**
             * @var AdTagInterface $adTag
             */
            $adTagMap[$adTag->getId()] = $adTag;
        }

        $pos = 1;
        $orderedAdTags = [];
        $processedAdTags = [];

        foreach ($newAdTagOrderIds as $adTagIds) {
            foreach ($adTagIds as $adTagId) {
                if (!array_key_exists($adTagId, $adTagMap)) {
                    throw new RuntimeException('One of ids not existed in ad tag list of current ad slot');
                }

                if (in_array((int)$adTagId, $processedAdTags)) {
                    throw new RuntimeException('There is duplication of ad tag');
                }

                $adTag = $adTagMap[$adTagId];
                if ($pos != $adTag->getPosition()) {
                    $adTag->setPosition($pos);
                    $this->adTagManager->save($adTag);
                    //update all sibling AdTag
//                    $librarySlotTag = $this->librarySlotTagManager->getByLibraryAdSlotAndLibraryAdTagAndRefId($adSlot->getLibraryAdSlot(), $adTag->getLibraryAdTag(), $adTag->getRefId());
//
//                    if($librarySlotTag instanceof LibrarySlotTagInterface) {
//
//                        /** @var LibrarySlotTagInterface $librarySlotTag */
//                        $siblingTags = $librarySlotTag->getLibraryAdTag()->getAdTags()->toArray();
//                        $siblingTags = array_filter($siblingTags, function(AdTagInterface $t) use($adSlot, $librarySlotTag){
//                            return $t->getAdSlot()->getLibraryAdSlot()->getId() == $adSlot->getLibraryAdSlot()->getId() && $librarySlotTag->getRefId() == $t->getRefId();
//                        });
//
//                        array_map(function(AdTagInterface $sib) use($pos){
//                            $sib->setPosition($pos);
//                            $this->adTagManager->save($sib);
//                        },$siblingTags);
//                    }


                }

                $processedAdTags[] = $adTag->getId();
                $orderedAdTags[] = $adTag;
            }

            $pos ++;
        }

        $this->em->flush();

        return $orderedAdTags;
    }

    public function setAdTagPositionForLibraryAdSlot(LibraryDisplayAdSlotInterface $libraryAdSlot, array $newAdTagOrderIds) {
        $librarySlotTags = $this->librarySlotTagManager->getByLibraryAdSlot($libraryAdSlot);

        if($librarySlotTags instanceof PersistentCollection)  $librarySlotTags = $librarySlotTags->toArray();


        if (empty($librarySlotTags)) {
            return [];
        }


        $librarySlotTagsMap = array();
        foreach ($librarySlotTags as $librarySlotTag) {
            /** @var LibrarySlotTagInterface $librarySlotTag */
            $librarySlotTagsMap[$librarySlotTag->getId()] = $librarySlotTag;
        }

        $pos = 1;
        $orderedLibrarySlotTags = [];
        $processedLibrarySlotTags = [];

        foreach ($newAdTagOrderIds as $adTagIds) {
            foreach ($adTagIds as $adTagId) {
                if (!array_key_exists($adTagId, $librarySlotTagsMap)) {
                    throw new RuntimeException('One of ids not existed in ad tag list of current ad slot');
                }

                if (in_array((int)$adTagId, $processedLibrarySlotTags)) {
                    throw new RuntimeException('There is duplication of ad tag');
                }

                $librarySlotTag = $librarySlotTagsMap[$adTagId];
                if ($pos != $librarySlotTag->getPosition()) {
                    $librarySlotTag->setPosition($pos);
                    $this->librarySlotTagManager->save($librarySlotTag);
                }

                $processedLibrarySlotTags[] = $librarySlotTag->getId();
                $orderedLibrarySlotTags[] = $librarySlotTag;
            }

            $pos ++;
        }

        $this->em->flush();

        return $orderedLibrarySlotTags;
    }


    /**
     * @param AdNetworkInterface $adNetwork
     * @param int $position
     * @return int number of ad tags get position updated
     */
    protected function setAdTagPositionForAdNetwork(AdNetworkInterface $adNetwork, $position)
    {
        $adTags = $this->adTagManager->getAdTagsForAdNetwork($adNetwork);

        return $this->updatePosition($adTags, $position);
    }

    protected function updatePosition(array $adTags, $position)
    {
        $allTagsToBeUpdated = $adTags;
        $processedSlots = [];
        foreach($adTags as $adTag) {
            /**
             * @var AdTagInterface $adTag
             */
            $adSlot = $adTag->getAdSlot();
            if (!$adSlot instanceof DisplayAdSlotInterface) {
                continue;
            }

            $updatingSlots = $adSlot->getCoReferencedAdSlots()->toArray();
            if (count($updatingSlots) < 2) { // only one slot then there is no shared 
                continue;
            }

            $updatingSlots = array_filter(
                $updatingSlots,
                function (DisplayAdSlotInterface $adSlot) use(&$processedSlots){
                    if (!in_array($adSlot, $processedSlots)) {
                        $processedSlots[] = $adSlot;

                        return true;
                    }

                    return false;
                }
            );

            foreach ($updatingSlots as $adSlot) {
                /**
                 * @var BaseAdSlotInterface $adSlot
                 */
                $foundAdTags = $this->adTagManager->getAdTagsByLibraryAdSlotAndRefId($adSlot->getLibraryAdSlot(), $adTag->getRefId());

                array_walk(
                    $foundAdTags,
                    function(AdTagInterface $t) use(&$allTagsToBeUpdated) {
                        if (!in_array($t, $allTagsToBeUpdated)) {
                            $allTagsToBeUpdated[] = $t;
                        }
                    }
                );
            }
        }

        $updateCount = 0;
        array_walk(
            $allTagsToBeUpdated,
            function($adTag) use ($position, &$updateCount)
            {
                /**
                 * @var AdTagInterface $adTag
                 */
                if (!$adTag->getAdSlot() instanceof DisplayAdSlotInterface) {
                    return; // not updating position for other types of ad slot like native ad slot
                }
                if ($adTag->getPosition() != $position ) {

                    $adTag->setPosition($position);
                    $updateCount ++;
                }
            }
        );

        $this->em->flush(); //!important this will help to trigger update cache listener to refresh cache

        return $updateCount;
    }
}