<?php

namespace Tagcade\DomainManager;

use Tagcade\Model\Core\BaseLibraryAdSlotInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\Core\AdNetworkInterface;

interface SegmentManagerInterface extends ManagerInterface
{
    /**
     * @param PublisherInterface $publisher
     * @param null $limit
     * @param null $offset
     * @return array
     */
    public function getSegmentsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null);
}