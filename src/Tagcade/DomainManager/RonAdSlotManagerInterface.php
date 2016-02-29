<?php

namespace Tagcade\DomainManager;


use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\ReportableLibraryAdSlotInterface;
use Tagcade\Model\Core\RonAdSlotInterface;
use Tagcade\Model\Core\Segment;
use Tagcade\Model\Core\SegmentInterface;
use Tagcade\Model\User\Role\PublisherInterface;

interface RonAdSlotManagerInterface extends ManagerInterface
{
    /**
     * Get all RonAdSlot that a specific publisher had created before
     *
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

    /**
     *
     * @param RonAdSlotInterface $ronAdSlot
     * @param $domain
     * @return BaseAdSlotInterface
     * @throw LogicException
     */
    public function createAdSlotFromRonAdSlotAndDomain(RonAdSlotInterface $ronAdSlot, $domain);

    /**
     * @param null $limit
     * @param null $offset
     * @return array
     */
    public function getRonAdSlotsWithoutSegment($limit = null, $offset = null);

    /**
     * @param ReportableLibraryAdSlotInterface $libraryAdSlot
     * @param array $ronAdSlotSegments
     * @return mixed
     */
    public function checkLibraryAdSlotReferredByRonAdSlotExistedAndCreate(ReportableLibraryAdSlotInterface $libraryAdSlot, array $ronAdSlotSegments);

    /**
     * Get all Ron Display AdSlot that a specific publisher had created before
     *
     * @param PublisherInterface $publisher
     * @param null|int $limit
     * @param null|int $offset
     * @return array
     */
    public function getRonDisplayAdSlotsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null);
}