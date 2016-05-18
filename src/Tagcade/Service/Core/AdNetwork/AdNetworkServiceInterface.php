<?php

namespace Tagcade\Service\Core\AdNetwork;

use Tagcade\Domain\DTO\Core\SiteStatus;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\AdNetworkPartnerInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\UserRoleInterface;

interface AdNetworkServiceInterface
{

    public function getSitesForPartnerFilterPublisherAndDomain(AdNetworkPartnerInterface $partner, PublisherInterface $publisher, $domain = null);

    /**
     * @param AdNetworkInterface $adNetwork
     * @param PublisherInterface $publisher null if not filter by any publisher
     * @return SiteStatus[]
     */
    public function getSitesForAdNetworkFilterPublisher(AdNetworkInterface $adNetwork, PublisherInterface $publisher = null);

    /**
     * @param AdNetworkInterface $adNetwork
     * @param PublisherInterface $publisher
     * @return SiteInterface[]
     */
    public function getActiveSitesForAdNetworkFilterPublisher(AdNetworkInterface $adNetwork, PublisherInterface $publisher = null);

    /**
     * @param AdNetworkInterface $adNetwork
     * @param UserRoleInterface $userRole
     * @return SiteInterface[]
     */
    public function getSitesForAdNetworkFilterUserRole(AdNetworkInterface $adNetwork, UserRoleInterface $userRole);
}