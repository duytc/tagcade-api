<?php

namespace Tagcade\Repository\Core;

use Doctrine\Common\Persistence\ObjectRepository;
use Tagcade\Model\Core\SegmentInterface;
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

    /**
     * @param PublisherInterface $publisher
     * @param string $type
     * @param int $limit
     * @param int $offset
     * @return SegmentInterface[]
     */
    public function getSegmentsByTypeForPublisher(PublisherInterface $publisher, $type = null, $limit = null, $offset = null);
}