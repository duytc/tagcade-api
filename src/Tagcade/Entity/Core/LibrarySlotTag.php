<?php

namespace Tagcade\Entity\Core;
use Tagcade\Model\Core\LibrarySlotTag as LibrarySlotTagModel;

class LibrarySlotTag extends LibrarySlotTagModel {

    protected $id;
    protected $libraryAdTag;
    protected $libraryAdSlot;
    protected $active = true;
    protected $frequencyCap;
    protected $rotation;
    protected $position;
    protected $refId;
    protected $createdAt;
    protected $updatedAt;
    protected $deletedAt;
    protected $impressionCap;
    protected $networkOpportunityCap;

    function __construct()
    {
    }
}