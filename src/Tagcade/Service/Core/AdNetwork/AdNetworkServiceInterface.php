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
     * @param SiteInterface[] $sites
     * @return $this
     */
    public function pauseAdNetworkBySites(array $sites);

}