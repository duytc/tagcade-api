<?php

namespace Tagcade\Service\TagLibrary;


use Tagcade\Model\Core\BaseLibraryAdSlotInterface;
use Tagcade\Model\Core\ChannelInterface;
use Tagcade\Model\Core\DynamicAdSlotInterface;
use Tagcade\Model\Core\LibraryDynamicAdSlotInterface;
use Tagcade\Model\Core\SiteInterface;

interface AdSlotGeneratorInterface
{
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
    public function generateTrueDefaultAdSlotAndExpectAdSlotInExpressionsForLibraryDynamicAdSlotBySite(LibraryDynamicAdSlotInterface $libraryDynamicAdSlot, SiteInterface $site);

    /**
     * Generate ad slot for channels and sites
     * @param BaseLibraryAdSlotInterface $libraryAdSlot
     * @param array $channels
     * @param array $sites
     * @return mixed
     */
    public function generateAdSlotFromLibraryForChannelsAndSites(BaseLibraryAdSlotInterface $libraryAdSlot, array $channels, array $sites);

    /**
     * generate DisplayAdSlot From LibraryDisplayAdSlot For Channels
     * @param BaseLibraryAdSlotInterface $libraryAdSlot
     * @param ChannelInterface[] $channels
     * @return int number of links created
     */
    public function generateAdSlotFromLibraryForChannels(BaseLibraryAdSlotInterface $libraryAdSlot, array $channels);

    /**
     * generate DisplayAdSlot From LibraryDisplayAdSlot For Sites
     * @param BaseLibraryAdSlotInterface $libraryAdSlot
     * @param SiteInterface[] $sites
     * @param int $returnAdSlot
     * @return int number of links created
     */
    public function generateAdSlotFromLibraryForSites(BaseLibraryAdSlotInterface $libraryAdSlot, array $sites, $returnAdSlot = 0);
}