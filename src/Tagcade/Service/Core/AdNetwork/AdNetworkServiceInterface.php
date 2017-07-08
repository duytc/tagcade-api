<?php

namespace Tagcade\Service\Core\AdNetwork;

use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\PagerParam;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\UserRoleInterface;

interface AdNetworkServiceInterface
{
    /**
     * @param AdNetworkInterface $adNetwork
     * @param PagerParam $param
     * @param PublisherInterface $publisher null if not filter by any publisher
     * @return \Tagcade\Domain\DTO\Core\SiteStatus[]
     */
    public function getSitesForAdNetworkFilterPublisher(AdNetworkInterface $adNetwork, PagerParam $param, PublisherInterface $publisher = null);

    /**
     * @param AdNetworkInterface $adNetwork
     * @param UserRoleInterface $userRole
     * @return SiteInterface[]
     */
    public function getSitesForAdNetworkFilterUserRole(AdNetworkInterface $adNetwork, UserRoleInterface $userRole);
}