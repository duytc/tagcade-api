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
     * @return $this
     */
    public function pauseAdNetwork(AdNetworkInterface $adNetwork);

    /**
     * @param AdNetworkInterface $adNetwork
     * @param SiteInterface $site
     * @param bool $active
     * @return $this
     */
    public function updateActiveStateBySingleSiteForAdNetwork(AdNetworkInterface $adNetwork, SiteInterface $site, $active = false);


    /**
     * @param AdNetworkInterface $adNetwork
     * @param PublisherInterface $publisher null if not filter by any publisher
     * @return SiteStatus[]
     */
    public function getSitesForAdNetworkFilterPublisher(AdNetworkInterface $adNetwork, PublisherInterface $publisher = null);

}