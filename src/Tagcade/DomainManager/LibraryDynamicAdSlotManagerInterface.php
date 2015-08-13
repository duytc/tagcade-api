<?php

namespace Tagcade\DomainManager;

use Tagcade\Model\Core\LibraryDynamicAdSlotInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\User\Role\PublisherInterface;

interface LibraryDynamicAdSlotManagerInterface extends ManagerInterface
{
    /**
     * @param PublisherInterface $publisher
     * @param int|null $limit
     * @param int|null $offset
     * @return LibraryDynamicAdSlotInterface[]
     */
    public function getLibraryDynamicAdSlotsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null);
}