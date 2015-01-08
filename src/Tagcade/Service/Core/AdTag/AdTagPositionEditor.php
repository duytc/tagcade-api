<?php

namespace Tagcade\Service\Core\AdTag;

use Doctrine\ORM\EntityManagerInterface;
use Gedmo\Sortable\SortableListener;
use Tagcade\DomainManager\AdTagManagerInterface;
use Tagcade\Exception\InvalidArgumentException;
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

    public function setAdTagPositionForAdSlot(AdSlotInterface $adSlot, array $newAdTagOrderIds) {

        $adTags = $adSlot->getAdTags()->toArray();

        if (empty($adTags)) {
            return [];
        }

        $adTagIds = array_map(function(AdTagInterface $adTag) {
                return $adTag->getId();
            }, $adTags);

        $adTags = array_combine($adTagIds, $adTags);

        if (count($newAdTagOrderIds) !== count(array_unique($newAdTagOrderIds))) {
            throw new InvalidArgumentException("Every ad tag id must be unique");
        }

        if (count(array_diff($newAdTagOrderIds, $adTagIds)) !== 0) {
            throw new InvalidArgumentException("There must be a matching new ad tag id order for every ad tag");
        }

        $orderedAdTags = array_map(function($id) use ($adTags) {
                return $adTags[$id];
            }, $newAdTagOrderIds);

        $position = 1;

        // remove SortableListener - Gedmo
        $sortableListener = null;
        foreach ($this->em->getEventManager()->getListeners('onFlush') as $listener) {
            if ($listener instanceof SortableListener) {
                $sortableListener = &$listener;
                $this->em->getEventManager()->removeEventSubscriber($listener);
                break;
            }
        }

        foreach($orderedAdTags as $adTag) {
            /** @var AdTagInterface $adTag */
            $adTag->setPosition($position);
            $position++;
        }

        $this->em->flush();

        if (null !== $sortableListener) {
            $this->em->getEventManager()->addEventSubscriber($sortableListener);
        }


        unset($adTag);

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