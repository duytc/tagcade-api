<?php

namespace Tagcade\Service\Core\AdTag;

use Doctrine\ORM\EntityManagerInterface;
use Gedmo\Sortable\SortableListener;
use Tagcade\DomainManager\AdTagManagerInterface;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Exception\RuntimeException;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\AdSlotInterface;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\SiteInterface;

class AdTagPositionEditor implements AdTagPositionEditorInterface
{
    /**
     * @var AdTagManagerInterface
     */
    private $adTagManager;
    /**
     * @var EntityManagerInterface
     */
    private $em;

    function __construct(AdTagManagerInterface $adTagManager, EntityManagerInterface $em)
    {
        $this->adTagManager = $adTagManager;
        $this->em = $em;
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
     * @param AdSlotInterface $adSlot
     * @param array $newAdTagOrderIds array of array [[adtag1_pos_1, adtag2_pos_1], [adtag3_pos2]]
     * @return \Tagcade\Model\Core\AdTagInterface[]
     */
    public function setAdTagPositionForAdSlot(AdSlotInterface $adSlot, array $newAdTagOrderIds) {

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
                }

                $processedAdTags[] = $adTag->getId();
                $orderedAdTags[] = $adTag;
            }

            $pos ++;
        }

        $this->em->flush();

        return $orderedAdTags;
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
        $updateCount = 0;

        array_walk(
            $adTags,
            function($adTag) use ($position, &$updateCount)
            {
                /**
                 * @var AdTagInterface $adTag
                 */
                if (!$adTag->getAdSlot() instanceof AdSlotInterface) {
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