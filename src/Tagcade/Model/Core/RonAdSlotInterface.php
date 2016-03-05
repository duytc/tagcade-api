<?php


namespace Tagcade\Model\Core;


use Doctrine\Common\Collections\ArrayCollection;
use Tagcade\Model\ModelInterface;
use Tagcade\Model\RTBEnabledInterface;

interface RonAdSlotInterface extends ModelInterface, RTBEnabledInterface
{
    /**
     * @param int $id
     * @return self
     */
    public function setId($id);

    /**
     * @return BaseLibraryAdSlotInterface
     */
    public function getLibraryAdSlot();

    /**
     * @param BaseLibraryAdSlotInterface $libraryAdSlot
     * @return self
     */
    public function setLibraryAdSlot($libraryAdSlot);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     * @return self
     */
    public function setName($name);

    /**
     * @return array
     */
    public function getRonAdTags();

    /**
     * @return \Datetime
     */
    public function getCreatedAt();

    /**
     * @return \Datetime
     */
    public function getUpdatedAt();

//    /**
//     * @return \Datetime
//     */
//    public function getDeletedAt();


    /**
     * @return ArrayCollection
     */
    public function getRonAdSlotSegments();

    /**
     * @param ArrayCollection $ronAdSlotSegments
     * @return self
     */
    public function setRonAdSlotSegments($ronAdSlotSegments);

    /**
     * @return array
     */
    public function getSegments();

    /**
     * @param RonAdSlotSegmentInterface $ronAdSlotSegment
     * @return self
     */
    public function addRonAdSlotSegment(RonAdSlotSegmentInterface $ronAdSlotSegment);

    /**
     * @return int
     */
    public function getRtbStatus();

    /**
     * @return float
     */
    public function getFloorPrice();

    /**
     * @param float $floorPrice
     */
    public function setFloorPrice($floorPrice);

    /**
     * @param int $rtbStatus
     * @return self
     */
    public function setRtbStatus($rtbStatus);
}