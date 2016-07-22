<?php

namespace Tagcade\Service\Core\AdNetwork;

use Doctrine\ORM\EntityManagerInterface;
use Tagcade\Domain\DTO\Core\SiteStatus;
use Tagcade\Entity\Core\AdNetwork;
use Tagcade\Entity\Core\AdTag;
use Tagcade\Entity\Core\Site;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\AdNetworkPartnerInterface;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\SubPublisherInterface;
use Tagcade\Model\User\Role\UserRoleInterface;
use Tagcade\Repository\Core\AdNetworkRepositoryInterface;
use Tagcade\Repository\Core\AdTagRepositoryInterface;
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
            /** @var AdTagInterface $adTag */
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
        /** @var AdTagRepositoryInterface $adTagRepository */
        $adTagRepository = $this->em->getRepository(AdTag::class);
        $siteIds = $adTagRepository->getActiveSitesForAdNetworkFilterPublisher($adNetwork, $publisher);
        foreach($siteIds as $siteId) {
            $site = $siteRepository->find($siteId);
            if ($site instanceof SiteInterface) {
                $active = $adTagRepository->countAdTagForSiteAndAdNetworkByStatus($adNetwork, $site, AdTagInterface::ACTIVE);
                $paused = $adTagRepository->countAdTagForSiteAndAdNetworkByStatus($adNetwork, $site, AdTagInterface::PAUSED);
                $siteStatus[] = new SiteStatus($site, $active, $paused);
            }
        }

        return $siteStatus;
    }
}