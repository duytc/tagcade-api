<?php

namespace Tagcade\Repository\Core;

use Doctrine\Common\Persistence\ObjectRepository;
use Tagcade\Model\Core\SegmentInterface;
use Tagcade\Model\User\Role\PublisherInterface;

interface RonAdSlotRepositoryInterface extends ObjectRepository
{
    /**
     * @param PublisherInterface $publisher
     * @param null|int $limit
     * @param null|int $offset
     * @return array
     */
    public function getRonAdSlotsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null);

    /**
     * @param SegmentInterface $segment
     * @param null $limit
     * @param null $offset
     * @return array
     */
    public function getRonAdSlotsForSegment(SegmentInterface $segment, $limit = null, $offset = null);
}