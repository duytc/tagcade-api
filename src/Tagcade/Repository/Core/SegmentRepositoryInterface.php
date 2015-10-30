<?php

namespace Tagcade\Repository\Core;

use Doctrine\Common\Persistence\ObjectRepository;
use Tagcade\Model\User\Role\PublisherInterface;

interface SegmentRepositoryInterface extends ObjectRepository
{
    /**
     * @param PublisherInterface $publisher
     * @param int|null $limit
     * @param int|null $offset
     * @return array
     */
    public function getSegmentsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null);
}