<?php

namespace Tagcade\Model\Core;


class RonAdSlotSegment implements RonAdSlotSegmentInterface
{

    protected $id;
    /**
     * @var RonAdSlotInterface
     */
    protected $ronAdSlot;

    /**
     * @var SegmentInterface
     */
    protected $segment;

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

    /**
     * @return int
     */
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
     * @return RonAdSlotInterface
     */
    public function getRonAdSlot()
    {
        return $this->ronAdSlot;
    }

    /**
     * @param RonAdSlotInterface $ronAdSlot
     * @return self
     */
    public function setRonAdSlot($ronAdSlot)
    {
        $this->ronAdSlot = $ronAdSlot;
        return $this;
    }

    /**
     * @return SegmentInterface
     */
    public function getSegment()
    {
        return $this->segment;
    }

    /**
     * @param SegmentInterface $segment
     * @return self
     */
    public function setSegment($segment)
    {
        $this->segment = $segment;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }


    public function __toString()
    {
        return $this->id . $this->segment->getId() . $this->ronAdSlot->getId();
    }

}