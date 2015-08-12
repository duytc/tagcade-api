<?php


namespace Tagcade\Service\TagLibrary;


use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\SiteInterface;

interface CloneAdSlotInterface {
    /**
     * clone AdSlot
     * @param BaseAdSlotInterface $originAdSlot
     * @param string $newName
     * @param SiteInterface $site
     */
    public function cloneAdSlot(BaseAdSlotInterface $originAdSlot, $newName, SiteInterface $site = null);
}