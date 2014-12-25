<?php

namespace Tagcade\Service\Core\AdNetwork;

use Doctrine\ORM\EntityManagerInterface;
use Tagcade\Domain\DTO\Core\SiteStatus;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\AdSlotInterface;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\User\Role\PublisherInterface;

class AdNetworkService implements AdNetworkServiceInterface
{

    /**
     * @var EntityManagerInterface
     */
    private $em;

    function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function pauseAdNetwork(AdNetworkInterface $adNetwork)
    {
        $adNetwork->setActive(false);

        $this->em->flush();
    }

    public function updateActiveStateBySingleSiteForAdNetwork(AdNetworkInterface $adNetwork, SiteInterface $site, $active = false)
    {

        foreach ($adNetwork->getAdTags() as $adTag) {
            /**
             * @var AdTagInterface $adTag
             */
            if ($adTag->getAdSlot()->getSite() == $site && $active != $adTag->isActive()) {
                $adTag->setActive($active);
            }
        }

        $this->em->flush();
    }

    public function getSitesForAdNetworkFilterPublisher(AdNetworkInterface $adNetwork, PublisherInterface $publisher = null)
    {
        $sites = [];

        $siteStatus = [];

        foreach ($adNetwork->getAdTags() as $adTag) {
            /**
             * @var AdTagInterface $adTag
             */
            $site = $adTag->getAdSlot()->getSite();

            if ($publisher != null && $site->getPublisher()->getId() != $publisher->getId()) {
                continue;
            }

            if (!in_array($site, $sites)) {
                $sites[] = $site;
                $siteStatus[] = new SiteStatus($site, $this->_isSiteActiveForAdNetwork($adNetwork, $site));
            }

            unset($site);
        }

        return $siteStatus;
    }

    private function _isSiteActiveForAdNetwork(AdNetworkInterface $adNetwork, SiteInterface $site)
    {

        $activeTags = array_filter(

            $adNetwork->getAdTags()->toArray(),

            function(AdTagInterface $adTag) use ($site)
            {
                return $adTag->getAdSlot()->getSite() == $site && $adTag->isActive();
            }
        );

        return count($activeTags) > 0;
    }


}