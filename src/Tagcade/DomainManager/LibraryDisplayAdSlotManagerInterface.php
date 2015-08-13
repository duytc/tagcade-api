<?php

namespace Tagcade\DomainManager;


use Tagcade\Model\Core\LibraryDisplayAdSlotInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\User\Role\PublisherInterface;

interface LibraryDisplayAdSlotManagerInterface extends ManagerInterface {
    /**
     * @param PublisherInterface $publisher
     * @param int|null $limit
     * @param int|null $offset
     * @return LibraryDisplayAdSlotInterface[]
     */
    public function getLibraryDisplayAdSlotsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null);
}