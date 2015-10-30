<?php

namespace Tagcade\Model\Core;

use Tagcade\Model\ModelInterface;

interface RonAdSlotSegmentInterface extends ModelInterface
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @param int $id
     * @return self
     */
    public function setId($id);

    /**
     * @return RonAdSlotInterface
     */
    public function getRonAdSlot();

    /**
     * @param RonAdSlotInterface $ronAdSlot
     * @return self
     */
    public function setRonAdSlot($ronAdSlot);

    /**
     * @return SegmentInterface
     */
    public function getSegment();

    /**
     * @param SegmentInterface $segment
     * @return self
     */
    public function setSegment($segment);

    /**
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * @return \DateTime
     */
    public function getDeletedAt();
}