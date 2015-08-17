<?php

namespace Tagcade\Service\Core\AdNetwork;

use Tagcade\Domain\DTO\Core\SiteStatus;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\User\Role\PublisherInterface;

interface AdNetworkServiceInterface
{
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


}