<?php

namespace Tagcade\Service\Core\AdNetwork;

use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\SiteInterface;

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

}