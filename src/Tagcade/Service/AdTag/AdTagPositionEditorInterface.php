<?php

namespace Tagcade\Service\AdTag;

use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\SiteInterface;

interface AdTagPositionEditorInterface
{

    /**
     * @param AdNetworkInterface $adNetwork
     * @param $position
     * @param SiteInterface[] $sites
     * @return int number of ad tags get updated
     */
    public function setAdTagPositionForAdNetworkAndSites(AdNetworkInterface $adNetwork, $position, $sites);


} 