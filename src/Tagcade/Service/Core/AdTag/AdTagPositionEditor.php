<?php

namespace Tagcade\Service\Core\AdTag;

use Doctrine\ORM\EntityManagerInterface;
use Tagcade\DomainManager\AdTagManagerInterface;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Core\AdNetworkInterface;
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

    /**
     * @param AdNetworkInterface $adNetwork
     * @param $position
     * @param SiteInterface[] $sites
     * @return int number of ad tags get updated
     */
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
     * @param AdNetworkInterface $adNetwork
     * @param int $position
     * @return int number of ad tags get position updated
     */
    protected  function setAdTagPositionForAdNetwork(AdNetworkInterface $adNetwork, $position)
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