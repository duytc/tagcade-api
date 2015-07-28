<?php

namespace Tagcade\Entity\Core;

use Tagcade\Model\Core\DisplayAdSlotInterface;
use Tagcade\Model\Core\LibraryDisplayAdSlot as LibraryDisplayAdSlotModel;
use Tagcade\Model\User\Role\PublisherInterface;

class LibraryDisplayAdSlot extends LibraryDisplayAdSlotModel
{
    protected $id;
    protected $referenceName;
    protected $width;
    protected $height;
    protected $visible;
    /**
     * @var PublisherInterface
     */
    protected $publisher;
    /**
     * @var DisplayAdSlotInterface[]
     */
    protected $displayAdSlots;

    public function __construct()
    {}

}