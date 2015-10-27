<?php

namespace Tagcade\Model\Core;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\Security\Acl\Exception\AclAlreadyExistsException;
use Tagcade\Entity\Core\LibraryAdSlotAbstract;

class RonAdSlot implements RonAdSlotInterface
{

    /** @var int */
    protected $id;

    /** @var BaseLibraryAdSlotInterface */
    protected $libraryAdSlot;

    /**
     * @var ArrayCollection
     */
    protected $ronAdSlotSegments;

    /** @var \Datetime */
    protected $createdAt;

    /** @var \Datetime */
    protected $updatedAt;

    /** @var \Datetime */
    protected $deletedAt;

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
     * @return string
     */
    public function getName()
    {
        if ($this->libraryAdSlot instanceof BaseLibraryAdSlotInterface) {
            return $this->libraryAdSlot->getName();
        }

        return null;
    }

    /**
     * @param string $name
     * @return self
     */
    public function setName($name)
    {
        if ($this->libraryAdSlot instanceof BaseLibraryAdSlotInterface) {
            return $this->libraryAdSlot->setName($name);
        }

        return $this;
    }

    /**
     * @return BaseLibraryAdSlotInterface
     */
    public function getLibraryAdSlot()
    {
        return $this->libraryAdSlot;
    }

    /**
     * @param BaseLibraryAdSlotInterface $libraryAdSlot
     * @return self
     */
    public function setLibraryAdSlot($libraryAdSlot)
    {
        $this->libraryAdSlot = $libraryAdSlot;
        return $this;
    }


    public function getRonAdTags()
    {
        if ($this->libraryAdSlot instanceof LibraryAdSlotAbstract) {
            return $this->libraryAdSlot->getLibSlotTags()->toArray();
        }

        return [];
    }


    /**
     * @return RonAdSlotSegmentInterface[]
     */
    public function getRonAdSlotSegments()
    {
        if ($this->ronAdSlotSegments === null) {
            $this->ronAdSlotSegments = new ArrayCollection();
        }

        return $this->ronAdSlotSegments;
    }

    /**
     * @param RonAdSlotSegmentInterface[] $ronAdSlotSegments
     * @return self
     */
    public function setRonAdSlotSegments($ronAdSlotSegments)
    {
        $this->ronAdSlotSegments = $ronAdSlotSegments;
        return $this;
    }

    public function addRonAdSlotSegment(RonAdSlotSegmentInterface $ronAdSlotSegment)
    {
        if ($this->ronAdSlotSegments === null) {
            $this->ronAdSlotSegments = new ArrayCollection();
        }

        $this->ronAdSlotSegments->add($ronAdSlotSegment);
        return $this;
    }

    /**
     * @return array
     */
    public function getSegments()
    {
        if ($this->ronAdSlotSegments instanceof PersistentCollection) {
            return array_map(function(RonAdSlotSegmentInterface $ronAdSlotSegment){
                return $ronAdSlotSegment->getSegment();
            },
            $this->ronAdSlotSegments->toArray());
        }
        else return [];
    }


    /**
     * @return \Datetime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return \Datetime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @return \Datetime
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

//    /**
//     * @return string
//     */
//    public function getType() {
//        if (!$this->libraryAdSlot instanceof BaseLibraryAdSlotInterface) {
//            return 'unknown';
//        }
//
//        if ($this->libraryAdSlot instanceof LibraryDisplayAdSlotInterface) {
//            return 'display';
//        }
//
//        if ($this->libraryAdSlot instanceof LibraryNativeAdSlotInterface) {
//            return 'native';
//        }
//
//        if ($this->libraryAdSlot instanceof LibraryDynamicAdSlotInterface) {
//            return 'dynamic';
//        }
//    }

    function __toString()
    {
        return $this->id . '';
    }

}