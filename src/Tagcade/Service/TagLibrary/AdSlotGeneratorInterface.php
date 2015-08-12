<?php

namespace Tagcade\Service\TagLibrary;


use Tagcade\Model\Core\DynamicAdSlotInterface;
use Tagcade\Model\Core\LibraryDynamicAdSlotInterface;
use Tagcade\Model\Core\SiteInterface;

interface AdSlotGeneratorInterface {
    /**
     * @param LibraryDynamicAdSlotInterface $libraryDynamicAdSlot
     * @param SiteInterface $site
     *
     * @return DynamicAdSlotInterface
     */
    public function getProspectiveDynamicAdSlotForLibraryAndSite(LibraryDynamicAdSlotInterface $libraryDynamicAdSlot, SiteInterface $site);

    /**
     * Generate expression and default ad slot associated to this site
     *
     * @param LibraryDynamicAdSlotInterface $libraryDynamicAdSlot
     * @param SiteInterface $site
     * @return mixed
     */
    public function generateTrueDefaultAdSlotAndExpressionsForLibraryDynamicAdSlotBySite(LibraryDynamicAdSlotInterface $libraryDynamicAdSlot, SiteInterface $site);

}