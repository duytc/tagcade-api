<?php

namespace Tagcade\DomainManager;

use Tagcade\Model\User\Role\PublisherInterface;

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