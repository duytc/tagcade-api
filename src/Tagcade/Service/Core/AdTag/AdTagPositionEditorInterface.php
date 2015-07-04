<?php

namespace Tagcade\Service\Core\AdTag;

use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\DisplayAdSlotInterface;
use Tagcade\Model\Core\SiteInterface;

interface AdTagPositionEditorInterface
{

    /**
     * @param AdNetworkInterface $adNetwork
     * @param $position
     * @param SiteInterface[] $sites
     * @return int number of ad tags get updated
     */
    public function setAdTagPositionForAdNetworkAndSites(AdNetworkInterface $adNetwork, $position, $sites = null);

    /**
     * @param DisplayAdSlotInterface $adSlot
     * @param array $newAdTagOrderIds
     * @return AdTagInterface[]
     */
    public function setAdTagPositionForAdSlot(DisplayAdSlotInterface $adSlot, array $newAdTagOrderIds);


} 