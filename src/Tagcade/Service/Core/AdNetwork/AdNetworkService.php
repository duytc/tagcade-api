<?php

namespace Tagcade\Service\Core\AdNetwork;

use Doctrine\ORM\EntityManagerInterface;
use Tagcade\Domain\DTO\Core\SiteStatus;
use Tagcade\Entity\Core\AdNetwork;
use Tagcade\Entity\Core\Site;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\AdNetworkPartnerInterface;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\ReportableAdSlotInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\SubPublisherInterface;
use Tagcade\Model\User\Role\UserRoleInterface;
use Tagcade\Repository\Core\AdNetworkRepositoryInterface;
use Tagcade\Repository\Core\SiteRepositoryInterface;

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


    public function getSitesForPartnerFilterPublisherAndDomain(AdNetworkPartnerInterface $partner, PublisherInterface $publisher, $domain = null)
    {
        /**
         * @var AdNetworkRepositoryInterface $adNetworkRepository
         */
        $adNetworkRepository = $this->em->getRepository(AdNetwork::class);

        $adNetworks = $adNetworkRepository->getAdNetworksForPublisherAndPartner($publisher, $partner);
        $sites = [];

        foreach ($adNetworks as $nw) {
            $tmpSites = $this->getSitesForAdNetworkFilterUserRole($nw, $publisher);
            foreach ($tmpSites as $st) {
                /**
                 * @var SiteInterface $st
                 */

                if ($domain != null && $st->getDomain() != $domain) {
                    continue;
                }

                if (!in_array($st, $sites)) {
                    $sites[] = $st;
                }
            }
        }

        return $sites;
    }
    /**
     * @inheritdoc
     */
    public function getSitesForAdNetworkFilterUserRole(AdNetworkInterface $adNetwork, UserRoleInterface $userRole)
    {
        $sites = [];

        foreach ($adNetwork->getAdTags() as $adTag) {
            /**
             * @var AdTagInterface $adTag
             */
            $site = $adTag->getAdSlot()->getSite();

            $siteDirectOwnerId = null;
            if ($userRole instanceof SubPublisherInterface) {
                $subPublisher = $site->getSubPublisher();
                if (!$subPublisher instanceof SubPublisherInterface) {
                    continue;
                }

                $siteDirectOwnerId = $subPublisher->getId();
            }
            else if ($userRole instanceof PublisherInterface) {
                $siteDirectOwnerId = $site->getPublisher()->getId();
            }

            if ($siteDirectOwnerId != null && $siteDirectOwnerId != $userRole->getId()) {
                continue;
            }

            if (!in_array($site, $sites)) {
                $sites[] = $site;
            }
        }

        return $sites;
    }

    public function getSitesForAdNetworkFilterPublisher(AdNetworkInterface $adNetwork, PublisherInterface $publisher = null)
    {
        $siteStatus = [];

        /** @var SiteRepositoryInterface $siteRepository */
        $siteRepository = $this->em->getRepository(Site::class);
        $sites = $siteRepository->getSiteHavingAdTagBelongsToAdNetworkFilterByPublisher($adNetwork, $publisher);

        foreach($sites as $site) {
            $siteStatus[] = new SiteStatus($site, $adNetwork);
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