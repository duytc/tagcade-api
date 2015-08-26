<?php

namespace Tagcade\Entity\Core;
use Tagcade\Model\Core\LibraryAdTag as LibraryAdTagModel;

class LibraryAdTag extends LibraryAdTagModel {

    protected $id;
    protected $html;
    protected $visible = false;
    protected $adNetwork;
    protected $adTags;
    protected $libSlotTags;
    protected $name;
    protected $adType;
    protected $descriptor;
    protected $createdAt;
    protected $updatedAt;
    protected $deletedAt;

    function __construct()
    {
    }
}