<?php

namespace Tagcade\DomainManager;


use Tagcade\Model\Core\LibraryNativeAdSlotInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\User\Role\PublisherInterface;

interface LibraryNativeAdSlotManagerInterface extends ManagerInterface {
    /**
     * @param PublisherInterface $publisher
     * @param int|null $limit
     * @param int|null $offset
     * @return LibraryNativeAdSlotInterface[]
     */
    public function getLibraryNativeAdSlotsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null);
}