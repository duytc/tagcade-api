<?php

namespace Tagcade\Service\Core\AdNetwork;

use Doctrine\ORM\EntityManagerInterface;
use Tagcade\Domain\DTO\Core\SiteStatus;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\ReportableAdSlotInterface;
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
                $siteStatus[] = new SiteStatus($site, $adNetwork);
            }

            unset($site);
        }

        return $siteStatus;
    }

    public function getActiveSitesForAdNetworkFilterPublisher(AdNetworkInterface $adNetwork, PublisherInterface $publisher = null)
    {
        $sites = [];

        foreach ($adNetwork->getAdTags() as $adTag) {
            /**
             * @var AdTagInterface $adTag
             */
            $site = $adTag->getAdSlot()->getSite();

            if ($publisher != null && $site->getPublisher()->getId() != $publisher->getId()) {
                continue;
            }

            if (!in_array($site, $sites) && $this->_isSiteActiveForAdNetwork($adNetwork, $site)) {
                $sites[] = $site;
            }

            unset($site);
            unset($adTag);
        }

        return $sites;
    }

    private function _isSiteActiveForAdNetwork(AdNetworkInterface $adNetwork, SiteInterface $site)
    {

        $activeTags = array_filter(

            $adNetwork->getAdTags(),

            function(AdTagInterface $adTag) use ($site)
            {
                if (!$adTag->getAdSlot() instanceof ReportableAdSlotInterface) {
                    return false;
                }

                return $adTag->getAdSlot()->getSite() == $site && $adTag->isActive();
            }
        );

        return count($activeTags) > 0;
    }


}