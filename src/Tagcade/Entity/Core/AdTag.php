<?php

namespace Tagcade\Entity\Core;

use DateTime;
use Tagcade\Model\Core\AdTag as AdTagModel;
use Tagcade\Model\Core\LibraryAdTagInterface;

class AdTag extends AdTagModel
{
    protected $id;
    protected $adSlot;
    protected $position;
    protected $active;

    protected $createdAt;
    protected $updatedAt;
    protected $deletedAt;
    protected $frequencyCap;

    /** int - for rotation display AdTags */
    protected $rotation;
    protected $refId;

    /**
     * @var LibraryAdTagInterface
     */
    protected $libraryAdTag;

    public function __construct()
    {
    }

}