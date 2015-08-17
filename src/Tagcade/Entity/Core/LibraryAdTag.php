<?php

namespace Tagcade\Entity\Core;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\LibraryAdTag as LibraryAdTagModel;
use Tagcade\Model\Core\LibrarySlotTagInterface;

class LibraryAdTag extends LibraryAdTagModel {

    protected $id;

    protected $html;

    protected $visible = false;
    /**
     * @var AdNetworkInterface
     */
    protected $adNetwork;

    protected $adTags;
    /**
     * @var LibrarySlotTagInterface
     */
    protected $libSlotTags;

    protected $name;

    /** int - type of AdTags*/
    protected $adType;
    /** array - json_array, descriptor of AdTag*/
    protected $descriptor;

    protected $createdAt;
    protected $updatedAt;
    protected $deletedAt;

    function __construct()
    {
    }
}