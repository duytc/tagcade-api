<?php

namespace Tagcade\Service\Core\AdTag;

use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\DisplayAdSlotInterface;
use Tagcade\Model\Core\LibraryDisplayAdSlotInterface;
use Tagcade\Model\Core\LibrarySlotTagInterface;
use Tagcade\Model\Core\SiteInterface;

interface AdTagPositionEditorInterface
{
    /**
     * set AdTag Position For AdNetwork And Sites (optional, one or array or null for all),
     * also, we support auto-Increase-Position(shift down) for all ad tags of other ad network
     *
     * @param AdNetworkInterface $adNetwork
     * @param int $position
     * @param null|SiteInterface|SiteInterface[] $sites optional
     * @param bool $autoIncreasePosition optional, true if need shift down
     * @return int
     */
    public function setAdTagPositionForAdNetworkAndSites(AdNetworkInterface $adNetwork, $position, $sites = null, $autoIncreasePosition = false);

    /**
     * @param DisplayAdSlotInterface $adSlot
     * @param array $newAdTagOrderIds
     * @return AdTagInterface[]
     */
    public function setAdTagPositionForAdSlot(DisplayAdSlotInterface $adSlot, array $newAdTagOrderIds);

    /**
     * Update positions for tags in a library
     * @param LibraryDisplayAdSlotInterface $libraryAdSlot
     * @param array $newAdTagOrderIds
     * @return LibrarySlotTagInterface[]
     */
    public function setAdTagPositionForLibraryAdSlot(LibraryDisplayAdSlotInterface $libraryAdSlot, array $newAdTagOrderIds);
}