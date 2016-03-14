<?php

namespace Tagcade\Model\Core;

use Doctrine\ORM\PersistentCollection;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\SubPublisherInterface;
use Tagcade\Model\User\UserEntityInterface;

class Segment implements SegmentInterface
{
    protected $id;

    /** @var UserEntityInterface */
    protected $publisher;

    /** @var UserEntityInterface */
    protected $subPublisher;

    protected $name;
    /**
     * @var array
     */
    protected $ronAdSlotSegments;
    /**
     * @var \DateTime
     */
    protected $createdAt;
    /**
     * @var \DateTime
     */
    protected $deletedAt;

    function __construct()
    {
        // TODO: Implement __construct() method.
    }


    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return PublisherInterface|null
     */
    public function getPublisher()
    {
        return $this->publisher;
    }

    /**
     * @return int|null
     */
    public function getPublisherId()
    {
        if (!$this->publisher) {
            return null;
        }

        return $this->publisher->getId();
    }

    /**
     * @param PublisherInterface $publisher
     * @return self
     */
    public function setPublisher(PublisherInterface $publisher)
    {
        $this->publisher = $publisher->getUser();
        return $this;
    }

    /**
     * @return SubPublisherInterface
     */
    public function getSubPublisher()
    {
        return $this->subPublisher;
    }

    /**
     * @param SubPublisherInterface $subPublisher
     */
    public function setSubPublisher(SubPublisherInterface $subPublisher)
    {
        $this->subPublisher = $subPublisher;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return array
     */
    public function getRonAdSlots()
    {
        if ($this->ronAdSlotSegments instanceof PersistentCollection) {
            return array_map(function(RonAdSlotSegmentInterface $ronAdSlotSegment){
                return $ronAdSlotSegment->getRonAdSlot();
            },
            $this->ronAdSlotSegments->toArray());
        }
        else return [];
    }

    public function getReportableRonAdSlots()
    {
        $ronAdSlots = $this->getRonAdSlots();

        return array_filter(
            $ronAdSlots,
            function (RonAdSlotInterface $ronAdSlot)
            {
                return $ronAdSlot->getLibraryAdSlot() instanceof ReportableLibraryAdSlotInterface;
            }
        );
    }


    public function __toString()
    {
        return $this->id . $this->getName();
    }
}
