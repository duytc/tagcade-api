<?php

namespace Tagcade\Entity\Core;
use Tagcade\Model\Core\BaseLibraryAdSlotInterface;
use Tagcade\Model\Core\LibraryAdTagInterface;
use Tagcade\Model\Core\LibrarySlotTag as LibrarySlotTagModel;

class LibrarySlotTag extends LibrarySlotTagModel {

    protected $id;
    /**
     * @var LibraryAdTagInterface
     */
    protected $libraryAdTag;

    /**
     * @var BaseLibraryAdSlotInterface
     */
    protected $libraryAdSlot;

    /**
     * @var boolean
     */
    protected $active = true;

    /**
     * @var integer
     */
    protected $frequencyCap;

    /**
     * @var integer
     */
    protected $rotation;

    /**
     * @var integer
     */
    protected $position;

    /**
     * @var string
     */
    protected $refId;

    protected $createdAt;
    protected $updatedAt;
    protected $deletedAt;

    function __construct()
    {
    }
}